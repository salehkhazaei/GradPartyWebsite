<?php
	require_once('config.php');
	if ( ! Access() )
	{
		redirect("index.php");
		CloseDatabase();
		die();
	}
	
	if ( isset ( $_GET['l'] ) )
	{
		$rows = Select ( "_verifylink", $GLOBALS['tbl_users'], "_verifylink=?", array ( $_GET['l'] ) );
		if ( $row = $rows->fetch() )
		{
			Update ( $GLOBALS['tbl_users'] , "_verify=2", "_verifylink=?", array ( $_GET['l'] ) );
			echo "Successful. Your account is activated.";
		}
		else
		{
			echo "Invalid activate link!";
		}
	}
	CloseDatabase();
?>