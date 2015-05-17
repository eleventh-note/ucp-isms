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
		
	//###### CHECK IF ID IS FOUND ELSE REDIRECT
	if(isset($_GET['id'])){
		$id = (int) $_GET['id'];
		if($id <= 0){
			header("Location: employment-employee.php");
			exit();
		} else {
			//# GET INFORMATION
			$employees = $emp->GetEmployees($id);

			if(sizeof($employees) == 0){
				//redirect if nothing is found
				$_SESSION['error'] = array("Employee record not found.");
				header("Location: employment-employee.php");
				exit();
			} else {
				$employee = $employees[0];
				//## GET EDUCATIONAL ATTAINMENT
				$educations = $emp->GetEducationalAttainment($id);
				if(sizeof($educations) > 0){
					$education = $educations[0];
				}
							
				//## Transferring data to variables
				$employee_number = $employee->employee_number;
				$last_name = $employee->last_name;
				$middle_name = $employee->middle_name;
				$first_name = $employee->first_name;
				
				$birthday = $employee->birthday;
				if($birthday != '1970-01-01'){ 
					$birthday = explode("-", $birthday); 
					$bday_yyyy = $birthday[0];
					$bday_mm = $birthday[1];
					$bday_dd = $birthday[2];
					$birthday = mktime(0,0,0, $bday_mm, $bday_dd, $bday_yyyy);
					$birthday = date("F d, Y", $birthday);
				}				
				$_gender = $employee->gender;
				$marital_status = $employee->marital_status;
				$city_address = $employee->city_address;
				$provincial_address = $employee->provincial_address;
				$telephone_number = $employee->telephone_number;
				$mobile_number = $employee->mobile_number;
				$email = $employee->email_address;
				$date_of_entry = $employee->date_of_entry;
				
				if($date_of_entry != '1970-01-01'){ 
					$date_of_entry = explode("-", $date_of_entry); 
					$entry_yyyy = $date_of_entry[0];
					$entry_mm = $date_of_entry[1];
					$entry_dd = $date_of_entry[2];
					$date_of_entry = mktime(0,0,0, $entry_mm, $entry_dd, $entry_yyyy);
					$date_of_entry = date("F d, Y", $date_of_entry);
				}
				
				$employment_status = $employee->employee_status;
				$sss = $employee->sss;
				$tax = $employee->tin;
				
				if(isset($education)){
					$educ_school = $education->school;
					$educ_degree = $education->degree;
					$educ_year = $education->year;
				}
				
			}
		}
	}
	
	$months = $opt->GetMonths(2);
	$positions = $hnd->GetPositions();
	
	$genders = $gen->GetGenders($_gender);
	$gender = $genders[0];
	
	$marital_statuses = $gen->GetMaritalStatus($marital_status);
	$marital_status = $marital_statuses[0];
	
	$employee_statuses = $emp->GetEmploymentStatuses($employment_status);
	$employee_status = $employee_statuses[0];
	
	$employee_count = $emp->GetEmployeeCount();
	
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
								<span class="Highlight">Employee Administration &raquo; Manage Employees &raquo; View Information</span> 
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
									<h2>EMPLOYEE INFORMATION</h2>
									<table class="form employee" cellspacing="0">
										<tr class="info">
											<td>Employee Number</td>
											<td class="column">:</td>
											<td><?php echo $employee_number; ?></td>
											<td></td>
										</tr>
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
											<td>Gender</td>
											<td class="column">:</td>
											<td><?php echo $gender->description; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Marital Status</td>
											<td class="column">:</td>
											<td><?php echo $marital_status->description; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>City Address</td>
											<td class="column">:</td>
											<td><?php if(isset($city_address)){ echo $city_address; }?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Provincial Address</td>
											<td class="column">:</td>
											<td><?php if(isset($provincial_address)){ echo $provincial_address; }?></td>
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
											<td>Email Address</td>
											<td class="column">:</td>
											<td><?php if(isset($email)){ echo $email; }?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Social Security Number</td>
											<td class="column">:</td>
											<td><?php if(isset($sss)){ echo $sss; }?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Tax Identication Number</td>
											<td class="column">:</td>
											<td><?php if(isset($tax)){ echo $tax; }?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Date of Entry</td>
											<td class="column">:</td>
											<td><?php if(isset($date_of_entry)){ echo $date_of_entry; }?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Employee Status</td>
											<td class="column">:</td>
											<td><?php if(isset($employee_status)){ echo $employee_status->description; }?></td>
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
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Back" onclick="window.location='employment-employee.php';"/>
												<input type="button" class="button" onclick="window.location='employment-employee-edit.php?id=<?php echo $id; ?>';" value="Edit Information" />
												<input type="button" class="button" onclick="
														if(confirm('Are you sure you want to delete this employee? Click OK to continue.')){
															window.location='employment-employee-process.php?id=<?php echo $id; ?>&action=delete';
														}
													" value="Delete Employee" />
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