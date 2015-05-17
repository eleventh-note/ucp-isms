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
		$PagePrivileges->AddPrivilege("Colleges - Administrator");
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
		
	//###### CHECK IF ID IS FOUND ELSE REDIRECT
	if(isset($_GET['id'])){
		$id = (int) $_GET['id'];
		if($id <= 0){
			header("Location: colleges.php");
			exit();
		} else {
			//# GET INFORMATION
			$colleges = $hnd->GetColleges($id);

			if(sizeof($colleges) == 0){
				//redirect if nothing is found
				$_SESSION['error'] = array("College not found.");
				header("Location: colleges.php");
				exit();
			} else {
				$college = $colleges[0];

				//## Transferring data to variables
				$code = $college->code;
				$description = $college->description;
				$type = $college->college_type[0]->description;
				$division = $college->division[0]->description;
				$added = $college->created;
				
				//## Get Courses
				$courses = $hnd_c->GetCoursesByCode($id);
				
			}
		}
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
	
	//# Otder Javascript Loaded Here
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_PLUGINS . "jquery.mini.js" . "\"></script>";
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_DEFAULT . "general.js" . "\"></script>";
	
	//Replace Timer Below witd script for javascript logout`
	//###TIMER###
?>
	</head>
	<body id="college">
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
								<span class="Highlight">College Administration &raquo; View Information</span>
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
									<h2>COLLEGE INFORMATION</h2>
									<table class="form subject_view" cellspacing="0">
										<tr class="info">
											<td>College Code</td>
											<td class="column">:</td>
											<td><?php echo $code; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>College Description</td>
											<td class="column">:</td>
											<td><?php echo $description; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Type</td>
											<td class="column">:</td>
											<td><?php echo $type; ?></td>
											<td></td>
										</tr>							
										<tr class="input">
											<td colspan="4"></td>
										</tr>
									</table>	
									<?php
										//## COLLEGE ADMINS
										/*
										<div id="college_admins">
											<h2>COLLEGE ADMINS</h2>
											<table class="form employee" cellspacing="0">
												<?php // ### SCHOOL, DEGREE and YEAR ?>
												<tr class="info">
													<td>School</td>
													<td class="column">:</td>
													<td><?php if(isset($educ_school)){ echo $educ_school; }?></td>
													<td></td>
												</tr>
												<tr class="info">
													<td>Degree</td>
													<td class="column">:</td>
													<td><?php if(isset($educ_degree)){ echo $educ_degree; }?></td>
													<td></td>
												</tr>
												<tr class="info">
													<td>Year</td>
													<td class="column">:</td>
													<td><?php if(isset($educ_year)){ if($educ_year > 0){ echo $educ_year; }}?></td>
													<td></td>
												</tr>
											</table>
										</div>
										*/
									?>
									<div id="course_offerings">
										<h2>COURSES OFFERED</h2>
										<div class="table">
											<table class="courses" cellspacing="0" title="">
												<thead>
													<th class="Count">No.</th>
													<th class="code">Code</th>
													<th class="course">Course</th>
													<th class="number_of_years"># of Years</th>
													<?php //<th class="Actions"></th> ?>
												</thead>
												<?php 
													$ctr = 0;
													if(sizeof($courses) > 0){
														foreach($courses as $item){
															$ctr++;
															//define the odd even tables
															if($ctr % 2 == 0){
																echo "<tr class=\"even\" onclick=\"window.location='colleges-course-view.php?id={$item->course_id}&cid={$id}';\">";
															} else {
																echo "<tr class=\"odd\" onclick=\"window.location='colleges-course-view.php?id={$item->course_id}&cid={$id}';\">";
															}
																echo "<td>{$ctr}</td>";
																echo "<td>{$item->code}</td>";
																echo "<td>{$item->description}</td>";
																echo "<td>{$item->max_year_level}</td>";
																//echo "<td class=\"Actions\">";
																	//echo "<a href=\"employment-employee-edit.php?id={$item->employee_id}\">Edit</a>";
																	//echo " | ";
																	//echo "<a href=\"employment-employee-process.php?id={$item->employee_id}&action=delete\" onclick=\"return confirm('Delete employee? Click OK to continue.')\">Delete</a>";
																//echo "</td>";
															echo "</tr>";
														}
													} else {
														echo "<td colspan=\"6\">There are no courses available.</td>";
													}
												?>
												
											</table>
										</div>
									</div>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Back" onclick="window.location='colleges.php';"/>
												<input type="button" class="button" onclick="window.location='colleges-edit.php?id=<?php echo $id; ?>';" value="Edit College Information" />
												<input type="button" class="button" value="Add Course" onclick="window.location='colleges-course-add.php?id=<?php echo $id; ?>';"/>
												<input type="button" class="button" onclick="
														if(confirm('Are you sure you want to delete this college? Click OK to continue.')){
															window.location='colleges-process.php?id=<?php echo $id; ?>&action=delete';
														}
													" value="Delete College" />
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