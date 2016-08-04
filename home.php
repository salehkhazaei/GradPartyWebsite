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
		if ( isset ( $_POST ['post'] ) )
		{
			$hashtags = "";
			preg_match_all("/(#[^\s,]+)/", $_POST ['post'], $matches);
			foreach ($matches[0] as $hashtag)
				$hashtags .= substr($hashtag,1).' ';
				
			$groupid = -1 ;
			if ( isset ( $_POST['groupid'] ) )
			{
				$groupid = $_POST['groupid'];
			}
			Create ( $GLOBALS['tbl_posts'], "(_group,_writer,_text,_tags,_time)", "(?,?,?,?,NOW())", array($groupid,$_SESSION['user'],$_POST['post'],$hashtags) );
		}
		if ( isset ( $_GET ['delete'] ) )
		{
			$crows = Select ( "*", $GLOBALS['tbl_posts'], "id=? AND _writer=?", array ($_GET['delete'],$_SESSION['user']) );
			if ( $crow = $crows->fetch() )
			{
				Delete ( $GLOBALS['tbl_posts'], "id=? AND _writer=?", array ($_GET['delete'],$_SESSION['user']) );
				Delete ( $GLOBALS['tbl_posts'], "_parent=?", array ($_GET['delete']) );
				Delete ( $GLOBALS['tbl_likes'], "_postid=?", array ($_GET['delete']) );
				redirect('home.php');
				CloseDatabase();
				die();
			}
		}
        if ( isset ( $_POST['group_name'] ) )
        {
			Create ( $GLOBALS['tbl_groups'], "(_admin,jashnid,_name,_color)", "(?,?,?,?)", array($_POST['group_admin'],getJashnID(),$_POST['group_name'],$_POST['group_color']) );
            redirect('home.php');
            CloseDatabase();
            die();
        }
	require('header.php');
?>
		<style>
			#groups {
				position: fixed;
				right: -195px;
				width: 200px;
				background: #444;
				height: 100%;
				top: 130px;
				border-left: 1px solid #aaa;
				border-top: 1px solid #aaa;
				border-top-left-radius: 5px;
				z-index: 1050;
			}
			#groupsbtn {
				position: fixed;
				right: -31px;
				top: 200px;
				width: 100px;
				z-index: 1060;
				background: #444;
				color: #fff;
				height: 30px;
				text-align: center;
				font-size: 14pt;
				padding-top: 5px;
				border-top-left-radius: 20px;
				border-top-right-radius: 20px;
				-ms-transform: rotate(-90deg); /* IE 9 */
				-webkit-transform: rotate(-90deg); /* Chrome, Safari, Opera */
				transform: rotate(-90deg);
				border-left: 1px solid #aaa;
				border-right: 1px solid #aaa;
				border-top: 1px solid #aaa;
				cursor: pointer;
			}
			.glyphicon-comment{
				color: #ccc;
				cursor: pointer;
			}
			.glyphicon-comment:hover{
				color: #c00;
			}
			.glyphicon-heart{
				color: #ccc;
				cursor: pointer;
			}
			.glyphicon-heart:hover{
				color: #c00;
			}
			.glyphicon-heart.liked{
				color: #e00;
			}
			.comment { 
				position: relative;
				z-index: 900;
				background: #444;
				padding: 0px;
				margin: 0px;
				margin-top: -4px;
				border-bottom-right-radius: 5px;
				border-bottom-left-radius: 5px;
				color: white;
			}
			.comment-title {
				padding: 10px;
				padding-bottom: 0px;
			}
			.comment-name {
				margin: 10px;
				margin-bottom: 0px;
				margin-top: 5px;
				padding-top: 5px;
				border-top: 1px solid #666;
			}
			.comment-text {
				margin-right: 10px;
				margin-left: 10px;
				padding-right: 20px;
				padding-left: 20px;
			}
			.comment-box {
				padding: 10px;
				padding-top: 5px;
			}
		</style>
		<link href="css/jquery-ui.min.css" rel="stylesheet">
		<script src="js/jquery-ui.min.js"></script>
        <?php 
            if ( isAdmin() )
            { ?>
		<div id=myModal class="modal fade" tabindex="-1" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-body">
			  <center>
				<h1>گروه جدید</h1>
                <form method=post>
                    <div class='row'><div class='col-md-12'>
                        نام گروه:
                        <input type='text' class='form-control' name='group_name' placeholder="نام گروه" required />
                    </div></div>
                    <div class='row'><div class='col-md-12'>
                        شماره دانشجویی مسئول گروه:
                        <input type='text' maxlength=7 class='form-control' name='group_admin' placeholder="شماره دانشجویی" required />
                    </div></div>
                    <div class='row'><div class='col-md-12'>
                        رنگ گروه:
                        <select class='form-control' name='group_color'>
                            <option value='red'>قرمز</option>
                            <option value='yellow'>زرد</option>
                            <option value='black'>مشکی</option>
                            <option value='sky'>آبی اسمانی</option>
                            <option value='mag'>بنفش</option>
                            <option value='blue'>آبی</option>
                            <option value='green'>سبز</option>
                        </select>
                    </div></div>
                    <div class='row'><div class='col-md-12'>
                        <input type='submit' class='form-control' value="ثبت" />
                    </div></div>
                </form>
			  </div>
			</div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
            <?php }
        ?>
		<div id=groups class="container">
			<div class='row'>
				<div class='col-md-12'>
					<?php 
						$wrows = Select ( "*" , $GLOBALS['tbl_groups'], "jashnid=?", array(getJashnID()) );
						while ( $wrow = $wrows->fetch() )
						{
							echo "<div groupid=".$wrow['id']." class='group-btn group-".$wrow['_color']."'>".$wrow['_name']."</div>";
						}
                        if ( isAdmin() )
                        {
                            echo "<hr><div id=newgroup class='btn group-white'>گروه جدید</div>
                            <script>
                                $('#newgroup').click(function(){
                                    $('#myModal').modal('show');
                                });
                            </script>";
                        }
					?>
				</div>
			</div>
		</div>
		<div id=groupsbtn>
			گروه ها
		</div>
		<div id=posts class="container theme-showcase" role="main">
			<div class='row'>
				<div class='col-md-6 col-md-offset-3'>
					<form method='post' enctype="multipart/form-data">
						<input type='hidden' name='groupid' value='<?php if ( isset ( $_GET['group'] ) ) echo $_GET['group']; else echo "-1"; ?>' />
						<div class='row'><div class='col-md-12'><textarea  rows=5 name='post' class='form-control' placeholder="پست جدید" ></textarea></div></div>
						<div class='row'><div class='col-md-offset-8 col-md-4'><input id='save_profile' type='submit' class='form-control btn-success' value="ثبت" /></div></div>
					</form>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-4 left'>
				</div>
				<div class='col-md-4 center'>
				</div>
				<div class='col-md-4 right'>
				</div>
			</div>
        </div>
	<?php require('footer.php'); ?>
	<script>
		$(function(){
			var c = 0;
			$("#groups,#groupsbtn").mouseenter(function(){
				$("#groups").stop();
				$("#groupsbtn").stop();
			
				$("#groups").animate({"right": "0px"},1000,"easeOutBounce");
				$("#groupsbtn").animate({"right": "164px"},1000,"easeOutBounce");
			});
			setTimeout(function(){
				$("#groupsbtn").animate({"top": "400px"},1000,"easeInOutQuart",function(){
					$("#groupsbtn").animate({"top": "200px"},1000,"easeInOutQuart");
				});
			},2000);
			setInterval(function(){
				$("#groupsbtn").animate({"top": "400px"},1000,"easeInOutQuart",function(){
					$("#groupsbtn").animate({"top": "200px"},1000,"easeInOutQuart");
				});
			},10000);
			$("#groupsbtn").click(function(){
				$("#groups").stop();
				$("#groupsbtn").stop();
			
				$("#groups").animate({"right": "0px"},1000,"easeOutBounce");
				$("#groupsbtn").animate({"right": "164px"},1000,"easeOutBounce");
			});
			$("#posts").mouseenter(function(){
				$("#groups").stop();
				$("#groupsbtn").stop();
			
				$("#groups").animate({"right": "-195px"},1000,"easeOutQuint");
				$("#groupsbtn").animate({"right": "-31px"},1000,"easeOutQuint");
			});
			$('body').on('click', '.glyphicon-heart', function(){
				var this_like = this;
				$.ajax({url: "ajax_posts.php?like=" + $(this).attr("postid") , dataType: "JSON", 
					error: function(error,err){
						console.log(error);
						console.log(err);
					},
					success: function(result){
						var elem = $(this_like).parent().parent().children(".like-number");
						if ( $(this_like).hasClass("liked") )
						{
							elem.text("" + (parseInt(elem.text()) - 1));
						}
						else
						{
							elem.text("" + (parseInt(elem.text()) + 1));
						}
						$(this_like).toggleClass("liked");
					}
				});
			});

			$("#posts").click(function(){
				$("#groups").stop();
				$("#groupsbtn").stop();
			
				$("#groups").animate({"right": "-195px"},1000,"easeOutQuint");
				$("#groupsbtn").animate({"right": "-31px"},1000,"easeOutQuint");
			});
			$(".group-btn").click(function(){
				window.location='home.php?group=' + $(this).attr('groupid');
			});
			$.ajax({url: "ajax_posts.php?<?php 
				if ( isset ( $_GET['group'] ) )
				{
					echo "group=".$_GET['group']."&";
				}
				else
				{
					echo "group=-1&";
				}
				if ( isset ( $_GET['tag'] ) )
				{
					echo "tag=".$_GET['tag']."&";
				}
				if ( isset ( $_GET['id'] ) )
				{
					echo "id=".$_GET['id']."&";
				}
			
			?>", dataType: "JSON",
				error: function(error,err){
					console.log(error);
					console.log(err);
				},
				success: function(result){
				for ( i in result.data )
				{
					var str = "<div><div class='post' postid=" + result.data[i].id + " >" +
						"<div class='row post-title'>" +
							"<div class='col-md-12 text'>" +
								"<a href='home.php?<?php if ( isset ( $_GET['group'] ) ) echo "group=".$_GET['group'].'&'; ?>id=" + result.data[i].writerid + "'>" + result.data[i].writer + "</a>:" +
							"</div>" +
						"</div>" +
						"<div class='row post-text'>" +
							"<div class='col-md-12 text'><p style='word-wrap: break-word;'>" +
								result.data[i].text +
							"</p></div>" +
						"</div>" +
						 "<div class='row post-hashtag'>" +
							'<div class="col-md-offset-1 col-md-1 like-number">' + result.data[i].likes + '</div><div class="col-md-1"><span postid=' + result.data[i].id + ' class="glyphicon glyphicon-heart ' + ( result.data[i].liked == "1" ? "liked" : "like" ) + '" aria-hidden="true"></span></div>' +
							( result.data[i].mine == 1 ? "<div class='col-md-2 text'><a class='btn-danger a-btn' href='home.php?delete=" + result.data[i].id + "'>حذف</a></div>" : "" ) +
						"</div>" +
					"</div><div class='comment'><div class='row'><div class='col-md-12'><div class='comment-title'>دیدگاه ها:</div></div></div>" ;

					$.ajax({url: "ajax_posts.php?parent=" + result.data[i].id, async: false, dataType: "JSON", 
						error: function(error,err){
							console.log(error);
							console.log(err);
						},
						success: function(res){
							for ( j in res.data )
							{
								str += "<div class='row comment-name'><div class='col-md-12'>" + res.data[j].writer + "</div></div>" +
										"<div class='row comment-text'><div class='col-md-12'><p style='word-wrap: break-word;'>" + res.data[j].text + "</p></div></div>" +
										 "<div class='row post-hashtag'>" +
											'<div class="col-md-offset-1 col-md-1 like-number">' + res.data[j].likes + '</div><div class="col-md-1"><span postid=' + res.data[j].id + ' class="glyphicon glyphicon-heart ' + ( res.data[j].liked == "1" ? "liked" : "like" ) + '" aria-hidden="true"></span></div>' +
											( res.data[j].mine == 1 ? "<div class='col-md-2 text'><a class='btn-danger a-btn' href='home.php?delete=" + res.data[j].id + "'>حذف</a></div>" : "" ) +
										"</div>";
							}
						}
					});
						
					str += "<div class='row comment-box-div'><div class='col-md-12'><div class='comment-box'><input postid=" + result.data[i].id + " type='text' class='form-control' placeholder='دیدگاه خود را بنویسید'/></div></div></div></div></div>";
					$( ( c == 0 ? ".right" : ( c == 1 ? ".center" : ".left" ) ) ).append($(str));
					c = ( c + 1 ) % 3;
				}
				hashtag_regexp = /(#[^\s,]+)/g;
				function linkHashtags(text) {
					return text.replace(
						hashtag_regexp,
						'<a class="hashtag">$1</a>'
					);
				} 
				$('p').each(function() {
					$(this).html(linkHashtags($(this).html()));
				});
				$('.hashtag').each(function() {
					$(this).attr("href", "home.php?<?php if ( isset ( $_GET['group'] ) ) echo "group=".$_GET['group'].'&'; ?>tag=" + $(this).text().substring(1));
				});
				$(".comment-box input").keydown(function(event){
					if ( event.which == 13 ) {
						event.preventDefault();
						var that = this;
						$.ajax({
							type: "POST",
							url: "ajax_posts.php?comment=" + $(that).attr("postid"),
							data: "text=" + $(that).val(),
							dataType: "JSON",
							success: function(res){ 
								$(".post").each(function(){
									if ( $(this).attr("postid") == $(that).attr("postid") )
									{
										$("<div class='row comment-name'><div class='col-md-12'>" + res.writer + "</div></div>" +
										"<div class='row comment-text'><div class='col-md-12'><p style='word-wrap: break-word;'>" + $(that).val() + "</p></div></div>" +
										 "<div class='row post-hashtag'>" +
											'<div class="col-md-offset-1 col-md-1 like-number">0</div><div class="col-md-1"><span postid=' + res.id + ' class="glyphicon glyphicon-heart like" aria-hidden="true"></span></div>' +
											"<div class='col-md-2 text'><a class='btn-danger a-btn' href='home.php?delete=" + res.id + "'>حذف</a></div>" +
										"</div>").insertBefore($(this).parent().find(".comment-box-div"));
									}
								});
								$(that).val(""); 
							},
							dataType: "JSON",
							error: function(error,err){
								console.log(error);
								console.log(err);
							}
						});
					}
				});
				
			}});			
		});
	</script>
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