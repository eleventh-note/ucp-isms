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

	if(isset($_POST['subject_save'])){
		$action = $_POST['subject_save'];

		switch($action){
			case 'Add':
				$hnd = new SubjectManager($conn);

				$code = $_POST['code'];
				$description = $_POST['description'];
				$type = $_POST['type'];
				$units = $_POST['units'];
				$unitsLab = $_POST['unitsLab'];
				$group = $_POST['group'];
				$isHalf = 0;
				$virtual = 0;
				if(isset($_POST['virtual'])){ $virtual = 1; }
				if(isset($_POST['isHalf'])){ $isHalf = 1; }

				if($hnd->AddSubject($code, $description, $units, $type, $group, $virtual, $unitsLab, $isHalf) == true){;
					$_SESSION['success'] = "Subject successfully added!";
					header("Location: schoolplusplus-subjects.php");
					exit();
				} else {

					$_SESSION['error'] = $hnd->error;
					$_SESSION['code'] = $_POST['code'];
					$_SESSION['description'] = $_POST['description'];
					$_SESSION['type'] = $_POST['type'];
					$_SESSION['units'] = $_POST['units'];
					$_SESSION['unitsLab'] = $_POST['unitsLab'];
					$_SESSION['group'] = $_POST['group'];
					$_SESSION['virtual'] = $_POST['virtual'];
					$_SESSION['isHalf'] = $_POST['isHalf'];

				  header("Location: schoolplusplus-subjects-add.php");
					exit();
				}
				break;
			case 'Save':
				$hnd = new SubjectManager($conn);
				$id = $_POST['id'];

				$code = $_POST['code'];
				$description = $_POST['description'];
				$type = $_POST['type'];
				$units = $_POST['units'];
				$unitsLab = $_POST['unitsLab'];
				$group = $_POST['group'];
				$isHalf = 0;
				$virtual = 0;
				if(isset($_POST['virtual'])){ $virtual = 1; }
				$virtual = 0;
				if(isset($_POST['isHalf'])){ $isHalf = 1; }

				if($hnd->EditSubject($id, $code, $description, $type, $units, $group, $virtual, $unitsLab, $isHalf) == true){;
					$_SESSION['success'] = "Subject successfully saved!";
					header("Location: schoolplusplus-subjects-view.php?id={$id}");
					exit();
				} else {
					$_SESSION['error'] = $hnd->error;
					$_SESSION['code'] = $_POST['code'];
					$_SESSION['description'] = $_POST['description'];
					$_SESSION['type'] = $_POST['type'];
					$_SESSION['units'] = $_POST['units'];
					$_SESSION['unitsLab'] = $_POST['unitsLab'];
					$_SESSION['group'] = $_POST['group'];
					$_SESSION['virtual'] = $_POST['virtual'];
					$_SESSION['isHalf'] = $_POST['isHalf'];

					header("Location: schoolplusplus-subjects-edit.php?id=$id");
					exit();
				}
				break;
		}//end switch

	} elseif(isset($_GET['action'])) {
		$hnd = new SubjectManager($conn);
		$id = (int) $_GET['id'];
		if($hnd->DeleteSubject($id) == true){
			$_SESSION['success'] = "Subject successfully deleted!";
			header("Location: schoolplusplus-subjects.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: schoolplusplus-subjects.php");
			exit();
		}
	} else {
		header("Location: schoolplusplus-subjects.php");
		exit();
	}

	$conn->Close();

?>
