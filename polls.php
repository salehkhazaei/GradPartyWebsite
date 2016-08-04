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
	
	
	$erows = Select ( "*", $GLOBALS['tbl_groups'], "_admin=?", array ( $_SESSION['user'] ));
	$erow = $erows->fetch();
		
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ( $row = $rows->fetch() )
	{
		if ( isset ( $_POST['title'] ) )
		{
			Create ( $GLOBALS ['tbl_pollconf'] , "(_group,jashnid,_text,_title,_type,_max)", "(?,?,?,?,?,?)", array ( $erow['id'], getJashnID(), $_POST['text'], $_POST['title'], $_POST['type'], $_POST['maxvote']) );
			$id = lastInsertedID();
			for ($i=0;$i<100;$i++)
			{
				if(isset($_POST['opt'.$i]))
				{
					Create ( $GLOBALS ['tbl_pollopt'] , "(_pollid,_optiontext)","(?,?)", array ( $id, $_POST['opt'.$i] ) );
				}
			}
			redirect('polls.php');
			CloseDatabase();
			die();
		}
		if ( isset ( $_GET['delete'] ) )
		{
			$orows = Select ( "*", $GLOBALS['tbl_pollconf'], "id=?", array( $_GET['delete'] ) );
			if ( $orow = $orows->fetch() )
			{
				$erows = Select ( "*", $GLOBALS['tbl_groups'], "id=?", array ( $orow['_group'] ));
				$mine = 0;
				if ( $erow = $erows->fetch() )
				{
					if ( $erow['_admin'] == $_SESSION['user'] )
					{
						$mine = 1;
					}
				}
                if ( $mine == 1 )
                {
                    Delete ( $GLOBALS['tbl_pollconf'], "id=?", array( $_GET['delete'] ) );
                }
				redirect ('polls.php');
				CloseDatabase();
				die();
			}
		}
		if ( isset ( $_GET['close'] ) )
		{
			$orows = Select ( "*", $GLOBALS['tbl_pollconf'], "id=?", array( $_GET['close'] ) );
			if ( $orow = $orows->fetch() )
			{
				$erows = Select ( "*", $GLOBALS['tbl_groups'], "id=?", array ( $orow['_group'] ));
				$mine = 0;
				if ( $erow = $erows->fetch() )
				{
					if ( $erow['_admin'] == $_SESSION['user'] )
					{
						$mine = 1;
					}
				}
                if ( $mine == 1 )
                {
                    Update ( $GLOBALS['tbl_pollconf'], "_done=1", "id=?", array( $_GET['close'] ) );
                }
				redirect ('polls.php');
				CloseDatabase();
				die();
			}
		}
		if ( isset ( $_GET['open'] ) )
		{
			$orows = Select ( "*", $GLOBALS['tbl_pollconf'], "id=?", array( $_GET['open'] ) );
			if ( $orow = $orows->fetch() )
			{
				$erows = Select ( "*", $GLOBALS['tbl_groups'], "id=?", array ( $orow['_group'] ));
				$mine = 0;
				if ( $erow = $erows->fetch() )
				{
					if ( $erow['_admin'] == $_SESSION['user'] )
					{
						$mine = 1;
					}
				}
                if ( $mine == 1 )
                {
                    Update ( $GLOBALS['tbl_pollconf'], "_done=0", "id=?", array( $_GET['open'] ) );
                }
				redirect ('polls.php');
				CloseDatabase();
				die();
			}
		}
	require('header.php');
?>
		<div class="container theme-showcase" role="main">
			<?php 
				if ( $erow != null )
				{
			?>
			<div class='row'>
				<div class='col-md-4 col-md-offset-4'>
					<form method=post>
						گروه:<br>
						<input class='form-control' name='group' type='text' value='<?php 
							echo $erow['_name'];
						?>' disabled />
						<br>
						عنوان:<br>
						<input type='text' name='title' class='form-control'/>
						<br>
						متن رای گیری:<br>
						<textarea name='text' class='form-control'></textarea>
						<br>
						نوع رای گیری:<br>
						<select name='type' class='form-control'>
							<option value='0'>انتخاب تکی</option>
							<option value='1'>انتخاب چندتایی</option>
						</select>
						<br>
						تعداد رای های مجاز:<br>
						<input type='number' name='maxvote' class='form-control' value='9999' />
						<br>
						گزینه 1:<br>
						<input class='form-control' name='opt1' type='text' />
						<br>
						<input id=addopt type="button" class='form-control' value="اضافه کردن گزینه" />
						<br>
						<input type="submit" class='form-control btn-success submitbtn' value="ثبت" />
					</form>
				</div>
			</div>
			<?php 
				}
			?>
			<div class='row'>
				<?php 
					$orows = Select ( "*", $GLOBALS['tbl_pollconf'], "jashnid=?", array(getJashnID()) );
					while ( $orow = $orows->fetch() )
					{
						$erows = Select ( "*", $GLOBALS['tbl_groups'], "id=?", array ( $orow['_group'] ));
						$mine = 0;
						if ( $erow = $erows->fetch() )
						{
							if ( $erow['_admin'] == $_SESSION['user'] )
							{
								$mine = 1;
							}
						}
						if ( $mine == 1 )
						{
							echo "
								<div class='col-md-4 col-md-offset-4 poll'><a href='poll.php?pollid=".$orow['id']."'>
									".$orow['_title']."
								</a><br><br><a class='btn-danger a-btn' href='polls.php?delete=".$orow['id']."'>حذف</a>
                                <a class='btn-danger a-btn' href='polls.php?close=".$orow['id']."'>بستن</a>
                                <a class='btn-danger a-btn' href='polls.php?open=".$orow['id']."'>باز کردن</a></div>
							";
						}
						else
						{
							echo "
								<a href='poll.php?pollid=".$orow['id']."'><div class='col-md-4 col-md-offset-4 poll'>
									".$orow['_title']."
								</div></a>
							";
						}
					}
				?>
			</div>
        </div>
		<script>
			var index = 2;
			$(function(){
				$("#addopt").click(function(){
					$("<div>گزینه " + index + ":<br>" +
						"<input class='form-control' name='opt" + index + "' type='text' />" +
						"<br></div>").insertBefore($("#addopt"));
					index ++ ;
				});
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