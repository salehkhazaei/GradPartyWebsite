<?php
	require_once('config.php');
	session_destroy();
	setcookie("user", "", time() - 3600, "/");
	setcookie("pwd", "", time() - 3600, "/");
	redirect("index.php");
	CloseDatabase();
?>
