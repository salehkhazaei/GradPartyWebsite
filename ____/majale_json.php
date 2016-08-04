<?php
	require_once('config.php');
    $arr = array();
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=? and _verify=2", array ($_GET['user']) );
	if ( $row = $rows->fetch() ) 
    {
        $arr['pic'] = trim($row['_pic'].' ');
        $arr['name'] = $row['_name'].' '.$row['_family'];
        $arr['id'] = $row['id'].'';
        $arr['birth'] = $row['_date_year'].'/'.$row['_date_month'].'/'.$row['_date_day'];
        $arr['city'] = trim($row['_city'].' ');
        $arr['majorid'] = $row['_major'];
        $arr['major'] = ( $row['_major'] == 1 ? "نرم افزار" :( $row['_major'] == 2 ? "سخت افزار" : "فناوری اطلاعات" ) );
        $arr['email'] = trim($row['_email'].' ');
        $arr['qoute'] = trim($row['_qoute'].' ');
        $arr['picopt'] = $row['_picstate'];

        $tarins = array();
        {
            $mainrows = Select ( "*" , $GLOBALS['tbl_bestlist'], "_best != '---'", array() );
            $current = 0 ;
            $prior1 = "";
            $best = "";
            $i = 0;
            while ( $mainrow = $mainrows->fetch() )
            {
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
                        $tarins [ $best ] = $prior + 1;
                    }
                    $prior ++ ;
                }
                $i ++;
            }
        }
        if ( count ( $tarins ) > 0 )
            $arr['tarin'] = $tarins;
        
        $personalities = array();
        {
            $mainrows = Select ( "*" , $GLOBALS['tbl_bestlist'], "_best != '---'", array() );
            $current = 0 ;
            $prior1 = "";
            $best = "";
            while ( $mainrow = $mainrows->fetch() )
            {
                $trows = Select ( "COUNT(_pollid) as c" , 
                                    $GLOBALS['tbl_bestpoll'], 
                                    "_bestid=? and _pollid=?", 
                                    array($mainrow['id'],$_GET['user']) );
                $best = $mainrow['_best'];
                $prior = 0 ;
                $prior1 = "";
                if ( $trow = $trows->fetch() )
                {
                    $prior1 = $trow[0];
                    $brows = Select ( "_userid" , $GLOBALS['tbl_bestpoll'], "_bestid=? AND _userid=? AND _prior=-1", array($mainrow['id'],$_GET['user']));
                    if ( $brow = $brows->fetch() )
                    {
//                        continue;
                    }
                    $personalities [ $best ] = $prior1;
                }
            }
        }
        if ( count ( $personalities ) > 0 )
            $arr['personalities'] = $personalities;

        $shortAs = array();
        {
            $i = 0;
            $brows = Select ("*", $GLOBALS['tbl_shortQ'], "1", array());
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
                $shortAs [ $brow['_question'] ] = array("text" => $a, "prior" => $i);
                $i ++ ;
            }

        }
        if ( count ( $shortAs ) > 0 )
            $arr['shortAs'] = $shortAs;

        $arr['longA'] = $row['_longAns'];
        $arr['pagePic'] = $row['_pagePic'];
        $khatere = array();
        {
            $mainrows = Select ( "*" , $GLOBALS['tbl_khatere'], "_targetid=? and _show=1", array($row['id']) );
            while ( $mainrow = $mainrows->fetch() )
            {
                $crows = Select ( "*", $GLOBALS['generation'], "id=?", array($mainrow['_userid']));
                if ( $crow = $crows->fetch () )
                {
                    $khatere [ $crow['name'].' '.$crow['family'] ] = array("text" => $mainrow['_text'], "prior" => $mainrow['_prior']);
                }
            }
        }
        if ( count ( $khatere ) > 0 )
            $arr['khatere'] = $khatere;
        echo json_myencode($arr);
    }
	CloseDatabase();
?>