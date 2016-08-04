<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();
    
    $crows = Select ( "_sleepClose", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
    if ( $crow = $crows->fetch() )
    {
        if ( $crow[0] == 1 )
        {
            redirect("closed.php");
            CloseDatabase();
            die();
        }
    }
	
	if ( isset ( $_FILES["file"] ) && $_FILES['file']['error'] != 4 )
	{
		$target_dir = "uploads/";
		$target_file = $target_dir . $_SESSION['user'] . "_sleep_" . md5($_SESSION['user'].round(microtime(true) * 1000)) . "." . pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["file"]["tmp_name"]);
			if($check !== false) {
				$uploadOk = 1;
			} else {
				$uploadOk = 0;
			}
		}	
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			$uploadOk = 0;
			echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			CloseDatabase();
			die();
		}			
		if ($uploadOk == 0) {
			echo "Sorry, your file was not uploaded.";
			CloseDatabase();
			die();
			// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
				Create ( $GLOBALS['tbl_sleep'], "(_userid,_pic)", "(?,?)", array ( $_SESSION['user'],$target_file ) );
			} else {
				echo "Sorry, there was an error uploading your file.";
				CloseDatabase();
				die();
			}
		}
	}
	
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ( $row = $rows->fetch() )
	{
		if ( isset ( $_GET ['delete'] ) )
		{
			$crows = Select ( "*", $GLOBALS['tbl_sleep'], "id=? AND _userid=?", array ($_GET['delete'],$_SESSION['user']) );
			if ( $crow = $crows->fetch() )
			{
				unlink ( $crow['_pic'] );
				Delete ( $GLOBALS['tbl_sleep'], "id=? AND _userid=?", array ($_GET['delete'],$_SESSION['user']) );
				redirect('sleep.php');
				CloseDatabase();
				die();
			}
		}
	require('header.php');
?>
		<div class="container theme-showcase" role="main">
			<div class='row post'>
				<div class='col-md-12'>
					<p class='text'>
لطفا عکس های جالبی که از بچه ها دارید که سوژه خوبی است را آپلود کنید.<br>
					</p>
				</div>
			</div>
			<form method='post' enctype="multipart/form-data">
				<div class='row'>
				<?php
					$rows = Select ( "*", $GLOBALS['tbl_sleep'], "_userid=?", array( $_SESSION['user'] ) );
					while ( $row = $rows->fetch () )
					{
						echo "
							<div class='col-md-2'><div class='row'><div class='col-md-12'><div class='imglabel' style=\"background-image: url('".
								$row['_pic'].
							"');\" ></div></div></div><div class='row'><div class='col-md-12'><a href='sleep.php?delete=".$row['id']."' class='btn-danger a-btn'> حذف </a></div></div></div>";
					}
				?>
				</div>
				<div class='row'><div class='col-md-offset-8 col-md-2'><input type='file' name='file' class='form-control' /></div>
				<div class='col-md-2'><div class='text'>آپلود عکس</div></div></div>

				<div class='row'><div class='col-md-offset-8 col-md-2'><input id='save_profile' type='submit' class='form-control btn-success' value="آپلود" /></div></div>
			</form>
        </div>
	<?php require('footer.php'); ?>
</html>
<?php
	}
	else{
		echo "Invalid session! <a href='logout.php'>Logout</a>";
		CloseDatabase();
		die();
	}
	CloseDatabase();
?>