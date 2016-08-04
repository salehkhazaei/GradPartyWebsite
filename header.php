<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script src="js/jquery.min.js" type="text/javascript"></script>
		<script src="js/bootstrap.min.js" type="text/javascript"></script>
		<title>جشن فارغ التحصیلی</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="css/bootstrap.min.css" />
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
			.page-header{
				direction: rtl;
				text-align: left;
				border: none;
				margin: 0px;
				text-align: center;
				color: #555;
			}
			header {
				background: #fff;
				color: black;
			}
			.navbar {
				background: #eee;
				border-top: 1px solid #ccc;
				border-bottom: 1px solid #ccc;
				filter: none;
			}
			li a,sr-only{
				color: #333 !important;
				font-size: 13px;
				background-image: none !important;
				background-repeat: no-repeat !important;
				filter: none !important;
			}
			li.active a,.sr-only{
				color: #fff !important;
				background-color: rgb(40,40,40) !important;
			}
			li.active a:hover,.sr-only:hover{
				color: #000 !important;
				background-color: rgb(40,40,40) !important;
			}
			li a:hover,.sr-only:hover{
				background-color: rgb(40,40,40) !important;
				color: white !important;
			}
			.navbar-nav li{
				font-size: 1.3em;   
			}
			.navbar-collapse{
				margin-left: -15px !important;
				margin-right: -15px !important;
				
				padding: 0px;
			}

			.theme-showcase{
				margin-top: 50px;
			}
			
			#chroomtitle {
				color: white;
				text-align: center;
				font-family: eras;
				font-size: 16pt;
				text-align: center;
				font-weight: bold;
				color: #fff;
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
			#captcha_code {
				color: black;
			}
			.imglabel {
				z-index: 10;
				position: relative;
				width: 150px;
				height: 150px;
				background-position: center center;
				background-repeat: no-repeat;
				background-size: 150px auto;
				border-radius: 150px;
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
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
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
			.group-orange { background: rgb(235,181,4); }
			.group-orange:hover { background: rgb(245,211,14); }
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
			.group-lbl {
				margin-top: 5px;
				margin-bottom: 5px;
				padding-top: 5px;
				padding-bottom: 5px;
				border-radius: 5px;
				color: white;
			}
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
			}
            .no_of_votes {
                background-color: #333;
                color: #eee;
                padding: 20px;
                position: fixed;
                top: 50%;
                left: 20%;
                width: auto;
                height: auto;
            }
            .time{
                font-size: 8pt;
                text-align: right;
            }
            .closing{
                font-size: 8pt;
                text-align: right;
                color: red;
            }
		</style>
	</head>
	<body  dir=rtl>
		<header>
			<div class="container">
				<div class="page-header">
                    <div class='row'>
                        <div class='col-md-6 col-md-offset-3'>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <h3><?php echo getJashnTitle(); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-12'>
                            <div class='time'><?php echo now(); ?></div>
                        </div>
                        <script>
                            function tr_num ( str, mod='en', mf='٫' )
                            {
                                var num_a=['0','1','2','3','4','5','6','7','8','9','.'];
                                var key_a=['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹',mf];
                                
                                for ( var i = 0 ; i < num_a.length ; i ++ )
                                {
                                    str = (mod=='fa') ? str.split(num_a[i]).join(key_a[i]) : str.split(key_a[i]).join(num_a[i]);
                                }
                                return str;
                            }
                            

                            var sec = parseInt($(".tsec").attr('val'));
                            var min = parseInt($(".tmin").attr('val'));
                            var hor = parseInt($(".thour").attr('val'));
                            var day = parseInt($(".tday").attr('val'));
                            setInterval(function(){
                                sec --;
                                if ( sec < 0 )
                                {
                                    min --;
                                    sec += 60;
                                }    
                                if ( min < 0 )
                                {
                                    hor --;
                                    min += 60;
                                }    
                                if ( hor < 0 )
                                {
                                    day --;   
                                    hor += 24;
                                }    
                                $(".tsec").text(tr_num(sec + "",'fa'));
                                $(".tmin").text(tr_num(min + "",'fa'));
                                $(".thour").text(tr_num(hor + "",'fa'));
                                $(".tday").text(tr_num(day + "",'fa'));
                            },1000);
                        </script>
                    </div>
				</div>
			</div>
		</header>
		<nav class="navbar navbar-default" role="navigation">
			<div class="container backcont">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<b><ul class="nav navbar-nav navbar-right">
						<li ><a href="logout.php">خروج<span class="sr-only">(current)</span></a></li>
						<?php if ( isAdmin() ) { ?><li ><a href="admin.php">مدیریت<span class="sr-only">(current)</span></a></li><?php } ?>
						<?php if ( isSubAdmin('_babyManager') ) { ?><li ><a href="babymanage.php">مدیریت عکس بچگی<span class="sr-only">(current)</span></a></li><?php } ?>
						<?php if ( isSubAdmin('_majaleManager') ) { ?><li ><a href="majale.php">مدیریت مجله<span class="sr-only">(current)</span></a></li><?php } ?>
						<?php if ( isSubAdmin('_sleepManager') ) { ?><li ><a href="sleepmanage.php">مدیریت عکس های جالب<span class="sr-only">(current)</span></a></li><?php } ?>
						<?php if ( isSubAdmin('_tarinManager') ) { ?><li ><a href="tarinresult.php">مدیریت ترین ها<span class="sr-only">(current)</span></a></li><?php } ?>
						<li ><a href="download.php">دانلود<span class="sr-only">(current)</span></a></li>
						<li ><a href="majale_preview.php">نسخه اولیه مجله<span class="sr-only">(current)</span></a></li>
						<li ><a href="tarinha.php">ترین ها<span class="sr-only">(current)</span></a></li>
						<li ><a href="status.php">شرکت کنندگان<span class="sr-only">(current)</span></a></li>
						<li ><a href="polls.php">رای گیری ها<span class="sr-only">(current)</span></a></li>
						<li ><a href="sleep.php">عکس های جالب<span class="sr-only">(current)</span></a></li>
						<li ><a href="baby.php">مسابقه عکس بچگی<span class="sr-only">(current)</span></a></li>
						<li ><a href="profile.php">پروفایل و مجله<span class="sr-only">(current)</span></a></li>
						<li ><a href="home.php">صفحه اصلی<span class="sr-only">(current)</span></a></li>
					</ul></b>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</nav>
