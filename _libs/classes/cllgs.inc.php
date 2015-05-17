<?php
	/*-----------------------------------------------

		COLLEGES
		---------

		FUNCTIONS:
			1) Faculty Management
			2) Course Management
			3) College Management
				a) Assign College Admins
				b) Assign Faculties

		 REQUIREMENTS:
			1) dvsns.inc.php
			2) emp.inc.php
	-------------------------------------------------*/

	//Graduate, Undergraduate, etc...
	class CollegeType{
		public $type_id;
		public $description;

		function __construct($type_id, $description){
			$this->type_id = $type_id;
			$this->description = $description;
		}
	}

	class College{
		public $college_id;
		public $division; //division class dvsns.inc.php
		public $code;
		public $description;
		public $created;
		public $modified;
		public $college_type; //college type class

		function __construct($college_id=null, $division=null, $code=null, $description=null, $created=null, $modified=null, $college_type=null){
			$this->college_id = $college_id;
			$this->division = $division;
			$this->code = $code;
			$this->description = $description;
			$this->created = $created;
			$this->modified = $modified;
			$this->college_type = $college_type;
		}
	}

	//College Admins
	class CollegeAdmin{
		public $admin_id;
		public $position; //class Position
		public $employee; //class Employee
		public $college; //class College
		public $created; //DATETIME
		public $modified; //DATETIME
	}

	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above;
	class CollegeManager{
		public $error = array();

		private $conn = null;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}

		/*--------------------------------------------------------

			COLLEGE TYPE [ SELECT | INSERT | UPDATE | DELETE ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetCollegeTypes($type_id = null){
			$types = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT TypeID, Description FROM `sch-college_type` ";

				if($type_id != null){
					$query .= "WHERE ";
					$query .= "TypeID=";
					$query .= $type_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$types = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$types[$ctr] = new CollegeType($row['TypeID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $types;
		}

		//Will always return null for errors else an array
		function GetCollegeTypesByKey($type_id = null){
			$types = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT TypeID, Description FROM `sch-college_type` ";

				if($type_id != null){
					$query .= "WHERE ";
					$query .= "TypeID=";
					$query .= $type_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$types = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$types[$row['TypeID']] = new CollegeType($row['TypeID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $types;
		}

		//Add College Type to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddCollegeType($description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			$result = false;

			if($description == ""){ $this->error[sizeof($this->error)] = "College Type cannot be blank."; }

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-college_type`(Description) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "')";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding college type. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Edit College Type in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditCollegeType($type_id, $description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			if($description == ""){ $this->error[sizeof($this->error)] = "College Type cannot be blank."; }

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-college_type` SET Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE TypeID=";
					$query .= $type_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error editing college type. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Delete College Type in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteCollegeType($type_id){

			$this->error = array();
			$result = false;

			if($this->IsCollegeTypeUsed($type_id)){
				$this->error[] = "College type is in use.";
			}

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				if(sizeof($this->error) == 0){
					$conn = $this->conn;

					$query = "DELETE FROM `sch-college_type` ";
					$query .= "WHERE TypeID=";
					$query .= $type_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "a foreign key constraint fails") !== false){
							$this->error[sizeof($this->error)] = "Error deleting college type. Information in use.";
						}	else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		private function IsCollegeTypeUsed($id){
			$isUsed = false;

			$query = "SELECT * FROM `sch-colleges` WHERE CollegeType={0} ";
			$query = str_replace("{0}", $id, $query);

			$conn = $this->conn;
			$result = $conn->query($query);

			if($result->num_rows != 0){
				$isUsed = true;
			}

			return $isUsed;
		}

		/*--------------------------------------------------------

			COLLEGE [ SELECT | INSERT | UPDATE | DELETE ]

		---------------------------------------------------------*/

		function GetColleges($college_id=null, $keyword=null){
			$colleges = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CollegeID, Code, Description, ";
				$query .= "DivisionID, CollegeType, Created, Modified FROM `sch-colleges` ";

				if($college_id != null){
					$query .= "WHERE ";
					$query .= "CollegeID=";
					$query .= $college_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "WHERE ";
					$query .= "Code LIKE '%{$keyword}%' OR Description LIKE '%{$keyword}%' ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$colleges = array();
					$ctr = 0;
					if($result->num_rows > 0){
						$hndlr = new DivisionsManager($conn);

						while($row = $result->fetch_assoc()){
							$division = $hndlr->GetDivisions($row['DivisionID']);
							$college_type = $this->GetCollegeTypes($row['CollegeType']);
							$colleges[$ctr] = new College(
										$row['CollegeID'],
										$division,
										$row['Code'],
										$row['Description'],
										$row['Created'],
										$row['Modified'],
										$college_type
									);
							$ctr++;
						}
					}
				}
			}

			return $colleges;
		}

		function GetCollegesByKey($college_id=null, $keyword=null){
			$colleges = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CollegeID, Code, Description, ";
				$query .= "DivisionID, CollegeType, Created, Modified FROM `sch-colleges` ";

				if($college_id != null){
					$query .= "WHERE ";
					$query .= "CollegeID=";
					$query .= $college_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "WHERE ";
					$query .= "Code LIKE '%{$keyword}%' OR Description LIKE '%{$keyword}%' ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$colleges = array();
					$ctr = 0;
					if($result->num_rows > 0){
						$hndlr = new DivisionsManager($conn);

						while($row = $result->fetch_assoc()){
							$division = $hndlr->GetDivisions($row['DivisionID']);
							$college_type = $this->GetCollegeTypes($row['CollegeType']);
							$colleges[$row['CollegeID']] = new College(
										$row['CollegeID'],
										$division,
										$row['Code'],
										$row['Description'],
										$row['Created'],
										$row['Modified'],
										$college_type
									);
							$ctr++;
						}
					}
				}
			}

			return $colleges;
		}

		function GetCollegeDetailsByKey($college_id){
			$colleges = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$college_id = (int) $college_id;
				$query =  "SELECT CollegeID, sc.Code AS Code, sc.Description AS Description, ";
				$query .= "sd.Description, sct.Description AS CollegeType, sd.Description AS Division, Created, Modified  ";
				$query .= "FROM  `sch-colleges` sc ";
				$query .= "LEFT JOIN  `sch-college_type` sct ON sct.TypeID = sc.CollegeType ";
				$query .= "LEFT JOIN  `sch-divisions` sd ON sd.DivisionID = sc.DivisionID ";
				$query .= "WHERE CollegeID={$college_id} ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					if($result->num_rows > 0){
						$hndlr = new DivisionsManager($conn);

						while($row = $result->fetch_assoc()){
							$colleges = $row;
						}
					}
				}
			}

			return $colleges;
		}

		function AddCollege(
			$division, //int
			$code,
			$description,
			$college_type //int
		){
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = ucwords(addslashes(strip_tags($code)));
			$division = (int) $division;
			$college_type = (int) $college_type;

			$result = false;

			if($description == ""){ $this->error[sizeof($this->error)] = "Description cannot be blank."; }
			if($code == ""){ $this->error[sizeof($this->error)] = "Code cannot be blank."; }
			if($division <= 0){ $this->error[sizeof($this->error)] = "Division must be selected."; }
			if($college_type <= 0){ $this->error[sizeof($this->error)] = "College Type must be selected."; }

			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `sch-colleges`(Code, Description, ";
				$query .= "DivisionID, CollegeType, Created, Modified) ";
				$query .= "VALUES('";
				$query .= $code;
				$query .= "','";
				$query .= $description;
				$query .= "',";
				$query .= $division;
				$query .= ",";
				$query .= $college_type;
				$query .= ",NOW(), NOW())";

				$conn->query($query);

				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding college. Duplicate found!";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;
		}
		function EditCollege($college_id,
			$division, //int
			$code,
			$description,
			$college_type //int
		){
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = ucwords(addslashes(strip_tags($code)));
			$division = (int) $division;
			$college_type = (int) $college_type;

			if($description == ""){ $this->error[sizeof($this->error)] = "Description cannot be blank."; }
			if($code == ""){ $this->error[sizeof($this->error)] = "Code cannot be blank."; }
			if($division <= 0){ $this->error[sizeof($this->error)] = "Division must be selected."; }
			if($college_type <= 0){ $this->error[sizeof($this->error)] = "College Type must be selected."; }

			$result = false;

			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `sch-colleges` SET ";
				$query .= "Code='{$code}', ";
				$query .= "Description='{$description}', ";
				$query .= "DivisionID={$division}, ";
				$query .= "CollegeType={$college_type}, ";
				$query .= "Modified=NOW() ";
				$query .= "WHERE CollegeID={$college_id}";

				$conn->query($query);

				if($conn->affected_rows > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error editing college. Duplicate found!";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;
		}
		function DeleteCollege($college_id){
			$result = false;
			$this->error = array();

			if($this->IsCollegeUsed($college_id)){
				$this->error[] = "College is in use.";
			}

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				if(sizeof($this->error) == 0){
					$conn = $this->conn;

					$query = "DELETE FROM `sch-colleges` ";
					$query .= "WHERE CollegeID=";
					$query .= $college_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "a foreign key constraint fails") !== false){
							$this->error[sizeof($this->error)] = "Error deleting college. Information in use.";
						}	else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		private function IsCollegeUsed($id){
			$isUsed = false;

			$query = "SELECT * FROM `sch-course_list` WHERE College={0} ";
			$query = str_replace("{0}", $id, $query);

			$conn = $this->conn;
			$result = $conn->query($query);

			if($result->num_rows != 0){
				$isUsed = true;
			}

			return $isUsed;
		}

		/*--------------------------------------------------------

			COLLEGE ADMINS [SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Returns the list of Admins on the specified division
		function GetCollegeAdmins($college_id){
			$admins = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT AdminID, Position, Employee, College, Created, Modified ";
				$query .= "FROM `sch-college_admins` ";
				$query .= "LEFT JOIN `sch-employees` emp ON emp.EmployeeID=Employee ";
				$query .= "WHERE College={$college_id} ";

				$query .= "ORDER BY emp.LastName";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$admins = array();
					$ctr = 0;

					if($result->num_rows > 0){

						//create the other handlers
						$emp_hdr = new EmployeeManager($conn);

						while($row = $result->fetch_assoc()){
							$positions = $emp_hdr->GetPositions($row['Position']);
							$employees = $emp_hdr->GetEmployees($row['Employee']);
							$colleges = $this->GetColleges($row['College']);

							$admins[$ctr] = new CollegeAdmin;
							$admins[$ctr]->admin_id = $row['AdminID'];
							$admins[$ctr]->position = $positions[0];
							$admins[$ctr]->employee = $employees[0];
							$admins[$ctr]->college = $colleges[0];
							$admins[$ctr]->created = $row['Created'];
							$admins[$ctr]->modified = $row['Modified'];
							$ctr++;
						}
					}
				}
			}
			return $admins;
		}

		function AddCollegeAdmin($employee_id, $position_id, $college_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `sch-college_admins`(Employee, Position, College, Created, Modified) ";
				$query .= "VALUES(";
				$query .= $employee_id;
				$query .= ", ";
				$query .= $position_id;
				$query .= ", ";
				$query .= $college_id;
				$query .= ",NOW(),NOW())";

				$conn->query($query);

				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error adding college admin. Duplicate found!";
					}
				}
			}

			return $result;

		}

		function EditCollegeAdmin($admin_id, $employee_id,$position_id, $college_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `sch-college_admins` ";
				$query .= "SET Employee={$employee_id}, ";
				$query .= "Position={$position_id}, ";
				$query .= "College={$college_id}, ";
				$query .= "Modified=NOW() ";
				$query .= "WHERE AdminID={$admin_id}";

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error editing college admin. Duplicate found!";
					}
				}
			}

			return $result;

		}

		function DeleteCollegeAdmin($admin_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-college_admins` ";
				$query .= "WHERE AdminID=";
				$query .= $admin_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting college admin. Information in use.";
					} else {
						$this->error = $conn->error;
					}

				}
			}

			return $result;
		}

	}

		//Associate Professor, etc...
	class FacultyRank{
		public $rank_id;
		public $description;

		function __construct($rank_id, $description){
			$this->rank_id = $rank_id;
			$this->description = $description;
		}
	}

	//Part-Time, Full-Time, Inactive, etc...
	class FacultyStatus{
		public $status_id;
		public $description;

		function __construct($status_id, $description){
			$this->status_id = $status_id;
			$this->description = $description;
		}
	}

	//Faculty
	class Faculty{
		public $faculty_id;
		public $faculty_rank; //Faculty Rank Class
		public $faculty_status; //Faculty Status Class
		public $college; //College class
		public $employee; //Employee Information
		public $created;
		public $modified;
	}

		//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above;
	class FacultyManager{
		public $error = array();

		private $conn = null;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}

		/*--------------------------------------------------------

			FACULTY RANK [ SELECT | INSERT | UPDATE | DELETE ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetFacultyRanks($rank_id = null){
			$ranks = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT RankID, Description FROM `sch-faculty_rank` ";

				if($rank_id != null){
					$query .= "WHERE ";
					$query .= "RankID=";
					$query .= $rank_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$ranks = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$ranks[$ctr] = new FacultyRank($row['RankID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $ranks;
		}

		//Add Faculty Rank to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddFacultyRank($description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			$result = false;

			if($description == ""){
				$this->error[sizeof($this->error)] = "Faculty rank cannot be blank.";
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-faculty_rank`(Description) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "')";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding faculty rank. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		//Edit Faculty Rank in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditFacultyRank($rank_id, $description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			$result = false;

			if($description == ""){
				$this->error[sizeof($this->error)] = "Faculty rank cannot be blank.";
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-faculty_rank` SET Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE RankID=";
					$query .= $rank_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error = "Error editing faculty rank. Duplicate found!";
						}
					}
				}
			}

			return $result;

		}

		//Delete Faculty Rank in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteFacultyRank($rank_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-faculty_rank` ";
				$query .= "WHERE RankID=";
				$query .= $rank_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting faculty rank. Information in use.";
					}	else {
						$this->error = $conn->error;
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			FACULTY STATUS [ SELECT | INSERT | UPDATE | DELETE ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetFacultyStatuses($status_id = null){
			$statuses = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT StatusID, Description FROM `sch-faculty_status` ";

				if($status_id != null){
					$query .= "WHERE ";
					$query .= "StatusID=";
					$query .= $status_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$statuses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$statuses[$ctr] = new FacultyStatus($row['StatusID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $statuses;
		}

		//Add Faculty Status to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddFacultyStatus($description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			$result = false;

			if($description == ""){
				$this->error[sizeof($this->error)] = "Faculty Status cannot be blank.";
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-faculty_status`(Description) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "')";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error = "Error adding faculty status. Duplicate found!";
						} else {
							$this->error = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		//Edit Faculty Status in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditFacultyStatus($status_id, $description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			$result = false;

			if($description == ""){
				$this->error[sizeof($this->error)] = "Faculty Status cannot be blank.";
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-faculty_status` SET Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE StatusID=";
					$query .= $status_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error editing faculty status. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Delete Faculty Status in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteFacultyStatus($status_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-faculty_status` ";
				$query .= "WHERE StatusID=";
				$query .= $status_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting faculty status. Information in use.";
					}	else {
						$this->error = $conn->error;
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			FACULTIES [ SELECT | INSERT | UPDATE | DELETE ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetFaculties($college_id = null, $faculty_id = null, $keyword = null){
			$faculties = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT FacultyID, FacultyRank, FacultyStatus, College, Employee, Created, fac.Modified ";
				$query .= "FROM `sch-college_faculties` fac ";
				$query .= "LEFT JOIN `sch-employees` emp ON emp.EmployeeID=fac.Employee ";
				$query .= "WHERE 1=1 AND emp.IsDeleted=0 ";

				if($college_id != null){
					$query .= "AND ";
					$query .= "College=";
					$query .= $college_id;
					$query .= " ";
				}

				if($faculty_id != null){
					$query .= "AND ";
					$query .= "FacultyID=";
					$query .= $faculty_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND ";
					$query .= "(emp.LastName LIKE '%{$keyword}%' OR emp.FirstName LIKE '%{$keyword}%' OR emp.MiddleName LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY emp.LastName ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$faculties = array();
					$ctr = 0;
					if($result->num_rows > 0){
						$college_hndlr = new CollegeManager($conn);
						$employee_hndlr = new EmployeeManager($conn);
						while($row = $result->fetch_assoc()){
							$colleges = $college_hndlr->GetColleges($row['College']);
							$employees = $employee_hndlr->GetEmployees($row['Employee']);
							$faculties[$ctr] = new Faculty;
							$faculties[$ctr]->faculty_id = $row['FacultyID'];
							$faculties[$ctr]->faculty_rank = $this->GetFacultyRanks($row['FacultyRank']);
							$faculties[$ctr]->faculty_status = $this->GetFacultyStatuses($row['FacultyStatus']);
							$faculties[$ctr]->employee = $employees[0];
							$faculties[$ctr]->college = $colleges[0];
							$faculties[$ctr]->created = $row['Created'];
							$faculties[$ctr]->modified = $row['Modified'];
							$ctr++;
						}
					}
				}
			}

			return $faculties;
		}

		//Will always return null for errors else an array
		function GetFacultiesByKey($college_id = null, $faculty_id = null, $keyword = null){
			$faculties = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT FacultyID, FacultyRank, FacultyStatus, College, Employee, Created, fac.Modified ";
				$query .= "FROM `sch-college_faculties` fac ";
				$query .= "LEFT JOIN `sch-employees` emp ON emp.EmployeeID=fac.Employee ";
				$query .= "WHERE 1=1 AND emp.IsDeleted=0 ";

				if($college_id != null){
					$query .= "AND ";
					$query .= "College=";
					$query .= $college_id;
					$query .= " ";
				}

				if($faculty_id != null){
					$query .= "AND ";
					$query .= "FacultyID=";
					$query .= $faculty_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND ";
					$query .= "(emp.LastName LIKE '%{$keyword}%' OR emp.FirstName LIKE '%{$keyword}%' OR emp.MiddleName LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY emp.LastName ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$faculties = array();
					$ctr = 0;
					if($result->num_rows > 0){
						$college_hndlr = new CollegeManager($conn);
						$employee_hndlr = new EmployeeManager($conn);
						while($row = $result->fetch_assoc()){
							$colleges = $college_hndlr->GetColleges($row['College']);
							$employees = $employee_hndlr->GetEmployees($row['Employee']);
							$faculties[$row['FacultyID']] = new Faculty;
							$faculties[$row['FacultyID']]->faculty_id = $row['FacultyID'];
							$faculties[$row['FacultyID']]->faculty_rank = $this->GetFacultyRanks($row['FacultyRank']);
							$faculties[$row['FacultyID']]->faculty_status = $this->GetFacultyStatuses($row['FacultyStatus']);
							$faculties[$row['FacultyID']]->employee = $employees[0];
							$faculties[$row['FacultyID']]->college = $colleges[0];
							$faculties[$row['FacultyID']]->created = $row['Created'];
							$faculties[$row['FacultyID']]->modified = $row['Modified'];
							$ctr++;
						}
					}
				}
			}

			return $faculties;
		}

		//Add Faculty to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AssignFaculty(
			$faculty_rank, //RankID
			$faculty_status, //StatusID
			$college, //CollegeID
			$employee //EmployeeID
		){

			$result = false;

			if($employee <= 0){
				$this->error[sizeof($this->error)] = "Employee not selected.";
			}

			if($faculty_rank <= 0){
				$this->error[sizeof($this->error)] = "Faculty Rank not selected.";
			}

			if($faculty_status <= 0){
				$this->error[sizeof($this->error)] = "Faculty Status not selected.";
			}

			if($college <= 0){
				$this->error[sizeof($this->error)] = "College Department not selected.";
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-college_faculties`(FacultyRank, FacultyStatus, College, Employee, Created, Modified) ";
					$query .= "VALUES({$faculty_rank}, {$faculty_status}, {$college}, {$employee}, NOW(), NOW())";

					$conn->query($query);

					if($conn->insert_id > 0){
						$query = "UPDATE `sch-employees` SET IsFaculty=1 WHERE EmployeeID={$employee}";
						$conn->query($query);

						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding faculty. Duplicate found!";
						} else {
							//$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		//Edit Faculty in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditFaculty(
			$faculty_id,
			$faculty_rank, //RankID
			$faculty_status, //StatusID
			$college, //CollegeID
			$employee //EmployeeID
		){

			$result = false;

			if($employee <= 0){
				$this->error[sizeof($this->error)] = "Employee not selected.";
			}

			if($faculty_rank <= 0){
				$this->error[sizeof($this->error)] = "Faculty Rank not selected.";
			}

			if($faculty_status <= 0){
				$this->error[sizeof($this->error)] = "Faculty Status not selected.";
			}

			if($college <= 0){
				$this->error[sizeof($this->error)] = "College Department not selected.";
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-college_faculties` ";
					$query .= "SET FacultyRank={$faculty_rank}, FacultyStatus={$faculty_status}, ";
					$query .= "College={$college}, Employee={$employee}, Modified=NOW() ";
					$query .= "WHERE FacultyID=";
					$query .= $faculty_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error editing faculty. Duplicate found!";
						}
					}
				}
			}

			return $result;

		}

		//Delete Faculty in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function UnassignFaculty($faculty_id, $employee){

			$result = false;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-college_faculties` ";
				$query .= "WHERE FacultyID=";
				$query .= $faculty_id;

				$conn->query($query);

				if($conn->affected_rows > 0){
					$query = "UPDATE `sch-employees` SET IsFaculty=0 WHERE EmployeeID={$employee}";
					$conn->query($query);


					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[] = "Error unassigning faculty. Information in use.";
					}	else {
						$this->error[] = $conn->error;
					}
				}
			}

			return $result;

		}

	}

	//1st year, 2nd year, etc...
	class YearLevel{
		public $level_id;
		public $equivalent;
		public $description;

		function __construct($level_id, $equivalent, $description){
			$this->level_id = $level_id;
			$this->equivalent = $equivalent;
			$this->description = $description;
		}
	}

	//Course (Computer Engineering, Computer Science, etc...
	class Course{
		public $course_id;
		public $code;
		public $description;
		public $college;
		public $max_year_level;
		public $created;
		public $modified;

		function __construct(
			$course_id, $code, $description,
			$college, $max_year_level, $created,
			$modified
		){
			$this->course_id = $course_id;
			$this->code = $code;
			$this->description = $description;
			$this->college = $college;
			$this->max_year_level = $max_year_level;
			$this->created = $created;
			$this->modified = $modified;
		}

	}

	class Major{
		public $major_id;
		public $code;
		public $description;
		public $created;
		public $modified;
		public $course;

		function __construct(
			$major_id, $code, $description,
			$created, $modified, $course
		){
			$this->major_id = $major_id;
			$this->code = $code;
			$this->description = $description;
			$this->created = $created;
			$this->modified = $modified;
			$this->course = $course;
		}
	}

	class CourseManager{
		public $error = array();

		private $conn = null;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}

		/*-----------------------------------------------------------------------

			YEAR LEVEL (1st year, 2nd year, etc...) [ SELECT ]

		----------------------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetYearLevels($level_id = null){
			$levels = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT YearLevelID, Equivalent, Description FROM `sch-year_levels` ";
				$query .= "WHERE 1=1 ";

				if($level_id != null){
					$query .= "AND ";
					$query .= "YearLevelID=";
					$query .= $level_id;
					$query .= " ";
				}

				$query .= "ORDER BY Equivalent ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$levels = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$levels[$ctr] = new YearLevel(
														$row['YearLevelID'],
														$row['Equivalent'],
														$row['Description']
													);
							$ctr++;
						}
					}
				}
			}

			return $levels;
		}

		//Will always return null for errors else an array
		function GetYearLevelsByKey($level_id = null){
			$levels = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT YearLevelID, Equivalent, Description FROM `sch-year_levels` ";
				$query .= "WHERE 1=1 ";

				if($level_id != null){
					$query .= "AND ";
					$query .= "YearLevelID=";
					$query .= $level_id;
					$query .= " ";
				}

				$query .= "ORDER BY Equivalent ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$levels = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$levels[$row['YearLevelID']] = new YearLevel(
														$row['YearLevelID'],
														$row['Equivalent'],
														$row['Description']
													);
							$ctr++;
						}
					}
				}
			}

			return $levels;
		}


		/*-----------------------------------------------------------------------

			COURSES (BSCpE, BSN, etc) [ SELECT | INSERT | UPDATE | DELETE ]

		----------------------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetCourses($college_id = null, $course_id = null, $keyword = null){
			$courses = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CourseID, Code, Description, College, MaxYearLevel, Created, Modified FROM `sch-course_list` ";
				$query .= "WHERE 1=1 ";

				if($college_id != null){
					$query .= "AND ";
					$query .= "College=";
					$query .= $college_id;
					$query .= " ";
				}

				if($course_id != null){
					$query .= "AND ";
					$query .= "CourseID=";
					$query .= $course_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "Code LIKE '%{$keyword}%' OR Description LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$courses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$courses[$ctr] = new Course(
														$row['CourseID'],
														$row['Code'],
														$row['Description'],
														$row['College'],
														$row['MaxYearLevel'],
														$row['Created'],
														$row['Modified']
													);
							$ctr++;
						}
					}
				}
			}

			return $courses;
		}

		function GetCoursesByKey($college_id = null, $course_id = null, $keyword = null){
			$courses = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CourseID, Code, Description, College, MaxYearLevel, Created, Modified FROM `sch-course_list` ";
				$query .= "WHERE 1=1 ";

				if($college_id != null){
					$query .= "AND ";
					$query .= "College=";
					$query .= $college_id;
					$query .= " ";
				}

				if($course_id != null){
					$query .= "AND ";
					$query .= "CourseID=";
					$query .= $course_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "Code LIKE '%{$keyword}%' OR Description LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$courses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$courses[$row['CourseID']] = new Course(
														$row['CourseID'],
														$row['Code'],
														$row['Description'],
														$row['College'],
														$row['MaxYearLevel'],
														$row['Created'],
														$row['Modified']
													);
							$ctr++;
						}
					}
				}
			}

			return $courses;
		}

		//Will always return null for errors else an array
		function GetCoursesForDisplay($college_id = null, $course_id = null, $keyword = null){
			$courses = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CourseID, Code, cl.Description, College, yl.Description AS MaxYearLevel, `Created`, Modified FROM `sch-course_list` cl ";
				$query .= "LEFT JOIN `sch-year_levels` yl ON yl.YearLevelID=cl.MaxYearLevel ";
				$query .= "WHERE 1=1 ";

				if($college_id != null){
					$query .= "AND ";
					$query .= "College=";
					$query .= $college_id;
					$query .= " ";
				}

				if($course_id != null){
					$query .= "AND ";
					$query .= "CourseID=";
					$query .= $course_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "Code LIKE '%{$keyword}%' OR Description LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$courses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$courses[$ctr] = new Course(
														$row['CourseID'],
														$row['Code'],
														$row['Description'],
														$row['College'],
														$row['MaxYearLevel'],
														$row['Created'],
														$row['Modified']
													);
							$ctr++;
						}
					}
				}
			}

			return $courses;
		}

		//Will always return null for errors else an array
		function GetCourseByKey($course_id){
			$courses = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CourseID, Code, Description, College, MaxYearLevel, Created, Modified FROM `sch-course_list` ";
				$query .= "WHERE 1=1 ";
				$query .= "AND CourseID={$course_id} ";

				$query .= "ORDER BY Code ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$courses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$courses[$ctr] = new Course(
														$row['CourseID'],
														$row['Code'],
														$row['Description'],
														$row['College'],
														$row['MaxYearLevel'],
														$row['Created'],
														$row['Modified']
													);
							$ctr++;
						}
					}
				}
			}

			return $courses;
		}

		//Will always return null for errors else an array
		function GetCoursesByCode($college_id = null, $keyword = null){
			$courses = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CourseID, Code, Description, College, MaxYearLevel, Created, Modified FROM `sch-course_list` ";
				$query .= "WHERE 1=1 ";

				if($college_id != null){
					$query .= "AND ";
					$query .= "College=";
					$query .= $college_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "Code LIKE '%{$keyword}%' OR Description LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY Code ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$courses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$courses[$ctr] = new Course(
														$row['CourseID'],
														$row['Code'],
														$row['Description'],
														$row['College'],
														$row['MaxYearLevel'],
														$row['Created'],
														$row['Modified']
													);
							$ctr++;
						}
					}
				}
			}

			return $courses;
		}

		//Add Course to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddCourse($code, $description, $college_id, $max_year_level){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));

			$result = false;

			$err_ctr = 0;

			if($description == ''){
				$this->error[$err_ctr] = "Description cannot be blank. ";
				$err_ctr++;
			}

			if($code == ''){
				$this->error[$err_ctr] = "Code cannot be blank. ";
				$err_ctr++;
			}

			if($college_id <= 0){
				$this->error[$err_ctr] = "College not found.";
				$err_ctr++;
			}

			if($max_year_level <= 0){
				$this->error[$err_ctr] = "Year Level not found.";
				$err_ctr++;
			}

			if($err_ctr-1 < 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-course_list`(Code, Description, College, MaxYearLevel, Created, Modified) ";
					$query .= "VALUES('";
					$query .= $code;
					$query .= "','{$description}',{$college_id}, {$max_year_level}, NOW(), NOW())";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error = "Error adding course. Duplicate found!";
						} else {
							$this->error = $conn->error;
						}
					}
				}
			}//end if

			return $result;
		}

		//Edit Course in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditCourse($course_id, $code, $description, $college_id, $max_year_level){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));

			$result = false;

			$err_ctr = 0;

			if($description == ''){
				$this->error[$err_ctr] = "Description is empty. ";
				$err_ctr++;
			}

			if($code == ''){
				$this->error[$err_ctr] = "Code is empty. ";
				$err_ctr++;
			}

			if($college_id <= 0){
				$this->error[$err_ctr] = "College: Invalid selection.";
				$err_ctr++;
			}

			if($max_year_level <= 0){
				$this->error[$err_ctr] = "Year Level: Invalid selection.";
				$err_ctr++;
			}

			if($err_ctr-1 < 0){

				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-course_list` SET Code='{$code}', Description='{$description}', ";
					$query .= "College={$college_id}, MaxYearLevel={$max_year_level}, Modified=NOW() ";
					$query .= "WHERE CourseID=";
					$query .= $course_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$err_ctr] = "Error editing course. Duplicate found!";
						}
					}
				}

			}
			return $result;

		}

		//Delete Course in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteCourse($course_id){

			$result = false;
			$this->error = array();

			if($this->IsCourseUsed($course_id)){
				$this->error[] = "Course is in use.";
			}

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				if(sizeof($this->error) == 0){
					$conn = $this->conn;

					$query = "DELETE FROM `sch-course_list` ";
					$query .= "WHERE CourseID=";
					$query .= $course_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "a foreign key constraint fails") !== false){
							$this->error[sizeof($this->error)] = "Error deleting course. Information in use.";
						}	else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		private function IsCourseUsed($id){
			$isUsed = false;

			$query = "SELECT * FROM `sch-curriculum` WHERE Course={0} ";
			$query = str_replace("{0}", $id, $query);

			$conn = $this->conn;
			$result = $conn->query($query);

			if($result->num_rows != 0){
				$isUsed = true;
			}

			return $isUsed;
		}

		/*----------------------------------------------------------------------------------------------------

			MAJORS (Engineering Major, English Major, Computer Major [ SELECT | INSERT | UPDATE | DELETE ]

		-----------------------------------------------------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetMajors($course_id, $keyword = null){
			$majors = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT MajorID, Code, Description, Created, Modified, Course FROM `sch-major_list` ";
				$query .= "WHERE Course={$course_id} ";

				if($keyword != null){
					$query .= "AND (";
					$query .= "Code LIKE '%{$keyword}%' OR Description LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$majors = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$majors[$ctr] = new Major(
														$row['MajorID'],
														$row['Code'],
														$row['Description'],
														$row['Created'],
														$row['Modified'],
														$row['Course']
													);
							$ctr++;
						}
					}
				}
			}

			return $majors;
		}

		//Add Major to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddMajor($course_id, $code, $description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));

			$result = false;

			$err_ctr = 0;

			if($description == ''){
				$this->error[$err_ctr] = "Description is empty. ";
				$err_ctr++;
			}

			if($code == ''){
				$this->error[$err_ctr] = "Code is empty. ";
				$err_ctr++;
			}

			if($course_id <= 0){
				$this->error[$err_ctr] = "Course: Invalid selection.";
				$err_ctr++;
			}

			if($err_ctr-1 < 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-major_list`(Code, Description, Created, Modified, Course) ";
					$query .= "VALUES('";
					$query .= $code;
					$query .= "','{$description}', NOW(), NOW(), {$course_id})";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$err_ctr] = "Error adding major. Duplicate found!";
						} else {
							$this->error[$err_ctr] = $conn->error;
						}
					}
				}
			}//end if

			return $result;
		}

		//Edit Major in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditMajor($major_id, $course_id, $code, $description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));

			$result = false;

			$err_ctr = 0;

			if($description == ''){
				$this->error[$err_ctr] = "Description is empty. ";
				$err_ctr++;
			}

			if($code == ''){
				$this->error[$err_ctr] = "Code is empty. ";
				$err_ctr++;
			}

			if($course_id <= 0){
				$this->error[$err_ctr] = "Course: Invalid selection.";
				$err_ctr++;
			}

			if($err_ctr-1 < 0){

				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-major_list` SET Code='{$code}', Description='{$description}', ";
					$query .= "Modified=NOW(), Course={$course_id} ";
					$query .= "WHERE MajorID=";
					$query .= $major_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$err_ctr] = "Error editing major. Duplicate found!";
						}
					}
				}

			}
			return $result;

		}

		//Delete Major in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteMajor($major_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-major_list` ";
				$query .= "WHERE MajorID=";
				$query .= $major_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting major. Information in use.";
					}	else {
						$this->error = $conn->error;
					}
				}
			}

			return $result;

		}

	}

?>
