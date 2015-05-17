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
	require_once(CLASSLIST . "dvsns.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "schdls.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
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
		$PagePrivileges->AddPrivilege("Enlistment - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	//# HANDLERS
	$hnd_cg = new CollegeManager($conn);
	$hnd_co = new CourseManager($conn);
	$hnd_sh = new ScheduleManager($conn);
	$hnd_sc = new SchoolManager($conn);
	$hnd_enl = new EnlistmentManager($conn);
	
	//# OPTION IS INITIALLY SHOWN
	$sy = $hnd_sc->GetActiveSchoolYear();
	$sem = $hnd_sc->GetActiveSemester();
	
	$colleges = $hnd_cg->GetColleges();
	
	//REMOVE ALL POST 
	if(isset($_POST['reset'])){ 
		unset($_POST['college']); 
		unset($_POST['course']); 
		unset($_POST['semester']); 
		unset($_POST['level']); 
	}
	
	//# ERASE SESSION enlistment
	if(isset($_SESSION['enlisted_subjects'])){
		unset($_SESSION['enlisted_subjects']);
	}
	
	//Dictionaries
	$dict_colleges = $hnd_cg->GetCollegesByKey();
	$dict_semesters = $hnd_sc->GetSemestersByKey();
	$dict_levels = $hnd_co->GetYearLevelsByKey();
	
	//Get Initial Enlisted Students w/o Filter
	if(isset($_GET['sort'])){
		$students = $hnd_enl->GetStudentEnlistmentDetailsSort($sy[0]->year_id, $sem[0]->semester_id, $_GET['sort']);
	} else {
		$students = $hnd_enl->GetStudentEnlistmentDetails($sy[0]->year_id, $sem[0]->semester_id);
	}

	if(isset($_POST['college'])){
		$college_id = (int) $_POST['college'];
		if($college_id > 0){
			$colleges = $hnd_cg->GetColleges($college_id);
			if(sizeof($colleges) > 0){
				$college = $colleges[0];
				$courses = $hnd_co->GetCourses($college->college_id);
				$sections = $hnd_sh->GetSectionsByCollege($college_id, $sy[0]->year_id, $sem[0]->semester_id);
			}			
			
		} else {
			unset($_POST['college']);
		}
	}
	
	if(isset($_POST['course'])){
		$course_id = (int) $_POST['course'];
		if($course_id > 0){
			$courses = $hnd_co->GetCourses($college_id,$course_id);
			if(sizeof($courses) > 0){
				$course = $courses[0];
				$levels = $hnd_co->GetYearLevels();
				$sections = $hnd_sh->GetSectionsByCourse($course_id, $sy[0]->year_id, $sem[0]->semester_id);
			}			
		} else {
			unset($_POST['course']);
		}
	}
	
	// if(isset($_POST['semester'])){
		// $semester_id = (int) $_POST['semester'];
		// if($semester_id > 0){
			// $semesters = $hnd_sc->GetSemesters($semester_id);
			// if(sizeof($semesters) > 0){
				// $semester = $semesters[0];
				// $levels = $hnd_co->GetYearLevels();
			// }			
		// } else {
			// unset($_POST['semester']);
		// }
	// }	
	
	if(isset($_POST['level'])){
		$level_id = (int) $_POST['level'];
		if($level_id > 0){
			$levels = $hnd_co->GetYearLevels($level_id);
			if(sizeof($levels) > 0){
				$level = $levels[0];
				$levels = $hnd_co->GetYearLevels();
				$sections = $hnd_sh->GetSectionsByCourse($course_id, $sy[0]->year_id, $sem[0]->semester_id, $level_id);
			}			
		} else {
			unset($_POST['level']);
		}
	}	
	
	//##### PROCESS ERROR or SUCCESS
	if(isset($_SESSION['success'])){
		$success = $_SESSION['success'];
		unset($_SESSION['success']);
	}
	
	if(isset($_SESSION['error'])){
		$error = $_SESSION['error'];
		unset($_SESSION['error']);
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
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_DEFAULT . "general.js" . "\"></script>";
	
	//Replace Timer Below witd script for javascript logout`
	//###TIMER###
?>
	</head>
	<body id="enlistment">
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
								<span class="Highlight">Student Enlistment</span> 
								<?php
									//##### PASS ERROR IF FOUND
									if(isset($success)){
										echo Sentry::ShowStatus('success',$success);
									}
									if(isset($error)){
										echo Sentry::ShowStatus('error',$error);
									}
								?>
							</h1>
							
							<p class=""><b></b></p>
							<div id="actions">
								<p class="action">
									<input type="button" value="Enlist a Student" onclick="window.location='enlistment-search-student.php'" />
									<?php //<input type="button" value="View All Enlisted Students" onclick="window.location='schedules-section.php'" /> ?>
								</p>
								<div id="sort_actions">
									<p class="sort">
										<input type="button" value="Enlistments Today" onclick="window.location='enlistment.php?sort=today'" />
										<input type="button" value="Current SY/SEM Enlistments" onclick="window.location='enlistment.php'" />
										<input type="button" value="By Last Name" onclick="window.location='enlistment.php?sort=alphabetical_sort'" />
									</p>
								</div>
								<?php /*
								<p class="action"><input type="button" value="View Active Sections" onclick="window.location='schedules-active_section.php'" /> - view sections currently being served with their respective subjects.</p>
								*/ ?>
							</div>
							<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
								<div class="table_form">
									<!--
									<p>Use the drop-down below to filter students.</p>
									<table class="form" cellspacing="0">
										<tr class="info">
											<tr class="info">
												<td class="label">College</td>
												<td class="input">: 
													<?php
														if(!isset($_POST['college'])){
															echo "<select id=\"oCollege\"  class=\"large\"  name=\"college\" >";
																echo "<option value=\"-1\" ></option>";
																foreach($colleges as $item){
																	echo "<option value=\"{$item->college_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															echo "</select>";
														} else {
															if(isset($college)){
																//include hidden
																echo "<input type=\"hidden\" name=\"college\" value=\"{$college_id}\" />";
																//show detail
																echo $college->description;
															}
														}
													?>
												</td>
											</tr>
										</tr>
										<?php
											//if college is inputted
											if(isset($college) && !isset($_POST['course'])){
												echo "<tr class=\"info\" >";
													echo "<td class=\"label\" >Course</td>";
													echo "<td class=\"input\" >: ";
														echo "<select id=\"oCourse\"  class=\"large\"  name=\"course\" >";
															echo "<option value=\"-1\" ></option>";
															foreach($courses as $item){
																echo "<option value=\"{$item->course_id}\">";
																	echo $item->description;
																echo "</option>";
															}
														echo "</select>";
													echo "</td>";
												echo "</tr>";
											} elseif (isset($college) && isset($_POST['course'])){
												echo "<tr class=\"info\" >";
													echo "<td class=\"label\" >Course</td>";
													echo "<td class=\"input\" >: ";
														echo "<input type=\"hidden\" name=\"course\" value=\"{$course_id}\" />";
														echo $course->description;
													echo "</td>";
												echo "</tr>";
											}
										?>
										<?php
											//if college is inputted
											if(isset($college) && isset($course) && !isset($_POST['level'])){
												echo "<tr class=\"info\" >";
													echo "<td class=\"label\" >Year Level</td>";
													echo "<td class=\"input\" >: ";
														echo "<select id=\"oYearLevel\"  class=\"large\"  name=\"level\" >";
															echo "<option value=\"-1\" ></option>";
															foreach($levels as $item){
																echo "<option value=\"{$item->level_id}\">";
																	echo $item->description;
																echo "</option>";
															}
														echo "</select>";
													echo "</td>";
												echo "</tr>";
											} elseif (isset($college) && isset($course) && isset($_POST['level'])){
												echo "<tr class=\"info\" >";
													echo "<td class=\"label\" >Year Level</td>";
													echo "<td class=\"input\" >: ";
														echo "<input type=\"hidden\" name=\"level\" value=\"{$level_id}\" />";
														echo $level->description;
													echo "</td>";
												echo "</tr>";
											}
										?>
										<?php
											// //if college is inputted
											// if(isset($college) && isset($course) && isset($semester) && !isset($_POST['level'])){
												// echo "<tr class=\"info\" >";
													// echo "<td class=\"label\" >Year Level</td>";
													// echo "<td class=\"input\" >: ";
														// echo "<select id=\"oLevel\"  class=\"large\"  name=\"level\" >";
															// echo "<option value=\"-1\" ></option>";
															// foreach($levels as $item){
																// if($item->equivalent <= $course->max_year_level){
																	// echo "<option value=\"{$item->level_id}\">";
																		// echo $item->description;
																	// echo "</option>";
																// }
															// }
														// echo "</select>";
													// echo "</td>";
												// echo "</tr>";
											// } elseif (isset($college) && isset($course) && isset($semester) && isset($_POST['level'])){
												// echo "<tr class=\"info\" >";
													// echo "<td class=\"label\" >Year Level</td>";
													// echo "<td class=\"input\" >: ";
														// echo "<input type=\"hidden\" name=\"level\" value=\"{$level_id}\" />";
														// echo $level->description;
													// echo "</td>";
												// echo "</tr>";
											// }
										?>
									</table>	
									<table class="form" cellspacing="0" style="margin-top: 20px; ">
											<td>
												<input type="submit" name="reset" class="button" value="Reset"/>
												<input type="submit" name="search" class="button" value="Get Sections"/>
											</td>
										</tr>
									</table>
									-->
							<?php 
									echo "<a id=\"list_of_subjects\"></a>";
									echo "<p class=\"margin-top: 20px;\">";
									echo "</p>";
									echo "<div class=\"table\">";
										echo "<table class=\"curriculum_subjects default\" style=\"margin-top:10px;\" cellspacing=\"0\" title=\"\">";
											echo "<thead><th colspan=\"10\" class=\"year_level\">Enlisted Students for ";
											if(isset($sy)){ 
												if(sizeof($sy) > 0){
													echo "[SY " . $sy[0]->start . " - " . $sy[0]->end . "]"; 
												}
											} 
											
											if(isset($sem)){
												if(sizeof($sem) > 0){
													echo " [" . $sem[0]->description . "]";
												}
											}
											echo "</th></thead>";
											echo "<thead>";
												echo "<th class=\"Count\">No.</th>";
												echo "<th class=\"code\">Name</th>";
												echo "<th class=\"description\">Course</th>";
												echo "<th class=\"units\">College</th>";
												echo "<th class=\"units center\">Officially Enrolled</th>";
												//echo "<th class=\"Actions\"></th>";
											echo "</thead>";
											$ctr = 0;
											
											if(isset($students)){
												foreach($students as $item){
													//# Get Information for processing
													$ctr++;
													
														//define the odd even tables
														if($ctr % 2 == 0){
															echo "<tr class=\"even\" title=\"Edit/View enlistment details for [{$item->name}]\" onclick=\"window.location='enlistment-view.php?id={$item->student_id}';\">";
														} else {
															echo "<tr class=\"odd\" title=\"Edit/View enlistment details for [{$item->name}]\" onclick=\"window.location='enlistment-view.php?id={$item->student_id}';\">";
														}
														
														echo "<td>{$ctr}</td>";
														//Name
														echo "<td>{$item->name}</td>";
														//Course
														echo "<td>{$item->course}</td>";
														//College
														echo "<td>";
															echo $item->college;
														echo "</td>";
														
														$paid = false;
														if($item->enrolled == 1){
															$paid = true;
														}
														//# Officially Enrolled
														echo "<td class=\"center\">";
															echo ($paid==false)? "YES": "NO";
														echo "</td>";
												}			
											}
											if($ctr == 0){
												echo "<tr><td colspan=\"6\">There are no enlisted students.</td></tr>";
											}
										echo "</table>";
									echo "</div>";

							?>									
									<hr class="form_top"/>
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
	$conn->Close();
?>