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
        $babymanager = -99;
        $crows = Select ( "_babyManager", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
        if ( $crow = $crows->fetch() )
        {
            $babymanager = $crow[0];
        }
        
		if ( isAdmin() || $_SESSION['user'] == $babymanager )
		{
	require('header.php');
?>
		<div class="container theme-showcase" role="main">
            عکس هایی که آپلود شده است:<br>
			<div class='row'>
			<?php 
				$vrows = Select ( "*", $GLOBALS['tbl_baby'], "1=1", array());
				while ( $vrow = $vrows->fetch() )
				{
					$urows = Select ( "*", $GLOBALS['tbl_users'], "id=? and jashnid=?", array ($vrow['_userid'],getJashnID()) );
					if ($urow = $urows->fetch() )
					{
						echo "
			<div class='post col-md-4'>
				<div class='row'>
					<div class='col-md-12'>
						".$urow['_name']." ".$urow['_family']."
					</div>
				</div>
				<div class='row'>
					<div class='col-md-12'>
						<img class='img-thumbnail' src='".$vrow['_pic']."' />
					</div>
				</div>
				<div class='row'>
					<div class='col-md-12'>
						<a href='".$vrow['_pic']."'>دانلود</a>
					</div>
				</div>
			</div>
";
					}
				}
			?>
			</div>
        </div>
	<?php require('footer.php'); ?>
</html>
<?php
		}
		else
		{
			echo "You dont have the permission to view this page! <a href='home.php'>Home</a>";
			CloseDatabase();
			die();
		}
	}
	else{
		echo "Invalid session! <a href='logout.php'>Logout</a>";
		CloseDatabase();
		die();
	}
	CloseDatabase();
?>