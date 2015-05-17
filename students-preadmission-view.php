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
	
	$std = new StudentManager($conn);
	$hnd_cu = new CourseManager($conn);
	$hnd_co = new CollegeManager($conn);
	$hnd_sc = new SchoolManager($conn);
	
	//Pre-AdmissionRecords
	$records = $std->GetPreAdmissionRecords();
	//For Drop-Downs
	$courses = $hnd_cu->GetCoursesByKey();
	$levels = $hnd_cu->GetYearLevelsByKey();
	$college_types = $hnd_co->GetCollegeTypes(); //Student Type
	$student_statuses = $std->GetStatusesByKey();
	$enrollment_statuses = $std->GetEnrollmentStatusesByKey();
	//###### CHECK IF ID IS FOUND ELSE REDIRECT
	if(isset($_GET['id'])){
		$id = (int) $_GET['id'];
		
		if($id <= 0){
			$_SESSION['error'] = array("Unknown Student Admission Record.");
			header("Location: students-preadmission.php");
			exit();
		} else {
			//# GET INFORMATION
			$records = $std->GetPreAdmissionRecords($id);
			
			if(sizeof($records) == 0){
				//redirect if nothing is found
				$_SESSION['error'] = array("Student Admission Record not found.");
				header("Location: students-preadmission.php");
				exit();
			} else {
				$record = $records[0];

				//## Transferring data to variables
				$application_number = $record->application_number;
				$last_name = $record->last_name;
				$middle_name = $record->middle_name;
				$first_name = $record->first_name;
				
				$birthday = $record->birthday;
				if($birthday != '1970-01-01'){ 
					$birthday = explode("-", $birthday); 
					$bday_yyyy = $birthday[0];
					$bday_mm = $birthday[1];
					$bday_dd = $birthday[2];
					$birthday = mktime(0,0,0, $bday_mm, $bday_dd, $bday_yyyy);
					$birthday = date("F d, Y", $birthday);
				}		

				$place_of_birth = $record->place_of_birth;
				$gender = $record->gender;
				$marital_status = $record->marital_status;
				$mailing_address = $record->mailing_address;
				$email = $record->email;
				$telephone_number = $record->telephone_number;
				$mobile_number = $record->mobile_number;
				$last_school = $record->last_school;
				$application_type = $record->application_type;
				$school_year = $record->school_year;
				$semester = $record->semester;
				$application_number = $record->application_number;
				$first_choice = $record->first_choice;
				$second_choice = $record->second_choice;
				$third_choice = $record->third_choice;
				$application_status = $record->application_status;
				$course_passed = $record->course_passed;
				$spr_created = $record->spr_created;
				
			}
		}
	} else { 
		$_SESSION['error'] = array("Unknown Student Admission Record.");
		header("Location: students-preadmission.php");
		exit();
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
		<script type="text/javascript">
			var oCourse;
			var oStudentType;
			var oStudentStatus;
			var oEnrollmentStatus;
			function checkRequirements(){
				var result = false;
					//check oCourse
					oCourse = document.getElementById("oCourse");					
					oStudentType = document.getElementById("oStudentType");
					oStudentStatus = document.getElementById("oStudentStatus");
					oEnrollmentStatus = document.getElementById("oEnrollmentStatus");
					// alert(oCourse);
					// alert(oCourse.value);
					// alert(oStudentType.value);
					// alert(oStudentStatus.value);
					// alert(oEnrollmentStatus.value);
					if(oCourse.value > -1 && oStudentType.value > -1 && oStudentStatus.value > -1 && oEnrollmentStatus.value > -1){
						result = true;
					} 
					
				return result;
			}
		</script>
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
								<span class="Highlight">Student Administration &raquo; Pre-Admission Records &raquo; View Record</span> 
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
											<td>Name</td>
											<td class="column">:</td>
											<td><?php echo $last_name . ", " . $first_name . " " . $middle_name; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Birthday</td>
											<td class="column">:</td>
											<td><?php echo $birthday; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Place of Birth</td>
											<td class="column">:</td>
											<td><?php echo $place_of_birth; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Gender</td>
											<td class="column">:</td>
											<td><?php echo $gender; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Marital Status</td>
											<td class="column">:</td>
											<td><?php echo $marital_status; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Mailing Address</td>
											<td class="column">:</td>
											<td><?php if(isset($mailing_address)){ echo $mailing_address; }?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Email Address</td>
											<td class="column">:</td>
											<td><?php if(isset($email)){ echo $email; }?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Telephone Number</td>
											<td class="column">:</td>
											<td><?php if(isset($telephone_number)){ echo $telephone_number; }?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Mobile Number</td>
											<td class="column">:</td>
											<td><?php if(isset($mobile_number)){ echo $mobile_number; }?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Last School Attended</td>
											<td class="column">:</td>
											<td><?php if(isset($last_school)){ echo $last_school; }?></td>
											<td></td>
										</tr>
									</table>	
									<div id="educational_attainment" style="margin-top: 10px; ">
										<h2>APPLICATION INFORMATION</h2>
										<table class="form employee" cellspacing="0">
											<tr class="info">
												<td>Application Number</td>
												<td class="column">:</td>
												<td><b><?php echo $application_number; ?></b></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Application Status</td>
												<td class="column">:</td>
												<td><b><?php if(isset($application_status)){ echo $application_status; }?></b></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Course Passed</td>
												<td class="column">:</td>
												<td><b><?php if(isset($course_passed)){ echo $course_passed; }?></b></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Application Type</td>
												<td class="column">:</td>
												<td><?php if(isset($application_type)){ echo $application_type; }?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>First Choice</td>
												<td class="column">:</td>
												<td><?php if(isset($first_choice)){ echo $first_choice; }?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Second Choice</td>
												<td class="column">:</td>
												<td><?php if(isset($second_choice)){ echo $second_choice; }?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Third Choice</td>
												<td class="column">:</td>
												<td><?php if(isset($third_choice)){ echo $third_choice; }?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Entry School Year:</td>
												<td class="column">:</td>
												<td><?php echo $school_year; ?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Entry Semester:</td>
												<td class="column">:</td>
												<td><?php echo $semester; ?></td>
												<td></td>
											</tr>
										</table>
									</div>
									<div id="spr_course_selection" style="margin-top: 10px; ">
										<?php if($spr_created==0){ ?>
										<h2>CURRENT ACADEMIC BACKGROUND</h2>
										<table class="form employee" cellspacing="0">
											<?php
												echo "<tr class=\"info\">";
													echo "<td class=\"label\">Course</td>";
													echo "<td class=\"input\">: ";
														echo "<select id=\"oCourse\" class=\"extra-large mono\" name=\"course\">";
															echo "<option value=\"-1\"></option>";
															
																foreach($courses as $item){
																	echo "<option value=\"{$item->course_id}\">";
																		echo str_pad($item->code,15, ".", STR_PAD_RIGHT) . 
																		str_pad($item->description, 60, ".", STR_PAD_RIGHT) .
																		str_pad($levels[$item->max_year_level]->equivalent . " Years", 10, ".", STR_PAD_RIGHT);
																	echo "</option>";
																}
															
														echo "</select>";
													echo "</td>";
												echo "</tr>";
												echo "<tr class=\"info\">";
													echo "<td class=\"label\">Student Type</td>";
													echo "<td class=\"input\">: ";
														echo "<select id=\"oStudentType\" class=\"small mono\" name=\"student_type\">";
															echo "<option value=\"-1\"></option>";
															
																foreach($college_types as $item){
																	echo "<option value=\"{$item->type_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															
														echo "</select>";
													echo "</td>";
												echo "</tr>";
												echo "<tr class=\"info\">";
													echo "<td class=\"label\">Student Status</td>";
													echo "<td class=\"input\">: ";
														echo "<select id=\"oStudentStatus\" class=\"small mono\" name=\"student_status\">";
															echo "<option value=\"-1\"></option>";
															
																foreach($student_statuses as $item){
																	echo "<option value=\"{$item->status_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															
														echo "</select>";
													echo "</td>";
												echo "</tr>";
												echo "<tr class=\"info\">";
													echo "<td class=\"label\">Enrollment Status</td>";
													echo "<td class=\"input\">: ";
														echo "<select id=\"oEnrollmentStatus\" class=\"small mono\" name=\"enrollment_status\">";
															echo "<option value=\"-1\"></option>";
															
																foreach($enrollment_statuses as $item){
																	echo "<option value=\"{$item->status_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															
														echo "</select>";
													echo "</td>";
												echo "</tr>";
											?>
										</table>
										<?php } ?>
									</div>
										<hr class="form_top"/>
										<table class="form" cellspacing="0">
											<tr class="button">
												<td colspan="2">
													<input type="button" class="button" value="Back" onclick="window.location='students-preadmission.php';"/>
													<?php if($spr_created==0){ ?>
													<input type="button" class="button" onclick="window.location='students-preadmission-edit.php?id=<?php echo $id; ?>';" value="Edit Information" />
													<input type="button" class="button" onclick="
														if(checkRequirements() == true){
															if(confirm('Are you sure you want to create SPR for this record? Click OK to continue.')){
																window.location='students-preadmission-process.php?id=<?php echo $id; ?>&action=spr' +
																'&c=' + oCourse.value + '&ss=' + oStudentStatus.value + '&st=' + oStudentType.value + '&es=' + oEnrollmentStatus.value
																;
															}
														} else {
															alert('Please input course, student type, student status, and enrollment status.');
														}
														" value="Create Student Permanent Record" />
													<input type="button" class="button" onclick="
															if(confirm('Are you sure you want to delete this pre-admission record? Click OK to continue.')){
																window.location='students-preadmission-process.php?id=<?php echo $id; ?>&action=delete';
															}
														" value="Delete Record" />
													<?php } ?>
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