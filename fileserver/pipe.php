<?php
    set_time_limit(0);
    if ( isset ($_GET['file']) )
    {
        header('Content-Description: Download');
        header('Content-Type: application/force-download');
        header('Content-Disposition: inline; filename='); 
        header('Content-Transfer-Encoding: binary');
        $file = fopen ($_GET['file'], 'rb');
        if ($file) {
            while(!feof($file)) {
                echo fread($file, 1024 * 8);
            }
        }
        if ($file) {
            fclose($file);
        }
    }
    else
    {
        ?>
        <html>
        <head>
        </head>
        <body>
            <form method=get>
                <input name='file'/>
                <input type='submit'/>
            </form>
        </body>
        </html>
        <?php
    }
?>