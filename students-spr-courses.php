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
	require_once(CLASSLIST . "gen.inc.php");
	require_once(CLASSLIST . "options.inc.php");
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
	
	$gen = new GeneralInformationManager($conn);
	$opt = new Options();
		
	if(!isset($_GET['id'])){
		$_SESSION['error'] = array("Unknown Student Permanent Record.");
		header("Location: students-spr.php");
		exit();
	} else {
		$id = (int) $_GET['id'];
	}
	
	$std = new StudentManager($conn);
	$courses = $std->getStudentCourses($id);
	
	if(sizeof($courses) == 0){
		//redirect if nothing is found
		$_SESSION['error'] = array("Unknown Student Permanent Record.");
		header("Location: students-spr.php");
		exit();
	} else {
		//# GET INFORMATION
		$records = $std->GetSprs($id);	
	
		//# get the record
		foreach($records as $item){ $record = $item; }
		
		//## Transferring data to variables
		$student_no = $record->student_no;
		$last_name = $record->last_name;
		$middle_name = $record->middle_name;
		$first_name = $record->first_name;
	}
	
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
	
	//# Other Javascript Loaded Here
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_PLUGINS . "jquery.mini.js" . "\"></script>"
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
								<span class="Highlight">Student Administration &raquo; Student Permanent Records &raquo; Courses Taken</span> 
							</h1>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>
							<form action="employment-employee-process.php" method="post">
								<input type="hidden" name="employee_id" value="<?php echo $id; ?>" />
								
								<div class="table_form">
									<h2>PERSONAL INFORMATION</h2>
									<table class="form employee" cellspacing="0">
										<tr class="info">
											<td>Student Number</td>
											<td class="column">:</td>
											<td class="magnify3"><?php echo $student_no; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Student Name</td>
											<td class="column">:</td>
											<td class="magnify3"><?php echo $last_name . ", " . $first_name . " " . $middle_name; ?></td>
											<td></td>
										</tr>
									</table>	
									<br/><br/>
									<h2>COURSES TAKEN</h2>
								
									<div class="table">
										<table class="" cellspacing="0" title="">
											<thead>
												<th class="">No.</th>
												<th class="">Course Code</th>
												<th class="">Course Description</th>
												<th class="">S.Y</th>
												<th class="">Semester</th>
												<th class="">Graduate	</th>
												<th class=""></th>
												<?php //<th class="Actions"></th> ?>
											</thead>
											<?php 
												$ctr = 0;
												if(sizeof($courses) > 0){
													foreach($courses as $item){
														$ctr++;
														//define the odd even tables
														if($ctr % 2 == 0){
															echo "<tr class=\"even\" onclick=\"window.open('students-spr-permanent_record.php?sid={$id}&cid={$item['id']}');\">";
														} else {
															echo "<tr class=\"odd\" onclick=\"window.open('students-spr-permanent_record.php?sid={$id}&cid={$item['id']}');\">";
														}
															echo "<td>{$ctr}</td>";
															echo "<td>{$item['courseCode']}</td>";
															echo "<td>{$item['courseDescription']}</td>";
															echo "<td>{$item['sy']}</td>";
															echo "<td>{$item['semester']}</td>";
															if($item['isGraduate'] == 1){
																echo "<td>YES</td>";
															} else {
																echo "<td>NO</td>";
															}
															echo "<td></td>";
														echo "</tr>";
													}
												} else {
													echo "<td colspan=\"7\">There are no existing Student Permanent Records.</td>";
												}
											?>
											
										</table>
									</div>
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