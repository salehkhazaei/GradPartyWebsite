<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();

    $crows = Select ( "_khatereClose", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
    if ( $crow = $crows->fetch() )
    {
        if ( $crow[0] == 1 )
        {
            redirect("closed.php");
            CloseDatabase();
            die();
        }
    }
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ($row = $rows->fetch() )
	{
        if ( isset($_GET['dshow']) )
        {
            Update ( $GLOBALS['tbl_khatere'], "_show=0", "id=?", array ($_GET['dshow']));
			CloseDatabase();
            redirect("khaterestate_me.php");
			die();
        }
        if ( isset($_GET['show']) )
        {
            Update ( $GLOBALS['tbl_khatere'], "_show=1", "id=?", array ($_GET['show']));
			CloseDatabase();
            redirect("khaterestate_me.php");
			die();
        }
        if ( isset($_GET['pshow']) )
        {
            Update ( $GLOBALS['tbl_khatere'], "_show=2", "id=?", array ($_GET['pshow']));
			CloseDatabase();
            redirect("khaterestate_me.php");
			die();
        }
        if ( isset($_GET['str']) )
        {
            $arr = explode("%",$_GET['str']);
            for ( $i = 0 ; $i < count ($arr) ; $i ++ )
            {
                if (strlen(trim($arr[$i])) == 0 )
                    continue;
                $keyv = explode("^",$arr[$i]);
                $id = $keyv[0];
                $prior = $keyv[1];
                
                Update ( $GLOBALS['tbl_khatere'], "_prior=?", "_targetid=? and id=?", array ($prior,$_SESSION['user'],$id));
            }
			CloseDatabase();
            redirect("khaterestate_me.php");
			die();
        }
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery.min.js" type="text/javascript"></script>
		<script src="js/bootstrap.min.js" type="text/javascript"></script>
		<title>جشن فارغ التحصیلی</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
		<link rel="stylesheet" href="css/jquery.mCustomScrollbar.css" />
		<style>
			@font-face {
				font-family: eras;
				src: url(fonts/IS.ttf);
			}

			html,body {
				background-image: url('img/bg.png');
				font-family: eras;
				font-size: 13px;
				font-style: normal;
				font-variant: normal;
				font-weight: normal;
			}
			body {
				padding-bottom: 100px;
			}
			.text {
				font-family: eras;
				font-size: 13px;
				font-style: normal;
				font-variant: normal;
				font-weight: normal;
				text-align: right;
				vertical-align: middle;
			}
			.row {
				margin-top: 5px;
			}
			.post { 
				position: relative;
				z-index: 1000;
				margin-top: 30px;
				padding: 3px;
				box-shadow: 0px 1px 1px #999;
				background-color: #fff;
				border-radius: 10px;
				color: #000;
			}
			.post-title {
				border-bottom: 1px solid #ccc;
				margin-left: 10px;
				margin-right: 10px;
				padding-top: 10px;
				padding-bottom: 5px;
			}
			.post-title div {
				color: #555;
			}
			.post-text {
				margin: 5px;
				padding: 0px;
			}
			.post-hashtags {
				border-top: 1px solid #ccc;
				margin-left: 10px;
				margin-right: 10px;
				padding-top: 10px;
			}
			.a-btn {
				padding: 2px;
				border-radius: 3px;
			}
			.a-btn:hover {
				text-decoration: none;
			}
			.group-btn {
				margin: 5px;
				padding: 5px;
				border-radius: 5px;
				color: white;
				cursor: pointer;
			}
			.group-lbl {
				margin: 5px;
				padding: 5px;
				border-radius: 5px;
				color: white;
			}
			.group-red { background: #900; }
			.group-red:hover { background: #c00; }
			.group-green { background: #090; }
			.group-green:hover { background: #0c0; }
			.group-yellow { background: #990; }
			.group-yellow:hover { background: #cc0; }
			.group-black { background: #333; }
			.group-black:hover { background: #000; }
			.group-white { background: #eee; color: #333; }
			.group-white:hover { background: #ccc; }
			.group-blue { background: #009; }
			.group-blue:hover {	background: #00c; }
			.group-sky { background: #099; }
			.group-sky:hover { background: #0cc; }
			.group-mag { background: #909; }
			.group-mag:hover { background: #c0c; }
			.poll { 
				margin-top: 30px;
				padding: 30px;
				box-shadow: 0px 0px 10px #999;
				background-color: #fff;
				border-radius: 5px;
				color: #000;
				cursor: pointer;
			}
			.check-vote{
				font-size: 16pt;
			}
			.person {
				background-color: #ccc;
				text-align: center;
				padding: 5px;
				cursor: pointer;
			}
			.person:hover {
				background-color: #eee;
			}
			.names {
				position: relative;
				left: 0px;
			}
			.names-parent {
				background: #1C383F;
				margin-top: 30px;
				position: relative;
				left: 0px;
				height: 100px;
				overflow-y: visible;
				padding-right: 10px;
				border-radius: 10px;
			}
			
			.glow {
				-webkit-box-shadow:0 0 20px white; 
				-moz-box-shadow: 0 0 20px white;
				box-shadow:0 0 20px white;
				background: white;
			}
			
			.prior {
				background: #eee;
				padding: 10px;
				border-radius: 10px;
			}
			.vote {
				padding: 10px;
			}
			.prior.active {
				background: #ccc;
			}
			.submit-vote {
				display: none;
			}
			.waiting {
				font-size: 20pt;
				/* Chrome, Safari, Opera */
				-webkit-animation-name: example;
				-webkit-animation-duration: 2s;
				-webkit-animation-timing-function: ease;
				-webkit-animation-iteration-count: infinite;
				/* Standard syntax */
				animation-name: example;
				animation-duration: 2s;
				animation-timing-function: ease;
				animation-iteration-count: infinite;
				margin-top: 50px;
				margin-bottom: 50px;
			}
			/* Chrome, Safari, Opera */
			@-webkit-keyframes example {
				0%   {-ms-transform: rotate(0deg); -webkit-transform: rotate(0deg); transform: rotate(0deg); color: #08c;}
				50%   {-ms-transform: rotate(180deg); -webkit-transform: rotate(180deg); transform: rotate(180deg); color: #f91;}
				100%   {-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg); color: #08c;}
			}

			/* Standard syntax */
			@keyframes example {
				0%   {-ms-transform: rotate(0deg); -webkit-transform: rotate(0deg); transform: rotate(0deg); color: #08c;}
				50%   {-ms-transform: rotate(180deg); -webkit-transform: rotate(180deg); transform: rotate(180deg); color: #f91;}
				100%   {-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg); color: #08c;}
			}
            .prior-btn{
                cursor:pointer;
            }
		</style>
	</head>
	<body  dir=rtl>
		<div class="container theme-showcase" role="main">
			<div class='row'>
				<div class='col-md-12'>
					<div class="row">
						<div class='col-md-3'>
							<a href='khatereh.php'><div class='group-btn group-red' style='text-align: center;'><h3>بازگشت</h3></div></a>
						</div>
						<div class='col-md-9'>
							<div class='group-btn group-sky' style='text-align: center;'><h3>خاطرات با دوستان</h3></div>
						</div>
					</div>
				</div>
			</div>
			<div class='row'>
                <div class='col-md-12'>
                    <div class='group-lbl group-red'>
                        لطفا برای مشخص کردن اولویت ها روی دکمه زرد زیر هر خاطره به ترتیب از پایین ترین اولویتتون تا بالاترین اولویت (اول صفحه قرار بگیره) کلیک کنید و در نهایت دکمه ثبت اولویت ها را بزنید
                    </div>
                    <div id=set-prior class='group-lbl group-green btn'>
                        ثبت اولویت ها
                    </div>
                </div>
            </div>
			<div class='row'>
				<div class='col-md-12'>
					<div class='post maindiv'>
						<div class='row'>
						<?php 
							$colors = array ( "red",
                                              "yellow",
							                  "black",
							                  "sky",
							                  "mag",
							                  "blue",
							                  "green" );
											  
							$i = 0 ;
							$mainrows = Select ( "*" , $GLOBALS['tbl_khatere'], "_targetid=?", array($_SESSION['user']) );
							while ( $mainrow = $mainrows->fetch() )
							{
								$crows = Select ( "*", $GLOBALS['generation'], "id=?", array($mainrow['_userid']));
								if ( $crow = $crows->fetch () )
								{
									echo "<div class='col-md-12'>
											<div class='group-lbl group-".$colors[$i]."'>".$crow['name'].' '.$crow['family']."</div>
											<div class='group-lbl group-white'>".$mainrow['_text']."</div>
											</div>";
									echo "<div class='col-md-offset-1 col-md-3'>
                                            <a class='a-btn group-lbl group-yellow prior-btn' khid=".$mainrow['id']." prior=".$mainrow['_prior']." >اولویت: ".$mainrow['_prior']."</a>
                                            </div>";
									echo "<div class='col-md-8'>
                                            این خاطره در صفحه شما
                                            <a class='a-btn group-lbl group-".($mainrow['_show'] == 1 ? "green" : "red" )."' href='khaterestate_me.php?show=".$mainrow['id']."' >نمایش داده بشود</a>
                                            <a class='a-btn group-lbl group-".($mainrow['_show'] == 0 ? "green" : "red" )."' href='khaterestate_me.php?dshow=".$mainrow['id']."' >نمایش داده نشود</a>
                                            <a class='a-btn group-lbl group-".($mainrow['_show'] == 2 ? "green" : "red" )."' href='khaterestate_me.php?pshow=".$mainrow['id']."' >فقط در نسخه PDF نمایش داده شود</a>
                                            </div>";
									echo "<div class='col-md-12'><hr></div>";
									$i = ($i + 1) %7;
								}
							}
						?>
						</div>
					</div>
				</div>
			</div>
        </div>
        <script>
            $("a.prior-btn").click(function(){
                $("a.prior-btn").each(function(){
                    if ( parseInt($(this).attr("prior")) != 9999 )
                    {
                        $(this).attr("prior",parseInt($(this).attr("prior")) + 1);
                        $(this).text("اولویت: " + $(this).attr("prior"));
                    }
                });
                $(this).attr("prior",1);
                $(this).text("اولویت: " + $(this).attr("prior"));
            });
            $("#set-prior").click(function(){
                var str = "";
                $("a.prior-btn").each(function(){
                    if ( parseInt($(this).attr("prior")) != 9999 )
                    {
                        str += $(this).attr("khid") + "^" + $(this).attr("prior") + "%";
                    }
                });
                window.location="khaterestate_me.php?str=" + encodeURI(str);
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
