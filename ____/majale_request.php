<?php
	require_once('config.php');
    $arr = array();
	$rows = Select ( "*", $GLOBALS['tbl_request'], "_done=0 order by id asc", array () );
	while ( $row = $rows->fetch() ) 
    {
        $arr[$row['id']] = $row['_userid'];
    }
    echo json_myencode($arr);
	CloseDatabase();
?>