<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();
/*	
	?><html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery.min.js" type="text/javascript"></script>
		<script src="js/bootstrap.min.js" type="text/javascript"></script>
		<title>جشن فارغ التحصیلی</title>
		<meta charset="UTF-8"><?php */
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ( $row = $rows->fetch() ) 
	{
		if ( $_SESSION['user'] == 9131089 )
		{
			$tarin_length = 0;
			$crows = Select ( "COUNT(id)", $GLOBALS['tbl_bestlist'], "1=1", array() );
			if ( $crow = $crows->fetch() )
			{
				$tarin_length = $crow[0];
			}

			$rows = Select ( "*", $GLOBALS['tbl_users'], "LENGTH(TRIM(_telegram)) = 0 AND _verify=2", array());
			
			$json_str = '{"data": [';
			$q = false;
			while ( $row = $rows->fetch() )
			{
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
				
				
				$str = "" ;
				if ( $money == false )
				{
					$str .= "=== لطفا هزینه جشن را حتما حداکثر تا 14 فروردین واریز کنید ===
";
				}
				if ( $vote == false )
				{
					$str .= "لطفا در قسمت رای گیری، محبوب ترین ترین ها رای خود را وارد کنید.
";
				}
				if ( $tarin == false )
				{
					$str .= "لطفا رای خود را برای بخش ترین ها کامل کنید.
";
				}
				if ( $baby == false )
				{
					$str .= "لطفا عکس بچگی خود را آپلود کنید.
";
				}
				if ( $profilepic == false )
				{
					$str .= "لطفا عکس مجله خود را در قسمت پروفایل وارد کنید.
";
				}
				if ( $profiledet == false )
				{
					$str .= "لطفا اطلاعات پروفایل خود را کامل کنید.
";
				}
				if ( $friendspic == false )
				{
					$str .= "لطفا عکس با دوستان خود را وارد کنید.
";
				}
				if ( $shortAns == false )
				{
					$str .= "لطفا سوالات کوتاه پاسخ را وارد نمایید.
";
				}
				if ( $longAns == false )
				{
					$str .= "لطفا خاطره خود را در پروفایل وارد کنید.
";
				}
				if ( $khatere == false )
				{
					$str .= "لطفا در بخش خاطرات دوستان، خاطراتی که با دوستان خود دارید را وارد نمایید.
";
				}
				
				if ( strlen(trim($str)) == 0 )
					continue;
				if ( strlen(trim($row['_email'])) == 0 )
					continue;
				
				$str = "امروز آخرین فرصت برای پر کردن سایت است (بخش عکس بچگی تا 20 فروردین فعال خواهد بود).

".$str;
				if ( $q == true )
				{
					$json_str .= ",";
				}
				$q = true ;
				
				$json_str .= 
'{
"email": "'.escapeJsonString($row['_email']).'",
"text": "'.escapeJsonString($str).'"
}';
			}
			$json_str .= '] }';
		
			echo $json_str;
		}
	}
	else{
		echo "Invalid session! <a href='logout.php'>Logout</a>";
		CloseDatabase();
		die();
	}
	CloseDatabase();
?>