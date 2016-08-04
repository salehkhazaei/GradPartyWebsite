<?php
    require_once ("../mainconfig.php");
    require_once ("jdf.php");
    
	global $tbl_jashn,$tbl_users,$tbl_posts,$tbl_baby,$tbl_sleep,$tbl_groups,$tbl_bestpoll;
	global $tbl_likes,$tbl_polls,$tbl_pollopt,$tbl_pollconf,$tbl_bestlist,$generation;
	global $tbl_shortQ,$tbl_shortA,$tbl_khatere,$tbl_request;
	global $DBH;

	$tbl_jashn = "tbl_jashn" ;
	$tbl_users = "tbl_users" ;
	$tbl_baby = "tbl_baby" ;
	$tbl_sleep = "tbl_sleep" ;
	$tbl_posts = "tbl_posts" ;
	$tbl_groups = "tbl_groups" ;
	$tbl_polls = "tbl_polls" ;
	$tbl_pollconf = "tbl_pollconf" ;
	$tbl_pollopt = "tbl_pollopt" ;
	$tbl_likes = "tbl_likes" ;
	$tbl_bestlist = "tbl_bestlist" ;
	$tbl_bestpoll = "tbl_bestpoll" ;
	$tbl_khatere = "tbl_khatere" ;
	$tbl_request = "tbl_request" ;
	$tbl_shortQ = "tbl_shortQ" ;
	$tbl_shortA = "tbl_shortA" ;
	$generation = "generation" ;
    
	OpenDatabase ( $db_host, $db_database, $db_username, $db_password );

    CreateGenerationTable ();
    CreateJashnTable ();
    CreateUsersTable ();
    CreatePostsTable ();
    CreateBabyTable ();
    CreateSleepTable ();
    CreateGroupTable ();
    CreatePollTable ();
    CreatePollConfigTable ();
    CreatePollOptionTable ();
    CreateBestListTable ();
    CreateBestPollTable ();
    CreateLikesTable ();
    CreateShortQTable ();
    CreateShortATable ();
    CreateKhatereTable ();
    CreateRequestTable ();

    if ( isset ( $_SESSION['user'] ) )
    {
        if (!isAdmin() && !(isset ( $_SESSION['isadmin'] ) && $_SESSION['isadmin'] == true))
        {
            if ( ! isset ( $_GET['dontclose'] ) )
            {
                close();
            }
        }
        else
        {
            if ( isset ($_GET['changeuser']) )
            {
                $currentuser = getJashnID();
                $nextuser = getJashnID($_GET['changeuser']);
                if ( $currentuser == $nextuser )
                {
                    $_SESSION['user'] = $_GET['changeuser']; 
                }
            }
        }
    }
    
    function close()
    {
        $rows = Select ( "_closetime", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()) );
        if ( $row = $rows->fetch() )
        {
            if ( strlen(trim($row[0])) > 0 )
            {
                $datetime = new DateTime(strtotime($row[0]));

                $months = $datetime->format('m') - tr_num(date("m"));
                $days = $datetime->format('d') - tr_num(date("d"));
                $hours = $datetime->format('H') - tr_num(date("H"));
                $min = $datetime->format('i') - tr_num(date("i"));
                $sec = $datetime->format('s') - tr_num(date("s"));
                $days += $months * 30;
                if ( $sec < 0 )
                {
                    $min --;
                    $sec += 60;
                }    
                if ( $min < 0 )
                {
                    $hours --;
                    $min += 60;
                }    
                if ( $hours < 0 )
                {
                    $days --;   
                    $hours += 24;
                }    
                if ( $days < 0 )
                {
                    $months --;   
                }    
                if ( $days < 0 && $months <= 0 )
                {
                    die ("<html><head><meta charset=\"UTF-8\"><title>جشن فارغ التحصیلی</title></head><body><h1>سایت بسته شده است!</h1></body></html>");
                }
            }
        }
    }
	
    function now() {
        $rows = Select ( "_closetime", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()) );
        if ( $row = $rows->fetch() )
        {
            if ( strlen(trim($row[0])) == 0 )
            {
                return "";
            }
            $datetime = new DateTime($row[0]);

            $months = $datetime->format('m') - tr_num(date("m"));
            $days = $datetime->format('d') - tr_num(date("d"));
            $hours = $datetime->format('H') - tr_num(date("H"));
            $min = $datetime->format('i') - tr_num(date("i"));
            $sec = $datetime->format('s') - tr_num(date("s"));
            $days += $months * 30;
            if ( $sec < 0 )
            {
                $min --;
                $sec += 60;
            }    
            if ( $min < 0 )
            {
                $hours --;
                $min += 60;
            }    
            if ( $hours < 0 )
            {
                $days --;   
                $hours += 24;
            }    
            return "<h4 style='color: red;'><span class='tday' val='".$days."'>".tr_num($days,'fa')."</span> روز و <span class='thour' val='".$hours."'>".tr_num($hours,'fa')."</span> ساعت و <span class='tmin' val='".$min."'>".tr_num($min,'fa')."</span> دقیقه و <span class='tsec' val='".$sec."'>".tr_num($sec,'fa')."</span> ثانیه دیگر سایت بسته خواهد شد.</h4>";
        }
        return "";
	}
    function json_myencode($struct) {
       return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct));
    }

	function CreateGenerationTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['generation'] . " ( id int(7),
																				jashnid int,
																				name CHAR(255),
																				family CHAR(255),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateJashnTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_jashn'] . " ( id int auto_increment,
																				_title CHAR(255),
																				_dladdr CHAR(255) not null,
																				_admin int(7),
																				_closetime DATETIME,
																				_profileClose int(1) default 0,
																				_khatereClose int(1) default 0,
																				_babyClose int(1) default 0,
																				_sleepClose int(1) default 0,
																				_pollsClose int(1) default 0,
																				_tarinClose int(1) default 0,
																				_majaleClose int(1) default 1,
																				_babyManager int(7),
																				_sleepManager int(7),
																				_tarinManager int(7),
																				_majaleManager int(7),
																				_paymentUser int default 40000,
																				_paymentReminiscent int default 25000,
																				_paymentExtra int default 15000,
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateUsersTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_users'] . " ( id int(7),
                                                                                jashnid int,
																				_stupwd char(255),
																				_name char(30),
																				_family char(30),
																				_date_year int(7),
																				_date_month int(7),
																				_date_day int(7),
																				_city char(30),
																				_major int,
																				_sex char(1),
																				_email char(100),
																				_average double,
																				_terms int,
																				_pic char(100),
																				_hamrahi int,
																				_abi text,
																				_verify int default 0,
																				_verifylink char(255),
																				_last_verify DATETIME,
																				_lastIP CHAR(255),
																				_lastForwardedIP CHAR(255),
																				_last_online DATETIME,
																				_lastTarin int default 0,
																				_tarin LONGTEXT,
																				_qoute CHAR(255),
																				_longAns TEXT(600),
																				_pagePic CHAR(255),
																				_payed int,
																				_telegram CHAR(255),
																				_picstate INT default 2,
																				_left INT default 1,
																				_payback INT default 0,
																				_paybackstate INT default 0,
																				_creditNo CHAR(16) default 0,
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreatePostsTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_posts'] . " ( id int auto_increment,
																				_parent int default -1,
																				_group int default -1,
																				_writer int(7),
																				_text text,
																				_time DATETIME,
																				_tags char(255),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateLikesTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_likes'] . " ( id int auto_increment,
																				_postid int,
																				_userid int(7),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateBabyTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_baby'] . " ( id int auto_increment,
																				_userid int(7),
																				_pic char(100),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateSleepTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_sleep'] . " ( id int auto_increment,
																				_userid int(7),
																				_pic char(100),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateGroupTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_groups'] . " ( id int auto_increment,
                                                                                jashnid int,
																				_admin int(7),
																				_name char(100),
																				_color char(100),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}
	function CreatePollTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_polls'] . " ( id int AUTO_INCREMENT,
																				_poll int,
																				_pollid int,
																				_id int(7),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreatePollConfigTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_pollconf'] . " ( id int AUTO_INCREMENT,
                                                                                jashnid int,
																				_group int,
																				_text text,
																				_title char(255),
																				_type int(1),
																				_max int(1) default 0,
																				_done int(1) default 0,
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreatePollOptionTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_pollopt'] . " ( id int AUTO_INCREMENT,
																				_pollid int,
																				_optiontext char(255),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateBestListTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_bestlist'] . " ( id int AUTO_INCREMENT,
                                                                                jashnid int,
																				_best char(255),
																				emoji_id int,
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateBestPollTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_bestpoll'] . " ( id int AUTO_INCREMENT,
																				_userid int(7),
																				_pollid int(7),
																				_prior int,
																				_bestid int,
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateKhatereTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_khatere'] . " ( id int AUTO_INCREMENT,
																				_userid int(7),
																				_targetid int(7),
																				_text TEXT(600),
                                                                                _prior int,
																				_show int,
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}
    
    function CreateRequestTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_request'] . " ( id int AUTO_INCREMENT,
																				_userid int(7),
																				_done int(1) default 0,
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateShortQTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_shortQ'] . " ( id int AUTO_INCREMENT,
                                                                                jashnid int,
																				_question char(255),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateShortATable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['tbl_shortA'] . " ( id int AUTO_INCREMENT,
																				_userid int(7),
																				_qid int(7),
																				_ans char(50),
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}


	function OpenDatabase($host, $database, $username, $password) {
		try {
			$GLOBALS ['DBH'] = new PDO ( "mysql:host=$host;dbname=$database;charset=utf8", $username, $password );
			$GLOBALS ['DBH']->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			
			return true;
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
			return false;
		}
		return false;

	}
	function CloseDatabase() {
		$GLOBALS ['DBH'] = null;
	}

	function Create($table, $headers, $values, $data) {
		try {
			$STH = $GLOBALS ['DBH']->prepare ( "INSERT INTO " . $table . " " . $headers . " values " . $values );
			$STH->execute ( $data );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
			return;
		}
		return true;
	}

	function Update($table, $set, $where, $data) {
		try {
			$STH = $GLOBALS ['DBH']->prepare ( "UPDATE " . $table . " SET " . $set . " WHERE " . $where );
			$STH->execute ( $data );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}

	}

	function Delete($table, $where, $data) {
		try {
			$STH = $GLOBALS ['DBH']->prepare ( "DELETE FROM " . $table . " WHERE " . $where );
			$STH->execute ( $data );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}

	}

	function Select($what, $table, $where, $data, $dump=false) {
		if ($where == null) {
			return $GLOBALS ['DBH']->query ( "select " . $what . " from " . $table );
		}
		else {
			$statement = $GLOBALS ['DBH']->prepare ( "select " . $what . " from " . $table . " where " . $where ); // name = :name");
			if ( $dump == true )
			{
				var_dump ( $statement);
				var_dump ( $data);
			}
			$statement->execute ( $data );
			return $statement;
		}

	}

	function redirect ( $url )	{
		echo "<script>window.location='".$url."';</script>";
	}
	
	function Access()
	{
		$ret = ( isset ( $_SESSION['user'] ) || ( isset ( $_COOKIE['user'] ) && isset ( $_COOKIE['pwd'] ) ) );
		
		if ( ! isset ( $_SESSION['user'] ) && $ret == true )
		{
			$rows = Select ( "id,_stupwd,_verify", $GLOBALS['tbl_users'], "id=?", array ($_COOKIE['user']) );
			if ( $row = $rows->fetch() )
			{
				if ( md5($row['_stupwd']."".$row['_stupwd']) == $_COOKIE['pwd'] )
				{
					$_SESSION['user'] = $_COOKIE['user'];
				}
			}
		}
		if ( isset ( $_SESSION['user'] ) )
		{
			$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
			if ($row = $rows->fetch())
			{
				$data = array ();
				$header = "";
				if ( isset ( $_SERVER['REMOTE_ADDR'] ) )
				{
					$header .= "_lastIP=?";
					array_push ( $data , $_SERVER['REMOTE_ADDR'] );
				}
				if ( isset ( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
				{
					if ( strlen ( trim ( $header ) ) != 0 )
					{
						$header .= ",";
					}
					$header .= "_lastForwardedIP=?";
					array_push ( $data , $_SERVER['HTTP_X_FORWARDED_FOR'] );
				}
				if ( strlen ( trim ( $header ) ) != 0 )
				{
					$header .= ",";
				}
				$header .= "_last_online=NOW()";
				array_push ( $data , $_SESSION['user'] );
				Update ( $GLOBALS['tbl_users'], $header, "id=?", $data );
                
                $yrows = Select ( "_admin", $GLOBALS['tbl_jashn'], "id=?", array($row['jashnid']) );
                if ( $yrow = $yrows->fetch() )
                {
                    if ( $_SESSION['user'] == $yrow[0] )
                    {
                        $_SESSION['isadmin'] = true;
                    }
                }
			}
		}
		return $ret ;
	}
	function verify(){
		$rows = Select ( "id,_name,jashnid,_stupwd,_email,_verify,TIMESTAMPDIFF(MINUTE,_last_verify,NOW()) as time_diff", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
		if ( $row = $rows->fetch() )
		{
			if ( $row['_verify'] < 2 )
			{
                $yrows = Select ( "_title", $GLOBALS['tbl_jashn'], "id=?", array($row['jashnid']) );
                if ( $yrow = $yrows->fetch() )
                {
                    if ( $row['time_diff'] > 10 || $row['time_diff'] == NULL)
                    {
                        $link = md5(round(microtime(true) * 1000).$row['_name'].$row['id']);
                        Update ( $GLOBALS['tbl_users'] , "_verifylink=?,_last_verify=NOW()", "id=?", array ($link,$_SESSION['user']));
                        $ret = mail( $row['_email'], $yrow[0], "لطفا جهت تایید اکانت خود روی این لینک کلیک نمایید: <br> http://saleh-khazaei.com/jashn91/verify.php?l=".urlencode($link), "From: verify@saleh-khazaei.com" );
                    }
                    echo "Please contact system admin to verify your account.";
                    CloseDatabase();
                    die();
                    return true;
                }
			}
		}
		else
		{
			return false;
		}
		return true;
	}
	function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
		$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}	
    function isAdmin()
    {
        $yrows = Select ( "_admin", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()) );
        if ( $yrow = $yrows->fetch() )
        {
            return $_SESSION['user'] == $yrow[0];
        }
        return false;
    }
    function isSubAdmin($section)
    {
        $manager = -99;
        $crows = Select ( $section, $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
        if ( $crow = $crows->fetch() )
        {
            $manager = $crow[0];
        }
		return ( isAdmin() || $_SESSION['user'] == $manager );
    }
    function getJashnID($userid=-1)
    {
        if ( isset ($_SESSION['user']) && $userid == -1 )
        {
            $userid = $_SESSION['user'];
        }
        $rows = Select ( "jashnid", $GLOBALS['tbl_users'], "id=?", array ($userid) );
        if ($row = $rows->fetch())
        {
            return $row[0];
        }
        return -1;
    }
    function getJashnTitle()
    {
        $yrows = Select ( "_title", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()) );
        if ( $yrow = $yrows->fetch() )
        {
            return $yrow[0];
        }
        return "";
    }
    function lastInsertedID() {
        return $GLOBALS['DBH']->lastInsertId();
    }
?>