<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();
    $crows = Select ( "_tarinClose", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
    if ( $crow = $crows->fetch() )
    {
        if ( $crow[0] == 1 )
        {
            redirect("closed.php");
            CloseDatabase();
            die();
        }
    }

	if ( isset ( $_POST ['gettarinId'] ) )
	{
		$mrows =  Select ( "id", $GLOBALS['tbl_bestlist'], "_best=? and jashnid=?", array($_POST['gettarinId'],getJashnID()));

		while ( $mrow = $mrows->fetch() )
		{
			echo $mrow['id'];
		}
		CloseDatabase();
		die();

	}
	if ( isset ( $_POST ['restart'] ) && $_POST['restart'] == 1 )
	{
		Update ( $GLOBALS['tbl_users'], "_lastTarin=0", "id=?", array ($_SESSION['user']) );
		CloseDatabase();
		die();
	}
	if ( isset ( $_GET ['fixprior'] ) && $_GET['fixprior'] == 1 )
	{
		Update ( $GLOBALS['tbl_bestpoll'], "_prior=2-_prior", "_userid=? AND _prior!=-1", array ($_SESSION['user']) );
		echo "fixed.";
		CloseDatabase();
		die();
	}
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ($row = $rows->fetch() )
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
						<div class='group-btn group-red'>توجه: با کلیک بر روی نام هر ترین می توانید رای خود را برای آن ترین تغییر دهید.</div>
					</p>
				</div>
			</div>
			<div id='mainrow' class="row post-text">
				<div class='col-md-12'>
					<p>
						<div id=restart class='group-btn group-red'>برای وارد کردن مجدد تمام قسمت ها اینجا کلیک کنید</div>
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
							$i = 0;
							while ( $mainrow = $mainrows->fetch() )
							{
								$trows = Select ( "*" , $GLOBALS['tbl_bestpoll'], "_userid=? and _bestid=?", array($_SESSION['user'],$mainrow['id']) );
								$hasVote=false;
								$best = $mainrow['_best'];
								$prior1 = "";
								$prior2 = "";
								$prior3 = "";
								while ( $trow = $trows->fetch() )
								{
									
									if ( $trow['_prior'] == 0 )
									{
										$hasVote=true;
										$prior1 = $trow['_pollid'];
										$grows = Select ( "*", $GLOBALS['generation'], "id=?", array( $prior1 ) );
										if ( $grow = $grows->fetch() )
										{
											$prior1 = $grow['name'].' '.$grow['family'];
										}
									}
									else if ( $trow['_prior'] == 1 )
									{
										$hasVote=true;
										$prior2 = $trow['_pollid'];
										$grows = Select ( "*", $GLOBALS['generation'], "id=?", array( $prior2 ) );
										if ( $grow = $grows->fetch() )
										{
											$prior2 = $grow['name'].' '.$grow['family'];
										}
									}
									else if ( $trow['_prior'] == 2 )
									{
										$hasVote=true;
										$prior3 = $trow['_pollid'];
										$grows = Select ( "*", $GLOBALS['generation'], "id=?", array( $prior3 ) );
										if ( $grow = $grows->fetch() )
										{
											$prior3 = $grow['name'].' '.$grow['family'];
										}
									}
									else if ( $trow['_prior'] == -1 )
									{
										$hasVote=true;
										$prior1=-1;
									}
								}
								if ( $prior1 == -1 )
									{
										echo "<div class='col-md-3 vote'>
											<div  class='bname group-btn group-".$colors[$i % 8]."'>".$best."</div>
											<div class='group-btn group-".$colors[$i % 8]."'>نام شما در این لیست قرار نخواهد گرفت</div>
											</div>";
								}
								else if(!$hasVote)
									{
										echo "<div class='col-md-3 vote'>
											<div  class='bname group-btn group-".$colors[$i % 8]."'>".$best."</div>
											<div class='group-lbl group-".$colors[$i % 8]."'>شما در این ترین رای نداده اید.</div>
											</div>";
									}
								else{
									echo "<div class='col-md-3 vote '>
											<div class='bname group-btn group-".$colors[$i % 8]."'>".$best."</div>
											<div class='group-lbl group-".$colors[$i % 8]."'>1-".$prior1."</div>
											<div class='group-lbl group-".$colors[$i % 8]."'>2-".$prior2."</div>
											<div class='group-lbl group-".$colors[$i % 8]."'>3-".$prior3."</div>
											</div>";

								}
									$i ++;
									if ( $i % 4 == 0 )
									{
										echo "</div><div class='row'>";
									}
								

							}
							
						/*	if ( $prior1 == "-1" )
							{
								echo "<div class='col-md-3 vote'>
									<div class='group-btn group-".$colors[$i % 8]."'>".$best."</div>
									<div class='group-btn group-".$colors[$i % 8]."'>نام شما در این لیست قرار نخواهد گرفت</div>
									</div>";
							}
							else
							{
								echo "<div class='col-md-3 vote'>
									<div class='group-btn group-".$colors[$i % 8]."'>".$best."</div>
									<div class='group-btn group-".$colors[$i % 8]."'>1-".$prior1."</div>
									<div class='group-btn group-".$colors[$i % 8]."'>2-".$prior2."</div>
									<div class='group-btn group-".$colors[$i % 8]."'>3-".$prior3."</div>
									</div>";
							}*/
						?>
						</div>
					</div>
				</div>
			</div>
        </div>
	<?php require('footer.php'); ?>
<script type="text/javascript">
	$(".bname").click(function(){
		var id;
		$.ajax({
					type: "POST",
					url: "tarinstate.php",
					data: "gettarinId=" +$(this).text(),
					async: false,
					dataType: "text",
					success: function(res){ 
						id=res;
						window.location="tarinha.php?edit="+id;
					},
					dataType: "text",
					error: function(xhr, status, error){
						 var err = eval("(" + xhr.responseText + ")");
						 alert("خطا 0: مجددا تلاش کنید");
					}
				});
		

});
$("#restart").click(function(){
		var id;
		$.ajax({
					type: "POST",
					url: "tarinstate.php",
					data: "restart=1",
					async: false,
					dataType: "text",
					success: function(res){ 
						window.location="tarinha.php";
					},
					dataType: "text",
					error: function(xhr, status, error){
						 var err = eval("(" + xhr.responseText + ")");
						 alert("خطا 1: مجددا تلاش کنید");
					}
				});
		
});							

</html>
</script>
<?php
	}
	else{
		echo "Invalid session! <a href='logout.php'>Logout</a>";
		CloseDatabase();
		die();
	}
	CloseDatabase();
?>
