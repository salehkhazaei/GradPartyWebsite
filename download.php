<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();
    
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ( $row = $rows->fetch() )
	{
        $jrows = Select ( "_dladdr", $GLOBALS['tbl_jashn'], "id=?", array (getJashnID()) );
        if ( $jrow = $jrows->fetch() )
        {
            if ( strlen(trim($jrow[0])) == 0 )
            {
                redirect("closed.php");
                CloseDatabase();
                die();
            }
            if ( isset ($_GET['file']) )
            {
                $name = file_get_contents($jrow[0]."/generatelink.php?user=".$_SESSION['user']."&file=".$_GET['file']);
                redirect($jrow[0]."/pipe.php?file=".$name);
                die();
            }
            require('header.php');
?>
		<div class="container theme-showcase" role="main">
			<div class='post'>
                <div class='row'><div class='col-md-4 col-md-offset-4'><h3 class='text-center'>لیست فایل ها</h3></div></div>
                <div class='row'><div class='col-md-4 col-md-offset-4'><h5 class='text-center'>توجه: لینک های دانلود یکبار مصرف هستند!</h5><hr></div></div>
                <?php
                    $files = file_get_contents($jrow[0]."/filelist.php");
                    foreach(preg_split("/((\r?\n)|(\r\n?))/", $files) as $line){
                        if ( strlen(trim($line)) > 0 )
                        {
                            $ar = explode("%",$line);
                            if ( $ar[0] == "Clips.rar" )
                                $ar[1] = 5368709120;
                            echo "<div class='row'><div class='col-md-4 col-md-offset-4'><a class='form-control btn btn-success' href='download.php?user=".$_SESSION['user']."&file=".$ar[0]."' target='_blank'>".$ar[0]." (Size=".round($ar[1] / 1048576)." MB)</a></div></div>";
                        }
                    } 
                ?>
                <br>
			</div>
        </div>
	<?php require('footer.php'); ?>
</html>
<?php
        }
	}
	else{
		echo "Invalid session! <a href='logout.php'>Logout</a>";
		CloseDatabase();
		die();
	}
	CloseDatabase();
?>