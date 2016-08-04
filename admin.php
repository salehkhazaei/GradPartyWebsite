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
        if ( isAdmin() )
        {
            if ( isset ( $_POST['_title'] ) )
            {
                Update ( $GLOBALS['tbl_jashn'], "_title=?,_dladdr=?,_closetime=?,_paymentUser=?,_paymentReminiscent=?,_paymentExtra=?,_babyManager=?,_sleepManager=?,_tarinManager=?,_majaleManager=?,_profileClose=?,_khatereClose=?,_babyClose=?,_sleepClose=?,_pollsClose=?,_tarinClose=?,_majaleClose=?",
                        "id=?",array($_POST['_title'],
                                     $_POST['_dladdr'],
                                     (strlen(trim($_POST['_closetime'])) == 0 ? null : $_POST['_closetime']),
                                     $_POST['_paymentUser'],
                                     $_POST['_paymentReminiscent'],
                                     $_POST['_paymentExtra'],
                                     $_POST['_babyManager'],
                                     $_POST['_sleepManager'],
                                     $_POST['_tarinManager'],
                                     $_POST['_majaleManager'],
                                     isset ($_POST['_profileClose']) && $_POST['_profileClose'] == 'on' ? 1 : 0,
                                     isset ($_POST['_khatereClose']) && $_POST['_khatereClose'] == 'on' ? 1 : 0,
                                     isset ($_POST['_babyClose']) && $_POST['_babyClose'] == 'on' ? 1 : 0,
                                     isset ($_POST['_sleepClose']) && $_POST['_sleepClose'] == 'on' ? 1 : 0,
                                     isset ($_POST['_pollsClose']) && $_POST['_pollsClose'] == 'on' ? 1 : 0,
                                     isset ($_POST['_tarinClose']) && $_POST['_tarinClose'] == 'on' ? 1 : 0,
                                     isset ($_POST['_majaleClose']) && $_POST['_majaleClose'] == 'on' ? 1 : 0,
                                     getJashnID()));
            }
            if ( isset ( $_FILES["file"] ) && isset ($_POST['field']) && $_FILES['file']['error'] != 4 )
            {
                $target_dir = "uploads/";
                $target_file = $target_dir . "_admin_" . md5($_SESSION['user'].round(microtime(true) * 1000)) . "." . pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if($imageFileType != "txt" ) {
                    $uploadOk = 0;
                    echo "Sorry, only txt files are allowed.";
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
                        if ( $_POST['field'] == "generation" )
                        {
                            Delete ( $GLOBALS['generation'], "jashnid=?", array(getJashnID()) );
                            $file = fopen($target_file,"r");
                            while(! feof($file))
                            {
                                $line = fgets($file);
                                $data = explode("\t",$line);
                                $num = count($data);
                                var_dump($data);
                                if ( $num != 3 )
                                {
                                    fclose($file);
                                    unlink($target_file);
                                    CloseDatabase();
                                    die ("Invalid file");
                                }
                                Create ( $GLOBALS['generation'], "(id,jashnid,name,family)", "(?,?,?,?)", array ( $data[0],getJashnID(),$data[1],$data[2] ) );
                            }
                            fclose($file);
                        }
                        else if ( $_POST['field'] == "shortQ" )
                        {
                            Delete ( $GLOBALS['tbl_shortQ'], "jashnid=?", array(getJashnID()) );
                            $file = fopen($target_file,"r");
                            while(! feof($file))
                            {
                                $line = fgets($file);
                                $data = explode("\t",$line);
                                $num = count($data);
                                if ( $num != 1 )
                                {
                                    fclose($file);
                                    unlink($target_file);
                                    CloseDatabase();
                                    die ("Invalid file");
                                }
                                Create ( $GLOBALS['tbl_shortQ'], "(jashnid,_question)", "(?,?)", array ( getJashnID(),$data[0] ));
                            }
                            fclose($file);
                        }
                        else if ( $_POST['field'] == "tarin" )
                        {
                            Delete ( $GLOBALS['tbl_bestlist'], "jashnid=?", array(getJashnID()) );
                            $file = fopen($target_file,"r");
                            while(! feof($file))
                            {
                                $line = fgets($file);
                                $data = explode("\t",$line);
                                $num = count($data);
                                if ( $num != 2 )
                                {
                                    fclose($file);
                                    unlink($target_file);
                                    CloseDatabase();
                                    die ("Invalid file");
                                }
                                Create ( $GLOBALS['tbl_bestlist'], "(jashnid,_best,emoji_id)", "(?,?,?)", array ( getJashnID(),$data[0],$data[1] ) );
                            }
                            fclose($file);
                        }
                        redirect("admin.php");
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                        CloseDatabase();
                        die();
                    }
                }
            }
            $qrows = Select ( "*", $GLOBALS['tbl_jashn'], "id=?", array (getJashnID()) );
            if ( $qrow = $qrows->fetch() )
            {
                require('header.php');
?>
		<div class="container theme-showcase" role="main">
            <form method=post>
                <div class='post'>
                    <h3>مدیریت</h3><hr>
                    <h4>لطفا برای اعمال تغییرات حتما دکمه ذخیره را بزنید</h4><br>
                    <div class='row '>
                        <div class='col-md-12'>
                            عنوان جشن:<br>
                            <input type='text' name='_title' class='form-control' value='<?php echo $qrow['_title']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class='row '>
                        <div class='col-md-12'>
                            لینک منبع دانلود:<br>
                            <input type='text' name='_dladdr' class='form-control' style='direction: ltr' value='<?php echo $qrow['_dladdr']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class='row '>
                        <div class='col-md-12'>
                            زمان بسته شدن سایت:<br>
                            <input type='text' name='_closetime' class='form-control' value='<?php echo $qrow['_closetime']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class='row '>
                        <div class='col-md-12'>
                            هزینه هر دانشجو:<br>
                            <input type='text' name='_paymentUser' class='form-control' value='<?php echo $qrow['_paymentUser']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class='row '>
                        <div class='col-md-12'>
                            هزینه هر دانشجو(فقط یادبود):<br>
                            <input type='text' name='_paymentReminiscent' class='form-control' value='<?php echo $qrow['_paymentReminiscent']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class='row '>
                        <div class='col-md-12'>
                            هزینه هر همراه:<br>
                            <input type='text' name='_paymentExtra' class='form-control' value='<?php echo $qrow['_paymentExtra']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class='row '>
                        <div class='col-md-12'>
                            مدیر بخش مسابقه عکس بچگی:<br>
                            <input type='text' name='_babyManager' class='form-control' value='<?php echo $qrow['_babyManager']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class='row '>
                        <div class='col-md-12'>
                            مدیر بخش عکس های جالب:<br>
                            <input type='text' name='_sleepManager' class='form-control' value='<?php echo $qrow['_sleepManager']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class='row '>
                        <div class='col-md-12'>
                            مدیر بخش ترین ها:<br>
                            <input type='text' name='_tarinManager' class='form-control' value='<?php echo $qrow['_tarinManager']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class='row '>
                        <div class='col-md-12'>
                            مدیر بخش مجله:<br>
                            <input type='text' name='_majaleManager' class='form-control' value='<?php echo $qrow['_majaleManager']; ?>' />
                            <br>
                        </div>
                    </div>
                    <div class=''>
                        <br>
                        <div class='row'>
                            <div class='col-md-12'>
                                بستن بخش ها: (در صورت داشتن تیک بخش مربوطه بسته خواهد شد)
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-2'>
                                <center>پروفایل:</center>
                                <input type='checkbox' name='_profileClose' class='form-control' <?php echo ($qrow['_profileClose']==1?"checked":""); ?> />
                            </div>
                            <div class='col-md-2'>
                                <center>خاطرات:</center>
                                <input type='checkbox' name='_khatereClose' class='form-control' <?php echo ($qrow['_khatereClose']==1?"checked":""); ?> />
                            </div>
                            <div class='col-md-2'>
                                <center>عکس بچگی:</center>
                                <input type='checkbox' name='_babyClose' class='form-control' <?php echo ($qrow['_babyClose']==1?"checked":""); ?> />
                            </div>
                            <div class='col-md-2'>
                                <center>عکس های جالب:</center>
                                <input type='checkbox' name='_sleepClose' class='form-control' <?php echo ($qrow['_sleepClose']==1?"checked":""); ?> />
                            </div>
                            <div class='col-md-2'>
                                <center>رای گیری:</center>
                                <input type='checkbox' name='_pollsClose' class='form-control' <?php echo ($qrow['_pollsClose']==1?"checked":""); ?> />
                            </div>
                            <div class='col-md-2'>
                                <center>ترین ها:</center>
                                <input type='checkbox' name='_tarinClose' class='form-control' <?php echo ($qrow['_tarinClose']==1?"checked":""); ?> />
                            </div>
                            <div class='col-md-2'>
                                <center>مجله:</center>
                                <input type='checkbox' name='_majaleClose' class='form-control' <?php echo ($qrow['_majaleClose']==1?"checked":""); ?> />
                            </div>
                        </div>
                    </div>
                    <br>
                    <input type='submit' class='form-control btn btn-success' value='ذخیره' />
                </div>
            </form>
            <div class='row post'>
                <div class='col-md-12'>
                    <h1>توجه:</h1>
                    <p>
                        برای آپلود لیست ها، لیست را در یک فایل اکسل آماده کرده و با فرمت <b><u>Unicode text</u></b> ذخیره کنید. سپس با یک ادیتور فایل را باز کرده و Encoding را بر روی UTF-8 قرار دهید.<br>
                        دقت داشته باشید که حتما فایل آپلودی فرمت ستون های ذکر شده در هر بخش را داشته باشد.<br>
                        <span style='color: red'>تذکر: نام ستون ها در فایل اکسل نوشته نشود<br></span>
                        در صورت بروز اشکال مجددا فایل را آپلود کنید.<br>
                        <b><span style='color: red'>در هر آپلود فایل اطلاعات قبلی حذف خواهد شد</span></b>
                    </p>
                </div>
            </div>
			<form method='post' enctype="multipart/form-data">
                <div class='row post'>
                    <div class='col-md-12'>
                        <p>ستون ها به ترتیب:<br> 1-شماره دانشجویی<br> 2-نام<br> 3-نام خانوادگی<br></p>
                    </div>
                    <div class='col-md-2 col-md-offset-3'>
                        <input type='submit' class='form-control btn btn-success' value='آپلود' />
                    </div>
                    <div class='col-md-4'>
                        <input type='hidden' name='field' value='generation' />
                        <input type='file' name='file' class='form-control' />
                    </div>
                    <div class='col-md-3'>
                        لیست شرکت کنندگان:
                    </div>
                </div>
            </form>
			<form method='post' enctype="multipart/form-data">
                <div class='row post'>
                    <div class='col-md-12'>
                        <p>ستون ها به ترتیب:<br> 1-نام ترین<br> 2-شماره ایموجی<br></p>
                    </div>
                    <div class='col-md-2 col-md-offset-3'>
                        <input type='submit' class='form-control btn btn-success' value='آپلود' />
                    </div>
                    <div class='col-md-4'>
                        <input type='hidden' name='field' value='tarin' />
                        <input type='file' name='file' class='form-control' />
                    </div>
                    <div class='col-md-3'>
                        لیست نام ترین ها:<br>
                    </div>
                </div>
            </form>
			<form method='post' enctype="multipart/form-data">
                <div class='row post'>
                    <div class='col-md-12'>
                        <p>ستون ها به ترتیب:<br> 1-سوال کوتاه پاسخ<br></p>
                    </div>
                    <div class='col-md-2 col-md-offset-3'>
                        <input type='submit' class='form-control btn btn-success' value='آپلود' />
                    </div>
                    <div class='col-md-4'>
                        <input type='hidden' name='field' value='shortQ' />
                        <input type='file' name='file' class='form-control' />
                    </div>
                    <div class='col-md-3'>
                        لیست سوالات کوتاه پاسخ ها:<br>
                    </div>
                </div>
            </form>
        </div>
	<?php require('footer.php'); ?>
</html>
<?php
            }
        }
        else{
            echo "You don't have permission to view this file <a href='home.php'>Back to home</a>";
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