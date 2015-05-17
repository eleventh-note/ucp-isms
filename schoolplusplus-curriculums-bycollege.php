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
	$hnd = new CollegeManager($conn);
	$hnd_c = new CourseManager($conn);
	$hnd_cu = new CurriculumManager($conn);
		
	if(isset($_GET['cid'])){
		$college_id = (int) $_GET['cid']; //selected from the selection of colleges to view curriculum	
		$colleges = $hnd->GetColleges($college_id);
				
		if(sizeof($colleges) > 0){
			$college = $colleges[0];
			$college_code = $college->code;

			$courses = $hnd_c->GetCourses($college_id);
			
			$course_list = array();
			$subject_list = array();
			
		} else {
		    //redirect if college selected is not found in the database
			$_SESSION['error'] = array("College not found. Select college below.");
			header("Location: schoolplusplus-curriculums.php");
			exit();
		}
	} else {
		//redirect if no college selected
		$_SESSION['error'] = array("College not found. Select college below.");
		header("Location: schoolplusplus-curriculums.php");
		exit();
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
	
	//Replace Timer Below with script for javascript logout`
	//###TIMER###
?>
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
								<span class="Highlight">Curriculum Administration &raquo; Curricula &raquo; <?php echo $college_code; ?></span>
							</h1>
							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='schoolplusplus-curriculums.php'" /> 
									<input type="button" value="New Curriculum" onclick="window.location='schoolplusplus-curriculums-select-course.php?cid=<?php echo $college->college_id; ?>'" title="Add new curriculum to <?php echo $college_code; ?>"/></p>
							</div>
							<p class="">Select a curriculum below to show details.</p>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>
							
									<?php 
										if(sizeof($courses) > 0){
											foreach($courses as $course){
												echo "<div class=\"table\">";
												echo "<table class=\"colleges\" cellspacing=\"0\" title=\"\">";
													$curriculums = $hnd_cu->GetCurriculums($course->course_id);
													echo "<thead><th colspan=\"2\" class=\"course_name\">{$course->description}</th></thead>";
													echo "<thead>";
														echo "<th class=\"Count\">No.</th>";
														echo "<th class=\"college\">Curriculum</th>";
													echo "</thead>";
													
													$ctr = 0;
													if(sizeof($curriculums) > 0){
														foreach($curriculums as $curriculum){
															$ctr++;
															
															//define the odd even tables
															if($ctr % 2 == 0){
																echo "<tr class=\"even\" onclick=\"window.location='schoolplusplus-curriculums-view.php?cid={$course->college}&cud={$course->course_id}&sy={$curriculum->school_year_id}';\">";
															} else {
																echo "<tr class=\"odd\" onclick=\"window.location='schoolplusplus-curriculums-view.php?cid={$course->college}&cud={$course->course_id}&sy={$curriculum->school_year_id}';\">";
															}
																echo "<td>{$ctr}</td>";
																echo "<td>SY {$curriculum->school_year}</td>";
																//echo "<td class=\"Actions\">";
																	//echo "<a href=\"employment-employee-edit.php?id={$item->employee_id}\">Edit</a>";
																	//echo " | ";
																	//echo "<a href=\"employment-employee-process.php?id={$item->employee_id}&action=delete\" onclick=\"return confirm('Delete employee? Click OK to continue.')\">Delete</a>";
																//echo "</td>";
															echo "</tr>";
														}
													} else {
														echo "<tr>";
														echo "<td colspan=\"2\">There are no existing curriculums for this course.</td>";
														echo "</tr>";
													}
												echo "</table>";
												echo "</div>";
											}
										} else {
											echo "<div class=\"table\">";
											echo "<table class=\"colleges\" cellspacing=\"0\" title=\"\">";
											echo "<td colspan=\"2\">There are no existing courses registered to this college.</td>";
											echo "</table>";
											echo "</div>";
										}
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
	$conn->Close();
?>