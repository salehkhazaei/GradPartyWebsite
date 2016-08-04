<?php
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    if ( isset ($_GET['file']) && isset ($_GET['user']) )
    {
        $name = generateRandomString();
        $myfile = fopen("temp/".$name, "w") or die("Unable to open file!");
        fwrite($myfile, $_GET['file']."\n");
        fwrite($myfile, $_SERVER['REQUEST_TIME']."\n");
        fwrite($myfile, $_GET['user']."\n");
        foreach($_SERVER as $h=>$v)
        {
            fwrite($myfile, "$h = $v\n");
        }
        fclose($myfile);
        echo $name;
    }
?>