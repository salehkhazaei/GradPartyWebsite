<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();

    $userid = $_SESSION['user'];

    $majalemanager = -99;
    $crows = Select ( "_majaleManager", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
    if ( $crow = $crows->fetch() )
    {
        $majalemanager = $crow[0];
    }

    $crows = Select ( "_majaleClose", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
    if ( $crow = $crows->fetch() )
    {
        if ( $crow[0] == 1 && !(isAdmin() || $_SESSION['user'] == $majalemanager ) )
        {
            redirect("closed.php");
            CloseDatabase();
            die();
        }
    }
    
    if ( (isAdmin() || $_SESSION['user'] == $majalemanager ) && isset ( $_GET['id'] ) )
    {
        $userid = $_GET['id'];
    }
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=? and jashnid=?", array ($userid,getJashnID()) );
	if ( $row = $rows->fetch() )
	{
        
        if ( isset ( $_GET['request'] ) )
        {
            $rows = Select ( "id", $GLOBALS['tbl_request'], "_userid=? and _done < 2" , array($userid));
            if ( $row = $rows->fetch() )
            {
                echo "<script>alert('شما قبلا درخواست داده اید');<script>";
            }
            else{
                Create($GLOBALS['tbl_request'],"(_userid)","(?)",array($userid));
                redirect("majale_preview.php");
                CloseDatabase();
                die();
            }
        }
        $rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($userid) );
        
		require('header.php');
?>
        <style>
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
				margin-top: 5px;
				margin-bottom: 5px;
				padding-top: 5px;
				padding-bottom: 5px;
				border-radius: 5px;
				color: white;
			}
			.group-white { background: #fff; color: #333; }
			.group-white:hover { background: #ccc; }
        </style>
		<div class="container theme-showcase" role="main">
            <?php 
                $wrows = Select ( "id", $GLOBALS['tbl_request'], "_userid=? and _done < 2" , array($userid));
                if ( $wrow = $wrows->fetch() )
                {
                    $wrows = Select ( "COUNT(id)", $GLOBALS['tbl_request'], "id < ? and _done < 2" , array($wrow[0]));
                    if ( $wrow = $wrows->fetch() )
                    {
                        echo "<div class='row'><div class='col-md-offset-3 col-md-6 group-lbl group-white text-center'>درخواست شما ثبت شده است، تعداد درخواست های در صف: ".$wrow[0]."، تخمین زمان اماده شدن صفحه: ".(((int)$wrow[0] + 1) * 30)." ثانیه.</div></div>";
                    }
                    else{
                        echo "<div class='row'><div class='col-md-offset-4 col-md-4 text-center'><a href='majale_preview.php?request' class='a-btn group-lbl group-red text-center'>درخواست ساخت پیش نمایش جدید</a></div></div>";
                    }
                }
                else{
                    echo "<div class='row'><div class='col-md-offset-4 col-md-4 text-center'><a href='majale_preview.php?request' class='a-btn group-lbl group-red text-center'>درخواست ساخت پیش نمایش جدید</a></div></div>";
                }
            ?>
            <div class='row group-lbl group-red'><div class='col-md-12'>صفحه شما در مجله با احتمال 90% سمت <?php 
                echo ($row['_left'] == 1 ? "چپ" : "راست");
            ?> خواهد بود</div></div>
            <div class='row'><div class='col-md-6'><img class='img-thumbnail' src='<?php 
                echo "magazine/".$row['id']."_".abs(((int)$row['id'] * 11) % 9999)."-left.jpg";
            ?>' /></div>
            <div class='col-md-6'><img class='img-thumbnail' src='<?php 
                echo "magazine/".$row['id']."_".abs(((int)$row['id'] * 11) % 9999)."-right.jpg";
            ?>' /></div></div>
        </div>
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