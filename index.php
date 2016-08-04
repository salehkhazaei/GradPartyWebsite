<?php 
	require_once ('config.php');
    require_once 'securimage/securimage.php';
	
	if ( Access() )
	{
		redirect("home.php");
		CloseDatabase();
		die();
	}
	
	if ( isset ( $_POST [ 'stunum' ] ) )
	{
		$stunum = $_POST['stunum'];
		$pwd = $_POST['stupwd'];
		
		if ( !preg_match( "/\d+/", $stunum ) ) {
			echo "Invalid Student Number! <a href='index.php?retry'>Back</a>";
			CloseDatabase();
			die();
		}
		else
		{
			$image = new Securimage();
			if ($image->check($_POST['captcha_code']) == true) {
				$rows = Select ( "id,_stupwd", $GLOBALS['tbl_users'], "id=?", array ($stunum) );
				if ( $row = $rows->fetch() )
				{
					if ( $row['_stupwd'] == $pwd )
					{
						setcookie("user", $stunum , time() + (86400 * 365), "/");
						setcookie("pwd", md5($pwd."".$pwd) , time() + (86400 * 365), "/");
						redirect ( "home.php" );
						$_SESSION['user'] = $stunum;
						CloseDatabase();
						die();
					}
					else
					{
						echo "Invalid Password! <a href='index.php?retry'>Back</a>";
						CloseDatabase();
						die();
					}
				}
				else
				{
					$rows = Select ( "id,jashnid,name,family", $GLOBALS['generation'], "id=?", array($stunum));
					if ( $row = $rows->fetch() )
					{
						Create ( $tbl_users , "(id,jashnid,_stupwd,_name,_family)", "(?,?,?,?,?)", array ( $stunum,$row['jashnid'],$stunum,$row['name'],$row['family']) );
						$_SESSION['user'] = $stunum;
						setcookie("user", $stunum , time() + (86400 * 365), "/");
						setcookie("pwd", md5($stunum."".$stunum) , time() + (86400 * 365), "/");
						redirect ( "home.php" );
						CloseDatabase();
						die();
					}
					else
					{
						echo "Invalid Student Number! <a href='index.php?retry'>Back</a>";
						CloseDatabase();
						die();
					}
				}
			} else {
				echo "Invalid Captcha! <a href='index.php?retry'>Back</a>";
				CloseDatabase();
				die();
			}
		}
	}
	require('header.php');
?>
		<div class="container theme-showcase" role="main">
			<div class='row'>
				<div class='col-md-12 text' style='text-align:center'>
					در صورتی که قبلا ثبت نام نکرده اید، رمزعبور شماره دانشجویی شما است.<br>
					لطفا پس از وارد شدن اطلاعات خود را کامل کنید.<br>
				</div>
			</div>
			<br>
			<br>
			<br>
			<div class='row'>
				<div class='col-md-4 col-md-offset-4'>
					<form method=post>
						<input type="text" name='stunum' class='form-control' placeholder="شماره دانشجویی" />
						<br>
						<input type="password" name='stupwd' class='form-control' placeholder="رمز عبور" />
						<br>
						<?php echo Securimage::getCaptchaHtml() ?>
						<br>
						<input type="submit" class='form-control btn-success' value="ورود" />
					</form>
				</div>
			</div>
			<br>
        </div>
	<?php require('footer.php'); ?>
</html>
<?php
	CloseDatabase();
?>