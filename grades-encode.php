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

	if(isset($_GET['id'])){
		$sectionSubject = (int) $_GET['id'];
	} else {
		header("Location: grades-encode-subject_list.php");
		exit();
	}
	$students = $hnd->getStudentsForEncoding($sectionSubject, $UserInfo->employee_id);
	$section = $students[0]['section'];
	$subjectCode = $students[0]['subjectCode'];
	$subjectDescription = $students[0]['subjectDescription'];

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
	<body id="grades">
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
								<span class="Highlight">Grades Administration &raquo; Encoding</span>
							</h1>
							<form action="grades-encode-process.php" method="post" >
							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='grades-encode-subject_list.php'" />
									<input type="submit" name="save_grades" value="Save Grades" onclick="window.location='grades-encode-process.php'" />
									<input type="button" name="" value="Get Grades Sheet" onclick="window.open('grades-grade-sheet.php?id=<?php echo $sectionSubject; ?>')" />
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
							<input type="hidden" name="sectionSubject" value="<?php echo $sectionSubject; ?>" />
							<div class="table_form">
								<table class="form" cellspacing="0">
									<tr class="info">
										<td class="label">Section:</td>
										<td class="input">:
											<span class="magnify3">
												<?php echo $section; ?>
											</span>
										</td>
									</tr>
									<tr class="info">
										<td class="label">Subject Code:</td>
										<td class="input">:
											<span class="magnify3">
												<?php echo $subjectCode; ?>
											</span>
										</td>
									</tr>
									<tr class="info">
										<td class="label">Subject Description:</td>
										<td class="input">:
											<span class="magnify3">
												<?php echo $subjectDescription; ?>
											</span>
										</td>
									</tr>
								</table>
							</div>
							<p class="">
								<b>DRP</b> - for DROPPED;
								<b>INC</b> - for INCOMPLETE
							</p>
							<div class="table">
								<table class="" cellspacing="0" title="">
									<thead>
										<th class="Count">No.</th>
										<th class="">Student No</th>
										<th class="student_name">Name</th>
										<th class="date_of_entry center">Midterm Grade</th>
										<th class="date_of_entry center">Final Grade</th>
										<?php //<th class="Actions"></th> ?>
									</thead>
									<?php
										$ctr = 0;
										if(sizeof($students) > 0){
											foreach($students as $item){
												$ctr++;
												//define the odd even tables
												if($ctr % 2 == 0){
													echo "<tr class=\"even\">";
												} else {
													echo "<tr class=\"odd\">";
												}
													echo "<input type=\"hidden\" name=\"gradeId[]\" value=\"{$item['gradeId']}\" />";
													echo "<td>{$ctr}</td>";
													echo "<td>{$item['StudentNo']}</td>";
													echo "<td>{$item['studentName']}</td>";
													if(time() < strtotime($item['activeUntil'])){
														echo "<td style=\"text-align: center;\"><input type=\"text\" style=\"width: 40px; text-align: center;\" name=\"midtermGrade[]\" maxlength=\"5\" value=\"{$item['midtermGrade']}\" /></td>";
													} else { //disabled
														echo "<td style=\"text-align: center;\"><input type=\"text\" style=\"width: 40px; text-align: center;\" name=\"midtermGrade[]\" maxlength=\"5\" value=\"{$item['midtermGrade']}\" disabled=\"disabled\"/></td>";
													}
													if(time() < strtotime($item['activeUntil2'])){
														echo "<td style=\"text-align: center;\"><input type=\"text\" style=\"width: 40px; text-align: center;\" name=\"finalGrade[]\" maxlength=\"5\" value=\"{$item['finalGrade']}\" /></td>";
													} else {
														echo "<td style=\"text-align: center;\"><input type=\"text\" style=\"width: 40px; text-align: center;\" name=\"finalGrade[]\" maxlength=\"5\" value=\"{$item['finalGrade']}\" disabled=\"disabled\"/></td>";
													}

												echo "</tr>";
											}
										} else {
											echo "<td colspan=\"6\">There are no existing subjects that are for encoding.</td>";
										}
									?>

								</table>
							</div>
							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='grades-encode-subject_list.php'" />
									<input type="submit" name="save_grades" value="Save Grades" onclick="window.location='grades-encode-process.php'" />
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
