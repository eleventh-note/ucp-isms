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

	//## FROM HERE ON, all that is required is the 'section' index in the session array
	if(isset($_SESSION['section']) && (isset($_GET['id']) || isset($_POST['subject']))){

		if(isset($_GET['id'])){
			$subject_id = (int) $_GET['id'];
		} else {
			$subject_id = (int) $_POST['subject'];
		}

		$college_id = (int) $_SESSION['section']['college_id'];
		$course_id = (int) $_SESSION['section']['course_id'];
		$curriculum_id = (int) $_SESSION['section']['curriculum_id'];
		$sem_id = (int) $_SESSION['section']['sem_id'];
		$sy_id = (int) $_SESSION['section']['sy_id'];
		$level_id = (int) $_SESSION['section']['level_id'];
		$type_id = (int) $_SESSION['section']['type_id'];
		$section_name = (string) $_SESSION['section']['section_name'];

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


			//get list of subjects from session that is passed
			$subjects = unserialize($_SESSION['section']['subjects']);

			if(isset($subjects[$subject_id])){
				$subject = $subjects[$subject_id];
			} else {
				header("Location: schedules-section-subjects.php");
				exit();
			}

			//# GET DATA REQUIRED FOR INPUTS
			$school_days = $hnd_sc->GetSchoolDaysByKey();
			$school_times = $hnd_sc->GetSchoolTimes();
			$faculties = $hnd_fa->GetFacultiesByKey();
			$rooms = $hnd_fc->GetRoomsByKey();

			//create an array for the subject schedules if not set
			if(!isset($_SESSION['section']['schedules'])){
				//#Array of SubjectSchedule class in 'schdls.inc.php'
				$_SESSION['section']['schedules'][$subject_id] = array();
			}

			//redirect if no courses found
			if(sizeof($subjects) == 0){
				$_SESSION['error'] = array("There are no subjects available.");
				header("Location: schedules-section-select-semyear.php?cid={$college_id}&cud={$course_id}");
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
	//echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_DEFAULT . "general.js" . "\"></script>";

	//Replace Timer Below witd script for javascript logout`
	//###TIMER###
?>
		<script type="text/javascript">
			var ADD = 1;
			var EDIT = 2;
			var mode = ADD;

			function getSelectValue(target){
				target = document.getElementById(target);
				content = target.value;

				return content;
			}

			function redirectTo(url, extension){
				window.location = url + extension;
			}

			function getValue(target){
				target = document.getElementById(target);
				return target.value;
			}

			$(document).ready( function() {
				// Bind Edit Function
				$edit = $('.Actions .edit');
				$edit.on('click', function (e) {
					e.preventDefault();
					mode = EDIT;
					$row = $(this).parent().parent();
					var schedule = $row.attr('data-schedule');
					var day = $row.attr('data-day');
					var from = $row.attr('data-from');
					var to = $row.attr('data-to');
					var instructor = $row.attr('data-instructor');
					var room = $row.attr('data-room');
					var capacity = $row.attr('data-capacity');

					$('#oScheduleId').val(schedule);
					$('#oInstructor').val(instructor);
					$('#oRoom').val(room);
					$('#oDay').val(day);
					$('#oFrom').val(from);
					$('#oTo').val(to);

					$('.button.add').css({ 'display' : 'none' });
					$('.button.cancel').css({ 'display' : 'inline-block' });
					$('.button.save').css({ 'display' : 'inline-block' });
				});

				$('.button.back').on('click', function (e) {
					window.location='schedules-section-subjects.php';
				});

				$('.button.cancel').on('click', function (e) {
					mode = ADD;
					$('#oScheduleId').val(-1);
					$('#oInstructor').val(-1);
					$('#oRoom').val(-1);
					$('#oDay').val(-1);
					$('#oFrom').val(-1);
					$('#oTo').val(-1);

					$('.button.add').css({ 'display' : 'inline-block' });
					$('.button.cancel').css({ 'display' : 'none' });
					$('.button.save').css({ 'display' : 'none' });
				});

				$('.button.save').on('click', function (e) {

				});
			});
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
								<span class="Highlight">Section &amp; Schedule Administration &raquo; Sections &raquo; Create Section &raquo; Subject Schedule</span>
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
							<form action="schedules-section-set_schedule-process.php" method="post">
								<input type="hidden" name="college" value="<?php echo $college_id; ?>" />
								<input type="hidden" name="course" value="<?php echo $course_id; ?>" />
								<input type="hidden" name="sy" value="<?php echo $sy_id; ?>" />
								<input type="hidden" name="sem" value="<?php echo $sem_id; ?>" />
								<input type="hidden" name="subject" value="<?php echo $subject_id; ?>" />

								<hr class="form_top"/>
								<div class="table_form">
									<h2>SUBJECT DETAILS</h2>
									<table class="form" cellspacing="0">
										<tr class="info">
											<td class="label">Subject</td>
											<td class="input">:
												<span class="magnify3">
													<?php echo $subject->subject; ?>
												</span>
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
											echo "Below are the list of schedules for this subject based on the section to be created:";
									echo "</p>";
									echo "<div class=\"table\">";
										echo "<table class=\"curriculum_subjects default\" style=\"margin-top:10px;\" cellspacing=\"0\" title=\"\">";
											echo "<thead><th colspan=\"7\" class=\"year_level\">Subject Schedule</th></thead>";
											echo "<thead>";
												echo "<th class=\"Count\">No.</th>";
												echo "<th class=\"day\">Day</th>";
												echo "<th class=\"time\">From</th>";
												echo "<th class=\"time\">To</th>";
												echo "<th class=\"\">Room</th>";
												echo "<th class=\"\">Instructor</th>";
												echo "<th class=\"Actions\"></th>";
											echo "</thead>";

											//define the odd even tables
											$ctr = 0;
											if(isset($_SESSION['section']['schedules'][$subject_id])){
												if(sizeof($_SESSION['section']['schedules'][$subject_id]) > 0){
													foreach($_SESSION['section']['schedules'][$subject_id] as $k => $item){

															if (isset($item['action'])) echo $item['action'] . '<br/>';
															
															// Continue to next record if marked as for deletion
															if (isset($item['action']) && $item['action'] == 'delete') continue;

															$ctr++;
															if($ctr % 2 == 0){
																echo '<tr class="even"
																					data-schedule="' . $k . '"
																					data-day="' . $item['day'] . '"
																					data-from="' . $item['from'] . '"
																					data-to="' . $item['to'] . '"
																					data-instructor="' . $item['instructor'] . '"
																					data-room="' . $item['room'] . '"
																					data-capacity="' . $item['capacity'] . '"
																			>';
															} else {
																echo '<tr class="odd"
																					data-schedule="' . $k . '"
																					data-day="' . $item['day'] . '"
																					data-from="' . $item['from'] . '"
																					data-to="' . $item['to'] . '"
																					data-instructor="' . $item['instructor'] . '"
																					data-room="' . $item['room'] . '"
																					data-capacity="' . $item['capacity'] . '"
																			>';
															}
																echo "<td>{$ctr}</td>";
																echo "<td>{$school_days[$item['day']]->shorthand}</td>";
																//from
																echo "<td>";
																	foreach($school_times as $time){
																		if($time->time_id == $item['from']){
																			echo $time->description;
																			break;
																		}
																	}
																echo "</td>";
																echo "<td>";
																	foreach($school_times as $time){
																		if($time->time_id == $item['to']){
																			echo $time->description;
																			break;
																		}
																	}
																echo "</td>";

																if($item['room'] != null){
																	echo "<td>";
																		echo $rooms[$item['room']]->description . "  [<b>" . $rooms[$item['room']]->code . "</b>]";
																	echo "</td>";
																} else {
																	echo "<td>{$item['room']}</td>";
																}

																if($item['instructor'] != null){
																	if (isset($faculties[$item['instructor']])) {
																		echo "<td>";
																			echo $faculties[$item['instructor']]->employee->last_name . ", " . $faculties[$item['instructor']]->employee->first_name;
																			if(trim($faculties[$item['instructor']]->employee->middle_name) <> ''){
																				echo " ";
																				echo substr($faculties[$item['instructor']]->employee->middle_name,0,1) . ".";
																			}
																		echo "</td>";
																	}
																} else {
																	echo "<td>{$item['instructor']}</td>";
																}

																echo "<td class=\"Actions\">";
																	echo "<a class=\"edit\" href=\"schedules-section-set_schedule-process.php?action=edit&sid={$subject_id}&id={$k}\">Edit</a>";
																	echo " | ";
																	echo "<a class=\"delete\" href=\"schedules-section-set_schedule-process.php?action=delete&sid={$subject_id}&id={$k}\" onclick=\"return confirm('Delete this schedule for this subject? Click OK to continue.')\">Delete</a>";
																echo "</td>";
															echo "</tr>";
													}
												}
											}
											if($ctr == 0){
												echo "<tr class=\"odd\" class=\"reminder\"><td colspan=\"10\">No schedules defined for this subject. </td></tr>";
											}
										echo "</table>";

									echo "</div>";

									echo "<p class=\"margin-top: 20px;\">";
											echo "Select a <b>day</b>, <b>time</b> and <b>instructor</b> and click add schedule below to set a schedule for this subject.";
									echo "</p>";
							?>

									<input type="hidden" id="oScheduleId" name="schedule" value="-1" />
									<table class="form" cellspacing="0">
										<tr class="info">
											<td class="label">Instructor</td>
											<td class="input">:
												<select id="oInstructor" class="large mono" name="instructor">
													<option value="-1"></option>
													<?php
														foreach($faculties as $item){
															echo "<option value=\"{$item->faculty_id}\">";
																echo str_pad($item->college->code,10,".", STR_PAD_RIGHT);
																echo $item->employee->last_name . ", " . $item->employee->first_name;
																if(trim($item->employee->middle_name) <> ''){
																	echo " ";
																	echo substr($item->employee->middle_name,0,1) . ".";
																}
															echo "</option>";
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Room</td>
											<td class="input">:
												<select id="oRoom" class="large mono" name="room">
													<option value="-1"></option>
													<?php
														foreach($rooms as $item){
															echo "<option value=\"{$item->room_id}\">";
																echo str_pad($item->code, 10, ".", STR_PAD_RIGHT);
																echo str_pad($item->description, 30, ".", STR_PAD_RIGHT);
																echo $item->seating_capacity . " seats";
															echo "</option>";
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Day</td>
											<td class="input">:
												<select id="oDay" class="small mono" name="day">
													<option value="-1"></option>
													<?php
														foreach($school_days as $item){
															echo "<option value=\"{$item->day_id}\">";
																echo $item->description;
															echo "</option>";
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">From</td>
											<td class="input">:
												<select id="oFrom" class="small mono" name="from">
													<option value="-1"></option>
													<?php
														foreach($school_times as $item){
															echo "<option value=\"{$item->time_id}\">";
																echo $item->description;
															echo "</option>";
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">To</td>
											<td class="input">:
												<select id="oTo" class="small mono" name="to">
													<option value="-1"></option>
													<?php
														foreach($school_times as $item){
															echo "<option value=\"{$item->time_id}\">";
																echo $item->description;
															echo "</option>";
														}
													?>
												</select>
											</td>
										</tr>
									</table>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
											<td>
												<input type="button" class="button back" value="Back to Section" />
												<input type="submit" class="button add" name="add_schedule" value="Add Schedule" onclick="if(getSelectValue('oRoom') > -1 && getSelectValue('oInstructor') > -1 && getSelectValue('oDay') > -1 && getSelectValue('oFrom') > -1 && getSelectValue('oTo') > -1){ return true; } else { alert('Please input Room, Instructor, Day, From and To.'); return false; }"/>
												<input type="submit" style="display: none" class="button save" name="save_schedule" value="Save Schedule" onclick="if(getSelectValue('oRoom') > -1 && getSelectValue('oInstructor') > -1 && getSelectValue('oDay') > -1 && getSelectValue('oFrom') > -1 && getSelectValue('oTo') > -1){ return true; } else { alert('Please input Room, Instructor, Day, From and To.'); return false; }"/>
												<input type="button" style="display: none" class="button cancel" name="cancel_schedule" value="Cancel Edit Schedule"/>
												<?php //<input type="submit" class="button" name="college_save" value="Add" /> ?>
											</td>
										</tr>
									</table>
								</div>
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
