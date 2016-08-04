<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();

	$email = false;
	$hamrahi = false;
	$pic = false;
	$errors = false;
	$not_reg = false;
	$verify_state = false;
	$id = false;
	$name = true;
	$all = false;
	$ip = false;
	$verifying = false;
	$payment = false;
	$payed = false;
	$telegram = false;

	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ( $row = $rows->fetch() )
	{
		if ( isAdmin() ) 
		{
			$email = true ;
			$hamrahi = true;
			$pic = true;
			$errors = true;
			$verify_state = true;
			$not_reg = true;
			$all = true ;
			$id = true ;
			$name = true ;
			$verifying = true;
			$ip = true;
			$payment = true;
			$telegram = true;
		}
		if ( $verifying == true && isset ( $_GET['verify'] ) )
		{
			Update ( $GLOBALS['tbl_users'], "_verify=2", "id=?", array($_GET['verify']));
			redirect("status.php");
			CloseDatabase();
			die();
		}
		if ( $verifying == true && isset ( $_GET['unverify'] ) )
		{
			Update ( $GLOBALS['tbl_users'], "_verify=0", "id=?", array($_GET['unverify']));
			redirect("status.php");
			CloseDatabase();
			die();
		}
		if ( $payment == true && isset ( $_GET['no'] ) && isset ( $_GET['payed'] ) )
		{
            $irows = Select("*", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
            if ( $irow = $irows->fetch() )
            {
                $payment = ($irow['_paymentUser'] + $_GET['no'] * $irow['_paymentExtra']);
                if ( $_GET['no'] == -1 )
                {
                    $payment = $irow['_paymentReminiscent'];
                }
                Update ( $GLOBALS['tbl_users'], "_payed=".($payment), "id=?", array($_GET['payed']));
            }
			redirect("status.php");
			CloseDatabase();
			die();
		}
		if ( $payment == true && isset ( $_GET['npayed'] ) )
		{
			Update ( $GLOBALS['tbl_users'], "_payed=0", "id=?", array($_GET['npayed']));
			redirect("status.php");
			CloseDatabase();
			die();
		}
        if ( $payment == true && isset ( $_POST['movefrom'] ) )
        {
            $irows = Select("*", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
            if ( $irow = $irows->fetch() )
            {
                $f_rows = Select("*", $GLOBALS['tbl_users'], "id=?", array($_POST['movefrom']));
                $t_rows = Select("*", $GLOBALS['tbl_users'], "id=?", array($_POST['moveto']));
                if ( ($f_row = $f_rows->fetch()) && ($t_row = $t_rows->fetch()) )
                {
                    $payment = $_POST['no'] * $irow['_paymentExtra'];
                    Update ( $GLOBALS['tbl_users'], "_payed=_payed-".($payment), "id=?", array($_POST['movefrom']));
                    Update ( $GLOBALS['tbl_users'], "_payed=_payed+".($payment), "id=?", array($_POST['moveto']));
                }
            }
            redirect("status.php");
			CloseDatabase();
			die();
        }
		if ( $telegram == true )
		{  
            $updated = false ;
            foreach ( $_POST as $key => $value )
			{
				if ( strpos($key, 'tel') !== false )
				{
                    $updated = true ;
					Update ( $GLOBALS ['tbl_users'] , "_telegram=?", "id=?", array ( $value ,(int)substr($key,3) ) );
				}
			}
            if ( $updated )
            {
                redirect("status.php");
                CloseDatabase();
                die();
            }
		}
	require('header.php');
?>
		<div class="container theme-showcase" role="main">
			<?php
				echo "<div class='post'>";

				$rows = Select ( "COUNT(id)" , $GLOBALS['tbl_users'], "jashnid=?", array(getJashnID()) );
				if ( $row = $rows->fetch () )
				{
					echo "<div class='row'><div class='col-md-12 text'>تعداد افرادی که ثبت نام کردند: ".$row[0]."</div></div>";
				}

				$rows = Select ( "COUNT(id)" , $GLOBALS['tbl_users'], "jashnid=? and _verify=2", array(getJashnID()) );
				if ( $row = $rows->fetch () )
				{
					echo "<div class='row'><div class='col-md-12 text'>تعداد اکانت های Verify شده: ".$row[0]."</div></div>";
				}

                $erows = Select ( "SUM(_payed)", $GLOBALS['tbl_users'], "jashnid=?", array (getJashnID()));
                if ( $erow = $erows->fetch() )
                {
                    echo "
            <div class='row'>
                <div class='col-md-12 text'>
                    جمع دریافتی: ".$erow['0']."
                </div>
            </div>";
                }
                $erows = Select ( "COUNT(id)", $GLOBALS['tbl_users'], "_paybackstate=3", array (getJashnID()));
                $eerows = Select ( "COUNT(id) as c, SUM(_payback) as s", $GLOBALS['tbl_users'], "_paybackstate=2", array (getJashnID()));
                if ( ($erow = $erows->fetch()) && ($eerow = $eerows->fetch()) )
                {
                    echo "
            <div class='row'>
                <div class='col-md-12 text'>
                    جمع مبلغی که به خیریه کمک می شود: ".($erow[0] * 40000 + ($eerow['c'] * 40000 - $eerow['s']) + 770000)." تومان
                </div>
            </div>";
                }

				if ( $hamrahi )
				{
					$rows = Select ( "COUNT(id)" , $GLOBALS['tbl_users'], "jashnid=? and _hamrahi IS NOT NULL AND _verify=2", array(getJashnID()) );
					if ( $row = $rows->fetch () )
					{
						echo "<div class='row'><div class='col-md-12 text'>تعداد افرادی که تعداد همراهی را وارد کرده اند(اکانت های verify شده): ".$row[0]."</div></div>";
					}

					$rows = Select ( "SUM(_hamrahi)" , $GLOBALS['tbl_users'], "jashnid=? and _verify=2", array(getJashnID()) );
					if ( $row = $rows->fetch () )
					{
						echo "<div class='row'><div class='col-md-12 text'>جمع تعداد همراهی ها(اکانت های verify شده): ".$row[0]."</div></div>";
					}
					$erows = Select ( "SUM(_payed)", $GLOBALS['tbl_users'], "jashnid=?", array (getJashnID()));
					if ( $erow = $erows->fetch() )
					{
						echo "
				<div class='row'>
					<div class='col-md-12 text'>
						جمع دریافتی: ".$erow['0']."
					</div>
				</div>";
					}
					$erows = Select ( "SUM(FORMAT((_payed - 40000) / 15000,0))", $GLOBALS['tbl_users'], "jashnid=? and _payed > 0", array (getJashnID()));
					if ( $erow = $erows->fetch() )
					{
						echo "
				<div class='row'>
					<div class='col-md-12 text'>
						جمع مهمان ها (پرداخت شده): ".$erow['0']."
					</div>
				</div>";
					}
					$erows = Select ( "SUM(ABS(FORMAT((_payed - 40000) / 15000,0) - _hamrahi))", $GLOBALS['tbl_users'], "jashnid=? and _payed > 0", array (getJashnID()));
					if ( $erow = $erows->fetch() )
					{
						echo "
				<div class='row'>
					<div class='col-md-12 text'>
						اختلاف با تعداد همراه وارد شده: ".$erow['0']."
					</div>
				</div>";
					}
				}
				if ( $email == true )
				{
					echo "
							<div class='row'>
								<div class='col-md-offset-4 col-md-2 '>
									<div id='getEmails' class='form-control btn-success a-btn'>لیست ایمیل ها</div>
								</div>
								<div class='col-md-6'>
									<textarea id=emaillist class='form-control'></textarea>
								</div>
							</div>";
					echo "";
				}
                if ( $payment == true )
                {
					echo "
							<form method=post>
								<div class='row'>
                                <hr>
								<div class='col-md-2 col-md-offset-2'>
                                    <br>
									<input type='submit' class='form-control btn btn-danger' value='انتقال' />
								</div>
								<div class='col-md-2'>
									تعداد: <input class='form-control' name='no' required />
								</div>
								<div class='col-md-2'>
									به:(شماره دانشجویی) <input class='form-control' name='moveto' required  />
								</div>
								<div class='col-md-2'>
									از:(شماره دانشجویی)<input class='form-control' name='movefrom' required  />
								</div>
								<div class='col-md-2'>
									انتقال مهمان: 
								</div>
								</div>
							</form>";
					echo "";
                }
				
				echo "</div><br><br><br><br><form method=post>";

                $tarin_length = 0;
                $crows = Select ( "COUNT(id)", $GLOBALS['tbl_bestlist'], "jashnid=?", array(getJashnID()) );
                if ( $crow = $crows->fetch() )
                {
                    $tarin_length = $crow[0];
                }
				
				$rows = Select ( "*" , $GLOBALS['tbl_users'], "jashnid=? and ".($all == true ? "1=1" : "_verify=2" )." ORDER BY _last_online DESC, _verify DESC", array(getJashnID()) );
				while ( $row = $rows->fetch() )
				{
					$error = "";
					if ( isset ( $row ['_hamrahi'] ) && $row ['_hamrahi'] != null )
					{} else	{ $error .= "تعداد همراهی وارد نشده است.<br>"; }

					if ( isset ( $row ['_email'] ) && $row ['_email'] != null )
					{} else	{ $error .= "ایمیل وارد نشده است.<br>"; }

					if ( isset ( $row ['_pic'] ) && $row ['_pic'] != null )
					{} else	{ $error .= "عکس مجله وارد نشده است.<br>"; }

                    $send = false;
                    $vote = false ;
                    $tarin = false ;
                    $baby = false ;
                    $profilepic = false ;
                    $profiledet = false ;
                    $money = false ;
                    $friendspic = false ;
                    $shortAns = false ;
                    $longAns = false ;
                    $khatere = false ;

                    $erows = Select ( "*", $GLOBALS['tbl_polls'], "_pollid=? AND _id=?", array ( 3, $row['id'] ));
                    if ( $erow = $erows->fetch() )
                    {
                        $vote = true;
                    }

                    $rrows = Select ( "COUNT(id)", $GLOBALS['tbl_baby'], "_userid=?", array( $row['id'] ) );
                    if ( $rrow = $rrows->fetch () )
                    {
                        if ( $rrow[0] > 0 )
                        {
                            $baby = true ;
                        }
                    }

                    $erows = Select ( "COUNT(id)", $GLOBALS['tbl_shortA'], "LENGTH(TRIM(_ans)) > 0 && _userid=?", array($row['id']));
                    if ( $erow = $erows->fetch () )
                    {
                        if ( $erow[0] > 0 )
                            $shortAns = true ;
                    }
                    
                    $erows = Select ( "COUNT(id)", $GLOBALS['tbl_khatere'], "_userid=?", array($row['id']));
                    if ( $erow = $erows->fetch () )
                    {
                        if ( $erow[0] > 0 )
                            $khatere = true ;
                    }
                    
                    if ( $row['_lastTarin'] >= $tarin_length )
                    {
                        $tarin = true ;
                    }

                    $erows = Select ( "COUNT(id)", $GLOBALS['tbl_bestpoll'], "_userid=?", array($row['id']));
                    if ( $erow = $erows->fetch () )
                    {
                        if ( $erow[0] < 10 )
                            $tarin = false ;
                    }
                    
                    if ( isset($row['_pagePic']) && strlen(trim($row['_pagePic'])) > 0 )
                    {
                        $friendspic = true ;
                    }
                    
                    if ( isset($row['_pic']) && strlen(trim($row['_pic'])) > 0 )
                    {
                        $profilepic = true ;
                    }
                    
                    if ( isset($row['_date_year']) && strlen(trim($row['_date_year'])) > 0 )
                    {
                        $profiledet = true ;
                    }
                    if ( isset($row['_city']) && strlen(trim($row['_city'])) > 0 )
                    {
                        $profiledet = true ;
                    }
                    if ( isset($row['_major']) && strlen(trim($row['_major'])) > 0 )
                    {
                        $profiledet = true ;
                    }
                    
                    if ( isset($row['_longAns']) && strlen(trim($row['_longAns'])) > 0 )
                    {
                        $longAns = true ;
                    }
                    
                    if ( $row['_payed'] > 0 )
                    {
                        $money = true ;
                    }
                    
                    
                    if ( $vote == false )
                    {
                        $error .= "لطفا در قسمت رای گیری، محبوب ترین ترین ها رای خود را وارد کنید.<br>";
                    }
                    if ( $tarin == false )
                    {
                        $error .= "لطفا رای خود را برای بخش ترین ها کامل کنید.<br>";
                    }
                    if ( $baby == false )
                    {
                        $error .= "لطفا عکس بچگی خود را آپلود کنید.<br>";
                    }
                    if ( $profilepic == false )
                    {
                        $error .= "لطفا عکس مجله خود را در قسمت پروفایل وارد کنید.<br>";
                    }
                    if ( $profiledet == false )
                    {
                        $error .= "لطفا اطلاعات پروفایل خود را کامل کنید.<br>";
                    }
                    if ( $money == false )
                    {
                        $error .= "لطفا هزینه جشن را سریع تر واریز کنید.<br>";
                    }
                    if ( $friendspic == false )
                    {
                        $error .= "لطفا عکس با دوستان خود را وارد کنید.<br>";
                    }
                    if ( $shortAns == false )
                    {
                        $error .= "لطفا سوالات کوتاه پاسخ را وارد نمایید.<br>";
                    }
                    if ( $longAns == false )
                    {
                        $error .= "لطفا خاطره خود را در پروفایل وارد کنید.<br>";
                    }
                    if ( $khatere == false )
                    {
                        $error .= "لطفا در بخش خاطرات دوستان، خاطراتی که با دوستان خود دارید را وارد نمایید.<br>";
                    }
					
					echo "
					<div class='post'><div class='row'>
						<div class='col-md-4 text'>
							".($email == true ? "ایمیل: <div class='email'>".$row['_email']."</div><br>" : "" ).' '.($hamrahi == true ? "تعداد همراهی: ".$row['_hamrahi']."<br><hr>" : "" )."
							".( $errors == true ? $error : "" )."
						</div>
						<div class='col-md-4 text'>
							".($ip == true ? "آخرین IP:<div>".$row['_lastIP']."</div><br>" : "" )."
							".($ip == true ? "آخرین Forwarded IP:<div>".$row['_lastForwardedIP']."</div><br>" : "" )."
							".($ip == true ? "آخرین زمان فعالیت:<div>".$row['_last_online']."</div><br>" : "" )."
							".($payment == true ? "هزینه پرداختی:<div>".$row['_payed']."</div><br>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=-1' >پرداخت شد فقط یادبود</a>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=0' >پرداخت شد بدون همراه</a>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=1' >پرداخت شد 1 همراه</a>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=2' >پرداخت شد 2 همراه</a>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=3' >پرداخت شد 3 همراه</a>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=4' >پرداخت شد 4 همراه</a>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=5' >پرداخت شد 5 همراه</a>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=6' >پرداخت شد 6 همراه</a>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=7' >پرداخت شد 7 همراه</a>" : "" )."
							".($payment == true && $row['_payed'] == 0 ? "<a class='btn-success btn a-btn' href='status.php?payed=".$row['id']."&no=8' >پرداخت شد 8 همراه</a>" : "" )."
							".($payment == true && $row['_payed'] > 0 ? "<a class='btn-danger btn a-btn' href='status.php?npayed=".$row['id']."' >برگردانده شد</a>" : "" )."
						</div>
						<div class='col-md-2 text'>
							".( $id == true ? $row['id'] : "" ).'<br> '.( $name == true ? $row['_name'].'<br> '.$row['_family'] : "" )."<br>
							".($verifying == true && $row['_verify'] != 2 ? "<div class='form-control btn-success btn verify-btn' acid=".$row['id']." >فعال کردن</div>" : "" )."
							".($verifying == true && $row['_verify'] == 2 ? "<div class='form-control btn-danger btn unverify-btn' acid=".$row['id']." >غیرفعال کردن</div>" : "" )."
							".($telegram == true ? "<br><br>کلیدواژه شناسایی اکانت تلگرام:<input class='form-control telegram-txt' name='tel".$row['id']."' acid=".$row['id']." value='".$row['_telegram']."' />" : "" )."
						</div>
						<div class='col-md-2'>
							<div class='imglabel' style=\"background-image: url('".
								( $pic == true ? ( isset ( $row ['_pic'] ) && $row ['_pic'] != null ? $row['_pic'] : "uploads/noimage.jpg" ) : "" ).
							"');\" ></div>
						</div>
					</div></div>";

					if ( $verify_state == true )
					{
						echo "<div class='group-btn group-".( $row['_verify'] == 2 ? "green" : "red" )."'>وضعیت اکانت: ".( $row['_verify'] == 2 ? "فعال" : "غیرفعال" )."<br>";
						echo "</div>";
						echo "<div class='group-btn group-".( $row['_paybackstate'] == 0 ? "red" : ($row['_paybackstate'] == 3 ? "green" : "yellow" ) )."'>".($row['_paybackstate'] == 0 ? "" : ($row['_paybackstate'] == 3 ? "اهدا به خیریه" : "شماره کارت: ".$row['_creditNo'].", ".($row['_paybackstate'] == 1 ? "بازگرداندن کل پول" : "مبلغ: ".$row['_payback'])))."<br>";
						echo "</div>";
					}
				}
                if ( $telegram == true )
                {
                    echo "<input type='submit' class='form-control btn btn-warning' value='ذخیره تلگرام' /></form>";
                }
				if ( $not_reg )
				{
					echo "
			<div class='post'><div class='row'>
				<div class='col-md-12 text'>
					افرادی که ثبت نام نکرده اند:
				</div>
			</div>";
					$rows = Select ( "*" , $GLOBALS['generation'], "1=1", array() );
					while ( $row = $rows->fetch() )
					{
						$crows = Select ( "*" , $GLOBALS['tbl_users'], "id=?", array( $row['id'] ) );
						if ( $crow = $crows->fetch() )
						{
							
						}
						else
						{
					echo "
							<div class='row'>
								<div class='col-md-12 text'>
									".$row['id']."-".$row['name']." ".$row['family']."
								</div>
							</div>";
						}
					}
					echo "</div>";
				}
			?>
			
        </div>
		<script>
			$(function(){
				$("#getEmails").click(function(){
					var emails = "";
					$(".email").each(function(){
						emails += $(this).text() + ",";
					});
					$("#emaillist").text(emails);
				});
				$(".verify-btn").click(function(){
					window.location = "status.php?verify=" + $(this).attr("acid");
				});
				$(".unverify-btn").click(function(){
					window.location = "status.php?unverify=" + $(this).attr("acid");
				});
			});
		</script>
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