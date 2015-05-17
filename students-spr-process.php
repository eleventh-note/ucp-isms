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
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "stdnts.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
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
		$PagePrivileges->AddPrivilege("Student - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	
	//Sem & Year
	$hnd_sc = new SchoolManager($conn);
	$semesters = $hnd_sc->GetActiveSemester();
	$semester = $semesters[0];
	$school_years = $hnd_sc->GetActiveSchoolYear();
	$school_year = $school_years[0];
	
	$sem = $semester->semester_id;
	$sy = $school_year->year_id;
	
	//### SAVE SPR
	if(isset($_POST['record_save'])){
		$action = $_POST['record_save'];
		
		switch($action){				
			//## SAVE INFORMATION [EDIT MODE]
			case 'Save Record':

				$id = $_POST['spr_id'];
				
				$hnd = new StudentManager($conn);
				
				//## NAME
				$last_name = ucwords($_POST['last_name']);
				$first_name = ucwords($_POST['first_name']);
				$middle_name = ucwords($_POST['middle_name']);

				//## BIRTHDAY
				$birthday = mktime(0,0,0,$_POST['bday_mm'], $_POST['bday_dd'], $_POST['bday_yyyy']);
				$birthday = date("Y-m-d", $birthday);
				
				//## GENDER & MARITAL STATUS
				$gender = (int) $_POST['gender'];
				$marital_status = (int) $_POST['marital_status'];
				
				//## CITY ADDRESS & PROVINCIAL ADDRESS
				$mailing_address = $_POST['mailing_address'];
				$place_of_birth = $_POST['place_of_birth'];
				
				//## TELEPHONE NUMBER, MOBILE NUMBER & EMAIL
				$telephone_number = $_POST['telephone_number'];
				$mobile_number = $_POST['mobile_number'];
				$email = $_POST['email'];
				$religion = $_POST['religion'];
				$citizenship = $_POST['citizenship'];
				$region = $_POST['region'];
				$country = $_POST['country'];
				$acr = $_POST['acr'];
				$city_address = $_POST['city_address'];
				$provincial_address = $_POST['provincial_address'];
					
				$student_no = $_POST['student_no'];
				$school_year = $_POST['school_years'];
				$semester = $_POST['semesters'];
				$curr_course = $_POST['curr_course'];
				$course = $_POST['course'];
				$student_status = $_POST['student_status'];
				$enrollment_status = $_POST['enrollment_status'];
				$graduated = (isset($_POST['graduated']))? 1 : 0;
				
				if($hnd->EditSpr(
					$id,
					$student_no,
					$last_name,
					$first_name,
					$middle_name, 
					$birthday, //null
					$gender, //int CONSTRAINT
					$marital_status, //int CONSTRAINT
					$mailing_address, //varchar
					$place_of_birth, //varchar
					$telephone_number, //null
					$mobile_number, //null
					$email, //null
					$school_year,
					$semester,
					$religion,
					$citizenship,
					$region,
					$country,
					$acr,
					$city_address,
					$provincial_address
				) == true){;
				 
					$_SESSION['success'][] = "Student Personal Record successfully updated!";
					// header("Location: students-spr-view.php?id={$id}");
					// exit();
				} else {
				
					$_SESSION['last_name'] = $last_name;
					$_SESSION['first_name'] = $first_name;
					$_SESSION['middle_name'] = $middle_name;
					$_SESSION['birthday'] = $birthday;
					$_SESSION['gender'] = $gender;
					$_SESSION['marital_status'] = $marital_status;
					$_SESSION['mailing_address'] = $mailing_address;
					$_SESSION['place_of_birth'] = $place_of_birth;
					$_SESSION['telephone_number'] = $telephone_number;
					$_SESSION['mobile_number'] = $mobile_number;
					$_SESSION['email'] = $email;
					$_SESSION['last_school'] = $last_school;
					$_SESSION['school_year'] = $school_year;
					$_SESSION['semester'] = $semester;
					
					$_SESSION['first_choice'] = $first_choice;
					$_SESSION['second_choice'] = $second_choice;
					$_SESSION['third_choice'] = $third_choice;
					$_SESSION['course_passed'] = $course_passed;
					
					$_SESSION['application_type'] = $application_type;
					$_SESSION['application_status'] = $application_status;
					$_SESSION['application_number'] = $application_number;
					
					$_SESSION['error'] = $hnd->error;
					foreach($hnd->error as $err){
						$_SESSION['error'][] = $err;
					}
					// header("Location: students-spr-edit.php?id={$id}");
					// exit();
					
				}// # END IF EDIT SPR
				
					
				var_dump($student_no);
				var_dump($school_year);
				var_dump($semester);
				var_dump($curr_course);
				var_dump($course);
				var_dump($student_status);
				var_dump($enrollment_status);
				var_dump($graduated);
				//exit();
				if($hnd->EditCurrentAcademicBackground(
					$id, $student_no, $curr_course, $course, $student_status, $enrollment_status,
					$semester, $school_year, $graduated, $sy, $sem) == true
				){
					$_SESSION['success'][] = "Current Academic Background successfully updated!";
					if($course != $curr_course){
						$_SESSION['success'][] = "Course successfully changed!";
					}
				}else {
					foreach($hnd->error as $err){
						$_SESSION['error'][] = $err;
					}
				}	// # END IF CURRENT ACADEMIC BACKGROUND
				
				if(sizeof($_SESSION['error']) == 0){
					header("Location: students-spr-view.php?id={$id}");
					exit();
				} else {
					header("Location: students-spr-edit.php?id={$id}");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new StudentManager($conn);
		
		$action = $_GET['action'];
		$id = (int) $_GET['id'];
		
		switch($action){
			case 'delete':
				if($hnd->DeleteAdmission($id) == true){
					$_SESSION['success'] = "Student Pre-Admission Record successfully deleted!";
					header("Location: students-preadmission.php");
					exit();
				} else {
					$_SESSION['error'] = $hnd->error;
					header("Location: students-preadmission-view.php?id={$id}");
					exit();
				}
				break;
			case 'spr':
			
				$records = $hnd->GetPreAdmissionRecordForEdit($id);
				
				$student_status = (int) $_GET['ss'];
				$student_type = (int) $_GET['st'];
				$enrollment_status = (int) $_GET['es'];
				$course = (int) $_GET['c'];
				
				if(sizeof($records) == 1){
					$record = $records[0];
					if ($hnd->AddSPR(
					$record->last_name,
					$record->first_name,
					$record->middle_name, 
					$record->birthday, //null
					$record->gender, //int CONSTRAINT
					$record->marital_status, //int CONSTRAINT
					$record->mailing_address, //varchar
					$record->place_of_birth, //varchar
					$record->telephone_number, //null
					$record->mobile_number, //null
					$record->email, //null
					$record->school_year,
					$record->semester
					) == true){
						$hnd->ToggleAdmissionSPRCreated($id);
						$hnd->AddCurrentAcademicBackground(
								$hnd->student_no, $course, $student_type, $student_status, $enrollment_status,
								$record->semester, $record->school_year, $record->application_type, $sy, $sem
							);
						$_SESSION['success'] = "Student Personal Record (SPR) has been successfully created! Student Number <b>{$hnd->student_no}</b>. <a href=\"students-spr-view.php?id={$hnd->student_id}\">Click here</a> to view SPR.";
						header("Location: students-preadmission-view.php?id={$id}");
						exit();
					} else {
						$_SESSION['error'] = $hnd->error;
						header("Location: students-preadmission-view.php?id={$id}");
						exit();
					}
					
					
					break;
				} else {
					$_SESSION['error'] = array("Pre-Admission record unknown.");
					header("Location: students-preadmission.php	");
					exit();
				}
		}
		
	} else {
		header("Location: students-preadmission.php");
		exit();
	}
		
	$conn->Close();
	
?>