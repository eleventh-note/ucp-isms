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
	$hnd_cg = new CollegeManager($conn);
	$hnd_co = new CourseManager($conn);
	$hnd_sc = new SchoolManager($conn);
	$hnd_su = new SubjectManager($conn);
	$hnd_cu = new CurriculumManager($conn);

	if(isset($_GET['cid']) && isset($_GET['cud']) && isset($_GET['sy']) && isset($_GET['sem']) && isset($_GET['yr'])){
		$college_id = (int) $_GET['cid'];
		$course_id = (int) $_GET['cud'];
		$sy_id = (int) $_GET['sy'];
		$sem_id = (int) $_GET['sem'];
		$yr_id = (int) $_GET['yr'];

		//get selected college & course
		$colleges = $hnd_cg->GetColleges($college_id);
		$courses = $hnd_co->GetCourses(null, $course_id);
		$school_years = $hnd_sc->GetSchoolYears($sy_id);
		$semesters = $hnd_sc->GetSemesters($sem_id);
		$levels = $hnd_co->GetYearLevels($yr_id);

		if(sizeof($colleges) > 0 && sizeof($courses) > 0 && sizeof($school_years) > 0 && sizeof($semesters) > 0 && sizeof($levels) > 0){
			$college = $colleges[0];
			$course = $courses[0];
			$school_year = $school_years[0];
			$semester = $semesters[0];
			$level = $levels[0];

			$subjects = $hnd_su->GetSubjectsByCode();

			//redirect if no courses found
			if(sizeof($subjects) == 0){
				$_SESSION['error'] = array("There are no subjects available.");
				header("Location: schoolplusplus-curriculums-select-sy.php?cid={$college_id}&cud={$course_id}");
				exit();
			} else {
				//##Get Subject Records
				$curriculum_id = $hnd_cu->VerifyCurriculum($course_id, $sy_id);

				//##Prepare data needed for displaying
				if($curriculum_id != -1){
					$c_subjects = $hnd_cu->GetSubjectsByCodeForPrerequisite($curriculum_id, $level->equivalent, $semester->semester_id);
					$r_subjects = $hnd_cu->GetSubjectsByCode($curriculum_id); //registered subjects
					$co_subjects = $hnd_cu->GetSubjectsByCodeForCorequisite($curriculum_id, $level->equivalent, $semester->semester_id);

					//var_dump($c_subjects);
					$max_level = $course->max_year_level;
					$levels = $hnd_co->GetYearLevels();
					$semesters = $hnd_sc->GetSemesters();

					//remove already registered subjects
					foreach($r_subjects as $r_item){
						foreach($subjects as $k => $item){
							if($item->subject_id == $r_item->subject_id){
								unset($subjects[$k]);
							}
						}
					}

				}

			}

		} else {
			$_SESSION['error'] = array("College, Course or School Year not selected.");
			header("Location: schoolplusplus-curriculums-select-college.php");
			exit();
		}

	} else {
		$_SESSION['error'] = array("College, Course or School Year not selected.");
		header("Location: schoolplusplus-curriculums-select-college.php");
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
	<body id="school">
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
								<span class="Highlight">Curriculum Administration &raquo; Curriculum &raquo; Assign Subject</span>
							</h1>
							<p class="">
								Select subjects for <b><?php echo $course->code; ?> SY <?php echo $school_year->start . " - " . $school_year->end; ?></b>.
								<?php
									if(isset($c_subjects)){
										if(sizeof($c_subjects) > 0){
											echo "To view subjects already added, <a class=\"scroll\" href=\"#list_of_subjects\">Click Here</a>";
										}
									}
								?>
							</p>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>
							<form action="schoolplusplus-curriculums-process.php" method="post">
								<input type="hidden" name="college" value="<?php echo $college_id; ?>" />
								<input type="hidden" name="course" value="<?php echo $course_id; ?>" />
								<input type="hidden" name="sy" value="<?php echo $sy_id; ?>" />
								<input type="hidden" name="sem" value="<?php echo $sem_id; ?>" />
								<input type="hidden" name="year" value="<?php echo $yr_id; ?>" />

								<hr class="form_top"/>
								<div class="table_form">
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
											<td class="label">School Year</td>
											<td class="input">:
												<?php echo "SY " . $school_year->start . " - " . $school_year->end; ?>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Year of Student</td>
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
											<td class="label">Subject</td>
											<td class="input">:
												<select id="oSubject" class="extra-large mono" name="subject">
													<option value="-1"></option>
													<?php
														foreach($subjects as $item){
															echo "<option value=\"{$item->subject_id}\">";
																echo str_pad("{$item->code}", 15, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->description}", 50, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->units}", 5, ".", STR_PAD_LEFT) . " lec unit(s)";
																echo str_pad("{$item->unitsLab}", 5, ".", STR_PAD_LEFT) . " lab unit(s)";
															echo "</option>";
														}
													?>
												</select>
											</td>
										</tr>
										<?php if(isset($c_subjects)){ if(sizeof($c_subjects) > 0){ ?>
											<tr class="info">
												<td class="label">Pre-Requisite</td>
												<td class="input">:
													<select class="extra-large mono" name="prerequisite[]">
														<option value="-1"></option>
														<?php
															foreach($c_subjects as $item){
																echo "<option value=\"{$item->curriculum_subject_id}\">";
																	echo str_pad("{$item->code}", 15, ".", STR_PAD_RIGHT);
																	echo str_pad("{$item->subject}", 50, ".", STR_PAD_RIGHT);
																	echo str_pad("{$item->units}", 5, ".", STR_PAD_LEFT) . " lec unit(s)";
																echo str_pad("{$item->unitsLab}", 5, ".", STR_PAD_LEFT) . " lab unit(s)";
																echo "</option>";
															}
														?>
													</select>
													<br/>&nbsp; <select class="extra-large mono" name="prerequisite[]">
														<option value="-1"></option>
														<?php
															foreach($c_subjects as $item){
																echo "<option value=\"{$item->curriculum_subject_id}\">";
																	echo str_pad("{$item->code}", 15, ".", STR_PAD_RIGHT);
																	echo str_pad("{$item->subject}", 50, ".", STR_PAD_RIGHT);
																	echo str_pad("{$item->units}", 5, ".", STR_PAD_LEFT) . " lec unit(s)";
																	echo str_pad("{$item->unitsLab}", 5, ".", STR_PAD_LEFT) . " lab unit(s)";
																echo "</option>";
															}
														?>
													</select>
													<br/>&nbsp; <select class="extra-large mono" name="prerequisite[]">
														<option value="-1"></option>
														<?php
															foreach($c_subjects as $item){
																echo "<option value=\"{$item->curriculum_subject_id}\">";
																	echo str_pad("{$item->code}", 15, ".", STR_PAD_RIGHT);
																	echo str_pad("{$item->subject}", 50, ".", STR_PAD_RIGHT);
																	echo str_pad("{$item->units}", 5, ".", STR_PAD_LEFT) . " lec unit(s)";
																	echo str_pad("{$item->unitsLab}", 5, ".", STR_PAD_LEFT) . " lab unit(s)";
																echo "</option>";
															}
														?>
													</select>
													<br/>&nbsp; <select class="extra-large mono" name="prerequisite[]">
														<option value="-1"></option>
														<?php
															foreach($c_subjects as $item){
																echo "<option value=\"{$item->curriculum_subject_id}\">";
																	echo str_pad("{$item->code}", 15, ".", STR_PAD_RIGHT);
																	echo str_pad("{$item->subject}", 50, ".", STR_PAD_RIGHT);
																	echo str_pad("{$item->units}", 5, ".", STR_PAD_LEFT) . " lec unit(s)";
																	echo str_pad("{$item->unitsLab}", 5, ".", STR_PAD_LEFT) . " lab unit(s)";
																echo "</option>";
															}
														?>
													</select>
												</td>
											</tr>
										<?php }} ?>
										<?php if(isset($co_subjects)){ if(sizeof($co_subjects) > 0){ ?>
										<tr class="info">
											<td class="label">Co-Requisite</td>
											<td class="input">:
												<select class="extra-large mono" name="corequisite[]">
													<option value="-1"></option>
													<?php
														foreach($co_subjects as $item){
															echo "<option value=\"{$item->curriculum_subject_id}\">";
																echo str_pad("{$item->code}", 15, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->subject}", 50, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->units}", 5, ".", STR_PAD_LEFT) . " lec unit(s)";
																echo str_pad("{$item->unitsLab}", 5, ".", STR_PAD_LEFT) . " lab unit(s)";
															echo "</option>";
														}
													?>
												</select>
												<br/>&nbsp; <select class="extra-large mono" name="corequisite[]">
													<option value="-1"></option>
													<?php
														foreach($co_subjects as $item){
															echo "<option value=\"{$item->curriculum_subject_id}\">";
																echo str_pad("{$item->code}", 15, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->subject}", 50, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->units}", 5, ".", STR_PAD_LEFT) . " lec unit(s)";
																echo str_pad("{$item->unitsLab}", 5, ".", STR_PAD_LEFT) . " lab unit(s)";
															echo "</option>";
														}
													?>
												</select>
												<br/>&nbsp; <select class="extra-large mono" name="corequisite[]">
													<option value="-1"></option>
													<?php
														foreach($co_subjects as $item){
															echo "<option value=\"{$item->curriculum_subject_id}\">";
																echo str_pad("{$item->code}", 15, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->subject}", 50, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->units}", 5, ".", STR_PAD_LEFT) . " lec unit(s)";
																echo str_pad("{$item->unitsLab}", 5, ".", STR_PAD_LEFT) . " lab unit(s)";
															echo "</option>";
														}
													?>
												</select>
												<br/>&nbsp; <select class="extra-large mono" name="corequisite[]">
													<option value="-1"></option>
													<?php
														foreach($co_subjects as $item){
															echo "<option value=\"{$item->curriculum_subject_id}\">";
																echo str_pad("{$item->code}", 15, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->subject}", 50, ".", STR_PAD_RIGHT);
																echo str_pad("{$item->units}", 5, ".", STR_PAD_LEFT) . " lec unit(s)";
																echo str_pad("{$item->unitsLab}", 5, ".", STR_PAD_LEFT) . " lab unit(s)";
															echo "</option>";
														}
													?>
												</select>
											</td>
										</tr>
										<?php }} ?>
									</table>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
											<td>
												<input type="button" class="button" value="Back" onclick="window.location='schoolplusplus-curriculums-select-semyear.php?cid=<?php echo $college_id; ?>&cud=<?php echo $course_id; ?>&sy=<?php echo $sy_id; ?>';"/>
												<input type="submit" class="button" name="curriculum_save" value="Add to Curriculum" onclick="if(getSelectValue('oSubject') > -1){ return true; } else { alert('Please select a subject.'); return false; }"/>



												<?php //<input type="submit" class="button" name="college_save" value="Add" /> ?>
											</td>
										</tr>
									</table>
								</div>
							</form>

							<?php
								if(isset($max_level)){
									echo "<h1>";
										echo "<span class=\"Highlight\">List of Subjects for {$course->code} SY {$school_year->start} - {$school_year->end}</span>";
									echo "</h1>";

									echo "<a id=\"list_of_subjects\"></a>";
									echo "<div class=\"table\">";

											for($i = 0; $i < $max_level; $i++){
												foreach($semesters as $s_item){
													echo "<table class=\"curriculum_subjects\" style=\"margin-top:30px;\" cellspacing=\"0\" title=\"\">";
														echo "<thead><th colspan=\"8\" class=\"year_level\">{$levels[$i]->description} - {$s_item->description}</th></thead>";
														echo "<thead>";
															echo "<th class=\"Count\">No.</th>";
															echo "<th class=\"code\">Code</th>";
															echo "<th class=\"description\">Description</th>";
															echo "<th class=\"code\">Lec Units</th>";
															echo "<th class=\"code\">Lab Units</th>";
															echo "<th class=\"prerequisite\">Pre-Requisite</th>";
															echo "<th class=\"corequisite\">Co-Requisite</th>";
															echo "<th class=\"Actions\"></th>";
														echo "</thead>";

														//define the odd even tables
														$ctr = 0;
														$total_units = 0;
														$total_unitsLab = 0;
														if(sizeof($r_subjects) > 0){
															foreach($r_subjects as $item){
																if($item->semester == $s_item->semester_id && $item->year_level == $levels[$i]->equivalent){
																	$ctr++;
																	if($ctr % 2 == 0){
																		echo "<tr class=\"even\">"; // onclick=\"window.location='schoolplusplus-curriculums-bycollege.php?id={$item->subject_id}';\">";
																	} else {
																		echo "<tr class=\"odd\">"; // onclick=\"window.location='schoolplusplus-curriculums-bycollege.php?id={$item->subject_id}';\">";
																	}
																		echo "<td>{$ctr}</td>";
																		echo "<td>{$item->code}</td>";
																		echo "<td>{$item->subject}</td>";
																		echo "<td>{$item->units}</td>";
																		echo "<td>{$item->unitsLab}</td>";
																		$total_units += $item->units;
																		$total_unitsLab += $item->unitsLab;
																		//#######################################
																		//	GET PRE-REQUISITES
																		//#######################################
																		$prerequisites = $hnd_cu->GetPrerequisitesByCode($item->curriculum_subject_id);
																		//var_dump($prerequisites);
																		$corequisites = $hnd_cu->GetCorequisitesByCode($item->curriculum_subject_id);
																		echo "<td>";
																		if(sizeof($prerequisites) > 0){
																			$data = "";
																			//get first data then delete
																			$is_first = true;
																			//get display data
																			foreach($prerequisites as $pre){
																				if($is_first == true){
																					$data .= $pre->code;
																					$is_first = false;
																				} else {
																					$data .= ", " . $pre->code;
																				}
																			}
																			echo $data;
																		}
																		echo "</td>";
																		echo "<td>";
																		if(sizeof($corequisites) > 0){
																			$data = "";
																			//get first data then delete
																			$is_first = true;
																			//get display data
																			foreach($corequisites as $co){
																				if($is_first == true){
																					$data .= $co->code;
																					$is_first = false;
																				} else {
																					$data .= ", " . $co->code;
																				}
																			}
																			echo $data;
																		}
																		echo "</td>";
																		echo "<td class=\"Actions\">";
																			echo "<a href=\"schoolplusplus-curriculums-process.php?cid={$college_id}&cud={$course_id}&sy={$sy_id}&sem={$sem_id}&yr={$yr_id}&action=delete&id={$item->curriculum_subject_id}\" onclick=\"return confirm('Delete subject from curriculum? Click OK to continue.')\">Delete</a>";
																		echo "</td>";
																	echo "</tr>";
																}
															}
														}
														if($ctr == 0){
															echo "<tr class=\"odd\" class=\"reminder\"><td colspan=\"8\">No subjects assigned. </td></tr>";
														}
														echo "<thead><th colspan=\"3\" style=\"text-align: right;\">Total Units:</th><th colspan=\"1\">{$total_units}</th><th colspan=\"1\">{$total_unitsLab}</th></thead>";
													echo "</table>";
												}

											}

									echo "</div>";
								} //end of check isset
							?>

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
