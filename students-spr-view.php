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
	$hnd_gi = new GeneralInformationManager($conn);
	
	//Dictionariess
	$dict_genders =  $hnd_gi->GetGendersByKey();
	$dict_marital_statuses = $hnd_gi->GetMaritalStatusesByKey();
	$dict_religions = $hnd_gi->GetReligionsByKey();
	$dict_citizenships = $hnd_gi->GetCitizenshipsByKey();
	
	$dict_courses = $hnd_cu->GetCoursesByKey();
	$dict_levels = $hnd_cu->GetYearLevelsByKey();
	$dict_college_types = $hnd_co->GetCollegeTypesByKey(); //Student Type
	$dict_student_statuses = $std->GetStatusesByKey();
	$dict_enrollment_statuses = $std->GetEnrollmentStatusesByKey();
	$dict_school_years = $hnd_sc->GetSchoolYearsByKey();
	$dict_semesters = $hnd_sc->GetSemestersByKey();
	$dict_application_types = $std->GetApplicationTypesByKey();
	$dict_regions = $hnd_gi->GetRegionsByKey();
	$dict_citizenships = $hnd_gi->GetCitizenshipsByKey();
	$dict_countries = $hnd_gi->GetCountriesByKey();

	//###### CHECK IF ID IS FOUND ELSE REDIRECT
	if(isset($_GET['id'])){
		$id = (int) $_GET['id'];
		
		if($id <= 0){
			$_SESSION['error'] = array("Unknown Student Permanent Record.");
			header("Location: students-spr.php");
			exit();
		} else {
			//# GET INFORMATION
			$records = $std->GetSprs($id);

			if(sizeof($records) == 0){
				//redirect if nothing is found
				$_SESSION['error'] = array("Unknown Student Permanent Record.");
				header("Location: students-spr.php");
				exit();
			} else {
				//# get the record
				foreach($records as $item){ $record = $item; }
				
				
				//## Transferring data to variables
				$student_no = $record->student_no;
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
					
					$seconds = time() - $birthday;
					$years = $seconds/31557600;
					$age = floor($years);
					$birthday = date("F d, Y", $birthday);
					
				}		

				$place_of_birth = $record->place_of_birth;
				$gender = $dict_genders[$record->gender]->description;
				$marital_status = $dict_marital_statuses[$record->marital_status]->description;
				$mailing_address = $record->mailing_address;
				$email = $record->email;
				$telephone_number = $record->telephone_number;
				$mobile_number = $record->mobile_number;
				
				$religion = "";
				if(isset($dict_religions[$record->religion])){
					$religion = $dict_religions[$record->religion]->description;
				}
				
				$citizenship = "";
				if(isset($dict_citizenships[$record->citizenship])){
					$citizenship = $dict_citizenships[$record->citizenship]->description;
				}
				
				$region = "";
				if(isset($dict_regions[$record->region])){
					$region = $dict_regions[$record->region]->description;
				}
				
				$country = "";
				if(isset($dict_countries[$record->country])){
					$country = $dict_countries[$record->country]->description;
				}
				
				$acr = $record->acr;
				$city_address = $record->city_address;
				$provincial_address = $record->provincial_address;
				
				//## GETTING CURRENT ACADEMIC BACKGROUND INFORMATION
				$backgrounds = $std->GetCurrentAcademicBackgroundsByKey($id);
				foreach($backgrounds as $item){ $background = $item; }
				//course
				$tmp = $dict_courses[$background->course];
				$course = "[" . $tmp->code . "] " . $tmp->description;
				//student_type
				$tmp = $dict_college_types[$background->student_type];
				$student_type = $tmp->description;
				//student status
				$tmp = $dict_student_statuses[$background->student_status];
				$student_status = $tmp->description;
				//enrollment_status
				$tmp = $dict_enrollment_statuses[$background->enrollment_status];
				$enrollment_status = $tmp->description;
				//entry School Year
				$tmp = $dict_school_years[$background->entry_sy];
				$entry_sy = "SY " . $tmp->start . " - " . $tmp->end;
				//entry semester
				$tmp = $dict_semesters[$background->entry_semester];
				$entry_semester = $tmp->description;
				//year of graduation
				//-->NOT YET DEFINED
				$tmp = $dict_application_types[$background->application_type];
				$application_type = $tmp->description;
				
			}
		}
	} else { 
		$_SESSION['error'] = array("Unknown Student Permanent Record.");
		header("Location: students-spr.php");
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
								<span class="Highlight">Student Administration &raquo; Student Permanent Records &raquo; View Record</span> 
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
											<td><?php echo $student_no; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Student Name</td>
											<td class="column">:</td>
											<td><?php echo $last_name . ", " . $first_name . " " . $middle_name; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Gender</td>
											<td class="column">:</td>
											<td><?php echo $gender; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Birthday</td>
											<td class="column">:</td>
											<td><?php echo $birthday; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Age</td>
											<td class="column">:</td>
											<td><?php echo $age; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Place of Birth</td>
											<td class="column">:</td>
											<td><?php echo $place_of_birth; ?></td>
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
											<td>Marital Status</td>
											<td class="column">:</td>
											<td><?php echo $marital_status; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Religion</td>
											<td class="column">:</td>
											<td><?php echo $religion; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Citizenship</td>
											<td class="column">:</td>
											<td><?php echo $citizenship; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Region</td>
											<td class="column">:</td>
											<td><?php echo $region; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Country</td>
											<td class="column">:</td>
											<td><?php echo $country; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>ACR</td>
											<td class="column">:</td>
											<td><?php echo $acr; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>City Address</td>
											<td class="column">:</td>
											<td><?php echo $city_address; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Provincial Address</td>
											<td class="column">:</td>
											<td><?php echo $provincial_address; ?></td>
											<td></td>
										</tr>
									</table>	
									<div id="educational_attainment" style="margin-top: 10px; ">
										<h2>CURRENT ACADEMIC BACKGROUND</h2>
										<table class="form employee" cellspacing="0">
											<tr class="info">
												<td>Course</td>
												<td class="column">:</td>
												<td><?php echo $course; ?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Student Type</td>
												<td class="column">:</td>
												<td><?php echo $student_type; ?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Student Status</td>
												<td class="column">:</td>
												<td><?php echo $student_status; ?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Enrollment Status</td>
												<td class="column">:</td>
												<td><?php echo $enrollment_status; ?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Entry School Year</td>
												<td class="column">:</td>
												<td><?php echo $entry_sy; ?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Entry Semester</td>
												<td class="column">:</td>
												<td><?php echo $entry_semester; ?></td>
												<td></td>
											</tr>
										</table>
									</div>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Back" onclick="window.location='students-spr.php';"/>
												<input type="button" class="button" onclick="window.location='students-spr-edit.php?id=<?php echo $id; ?>';" value="Edit Information" />
												<input type="button" class="button" onclick="window.location='students-spr-courses.php?id=<?php echo $id; ?>';" value="Courses Taken" />
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