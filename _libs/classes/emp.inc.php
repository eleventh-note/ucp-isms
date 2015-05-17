<?php
	// Provides CLASSES for verifying the user identity
	// and verifiying user privileges
	// ONLINE and OFFLINE

	//REQUIREMENT:
	//	1) RtRegExpClass

	//Position
	class Position{
		public $position_id;
		public $description;

		function __construct($position_id, $description){
			$this->position_id = $position_id;
			$this->description = $description;
		}
	}

	//Educational Attainment
	class EducationalAttainment{
		public $education_id;
		public $employee;
		public $school;
		public $degree;
		public $year;

		function __construct($education_id, $employee, $school, $degree, $year){
			$this->education_id = $education_id;
			$this->employee = $employee;
			$this->school = $school;
			$this->degree = $degree;
			$this->year = $year;
		}

	}

	//Employment Status
	class EmploymentStatus{
		public $status_id;
		public $description;

		function __construct($status_id, $description){
			$this->status_id = $status_id;
			$this->description = $description;
		}
	}

	//Employee Information
	class EmployeeInfo{
		public $employee_id;
		public $employee_number; //null
		public $last_name;
		public $first_name;
		public $middle_name;
		public $birthday; //null
		public $email_address; //null
		public $city_address; //null
		public $provincial_address; //null
		public $gender; //int CONSTRAINT
		public $marital_status; //int CONSTRAINT
		public $telephone_number;
		public $mobile_number;
		public $sss;  //null
		public $tin; //null
		public $date_of_entry; //null
		public $employee_status; //int CONSTRAINT
		public $philhealth; //null
		public $pagibig; //null

		function __construct(
			$employee_id,
			$employee_number, //null
			$last_name,
			$first_name,
			$middle_name,
			$birthday, //null
			$email_address, //null
			$city_address, //null
			$provincial_address, //null
			$gender, //int CONSTRAINT
			$marital_status, //int CONSTRAINT
			$telephone_number,
			$mobile_number,
			$sss,  //null
			$tin, //null
			$date_of_entry, //null
			$employee_status, //int CONSTRAINT
			$philhealth, //null
			$pagibig //null
		){
			$this->employee_id = $employee_id;
			$this->employee_number = $employee_number;
			$this->last_name = $last_name;
			$this->first_name = $first_name;
			$this->middle_name = $middle_name;
			$this->birthday = $birthday;
			$this->email_address = $email_address;
			$this->city_address = $city_address;
			$this->provincial_address = $provincial_address;
			$this->gender = $gender;
			$this->marital_status = $marital_status;
			$this->telephone_number = $telephone_number;
			$this->mobile_number = $mobile_number;
			$this->sss = $sss;
			$this->tin = $tin;
			$this->date_of_entry = $date_of_entry;
			$this->employee_status = $employee_status;
			$this->philhealth = $philhealth;
			$this->pagibig = $pagibig;
		}
	}

	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class EmployeeManager{

		public $error = array();
		public $error_count = 0;

		private $conn = null;

		//last employee insert id
		public $employee_id = 0;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}

		/*--------------------------------------------------------

			EMPLOYMENT STATUS [ SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetEmploymentStatuses($status_id = null){
			$statuses = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT StatusID, Description FROM `sch-employment_status` ";

				if($status_id != null){
					$query .= "WHERE ";
					$query .= "StatusID=";
					$query .= $status_id;
					$query .= " ";
				}

				$query .= "ORDER BY StatusID ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$statuses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$statuses[$ctr] = new EmploymentStatus($row['StatusID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $statuses;
		}

		//Add Employment Status to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddEmploymentStatus($description){

			//clean input
			$description = addslashes(strip_tags($description));

			$result = false;

			if($description == ""){
				$this->error[sizeof($this->error)] = "Description cannot be blank!";
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-employment_status`(Description) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "')";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding employment status. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Edit Employment Status in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditEmploymentStatus($status_id, $description){

			//clean input
			$description = addslashes(strip_tags($description));

			if($description == ""){
				$this->error[sizeof($this->error)] = "Description cannot be blank!";
			}

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-employment_status` SET Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE StatusID=";
					$query .= $status_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error editing employment status. Duplicate found!";
						} 	else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}
			return $result;

		}

		//Delete Employment Status in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteEmploymentStatus($status_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-employment_status` ";
				$query .= "WHERE StatusID=";
				$query .= $status_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting employment status. Information in use.";
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			EMPLOYEE [ SELECT | INSERT | UPDATE | DELETE ]
			- ADD | EDIT | DELETE | SEARCH | PAGINATION

		---------------------------------------------------------*/

		//returns the number of employees
		//-->can be filtered by {keyword}
		function GetEmployeeCount($keyword=null){
			$count = 0;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
				$conn = $this->conn;

				$query = "SELECT COUNT(*) AS Total FROM `sch-employees` ";

				if($keyword != null){
					$query .= "WHERE (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= "OR EmployeeNumber LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				$query .= "ORDER BY LastName";

				$result = $conn->query($query);
				echo $conn->error;
				if($conn->error == ''){
					if($result->num_rows){
						$row = $result->fetch_assoc();
						$count = $row['Total'];
					}
				}

			}

			return $count;
		}

		//Will always return null for errors else an array
		function GetEmployees(
			$employee_id = null,
			$keyword = null,
			$page = null,
			$item_count = null
		){
			$employees = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				//Get all info if specifics
				if($employee_id != null){
					$query = "SELECT ";
					$query .= "EmployeeID, EmployeeNumber, ";
					$query .= "LastName, FirstName, ";
					$query .= "MiddleName, ";
					$query .= "Birthday, ";
					$query .= "EmailAddress, ";
					$query .= "CityAddress, ";
					$query .= "ProvincialAddress, ";
					$query .= "Gender, MaritalStatus, ";
					$query .= "TelephoneNumber, ";
					$query .= "MobileNumber, ";
					$query .= "SocialSecurityNumber, ";
					$query .= "TaxIdentificationNumber, ";
					$query .= "DateOfEntry, ";
					$query .= "EmployeeStatus, ";
					$query .= "PhilHealthNumber, ";
					$query .= "PagibigNumber ";
					$query .= "FROM `sch-employees` ";
					$query .= "WHERE EmployeeID={$employee_id} ";
				} else {
					//for display on searches
					$query = "SELECT ";
					$query .= "EmployeeID, EmployeeNumber, ";
					$query .= "LastName, FirstName, ";
					$query .= "MiddleName, ";
					$query .= "Birthday, ";
					$query .= "EmailAddress, ";
					$query .= "CityAddress, ";
					$query .= "ProvincialAddress, ";
					$query .= "Gender, MaritalStatus, ";
					$query .= "TelephoneNumber, ";
					$query .= "MobileNumber, ";
					$query .= "SocialSecurityNumber, ";
					$query .= "TaxIdentificationNumber, ";
					$query .= "DateOfEntry, ";
					$query .= "es.Description AS EmployeeStatus, ";
					$query .= "PhilHealthNumber, ";
					$query .= "PagibigNumber ";
					$query .= "FROM `sch-employees` e ";
					$query .= "LEFT JOIN `sch-employment_status` es ON es.StatusID=e.EmployeeStatus ";
					$query .= "WHERE 1=1 ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= "OR EmployeeNumber LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				$query .= "ORDER BY LastName ";

				if($page > 0 && $item_count > 0){
					 $limit = $item_count;
					 $start = ($page-1) * $item_count;
					 $query .= "LIMIT {$start}, {$limit} ";
				}

				$result = $conn->query($query);

				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$employees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$employees[$ctr] = new EmployeeInfo(
								$row['EmployeeID'], $row['EmployeeNumber'],
								$row['LastName'], $row['FirstName'],
								$row['MiddleName'], $row['Birthday'],
								$row['EmailAddress'], $row['CityAddress'],
								$row['ProvincialAddress'], $row['Gender'],
								$row['MaritalStatus'], $row['TelephoneNumber'],
								$row['MobileNumber'], $row['SocialSecurityNumber'],
								$row['TaxIdentificationNumber'], $row['DateOfEntry'],
								$row['EmployeeStatus'], $row['PhilHealthNumber'],
								$row['PagibigNumber']
							);

							$ctr++;
						}
					}
				}
			}

			return $employees;
		}

		function GetEmployeesWithoutUser(
			$employee_id = null,
			$keyword = null,
			$page = null,
			$item_count = null
		){
			$employees = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

					//for display on searches
					$query = "SELECT ";
					$query .= "EmployeeID, EmployeeNumber, ";
					$query .= "LastName, FirstName, ";
					$query .= "MiddleName, ";
					$query .= "Birthday, ";
					$query .= "EmailAddress, ";
					$query .= "CityAddress, ";
					$query .= "ProvincialAddress, ";
					$query .= "Gender, MaritalStatus, ";
					$query .= "TelephoneNumber, ";
					$query .= "MobileNumber, ";
					$query .= "SocialSecurityNumber, ";
					$query .= "TaxIdentificationNumber, ";
					$query .= "DateOfEntry, ";
					$query .= "es.Description AS EmployeeStatus, ";
					$query .= "PhilHealthNumber, ";
					$query .= "PagibigNumber ";
					$query .= "FROM `sch-employees` e ";
					$query .= "LEFT JOIN `sch-employment_status` es ON es.StatusID=e.EmployeeStatus ";
					$query .= "WHERE 1=1 ";

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= "OR EmployeeNumber LIKE '%{$keyword}%' ";
					$query .= ") ";
				}

				//$query .= "AND (SELECT COUNT(*) FROM `adm-users` au WHERE au.EmployeeID=e.EmployeeID) = 0 OR e.EmployeeID={$employee_id} ";
				$query .= "ORDER BY LastName ";

				if($page > 0 && $item_count > 0){
					 $limit = $item_count;
					 $start = ($page-1) * $item_count;
					 $query .= "LIMIT {$start}, {$limit} ";
				}

				$result = $conn->query($query);

				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$employees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$employees[$ctr] = new EmployeeInfo(
								$row['EmployeeID'], $row['EmployeeNumber'],
								$row['LastName'], $row['FirstName'],
								$row['MiddleName'], $row['Birthday'],
								$row['EmailAddress'], $row['CityAddress'],
								$row['ProvincialAddress'], $row['Gender'],
								$row['MaritalStatus'], $row['TelephoneNumber'],
								$row['MobileNumber'], $row['SocialSecurityNumber'],
								$row['TaxIdentificationNumber'], $row['DateOfEntry'],
								$row['EmployeeStatus'], $row['PhilHealthNumber'],
								$row['PagibigNumber']
							);

							$ctr++;
						}
					}
				}
			}

			return $employees;
		}

		//Returns Employees that are not assigned as faculty
		function GetUnassignedEmployees(
			$employee_id = null,
			$keyword = null,
			$page = null,
			$item_count = null
		){
			$employees = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				//Get all info if specifics
				if($employee_id != null){
					$query = "SELECT ";
					$query .= "EmployeeID, EmployeeNumber, ";
					$query .= "LastName, FirstName, ";
					$query .= "MiddleName, ";
					$query .= "Birthday, ";
					$query .= "EmailAddress, ";
					$query .= "CityAddress, ";
					$query .= "ProvincialAddress, ";
					$query .= "Gender, MaritalStatus, ";
					$query .= "TelephoneNumber, ";
					$query .= "MobileNumber, ";
					$query .= "SocialSecurityNumber, ";
					$query .= "TaxIdentificationNumber, ";
					$query .= "DateOfEntry, ";
					$query .= "EmployeeStatus, ";
					$query .= "PhilHealthNumber, ";
					$query .= "PagibigNumber ";
					$query .= "FROM `sch-employees` ";
					$query .= "WHERE EmployeeID={$employee_id} AND IsFaculty=0 ";
				} else {
					//for display on searches
					$query = "SELECT ";
					$query .= "EmployeeID, EmployeeNumber, ";
					$query .= "LastName, FirstName, ";
					$query .= "MiddleName, ";
					$query .= "Birthday, ";
					$query .= "EmailAddress, ";
					$query .= "CityAddress, ";
					$query .= "ProvincialAddress, ";
					$query .= "Gender, MaritalStatus, ";
					$query .= "TelephoneNumber, ";
					$query .= "MobileNumber, ";
					$query .= "SocialSecurityNumber, ";
					$query .= "TaxIdentificationNumber, ";
					$query .= "DateOfEntry, ";
					$query .= "es.Description AS EmployeeStatus, ";
					$query .= "PhilHealthNumber, ";
					$query .= "PagibigNumber ";
					$query .= "FROM `sch-employees` e ";
					$query .= "LEFT JOIN `sch-employment_status` es ON es.StatusID=e.EmployeeStatus ";
					$query .= "WHERE 1=1 AND IsFaculty=0 ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= "OR EmployeeNumber LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				$query .= "ORDER BY LastName ";

				if($page > 0 && $item_count > 0){
					 $limit = $item_count;
					 $start = ($page-1) * $item_count;
					 $query .= "LIMIT {$start}, {$limit} ";
				}

				$result = $conn->query($query);

				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$employees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$employees[$ctr] = new EmployeeInfo(
								$row['EmployeeID'], $row['EmployeeNumber'],
								$row['LastName'], $row['FirstName'],
								$row['MiddleName'], $row['Birthday'],
								$row['EmailAddress'], $row['CityAddress'],
								$row['ProvincialAddress'], $row['Gender'],
								$row['MaritalStatus'], $row['TelephoneNumber'],
								$row['MobileNumber'], $row['SocialSecurityNumber'],
								$row['TaxIdentificationNumber'], $row['DateOfEntry'],
								$row['EmployeeStatus'], $row['PhilHealthNumber'],
								$row['PagibigNumber']
							);

							$ctr++;
						}
					}
				}
			}

			return $employees;
		}

		//Returns Employees that are not assigned as faculty
		function GetAssignedEmployees(
			$employee_id = null,
			$keyword = null,
			$page = null,
			$item_count = null
		){
			$employees = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				//Get all info if specifics
				if($employee_id != null){
					$query = "SELECT ";
					$query .= "EmployeeID, EmployeeNumber, ";
					$query .= "LastName, FirstName, ";
					$query .= "MiddleName, ";
					$query .= "Birthday, ";
					$query .= "EmailAddress, ";
					$query .= "CityAddress, ";
					$query .= "ProvincialAddress, ";
					$query .= "Gender, MaritalStatus, ";
					$query .= "TelephoneNumber, ";
					$query .= "MobileNumber, ";
					$query .= "SocialSecurityNumber, ";
					$query .= "TaxIdentificationNumber, ";
					$query .= "DateOfEntry, ";
					$query .= "EmployeeStatus, ";
					$query .= "PhilHealthNumber, ";
					$query .= "PagibigNumber ";
					$query .= "FROM `sch-employees` ";
					$query .= "WHERE EmployeeID={$employee_id} AND IsFaculty=1 ";
				} else {
					//for display on searches
					$query = "SELECT ";
					$query .= "EmployeeID, EmployeeNumber, ";
					$query .= "LastName, FirstName, ";
					$query .= "MiddleName, ";
					$query .= "Birthday, ";
					$query .= "EmailAddress, ";
					$query .= "CityAddress, ";
					$query .= "ProvincialAddress, ";
					$query .= "Gender, MaritalStatus, ";
					$query .= "TelephoneNumber, ";
					$query .= "MobileNumber, ";
					$query .= "SocialSecurityNumber, ";
					$query .= "TaxIdentificationNumber, ";
					$query .= "DateOfEntry, ";
					$query .= "es.Description AS EmployeeStatus, ";
					$query .= "PhilHealthNumber, ";
					$query .= "PagibigNumber ";
					$query .= "FROM `sch-employees` e ";
					$query .= "LEFT JOIN `sch-employment_status` es ON es.StatusID=e.EmployeeStatus ";
					$query .= "WHERE 1=1 AND IsFaculty=1 ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= "OR EmployeeNumber LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				$query .= "ORDER BY LastName ";

				if($page > 0 && $item_count > 0){
					 $limit = $item_count;
					 $start = ($page-1) * $item_count;
					 $query .= "LIMIT {$start}, {$limit} ";
				}

				$result = $conn->query($query);

				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$employees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$employees[$ctr] = new EmployeeInfo(
								$row['EmployeeID'], $row['EmployeeNumber'],
								$row['LastName'], $row['FirstName'],
								$row['MiddleName'], $row['Birthday'],
								$row['EmailAddress'], $row['CityAddress'],
								$row['ProvincialAddress'], $row['Gender'],
								$row['MaritalStatus'], $row['TelephoneNumber'],
								$row['MobileNumber'], $row['SocialSecurityNumber'],
								$row['TaxIdentificationNumber'], $row['DateOfEntry'],
								$row['EmployeeStatus'], $row['PhilHealthNumber'],
								$row['PagibigNumber']
							);

							$ctr++;
						}
					}
				}
			}

			return $employees;
		}

		//Add Employee to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddEmployee(
				$employee_number, //null
				$last_name,
				$first_name,
				$middle_name,
				$birthday, //null
				$email_address, //null
				$city_address, //null
				$provincial_address, //null
				$gender, //int CONSTRAINT
				$marital_status, //int CONSTRAINT
				$telephone_number, //null
				$mobile_number, //null
				$sss,  //null
				$tin, //null
				$date_of_entry, //null
				$employee_status, //int CONSTRAINT
				$philhealth, //null
				$pagibig //null
			){

			$this->error = array();
			$this->error_count = 0;

			//clean input
			$employee_number = addslashes(strip_tags($employee_number));
			$last_name = ucwords(addslashes(strip_tags($last_name)));
			$first_name = ucwords(addslashes(strip_tags($first_name)));
			$middle_name = ucwords(addslashes(strip_tags($middle_name)));
			$email_address = addslashes(strip_tags($email_address));
			$birthday = addslashes(strip_tags($birthday));
			$city_address = ucwords(addslashes(strip_tags($city_address)));
			$provincial_address = ucwords(addslashes(strip_tags($provincial_address)));
			$telephone_number = addslashes(strip_tags($telephone_number));
			$mobile_number = addslashes(strip_tags($mobile_number));
			$sss = addslashes(strip_tags($sss));
			$tin = addslashes(strip_tags($tin));
			$philhealth = addslashes(strip_tags($philhealth));
			$pagibig = addslashes(strip_tags($pagibig));

			$result = false;

			//create RegEx Hanlder
			$reg = new RtRegExp;


			if($last_name <> ''){
				if($reg->CheckName($last_name) <= 0 || $reg->CheckName($last_name) == false){
					$this->error[$this->error_count] = "Last name is in invalid format.";
					$this->error_count++;
				}
			} else {
				$this->error[$this->error_count] = "Last name cannot be empty.";
				$this->error_count++;
			}

			if($first_name <> ''){
				if($reg->CheckName($first_name) <= 0 || $reg->CheckName($first_name) == false){
					$this->error[$this->error_count] = "First name is in invalid format.";
					$this->error_count++;
				}
			} else {
				$this->error[$this->error_count] = "First name cannot be empty.";
				$this->error_count++;
			}

			if($middle_name <> ''){
				if($reg->CheckName($middle_name) <= 0 || $reg->CheckName($middle_name) == false){
					$this->error[$this->error_count] = "Middle name is in invalid format.";
					$this->error_count++;
				}
			}

			if($email_address <> ''){
				if($reg->CheckEmail($email_address) <= 0 || $reg->CheckEmail($email_address) == false){
					$this->error[$this->error_count] = "Email is in invalid format.";
					$this->error_count++;
				}
			}

			if($tin <> ''){
				if($reg->CheckTin($tin) <= 0 || $reg->CheckTin($tin) == false){
					$this->error[$this->error_count] = "TIN is in invalid format.";
					$this->error_count++;
				} else {
					$tin = $reg->formatted;
				}
			}

			if($sss <> ''){
				if($reg->CheckSss($sss) <= 0 || $reg->CheckSss($sss) == false){
					$this->error[$this->error_count] = "SSS Number is in invalid format.";
					$this->error_count++;
				} else {
					$sss = $reg->formatted;
				}
			}

			if($gender <= 0 || $gender == ''){
				$this->error[$this->error_count] = "Gender not found.";
				$this->error_count++;
			}

			if($marital_status <= 0 || $marital_status == ''){
				$this->error[$this->error_count] = "Marital Status not found.";
				$this->error_count++;
			}

			if($employee_status <= 0 || $employee_status == ''){
				$this->error[$this->error_count] = "Employee Status not found.";
				$this->error_count++;
			}

			if($birthday == '1970-01-01'){
				$this->error[$this->error_count] = "Birthday not inputted.";
				$this->error_count++;
			}

			if($date_of_entry == '1970-01-01'){
				$this->error[$this->error_count] = "Date of entry not inputted.";
				$this->error_count++;
			}


			//check if error is found first
			//-->else, just show the result
			if(count($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-employees`(";
					$query .= "EmployeeNumber, LastName, FirstName, ";
					$query .= "MiddleName, Birthday, EmailAddress, ";
					$query .= "CityAddress, ProvincialAddress, ";
					$query .= "Gender, MaritalStatus, TelephoneNumber, ";
					$query .= "MobileNumber, SocialSecurityNumber, ";
					$query .= "TaxIdentificationNumber, DateOfEntry, ";
					$query .= "EmployeeStatus, PhilHealthNumber, ";
					$query .= "PagibigNumber, Modified";
					$query .= ") ";
					$query .= "VALUES('{$employee_number}', '{$last_name}', '{$first_name}', ";
					$query .= "'{$middle_name}', '{$birthday}', '{$email_address}', ";
					$query .= "'{$city_address}', '{$provincial_address}', {$gender}, ";
					$query .= "{$marital_status}, '{$telephone_number}', '{$mobile_number}', ";
					$query .= "'{$sss}', '{$tin}', '{$date_of_entry}', {$employee_status}, ";
					$query .= "'{$philhealth}', '{$pagibig}'";
					$query .= ",NOW()) ";

					$conn->query($query);

					if($conn->insert_id > 0){
						//# set the employee id
						$this->employee_id = $conn->insert_id;

						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$this->error_count] = "Error adding employee. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		//Edit Employee in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditEmployee(
				$employee_id,
				$employee_number, //null
				$last_name,
				$first_name,
				$middle_name,
				$birthday, //null
				$email_address, //null
				$city_address, //null
				$provincial_address, //null
				$gender, //int CONSTRAINT
				$marital_status, //int CONSTRAINT
				$telephone_number, //null
				$mobile_number, //null
				$sss,  //null
				$tin, //null
				$date_of_entry, //null
				$employee_status, //int CONSTRAINT
				$philhealth, //null
				$pagibig //null
			){

			$this->error = array();
			$this->error_count = 0;

			//clean input
			$employee_number = addslashes(strip_tags($employee_number));
			$last_name = ucwords(addslashes(strip_tags($last_name)));
			$first_name = ucwords(addslashes(strip_tags($first_name)));
			$middle_name = ucwords(addslashes(strip_tags($middle_name)));
			$email_address = addslashes(strip_tags($email_address));
			$birthday = addslashes(strip_tags($birthday));
			$city_address = ucwords(addslashes(strip_tags($city_address)));
			$provincial_address = ucwords(addslashes(strip_tags($provincial_address)));
			$telephone_number = addslashes(strip_tags($telephone_number));
			$mobile_number = addslashes(strip_tags($mobile_number));
			$sss = addslashes(strip_tags($sss));
			$tin = addslashes(strip_tags($tin));
			$philhealth = addslashes(strip_tags($philhealth));
			$pagibig = addslashes(strip_tags($pagibig));

			$result = false;

			//create RegEx Hanlder
			$reg = new RtRegExp;


			if($last_name <> ''){
				if($reg->CheckName($last_name) <= 0 || $reg->CheckName($last_name) == false){
					$this->error[$this->error_count] = "Last name is in invalid format.";
					$this->error_count++;
				}
			} else {
				$this->error[$this->error_count] = "Last name cannot be empty.";
				$this->error_count++;
			}

			if($first_name <> ''){
				if($reg->CheckName($first_name) <= 0 || $reg->CheckName($first_name) == false){
					$this->error[$this->error_count] = "First name is in invalid format.";
					$this->error_count++;
				}
			} else {
				$this->error[$this->error_count] = "First name cannot be empty.";
				$this->error_count++;
			}

			if($middle_name <> ''){
				if($reg->CheckName($middle_name) <= 0 || $reg->CheckName($middle_name) == false){
					$this->error[$this->error_count] = "Middle name is in invalid format.";
					$this->error_count++;
				}
			}

			if($email_address <> ''){
				if($reg->CheckEmail($email_address) <= 0 || $reg->CheckEmail($email_address) == false){
					$this->error[$this->error_count] = "Email is in invalid format.";
					$this->error_count++;
				}
			}

			if($tin <> ''){
				if($reg->CheckTin($tin) <= 0 || $reg->CheckTin($tin) == false){
					$this->error[$this->error_count] = "TIN is in invalid format.";
					$this->error_count++;
				} else {
					$tin = $reg->formatted;
				}
			}

			if($sss <> ''){
				if($reg->CheckSss($sss) <= 0 || $reg->CheckSss($sss) == false){
					$this->error[$this->error_count] = "SSS Number is in invalid format.";
					$this->error_count++;
				} else {
					$sss = $reg->formatted;
				}
			}

			if($gender <= 0 || $gender == ''){
				$this->error[$this->error_count] = "Gender not found.";
				$this->error_count++;
			}

			if($marital_status <= 0 || $marital_status == ''){
				$this->error[$this->error_count] = "Marital Status not found.";
				$this->error_count++;
			}

			if($employee_status <= 0 || $employee_status == ''){
				$this->error[$this->error_count] = "Employee Status not found.";
				$this->error_count++;
			}

			if($birthday == '1970-01-01'){
				$this->error[$this->error_count] = "Birthday not inputted.";
				$this->error_count++;
			}

			//check if error is found first
			//-->else, just show the result
			if(count($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-employees` SET ";
					$query .= "EmployeeNumber='{$employee_number}', ";
					$query .= "LastName='{$last_name}', FirstName='{$first_name}', ";
					$query .= "MiddleName='{$middle_name}', ";
					$query .= "Birthday='{$birthday}', ";
					$query .= "EmailAddress='{$email_address}', ";
					$query .= "CityAddress='{$city_address}', ";
					$query .= "ProvincialAddress='{$provincial_address}', ";
					$query .= "Gender={$gender}, MaritalStatus={$marital_status}, ";
					$query .= "TelephoneNumber='{$telephone_number}', ";
					$query .= "MobileNumber='{$mobile_number}', ";
					$query .= "SocialSecurityNumber='{$sss}', ";
					$query .= "TaxIdentificationNumber='{$tin}', ";
					$query .= "DateOfEntry='{$date_of_entry}', ";
					$query .= "EmployeeStatus={$employee_status}, ";
					$query .= "PhilHealthNumber='{$philhealth}', ";
					$query .= "PagibigNumber='{$pagibig}', Modified=NOW() ";
					$query .= "WHERE EmployeeID={$employee_id}";

					$conn->query($query);

					if($conn->affected_rows > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error editing employee. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = "No change has been done.";
						}
					}
				}
			}

			return $result;
		}

		//Delete Employee in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteEmployee($employee_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `sch-employees` SET IsDeleted=1 ";
				$query .= "WHERE EmployeeID=";
				$query .= $employee_id;

				//### DELETE Educational Attainment
				$this->DeleteEducationalAttainment($employee_id);

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting employee. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			POSITION [ SELECT | INSERT | UPDATE | DELETE ]
			- ADD | EDIT | DELETE | SEARCH | PAGINATION

		---------------------------------------------------------*/

		function GetPositions($position_id = null, $keyword = null){
			$positions = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT PositionID, Description FROM `sch-positions` ";
				$query .= "WHERE 1=1 ";

				if($position_id != null){
					$query .= "AND ";
					$query .= "PositionID=";
					$query .= $position_id;
					$query .= " ";
				}

				if($keyword != null){
				$query .= "AND ";
					$query .= "Description LIKE '%{$keyword}%'";
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$positions = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$positions[$ctr] = new Position($row['PositionID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $positions;
		}

		function AddPosition($description){
			//clean input
			$description = trim(ucwords(addslashes(strip_tags($description))));

			$this->error = array();

			if($description == ""){
				$this->error[sizeof($this->error)] = "Position: Cannot be blank!";
			}

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				if(sizeof($this->error) == 0){
					$conn = $this->conn;

					$query = "INSERT INTO `sch-positions`(Description) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "')";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding position. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		function EditPosition($position_id, $description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			$result = false;

			$this->error = array();

			if($description == ""){
				$this->error[sizeof($this->error)] = "Position: Cannot be blank!";
			}

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				if(sizeof($this->error) == 0){
					$conn = $this->conn;

					$query = "UPDATE `sch-positions` SET Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE PositionID=";
					$query .= $position_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error editing position. Duplicate found!";
						}
					}
				}
			}

			return $result;

		}

		function DeletePosition($position_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-positions` ";
				$query .= "WHERE PositionID=";
				$query .= $position_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting position. Information in use.";
					} else {
						$this->error = "No change has been done.";
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			EDUCATIONAL ATTAINMENT [ SELECT | INSERT | UPDATE | DELETE ]
			- ADD | EDIT | DELETE | SEARCH | PAGINATION

		---------------------------------------------------------*/

		function GetEducationalAttainment($employee_id, $keyword=null){
			$attainments = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT EducationID, Employee, School, Degree, Year FROM `sch-educational_attainment` ";
				$query .= "WHERE 1=1 ";

				if($employee_id != null){
					$query .= "AND ";
					$query .= "Employee=";
					$query .= $employee_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND ";
					$query .= "(Degree LIKE '%{$keyword}%'";
					$query .= " OR ";
					$query .= "School LIKE '%{$keyword}%')";
				}

				$query .= "ORDER BY Year ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$attainments = array();
					$ctr = 0;
					if($result->num_rows > 0){

						while($row = $result->fetch_assoc()){
							$attainments[$ctr] = new EducationalAttainment(
												$row['EducationID'],
												$row['Employee'],
												$row['School'],
												$row['Degree'],
												$row['Year']
												);
							$ctr++;
						}
					}
				}
			}

			return $attainments;
		}

		//Check EducationalAttainment if valid
		function CheckEducationalAttainment($school, $degree, $year){
			//clean input
			$school = ucwords(addslashes(strip_tags($school)));
			$degree = ucwords(addslashes(strip_tags($degree)));
			$year = (int) $year;

			if($year <= 1900){
				$this->error[sizeof($this->error)] = "Invalid year.";
			}

		}

		function AddEducationalAttainment($employee_id, $school, $degree, $year){
			//clean input
			$school = ucwords(addslashes(strip_tags($school)));
			$degree = ucwords(addslashes(strip_tags($degree)));
			$year = (int) $year;

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `sch-educational_attainment`(Employee, School, Degree, Year) ";
				$query .= "VALUES(";
				$query .= $employee_id;
				$query .= ",'";
				$query .= $school;
				$query .= "','";
				$query .= $degree;
				$query .= "',";
				$query .= $year;
				$query .= ")";

				$conn->query($query);

				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error adding educational attainment. Duplicate found!";
					}
				}
			}

			return $result;
		}

		function EditEducationalAttainment($employee_id, $school, $degree, $year){

			///clean input
			$school = ucwords(addslashes(strip_tags($school)));
			$degree = ucwords(addslashes(strip_tags($degree)));

			$result = false;

			if($year <= 1900){
				$this->error = "Invalid year.";
			}elseif($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `sch-educational_attainment` ";
				$query .= "SET ";
				$query .= "School='{$school}', ";
				$query .= "Degree='{$degree}', ";
				$query .= "Year={$year} ";
				$query .= "WHERE Employee=";
				$query .= $employee_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error editing educational attainment. Duplicate found!";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		function DeleteEducationalAttainment($employee_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-educational_attainment` ";
				$query .= "WHERE Employee=";
				$query .= $employee_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting educational attainment. Information in use.";
					} else {
						$this->error = "No change has been done.";
					}
				}
			}

			return $result;

		}
	}


?>
