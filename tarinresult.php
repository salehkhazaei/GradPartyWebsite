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
	if ($row = $rows->fetch() )
	{
        $tarinmanager = -99;
        $crows = Select ( "_tarinManager", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
        if ( $crow = $crows->fetch() )
        {
            $tarinmanager = $crow[0];
        }
        if ( isAdmin() || $_SESSION['user'] == $tarinmanager )
		{
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
		</style>
	</head>
	<body  dir=rtl>
		<div class="container theme-showcase" role="main">
			<div class='row'>
				<div class='col-md-12'>
					<div class="row">
						<div class='col-md-3'>
							<a href='tarinha.php'><div class='group-btn group-red' style='text-align: center;'><h3>بازگشت</h3></div></a>
						</div>
						<div class='col-md-9'>
							<div class='group-btn group-sky' style='text-align: center;'><h3>مسابقه ترین ها</h3></div>
						</div>
					</div>
				</div>
			</div>
			<div id='mainrow' class="row post-text">
				<div class='col-md-12'>
					<p>
						<div class='group-btn group-red'>تعداد افرادی که رای داده اند: 					<?php
						$krows = Select ( "COUNT(b.id)" , 
											$GLOBALS['tbl_bestpoll']." b inner join ".$GLOBALS['tbl_users']." u on b._userid=u.id", 
											"jashnid=? group by _userid", 
											array(getJashnID()) );
						echo $krows->rowCount();
					?>
</div>
					</p>
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
							                  "white",
							                  "mag",
							                  "blue",
							                  "green" );
							$mainrows = Select ( "*" , $GLOBALS['tbl_bestlist'], "_best != '---' and jashnid=?", array(getJashnID()) );
							$current = 0 ;
							$prior1 = "";
							$prior2 = "";
							$prior3 = "";
							$best = "";
							$count = 0;
							$i = 0;
							while ( $mainrow = $mainrows->fetch() )
							{
								$ccrows = Select ( "_userid" , $GLOBALS['tbl_bestpoll'], "_bestid=? group by _userid", array($mainrow['id']));
								$count = $ccrows->rowCount();
								// SELECT _pollid,COUNT(_pollid) as c, SUM(_prior) as p FROM `tbl_bestpoll` WHERE _bestid=94 group by _pollid order by c desc, p desc limit 0,3
								$trows = Select ( "_pollid,COUNT(_pollid) as c, SUM(_prior) as p" , 
													$GLOBALS['tbl_bestpoll'], 
													"_bestid=? and _pollid != -1 group by _pollid order by c desc, p desc limit 0,5", 
													array($mainrow['id']) );
								$hasVote=false;
								$best = $mainrow['_best'];
								$prior = 0 ;
								$prior1 = "";
								echo "<div class='col-md-3 vote '>
										<div class='bname group-btn group-".$colors[$i % 8]."'>".$best."</div>
										<div class='group-lbl group-".$colors[$i % 8]."'>تعداد رای ها-".$count."</div>";
								while ( $trow = $trows->fetch() )
								{
									$showName = true;
									$hasVote=true;
									$prior1 = $trow['_pollid'];
									$brows = Select ( "_userid" , $GLOBALS['tbl_bestpoll'], "_bestid=? AND _userid=? AND _prior=-1", array($mainrow['id'],$prior1));
									if ( $brow = $brows->fetch() )
									{
										$showName = false;
									}

									$grows = Select ( "*", $GLOBALS['generation'], "id=? and jashnid=?", array( $prior1,getJashnID() ) );
									if ( $grow = $grows->fetch() )
									{
										$prior1 = ( $showName ? $grow['name'].' '.$grow['family'] : "????" ).' ('.$trow['c'].' رای)';
									}
									echo "	<div class='group-lbl group-".$colors[$i % 8]."'>".$prior."-".$prior1."</div>";
									$prior ++ ;
								}
								echo "</div>";
								$i ++;
								if ( $i % 4 == 0 )
								{
									echo "</div><div class='row'>";
								}
							}
						?>
						</div>
					</div>
				</div>
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
