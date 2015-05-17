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
	require_once(CLASSLIST . "sbjcts.inc.php");
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
		$PagePrivileges->AddPrivilege("School - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	if(isset($_POST['group_save'])){
		$action = $_POST['group_save'];
		
		switch($action){
			case 'Add':
				$hnd = new SubjectManager($conn);
				$description = $_POST['description'];
				
				
				if($hnd->AddSubjectGroup($description) == true){;
					$_SESSION['success'] = "Subject Group successfully added!";
					header("Location: schoolplusplus-subject-groups.php");
					exit();
				} else {
					$_SESSION['error'] = $hnd->error;
					
				    header("Location: schoolplusplus-subject-groups-add.php");
					exit();
				}
				break;
			case 'Save':
				$hnd = new SubjectManager($conn);
				$id = $_POST['id'];
				$description = $_POST['description'];
				
				if($hnd->EditSubjectGroup($id, $description) == true){;
					$_SESSION['success'] = "Subject Group successfully saved!";
					header("Location: schoolplusplus-subject-groups.php");
					exit();
				} else {
					$_SESSION['description'] = $description;
					$_SESSION['error'] = $hnd->error;
					header("Location: schoolplusplus-subject-groups-edit.php?id=$id");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new EmployeeManager($conn);
		$id = (int) $_GET['id'];
		if($hnd->DeletePosition($id) == true){
			$_SESSION['success'] = "Position successfully deleted!";
			header("Location: employment-position.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: employment-position.php");
			exit();
		}
	} else {
		header("Location: employment-position.php");
		exit();
	}
		
	$conn->Close();
	
?>