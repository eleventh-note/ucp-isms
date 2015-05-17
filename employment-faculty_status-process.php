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
	require_once(CLASSLIST . "auditor.inc.php");
	require_once(CLASSLIST . "audit.inc.php");
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
	//modification - 20130706 - Audit Trail
	$auditor = new AuditTrail($conn);
	$audit = new Audit();
	$audit->userId = $UserInfo->id;
	$audit->tableName = "sch-faculty_status";
	$audit->newValue = "Description:{0}; ";
	
	if(isset($_POST['status_save'])){
		$action = $_POST['status_save'];
		
		switch($action){
			case 'Add':
				$hnd = new FacultyManager($conn);
				$status = $_POST['status'];
				
				if($hnd->AddFacultyStatus($status) == true){
				
					//modification - 20130706 - Audit Trail
					$audit->action = "Add Faculty Status";
					$audit->newValue = str_replace("{0}", $status, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "Faculty Status successfully added!";
					header("Location: employment-faculty_status.php");
					exit();
				} else {
					$_SESSION['status'] = $status;
					$_SESSION['error'] = $hnd->error;
					header("Location: employment-faculty_status-add.php");
					exit();
				}
				break;
			case 'Save':
				$hnd = new FacultyManager($conn);
				$id = $_POST['id'];
				$status = $_POST['status'];
				
				if($hnd->EditFacultyStatus($id, $status) == true){
				
					//modification - 20130706 - Audit Trail
					$audit->action = "Update Faculty Rank";
					$audit->newValue = "RankID:{0}; Description:{1}; ";
					$audit->newValue = str_replace("{0}", $id, $audit->newValue);
					$audit->newValue = str_replace("{1}", $status, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "Faculty Status successfully saved!";
					header("Location: employment-faculty_status.php");
					exit();
				} else {
					$_SESSION['status'] = $status;
					$_SESSION['error'] = $hnd->error;
					header("Location: employment-faculty_status-edit.php?id=$id");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new FacultyManager($conn);
		$id = (int) $_GET['id'];
		if($hnd->DeleteFacultystatus($id) == true){
			//modification - 20130706 - Audit Trail
			$audit->action = "Add Faculty Rank";
			$audit->newValue = "RankID:{0}";
			$audit->newValue = str_replace("{0}", $id, $audit->newValue);
			$auditor->Add($audit);
			
			$_SESSION['success'] = "Faculty status successfully deleted!";
			header("Location: employment-faculty_status.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: employment-faculty_status.php");
			exit();
		}
	} else {
		header("Location: employment-faculty_status.php");
		exit();
	}
		
	$conn->Close();
	
?>