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
	require_once(CLASSLIST . "dvsns.inc.php");
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
		$PagePrivileges->AddPrivilege("Colleges - Administrator");
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
	$audit->tableName = "adm-colleges";
	$audit->newValue = "College:{0}; Code: {1}; DivisionID: {2}; CollegeType: {3}";
	$divHnd = new DivisionsManager($conn);
	
	if(isset($_POST['college_save'])){
		$action = $_POST['college_save'];
		
		switch($action){
			case 'Add':
				$hnd = new CollegeManager($conn);
				$description = $_POST['description'];
				$code = $_POST['code'];
				$college_type = (int) $_POST['college_type'];
				$division = (int) $_POST['division'];
				
				if($hnd->AddCollege($division, $code, $description, $college_type) == true){
					//modification - 20130706 - Audit Trail
					$audit->action = "Add College";
					$audit->newValue = str_replace("{0}", $description, $audit->newValue);
					$audit->newValue = str_replace("{1}", $code, $audit->newValue);
					$detail = $divHnd->GetDivisions($division);
					$audit->newValue = str_replace("{2}", $detail[0]->description, $audit->newValue);
					$detail = $hnd->GetCollegeTypesByKey($college_type);
					$detail = array_values($detail);
					$audit->newValue = str_replace("{3}", $detail[0]->description, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "College successfully created!";
					header("Location: colleges.php");
					exit();
				} else {
				
					$_SESSION['description'] = $_POST['description'];
					$_SESSION['code'] = $_POST['code'];
					$_SESSION['college_type'] = $_POST['college_type'];
					$_SESSION['division'] = $_POST['division'];
					
					$_SESSION['error'] = $hnd->error;
					header("Location: colleges-add.php");
					
					exit();
				}
				break;
			case 'Save':
				$hnd = new CollegeManager($conn);
				$id = $_POST['id'];
				$description = $_POST['description'];
				$code = $_POST['code'];
				$college_type = (int) $_POST['college_type'];
				$division = (int) $_POST['division'];
				
				if($hnd->EditCollege($id, $division, $code, $description, $college_type) == true){
					//modification - 20130706 - Audit Trail
					$audit->action = "Update College";
					$audit->newValue = str_replace("{0}", $description, $audit->newValue);
					$audit->newValue = str_replace("{1}", $code, $audit->newValue);
					$detail = $divHnd->GetDivisions($division);
					$audit->newValue = str_replace("{2}", $detail[0]->description, $audit->newValue);
					$detail = $hnd->GetCollegeTypesByKey($college_type);
					$detail = array_values($detail);
					$audit->newValue = str_replace("{3}", $detail[0]->description, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "College information successfully saved!";
					header("Location: colleges-view.php?id={$id}");
					exit();
				} else {
				
					//$_SESSION['employee'] = $_POST['faculty'];
					$_SESSION['description'] = $_POST['description'];
					$_SESSION['code'] = $_POST['code'];
					$_SESSION['college_type'] = $_POST['college_type'];
					$_SESSION['division'] = $_POST['division'];
					
					$_SESSION['error'] = $hnd->error;
					
					header("Location: colleges-edit.php?id=$id");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new CollegeManager($conn);
		$id = (int) $_GET['id'];
		
		$details = $hnd->GetCollegeDetailsByKey($id);
		
		if($hnd->DeleteCollege($id) == true){
			//modification - 20130706 - Audit Trail
			$audit->action = "Delete College";
			$audit->newValue = str_replace("{0}", $details['Description'], $audit->newValue);
			$audit->newValue = str_replace("{1}", $details['Code'], $audit->newValue);
			$audit->newValue = str_replace("{2}", $details['Division'], $audit->newValue);
			$audit->newValue = str_replace("{3}", $details['CollegeType'], $audit->newValue);
			$auditor->Add($audit);
			
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