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

	//Define Sorting Order
	$sort = 0;
	if(!isset($_GET['sort'])){
		$records = $std->GetPreAdmissionRecordsByApplicationNoDesc();
	} else {
		$sort = (int) $_GET['sort'];
		$records = $std->GetPreAdmissionRecordsSort($sort);
	}

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
								<span class="Highlight">Student Administration &raquo; Pre-Admission Records</span>
							</h1>
							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='students.php'" />
									<input type="button" value="Add Pre-Admission Record" onclick="window.location='students-preadmission-add.php'" />
									<input type="button" value="Get Report" target="_new" onclick="window.open('students-preadmission-report.php?sort=<?php echo $sort; ?>')" />
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
							<p class="">Below are the students who have taken the pre-admission exam.</p>
							<div id="sort_actions">
								<p class="sort">
									<input type="button" value="Latest First" onclick="window.location='students-preadmission.php'" />
									<input type="button" value="Sort by Application No" onclick="window.location='students-preadmission.php?sort=1'" />
									<input type="button" value="Sort by Last Name" onclick="window.location='students-preadmission.php?sort=2'" />
									<input type="button" value="Sort by Date Entered" target="_new" onclick="window.location='students-preadmission.php?sort=3'" />
									<input type="button" value="Sort by SPR CREATED" target="_new" onclick="window.location='students-preadmission.php?sort=4'" />
								</p>
							</div>
							<div class="table">
								<table class="employees" cellspacing="0" title="">
									<thead>
										<th class="Count">No.</th>
										<th class="application_number center">Application Number</th>
										<th class="student_name">Name</th>
										<th class="date_of_entry center">Application Type</th>
										<th class="mobile_number center">Course Passed</th>
										<th class="employee_status">Date Entered</th>
										<th class="mobile_number center">SPR CREATED</th>
										<?php //<th class="Actions"></th> ?>
									</thead>
									<?php
										$ctr = 0;
										if(sizeof($records) > 0){
											foreach($records as $item){
												$ctr++;
												//define the odd even tables
												if($ctr % 2 == 0){
													echo "<tr class=\"even\" onclick=\"window.location='students-preadmission-view.php?id={$item->preadmission_id}';\">";
												} else {
													echo "<tr class=\"odd\" onclick=\"window.location='students-preadmission-view.php?id={$item->preadmission_id}';\">";
												}
													echo "<td>{$ctr}</td>";
													echo "<td>{$item->application_number}</td>";
													echo "<td>{$item->last_name}, {$item->first_name} " . substr($item->middle_name,0,1) . ".</td>";
													echo "<td>{$item->application_type}</td>";
													echo "<td>{$item->course_passed}</td>";
													echo "<td>" . date("M-d-Y", strtotime($item->created)) . "</td>";
													if($item->spr_created == 1){
														echo "<td>YES</td>";
													} else {
														echo "<td>NO</td>";
													}

													//echo "<td class=\"Actions\">";
														//echo "<a href=\"employment-employee-edit.php?id={$item->employee_id}\">Edit</a>";
														//echo " | ";
														//echo "<a href=\"employment-employee-process.php?id={$item->employee_id}&action=delete\" onclick=\"return confirm('Delete employee? Click OK to continue.')\">Delete</a>";
													//echo "</td>";
												echo "</tr>";
											}
										} else {
											echo "<td colspan=\"9\">There are no existing Pre-Admission Records.</td>";
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
