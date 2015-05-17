<?php
	/* #-------------------------------------------------
	   #
	   #	Description:	Template for 00 Default Layout
	   #	Author:		Algefmarc A. L. Almocera
	   #	Date Started:	December 02, 2011
	   #	Last Modified:	December 02, 2011
	   #
	   #-------------------------------------------------
	*/

	//Set no caching
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	header("Cache-Control: no-store, no-cache, must-revalidate"); 
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

//::START OF 'SESSION DECLARATION'
	//open session here if needed (e.g: session_start())
	session_start();
//::END OF 'SESSION DECLARATION'

//::START OF 'CONFIGURATION'
	require_once("_system/_config/sys_config.php");
	//configurations can be overriden here	
	unset($_SESSION['UserInfo']);
	$_SESSION = array();
	
	header("Location:index.php");
	exit();
	
//::END OF 'CONFIGURATION'
?>