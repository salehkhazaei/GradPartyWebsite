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
	
	if ( isset ( $_POST ['tarin'] ) )
	{
		Delete ( $GLOBALS['tbl_khatere'], "_userid=? and _targetid=?", array ( $_SESSION['user'] ,$_POST ['tarin']) );
		if ( strlen( trim( $_POST['newtarin'] ) ) > 0 )
		{
			Create ( $GLOBALS['tbl_khatere'], "(_userid,_targetid,_text)", "(?,?,?)", array (
					$_SESSION['user'], $_POST ['tarin'], trim( urldecode($_POST['newtarin']) )));
			echo "{\"result\": \"OK\"}";
			CloseDatabase();
			die();
		}
		echo "{\"result\": \"NOK\"}";
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
				<h1>خاطره با دوستان</h1>
				<p>
					لطفا به نکات زیر دقت فرمایید:<br>
					<br>
- لطفا دقت کنید که پس از انتخاب نام فرد مورد نظر، حتما نام او در بالای صفحه نوشته شده باشد.<br><br>
- لطفا تا زمانی که علامت زیر در گوشه بالا سمت راست نمایش داده می شود پنجره را نبندید، زیرا تا زمانی که این علامت حذف 
نشود خاطرات شما روی سرور ذخیره نخواهد شد.<br>
<center><span class='glyphicon glyphicon-repeat waiting' aria-hidden='true'></span></center><br>
- لطفا پس از ثبت خاطرات از بخش خاطرات من صحت اطلاعات را بررسی نمایید<br>
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
			$("#myModal").modal('show');
		</script>
		<div class="container theme-showcase" role="main">
			<div class='row'>
				<div class='col-md-12'>
					<div class="row">
						<div class='col-md-2'>
							<a href='home.php'><div class='group-btn group-red' style='text-align: center;'><h3>بازگشت</h3></div></a>
						</div>
						<div class='col-md-5'>
							<a href='khaterestate.php'><div class='group-btn group-red' style='text-align: center;'><h3>خاطرات من با بقیه</h3></div></a>
						</div>
						<div class='col-md-5'>
							<a href='khaterestate_me.php'><div class='group-btn group-sky' style='text-align: center;'><h3>خاطرات بقیه با من</h3></div></a>
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
						<div id='mainrow' class="row post-text">
							<div class='col-md-12'>
								<p>
									<div class='group-btn group-red'>توجه: لطفا دقت کنید که پس از انتخاب نام فرد مورد نظر، حتما نام او در بالای صفحه نوشته شده باشد.</div>
								</p>
							</div>
						</div>
						<br><br>
						<div class="row">
							<div class='col-md-12' style='text-align: right;'>
								<p>لطفا نام فرد مورد نظر را وارد کنید.<br>همچنین می توانید از لیست سمت چپ فرد را انتخاب کنید</p>
                                <h3>دقت کنید برای هر نفر فقط یک خاطره می توانید بنویسید</h3>
							</div>
						</div>
						<div class='prior active'>
							<div class="row post-title">
								<div class='col-md-12'>
									<center><h1 class='tarin-title valid'>
										
									</h1></center>
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
							<div class="row">
								<div class='col-md-12'>
									خاطره
								</div>
							</div>
							<div class="row">
								<div class='col-md-12'>
									<textarea class='form-control txtkhatere' maxlength=600 ></textarea>
								</div>
							</div>
						</div>
						<div class="row">
							<div class='col-md-4 col-md-offset-4'>
								<input id=sub type='button' class='form-control btn-success' value='ثبت' />
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
			<h3 class="panel-title">در حال ثبت خاطرات شما</h3>
		  </div>
		  <div class="panel-body">
				<p class='sub-msg'>
					لطفا صبر کنید. خاطرات شما در حال ثبت شدن است...
				</p>
		  </div>
		</div>
		</div>
		</div>
		<div class='waiting-ajax hide'><span class='glyphicon glyphicon-repeat waiting' aria-hidden='true'></span></div>
		<script>
// variables
var ajax_count = 0;

// functions
function submitVotes()
{
	ajax_count ++ ;
	console.log("tarin=" + $(".valid").attr("userid")+"&newtarin="+encodeURI($(".txtkhatere").val()));
	$.ajax({
		type: "POST",
		url: "khatereh.php",
		data: "tarin=" + $(".valid").attr("userid")+"&newtarin="+encodeURI($(".txtkhatere").val()),
		dataType: "JSON",
		async: true,
		success: function(res){ 
			ajax_count --;
			console.log(res);
			if ( res['result'] == "OK" )
			{
				$(".sub-msg").html("خاطره شما ثبت شد :) <a href='home.php'>بازگشت</a>");
			}
			else
			{
				$(".sub-msg").html("خاطره شما ثبت نشد :( لطفا ارتباط اینترنت خود را چک کنیدو مجددا تلاش کنید. <a href='home.php'>بازگشت</a>");
			}
			$(".waiting-bg").remove();
		},
		dataType: "JSON",
		error: function(xhr, status, error){
			ajax_count --;
			console.log("(" + xhr.responseText + ")");
			$(".sub-msg").html("خاطره شما ثبت نشد :( لطفا ارتباط اینترنت خود را چک کنیدو مجددا تلاش کنید. <a href='home.php'>بازگشت</a>");
			$(".waiting-bg").remove();
		}
	});
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

	var myUserId = <?php echo $_SESSION['user']; ?>;
	for ( var i in data )
	{
		$(".names").append("<div class='row'><div class='col-md-12'><div class='person' userid=" + i + " >" + data[i] + "</div></div></div>");
	}

	$(".tarin-title").text( "" );
	$(".names-parent").height ( $(".maindiv").height() );
	$(".person").click(function(){
		$(".active .stuname").val ( $(this).text());
		$(".active .txtkhatere").val ( "");
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
		submitVotes();
		$(".tarin-title").text( "" );
		$(".stuname,.stuid,.txtkhatere").each(function(){
			$(this).val("");
		});
		$(".valid").each(function(){
			$(this).text("");
			$(this).attr("userid","");
		});
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
