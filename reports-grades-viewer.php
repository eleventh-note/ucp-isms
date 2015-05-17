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
	require_once(CLASSLIST . "grds.inc.php");
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
		$PagePrivileges->AddPrivilege("Grades - Administrator");
		$PagePrivileges->AddPrivilege("Grades - Viewer");
		$PagePrivileges->AddPrivilege("Grades - Encoder");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}

	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();

	$hnd = new GradesManager($conn);
	$hnd_sc = new SchoolManager($conn);

	if(isset($_GET['id'])){
		$studentId = (int) $_GET['id'];
	} else {
		header("Location: reports-grades-viewer-search.php");
		exit();
	}

	$students = $hnd->getStudentSubjectsWithGrades($studentId);
	$school_years = $hnd->getSchoolYearsWithGrades($studentId);
	$semesters = $hnd->getSemesterWithGrades($studentId);

	if(sizeof($students) == 0){
		$_SESSION['error'][] = "Grades are still not available for the selected student in the current semester.";
		header("Location: reports-grades-viewer-search.php");
		exit();
	}

	$studentName = $students[0]['studentName'];
	$studentNo = $students[0]['StudentNo'];
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
								<span class="Highlight">Reports &raquo; Grades Viewer &raquo; Search Student</span>
							</h1>
							<form action="grades-encode-process.php" method="post" >
							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='grades-viewer-search.php'" />
									<input type="button" value="Get PDF" onclick="window.open('grades-viewer-pdf.php?id=<?php echo $studentId; ?>');" />
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
							<div class="table_form">
								<table class="form" cellspacing="0">
									<tr class="info">
										<td class="label">Student No.:</td>
										<td class="input">:
											<span class="magnify1">
												<?php echo $studentNo; ?>
											</span>
										</td>
									</tr>
									<tr class="info">
										<td class="label">Student Name:</td>
										<td class="input">:
											<span class="magnify1">
												<?php echo $studentName; ?>
											</span>
										</td>
									</tr>
									<tr class="info">
										<td class="label">School Year</td>
										<td class="input">: 
											<select id="oReportGradesViewerSy" class="medium" name="school_year">
												<option value="-1"></option>
												<?php
													foreach($school_years as $sy){
														echo "<option value=\"{$sy['SchoolYearID']}\">";
															echo $sy['schoolYear'];
														echo "</option>";
													}
												?>
											</select> 
										</td>
									</tr>	
									<tr class="info">
										<td class="label">Semesters</td>
										<td class="input">: 
											<select id="oReportGradesViewerSem" class="medium" name="semester">
												<option value="-1"></option>
												<?php
													foreach($semesters as $sem){
														echo "<option value=\"{$sem['SemesterId']}\">";
															echo $sem['semester'];
														echo "</option>";
													}
												?>
											</select>
										</td>
									</tr>								
								</table>
							</div>

							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='reports-grades-viewer-search.php'" />
									<input type="button" value="Get PDF" onclick="openReportsGradeViewer(<?php echo $studentId; ?>);" />
								</p>
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
