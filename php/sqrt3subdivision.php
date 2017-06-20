<?php

include 'Vertex.php';
include 'Face.php';



$vertices = array();
$faces = array();

ini_set('max_execution_time', 600);

$centerVertices = array();
$refinedVertices = array();
$pairs = array();
$newFaces = array();

$iterations = $_POST["iterations"];




$target_file = "../models/tmp/" . basename($_FILES["objFile"]["name"]);
move_uploaded_file($_FILES["objFile"]["tmp_name"], $target_file);

sqrt3subdivision(2);
downloadFile();

foreach ($centerVertices as $centerVertex){


    echo "v ".$centerVertex->x." ".$centerVertex->y." ".$centerVertex->z;
    echo "<br>";
}

foreach ($refinedVertices as $refinedVertex){


    echo "v ".$refinedVertex->x." ".$refinedVertex->y." ".$refinedVertex->z;
    echo "<br>";
}
foreach ($newFaces as $newFace){

    echo "<br>";
    echo "f ".$newFace->v1." ".$newFace->v2." ".$newFace->v3;
}

$faces = $newFaces;
$vertices = array();
array_push($vertices,$centerVertices,$refinedVertices);

function initializeData($lines){

    global $vertices,$faces;

    $id = 1;

    for ($i = 0; $i<sizeof($lines); $i++){
        $lines[$i] = str_replace("  "," ",$lines[$i]);


        if (substr( $lines[$i], 0, 1 ) == "v" ){

            $tokens = explode(" ",$lines[$i]);

            $vertex = new Vertex();
            $vertex->x = $tokens[1];
            $vertex->y = $tokens[2];
            $vertex->z = $tokens[3];
            $vertex->id = $i;
            array_push($vertices,$vertex);

        }
    }
    $id = 1;
    for ($i = 0; $i<sizeof($lines); $i++){

        $lines[$i] = str_replace("  "," ",$lines[$i]);
        if (substr( $lines[$i], 0, 1 ) == "f" ){

            $tokens = explode(" ",$lines[$i]);

            $face = new Face();
            $face->v1 = (float)$tokens[1];
            $face->v2 = (float)$tokens[2];
            $face->v3 = (float)$tokens[3];
            $face->id = $id;
            $id++;

            array_push($faces,$face);
        }
    }
}

function findFaceNeighbours ($faces){

    for ($i=0 ; $i<sizeof($faces) ; $i++){
        $currentFace = array();
        array_push($currentFace,$faces[$i]->v1,$faces[$i]->v2,$faces[$i]->v3);
        for ($j=0 ; $j<sizeof($faces) ; $j++){
            $nextFace = array();
            array_push($nextFace,$faces[$j]->v1,$faces[$j]->v2,$faces[$j]->v3);
            if ($j != $i){
                $intersection = array_intersect($currentFace,$nextFace);
                if (sizeof($intersection) == 2){
                    array_push($faces[$i]->neighbours,$faces[$j]);
                }
            }
        }
    }
}
function findVertexNeighbours($faces,$vertices){
    for ($i=0 ; $i<sizeof($faces) ; $i++){

        array_push($vertices[$faces[$i]->v1-1]->neighbours,$faces[$i]->v2,$faces[$i]->v3);
        array_push($vertices[$faces[$i]->v2-1]->neighbours,$faces[$i]->v1,$faces[$i]->v3);
        array_push($vertices[$faces[$i]->v3-1]->neighbours,$faces[$i]->v2,$faces[$i]->v1);

    }
    foreach ($vertices as $vertex){
        $vertex->neighbours = array_map('trim',$vertex->neighbours);
        $vertex->neighbours = array_values(array_unique($vertex->neighbours));
    }
}



function sqrt3subdivision($executions){
    global $faces,$newFaces,$vertices,$centerVertices,$refinedVertices,$iterations;

    $counter = 0;


    do{
        if ($counter == 0){
            $myFile = "../models/tmp/".basename($_FILES["objFile"]["name"]);
            $lines = file($myFile);

            unlink($myFile);
        }else {
            $myFile = "../models/tmp/temp.obj";
            $lines = file($myFile);
        }
        initializeData($lines);

        

        findVertexNeighbours($faces,$vertices);
        findFaceNeighbours($faces);

        $c = 1;
        foreach ($faces as $face){

            $centerVertex = new Vertex();
            $centerVertex->x = round(((float)$vertices[$face->v1-1]->x + (float)$vertices[$face->v2-1]->x + (float)$vertices[$face->v3-1]->x)/3,6);
            $centerVertex->y = round(((float)$vertices[$face->v1-1]->y + (float)$vertices[$face->v2-1]->y + (float)$vertices[$face->v3-1]->y)/3,6);
            $centerVertex->z = round(((float)$vertices[$face->v1-1]->z + (float)$vertices[$face->v2-1]->z + (float)$vertices[$face->v3-1]->z)/3,6);

            $face->center = $c;

            array_push($centerVertices,$centerVertex);

            $c++;
        }

        foreach ($vertices as $vertex){

            $n = sizeof($vertex->neighbours);
            $an = (4-(2*cos(2*pi()/$n)))/9;


            $refinedVertex = new Vertex();

            $sumX = 0; $sumY = 0; $sumZ = 0;


            foreach ($vertex->neighbours as $neighbour){
                $sumX += (float)$vertices[$neighbour-1]->x;
                $sumY += (float)$vertices[$neighbour-1]->y;
                $sumZ += (float)$vertices[$neighbour-1]->z;
            }
            $refinedVertex->x = round((1-$an)*(float)$vertex->x+$an*(1/$n)*$sumX,6);
            $refinedVertex->y = round((1-$an)*(float)$vertex->y+$an*(1/$n)*$sumY,6);
            $refinedVertex->z = round((1-$an)*(float)$vertex->z+$an*(1/$n)*$sumZ,6);

            $refinedVertex->neighbours = $vertex->neighbours;

            array_push($refinedVertices,$refinedVertex);
        }

        foreach ($vertices as $vertex){

            foreach ($faces as $face){
                if ($face->v1 == $vertex->id || $face->v2 == $vertex->id || $face->v3 == $vertex->id){
                    array_push($vertex->fNeighbours,$face->id);
                }
            }
        }

        foreach ($faces as $face){
            $faceV = array();

            $neighbourFaceV = array();

            $intersection = array();

            array_push($faceV,$face->v1,$face->v2,$face->v3);

            foreach ($face->neighbours as $fNeighbour){

                array_push($neighbourFaceV,$fNeighbour->v1,$fNeighbour->v2,$fNeighbour->v3);

                $intersection = array_intersect($neighbourFaceV,$faceV);

                foreach ($intersection as $item) {
                    $newFace = new Face();
                    $newFace->v1 = $face->center;
                    $newFace->v2 = $fNeighbour->center;
                    $newFace->v3 = $item+sizeof($centerVertices);

                    $found = false;


                    foreach ($newFaces as $of){
                        $nfVertices = array();
                        $ofVertices = array();

                        array_push($nfVertices,$newFace->v1,$newFace->v2,$newFace->v3);
                        array_push($ofVertices,$of->v1,$of->v2,$of->v3);

                        if (sizeof(array_intersect($ofVertices,$nfVertices)) == 3){
                            $found = true;
                        }
                    }
                    if (!$found){
                        array_push($newFaces,$newFace);
                    }

                }
                $neighbourFaceV = array();
            }
        }

        $myfile = fopen("../models/tmp/temp.obj", "w") or die("Unable to open file!");
        foreach ($centerVertices as $centerVertex){
            fwrite($myfile,"v ". $centerVertex->x." ".$centerVertex->y." ".$centerVertex->z);
            fwrite($myfile, "\n");
        }
        foreach ($refinedVertices as $refinedVertex){
            fwrite($myfile,"v ". $refinedVertex->x." ".$refinedVertex->y." ".$refinedVertex->z);
            fwrite($myfile, "\n");
        }
        foreach ($newFaces as $newFace){
            fwrite($myfile,"f ". $newFace->v1." ".$newFace->v2." ".$newFace->v3);
            fwrite($myfile, "\n");
        }

        fclose($myfile);

        $counter++;

        $vertices = array();
        $faces = array();

        $centerVertices = array();
        $refinedVertices = array();
        $newFaces = array();


    }while($counter<$iterations);

}
function downloadFile()
{

    $file = '../models/tmp/temp.obj';

    if (file_exists($file)) {
        header('Content-Description: File Transfer');

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($_FILES["objFile"]["name"]) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);

    }
    unlink($file);
    exit;
}

?>
