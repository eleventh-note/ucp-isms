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
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "crrclm.inc.php");
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
	
	if(isset($_POST['curriculum_save'])){
	
		$action = $_POST['curriculum_save'];
		
		switch($action){
			case 'Add to Curriculum':
				$hnd = new CurriculumManager($conn);
				
				//## GET INITIAL INFORMATION
				$college = (int) $_POST['college'];
				$course = (int) $_POST['course'];
				$sy = (int) $_POST['sy'];
				$sem = (int) $_POST['sem'];
				$year = (int) $_POST['year'];
				$subject = (int) $_POST['subject'];
				$prerequisites = null;
				$corequisites = null;
				
				//##Check first if curriculum is already registered
				//-->if TRUE, then just add subject after checking if valid
				//-->if FALSE, registere curriculum first
				
				echo 'College ID: ' . $college . '<br/>';
				echo 'Course ID: ' . $course . '<br/>';
				echo 'SY ID: ' . $sy . '<br/>';
				echo 'Semester ID: ' . $sem . '<br/>';
				echo 'Year ID: ' . $year . '<br/>';
				echo 'Subject ID: ' . $subject . '<br/>';
				
				//check duplicates inside the list
				function checkDuplicates($list, $url, $error){
					$ctr = 0;
					
					for($i = 0; $i < sizeof($list); $i++){
						for($j = $i+1; $j < sizeof($list); $j++){
							if($list[$i] == $list[$j]){
								$_SESSION['error'] = array($error);
								header("Location:{$url}");
								exit();
							}
						}
					}
				}
				
				//C## Check if Prerequisite is valid
				if(isset($_POST['prerequisite'])){
					$prerequisites = $_POST['prerequisite'];
					$ctr = 0;
					//remove all negatives
					foreach($prerequisites as $item){
					
						if($item == -1){
						  unset($prerequisites[$ctr]);
						}
						
						$ctr++;
					}
					
					$prerequisites = array_values($prerequisites);
					
					checkDuplicates($prerequisites, 
						"schoolplusplus-curriculums-select-subject.php?cid={$college}&cud={$course}&sy={$sy}&sem={$sem}&yr={$year}", 
						"Prerequisites should have no duplicates."
					);
					
				}
				
				$corequisites = $_POST['corequisite'];
				$ctr = 0;
				//remove all negatives
				foreach($corequisites as $item){
				
					if($item == -1){
					  unset($corequisites[$ctr]);
					}
					$ctr++;
					
				}					

				//## Check if Corequisite is valid
				$corequisites = array_values($corequisites);
				checkDuplicates($corequisites, 
					"schoolplusplus-curriculums-select-subject.php?cid={$college}&cud={$course}&sy={$sy}&sem={$sem}&yr={$year}", 
					"Corequisites should have no duplicates."
				);
								
				//##Get the CurriculumID
				$curriculum = $hnd->VerifyCurriculum($course, $sy);
				
				if($curriculum == -1){
				
					//##Register Curriculum
					if($hnd->AddCurriculum($sy, $course) == true){
						$curriculum = $hnd->VerifyCurriculum($course, $sy);
					} else {
						$_SESSION['error'] = $hnd->error;//array("Error creating curriculum. Please try again. If problem persists, please contact developer.");
						header("Location:schoolplusplus-curriculums-select-subject.php?cid={$college}&cud={$course}&sy={$sy}&sem={$sem}&yr={$year}");
						exit();
					}
				}
				
				if($hnd->AddSubject($curriculum, $subject, $sem, $year, $prerequisites, $corequisites) == true){;
					$_SESSION['success'] = "Subject successfully added! <a href=\"#list_of_subjects\" class=\"scroll\">Click Here to view list.</a>";
					header("Location:schoolplusplus-curriculums-select-subject.php?cid={$college}&cud={$course}&sy={$sy}&sem={$sem}&yr={$year}");
					exit();
				} else {
					$_SESSION['error'] = $hnd->error;
				    header("Location:schoolplusplus-curriculums-select-subject.php?cid={$college}&cud={$course}&sy={$sy}&sem={$sem}&yr={$year}");
					exit();
				}
				break;
			case 'Save':
				/*################
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
				#################*/
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		//## GET INITIAL INFORMATION
		$college = (int) $_GET['cid'];
		$course = (int) $_GET['cud'];
		$sy = (int) $_GET['sy'];
		$sem = (int) $_GET['sem'];
		$year = (int) $_GET['yr'];
		$subject = (int) $_GET['id'];
		
		$action = $_GET['action'];
		$hnd = new CurriculumManager($conn);
		
		if($action == "delete"){
				if($hnd->DeleteSubject($subject) == true){
				$_SESSION['success'] = "Subject successfully removed from curriculum! <a href=\"#list_of_subjects\" class=\"scroll\">Click Here to view list.</a>";
				header("Location: schoolplusplus-curriculums-select-subject.php?cid={$college}&cud={$course}&sy={$sy}&sem={$sem}&yr={$year}");
				exit();
			} else {
				$_SESSION['error'] = $hnd->error;
				header("Location: schoolplusplus-curriculums-select-subject.php?cid={$college}&cud={$course}&sy={$sy}&sem={$sem}&yr={$year}");
				exit();
			}
		}
	} else {
		header("Location: schoolplusplus-curriculums-select-subject.php?cid={$college}&cud={$course}&sy={$sy}&sem={$sem}&yr={$year}");
		exit();
	}
		
	$conn->Close();
	
?>
