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
	$audit->tableName = "adm-course_list";
	$audit->newValue = "Code:{0}; Description: {1}; College: {2}; MaxYearLevel: {3}";
	
	if(isset($_POST['course_save'])){
		$action = $_POST['course_save'];
		
		switch($action){
			case 'Add':
				$hnd = new CourseManager($conn);
				$description = $_POST['description'];
				$code = $_POST['code'];
				$college = $_POST['college'];
				$level = (int) $_POST['level'];
				
				if($hnd->AddCourse($code, $description, $college, $level) == true){
					//modification - 20130706 - Audit Trail
					$audit->action = "Add Course";
					$audit->newValue = str_replace("{0}", $code, $audit->newValue);
					$audit->newValue = str_replace("{1}", $description, $audit->newValue);
					$audit->newValue = str_replace("{2}", $college, $audit->newValue);
					$audit->newValue = str_replace("{3}", $level, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "Course successfully created!";
					header("Location: colleges-view.php?id={$college}");
					exit();
				} else {
				
					$_SESSION['description'] = $_POST['description'];
					$_SESSION['code'] = $_POST['code'];
					$_SESSION['level'] = $_POST['level'];
					
					$_SESSION['error'] = $hnd->error;
					header("Location: colleges-course-add.php?id={$college}");
					
					exit();
				}
				break;
			case 'Save':
				$hnd = new CourseManager($conn);
				$id = $_POST['id'];
				$description = $_POST['description'];
				$code = $_POST['code'];
				$college = $_POST['college'];
				$level = (int) $_POST['level'];
				
				if($hnd->EditCourse($id, $code, $description, $college, $level) == true){
					//modification - 20130706 - Audit Trail
					$audit->action = "Update Course";
					$audit->newValue = str_replace("{0}", $code, $audit->newValue);
					$audit->newValue = str_replace("{1}", $description, $audit->newValue);
					$audit->newValue = str_replace("{2}", $college, $audit->newValue);
					$audit->newValue = str_replace("{3}", $level, $audit->newValue);
					$audit->newValue = "CourseID: {$id}; " . $audit->newValue;
					$auditor->Add($audit);
					
					$_SESSION['success'] = "College successfully saved!";
					header("Location: colleges-course-view.php?id={$id}&cid={$college}");
					exit();
				} else {
				
					$_SESSION['description'] = $_POST['description'];
					$_SESSION['code'] = $_POST['code'];
					$_SESSION['level'] = $_POST['level'];
					
					$_SESSION['error'] = $hnd->error;
					
					header("Location: colleges-course-edit.php?id={$id}&cid={$college}");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new CourseManager($conn);
		$id = (int) $_GET['id'];
		$college = (int) $_GET['cid'];
		
		if($hnd->DeleteCourse($id) == true){
			//modification - 20130706 - Audit Trail
			$audit->action = "Delete Course";
			$audit->newValue = "CourseId: {0}";
			$audit->newValue = str_replace("{0}", $id, $audit->newValue);
			$auditor->Add($audit);
			
			$_SESSION['success'] = "Course successfully deleted!";
			header("Location: colleges-view.php?id={$college}");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: colleges-view.php?id={$college}");
			exit();
		}
	} else {
		header("Location: colleges.php");
		exit();
	}
		
	$conn->Close();
	
?>