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
		$PagePrivileges->AddPrivilege("Reports - Administrator");
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

	//# ERASE SESSION on sections
	if(isset($_SESSION['section'])){
		unset($_SESSION['section']);
	}

	//Dictionaries
	$dict_colleges = $hnd_cg->GetCollegesByKey();
	$dict_semesters = $hnd_sc->GetSemestersByKey();
	$dict_levels = $hnd_co->GetYearLevelsByKey();

	//Get Initial Sections without filter
	$sections = $hnd_sh->GetSectionsByKey(null, $sy[0]->year_id, $sem[0]->semester_id);

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

	//# Otder Javascript Loaded Here
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_PLUGINS . "jquery.mini.js" . "\"></script>";
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_DEFAULT . "general.js" . "\"></script>";

	//Replace Timer Below witd script for javascript logout`
	//###TIMER###
?>
	</head>
	<body id="reports">
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
								<span class="Highlight">Student Count per Subject per Section</span>
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
							<p>
								Sort report by:
								<select id="sort">
									<option value="1">Subject Code</option>
									<option value="2">Subject Description</option>
									<option value="3">Section</option>
								</select>
								then <input type="button" id="generate-report" value="Generate Report" />
							</p>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div><?php //end of body ?>
			<div id="footer">
				<?php require_once("_system/main/footer.inc.php"); ?>
			</div><?php //end of footer ?>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$('#generate-report').bind('click', function() {
					var sort = $('#sort').val();
					window.open('reports-student_count_per_subject_section-pdf.php?sort=' + sort);
				});
			});
		</script>
	</body>
</html>
<?php
	//::START OF 'CLOSING REMARKS'
		//memory releasing and stuffs
	//::END OF 'CLOSING REMARKS'
	$conn->Close();
?>
