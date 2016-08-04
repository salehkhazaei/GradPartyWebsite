<?php
	require_once('config.php');
    if ( ! isset ( $_GET['hey'] ))
    {
        CloseDatabase();
        die();
    }
    $mainrows = Select ( "*" , $GLOBALS['tbl_bestlist'], "_best != '---'", array() );
    $prior1 = "";
    $best = "";
    $count = 0;
    $i = 0;
    $arr = array();
    while ( $mainrow = $mainrows->fetch() )
    {
        $ccrows = Select ( "_userid" , $GLOBALS['tbl_bestpoll'], "_bestid=? group by _userid", array($mainrow['id']));
        $count = $ccrows->rowCount();
        // SELECT _pollid,COUNT(_pollid) as c, SUM(_prior) as p FROM `tbl_bestpoll` WHERE _bestid=94 group by _pollid order by c desc, p desc limit 0,3
        $trows = Select ( "_pollid,COUNT(_pollid) as c, SUM(_prior) as p" , 
                            $GLOBALS['tbl_bestpoll'], 
                            "_bestid=? and _pollid != -1 group by _pollid order by c desc, p asc, _pollid asc limit 0,5", 
                            array($mainrow['id']) );
        $hasVote=false;
        $best = $mainrow['_best'];
        $prior = 0 ;
        $prior1 = "";
        $arr[$best] = array('count' => $count, 'rank' => array(), 'emoji_id' => $mainrow['emoji_id']);
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

            
            $grows = Select ( "*", $GLOBALS['generation'], "id=?", array( $prior1 ) );
            if ( $grow = $grows->fetch() )
            {
                if ( $showName )
                {
                    if ( $prior1 == 9131009 )
                    {
$prior1 = "شیرین
شیرازی";
                    }
                    else if ( $prior1 == 9131073 )
                    {
$prior1 = "بهنام
سلمانی";
                    }
                    else
                    {
                        $orows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array( $prior1 ) );
                        if ( $orow = $orows->fetch() )
                        {
                            $prior1 = $orow['_name'].'
'.$orow['_family'];
                        }
                        else{
                            $prior1 = ( $showName ? $grow['name'].'
'.$grow['family'] : "????" );
                        }
                    }
                }
                else
                {
                    $prior1 = ( $showName ? $grow['name'].'
'.$grow['family'] : "????" );
                }
            }
            $arr[$best]['rank'][$prior] = array($prior1,$trow['c']);
            $prior ++ ;
        }
    }
    /*
        SELECT _pollid, COUNT(id) as c FROM `tbl_bestpoll` WHERE 1 GROUP BY _pollid ORDER BY c DESC // por ray tarin
        SELECT _userid, COUNT(id) as c FROM `tbl_bestpoll` WHERE 1 GROUP BY _userid ORDER BY c DESC // ray dahande tarin
        SELECT _userid, COUNT(id) as c FROM `tbl_khatere` WHERE 1 GROUP BY _userid ORDER BY c desc // khatere nevis tarin
        SELECT _targetid, COUNT(id) as c FROM `tbl_khatere` WHERE 1 GROUP BY _targetid ORDER BY c desc // khatere dar tarin
        SELECT _userid,COUNT(id) as c FROM `tbl_request` WHERE 1 GROUP BY _userid order by c desc // request dahande tarin
    */
    $brows = Select ( "_pollid, COUNT(id) as c" , $GLOBALS['tbl_bestpoll'], "1 GROUP BY _pollid ORDER BY c DESC", array());
    $arr['پر رای ترین'] = array('count' => 1, 'rank' => array(), 'emoji_id' => 64);
    $i = 0;
    while ( $brow = $brows->fetch() )
    {
        $orows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array( $brow['_pollid'] ) );
        if ( $orow = $orows->fetch() )
        {
if ( $orow['id'] == 9131009 )
{
$prior1 = "شیرین
شیرازی";
}
else if ( $orow['id'] == 9131073 )
{
$prior1 = "بهنام
سلمانی";
}
else{
            $prior1 = $orow['_name'].'
'.$orow['_family'];
}
        }
        $arr['پر رای ترین']['rank'][$i] = array($prior1,$brow['c']);
        $i ++;
    }

    $brows = Select ( "_userid, COUNT(id) as c" , $GLOBALS['tbl_bestpoll'], "1 GROUP BY _userid ORDER BY c DESC", array());
    $arr['رای دهنده ترین'] = array('count' => 1, 'rank' => array(), 'emoji_id' => 64);
    $i = 0;
    while ( $brow = $brows->fetch() )
    {
        $orows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array( $brow['_userid'] ) );
        if ( $orow = $orows->fetch() )
        {
if ( $orow['id'] == 9131009 )
{
$prior1 = "شیرین
شیرازی";
}
else if ( $orow['id'] == 9131073 )
{
$prior1 = "بهنام
سلمانی";
}
else{
            $prior1 = $orow['_name'].'
'.$orow['_family'];
}
        }
        $arr['رای دهنده ترین']['rank'][$i] = array($prior1,$brow['c']);
        $i ++;
    }

    $brows = Select ( "_userid, COUNT(id) as c" , $GLOBALS['tbl_khatere'], "1 GROUP BY _userid ORDER BY c DESC", array());
    $arr['خاطره نویس ترین'] = array('count' => 1, 'rank' => array(), 'emoji_id' => 64);
    $i = 0;
    while ( $brow = $brows->fetch() )
    {
        $orows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array( $brow['_userid'] ) );
        if ( $orow = $orows->fetch() )
        {
if ( $orow['id'] == 9131009 )
{
$prior1 = "شیرین
شیرازی";
}
else if ( $orow['id'] == 9131073 )
{
$prior1 = "بهنام
سلمانی";
}
else{
            $prior1 = $orow['_name'].'
'.$orow['_family'];
}
        }
        $arr['خاطره نویس ترین']['rank'][$i] = array($prior1,$brow['c']);
        $i ++;
    }

    $brows = Select ( "_targetid, COUNT(id) as c" , $GLOBALS['tbl_khatere'], "1 GROUP BY _targetid ORDER BY c DESC", array());
    $arr['خاطره دار ترین'] = array('count' => 1, 'rank' => array(), 'emoji_id' => 64);
    $i = 0;
    while ( $brow = $brows->fetch() )
    {
        $orows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array( $brow['_targetid'] ) );
        if ( $orow = $orows->fetch() )
        {
if ( $orow['id'] == 9131009 )
{
$prior1 = "شیرین
شیرازی";
}
else if ( $orow['id'] == 9131073 )
{
$prior1 = "بهنام
سلمانی";
}
else{
            $prior1 = $orow['_name'].'
'.$orow['_family'];
}
        }
        $arr['خاطره دار ترین']['rank'][$i] = array($prior1,$brow['c']);
        $i ++;
    }

    $brows = Select ( "_userid, COUNT(id) as c" , $GLOBALS['tbl_request'], "1 GROUP BY _userid ORDER BY c DESC", array());
    $arr['ریکوئست دهنده ترین'] = array('count' => 1, 'rank' => array(), 'emoji_id' => 64);
    $i = 0;
    while ( $brow = $brows->fetch() )
    {
        $orows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array( $brow['_userid'] ) );
        if ( $orow = $orows->fetch() )
        {
if ( $orow['id'] == 9131009 )
{
$prior1 = "شیرین
شیرازی";
}
else if ( $orow['id'] == 9131073 )
{
$prior1 = "بهنام
سلمانی";
}
else{
            $prior1 = $orow['_name'].'
'.$orow['_family'];
}
        }
        $arr['ریکوئست دهنده ترین']['rank'][$i] = array($prior1,$brow['c']);
        $i ++;
    }

    //echo "<html><head><meta charset='utf-8'></head><body>";
    echo json_myencode($arr);
	CloseDatabase();
?>