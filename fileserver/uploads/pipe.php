<?php
    if ( isset ($_GET['file']) )
    {
        $file = fopen($_GET['file'],"r") or die("Invalid link.");
        $name = fgets($file);
        $req_time = fgets($file);
        $req_addr = fgets($file);
        fclose($file);

        if ( time() - $req_time < 60 )
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$name); 
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            echo file_get_contents(trim($name));
        }
        else{
            die ("Expired link. <a href='http://mokhi.ir/jashn91/download.php'>Back</a>");
        }
    }
    else
    {
        die ("Invalid link. <a href='http://mokhi.ir/jashn91/download.php'>Back</a>");
    }
?>