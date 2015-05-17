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

	//## EDIT SECTION [From Clicking on list or after saving data]
	if((isset($_SESSION['section']['id']) || isset($_GET['sid'])) && isset($_SESSION['section']['mode'])){
		if(isset($_SESSION['section']['id'])){
			$section_id = (int) $_SESSION['section']['id'];
		} else {
			$section_id = (int) $_GET['sid'];
			$_SESSION['section']['id'] = $section_id;
		}

		//## get section details
		$sections = $hnd_sh->GetSections($section_id);
		$section = $sections[0]; 												//get index
		$section_subjects = $hnd_sh->GetSectionSubjects($section->section_id); 	//get section subjects

		//## get necessary IDs
		$college_id = (int) $section->college;
		$course_id = (int) $section->course;
		$curriculum_id = (int) $section->curriculum;
		$sem_id = (int) $section->semester;
		$sy_id = (int) $section->sy;
		$level_id = (int) $section->year_level;
		$type_id = (int) $section->type;
		$section_name = (string) $section->name;

		//## Load the details to Session
		$_SESSION['section']['college_id'] = $college_id;
		$_SESSION['section']['course_id'] = $course_id;
		$_SESSION['section']['curriculum_id'] = $curriculum_id;
		$_SESSION['section']['sem_id'] = $sem_id;
		$_SESSION['section']['sy_id'] = $sy_id;
		$_SESSION['section']['type_id'] = $type_id;
		$_SESSION['section']['level_id'] = $level_id;
		$_SESSION['section']['section_name'] = $section_name;

		//## Get Information for display
		$colleges = $hnd_cg->GetColleges($college_id);
		$courses = $hnd_co->GetCourses(null, $course_id);
		$curriculums = $hnd_cu->GetCurriculums($course_id, $curriculum_id);
		$semesters = $hnd_sc->GetSemesters($sem_id);
		$school_years = $hnd_sc->GetSchoolYears($sy_id);
		$types = $hnd_sh->GetSectionTypes($type_id);
		$levels = $hnd_co->GetYearLevels($level_id);


		//## Check if required information were retrieved
		if(sizeof($colleges) > 0 && sizeof($courses) > 0 && sizeof($school_years) > 0 && sizeof($semesters) > 0 && sizeof($curriculums) > 0 && sizeof($types) > 0){
			$college = $colleges[0];
			$course = $courses[0];
			$school_year = $school_years[0];
			$semester = $semesters[0];
			$level = $levels[0];
			$type = $types[0];

			foreach($curriculums as $item){
				$curriculum = $item;
			}

			//$subjects = $hnd_cu->GetSectionSubjects($curriculum_id, $level_id, $sem_id);
			$school_days = $hnd_sc->GetSchoolDaysByKey();
			$school_times = $hnd_sc->GetSchoolTimes();
			$faculties = $hnd_fa->GetFacultiesByKey();
			$rooms = $hnd_fc->GetRoomsByKey();

			$root_subjects = $hnd_su->GetSubjectsByKey();

			$subjects = $section_subjects;
			$_SESSION['section']['subjects'] = serialize($subjects);
			if(!isset($_SESSION['section']['changed'])){
				//##GET Schedules for each subject
				foreach ($subjects as $subject) {
					$schedules = $hnd_sh->GetSubjectSchedulesBySubject($subject->section_subject_id, $subject->section[0]->section_id);
					$csid = $subject->curriculum_subject_id;
					if (sizeof($schedules) > 0) {
						$_SESSION['section']['schedules'][$csid] = array();
						foreach ($schedules as $schedule) {
							$_SESSION['section']['schedules'][$csid][$schedule->schedule_id]['day'] = $schedule->day;
							$_SESSION['section']['schedules'][$csid][$schedule->schedule_id]['from'] = $schedule->from;
							$_SESSION['section']['schedules'][$csid][$schedule->schedule_id]['to'] = $schedule->to;
							$_SESSION['section']['schedules'][$csid][$schedule->schedule_id]['instructor'] = $schedule->instructor;
							$_SESSION['section']['schedules'][$csid][$schedule->schedule_id]['room'] = $schedule->room;
							$_SESSION['section']['schedules'][$csid][$schedule->schedule_id]['capacity'] = $schedule->max_students;
						}
					}
				}
			}

		} else {
			$_SESSION['error'] = array("College, Course or School Year not selected.");
			header("Location: schedules-section.php");
			exit();
		}

	//## CREATE SECTION
	} elseif(isset($_SESSION['section']) && ((isset($_POST['type']) && isset($_POST['section_name'])) || (isset($_SESSION['section']['type_id']) && isset($_SESSION['section']['section_name'])))){

		$college_id = (int) $_SESSION['section']['college_id'];
		$course_id = (int) $_SESSION['section']['course_id'];
		$curriculum_id = (int) $_SESSION['section']['curriculum_id'];
		$sem_id = (int) $_SESSION['section']['sem_id'];
		$sy_id = (int) $_SESSION['section']['sy_id'];
		$level_id = (int) $_SESSION['section']['level_id'];
		if(isset($_POST['type'])){
			$type_id = (int) $_POST['type'];
			$section_name = (string) $_POST['section_name'];
		} else {
			$type_id = (int) $_SESSION['section']['type_id'];
			$section_name = (string) $_SESSION['section']['section_name'];
		}

		//set the details of the section
		$_SESSION['section']['college_id'] = $college_id;
		$_SESSION['section']['course_id'] = $course_id;
		$_SESSION['section']['curriculum_id'] = $curriculum_id;
		$_SESSION['section']['sem_id'] = $sem_id;
		$_SESSION['section']['sy_id'] = $sy_id;
		$_SESSION['section']['type_id'] = $type_id;
		$_SESSION['section']['level_id'] = $level_id;
		$_SESSION['section']['section_name'] = $section_name;

		//get selected college & course
		$colleges = $hnd_cg->GetColleges($college_id);
		$courses = $hnd_co->GetCourses(null, $course_id);
		$curriculums = $hnd_cu->GetCurriculums($course_id, $curriculum_id);
		$semesters = $hnd_sc->GetSemesters($sem_id);
		$school_years = $hnd_sc->GetSchoolYears($sy_id);
		$types = $hnd_sh->GetSectionTypes($type_id);
		$levels = $hnd_co->GetYearLevels($level_id);

		if(sizeof($colleges) > 0 && sizeof($courses) > 0 && sizeof($school_years) > 0 && sizeof($semesters) > 0 && sizeof($curriculums) > 0 && sizeof($types) > 0){
			$college = $colleges[0];
			$course = $courses[0];
			$school_year = $school_years[0];
			$semester = $semesters[0];
			$level = $levels[0];
			$type = $types[0];

			foreach($curriculums as $item){
				$curriculum = $item;
			}

			$subjects = $hnd_cu->GetSectionSubjects($curriculum_id, $level_id, $sem_id);
			$root_subjects = $hnd_su->GetSubjectsByKey();

			$school_days = $hnd_sc->GetSchoolDaysByKey();
			$school_times = $hnd_sc->GetSchoolTimes();
			$faculties = $hnd_fa->GetFacultiesByKey();
			$rooms = $hnd_fc->GetRoomsByKey();

			$_SESSION['section']['subjects'] = serialize($subjects);

			//redirect if no courses found
			if(sizeof($subjects) == 0){
				$_SESSION['error'] = array("There are no subjects available for the selected year and semester.");
				header("Location: schedules-section-select-type.php?cid={$college_id}&cud={$course_id}&cur={$curriculum_id}&sem={$sem_id}&sy={$sy_id}&yr={$level_id}");
				exit();
			} else {
				//## UPDATE SESSION include list of Subjects
				$_SESSION['section']['subjects'] = serialize($subjects);
			}

		} else {
			$_SESSION['error'] = array("College, Course or School Year not selected.");
			header("Location: schedules-section.php");
			exit();
		}

	} else {
		$_SESSION['error'] = array("College, Course or School Year not selected.");
		header("Location: schedules-section.php");
		exit();
	}

	//##### PROCESS ERROR or SUCCESS
	if(isset($_SESSION['error'])){
		$error = $_SESSION['error'];
		unset($_SESSION['error']);
	}
	if(isset($_SESSION['success'])){
		$success = $_SESSION['success'];
		unset($_SESSION['success']);
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xml:lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
	<head>
<?php
//::START OF 'DEFAULT HEAD CONFIG'
	require_once("_system/_config/head_config.php");
//::END OF 'DEFAULT HEAD CONFIG'

	//# Otder CSS Loaded Here
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "home.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "verticalnav.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "columns.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "content.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "actions.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "tables.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "tweaks.css\" />";

	//# Other Javascript Loaded Here
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_PLUGINS . "jquery.mini.js" . "\"></script>";
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_DEFAULT . "scroll.js" . "\"></script>";
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_DEFAULT . "general.js" . "\"></script>";

	//Replace Timer Below witd script for javascript logout`
	//###TIMER###
?>
		<script type="text/javascript">
			function getSelectValue(target){
				target = document.getElementById(target);
				content = target.value;

				return content;
			}

			function redirectTo(url, extension){
				window.location = url + extension;
			}
		</script>
	</head>
	<body id="schedules">
		<div id="container">
			<div id="header">
				<?php require_once("_system/main/banner.inc.php"); ?>
				<?php require_once("_system/main/dashboard.inc.php"); ?>
			</div><?php //end of header ?>

			<div id="body">
				<?php
					//Replace witd error_handling script below
					//###ERROR SCRIPT###
				?>
				<div class="content">
					<div class="column" id="column-first">
						<?php require_once("_system/main/mainmenu.inc.php"); ?>
					</div>
					<div class="column" id="column-second">
						<div class="inner">
							<h1>
								<?php
									if($_SESSION['section']['mode'] == "add"){
										echo "<span class=\"Highlight\">Section &amp; Schedule Administration &raquo; Sections &raquo; Create Section</span>";
									} elseif($_SESSION['section']['mode'] == "edit"){
										echo "<span class=\"Highlight\">Section &amp; Schedule Administration &raquo; Sections &raquo; Edit Section</span>";
									}
								?>
							</h1>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>
							<form action="#" method="post">
								<input type="hidden" name="college" value="<?php echo $college_id; ?>" />
								<input type="hidden" name="course" value="<?php echo $course_id; ?>" />
								<input type="hidden" name="sy" value="<?php echo $sy_id; ?>" />
								<input type="hidden" name="sem" value="<?php echo $sem_id; ?>" />
								<input type="hidden" name="year" value="<?php echo $yr_id; ?>" />

								<hr class="form_top"/>
								<div class="table_form">
									<h2>SECTION DETAILS</h2>
									<table class="form" cellspacing="0">
										<tr class="info">
											<td class="label">College</td>
											<td class="input">:
												<?php echo $college->description; ?>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Course</td>
											<td class="input">:
												<?php echo $course->description; ?>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Curriculum</td>
											<td class="input">:
												<?php echo $curriculum->info; ?>
											</td>
										</tr>
										<tr class="info">
											<td class="label">School Year</td>
											<td class="input">:
												<?php echo "SY " . $school_year->start . " - " . $school_year->end; ?>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Section Year Level</td>
											<td class="input">:
												<?php echo $level->description; ?>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Semester</td>
											<td class="input">:
												<?php echo $semester->description; ?>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Section Name:</td>
											<td class="input">:
												<span class="magnify1">
													<?php echo $section_name; ?>
												</span>
											</td>
										</tr>
									</table>
							<?php
									echo "<a id=\"list_of_subjects\"></a>";
									echo "<p class=\"margin-top: 20px;\">";
											if($type->description == "Block Section"){
												echo "Below are the subjects available for this <b>BLOCK SECTION</b> based on the curriculum. Subjects cannot be removed from a <b>BLOCK SECTION</b>; you are only allowed to set the schedule and the instructor for the subject. To set a subject's schedule, click on the subject from the list below:";
											} else {
												echo "Below are the subjects available for this <b>FREE SECTION</b>.";
											}
									echo "</p>";
									echo "<div class=\"table\">";
										foreach($semesters as $s_item){
											echo "<table class=\"curriculum_subjects default\" style=\"margin-top:10px;\" cellspacing=\"0\" title=\"\">";
												echo "<thead><th colspan=\"11\" class=\"year_level\">Subject Listing</th></thead>";
												echo "<thead>";
													echo "<th class=\"Count\">No.</th>";
													echo "<th class=\"code\">Code</th>";
													echo "<th class=\"description\">Description</th>";
													echo "<th class=\"description\">Subject Group</th>";
													echo "<th class=\"units\">Lec Units</th>";
													echo "<th class=\"units\">Lab Units</th>";
													echo "<th class=\"day\">Day(s)</th>";
													echo "<th class=\"time\">From</th>";
													echo "<th class=\"time\">To</th>";
													echo "<th class=\"\">Room</th>";
													echo "<th class=\"\">Instructor</th>";
													//echo "<th class=\"Actions\"></th>";
												echo "</thead>";

												//define the odd even tables
												$ctr = 0;
												if(sizeof($subjects) > 0){
													$total_units = 0;
													foreach($subjects as $item){
														//#Variables for containing previously outputted data
														$prev_day = "";
														$prev_from = "";
														$prev_to = "";
														$prev_room = "";
														$prev_instructor = "";
														$prev_code = "";
															$ctr++;
															if($ctr % 2 == 0){
																echo "<tr class=\"even\" title=\"Edit schedule for [{$item->code}] {$item->subject}\" onclick=\"window.location='schedules-section-set_schedule.php?id={$item->curriculum_subject_id}';\">";
															} else {
																echo "<tr class=\"odd\" title=\"Edit schedule for [{$item->code}] {$item->subject}\" onclick=\"window.location='schedules-section-set_schedule.php?id={$item->curriculum_subject_id}';\">";
															}
																echo "<td>{$ctr}</td>";
																echo "<td>{$item->code}</td>";
																echo "<td>{$item->subject}</td>";
																echo "<th class=\"description\">" . $root_subjects[$item->subject_id]->group . "</th>";
																echo "<td class=\"center\">{$item->units}</td>";
																echo "<td class=\"center\">{$item->unitsLab}</td>";
																$total_units += $item->units + $item->unitsLab;
																//Day
																echo "<td>";
																	if(isset($_SESSION['section']['schedules'][$item->curriculum_subject_id])){
																		if(sizeof($_SESSION['section']['schedules'][$item->curriculum_subject_id]) > 0){
																			foreach($_SESSION['section']['schedules'][$item->curriculum_subject_id] as $data){
																				if($prev_day != $school_days[$data['day']]->shorthand){
																					echo $school_days[$data['day']]->shorthand . "<br/>";
																					$prev_day = $school_days[$data['day']]->shorthand;
																				} else {
																					echo "<br/>";
																				}
																			}
																		}
																	}
																echo "</td>";
																//From
																echo "<td>";
																	if(isset($_SESSION['section']['schedules'][$item->curriculum_subject_id])){
																		if(sizeof($_SESSION['section']['schedules'][$item->curriculum_subject_id]) > 0){
																			foreach($_SESSION['section']['schedules'][$item->curriculum_subject_id] as $data){
																				foreach($school_times as $time){
																					//if($prev_from != $time->description){
																						if($time->time_id == $data['from']){
																							echo $time->description . "<br/>";
																							$prev_from = $time->description;
																							break;
																						}
																					//} else {
																						//echo "<br/>";
																					//}
																				}
																			}
																		}
																	}
																echo "</td>";
																//To
																echo "<td>";
																	if(isset($_SESSION['section']['schedules'][$item->curriculum_subject_id])){
																		if(sizeof($_SESSION['section']['schedules'][$item->curriculum_subject_id]) > 0){
																			foreach($_SESSION['section']['schedules'][$item->curriculum_subject_id] as $data){
																				foreach($school_times as $time){
																					//if($prev_to != $time->description){
																						if($time->time_id == $data['to']){
																							echo $time->description . "<br/>";
																							$prev_to = $time->description;
																							break;
																						}
																					//} else {
																						//echo "<br/>";
																					//}
																				}
																			}
																		}
																	}
																echo "</td>";
																//Room
																echo "<td>";
																	if(isset($_SESSION['section']['schedules'][$item->curriculum_subject_id])){
																		if(sizeof($_SESSION['section']['schedules'][$item->curriculum_subject_id]) > 0){
																			foreach($_SESSION['section']['schedules'][$item->curriculum_subject_id] as $data){
																				if($prev_room != $rooms[$data['room']]->description){
																					echo $rooms[$data['room']]->description . "<br/>";
																					$prev_room = $rooms[$data['room']]->description;
																				} else {
																					echo "<br/>";
																				}
																			}
																		}
																	}
																echo "</td>";
																//Instructor
																echo "<td>";
																	if(isset($_SESSION['section']['schedules'][$item->curriculum_subject_id])){
																		if(sizeof($_SESSION['section']['schedules'][$item->curriculum_subject_id]) > 0){
																			foreach($_SESSION['section']['schedules'][$item->curriculum_subject_id] as $data){

																				if (isset($faculties[$data['instructor']])) {
																					$tmp = $faculties[$data['instructor']]->employee->last_name . ", " . $faculties[$data['instructor']]->employee->first_name . " ";

																					if(trim($faculties[$data['instructor']]->employee->middle_name) <> ''){
																						$tmp .= substr($faculties[$data['instructor']]->employee->middle_name,0,1) . ".";
																					}

																					if($prev_instructor != $tmp){
																						echo $tmp;
																						echo "<br/>";
																						$prev_instructor = $tmp;
																					} else {
																						echo "<br/>";
																					}
																				}

																			}
																		}
																	}
																echo "</td>";

																//echo "<td class=\"Actions\">";
																	//echo "<a href=\"schedules-section-process.php?action=delete&id={$item->curriculum_subject_id}\" onclick=\"return confirm('Delete s from curriculum? Click OK to continue.')\">Delete</a>";
																//echo "</td>";
															echo "</tr>";
													}
													echo "<thead><th colspan=\"3\" style=\"text-align: right;\">Total Units:</th><th colspan=\"1\">{$total_units}</th></thead>";
												}
												if($ctr == 0){
													echo "<tr class=\"odd\" class=\"reminder\"><td colspan=\"10\">No subjects available. </td></tr>";
												}
											echo "</table>";
										}


									echo "</div>";

							?>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
											<td>
												<?php
													if($_SESSION['section']['mode'] == "add"){
														echo "<input type=\"button\" class=\"button\" value=\"Cancel\" onclick=\"result = confirm('Continue cancelling creation of this section? Click OK to continue.'); if(result==true){ window.location='schedules-section-select-type.php?cid={$college_id}&cud={$course_id}&cur={$curriculum_id}&sem={$sem_id}&sy={$sy_id}&yr={$level_id}'; }\"/>";
													} elseif($_SESSION['section']['mode'] == "edit"){
														echo "<input type=\"button\" class=\"button\" value=\"New Section\" ";
														if(!isset($_SESSION['section']['changed'])){
															echo "onclick=\"window.location='schedules-section.php';\" ";
														} else {
															echo "onclick=\"result = confirm('A change has been made to the current section. Leave without saving? Click OK to continue.'); if(result){ window.location='schedules-section.php'; }\" ";
														}
														echo "/>";
													}
												?>

												<input type="button" class="button" value="Save Section" onclick="window.location='schedules-section-subjects-process.php?mode=<?php echo $_SESSION['section']['mode']; ?>'"/>
												<?php
													//# Show Delete Section Button if section is already saved
													if(isset($_SESSION['section']['mode'])){
														$mode = $_SESSION['section']['mode'];
														if($mode == "edit"){
															echo "<input type=\"button\" class=\"button\" value=\"Delete Section\" onclick=\"result = confirm('Are you sure you want to delete this section? Click OK to continue.'); if(result==true){ window.location='schedules-section-subjects-process.php?mode=delete'; }\" />";
															//# Back to List of Active Sections
															echo "<input type=\"button\" class=\"button\" value=\"Back to List of Active Sections\"";
															if(!isset($_SESSION['section']['changed'])){
																echo "onclick=\"window.location='schedules.php';\" ";
															} else {
																echo "onclick=\"result = confirm('A change has been made to the current section. Leave without saving? Click OK to continue.'); if(result){ window.location='schedules.php'; }\" ";
															}
															echo "/>";
														}
													}
												?>
												<?php //<input type="submit" class="button" name="college_save" value="Add" /> ?>
											</td>
										</tr>
									</table>
								</div><?php //end TABLE FORM ?>
							</form>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div><?php //end of body ?>
			<div id="footer">
				<?php require_once("_system/main/footer.inc.php"); ?>
			</div><?php //end of footer ?>
		</div>
	</body>
</html>
<?php
	//::START OF 'CLOSING REMARKS'
		//memory releasing and stuffs
	//::END OF 'CLOSING REMARKS'
	//close the connection
	$conn->Close();
?>
