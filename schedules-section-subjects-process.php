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
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "crrclm.inc.php");
	require_once(CLASSLIST . "schdls.inc.php");
	require_once(CLASSLIST . "fclts.inc.php");

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
	$hnd_fa = new FacultyManager($conn);
	$hnd_fc = new FacilitiesManager($conn);

	if(isset($_SESSION['section']) && isset($_GET['mode'])){

		//get mode type
		$mode = $_GET['mode'];

		switch($mode){
			/*################################
				ADD DATA TO DATABASE
			##################################*/
			case 'add':
				//GET DATA FROM SESSION TO SAVE ON DATABASE
				$college_id = (int) $_SESSION['section']['college_id'];
				$course_id = (int) $_SESSION['section']['course_id'];
				$curriculum_id = (int) $_SESSION['section']['curriculum_id'];
				$sem_id = (int) $_SESSION['section']['sem_id'];
				$sy_id = (int) $_SESSION['section']['sy_id'];
				$type_id = (int) $_SESSION['section']['type_id'];
				$level_id = (int) $_SESSION['section']['level_id'];
				$section_name = $_SESSION['section']['section_name'];
				//UNSERIALIZE TO GET DATA FROM SESSION
				$subjects = unserialize($_SESSION['section']['subjects']);
				if(isset($_SESSION['section']['schedules'])){
					$schedules = $_SESSION['section']['schedules'];
				}
				//var_dump($_SESSION['section']);
				//var_dump($subjects);
				//redirect if no courses found

				//check first if there are subjects to be saved
				if(sizeof($subjects) > 0){
					//check if success then add subjects to database
					$total_subjects = sizeof($subjects);
					$saved_subjects = 0;
					if($hnd_sh->AddSection($section_name, $type_id, $curriculum_id, $sy_id, $sem_id, $level_id) == true){
						foreach($subjects as $subject){
							if($hnd_sh->AddSectionSubject($hnd_sh->section_id, $subject->curriculum_subject_id) == true){
								//#Check if a schedule has been set for this subject
								if(isset($schedules)){
									if(isset($schedules[$subject->curriculum_subject_id])){
										//#Set schedules to variable
										$subject_schedules = $schedules[$subject->curriculum_subject_id];
										foreach($subject_schedules as $sched){
											$hnd_sh->AddSubjectSchedule($hnd_sh->section_subject_id, $sched['day'], $sched['from'], $sched['to'], $sched['instructor'], $sched['room'], $sched['capacity']);
										}
									}
								}
								$saved_subjects++;
							}
						}

						if($saved_subjects == $total_subjects){

							//##REMOVE Original Schedules and take it from the database
							unset($_SESSION['section']);
							$_SESSION['section'] = array();
							unset($_SESSION['section']['schedules']);
							$_SESSION['section']['schedules'] = array();

							$_SESSION['section']['mode'] = "edit";
							$_SESSION['section']['id'] = $hnd_sh->section_id; //*# THIS WILL BE USED FOR EDITING SECTIONS *#
							$_SESSION['success'] = "Section and Subjects successfully saved.";
							header("Location: schedules-section-subjects.php");
							exit();
						}
					} else {
						$_SESSION['error'] = $hnd_sh->error;
						header("Location: schedules-section-subjects.php");
						exit();
					}
				} else {
					$_SESSION['error'] = array('There are no subjects to be added for this section');
					header("Location: schedules-section-subjects.php");
					exit();
				}
				break;

			/*################################
				EDIT DATA IN DATABASE
			##################################*/
			case 'edit':

				if(isset($_SESSION['section']['id'])){

					//GET DATA FROM SESSION TO SAVE ON DATABASE
					$college_id = (int) $_SESSION['section']['college_id'];
					$course_id = (int) $_SESSION['section']['course_id'];
					$curriculum_id = (int) $_SESSION['section']['curriculum_id'];
					$sem_id = (int) $_SESSION['section']['sem_id'];
					$sy_id = (int) $_SESSION['section']['sy_id'];
					$type_id = (int) $_SESSION['section']['type_id'];
					$level_id = (int) $_SESSION['section']['level_id'];
					$section_name = $_SESSION['section']['section_name'];

					//UNSERIALIZE TO GET DATA FROM SESSION
					$subjects = unserialize($_SESSION['section']['subjects']);
					if(isset($_SESSION['section']['schedules'])){
						$schedules = $_SESSION['section']['schedules'];
					}

					//var_dump($_SESSION['section']);
					//var_dump($subjects);
					//redirect if no courses found

					//check first if there are subjects to be saved
					if(sizeof($subjects) > 0){

						//check if success then add subjects to database
						$total_subjects = sizeof($subjects);
						if($total_subjects > 0){
							switch($type_id){
								case 1: //# Block Section
									$saved_subjects = 0;
									foreach($subjects as $subject){
										//#Subject cannot be added or deleted once section is saved
										//#Delete Schedules for each subject then reload
										// 2015/02/03 -- STOP DELETING ALL SCHEDULES
										//$hnd_sh->DeleteSubjectSchedules($subject->section_subject_id);

										//#Check if schedule has been set for this subject
										if(isset($schedules)){
											if(isset($schedules[$subject->curriculum_subject_id])){
												//#Set schedules to variable
												$subject_schedules = $schedules[$subject->curriculum_subject_id];
												foreach($subject_schedules as $schedule_id => $sched){

													if (isset($sched['action'])) {
														switch ($sched['action']) {
															case 'add':
																$hnd_sh->AddSubjectSchedule(
																					$subject->section_subject_id,
																					$sched['day'],
																					$sched['from'],
																					$sched['to'],
																					$sched['instructor'],
																					$sched['room'],
																					$sched['capacity']
																				);
																break;

															case 'update':
																echo $schedule_id . '<br/>';
																$hnd_sh->UpdateSubjectSchedule(
																					$schedule_id,
																					$sched['day'],
																					$sched['from'],
																					$sched['to'],
																					$sched['instructor'],
																					$sched['room'],
																					$sched['capacity']
																				);
																break;

															case 'delete':
																$hnd_sh->DeleteSubjectSchedule($schedule_id);
																break;
														}
													}
												}
											}
										}
										$saved_subjects++;
									}
									//exit();

									if($saved_subjects == $total_subjects){
										$_SESSION['section']['mode'] = "edit";
										unset($_SESSION['section']['changed']);
										$_SESSION['success'] = "Section and Subjects successfully saved.";
										header("Location: schedules-section-subjects.php");
										exit();
									}
									break;
								case 2: //# Free Section
									break;
							}
						} else {
							$_SESSION['error'] = array('There are no subjects to be saved.');
							header("Location: schedules-section-subjects.php");
							exit();
						}
					} else {
						$_SESSION['error'] = array('There are no subjects to be added for this section');
						header("Location: schedules-section-subjects.php");
						exit();
					}
				}
				break;
			/*################################
				DELETE DATA IN DATABASE
			##################################*/
			case 'delete':
				//#Get Section ID
				$section_id = (int) $_SESSION['section']['id'];
				//#Get Subjects of the current section
				$section_subjects = $hnd_sh->GetSectionSubjects($section_id);
				//#Get number of subjects to delete
				$total_subjects = sizeof($section_subjects);
				$delete_count = 0;
				//#Delete each subject including their schedules
				foreach($section_subjects as $item){
					if($hnd_sh->DeleteSectionSubject($item->section_subject_id) == true){
						$delete_count++;
					}
				}

				if($delete_count == $total_subjects){
					if($hnd_sh->DeleteSection($section_id) == true){
						unset($_SESSION['section']);
						$_SESSION['success'] = "Section successfully deleted.";
						header("Location: schedules.php");
						exit();
					}
				}

				break;
		}

	} else {
		$_SESSION['error'] = array("Required processes in creating a section was not followed. Please start below.");
		header("Location: schedules-section.php");
		exit();
	}

	//close the connection
	$conn->Close();
?>
