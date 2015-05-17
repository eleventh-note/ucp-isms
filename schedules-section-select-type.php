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
	require_once(CLASSLIST . "schdls.inc.php");
	
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

	if(isset($_GET['cid']) && isset($_GET['cud']) && isset($_GET['cur']) && isset($_GET['sem']) && isset($_GET['yr']) && isset($_GET['sy'])){
		$college_id = (int) $_GET['cid'];
		$course_id = (int) $_GET['cud'];
		$curriculum_id = (int) $_GET['cur'];
		$sem_id = (int) $_GET['sem'];
		$sy_id = (int) $_GET['sy'];
		$level_id = (int) $_GET['yr'];
		
		//set the details of the section
		unset($_SESSION['section']);
		
		$_SESSION['section']['college_id'] = $college_id;
		$_SESSION['section']['course_id'] = $course_id;
		$_SESSION['section']['curriculum_id'] = $curriculum_id;
		$_SESSION['section']['sem_id'] = $sem_id;
		$_SESSION['section']['sy_id'] = $sy_id;
		$_SESSION['section']['level_id'] = $level_id;
		$_SESSION['section']['mode'] = 'add';
		
		//get selected college & course
		$colleges = $hnd_cg->GetColleges($college_id);
		$courses = $hnd_co->GetCourses(null, $course_id);
		$curriculums = $hnd_cu->GetCurriculums($course_id, $curriculum_id);
		$semesters = $hnd_sc->GetSemesters($sem_id);
		$school_years = $hnd_sc->GetSchoolYears($sy_id);
		$levels = $hnd_co->GetYearLevels($level_id);
					
		if(sizeof($colleges) > 0 && sizeof($courses) > 0 && sizeof($school_years) > 0 && sizeof($semesters) > 0 && sizeof($curriculums) > 0){
			$college = $colleges[0];
			$course = $courses[0];
			$school_year = $school_years[0];
			$semester = $semesters[0];
			$level = $levels[0];
			
			foreach($curriculums as $item){
				$curriculum = $item;
			}
							
			$types = $hnd_sh->GetSectionTypes();
						
			//redirect if no courses found
			if(sizeof($types) == 0){
				$_SESSION['error'] = array("There are no subjects available.");
				header("Location: schedules-section-select-semyear.php?cid={$college_id}&cud={$course_id}");
				exit();
			} else {			
				//##Prepare data needed for displaying
				if($curriculum_id != -1){
					
				}				
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
			
			function getValue(target){
				target = document.getElementById(target);
				return target.value;
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
								<span class="Highlight">Section &amp; Schedule Administration &raquo; Sections &raquo; Select Section Type</span>
							</h1>
							<p class="">
								Select section type.
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
							<form action="schedules-section-subjects.php" method="post">
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
												<input type="text" id="oSection" name="section_name" maxlength="20" />
											</td>
										</tr>
										<tr class="info">
											<td class="label">Type</td>
											<td class="input">: 
												<select id="oType" class="small" name="type">
													<option value="-1"></option>
													<?php
														foreach($types as $item){
															echo "<option value=\"{$item->type_id}\">";
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
												<input type="button" class="button" value="Back" onclick="window.location='schedules-section-select-semyear.php?cid=<?php echo $college_id; ?>&cud=<?php echo $course_id; ?>&cur=<?php echo $curriculum_id; ?>';"/>
												<input type="submit" class="button" value="Create Section" onclick="if(getSelectValue('oType') > -1 && getValue('oSection') != ''){ return true; } else { alert('Please input a name for the section and select a section type.'); return false; }"/>
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