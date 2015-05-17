<?php
	/* #-------------------------------------------------
	   #
	   #	Description:	Template for 00 Default Layout
	   #	Autdor:		Algefmarc A. L. Almocera
	   #	Date Started:	May 30, 2011
	   #	Last Modified:	June 8, 2011
	   #
	   #-------------------------------------------------
	*/
//::START OF 'SESSION DECLARATION'
	//open session here if needed (e.g: session_start())
	session_start();
//::END OF 'SESSION DECLARATION'

	//Set no caching
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	header("Cache-Control: no-store, no-cache, must-revalidate"); 
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

//::START OF 'CONFIGURATION'
	require_once("_system/_config/sys_config.php");
	//configurations can be overriden here
	include_once(CLASSLIST . "dataconnection.inc.php");
	require_once(CLASSLIST . "user.inc.php");
	require_once(CLASSLIST . "sentry.inc.php");
	require_once(CLASSLIST . "menu.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
//::END OF 'CONFIGURATION'
		
	//# General Variables - shown in all documents for easy modification
		$title = SCHOOL_NAME . " Integrated School Management System";
		$keywords = "";
		$description = "";
		$autdor = "";
		$robots="noindex,nofollow";
	
	//Sentry/Security Measures must be done here
	if(isset($_SESSION['UserInfo'])){
		//autdenticate user privileges
		$UserInfo = unserialize($_SESSION['UserInfo']);
		$Sentry = new Sentry($UserInfo);

		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	if(isset($_POST['change_password'])){
		$id = $_POST['id'];
		$npassword = $_POST['new_password'];
		$cpassword = $_POST['c_password'];
		$opassword = $_POST['o_password'];

		$user = new User($conn);
		
		if($user->ChangePassword($id, $npassword, $cpassword, $opassword) == true){
			$_SESSION['success'] = 'Password successfully changed!';
			header("Location: account-settings.php");
			exit();
		} else {
			$_SESSION['error'] = $user->error;
			header("Location: account-settings.php");
			exit();
		};
		
	} elseif(isset($_GET['action'])) {
		$hnd = new CollegeManager($conn);
		$id = (int) $_GET['id'];
		
		if($hnd->DeleteCollege($id) == true){
			$_SESSION['success'] = "College successfully deleted!";
			header("Location: colleges.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: colleges-view.php?id={$id}");
			exit();
		}
	} else {
		header("Location: colleges.php");
		exit();
	}
		
	$conn->Close();
	
?>