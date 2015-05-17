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
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "grds.inc.php");
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
		$PagePrivileges->AddPrivilege("Grades - Administrator");
		$PagePrivileges->AddPrivilege("Grades - Encoder");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	$hnd = new GradesManager($conn);
	
	if(isset($_POST['save_grades'])){
		$sectionSubject = (int) $_POST['sectionSubject'];
		$midtermGrades = array();
		$finalGrades = array();
		$gradeIds = $_POST['gradeId'];
		if(isset($_POST['midtermGrade'])){ $midtermGrades = $_POST['midtermGrade']; }
		if(isset($_POST['finalGrade'])){ $finalGrades = $_POST['finalGrade']; }
		
		foreach($midtermGrades as $k => $data){
			if(is_string($data)){
				$data = strtoupper($data);
			}
			
			$hnd->saveMidtermGrade($gradeIds[$k], $data);
		}
		
		foreach($finalGrades as $k => $data){
			if(is_string($data)){
				$data = strtoupper($data);
			}
			
			$hnd->saveFinalGrade($gradeIds[$k], $data);
		}
		
		
		$_SESSION['success'] = "Grades successfully saved!";
		header("Location:grades-encode.php?id=" . $sectionSubject);
		exit();
	} else {
		header("Location: grades-encode-subject_list.php");
		exit();
	}
	
	$conn->Close();
