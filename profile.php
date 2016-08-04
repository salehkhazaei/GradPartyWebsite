<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();

    $crows = Select ( "_profileClose", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
    if ( $crow = $crows->fetch() )
    {
        if ( $crow[0] == 1 )
        {
            redirect("closed.php");
            CloseDatabase();
            die();
        }
    }
	
	if ( isset ( $_POST['name'] ) )
	{
		Update ( $GLOBALS['tbl_users'], "_name=?,_family=?,_date_year=?,_date_month=?,_date_day=?,_city=?,_major=?,_sex=?,_email=?,_hamrahi=?,_abi=?,_qoute=?,_longAns=?,_picstate=?,_payback=?,_paybackstate=?,_creditNo=?", "id=?",
				array ( $_POST['name'],
				$_POST['family'],
				$_POST['birth_year'],
				$_POST['birth_month'],
				$_POST['birth_day'],
				$_POST['city'],
				$_POST['major'],
				$_POST['sex'],
				$_POST['email'],
				$_POST['hamrahi'],
				$_POST['abilities'], 
				$_POST['qoute'], 
				$_POST['longAns'], 
				$_POST['picstate'], 
				$_POST['payback'], 
				$_POST['paybackstate'], 
				$_POST['creditNo'], 
				$_SESSION['user'] ) );
		
		if ( isset ( $_POST ['pwd'] ) && strlen ( trim ( $_POST ['pwd'] ) ) > 0 )
		{
			Update ( $GLOBALS['tbl_users'], "_stupwd=?", "id=?", 
				array ( $_POST['pwd'], $_SESSION['user'] ) );
		}
		if ( isset ( $_FILES["file"] ) && $_FILES['file']['error'] != 4 )
		{
			$target_dir = "uploads/";
			$target_file = $target_dir . $_SESSION['user'] . "_profile_" . md5($_SESSION['user'].round(microtime(true) * 1000)) . "." . pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION);
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
					Update ( $GLOBALS['tbl_users'], "_pic=?", "id=?", array ( $target_file, $_SESSION['user'] ) );
					
				} else {
					echo "Sorry, there was an error uploading your file.";
					CloseDatabase();
					die();
				}
			}
		}
		if ( isset ( $_FILES["file2"] ) && $_FILES['file2']['error'] != 4 )
		{
			$target_dir = "uploads/";
			$target_file = $target_dir . $_SESSION['user'] . "_friends_" . md5($_SESSION['user'].round(microtime(true) * 1000)) . "." . pathinfo(basename($_FILES["file2"]["name"]),PATHINFO_EXTENSION);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			// Check if image file is a actual image or fake image
			if(isset($_POST["submit"])) {
				$check = getimagesize($_FILES["file2"]["tmp_name"]);
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
				if (move_uploaded_file($_FILES["file2"]["tmp_name"], $target_file)) {
					$crows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
					if ( $crow = $crows->fetch() )
					{
						if ( strlen(trim($crow['_pagePic'])) > 0 )
						{
							unlink ( $crow['_pagePic'] );
						}
					}
					Update ( $GLOBALS['tbl_users'], "_pagePic=?", "id=?", array ( $target_file, $_SESSION['user'] ) );
				} else {
					echo "Sorry, there was an error uploading your file.";
					CloseDatabase();
					die();
				}
			}
		}
	}
	for ( $q = 0 ; $q < 100 ; $q ++ )
	{
		if ( isset ( $_POST ['q'.$q] ) )
		{
			Delete ( $GLOBALS['tbl_shortA'], "_userid=? AND _qid=?", array ($_SESSION['user'],$q) );
			Create ( $GLOBALS['tbl_shortA'], "(_userid,_qid,_ans)", "(?,?,?)", array($_SESSION['user'],$q,$_POST ['q'.$q]));
		}
	}
	
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ( $row = $rows->fetch() )
	{
		require('header.php');
?>
		<div class="container theme-showcase" role="main">
			<form method='post' enctype="multipart/form-data">
				<div class='row'>
				<div class='col-md-4'>
					<div class='row'>
					<div class='col-md-12'>
						لطفا به سوالات زیر پاسخ کوتاه بدهید. (30 کاراکتر).<br>
						لطفا پس از پر کردن فیلد ها دکمه ثبت را بزنید.
					</div>
					</div>
					<br>
					<div class='row'>
					<div class='col-md-12'>
						لطفا این قسمت را نیز پر کنید<br>
						<a class='btn btn-warning' href='khatereh.php'>خاطرات با دوستان</a>
					</div>
					</div>
					<?php
						$brows = Select ("*", $GLOBALS['tbl_shortQ'], "jashnid=?", array(getJashnID()));
						while ( $brow = $brows->fetch() )
						{
							$nrows = Select ( "*", $GLOBALS['tbl_shortA'], "_userid=? && _qid=?",
																		array ( $_SESSION['user'], $brow['id'] ) );
							$a = "";
							if ( $nrow = $nrows->fetch() )
							{
								$a = $nrow['_ans'];
							}
							echo "<div class='row'><div class='col-md-12'>".$brow['_question']."</div></div>";
							echo "<div class='row'><div class='col-md-12'><input type='text' class='form-control' name='q".$brow['id']."' value='".$a."' maxlength=30 /></div></div>";
						}
					?>
					<div class='row'>
					<div class='col-md-12'>
						لطفا پس از پر کردن فیلد ها دکمه ثبت را بزنید.
					</div>
					</div>
				</div>
				<div class='col-md-8'>
					<div class='row'><div class='col-md-offset-5 col-md-4'><div class='imglabel' style="background-image: url('<?php 
						echo $row['_pic'];
					?>');" ></div></div><div class='col-md-2'><div class='text'>عکس</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input type='file' name='file' class='form-control' /></div>
					<div class='col-md-3'><div class='text'>آپلود عکس</div></div></div>
<!--
					<div class='row'><div class='col-md-offset-5 col-md-4 btn btn-success'>شما مبلغ <?php echo $row['_payed']; ?> پرداخت کرده اید</div>
					<div class='col-md-3'><div class='text'>پرداختی</div></div></div>
-->
					<div class='row'><div class='col-md-offset-5 col-md-7 post'>
                        مبلغ باقیمانده از جشن برای افرادی که فقط یادبود خواسته بودند مبلغ 25 هزار تومان و افرادی که در جشن حضور داشتند 40 هزار تومان است. تعدادی از دوستان پیشنهاد دادند که این مبلغ رو به خیریه اهدا کنیم ولی از آنجایی که این مبلغ به شما تعلق دارد و حق تصمیم گیری برای پول باقی مانده هر فرد با خودش است، ما امکان زیر رو برای شما فراهم کردیم.<br>
ما فعلا در حال صحبت با شیرخوارگاه شهید ترکمانی هستیم که مبلغی که بچه های ما دوست داشته باشند رو به این خیریه کمک کنیم.<br>
شما میتونید از توی سایت بخش پروفایل یکی از گزینه های 1- دریافت کل پولتون، 2- کمک کردن بخشی از اون به خیریه و یا 3- اهدا کل اون به خیریه رو انتخاب کنید.<br>
ضما در صورت انتخاب گزینه هایی که باید پولی به شما برگردونیم حتما توی همون سایت شماره کارتتون رو وارد کنید.<br>
                    </div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'>
						<select id='paybackstate' name='paybackstate' class='form-control'>
							<option value=1>برگرداندن کل پول</option>
							<option value=2>اهدا بخشی از پول به خیریه</option>
							<option value=3>اهدا کل پول به خیریه</option>
						</select>
					</div>
					<div class='col-md-3'><div class='text'>پول باقیمانده</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input id='payback' type='text' name='payback' value='<?php 
						echo $row['_payback'];
					?>' class='form-control' required /></div>
					<div class='col-md-3'><div class='text'>مبلغ بازگشتی</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input id='creditNo' type='text' name='creditNo' value='<?php 
						echo $row['_creditNo'];
					?>' class='form-control' style='direction: ltr;' maxlength=16 required /></div>
					<div class='col-md-3'><div class='text'>شماره کارت</div></div></div>

                    <hr>
					<div class='row'><div class='col-md-offset-5 col-md-4'><input type='text' name='name' value='<?php 
						echo $row['_name'];
					?>' class='form-control' required /></div>
					<div class='col-md-3'><div class='text'>نام</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input type='text' name='family' value='<?php 
						echo $row['_family'];
					?>' class='form-control' required /></div>
					<div class='col-md-3'><div class='text'>نام خانوادگی</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input type='text' name='id' value='<?php 
						echo $row['id'];
					?>' class='form-control' required /></div>
					<div class='col-md-3'><div class='text'>شماره دانشجویی</div></div></div>

					<div class='row'><div class='col-md-2 col-md-offset-4'>
						<select id='year' name='birth_year' class='form-control'>
							<option value=1369>1369</option>
							<option value=1370>1370</option>
							<option value=1371>1371</option>
							<option value=1372>1372</option>
							<option value=1373>1373</option>
							<option value=1374>1374</option>
							<option value=1375>1375</option>
							<option value=1376>1376</option>
							<option value=1377>1377</option>
							<option value=1378>1378</option>
							<option value=1379>1379</option>
							<option value=1380>1380</option>
						</select></div><div class='col-md-2'>
						<select id='month' name='birth_month' class='form-control'>
							<option value=1>فروردین</option>
							<option value=2>اردیبهشت</option>
							<option value=3>خرداد</option>
							<option value=4>تیر</option>
							<option value=5>مرداد</option>
							<option value=6>شهریور</option>
							<option value=7>مهر</option>
							<option value=8>آبان</option>
							<option value=9>آذر</option>
							<option value=10>دی</option>
							<option value=11>بهمن</option>
							<option value=12>اسفند</option>
						</select></div><div class='col-md-2'>
						<select id='day' name='birth_day' class='form-control'>
							<?php 
								for ( $i = 1 ; $i <= 31 ; $i ++ )
								{
									echo "<option value=".$i.">".$i."</option>";
								}
							?>
						</select>
					</div>
					<div class='col-md-2'><div class='text'>تاریخ تولد</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input type='text' name='city' value='<?php 
						echo $row['_city'];
					?>' class='form-control' required /></div>
					<div class='col-md-3'><div class='text'>اهل</div></div></div>

					
					<div class='row'><div class='col-md-offset-5 col-md-4'>
						<select id='major' name='major' class='form-control'>
							<option value=1>نرم افزار</option>
							<option value=2>سخت افزار</option>
							<option value=3>فناوری اطلاعات</option>
						</select>
					</div>
					<div class='col-md-3'><div class='text'>رشته</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'>
						<select id='sex' name='sex' class='form-control'>
							<option value=1>مرد</option>
							<option value=2>زن</option>
						</select>
					</div>
					<div class='col-md-3'><div class='text'>جنسیت</div></div></div>
																						
					<div class='row'><div class='col-md-offset-5 col-md-4'><input name='email' type='email' value='<?php 
						echo $row['_email'];
					?>' class='form-control' required /></div>
					<div class='col-md-3'><div class='text'>ایمیل</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input name='hamrahi' type='text' value='<?php 
						echo $row['_hamrahi'];
					?>' class='form-control' required /></div>
					<div class='col-md-3'><div class='text'>تعداد همراهی ها</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><textarea name='abilities' class='form-control' ><?php 
						echo $row['_abi'];
					?></textarea></div>
					<div class='col-md-3'><div class='text'>توانایی ها</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input name='qoute' type='text' value='<?php 
						echo $row['_qoute'];
					?>' class='form-control'  maxlength=255  /></div>
					<div class='col-md-3'><div class='text'>نقل قول مورد علاقه</div></div></div>

					<br>
					<div class='row'><div class='col-md-offset-5 col-md-4'><textarea name='longAns' rows=10 cols=60 class='form-control' maxlength=600 ><?php 
						echo $row['_longAns'];
					?></textarea></div>
					<div class='col-md-3'><div class='text'>باحال ترين خاطره اين ٤ سال يا خنده دار ترين سوتي اي كه دادي رو بنويس</div></div></div>
					<br>
					<br>
					<div class='row'><div class='col-md-offset-5 col-md-4'><img class='img-thumbnail' src='<?php 
						echo $row['_pagePic'];
					?>' /></div><div class='col-md-2'><div class='text'>یک عکس برای صفحه خودت (با دوستای خوبت) آپلود کن</div></div></div>
					<br>
					<div class='row'><div class='col-md-offset-5 col-md-4'><input type='file' name='file2' class='form-control' /></div>
					<div class='col-md-3'><div class='text'>آپلود عکس</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'>
						<select id='picstate' name='picstate' class='form-control'>
							<option value=1>عمودی</option>
							<option value=2>افقی - پر کردن کل عرض صفحه</option>
							<option value=3>افقی</option>
						</select>
					</div>
					<div class='col-md-3'><div class='text'>طرز قرار گیری عکس با دوستان در صفحه</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input name='pwd' type='password' class='form-control' /></div>
					<div class='col-md-3'><div class='text'>رمز عبور</div></div></div>

					<div class='row'><div class='col-md-offset-5 col-md-4'><input id='save_profile' type='submit' class='form-control btn-success' value="ثبت" /></div></div>
				</div>
				</div>
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
			$("#picstate").val("<?php echo $row['_picstate']; ?>");
            $('#paybackstate').on('change', function() {
                if($(this).val() == 2)
                {
                    $("#payback").prop('disabled', false);
                }
                else{
                    $("#payback").prop('disabled', true);
                }
                if($("#paybackstate").val() == 3)
                {
                    $("#creditNo").prop('disabled', true);
                }
                else{
                    $("#creditNo").prop('disabled', false);
                }
            });
			$("#paybackstate").val("<?php echo $row['_paybackstate']; ?>");
            if($("#paybackstate").val() == 2)
            {
                $("#payback").prop('disabled', false);
            }
            else{
                $("#payback").prop('disabled', true);
            }
            if($("#paybackstate").val() == 3)
            {
                $("#creditNo").prop('disabled', true);
            }
            else{
                $("#creditNo").prop('disabled', false);
            }
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