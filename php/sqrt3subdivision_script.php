<?php

include 'Vertex.php';
include 'Face.php';

$myFile = "../models/stanford_bunny.obj";
$lines = file($myFile);

$vertices = array();
$faces = array();

$newVertices = array();
$newFaces = array();

for ($i = 0; $i<sizeof($lines); $i++){
    $lines[$i] = str_replace("  "," ",$lines[$i]);
    if (substr( $lines[$i], 0, 1 ) == "v" ){

        $tokens = explode(" ",$lines[$i]);

        $vertex = new Vertex();
        $vertex->x = $tokens[1];
        $vertex->y = $tokens[2];
        $vertex->z = $tokens[3];
        $vertex->id = $i+1;

        array_push($vertices,$vertex);
    }
    if (substr( $lines[$i], 0, 1 ) == "f" ){

        $tokens = explode(" ",$lines[$i]);

        $face = new Face();
        $face->v1 = $tokens[1];
        $face->v2 = $tokens[2];
        $face->v3 = $tokens[3];

        array_push($faces,$face);
    }
}

findNeighbours($faces,$vertices);

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



function findNeighbours($faces,$vertices){
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

    echo "<br>";
    echo "v ".$newVertex->x."    ".$newVertex->y."         ".$newVertex->z;
}
foreach ($newFaces as $newFace){

    echo "<br>";
    echo "f ".$newFace->v1."    ".$newFace->v2."         ".$newFace->v3;
}

function sqrt3subdivision(){

    global $faces,$newFaces,$vertices,$newVertices;

    foreach ($faces as $face){

        $newVertex = new Vertex();
        $newVertex->x = round(($vertices[$face->v1-1]->x + $vertices[$face->v2-1]->x + $vertices[$face->v3-1]->x)/3,6);
        $newVertex->y = round(($vertices[$face->v1-1]->y + $vertices[$face->v2-1]->y + $vertices[$face->v3-1]->y)/3,6);
        $newVertex->z = round(($vertices[$face->v1-1]->z + $vertices[$face->v2-1]->z + $vertices[$face->v3-1]->z)/3,6);
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
            $sumX += $vertices[$neighbour-1]->x;
            $sumY += $vertices[$neighbour-1]->y;
            $sumZ += $vertices[$neighbour-1]->z;
        }
        $newVertex->x = round((1-$an)*$vertex->x+$an*(1/$n)*$sumX,4);
        $newVertex->y = round((1-$an)*$vertex->y+$an*(1/$n)*$sumY,4);
        $newVertex->z = round((1-$an)*$vertex->z+$an*(1/$n)*$sumZ,4);

        array_push($newVertices,$newVertex);
    }
    for ($i=0 ; $i<sizeof($faces) ; $i++){


        $newFace = new Face();
        $newFace->v1 = $i+1;
        $newFace->v2 = $faces[$i]->v1 + sizeof($faces);
        $newFace->v3 = $faces[$i]->v2 + sizeof($faces);

        array_push($newFaces,$newFace);

        $newFace = new Face();
        $newFace->v1 = $i+1;
        $newFace->v2 = $faces[$i]->v1 + sizeof($faces);
        $newFace->v3 = $faces[$i]->v3 + sizeof($faces);

        array_push($newFaces,$newFace);

        $newFace = new Face();
        $newFace->v1 = $i+1;
        $newFace->v2 = $faces[$i]->v2 + sizeof($faces);
        $newFace->v3 = $faces[$i]->v3 + sizeof($faces);

        array_push($newFaces,$newFace);

    }

}

?>
