<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	verify();
	
    $rows = Select ( "*", $GLOBALS['tbl_users'], "id=? and jashnid=?", array ( ( isset ( $_GET['id'] ) ? $_GET['id'] : $_SESSION['user']),getJashnID() ) );
	if ( $row = $rows->fetch() )
	{
		require('header.php');

        $majalemanager = -99;
        $crows = Select ( "_majaleManager", $GLOBALS['tbl_jashn'], "id=?", array(getJashnID()));
        if ( $crow = $crows->fetch() )
        {
            $majalemanager = $crow[0];
        }
        if ( isAdmin() || $_SESSION['user'] == $majalemanager )
        {
?>
        <style>
    		.group-lbl {
				margin: 5px;
				padding: 5px;
				border-radius: 5px;
				color: white;
			}
			.group-orange { background: rgb(221,171,0); }
			.group-orange:hover { background: rgb(255,204,28); }
			.group-white { background: #ccc; }
			.group-white:hover { background: #aaa; }
        </style>
		<div class="container theme-showcase" role="main">
            <div class='row'>
                <form method=get>
                <div class='col-md-2 col-md-offset-6'>
                    <input type='submit' class='form-control btn btn-danger' value='مشاهده صفحه' />
                </div>
                <div class='col-md-2'>
                    <input type='text' name='id' class='form-control' value='<?php echo (isset ( $_GET['id'] ) ? $_GET['id'] : $_SESSION['user']); ?>' /><br>
                </div>
                <div class='col-md-2'>
                    شماره دانشجویی:<br>
                </div>
                </form>
            </div>
            <div class='row'>
                <div class='col-md-3'>
                    <div class='imglabel' style="background-image: url('<?php 
						echo $row['_pic'];
					?>');" ></div>
                </div>
                <div class='col-md-9'>
                    <div class='row'>
                        <div class='col-md-12'>
                            <h3 style='text-align: left'><?php echo $row['_name'].' '.$row['_family']; ?></h3>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-3'>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <h6 style='text-align: left'>شماره دانشجویی: <?php echo $row['id']; ?></h6>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <h6 style='text-align: left'>تاریخ تولد: <?php echo $row['_date_year'].'/'.$row['_date_month'].'/'.$row['_date_day']; ?></h6>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <h6 style='text-align: left'>اهل: <?php echo $row['_city']; ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-6'>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <h6 style='text-align: left'>گرایش: <?php echo ( $row['_major'] == 1 ? "نرم افزار" :( $row['_major'] == 2 ? "سخت افزار" : "فناوری اطلاعات" ) ) ; ?></h6>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <h6 style='text-align: left'>ایمیل: <?php echo $row['_email'] ; ?></h6>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <h6 style='text-align: left'>نقل قول مورد علاقه: <?php echo $row['_qoute'] ; ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                    
                    <?php 
                    $colors = array ( "yellow",
                                      "white",
                                      "orange",
                                      "sky",
                                      "black");
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
                        while ( $trow = $trows->fetch() )
                        {
                            $hasVote=true;
                            $prior1 = $trow['_pollid'];
                            $brows = Select ( "_userid" , $GLOBALS['tbl_bestpoll'], "_bestid=? AND _userid=? AND _prior=-1", array($mainrow['id'],$prior1));
                            if ( $brow = $brows->fetch() )
                            {
                                continue;
                            }
                            
                            if ( $row['id'] == $prior1 )
                            {
                                echo "<div class='col-md-2'><div class='group-lbl group-".$colors[$prior]."'>".($prior + 1)."-".$best."</div></div>";
                            }
                            $prior ++ ;
                        }
                        $i ++;
                    }
                ?>
            </div>
            <div class='row'>
                <div class='col-md-5'>
					<?php
						$brows = Select ("*", $GLOBALS['tbl_shortQ'], "jashnid=?", array(getJashnID()));
						while ( $brow = $brows->fetch() )
						{
							$nrows = Select ( "*", $GLOBALS['tbl_shortA'], "_userid=? && _qid=?",
																		array ( $row['id'], $brow['id'] ) );
							$a = "";
							if ( $nrow = $nrows->fetch() )
							{
								$a = $nrow['_ans'];
							}
                            if ( strlen(trim($a)) == 0 )
                                continue;
							echo "<div class='row'><div class='col-md-12'>".$brow['_question'].": ".$a."</div></div>";
						}
					?>
                </div>
                <div class='col-md-7'>
                    <div class='row'>
                        <div class='col-md-12'>
                            <p><h5><b><?php echo $row['_name'].' '.$row['_family']; ?>:</b><br></h5>
                            <?php echo $row['_longAns']; ?>
                            </p>
                        </div>
                    </div>
                    <?php 
                        $mainrows = Select ( "*" , $GLOBALS['tbl_khatere'], "_targetid=? and _show=1", array($row['id']) );
                        while ( $mainrow = $mainrows->fetch() )
                        {
                            $crows = Select ( "*", $GLOBALS['generation'], "id=?", array($mainrow['_userid']));
                            if ( $crow = $crows->fetch () )
                            {
                                echo "<div class='row'>
                                    <div class='col-md-12'>
                                        <p><h5><b>".$crow['name'].' '.$crow['family'].":</b><br></h5>
                                        ".$mainrow['_text']."
                                        </p>
                                    </div>
                                </div>";
                            }
                        }
                    ?>
                    <div class='row'>
                        <div class='col-md-12'>
                            <img class='img-thumbnail' src='<?php 
                                echo $row['_pagePic'];
                            ?>' />
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-12'>
                            <h5><b><?php echo ($row['_sex'] == 1 ? "آقای " : "خانم " ).$row['_family']." وقتی بچه بوده این شکلی بوده:"; ?></b></h5>
                        </div>
                    </div>
                    <div class='row'>
                    <?php
                        $brows = Select ( "*", $GLOBALS['tbl_baby'], "_userid=?", array( $row['id'] ) );
                        while ( $brow = $brows->fetch () )
                        {
                            echo "<div class='col-md-4'>
                            <img class='img-thumbnail' src='".$brow['_pic']."' />
                        </div>";
                        }
                    ?>
                    </div>
                    
                </div>
            </div>
        </div>
	<?php require('footer.php'); ?>
</html>
<?php
        }
        else{
            echo "You dont have the permission to view this page! <a href='home.php'>Home</a>";
            CloseDatabase();
            die();
        }
	}
	else{
		echo "Invalid user! <a href='majale.php'>Back</a>";
		CloseDatabase();
		die();
	}
	CloseDatabase();
?>