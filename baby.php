<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();
    
    $crows = Select ( "_babyClose", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
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
		$target_file = $target_dir . $_SESSION['user'] . "_baby_" . md5($_SESSION['user'].round(microtime(true) * 1000)) . "." . pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION);
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
				Create ( $GLOBALS['tbl_baby'], "(_userid,_pic)", "(?,?)", array ( $_SESSION['user'],$target_file ) );
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
			$crows = Select ( "*", $GLOBALS['tbl_baby'], "id=? AND _userid=?", array ($_GET['delete'],$_SESSION['user']) );
			if ( $crow = $crows->fetch() )
			{
				unlink ( $crow['_pic'] );
				Delete ( $GLOBALS['tbl_baby'], "id=? AND _userid=?", array ($_GET['delete'],$_SESSION['user']) );
				redirect('baby.php');
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
دوستان لطفا در سایت زیر عکس های بچگیتونو برای مسابقه آپلود کنید.<br>
به چند نکته ی زیر توجه کنید:<br>
۱- امکان آپلود بیش از یک عکس فراهم است<br>
۲- لطفا عکس نوزادی اپلود نکنید. <br>
۳- عکس هایی که قبلا در شبکه های اجتماعی به اشتراک گذاشتید رو آپلود نکنید. <br>
4- سعی کنید حتی الامکان کیفیت عکس آپلود شده بالا باشد ؛ مثلا از روی عکس ، عکس نگیرید. <br>
۵- عکس های دسته جمعی آپلود نکنید.<br>
					</p>
				</div>
			</div>
			<form method='post' enctype="multipart/form-data">
				<div class='row'>
				<?php
					$rows = Select ( "*", $GLOBALS['tbl_baby'], "_userid=?", array( $_SESSION['user'] ) );
					while ( $row = $rows->fetch () )
					{
						echo "
							<div class='col-md-2'><div class='row'><div class='col-md-12'><div class='imglabel' style=\"background-image: url('".
								$row['_pic'].
							"');\" ></div></div></div><div class='row'><div class='col-md-12'><a href='baby.php?delete=".$row['id']."' class='btn-danger a-btn'> حذف </a></div></div></div>";
					}
				?>
				</div>
				<div class='row'><div class='col-md-offset-8 col-md-2'><input type='file' name='file' class='form-control' /></div>
				<div class='col-md-2'><div class='text'>آپلود عکس</div></div></div>

				<div class='row'><div class='col-md-offset-8 col-md-2'><input id='save_profile' type='submit' class='form-control btn-success' value="آپلود" /></div></div>
			</form>
        </div>
	<?php require('footer.php'); ?>
	<script>
		$(function(){
			$("#year").val("<?php echo $row['_date_year']; ?>");
			$("#month").val("<?php echo $row['_date_month']; ?>");
			$("#day").val("<?php echo $row['_date_day']; ?>");
			$("#major").val("<?php echo $row['_major']; ?>");
			$("#sex").val("<?php echo $row['_sex']; ?>");
		});
	</script>
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