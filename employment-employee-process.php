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
	require_once(CLASSLIST . "auditor.inc.php");
	require_once(CLASSLIST . "audit.inc.php");
	require_once(CLASSLIST . "user.inc.php");
	require_once(CLASSLIST . "sentry.inc.php");
	require_once(CLASSLIST . "menu.inc.php");
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "emp.inc.php");
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
	//modification - 20130706 - Audit Trail
	$auditor = new AuditTrail($conn);
	$audit = new Audit();
	$audit->userId = $UserInfo->id;
	$audit->tableName = "sch-employees";
	$audit->newValue = "EmployeeNumber:{0}; LastName:{1}; FirstName:{2}; MiddleName:{3}; Birthday:{4}; MobileNumber:{5}; SocialSecurityNumber:{6}; TaxIdentificationNumber:{7}; EmployeeStatus:{8}; ";
	
	//### ADD EMPLOYEE
	if(isset($_POST['employee_save'])){
		$action = $_POST['employee_save'];
		
		switch($action){
			case 'Add Employee':
				$hnd = new EmployeeManager($conn);
				
				//## EMPLOYEE NUMBER
				$employee_number = $_POST['employee_number'];
				
				//## NAME
				$last_name = ucwords($_POST['last_name']);
				$first_name = ucwords($_POST['first_name']);
				$middle_name = ucwords($_POST['middle_name']);

				//## BIRTHDAY
				$birthday = mktime(0,0,0,$_POST['bday_mm'], $_POST['bday_dd'], $_POST['bday_yyyy']);
				$birthday = date("Y-m-d", $birthday);
				
				//## GENDER & MARITAL STATUS
				$gender = (int) $_POST['gender'];
				$marital_status = (int) $_POST['marital_status'];
				
				//## CITY ADDRESS & PROVINCIAL ADDRESS
				$city_address = $_POST['city_address'];
				$provincial_address = $_POST['provincial_address'];
				
				//## TELEPHONE NUMBER, MOBILE NUMBER & EMAIL
				$telephone_number = $_POST['telephone_number'];
				$mobile_number = $_POST['mobile_number'];
				$email = $_POST['email'];
				
				//## SSS & TAX
				$sss_1 = $_POST['sss_1'];
				$sss_2 = $_POST['sss_2'];
				$sss_3 = $_POST['sss_3'];
				$sss = $sss_1 . $sss_2 . $sss_3;
				
				$tax_1 = $_POST['tax_1'];
				$tax_2 = $_POST['tax_2'];
				$tax_3 = $_POST['tax_3'];
				$tax_4 = $_POST['tax_4'];
				$tax = $tax_1 . $tax_2 . $tax_3 . $tax_4;
				
				//## DATE OF ENTRY
				$date_of_entry = mktime(0,0,0,$_POST['entry_mm'], $_POST['entry_dd'], $_POST['entry_yyyy']);
				$date_of_entry = date("Y-m-d", $date_of_entry);
				
				//## EMPLOYMENT STATUS
				$employment_status = (int) $_POST['employment_status'];
				
				//############################
				//## EDUCATIONAL ATTAINMENT ##
				//############################
				
				$educ_school = $_POST['educ_school'];
				$educ_degree = $_POST['educ_degree'];
				$educ_year = $_POST['educ_year'];
						
				if($hnd->AddEmployee(
					$employee_number, $last_name, $first_name, $middle_name, 
					$birthday, $email, $city_address, $provincial_address,
					$gender, $marital_status, $telephone_number, $mobile_number,
					$sss, $tax, $date_of_entry, $employment_status, '',''
				) == true){;
				
					$hnd->AddEducationalAttainment($hnd->employee_id, $educ_school, $educ_degree, $educ_year);				
					
					//modification - 20130706 - Audit Trail
					$audit->action = "Add Employee";
					$audit->newValue = str_replace("{0}", $employee_number, $audit->newValue);
					$audit->newValue = str_replace("{1}", $last_name, $audit->newValue);
					$audit->newValue = str_replace("{2}", $first_name, $audit->newValue);
					$audit->newValue = str_replace("{3}", $middle_name, $audit->newValue);
					$audit->newValue = str_replace("{4}", $birthday, $audit->newValue);
					$audit->newValue = str_replace("{5}", $mobile_number, $audit->newValue);
					$audit->newValue = str_replace("{6}", $sss, $audit->newValue);
					$audit->newValue = str_replace("{7}", $tax, $audit->newValue);
					$audit->newValue = str_replace("{8}", $employment_status, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "Employee successfully added!";
					header("Location: employment-employee.php");
					exit();
						
				} else {
				
					$_SESSION['last_name'] = $last_name;
					$_SESSION['first_name'] = $first_name;
					$_SESSION['middle_name'] = $middle_name;
					$_SESSION['birthday'] = $birthday;
					$_SESSION['gender'] = $gender;
					$_SESSION['marital_status'] = $marital_status;
					$_SESSION['city_address'] = $city_address;
					$_SESSION['provincial_address'] = $provincial_address;
					$_SESSION['telephone_number'] = $telephone_number;
					$_SESSION['mobile_number'] = $mobile_number;
					$_SESSION['email'] = $email;
					$_SESSION['sss_1'] = $sss_1;
					$_SESSION['sss_2'] = $sss_2;
					$_SESSION['sss_3'] = $sss_3;
					$_SESSION['tax_1'] = $tax_1;
					$_SESSION['tax_2'] = $tax_2;
					$_SESSION['tax_3'] = $tax_3;
					$_SESSION['tax_4'] = $tax_4;
					$_SESSION['date_of_entry'] = $date_of_entry;
					$_SESSION['employment_status'] = $employment_status;
					
					$_SESSION['educ_school'] = $educ_school;
					$_SESSION['educ_year'] = $educ_year;
					$_SESSION['educ_degree'] = $educ_degree;
					
					$_SESSION['error'] = $hnd->error;
					header("Location: employment-employee-add.php");
					exit();
				}
				break;
				
			//## SAVE INFORMATION [EDIT MODE]
			case 'Save Information':
			
				$hnd = new EmployeeManager($conn);
				
				//## EMPLOYEE ID
				$employee_id = (int) $_POST['employee_id'];

				//## EMPLOYEE NUMBER
				$employee_number = $_POST['employee_number'];
				
				//## NAME
				$last_name = ucwords($_POST['last_name']);
				$first_name = ucwords($_POST['first_name']);
				$middle_name = ucwords($_POST['middle_name']);

				//## BIRTHDAY
				$birthday = mktime(0,0,0,$_POST['bday_mm'], $_POST['bday_dd'], $_POST['bday_yyyy']);
				$birthday = date("Y-m-d", $birthday);
				
				//## GENDER & MARITAL STATUS
				$gender = (int) $_POST['gender'];
				$marital_status = (int) $_POST['marital_status'];
				
				//## CITY ADDRESS & PROVINCIAL ADDRESS
				$city_address = $_POST['city_address'];
				$provincial_address = $_POST['provincial_address'];
				
				//## TELEPHONE NUMBER, MOBILE NUMBER & EMAIL
				$telephone_number = $_POST['telephone_number'];
				$mobile_number = $_POST['mobile_number'];
				$email = $_POST['email'];
				
				//## SSS & TAX
				$sss_1 = $_POST['sss_1'];
				$sss_2 = $_POST['sss_2'];
				$sss_3 = $_POST['sss_3'];
				$sss = $sss_1 . $sss_2 . $sss_3;
				
				$tax_1 = $_POST['tax_1'];
				$tax_2 = $_POST['tax_2'];
				$tax_3 = $_POST['tax_3'];
				$tax_4 = $_POST['tax_4'];
				$tax = $tax_1 . $tax_2 . $tax_3 . $tax_4;
				
				//## DATE OF ENTRY
				$date_of_entry = mktime(0,0,0,$_POST['entry_mm'], $_POST['entry_dd'], $_POST['entry_yyyy']);
				$date_of_entry = date("Y-m-d", $date_of_entry);
								
				//## EMPLOYMENT STATUS
				$employment_status = (int) $_POST['employment_status'];
				
				//############################
				//## EDUCATIONAL ATTAINMENT ##
				//############################
				
				$educ_school = $_POST['educ_school'];
				$educ_degree = $_POST['educ_degree'];
				$educ_year = $_POST['educ_year'];
						
				if($hnd->EditEmployee(
					$employee_id, $employee_number, $last_name, $first_name, $middle_name, 
					$birthday, $email, $city_address, $provincial_address,
					$gender, $marital_status, $telephone_number, $mobile_number,
					$sss, $tax, $date_of_entry, $employment_status, '',''
				) == true){;
				
					$hnd->EditEducationalAttainment($employee_id, $educ_school, $educ_degree, $educ_year);
					
					//modification - 20130706 - Audit Trail
					$audit->action = "Update Employee";
					$audit->newValue = str_replace("{0}", $employee_number, $audit->newValue);
					$audit->newValue = str_replace("{1}", $last_name, $audit->newValue);
					$audit->newValue = str_replace("{2}", $first_name, $audit->newValue);
					$audit->newValue = str_replace("{3}", $middle_name, $audit->newValue);
					$audit->newValue = str_replace("{4}", $birthday, $audit->newValue);
					$audit->newValue = str_replace("{5}", $mobile_number, $audit->newValue);
					$audit->newValue = str_replace("{6}", $sss, $audit->newValue);
					$audit->newValue = str_replace("{7}", $tax, $audit->newValue);
					$audit->newValue = str_replace("{8}", $employment_status, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "Employee information successfully saved!";
					header("Location: employment-employee-view.php?id={$employee_id}");
					exit();
						
				} else {
				
					$_SESSION['last_name'] = $last_name;
					$_SESSION['first_name'] = $first_name;
					$_SESSION['middle_name'] = $middle_name;
					$_SESSION['birthday'] = $birthday;
					$_SESSION['gender'] = $gender;
					$_SESSION['marital_status'] = $marital_status;
					$_SESSION['city_address'] = $city_address;
					$_SESSION['provincial_address'] = $provincial_address;
					$_SESSION['telephone_number'] = $telephone_number;
					$_SESSION['mobile_number'] = $mobile_number;
					$_SESSION['email'] = $email;
					$_SESSION['sss_1'] = $sss_1;
					$_SESSION['sss_2'] = $sss_2;
					$_SESSION['sss_3'] = $sss_3;
					$_SESSION['tax_1'] = $tax_1;
					$_SESSION['tax_2'] = $tax_2;
					$_SESSION['tax_3'] = $tax_3;
					$_SESSION['tax_4'] = $tax_4;
					$_SESSION['date_of_entry'] = $date_of_entry;
					$_SESSION['employment_status'] = $employment_status;
					
					$_SESSION['educ_school'] = $educ_school;
					$_SESSION['educ_year'] = $educ_year;
					$_SESSION['educ_degree'] = $educ_degree;
					
					$_SESSION['error'] = $hnd->error;
					header("Location: employment-employee-edit.php?id={$employee_id}");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new EmployeeManager($conn);
		$id = (int) $_GET['id'];
		if($hnd->DeleteEmployee($id) == true){
			
			//modification - 20130706 - Audit Trail
			$audit->action = "Delete Employee";
			$audit->newValue = "EmployeeId:{0}";
			$audit->newValue = str_replace("{0}", $id, $audit->newValue);
			$auditor->Add($audit);
			
			$_SESSION['success'] = "Employee successfully deleted!";
			header("Location: employment-employee.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: employment-employee-view.php?id={$id}");
			exit();
		}
	} else {
		header("Location: employment-employee.php");
		exit();
	}
		
	$conn->Close();
	
?>