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
		$PagePrivileges->AddPrivilege("Employment - Administrator");
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
	$opt = new Options();
	
	$months = $opt->GetMonths(2);
	
	$positions = $hnd->GetPositions();
	$genders = $gen->GetGenders();
	$marital_statuses = $gen->GetMaritalStatus();
	$employee_statuses = $emp->GetEmploymentStatuses();
	$employee_count = $emp->GetEmployeeCount();
	
	$conn->Close();
	
	//##### PROCESS ERROR or SUCCESS
	if(isset($_SESSION['error'])){
		$error = $_SESSION['error'];
		unset($_SESSION['error']);
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
	if(isset($_SESSION['city_address'])){ $city_address = $_SESSION['city_address']; unset($_SESSION['city_address']); }
	if(isset($_SESSION['provincial_address'])){ $provincial_address = $_SESSION['provincial_address']; unset($_SESSION['provincial_address']); }
	if(isset($_SESSION['telephone_number'])){ $telephone_number = $_SESSION['telephone_number']; unset($_SESSION['telephone_number']); }
	if(isset($_SESSION['mobile_number'])){ $mobile_number = $_SESSION['mobile_number']; unset($_SESSION['mobile_number']); }
	if(isset($_SESSION['email'])){ $email = $_SESSION['email']; unset($_SESSION['email']); }
	if(isset($_SESSION['date_of_entry'])){ $date_of_entry = $_SESSION['date_of_entry']; unset($_SESSION['date_of_entry']);
		if($date_of_entry != '1970-01-01'){ $date_of_entry = explode("-", $date_of_entry); $entry_yyyy = $date_of_entry[0]; $entry_mm = $date_of_entry[1]; $entry_dd = $date_of_entry[2]; }
	}
	if(isset($_SESSION['employment_status'])){ $employment_status = $_SESSION['employment_status']; unset($_SESSION['employment_status']); }
	if(isset($_SESSION['sss_1'])){ $sss_1 = $_SESSION['sss_1']; unset($_SESSION['sss_1']); }
	if(isset($_SESSION['sss_2'])){ $sss_2 = $_SESSION['sss_2']; unset($_SESSION['sss_2']); }
	if(isset($_SESSION['sss_3'])){ $sss_3 = $_SESSION['sss_3']; unset($_SESSION['sss_3']); }
	if(isset($_SESSION['tax_1'])){ $tax_1 = $_SESSION['tax_1']; unset($_SESSION['tax_1']); }
	if(isset($_SESSION['tax_2'])){ $tax_2 = $_SESSION['tax_2']; unset($_SESSION['tax_2']); }
	if(isset($_SESSION['tax_3'])){ $tax_3 = $_SESSION['tax_3']; unset($_SESSION['tax_3']); }
	if(isset($_SESSION['tax_4'])){ $tax_4 = $_SESSION['tax_4']; unset($_SESSION['tax_4']); }
	if(isset($_SESSION['educ_year'])){ $educ_year = $_SESSION['educ_year']; unset($_SESSION['educ_year']); }
	if(isset($_SESSION['educ_school'])){ $educ_school = $_SESSION['educ_school']; unset($_SESSION['educ_school']); }
	if(isset($_SESSION['educ_degree'])){ $educ_degree = $_SESSION['educ_degree']; unset($_SESSION['educ_degree']); }
	
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
	<body id="employment">
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
								<span class="Highlight">Employee Administration &raquo; Manage Employees &raquo; Add</span> 
							</h1>
							<p class="">Please complete required employee information.</p>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
							?>
							<form action="employment-employee-process.php" method="post">
								<hr class="form_top"/>
								<div class="table_form">
									<h2>EMPLOYEE INFORMATION</h2>
									<table class="form employee" cellspacing="0">
										<tr class="label">
											<td>Employee Number:</td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										<tr class="input">
											<td><input type="text" name="employee_number" class="input_employee_number" value="<?php echo date("Y") . "-" . str_pad($employee_count+1,5,"0",STR_PAD_LEFT); ?>"/></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
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
														$year_last = date("Y", time()) - 18;
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
										<?php //## CITY ADDRESS ?>
										<tr class="label">
											<td colspan="4">City Address:</td>
										</tr>
										<tr class="input">
											<td colspan="4"><input type="text" name="city_address" class="input_address" value="<?php if(isset($city_address)){ echo $city_address; }?>"/></td>
										</td>
										<?php //## PROVINCIAL ADDRESS ?>
										<tr class="label">
											<td colspan="4">Provincial Address:</td>
										</td>
										<tr class="input">
											<td colspan="4"><input type="text" name="provincial_address" class="input_address" value="<?php if(isset($provincial_address)){ echo $provincial_address; }?>"/></td>
										</td>
										<?php //## CONTACT NUMBER and EMAIL ADDRESS ?>
										<tr class="label">
											<td>Telephone Number:</td>
											<td>Mobile Number:</td>
											<td colspan="2">Email Address:</td>
										</td>
										<tr class="input">
											<td><input type="text" name="telephone_number" class="input_contact" value="<?php if(isset($telephone_number)){ echo $telephone_number; }?>"/></td>
											<td><input type="text" name="mobile_number" class="input_contact" value="<?php if(isset($mobile_number)){ echo $mobile_number; }?>"/></td>
											<td colspan="2"><input type="text" name="email" class="input_email" value="<?php if(isset($email)){ echo $email; }?>"/></td>
										</td>
										<tr class="label">
											<td>Social Security No.:</td>
											<td>Tax Identification No.:</td>
											<td></td>
											<td></td>
										</tr>
										<tr class="input">
											<td>
												<input type="text" name="sss_1" class="input_sss1" maxlength="2" value="<?php if(isset($sss_1)){ echo $sss_1; }?>"/> - 
												<input type="text" name="sss_2" class="input_sss2" maxlength="7" value="<?php if(isset($sss_2)){ echo $sss_2; }?>"/> - 
												<input type="text" name="sss_3" class="input_sss3" maxlength="1" value="<?php if(isset($sss_3)){ echo $sss_3; }?>"/>
											</td>
											<td>
												<input type="text" name="tax_1" class="input_tax" maxlength="3" value="<?php if(isset($tax_1)){ echo $tax_1; }?>"/> - 
												<input type="text" name="tax_2" class="input_tax" maxlength="3" value="<?php if(isset($tax_2)){ echo $tax_2; }?>"/> - 
												<input type="text" name="tax_3" class="input_tax" maxlength="3" value="<?php if(isset($tax_3)){ echo $tax_3; }?>"/> - 
												<input type="text" name="tax_4" class="input_tax" maxlength="3" value="<?php if(isset($tax_4)){ echo $tax_4; }?>"/>
											</td>
											<td></td>
											<td></td>
										</tr>
										<tr class="label">
											<td>* Date of Entry:</td>
											<td>* Employee Status:</td>
											<td></td>
											<td></td>
										</tr>
										<tr class="input">
											<td colspan="1">
												<select name="entry_mm">
													<option value="-1">Month</option>
													<?php 
														$ctr = 0;
														foreach($months as $month){
															$ctr++;
															if(isset($entry_mm)){
																if($entry_mm == $ctr){
																	echo "<option value=\"" . ($ctr) . "\" selected=\"selected=\">" . $month . "</option>";
																} else {
																	echo "<option value=\"" . ($ctr) . "\">" . $month . "</option>";
																}
															} else {
																echo "<option value=\"" . ($ctr) . "\">" . $month . "</option>";
															}
														}
													?>
												</select> - 
												<select name="entry_dd">
													<option value="-1">Day</option>
													<?php 
														$ctr = 0;
														for($ctr = 0; $ctr < 31; $ctr++){
															if(isset($entry_dd)){
																if($entry_dd == ($ctr+1)){
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
												<select name="entry_yyyy">
													<option value="-1">Year</option>
													<?php 
														$ctr = 0;
														$year_last = date("Y", time());
														for($ctr = $year_last; $ctr > 1950 ; $ctr--){
															if(isset($entry_yyyy)){
																if($entry_yyyy == ($ctr)){
																	echo "<option value=\"" . ($ctr) . "\" selected=\"selected\">" . ($ctr) . "</option>";
																} else {
																	echo "<option value=\"" . ($ctr) . "\">" . ($ctr) . "</option>";
																}
															} else {
																echo "<option value=\"" . ($ctr) . "\">" . ($ctr) . "</option>";
															}
														}
													?>
												</select>
											</td>
											<td>
												<select name="employment_status" class="employment_status">
													<option value="-1"></option>
													<?php
														foreach($employee_statuses as $status){
															if(isset($employment_status)){
																if($employment_status == $status->status_id){
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
											<td></td>
										</tr>
										<tr class="input">
											<td colspan="4"></td>
										</tr>
									</table>	
									<div id="educational_attainment">
										<h2>EDUCATIONAL ATTAINMENT</h2>
										<table class="form employee" cellspacing="0">
											<?php // ### SCHOOL, DEGREE and YEAR ?>
											<tr class="label">
												<td colspan="2">School:</td>
												<td>Degree:</td>
												<td>Year:</td>
											</tr>
											<tr class="input">
												<td colspan="2"><input type="text" name="educ_school" class="input_school" value="<?php if(isset($educ_school)){ echo $educ_school; }?>"/></td>
												<td><input type="text" name="educ_degree" class="input_degree" value="<?php if(isset($educ_degree)){ echo $educ_degree; }?>"/></td>
												<td><input type="text" name="educ_year" class="input_year" value="<?php if(isset($educ_year)){ echo $educ_year; }?>"/></td>
											</tr>
										</table>
									</div>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Cancel" onclick="window.location='employment-employee.php';"/>
												<input type="submit" class="button" name="employee_save" value="Add Employee" />
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