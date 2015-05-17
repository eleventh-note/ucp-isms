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
	
	if(isset($_GET['cid']) && isset($_GET['cud']) && isset($_GET['sy'])){
		$college_id = (int) $_GET['cid'];
		$course_id = (int) $_GET['cud'];
		$sy_id = (int) $_GET['sy'];
		
		//get selected college & course
		$colleges = $hnd_cg->GetColleges($college_id);
		$courses = $hnd_co->GetCourses(null, $course_id);
		$school_years = $hnd_sc->GetSchoolYears($sy_id);
		
		if(sizeof($colleges) > 0 && sizeof($courses) > 0 && sizeof($school_years) > 0){
			$college = $colleges[0];
			$course = $courses[0];
			$school_year = $school_years[0];
			
			$sems = $hnd_sc->GetSemesters();
			$levels = $hnd_co->GetYearLevels();
			
			//redirect if no courses found
			if(sizeof($sems) == 0){
				$_SESSION['error'] = array("There are no semesters defined.");
				header("Location: schoolplusplus-curriculums-select-sy.php?cid={$college_id}&cud={$course_id}");
				exit();
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
	
	$conn->Close();
		
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
	
	//# Otder Javascript Loaded Here
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
								<span class="Highlight">Curriculum Administration &raquo; Curriculum &raquo; Select Student Year &amp; Semester</span>
							</h1>
							<p class="">Select the year of the student and the semester:</p>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>
							<form action="colleges-process.php" method="post">
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
												<select id="oYear" class="small" name="year">
													<option value="-1"></option>
													<?php
														foreach($levels as $item){
															if($item->equivalent <= $course->max_year_level){
																echo "<option value=\"{$item->level_id}\">";
																	echo $item->description;
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Semester</td>
											<td class="input">: 
												<select id="oSemester" class="small" name="semester">
													<option value="-1"></option>
													<?php
														foreach($sems as $item){
															echo "<option value=\"{$item->semester_id}\">";
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
												<input type="button" class="button" value="Back" onclick="window.location='schoolplusplus-curriculums-select-sy.php?cid=<?php echo $college_id; ?>&cud=<?php echo $course_id; ?>';"/>
												<input type="button" class="button"  value="Next" onclick="if(getSelectValue('oSemester') > 0 && getSelectValue('oYear')){
												
												redirectTo('schoolplusplus-curriculums-select-subject.php?cid=<?php echo $college_id; ?>&cud=<?php echo $course_id; ?>&sy=<?php echo $sy_id; ?>&sem=', getSelectValue('oSemester') + '&yr=' + getSelectValue('oYear')); } else { alert('Please select a semester and a year.') }" />
												
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
?>