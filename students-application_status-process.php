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
	require_once(CLASSLIST . "stdnts.inc.php");
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
		
		$PagePrivileges = new PagePrivileges();
		$PagePrivileges->AddPrivilege("SUPERADMIN");
		$PagePrivileges->AddPrivilege("Application - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	if(isset($_POST['status_save'])){
		$action = $_POST['status_save'];
		
		switch($action){
			case 'Add Status':
				$hnd = new StudentManager($conn);
				$description = $_POST['description'];
				
				if($hnd->AddApplicationStatus($description) == true){;
					$_SESSION['success'] = "Application Status successfully added!";
					header("Location: students-application_status.php");
					exit();
				} else {
									
					$_SESSION['error'] = $hnd->error;
					header("Location: students-application_status-add.php");
					
					exit();
				}
				break;
			case 'Save Status':
				$hnd = new StudentManager($conn);
				$id = (int) $_POST['id'];
				$description = $_POST['description'];

				
				if($hnd->UpdateApplicationStatus($id, $description) == true){;
					$_SESSION['success'] = "Application Status successfully saved!";
					header("Location: students-application_status.php");
					exit();
				} else {
								
					$_SESSION['error'] = $hnd->error;
					
					header("Location: students-application_status-edit.php?id=$id");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new StudentManager($conn);
		$id = (int) $_GET['id'];
		
		if($hnd->DeleteApplicationStatus($id) == true){
			$_SESSION['success'] = "Application Status successfully deleted!";
			header("Location: students-application_status.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: students-application_status.php");
			exit();
		}
	} else {
		header("Location: students-application_status.php");
		exit();
	}
		
	$conn->Close();
	
?>