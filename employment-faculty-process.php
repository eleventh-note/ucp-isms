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
		
		$PagePrivileges = new PagePrivileges();
		$PagePrivileges->AddPrivilege("SUPERADMIN");
		$PagePrivileges->AddPrivilege("Employment - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	if(isset($_POST['faculty_save'])){
		$action = $_POST['faculty_save'];
		
		switch($action){
			case 'Assign':
				$hnd = new FacultyManager($conn);
				$faculty = $_POST['faculty'];
				$status = $_POST['status'];
				$rank = $_POST['rank'];
				$college = $_POST['college'];
				
				if($hnd->AssignFaculty($rank, $status, $college, $faculty) == true){;
					$_SESSION['success'] = "Faculty successfully assigned!";
					header("Location: employment-faculty-add.php");
					exit();
				} else {
				
					$_SESSION['employee'] = $_POST['faculty'];
					$_SESSION['status'] = $_POST['status'];
					$_SESSION['rank'] = $_POST['rank'];
					$_SESSION['college'] = $_POST['college'];
					
					$_SESSION['error'] = $hnd->error;
					header("Location: employment-faculty-add.php");
					
					exit();
				}
				break;
			case 'Save':
				$hnd = new FacultyManager($conn);
				$id = $_POST['id'];
				$faculty = $_POST['faculty'];
				$status = $_POST['status'];
				$rank = $_POST['rank'];
				$college = $_POST['college'];
				
				if($hnd->EditFaculty($id, $rank, $status, $college, $faculty) == true){;
					$_SESSION['success'] = "Faculty details successfully saved!";
					header("Location: employment-faculty.php");
					exit();
				} else {
				
					//$_SESSION['employee'] = $_POST['faculty'];
					$_SESSION['employee'] = $_POST['faculty'];
					$_SESSION['status'] = $_POST['status'];
					$_SESSION['rank'] = $_POST['rank'];
					$_SESSION['college'] = $_POST['college'];
					
					$_SESSION['error'] = $hnd->error;
					
					header("Location: employment-faculty-edit.php?id=$id");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new FacultyManager($conn);
		$id = (int) $_GET['id'];
		$employee = (int) $_GET['id2'];
		
		if($hnd->UnassignFaculty($id, $employee) == true){
			$_SESSION['success'] = "Faculty successfully unassigned!";
			header("Location: employment-faculty.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: employment-faculty.php");
			exit();
		}
	} else {
		header("Location: employment-faculty.php");
		exit();
	}
		
	$conn->Close();
	
?>