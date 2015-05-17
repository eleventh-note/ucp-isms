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
	require_once(CLASSLIST . "dvsns.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "crrclm.inc.php");
	require_once(CLASSLIST . "schdls.inc.php");
	
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
		$PagePrivileges->AddPrivilege("Schedules - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd_cg = new CollegeManager($conn);
	$hnd_co = new CourseManager($conn);
	$hnd_sc = new SchoolManager($conn);
	$hnd_su = new SubjectManager($conn);
	$hnd_cu = new CurriculumManager($conn);
	$hnd_sh = new ScheduleManager($conn);

	if(isset($_GET['id']) && isset($_GET['cid']) && isset($_GET['cud']) && isset($_GET['cur']) && isset($_GET['sem']) && isset($_GET['yr']) && isset($_GET['sy'])){
		// $college_id = (int) $_GET['cid'];
		// $course_id = (int) $_GET['cud'];
		// $curriculum_id = (int) $_GET['cur'];
		// $sem_id = (int) $_GET['sem'];
		// $sy_id = (int) $_GET['sy'];
		// $level_id = (int) $_GET['yr'];
		
		//set the details of the section
		unset($_SESSION['section']);
		
		// $_SESSION['section']['college_id'] = $college_id;
		// $_SESSION['section']['course_id'] = $course_id;
		// $_SESSION['section']['curriculum_id'] = $curriculum_id;
		// $_SESSION['section']['sem_id'] = $sem_id;
		// $_SESSION['section']['sy_id'] = $sy_id;
		// $_SESSION['section']['level_id'] = $level_id;
		$_SESSION['section']['id'] = (int) $_GET['id'];
		$_SESSION['section']['mode'] = 'edit';
		
		header("Location: schedules-section-subjects.php");		
		exit();
		
	} else {
		$_SESSION['error'] = array("Section unknown.");
		header("Location: schedules.php");
		exit();
	}

	$conn->Close();
?>