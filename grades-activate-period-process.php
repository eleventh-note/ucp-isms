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
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "grds.inc.php");
	require_once(CLASSLIST . "enl.inc.php");
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
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd = new GradesManager($conn);
	$hnd_sc = new SchoolManager($conn);
	
	if(isset($_POST['grading_activate'])){
		$periodId = (int) $_POST['period'];
		$deadline = $_POST['deadline'];
		$sem = $hnd_sc->GetActiveSemester();
		$sy = $hnd_sc->GetActiveSchoolYear();

		if($hnd->activateAllByGrading($periodId, $deadline, $sy[0]->year_id, $sem[0]->semester_id) == true){
			switch($periodId){
				case 1:
					$period = "Midterm";
					break;
				case 2:
					$period = "Finals";
					break;
			}
			$_SESSION['success'] = "Grading is now activated for " . $period . " until " . $deadline . " midnight.";
			$_SESSION['data']['date'] = $deadline;
			$_SESSION['data']['period'] = $periodId;
			header("Location: grades-activate-period.php");
		} else {
			$_SESSION['error'] = $hnd->error;
			$_SESSION['data']['date'] = $deadline;
			$_SESSION['data']['period'] = $periodId;
			header("Location: grades-activate-period.php");
		}
		exit();
	} else {
		header("Location: grades-activate-period.php");
		exit();
	}
	
	//##### PROCESS INFORMATION FOR FORM
	
	$conn->Close();
