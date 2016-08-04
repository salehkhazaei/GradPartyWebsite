<?php
	function checkVotes($uid,$prior,$bestid,$curr){
		$mrows = Select ( "*", $GLOBALS['tbl_bestpoll'], "_userid=? and _bestid=? and _pollid=?", array($uid,$bestid,$curr));
		while ( $mrow = $mrows->fetch() )
		{
			return false;
		}
		return true;
	}	
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
	
	if ( isset ( $_POST ['gettarinName'] ) )
	{
		$mrows =  Select ( "_best", $GLOBALS['tbl_bestlist'], "id=?", array($_POST['gettarinName']));
		if ( $mrow = $mrows->fetch() )
		{
			echo $mrow['_best'];
		}
		CloseDatabase();
		die();
	}
	if ( isset ( $_POST ['tarin'] ) )
	{
		Update($GLOBALS['tbl_users'], "_lastTarin=?", "id=?",array($_POST['lastTarin'],$_SESSION['user']));
		Delete ( $GLOBALS['tbl_bestpoll'], "_userid=? and _bestid=?", array ( $_SESSION['user'] ,$_POST ['tarin']) );
		$arr = explode ( "^", $_POST['newtarin'] );
		for ( $p = 0 ; $p < 3 && $p < count($arr) ; $p ++ )
		{
			if ( strlen( trim( $arr[$p] ) ) > 0 )
			{
				if($arr[$p]=="-1"){
					Create ( $GLOBALS['tbl_bestpoll'], "(_userid,_pollid,_prior,_bestid)", "(?,?,?,?)", array (
							$_SESSION['user'], $arr[$p], -1, $_POST ['tarin']));
				}
				else if(checkVotes($_SESSION['user'],0,$_POST ['tarin'],$arr[$p])){
					Create ( $GLOBALS['tbl_bestpoll'], "(_userid,_pollid,_prior,_bestid)", "(?,?,?,?)", array (
							$_SESSION['user'], $arr[$p], 2-$p, $_POST ['tarin']));
				}
				else{

					echo "{\"result\": \"NOK\"}";
					CloseDatabase();
					die();
				}
			}
		}
		echo "{\"result\": \"OK\"}";
		CloseDatabase();
		die();
	}
	if ( isset ( $_POST ['etarin'] ) )
	{
		Delete ( $GLOBALS['tbl_bestpoll'], "_userid=? and _bestid=?", array ( $_SESSION['user'] ,$_POST ['etarin']) );
		$arr = explode ( "^", $_POST['newtarin'] );
		for ( $p = 0 ; $p < 3 && $p < count($arr) ; $p ++ )
		{
			if ( strlen( trim( $arr[$p] ) ) > 0 )
			{
				if($arr[$p]=="-1"){
					Create ( $GLOBALS['tbl_bestpoll'], "(_userid,_pollid,_prior,_bestid)", "(?,?,?,?)", array (
							$_SESSION['user'], $arr[$p], -1, $_POST ['etarin']));
				}
				else if(checkVotes($_SESSION['user'],0,$_POST ['etarin'],$arr[$p])){
					Create ( $GLOBALS['tbl_bestpoll'], "(_userid,_pollid,_prior,_bestid)", "(?,?,?,?)", array (
							$_SESSION['user'], $arr[$p], 2-$p, $_POST ['etarin']));
				}
				else{

					echo "{\"result\": \"NOK\"}";
					CloseDatabase();
					die();
				}
			}
		}
		echo "{\"result\": \"OK\"}";
		CloseDatabase();
		die();
	}
	if ( isset ( $_POST ['settarin'] ) )
	{
		$mrows =  Update($GLOBALS['tbl_users'], "_lastTarin=?", "id=?",array($_POST ['settarin'],$_SESSION['user']));
		echo "{\"result\": \"OK\"}";
		CloseDatabase();
		die();
	}
	if(isset ( $_POST ['gettarin'] )){
		$nrows = Select ( "_lastTarin", $GLOBALS['tbl_users'], "id=?",array( $_SESSION['user']));
		if ( $nrow = $nrows->fetch() )
		{
			echo "{\"last\": \""+$nrow["_lastTarin"]+"\"}";
		}
		CloseDatabase();
		die();
	}
	
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ($row = $rows->fetch())
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
				background-image: url('img/bg.png');;
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
			.group-red { background: #900; }
			.group-red:hover { background: #c00; }
			.group-green { background: #090; }
			.group-green:hover { background: #0c0; }
			.group-yellow { background: #990; }
			.group-yellow:hover { background: #cc0; }
			.group-black { background: #333; }
			.group-black:hover { background: #000; }
			.group-white { background: #fff; color: #333; }
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
			.prior.active {
				background: #ccc;
			}
			.submit-vote {
				display: none;
			}
			.waiting-ajax {
				position: fixed;
				right: 20;
				top: 20;
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
		<div id=myModal class="modal fade" tabindex="-1" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-body">
			  <center>
				<h1>ترین ها</h1>
				<img src='img/minions.png' /></center>
				<p>
					لطفا به نکات زیر دقت فرمایید:<br>
					<br>
- لطفا دقت کنید که پس از انتخاب نام فرد مورد نظر، حتما نام او در قسمت "رای" نوشته شده باشد.<br><br>
- در صورتی که نمی خواهید اسم شما در لیست یک ترین نمایش داده شود گزینه مربوطه را در انتهای صفحه انتخاب کنید.<br><br>
- لطفا تا زمانی که علامت زیر در گوشه بالا سمت راست نمایش داده می شود پنجره را نبندید، زیرا تا زمانی که این علامت حذف 
نشود رای های شما روی سرور ذخیره نخواهد شد.<br>
<center><span class='glyphicon glyphicon-repeat waiting' aria-hidden='true'></span></center><br>
- لطفا پس از رای دادن از بخش رای های من صحت اطلاعات را بررسی نمایید<br>
در صورت بروز هرگونه مشکل با مسئول سایت تماس بگیرید.<br>
				</p>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
			  </div>
			</div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		<script>
			var t=[<?php if(isset ( $_GET['edit'])) echo "true" ; else echo "false";?>];
			if(t=="false")
			$("#myModal").modal('show');
		</script>
		<div class="container theme-showcase" role="main">
			<div class='row'>
				<div class='col-md-12'>
					<div class="row">
						<div class='col-md-3'>
							<a href='home.php'><div class='group-btn group-red' style='text-align: center;'><h3>بازگشت</h3></div></a>
						</div>
						<div class='col-md-3'>
							<a href='tarinstate.php'><div class='group-btn group-red' style='text-align: center;'><h3>رای های من</h3></div></a>
						</div>
						<div class='col-md-6'>
							<div class='group-btn group-sky' style='text-align: center;'><h3>مسابقه ترین ها</h3></div>
						</div>
					</div>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-3'>
					<div class='names-parent'>
						<div class='names'>
						</div>
					</div>
				</div>
				<div class='col-md-9'>
					<div class='post maindiv'>
						<div class="row post-title">
							<div class='col-md-12'>
								<center><h1 class='tarin-title'>
									
								</h1></center>
								<br>
								<div class="row">
									<div class='col-md-6 col-md-offset-3'>
										<div class='progress'>
											<div class='progress-bar progress-bar-success progress-bar-striped active' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 0%'>
												0%
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id='mainrow' class="row post-text">
							<div class='col-md-12'>
								<p>
									<div class='group-btn group-red'>توجه: در صورتی که نمی خواهید اسم شما در لیست این ترین نمایش داده شود گزینه مربوطه را در انتهای صفحه انتخاب کنید.</div>
								</p>
							</div>
						</div>
						<div id='mainrow' class="row post-text">
							<div class='col-md-12'>
								<p>
									<div class='group-btn group-red'>توجه: لطفا دقت کنید که پس از انتخاب نام فرد مورد نظر، حتما نام او در بخش پایینی قسمت رای نوشته شده باشد.</div>
								</p>
							</div>
						</div>
						<br><br>
						<div class="row">
							<div class='col-md-12' style='text-align: right;'>
								<p>لطفا نام فرد مورد نظر را وارد کنید.<br>همچنین می توانید از لیست سمت چپ فرد را انتخاب کنید</p>
							</div>
						</div>
						<div class="row">
							<div class='col-md-4'>
								<div class='prior'>
									<div class="row">
										<div class='col-md-12'>
											اولویت سوم
										</div>
									</div>
									<div class="row">
										<div class='col-md-12'>
											<input type='text' class='form-control stuname' />
										</div>
									</div>
									<div class="row">
										<div class='col-md-12'>
											شماره دانشجویی
										</div>
									</div>
									<div class="row">
										<div class='col-md-12'>
											<input type='text' class='form-control stuid' />
										</div>
									</div>
									رای:
									<div class="row">
										<div class='col-md-12 valid'>
											
										</div>
									</div>
								</div>
							</div>
							<div class='col-md-4'>
								<div class='prior'>
									<div class="row">
										<div class='col-md-12'>
											اولویت دوم
										</div>
									</div>
									<div class="row">
										<div class='col-md-12'>
											<input type='text' class='form-control stuname' />
										</div>
									</div>
									<div class="row">
										<div class='col-md-12'>
											شماره دانشجویی
										</div>
									</div>
									<div class="row">
										<div class='col-md-12'>
											<input type='text' class='form-control stuid' />
										</div>
									</div>
									رای:
									<div class="row">
										<div class='col-md-12 valid'>
											
										</div>
									</div>
								</div>
							</div>
							<div class='col-md-4'>
								<div class='prior active'>
									<div class="row">
										<div class='col-md-12'>
											اولویت اول
										</div>
									</div>
									<div class="row">
										<div class='col-md-12'>
											<input type='text' class='form-control stuname' />
										</div>
									</div>
									<div class="row">
										<div class='col-md-12'>
										شماره دانشجویی
										</div>
									</div>
									<div class="row">
										<div class='col-md-12'>
											<input type='text' class='form-control stuid' />
										</div>
									</div>
									رای:
									<div class="row">
										<div class='col-md-12 valid'>
											
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class='col-md-4 col-md-offset-4'>
								<input id=sub type='button' class='form-control btn-success' value='ثبت' />
							</div>
						</div>
						<br>
						<div class="row">
							<div class='col-md-6 col-md-offset-3'>
								<input id=notme type='button' class='form-control btn-warning' value='اسم من در این ترین نمایش داده نشود' />
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
		
		<div class="row submit-vote">
		<div class="col-md-4 col-md-offset-4">
		<br>
		<br>
		<br>
		<br>
		<div class="panel panel-default">
		  <div class="panel-heading">
			<h3 class="panel-title">در حال ثبت رای شما</h3>
		  </div>
		  <div class="panel-body">
				<p class='sub-msg'>
					لطفا صبر کنید. رای شما در حال ثبت شدن است...
				</p>
		  </div>
		</div>
		</div>
		</div>
		<div class='waiting-ajax hide'><span class='glyphicon glyphicon-repeat waiting' aria-hidden='true'></span></div>
		<script>
// variables
var tarin_i = 0 ;
var ajax_count = 0;
var tarin = "";
var prior_i = 1 ;
var edit = 0;

// functions
function submitVotes()
{
	ajax_count ++ ;
	$.ajax({
		type: "POST",
		url: "tarinha.php",
		data: "tarin=" + tarin_i+"&newtarin="+tarin[tarin_i] + "&lastTarin=" + (tarin_i+1), // "tarin=" + escape(JSON.stringify(tarin)),
		dataType: "JSON",
		async: true,
		success: function(res){ 
			ajax_count --;
			if ( res['result'] == "OK" )
			{
				$(".sub-msg").html("رای شما ثبت شد :) <a href='home.php'>بازگشت</a>");
			}
			else
			{
				$(".sub-msg").html("رای شما ثبت نشد :( لطفا ارتباط اینترنت خود را چک کنیدو مجددا تلاش کنید. <a href='home.php'>بازگشت</a>");
			}
			$(".waiting-bg").remove();
		},
		dataType: "JSON",
		error: function(xhr, status, error){
			ajax_count --;
			console.log("(" + xhr.responseText + ")");
			$(".sub-msg").html("رای شما ثبت نشد :( لطفا ارتباط اینترنت خود را چک کنیدو مجددا تلاش کنید. <a href='home.php'>بازگشت</a>");
			$(".waiting-bg").remove();
		}
	});
}
function editVotes()
{
	ajax_count ++ ;
	$.ajax({
		type: "POST",
		url: "tarinha.php",
		data: "etarin=" + edit+"&newtarin="+tarin[tarin_i],
		dataType: "JSON",
		async: true,
		success: function(res){ 
			ajax_count -- ;
			if ( res['result'] == "OK" )
			{
				$(".sub-msg").html("رای شما ویرایش شد :) <a href='tarinstate.php'>بازگشت</a>");
			}
			else
			{
				$(".sub-msg").html("رای شما ثبت نشد :( لطفا ارتباط اینترنت خود را چک کنیدو مجددا تلاش کنید. <a href='tarinstate.php'>بازگشت</a>");
			}
			$(".waiting-bg").remove();
		},
		dataType: "JSON",
		error: function(xhr, status, error){
			ajax_count -- ;
			console.log("(" + xhr.responseText + ")");
			 
			$(".sub-msg").html("رای شما ثبت نشد :( لطفا ارتباط اینترنت خود را چک کنیدو مجددا تلاش کنید. <a href='tarinstate.php'>بازگشت</a>");
			$(".waiting-bg").remove();
		}
	});
}
function getMyTarin(myUserId){
	ajax_count ++ ;
	$.ajax({
		type: "POST",
		url: "tarinha.php",
		data: "gettarin=" + myUserId,
		async: false,
		dataType: "JSON",
		success: function(res){ 
			ajax_count --;
			tarin_i = res['_lastTarin'];
		},
		dataType: "JSON",
		error: function(xhr, status, error){
			ajax_count -- ;
			console.log("(" + xhr.responseText + ")");
			$(".sub-msg").html("بازیابی اطلاعات شما ممکن نیست!<a href='home.php'>بازگشت</a>");
			$(".waiting-bg").remove();
		}
	});

}
/*function setMyTarin(tarin_i){
	$.ajax({
		type: "POST",
		url: "tarinha.php",
		data: "settarin=" +tarin_i,
		async: false,
		dataType: "JSON",
		success: function(res){ 
			alert(JSON.stringify(res));
		},
		dataType: "JSON",
		error: function(xhr, status, error){
			console.log("(" + xhr.responseText + ")");
			$(".sub-msg").html("بازیابی اطلاعات شما ممکن نیست!<a href='home.php'>بازگشت</a>");
			$(".waiting-bg").remove();
		}
	});
	
}*/
function hasDuplicates(array) 
{
	var valuesSoFar = Object.create(null);
	for (var i = 0; i < array.length; ++i) 
	{
		var value = array[i];
		if(value){
			if (value in valuesSoFar) {
				return true;
			}
			valuesSoFar[value] = true;
		}
	}
	return false;
}
function levenshteinDistance (a, b){
	if(a.length == 0) return b.length; 
	if(b.length == 0) return a.length; 

	var matrix = [];

	// increment along the first column of each row
	var i;
	for(i = 0; i <= b.length; i++){
		matrix[i] = [i];
	}

	// increment each column in the first row
	var j;
	for(j = 0; j <= a.length; j++){
		matrix[0][j] = j;
	}

	// Fill in the rest of the matrix
	for(i = 1; i <= b.length; i++){
		for(j = 1; j <= a.length; j++){
			if(b.charAt(i-1) == a.charAt(j-1)){
				matrix[i][j] = matrix[i-1][j-1];
			} else {
				matrix[i][j] = Math.min(matrix[i-1][j-1] + 1, // substitution
					Math.min(matrix[i][j-1] + 1, // insertion
							 matrix[i-1][j] + 1)); // deletion
			}
		}
	}

	return matrix[b.length][a.length];
}

// main 
setInterval(function(){
	if ( ajax_count > 0 )
	{
		$(".waiting-ajax").removeClass('hide');
	}
	else
	{
		$(".waiting-ajax").addClass('hide');
	}
},100);

do {
	edit = [<?php if(isset ( $_GET['edit'])) echo $_GET['edit'] ; else echo "false";?>];
	var data = {<?php 
		$mrows = Select ( "*", $GLOBALS['generation'], "jashnid=?", array(getJashnID()) );
		$start = true;
		while ( $mrow = $mrows->fetch() )
		{
			if ( $start == false )
				echo ",";
			echo '"'.$mrow['id'].'": "'.$mrow['name'].' '.$mrow['family'].'"';
			$start = false;
		}
	?>};
	tarin = [<?php 
		$mrows = Select ( "*", $GLOBALS['tbl_bestlist'], "jashnid=?", array(getJashnID()) );
		$start = true;
		while ( $mrow = $mrows->fetch() )
		{
			if ( $start == false )
				echo ",";
			echo '"'.$mrow['_best'].'"';
			$start = false;
		}
	?>];

	var myUserId = <?php echo $_SESSION['user']; ?>;
	if ( edit == "false" ){
		tarin_i = parseInt(<?php $nrows = Select ( "_lastTarin", $GLOBALS['tbl_users'], "id=?",array( $_SESSION['user']));
								if ( $nrow = $nrows->fetch() ) {
									echo $nrow["_lastTarin"];
								} else {
									echo "0";
								}?>);
	}
	else {
		tarin_i = parseInt(edit);
		ajax_count ++ ;
		$.ajax({
			type: "POST",
			url: "tarinha.php",
			data: "gettarinName=" + tarin_i,
			async: false,
			dataType: "text",
			success: function(res){ 
				ajax_count --;
				tarin[tarin_i]=res;
			},
			dataType: "text",
			error: function(xhr, status, error){
				ajax_count --;
				console.log("(" + xhr.responseText + ")");
			}
		});
	}
	while ( tarin_i < tarin.length && tarin[tarin_i] == '---' ){ 
		tarin_i ++ ;
	}
	if(tarin_i>=tarin.length && edit=="false"){
		$('.theme-showcase').addClass('hide');
		$('.submit-vote').show();
		$(".sub-msg").html("شما قبلا رای داده اید! <a href='home.php'>بازگشت</a><br> از <a href='tarinstate.php'>این</a> قسمت می توانید رای های خود را مشاهده و یا تغییر دهید");
		$(".waiting-bg").remove();
	}
	for ( var i in data )
	{
		$(".names").append("<div class='row'><div class='col-md-12'><div class='person' userid=" + i + " >" + data[i] + "</div></div></div>");
	}

	$(".tarin-title").text( tarin[tarin_i] );
	$(".progress-bar").attr("aria-valuenow", parseInt("" + ( tarin_i / tarin.length ) * 100) );
	$(".progress-bar").text( parseInt("" + ( tarin_i / tarin.length ) * 100) + "%" );
	$(".progress-bar").css({ "width": parseInt("" + ( tarin_i / tarin.length ) * 100) + "%" });
	$(".names-parent").height ( $(".maindiv").height() );
	$(".person").click(function(){
		$(".active .stuname").val ( $(this).text());
		$(".active .stuid").val ( $(this).attr('userid'));
		$(".active .valid").text ( $(this).text() );
		$(".active .valid").attr ( "userid", $(this).attr('userid') );
	});
	$(".prior").click(function(){
		$(".active").each(function(){ $(this).removeClass("active"); });
		$(this).addClass("active");
	});
	$(".stuid,.stuname").click(function(){
		$(".active").each(function(){ $(this).removeClass("active"); });
		$(this).parent().parent().parent().addClass("active");
	});
	$("body").on("keyup",".active .stuid",function(){
		$(".person").each(function(){
			if ( $(".active .stuid").val() == $(this).attr('userid') )
			{
				$(".active .stuname").val( $(this).text() );
				$(".active .valid").text ( $(this).text() );
				$(".active .valid").attr ( "userid", $(this).attr('userid') );
			}
		});
	});
	$("body").on("keyup",".active .stuname",function(){
		var contains = null;
		var best = null;
		var best_dist = -1;
		var name = $(".active .stuname").val().replace("ي","ی");
		$(".person").each(function(){
			var name2 = $(this).text().replace("ي","ی");
			if ( name2.indexOf ( name ) > -1 )
			{
				contains = $(this);
			}
			if ( best_dist == -1 )
			{
				best_dist = levenshteinDistance ( name , name2 );
				best = $(this);
			}
			else 
			{
				var dist = levenshteinDistance ( name , name2 );
				if ( dist < best_dist )
				{
					best_dist = dist ;
					best = $(this);
				}
			}
		});
		if ( contains != null )
		{
			best = contains;
		}
		$(".glow").each(function(){
			$(this).removeClass("glow");
		});
		$('.names').mCustomScrollbar( 'scrollTo', Math.max ( 0 , best.parent().parent()[0].offsetTop - ($(".maindiv").height() / 2) ));
		best.addClass("glow");

		$(".person").each(function(){
			if ( $(".active .stuname").val() == $(this).text() )
			{
				$(".active .stuid").val( $(this).attr('userid') );
				$(".active .valid").text ( $(this).text() );
				$(".active .valid").attr ( "userid", $(this).attr('userid') );
			}
		});
	});
	(function($){
		$(window).load(function(){
			$(".names").mCustomScrollbar({
				axis:"y",
				theme:"rounded-dots"
			});
		});
	})(jQuery);
	$(".valid").each(function(){
		$(this).attr('userid',"");
	});
	$("#sub").click(function(){
		var str = "";
		var str_c = [];
		var str_cc = 0;
		$(".valid").each(function(){
			str += $(this).attr('userid')+ "^";
			str_c[str_cc]=$(this).attr('userid');
			str_cc++;
		});
		if(hasDuplicates(str_c)){
			$(".stuname,.stuid").each(function(){
				$(this).val("");
			});
			$(".valid").each(function(){
				$(this).text("");
				$(this).attr("userid","");
			});
			alert("در رای شما تکرار وجود دارد!");
			return false;
		}
		if(edit=="false"){
			tarin[tarin_i] = str;
			submitVotes();
			do { 
				tarin_i ++ ;
			} while ( tarin_i < tarin.length && tarin[tarin_i] == '---' );

			//localStorage.setItem("tarins_" + myUserId, JSON.stringify(tarin));
			//localStorage.setItem("tarin_i_" + myUserId, tarin_i);
			//setMyTarin(tarin_i);

			if ( tarin_i >= tarin.length )
			{
				$('.theme-showcase').addClass('hide');;
				$('.submit-vote').show();
				$(".sub-msg").html("رای شما ثبت شد :) <a href='home.php'>بازگشت</a>"); 
				return true;
			}
			$(".tarin-title").text( tarin[tarin_i] );
			$(".progress-bar").attr("aria-valuenow", parseInt("" + ( tarin_i / tarin.length ) * 100) );
			$(".progress-bar").text( parseInt("" + ( tarin_i / tarin.length ) * 100) + "%" );
			$(".progress-bar").css({ "width": parseInt("" + ( tarin_i / tarin.length ) * 100) + "%" });
			$(".stuname,.stuid").each(function(){
				$(this).val("");
			});
			$(".valid").each(function(){
				$(this).text("");
				$(this).attr("userid","");
			});
		}else {//////////////////editing a tarin
			tarin[tarin_i] = str;
			$('.theme-showcase').addClass('hide');
			$('.submit-vote').show();
			editVotes();
			return true;
		}
	});
	$("#notme").click(function(){
		if(edit=="false"){
			var str = "";
			var str_c=[];
			var str_cc=0;
			$(".valid").each(function(){
					str += $(this).attr('userid')+ "^";
					str_c[str_cc]=$(this).attr('userid');
					str_cc++;
				});
				if(hasDuplicates(str_c)){
				$(".stuname,.stuid").each(function(){
					$(this).val("");
				});
				$(".valid").each(function(){
					$(this).text("");
					$(this).attr("userid","");
				});
				alert("در رای شما تکرار وجود دارد!");
				return false;
			}
			tarin[tarin_i] = "-1";
			submitVotes();
			do { 
				tarin_i ++ ;
			} while ( tarin_i < tarin.length && tarin[tarin_i] == '---' );
		//	localStorage.setItem("tarins_" + myUserId, JSON.stringify(tarin));
		//	localStorage.setItem("tarin_i_" + myUserId, tarin_i);
			//setMyTarin(tarin_i);
			if ( tarin_i >= tarin.length )
			{
				tarin[tarin_i] = str;
				$('.theme-showcase').addClass('hide');
				$('.submit-vote').show();
				$(".sub-msg").html("رای شما ثبت شد :) <a href='home.php'>بازگشت</a>");
				
				return true;
			}
			$(".tarin-title").text( tarin[tarin_i] );
			$(".progress-bar").attr("aria-valuenow", parseInt("" + ( tarin_i / tarin.length ) * 100) );
			$(".progress-bar").text( parseInt("" + ( tarin_i / tarin.length ) * 100) + "%" );
			$(".progress-bar").css({ "width": parseInt("" + ( tarin_i / tarin.length ) * 100) + "%" });
			$(".stuname,.stuid").each(function(){
				$(this).val("");
			});
			$(".valid").each(function(){
				$(this).text("");
				$(this).attr("userid","");
			});
	}else{//editing a tarin
		tarin[tarin_i] = "-1";
		$('.theme-showcase').addClass('hide');
		$('.submit-vote').show();
		
		editVotes();
		return true;
	}
	});
} while ( 0 );
		</script>
	<?php require('footer.php'); ?>
</html>
<?php }
	
	else{
		echo "Invalid session! <a href='logout.php'>Logout</a>";
		CloseDatabase();
		die();
	}
	CloseDatabase();
?>
