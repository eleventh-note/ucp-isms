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
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "stdnts.inc.php");
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
		$PagePrivileges->AddPrivilege("Student - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd = new EmployeeManager($conn);
	$std = new StudentManager($conn);
	$hnd_cu = new CourseManager($conn);
	$hnd_co = new CollegeManager($conn);
	$hnd_sc = new SchoolManager($conn);
	
	//Define Sorting Order
	$sort = 0;
	if(!isset($_GET['sort'])){
		$records = $std->GetSprsSort($sort);
	} else {
		$sort = (int) $_GET['sort'];
		$records = $std->GetSprsSort($sort);
	}

	$backgrounds = $std->GetCurrentAcademicBackgroundsByKey();
	$courses = $hnd_cu = $hnd_cu->GetCoursesByKey();
	$college_types = $hnd_co->GetCollegeTypesByKey();
	$student_statuses = $std->GetStatusesByKey();
	$enrollment_statuses = $std->GetEnrollmentStatusesByKey();
	$enrollment_statuses = $std->GetEnrollmentStatusesByKey();
	$semesters = $hnd_sc->GetSemestersByKey();
	$school_years = $hnd_sc->GetSchoolYearsByKey();
	
	$reg = new RtRegExp();
	
	$conn->Close();
	
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
	<body id="students">
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
								<span class="Highlight">Student Administration &raquo; Student Permanent Records</span> 
							</h1>
							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='students.php'" />
									<input type="button" value="Get Report" target="_new" onclick="window.open('students-spr-report.php?sort=<?php echo $sort; ?>')" />
								</p>
							</div>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
							?>
							<p class="">Below are the list of student permanent records.</p>
							<div id="sort_actions">
								<p class="sort">
									<input type="button" value="Latest First" onclick="window.location='students-spr.php'" />
									<input type="button" value="Sort by Application No" onclick="window.location='students-spr.php?sort=1'" />
									<input type="button" value="Sort by Last Name" onclick="window.location='students-spr.php?sort=2'" />
									<input type="button" value="Sort by Course Code" target="_new" onclick="window.location='students-spr.php?sort=3'" />
								</p>
							</div>
							<div class="table">
								<table class="employees" cellspacing="0" title="">
									<thead>
										<th class="Count">No.</th>
										<th class="application_number center">Student Number</th>
										<th class="student_name">Name</th>
										<th class="date_of_entry center">Course</th>
										<th class="employee_status">Mobile Number</th>
										<th class="employee_status">Student Status</th>
										<th class="employee_status">Enrollment Status</th>
										<?php //<th class="Actions"></th> ?>
									</thead>
									<?php 
										$ctr = 0;
										if(sizeof($records) > 0){
											foreach($records as $item){
												$ctr++;
												//define the odd even tables
												if($ctr % 2 == 0){
													echo "<tr class=\"even\" onclick=\"window.location='students-spr-view.php?id={$item->student_id}';\">";
												} else {
													echo "<tr class=\"odd\" onclick=\"window.location='students-spr-view.php?id={$item->student_id}';\">";
												}
													echo "<td>{$ctr}</td>";
													echo "<td class=\"center\">{$item->student_no}</td>";
													echo "<td>{$item->last_name}, {$item->first_name} " . substr($item->middle_name,0,1) . ".</td>";
													$course = $courses[$backgrounds[$item->student_id]->course];
													echo "<td>" . "[" . $course->code . "] " . $course->description . "</td>";
													echo "<td>" . $item->mobile_number . "</td>";
													echo "<td>" . $student_statuses[$backgrounds[$item->student_id]->student_status]->description . "</td>";
													echo "<td>" . $enrollment_statuses[$backgrounds[$item->student_id]->enrollment_status]->description . "</td>";
													$school_year = $school_years[$backgrounds[$item->student_id]->entry_sy];
													//echo "<td class=\"Actions\">";
														//echo "<a href=\"employment-employee-edit.php?id={$item->employee_id}\">Edit</a>";
														//echo " | ";
															//echo "<a href=\"employment-employee-process.php?id={$item->employee_id}&action=delete\" onclick=\"return confirm('Delete employee? Click OK to continue.')\">Delete</a>";
													//echo "</td>";
												echo "</tr>";
											}
										} else {
											echo "<td colspan=\"7\">There are no existing Student Permanent Records.</td>";
										}
									?>
									
								</table>
							</div>
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