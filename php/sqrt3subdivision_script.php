<?php

include 'Vertex.php';
include 'Face.php';

$myFile = "../models/cube.obj";
$lines = file($myFile);

$vertices = array();
$faces = array();

$newVertices = array();
$newFaces = array();
$id = 1;

for ($i = 0; $i<sizeof($lines); $i++){
    $lines[$i] = str_replace("  "," ",$lines[$i]);


    if (substr( $lines[$i], 0, 1 ) == "v" ){

        $tokens = explode(" ",$lines[$i]);

        $vertex = new Vertex();
        $vertex->x = $tokens[1];
        $vertex->y = $tokens[2];
        $vertex->z = $tokens[3];
        $vertex->id = $id;

        array_push($vertices,$vertex);

    }
    if (substr( $lines[$i], 0, 1 ) == "f" ){

        $tokens = explode(" ",$lines[$i]);

        $face = new Face();
        $face->v1 = (float)$tokens[1];
        $face->v2 = (float)$tokens[2];
        $face->v3 = (float)$tokens[3];
        $face->id = $id;

        array_push($faces,$face);
        $id++;
    }

}

findVertexNeighbours($faces,$vertices);
findFaceNeighbours($faces);
/*foreach ($vertices as $vertex){

    echo "ID = ". $vertex->id; echo "<br>";
    echo "Neighbours: ";
    foreach ($vertex->neighbours as $neighbour){
        echo $neighbour. " / ";
    }
    echo"<br>";
    echo "==========";
    echo "<br>";
}*/

sqrt3subdivision();


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
                    array_push($faces[$i]->neighbours,$faces[$j]->id);
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

foreach ($newVertices as $newVertex){


    echo "v ".$newVertex->x." ".$newVertex->y." ".$newVertex->z;
    echo "<br>";
}
foreach ($newFaces as $newFace){

    echo "<br>";
    echo "f ".$newFace->v1." ".$newFace->v2." ".$newFace->v3;
}

function sqrt3subdivision(){

    global $faces,$newFaces,$vertices,$newVertices;

    foreach ($faces as $face){

        $newVertex = new Vertex();
        $newVertex->x = round(((float)$vertices[$face->v1-1]->x + (float)$vertices[$face->v2-1]->x + (float)$vertices[$face->v3-1]->x)/3,6);
        $newVertex->y = round(((float)$vertices[$face->v1-1]->y + (float)$vertices[$face->v2-1]->y + (float)$vertices[$face->v3-1]->y)/3,6);
        $newVertex->z = round(((float)$vertices[$face->v1-1]->z + (float)$vertices[$face->v2-1]->z + (float)$vertices[$face->v3-1]->z)/3,6);
        array_push($newVertices,$newVertex);
    }

    foreach ($vertices as $vertex){
        $n = sizeof($vertex->neighbours);
        $an = (4-(2*cos(2*pi()/$n)))/9;


        //echo "An = ".$an; echo "<br>";
        //echo $n. " ";
        $newVertex = new Vertex();

        $sumX = 0; $sumY = 0; $sumZ = 0;


        foreach ($vertex->neighbours as $neighbour){
            $sumX += (float)$vertices[$neighbour-1]->x;
            $sumY += (float)$vertices[$neighbour-1]->y;
            $sumZ += (float)$vertices[$neighbour-1]->z;
        }
        $newVertex->x = round((1-$an)*(float)$vertex->x+$an*(1/$n)*$sumX,4);
        $newVertex->y = round((1-$an)*(float)$vertex->y+$an*(1/$n)*$sumY,4);
        $newVertex->z = round((1-$an)*(float)$vertex->z+$an*(1/$n)*$sumZ,4);

        array_push($newVertices,$newVertex);
    }
    for ($i=0 ; $i<sizeof($faces) ; $i++){

        for ($j=0 ; $j<sizeof($faces[$i]->neighbours)*2; $j++){
            $newFace = new Face();


            $newFace->v1 = $i+1;
            $newFace->v2 = 0;
            $newFace->v3 = 0;

            array_push($newFaces,$newFace);

        }

    }

}

?>
