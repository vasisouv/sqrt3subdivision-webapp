<?php
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.

$uploaddir = '../models/subdivided/subdivided_';
$uploadfile = $uploaddir . basename($_FILES['plyFile']['name']);

/*echo '<pre>';
if (move_uploaded_file($_FILES['plyFile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";*/
$file = $_FILES['plyFile']['tmp_name'];

$sFileName = basename($_FILES['plyFile']['name']);
$sFile = sqrt3subdivision($file,$sFileName);

/*if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$sFileName.'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($sFile);
    exit;
}*/

function sqrt3subdivision($file,$sFileName){
    $lines = file($file);
    $sFile = fopen("../models/sqrt3_".$sFileName, "w+");

    for ($i=0 ; $i<sizeOf($lines) ; $i++){
        fwrite($sFile,$lines[$i]);
        if (strcmp(trim($lines[$i]),"end_header") === 0){

            for($j=$i; $j<sizeof($lines) ; $j++ ){
                //fwrite($sFile,$lines[$j]);
                fwrite($sFile,"todo\n");

            }
            break;
        }
    }

}

?>
