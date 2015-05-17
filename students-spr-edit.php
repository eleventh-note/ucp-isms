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
	
	$hnd = new EmployeeManager($conn);
	$gen = new GeneralInformationManager($conn);
	$emp = new EmployeeManager($conn);
	$std = new StudentManager($conn);
	$cou = new CourseManager($conn);
	$sch = new SchoolManager($conn);
	$opt = new Options();
	
	$months = $opt->GetMonths(2);
	
	//##	GENERAL INFORMATION
	$genders = $gen->GetGenders();
	$marital_statuses = $gen->GetMaritalStatus();
	$religions = $gen->GetReligions();
	$citizenships = $gen->GetCitizenships();
	$regions = $gen->GetRegions();
	$countries = $gen->GetCountries();
	
	//##	ADMISSION
	$statuses = $std->GetStatusesByKey();
	$enrollment_statuses = $std->GetEnrollmentStatusesByKey();
	
	//##	COURSES
	$courses = $cou->GetCoursesByCode();
	
	//##	SCHOOLS
	$semesters = $sch->GetSemesters();
	$school_years = $sch->GetSchoolYears();
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	$gen = new GeneralInformationManager($conn);
	$opt = new Options();
	
	$std = new StudentManager($conn);
	
	//###### CHECK IF ID IS FOUND ELSE REDIRECT
	if(isset($_GET['id'])){
		$id = (int) $_GET['id'];
		
		if($id <= 0){
			$_SESSION['error'] = array("Student Permanent Record not found.");
			header("Location: students-spr.php");
			exit();
		} else {
			//# GET INFORMATION
			$records = $std->GetSprs($id);

			if(sizeof($records) == 0){
				//redirect if nothing is found
				$_SESSION['error'] = array("Student Permanent Record not found.");
				header("Location: students-spr.php");
				exit();
			} else {
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
					$birthday = date("F d, Y", $birthday);
				}		

				$place_of_birth = $record->place_of_birth;
				$_gender = $record->gender;
				$marital_status = $record->marital_status;
				$mailing_address = $record->mailing_address;
				$email = $record->email;
				$telephone_number = $record->telephone_number;
				$mobile_number = $record->mobile_number;
				$religion = $record->religion;
				$country = $record->country;
				$region = $record->region;
				$citizenship = $record->citizenship;
				
				$acr = $record->acr;
				$city_address = $record->city_address;
				$provincial_address = $record->provincial_address;
				
				$backgrounds = $std->GetCurrentAcademicBackgroundsByKey($id);
				
				$course = $backgrounds[$id]->course;
				$student_status = $backgrounds[$id]->student_status;
				$enrollment_status = $backgrounds[$id]->enrollment_status;
				
			}
		}
	} else { 
		$_SESSION['error'] = array("Unknown Student Admission Record.");
		header("Location: students-preadmission.php");
		exit();
	}
	
	$conn->Close();
	
	//##### PROCESS ERROR or SUCCESS
	if(isset($_SESSION['error'])){
		$error = $_SESSION['error'];
		unset($_SESSION['error']);
	}
	if(isset($_SESSION['success'])){
		$success = $_SESSION['success'];
		unset($_SESSION['success']);
	}
	//##### PROCESS DATA passed
	if(isset($_SESSION['last_name'])){ $last_name = $_SESSION['last_name']; unset($_SESSION['last_name']); }
	if(isset($_SESSION['middle_name'])){ $middle_name = $_SESSION['middle_name']; unset($_SESSION['middle_name']); }
	if(isset($_SESSION['first_name'])){ $first_name = $_SESSION['first_name']; unset($_SESSION['first_name']); }
	if(isset($_SESSION['birthday'])){ $birthday = $_SESSION['birthday']; unset($_SESSION['birthday']);
		if($birthday != '1970-01-01'){ $birthday = explode("-", $birthday); $bday_yyyy = $birthday[0]; $bday_mm = $birthday[1]; $bday_dd = $birthday[2]; }
	}
	if(isset($_SESSION['gender'])){ $_gender = $_SESSION['gender']; unset($_SESSION['gender']); }
	if(isset($_SESSION['marital_status'])){ $marital_status = $_SESSION['marital_status']; unset($_SESSION['marital_status']); }
	
	if(isset($_SESSION['mailing_address'])){ $mailing_address = $_SESSION['mailing_address']; unset($_SESSION['mailing_address']); }
	if(isset($_SESSION['place_of_birth'])){ $place_of_birth = $_SESSION['place_of_birth']; unset($_SESSION['place_of_birth']); }
	
	if(isset($_SESSION['telephone_number'])){ $telephone_number = $_SESSION['telephone_number']; unset($_SESSION['telephone_number']); }
	if(isset($_SESSION['mobile_number'])){ $mobile_number = $_SESSION['mobile_number']; unset($_SESSION['mobile_number']); }
	if(isset($_SESSION['email'])){ $email = $_SESSION['email']; unset($_SESSION['email']); }

	if(isset($_SESSION['last_school'])){ $last_school = $_SESSION['last_school']; unset($_SESSION['last_school']); }

	if(isset($_SESSION['first_choice'])){ $_first = $_SESSION['first_choice']; unset($_SESSION['first_choice']); }
	if(isset($_SESSION['second_choice'])){ $_second = $_SESSION['second_choice']; unset($_SESSION['second_choice']); }
	if(isset($_SESSION['third_choice'])){ $_third = $_SESSION['third_choice']; unset($_SESSION['third_choice']); }
	
	if(isset($_SESSION['course_passed'])){ $_passed = $_SESSION['course_passed']; unset($_SESSION['course_passed']); }
	if(isset($_SESSION['application_type'])){ $_type = $_SESSION['application_type']; unset($_SESSION['application_type']); }
	if(isset($_SESSION['application_status'])){ $_status = $_SESSION['application_status']; unset($_SESSION['application_status']); }
	
	if(isset($_SESSION['school_year'])){ $_school_year = $_SESSION['school_year']; unset($_SESSION['school_year']); }
	if(isset($_SESSION['semester'])){ $_semester = $_SESSION['semester']; unset($_SESSION['semester']); }

	
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
								<span class="Highlight">Student Administration &raquo; Student Permanent Records &raquo; Add Record</span> 
							</h1>
							<p class="">Please complete required pre-admission information.</p>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
							?>
							<form action="students-spr-process.php" method="post">
								<input type="hidden" name="spr_id" value="<?php echo $id; ?>" />
								<hr class="form_top"/>
								<div class="table_form">
									<?php /* ###############################################
										
													PERSONAL INFORMATION
									
											 ############################################### */
									?>
									<h2>PERSONAL INFORMATION</h2>
									<table class="form employee" cellspacing="0">
										<?php // ### NAME ?>
										<tr class="label">
											<td>* Last Name:</td>
											<td>* First Name:</td>
											<td>* Middle Name:</td>
											<td></td>
										</tr>
										<tr class="input">
											<td><input type="text" name="last_name" class="input_name" value="<?php if(isset($last_name)){ echo $last_name; }?>"/></td>
											<td><input type="text" name="first_name" class="input_name" value="<?php if(isset($first_name)){ echo $first_name; }?>"/></td>
											<td><input type="text" name="middle_name" class="input_name" value="<?php if(isset($middle_name)){ echo $middle_name; }?>"/></td>
											<td></td>
										</tr>
										<tr class="label">
											<td>* Birthday:</td>
											<td>* Gender:</td>
											<td>* Marital Status:</td>
											<td></td>
										</tr>
										<tr class="input">
											<td colspan="1">
												<select name="bday_mm">
													<option value="-1">Month</option>
													<?php 
														$ctr = 0;
														foreach($months as $month){
															$ctr++;
															if(isset($bday_mm)){
																if($bday_mm == $ctr){
																	echo "<option value=\"" . ($ctr) . "\" selected=\"selected\">" . $month . "</option>";
																} else {
																	echo "<option value=\"" . ($ctr) . "\">" . $month . "</option>";
																}
															} else {
																echo "<option value=\"" . ($ctr) . "\">" . $month . "</option>";
															}
														}
													?>
												</select> - 
												<select name="bday_dd">
													<option value="-1">Day</option>
													<?php 
														$ctr = 0;
														for($ctr = 0; $ctr < 31; $ctr++){
															if(isset($bday_dd)){
																if($bday_dd == ($ctr+1)){
																	echo "<option value=\"" . ($ctr+1) . "\" selected=\"selected\">" . ($ctr+1) . "</option>";
																} else {
																	echo "<option value=\"" . ($ctr+1) . "\">" . ($ctr+1) . "</option>";
																}
															} else {
																echo "<option value=\"" . ($ctr+1) . "\">" . ($ctr+1) . "</option>";
															}
														}
													?>
												</select> - 
												<select name="bday_yyyy">
													<option value="-1">Year</option>
													<?php 
														$ctr = 0;
														$year_last = date("Y", time()) - 12;
														for($ctr = $year_last; $ctr > (12 + 1918) ; $ctr--){
															if(isset($bday_yyyy)){
																if($bday_yyyy == ($ctr+1)){
																	echo "<option value=\"" . ($ctr+1) . "\" selected=\"selected\">" . ($ctr+1) . "</option>";
																} else {
																	echo "<option value=\"" . ($ctr+1) . "\">" . ($ctr+1) . "</option>";
																}
															} else {
																echo "<option value=\"" . ($ctr+1) . "\">" . ($ctr+1) . "</option>";
															}
														}
													?>
												</select>
											</td>
											<td>
												<select name="gender" class="gender">
													<option value="-1"></option>
													<?php
														foreach($genders as $gender){
															if(isset($_gender)){
																if($_gender==$gender->gender_id){
																	echo "<option value=\"{$gender->gender_id}\" selected=\"selected\">{$gender->description}</option>";
																} else {
																	echo "<option value=\"{$gender->gender_id}\">{$gender->description}</option>";
																}
															} else {
																echo "<option value=\"{$gender->gender_id}\">{$gender->description}</option>";
															}
														}
													?>
												</select>
											</td>
											<td>
												<select name="marital_status" class="marital_status">
													<option value="-1"></option>
													<?php
														foreach($marital_statuses as $status){
															if(isset($marital_status)){
																if($marital_status == $status->status_id){
																	echo "<option value=\"{$status->status_id}\" selected=\"selected\">{$status->description}</option>";
																} else {
																	echo "<option value=\"{$status->status_id}\">{$status->description}</option>";
																}
															} else {
																echo "<option value=\"{$status->status_id}\">{$status->description}</option>";
															}
														}
													?>
												</select>
											</td>
											<td></td>
										</tr>
										<?php //## PLACE OF BIRTH ?>
										<tr class="label">
											<td colspan="4">* Place of Birth:</td>
										</tr>
										<tr class="input">
											<td colspan="4"><input type="text" name="place_of_birth" class="input_address" value="<?php if(isset($place_of_birth)){ echo $place_of_birth; }?>"/></td>
										</td>
										<?php //## MAILING ADDRESS ?>
										<tr class="label">
											<td colspan="4">Mailing Address:</td>
										</tr>
										<tr class="input">
											<td colspan="4"><input type="text" name="mailing_address" class="input_address" value="<?php if(isset($mailing_address)){ echo $mailing_address; }?>"/></td>
										</tr>
										<?php //## CONTACT NUMBER and EMAIL ADDRESS ?>
										<tr class="label">
											<td>Telephone Number:</td>
											<td>Mobile Number:</td>
											<td colspan="2">Email Address:</td>
										</td>
										<tr class="input">
											<td><input type="text" name="telephone_number" class="input_contact" value="<?php if(isset($telephone_number)){ echo $telephone_number; }?>" maxlength="15"/></td>
											<td><input type="text" name="mobile_number" class="input_contact" value="<?php if(isset($mobile_number)){ echo $mobile_number; }?>" maxlength="15"/></td>
											<td colspan="2"><input type="text" name="email" class="input_email" value="<?php if(isset($email)){ echo $email; }?>"/></td>
										</td>
										<tr class="label">
											<td colspan="4">Religion:</td>
										</tr>
										<tr class="input">
											<td colspan="2">
												<select name="religion" class="religion large">
													<option value="-1"></option>
													<?php
														foreach($religions as $status){
															if(isset($religion)){
																if($religion == $status->religion_id){
																	echo "<option value=\"{$status->religion_id}\" selected=\"selected\">{$status->description}</option>";
																} else {
																	echo "<option value=\"{$status->religion_id}\">{$status->description}</option>";
																}
															} else {
																echo "<option value=\"{$status->religion_id}\">{$status->description}</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="label">
											<td colspan="2">Country</td>
											<td colspan="2">Citizenship:</td>
										</tr>
										<tr class="input">
											<td colspan="2">
												<select name="country" class="country large">
													<option value="-1"></option>
													<?php
														foreach($countries as $item){
															if(isset($country)){
																if($country == $item->country_id){
																	echo "<option value=\"{$item->country_id}\" selected=\"selected\">{$item->description}</option>";
																} else {
																	echo "<option value=\"{$item->country_id}\">{$item->description}</option>";
																}
															} else {
																echo "<option value=\"{$item->country_id}\">{$item->description}</option>";
															}
														}
													?>
												</select>
											</td>
											<td colspan="2">
												<select name="citizenship" class="citizenship small">
													<option value="-1"></option>
													<?php
														foreach($citizenships as $item){
															if(isset($citizenship)){
																if($citizenship == $item->citizenship_id){
																	echo "<option value=\"{$item->citizenship_id}\" selected=\"selected\">{$item->description}</option>";
																} else {
																	echo "<option value=\"{$item->citizenship_id}\">{$item->description}</option>";
																}
															} else {
																echo "<option value=\"{$item->citizenship_id}\">{$item->description}</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="label">
											<td colspan="2">Region</td>
											<td colspan="2"></td>
										</tr>
										<tr class="input">
											<td colspan="2">
												<select name="region" class="region small">
													<option value="-1"></option>
													<?php
														foreach($regions as $item){
															if(isset($region)){
																if($region == $item->region_id){
																	echo "<option value=\"{$item->region_id}\" selected=\"selected\">{$item->description}</option>";
																} else {
																	echo "<option value=\"{$item->region_id}\">{$item->description}</option>";
																}
															} else {
																echo "<option value=\"{$item->region_id}\">{$item->description}</option>";
															}
														}
													?>
												</select>
											</td>
											<td colspan="2">
											</td>
										</tr>	
										<?php //## ACR ?>
										<tr class="label">
											<td colspan="4">ACR:</td>
										</tr>
										<tr class="input">
											<td colspan="4"><input type="text" name="acr" class="input_address" value="<?php if(isset($acr)){ echo $acr; }?>"/></td>
										</tr>
										<tr class="input">
											<td colspan="4"></td>
										</tr>
										<?php //## City Address ?>
										<tr class="label">
											<td colspan="4">City Address:</td>
										</tr>
										<tr class="input">
											<td colspan="4"><input type="text" name="city_address" class="input_address" value="<?php if(isset($city_address)){ echo $city_address; }?>"/></td>
										</tr>
										<tr class="input">
											<td colspan="4"></td>
										</tr>
										<?php //## Provincial Address ?>
										<tr class="label">
											<td colspan="4">Provincial Address:</td>
										</tr>
										<tr class="input">
											<td colspan="4"><input type="text" name="provincial_address" class="input_address" value="<?php if(isset($provincial_address)){ echo $provincial_address; }?>"/></td>
										</tr>
										<tr class="input">
											<td colspan="4"></td>
										</tr>
									</table>
									<?php /* ###############################################
										
													CURRENT ACADEMIC BACKGROUND
									
											 ############################################### */
									?>
									<input type="hidden" name="curr_course" value="<?php echo $course; ?>" />
									<h2>CURRENT ACADEMIC BACKGROUND</h2>
									<table class="form employee" cellspacing="0">
										<tr class="label">
											<td>Student No:</td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr class="input">
											<td><input type="text" name="student_no" class="input_employee_number" value="<?php echo $student_no; ?>" maxlength="11" /></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr class="label">
											<td>* Entry School Year:</td>
											<td>* Entry Semester:</td>
											<td></td>
											<td></td>
										</tr>
										<tr class="input">
											<td>
												<select name="school_years" class="employment_status small" readonly="readonly">
													<option value="-1"></option>
													<?php
														foreach($school_years as $item){
															if(isset($_school_year)){
																if($_school_year == $item->year_id){
																	echo "<option value=\"{$item->year_id}\" selected=\"selected\">SY {$item->start}-{$item->end}</option>";
																} else {
																	echo "<option value=\"{$item->year_id}\">SY {$item->start}-{$item->end}</option>";
																}
															} else {
																if($item->active == 1){
																	echo "<option value=\"{$item->year_id}\" selected=\"selected\">SY {$item->start}-{$item->end}</option>";
																} else {
																	echo "<option value=\"{$item->year_id}\">SY {$item->start}-{$item->end}</option>";
																}
															}
														}
													?>
												</select>
											</td>
											<td>
												<select name="semesters" class="employment_status small" readonly="readonly">
													<option value="-1"></option>
													<?php
														foreach($semesters as $item){
															if(isset($_semester)){
																if($_semester == $item->semester_id){
																	echo "<option value=\"{$item->semester_id}\" selected=\"selected\">{$item->description}</option>";
																} else {
																	echo "<option value=\"{$item->semester_id}\">{$item->description}</option>";
																}
															} else {
																if($item->active == 1){
																	echo "<option value=\"{$item->semester_id}\" selected=\"selected\">{$item->description}</option>";
																} else {
																	echo "<option value=\"{$item->semester_id}\">{$item->description}</option>";
																}
															}
														}
													?>
												</select>
											</td>		
											<td></td>
											<td></td>
										</tr>
										<tr class="label">
											<td colspan="4">* Course:</td>
										</tr>
										<tr class="input">
											<td colspan="4">
												<select name="course" class="employment_status large">
													<option value="-1"></option>
													<?php
														foreach($courses as $item){
															if(isset($course)){
																if($course == $item->course_id){
																	echo "<option value=\"{$item->course_id}\" selected=\"selected\">[{$item->code}] {$item->description}</option>";
																} else {
																	echo "<option value=\"{$item->course_id}\">[{$item->code}] {$item->description}</option>";
																}
															} else {
																echo "<option value=\"{$item->course_id}\">[{$item->code}] {$item->description}</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="label">
											<td>* Student Status:</td>
											<td>* Enrollment Status:</td>
											<td></td>
											<td></td>
										</tr>
										<tr class="input">
											<td>
												<select name="student_status" class="employment_status small">
													<option value="-1"></option>
													<?php
														foreach($statuses as $item){
															if(isset($student_status)){
																if($student_status == $item->status_id){
																	echo "<option value=\"{$item->status_id}\" selected=\"selected\">{$item->description}</option>";
																} else {
																	echo "<option value=\"{$item->status_id}\">{$item->description}</option>";
																}
															} else {
																echo "<option value=\"{$item->status_id}\">{$item->description}</option>";
															}
														}
													?>
												</select>
											</td>		
											<td>
												<select name="enrollment_status" class="employment_status small">
													<option value="-1"></option>
													<?php
														foreach($enrollment_statuses as $item){
															if(isset($enrollment_status)){
																if($enrollment_status == $item->status_id){
																	echo "<option value=\"{$item->status_id}\" selected=\"selected\">{$item->description}</option>";
																} else {
																	echo "<option value=\"{$item->status_id}\">{$item->description}</option>";
																}
															} else {
																echo "<option value=\"{$item->status_id}\">{$item->description}</option>";
															}
														}
													?>
												</select>
											</td>
											<td></td>
											<td></td>
										</tr>
										<tr class="label">
											<td>Graduated: <input type="checkbox" name="graduated" value="1" /></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr class="input">
											<td></td>		
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr class="input">
											<td colspan="4"></td>
										</tr>
									</table>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Cancel" onclick="window.location='students-spr-view.php?id=<?php echo $id; ?>';"/>
												<input type="submit" class="button" name="record_save" value="Save Record" />
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