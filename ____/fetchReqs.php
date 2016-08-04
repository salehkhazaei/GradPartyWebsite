<?php
$files = scandir("requests");
$arr = array();
for ( $i = 0 ; $i < count ( $files ) ; $i ++ )
{
    if ( $files[$i] == '.' || $files[$i] == '..' )
        continue;
    
    $myfile = fopen("requests/".$files[$i], "r") or die("Unable to open file!");
    $strfile = fread($myfile,filesize("requests/".$files[$i]));
    fclose($myfile);
    
    $arr[$files[$i]] = $strfile ;
}
echo json_myencode($arr);
?>