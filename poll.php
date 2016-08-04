<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();

    $crows = Select ( "_pollsClose", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
    if ( $crow = $crows->fetch() )
    {
        if ( $crow[0] == 1 )
        {
            redirect("closed.php");
            CloseDatabase();
            die();
        }
    }
	
	if ( ! isset ( $_GET['pollid'] ) )
	{
		redirect("home.php");
		CloseDatabase();
		die();
	}
	
	$prows = Select ( "*", $GLOBALS['tbl_pollconf'], "id=? and jashnid=?", array ($_GET['pollid'],getJashnID()) );
	$prow = $prows->fetch();
	if ( $prow == null )
	{
		redirect("home.php");
		CloseDatabase();
		die();
	}

	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ( $row = $rows->fetch() )
	{
		if ( isset ( $_POST['poll'] ) )
		{
			if ( $prow['_done'] == 1 )
			{
				echo "<html><head><meta charset='UTF-8'></head><body>رای گیری به پایان رسیده است. <a href='home.php'>بازگشت</a></body></html>";
				CloseDatabase();
				die();
			}
			$erows = Select ( "*", $GLOBALS['tbl_polls'], "_pollid=? AND _id=?", array ( $_GET['pollid'], $_SESSION['user'] ));
			if ( $erow = $erows->fetch() )
			{
				Delete ( $GLOBALS ['tbl_polls'], "_pollid=? AND _id=?", array ( $_GET['pollid'], $_SESSION['user'] ));
			}
			Create ( $GLOBALS ['tbl_polls'] , "(_poll,_pollid,_id)", "(?,?,?)", array ( $_POST['poll'], $_GET['pollid'], $_SESSION['user'] ) );
		}
		else if ( isset ( $_POST['type'] ) )
		{
			if ( $prow['_done'] == 1 )
			{
				echo "<html><head><meta charset='UTF-8'></head><body>رای گیری به پایان رسیده است. <a href='home.php'>بازگشت</a></body></html>";
				CloseDatabase();
				die();
			}
			$erows = Select ( "*", $GLOBALS['tbl_polls'], "_pollid=? AND _id=?", array ( $_GET['pollid'], $_SESSION['user'] ));
			if ( $erow = $erows->fetch() )
			{
				Delete ( $GLOBALS ['tbl_polls'], "_pollid=? AND _id=?", array ( $_GET['pollid'], $_SESSION['user'] ));
			}
			$i = 0 ;
			
			foreach ( $_POST as $key => $value )
			{
				if ( strpos($key, 'opt') !== false )
				{
					$i ++ ;
					if ( $i > $prow ['_max'] )
						break;
					Create ( $GLOBALS ['tbl_polls'] , "(_poll,_pollid,_id)", "(?,?,?)", array ( (int)substr($key,3) , $_GET['pollid'], $_SESSION['user'] ) );
				}
			}
		}
	require('header.php');
        if ( $prow['_max'] > 0 )
        {
            echo "<div class='no_of_votes'>تعداد رای ها</div>";
        }
?>        
		<div class="container theme-showcase" role="main">
			<div class="row">
				<div class='col-md-12'>
					<?php
						$erows = Select ( "*", $GLOBALS['tbl_groups'], "id=?", array ( $prow['_group'] ));
						if ( $erow = $erows->fetch() )
						{
							echo "<div groupid=".$erow['id']." class='group-btn group-".$erow['_color']."'>".$erow['_name']."</div>";
						}
					?>
				</div>
	        </div>
			<div class='post'>
				<div class="row post-title">
					<div class='col-md-12'>
						<p>
							<?php echo $prow['_title']; ?>
						</p>
					</div>
				</div>
				<div id='mainrow' class="row post-text">
					<div class='col-md-12'>
						<p>
							<?php echo $prow['_text']."<br><br><div style='color: red'>حداکثر رای مجاز: ".$prow['_max']." رای</div>"; ?>
						</p>
					</div>
				</div>
			</div>
			<br>
			<br>
			<br>
			<div class="row">
				<div class='col-md-12'>
					<?php
						$erows = Select ( "*", $GLOBALS['tbl_polls'], "_pollid=? AND _id=?", array ( $_GET['pollid'], $_SESSION['user'] ));
						if ( $erow = $erows->fetch() )
						{
							echo "<div class='group-btn group-red'>شما قبلا رای داده اید، در صورتی که مجددا رای بدهید رای قبلی شما حذف خواهد شد.</div>";
						}
						
						$array = array();
						$erows = Select ( "*", $GLOBALS['tbl_pollopt'], "_pollid=?", array ( $_GET['pollid'] ));
						while ( $erow = $erows->fetch() )
						{
							$array[$erow['id']] = $erow['_optiontext'];
						}
						
						if ( $prow['_done'] == 1 )
						{
							$erows = Select ( "*", $GLOBALS['tbl_polls'], "_pollid=? AND _id=?", array ( $_GET['pollid'], $_SESSION['user'] ));
							echo "<div class='group-btn group-red'><h1>این رای گیری به پایان رسیده است :)</h1><br></div>";
						}

						$erows = Select ( "*", $GLOBALS['tbl_polls'], "_pollid=? AND _id=?", array ( $_GET['pollid'], $_SESSION['user'] ));
						echo "<div class='group-btn group-orange'>رای شما:<br>";
						while ( $erow = $erows->fetch() )
						{
							echo $array[$erow['_poll']]." - ";
						}
						echo "</div>";
					?>
				</div>
	        </div>
			<div class='row'>
				<div class='col-md-4 col-md-offset-4'>
					<form method=post>
						رای شما:
						<?php 
							if ( $prow['_type'] == 0 )
							{
								echo "<select name=poll class='form-control'>";
									$crows = Select ( "*" , $GLOBALS['tbl_pollopt'], "_pollid=?", array ($_GET['pollid']) );
									while ( $crow = $crows->fetch() )
									{
										echo '<option value="'.$crow['id'].'">'.$crow['_optiontext'].'</option>';
									}
								echo "</select>";
							}
							else if ( $prow['_type'] == 1 )
							{
								echo '<input type="hidden" name="type" value="check"/><br>';
								$crows = Select ( "*" , $GLOBALS['tbl_pollopt'], "_pollid=?", array ($_GET['pollid']) );
								while ( $crow = $crows->fetch() )
								{
									echo '<div class="check-vote"><input type="checkbox" name="opt'.$crow['id'].'" />'.$crow['_optiontext'].'</div><br>';
								}
							}
						?>
						<br>
						<input type="submit" class='form-control btn-success submitbtn' value="ثبت" />
					</form>
				</div>
			</div>
			
			<div class='row'>
				<div class='col-md-4 col-md-offset-4'>
					<?php 

						$count = 0;
						$yrows = Select ( "COUNT(tbl_polls._poll) as c", 
                                            $GLOBALS['tbl_pollopt']." inner join tbl_polls on tbl_pollopt.id=tbl_polls._poll", 
                                            "tbl_pollopt._pollid=? group by tbl_pollopt.id order by c desc limit 0,1", array($_GET['pollid']));
						if ($yrow = $yrows->fetch())
						{
							$count = $yrow[0];
						}
						if ( $count == 0 )
							$count = 1;

						$votes = 0;
						$yrows = Select ( "DISTINCT _id", $GLOBALS['tbl_polls'], "_pollid=?", array($_GET['pollid']));
						while ($yrow = $yrows->fetch())
						{
							$hrows = Select ( "id", $GLOBALS['tbl_users'], "id=? AND _verify > 1", array ($yrow[0]) );
							if ( $hrow = $hrows->fetch() )
							{
								$votes ++ ;
							}
						}

						echo "<div class='post'>
						<div class='row'>
							<div class='col-md-12'>
							تعداد افرادی که رای داده اند: ".$votes." نفر
							</div>
						</div>
						</div>";

						$trows = Select ( "*", $GLOBALS['tbl_pollopt'], "_pollid=?", array($_GET['pollid']));
						$arr = array();
						$maxarr = 0;
						while ($trow = $trows->fetch())
						{
							$yrows = Select ( "_id", $GLOBALS['tbl_polls'], "_pollid=? AND _poll=?", array($_GET['pollid'],$trow['id']));
							$ccc = 0;
							while ($yrow = $yrows->fetch())
							{
								$hrows = Select ( "id", $GLOBALS['tbl_users'], "id=? AND _verify > 1", array ($yrow[0]) );
								if ( $hrow = $hrows->fetch() )
								{
									$ccc ++;
								}
							}
							$arr[$trow['_optiontext']] = $ccc;
							if ( $maxarr < $ccc )
								$maxarr = $ccc;
						}
//SELECT *,COUNT(tbl_polls._poll) as c FROM `tbl_pollopt` inner join tbl_polls on tbl_pollopt.id=tbl_polls._poll WHERE tbl_pollopt._pollid=3 group by tbl_pollopt.id order by c desc
						$trows = Select ( "tbl_pollopt.id,tbl_pollopt._optiontext,COUNT(tbl_polls._poll) as c", 
                                            $GLOBALS['tbl_pollopt']." inner join tbl_polls on tbl_pollopt.id=tbl_polls._poll", 
                                            "tbl_pollopt._pollid=? group by tbl_pollopt.id order by c desc", array($_GET['pollid']));
						while ($trow = $trows->fetch())
						{
							$yrows = Select ( "_id", $GLOBALS['tbl_polls'], "_pollid=? AND _poll=?", array($_GET['pollid'],$trow['id']));
							$ccc = 0;
							while ($yrow = $yrows->fetch())
							{
								$hrows = Select ( "id", $GLOBALS['tbl_users'], "id=? AND _verify > 1", array ($yrow[0]) );
								if ( $hrow = $hrows->fetch() )
								{
									$ccc ++;
								}
							}
							echo "<div class='post'>
							<div class='row'>
								<div class='col-md-12'>
								".$trow['_optiontext'].": ".$ccc." رای
<div class='progress'>
<div class='progress-bar progress-bar-info progress-bar-striped' role='progressbar' aria-valuenow='".(int)(($ccc * 100) / $count)."' aria-valuemin='0' aria-valuemax='100' style='width: ".(int)(($ccc * 100) / $count)."%'>
 ".$ccc." رای
</div>
</div>
								</div>
							</div>
							</div>";
						}
					?>
				</div>
			</div>
        </div>
		<script>
			<?php
				if ( $prow ['_max'] > 0 )
				{
			?>
			$("form").submit(function(){
				var n = $( "input:checked" ).length;
				if ( n > <?php echo $prow ['_max']; ?> )
				{
					alert ("تعداد رای ها نباید بیشتر از <?php echo $prow ['_max']; ?> عدد باشد!");
					return false;
				}
				return true;
			});
            setInterval(function(){
				var n = $( "input:checked" ).length;
                $(".no_of_votes").text(n + " رای");
            },500);
			<?php
				}
			?>
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