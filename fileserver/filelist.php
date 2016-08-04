<?php
    $dir    = './uploads';
    $files1 = scandir($dir);
    foreach($files1 as $v)
    {
        if ($v == "." || $v == "..")
            continue;
        echo "$v\n";
    }
?>