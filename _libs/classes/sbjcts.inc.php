<?php
	/*-----------------------------------------------

		SUBJECTS
		-contains subject details

		DEPENDENCIES:
			1) cllgs.inc.php - class for colleges
			2) grds.inc.php - class for grades
	-------------------------------------------------*/

	class SubjectType{
		public $type_id;
		public $code;
		public $description;

		function __construct(
			$type_id, $code, $description
		){
			$this->type_id = $type_id;
			$this->code = $code;
			$this->description = $description;
		}

	}

	class SubjectGroup{
		public $group_id;
		public $description;

		function __construct($subject_group_id, $description){
			$this->group_id = $subject_group_id;
			$this->description = $description;
		}
	}


	//Subject
	class Subject{
		public $subject_id;
		public $code;
		public $description;
		public $grading_scheme; //string
		public $subject_type; //string
		public $units; //int
		public $unitsLab; //int
		public $group; //subject group string
		public $college; //string
		public $virtual; //int 0 or 1 only
		public $isHalf;
		public $created;
		public $modified;

		function __construct($subject_id, $code, $description,
			$subject_type, $units,
			$group, $virtual, //int 0 or 1 only
			$created, $modified, $unitsLab, $isHalf
		){
			$this->subject_id = $subject_id;
			$this->code = $code;
			$this->description = $description;
			$this->subject_type = $subject_type; //string
			$this->units = $units; //int
			$this->group = $group; //subject group string
			$this->virtual = $virtual; //int 0 or 1 only
			$this->created = $created;
			$this->modified = $modified;
			$this->unitsLab = $unitsLab;
			$this->isHalf = $isHalf;
		}
	}

	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class SubjectManager{

		public $error = array();
		public $error_count = 0;

		private $conn = null;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}

		/*--------------------------------------------------------

			SUBJECT TYPE [ SELECT | INSERT | UPDATE | DELETE]
			//Laboratory (Lab), Lecture (Lec)

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetSubjectTypes($type_id = null){
			$types = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT TypeID, Description, Code FROM `sch-subject_type` ";

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
							$types[$ctr] = new SubjectType($row['TypeID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $types;
		}

		/*--------------------------------------------------------

			SUBJECT GROUPS [ SELECT | INSERT | UPDATE | DELETE]
			//Electives, Majors, etc...

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetSubjectGroups($group_id = null){
			$groups = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SubjectGroupID, Description FROM `sch-subject_group` ";

				if($group_id != null){
					$query .= "WHERE ";
					$query .= "SubjectGroupID=";
					$query .= $group_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$groups = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$groups[$ctr] = new SubjectGroup($row['SubjectGroupID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $groups;
		}

		//Add Subject Group to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddSubjectGroup($description){

			//clean input
			$description = addslashes(strip_tags($description));

			$result = false;

			if($description == ""){
				$this->error[sizeof($this->error)] = "Subject Group cannot be blank.";
			}

			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `sch-subject_group`(Description) ";
				$query .= "VALUES('";
				$query .= $description;
				$query .= "')";

				$conn->query($query);

				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding subject group. Duplicate found!";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Edit Subject Group in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditSubjectGroup($group_id, $description){

			//clean input
			$description = addslashes(strip_tags($description));

			$result = false;

			if($description == ""){
				$this->error[sizeof($this->error)] = "Subject Group cannot be blank.";
			}

			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `sch-subject_group` SET Description='";
				$query .= $description;
				$query .= "' ";
				$query .= "WHERE SubjectGroupID=";
				$query .= $group_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error editing subject group. Duplicate found!";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Delete Subject Group in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteSubjectGroup($group_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-subject_group` ";
				$query .= "WHERE SubjectGroupID=";
				$query .= $group_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting subject group. Information in use.";
					}  else {
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			SUBJECTS [ SELECT | INSERT | UPDATE | DELETE]
			//Geometry, Calculus, etc...

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetSubjects($subject_id = null, $keyword = null){
			$subjects = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SubjectID, s.Code, s.Description, ";
				$query .= "st.Description AS SubjectType, Units, sg.Description AS SubjectGroup, Virtual, ";
				$query .= "Created, Modified, UnitsLab, IsHalfFee ";
				$query .= "FROM `sch-subjects` s ";
				$query .= "LEFT JOIN `sch-subject_type` st ON st.TypeID=s.SubjectType ";
				$query .= "LEFT JOIN `sch-subject_group` sg ON sg.SubjectGroupID=s.Group ";
				$query .= "WHERE 1=1 ";

				if($subject_id != null){
					$query .= "AND ";
					$query .= "SubjectID=";
					$query .= $subject_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND ";
					$query .= "(s.Code LIKE '%{$keyword}%' ";
					$query .= "OR s.Description LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY s.Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$ctr] = new Subject(
								$row['SubjectID'],
								$row['Code'],
								$row['Description'],
								$row['SubjectType'],
								$row['Units'],
								$row['SubjectGroup'],
								$row['Virtual'],
								$row['Created'],
								$row['Modified'],
								$row['UnitsLab'],
								$row['IsHalfFee']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetSubjectsByKey($subject_id = null, $keyword = null){
			$subjects = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SubjectID, s.Code, s.Description, ";
				$query .= "st.Description AS SubjectType, Units, sg.Description AS SubjectGroup, Virtual, ";
				$query .= "Created, Modified, UnitsLab, IsHalfFee ";
				$query .= "FROM `sch-subjects` s ";
				$query .= "LEFT JOIN `sch-subject_type` st ON st.TypeID=s.SubjectType ";
				$query .= "LEFT JOIN `sch-subject_group` sg ON sg.SubjectGroupID=s.Group ";
				$query .= "WHERE 1=1 ";

				if($subject_id != null){
					$query .= "AND ";
					$query .= "SubjectID=";
					$query .= $subject_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND ";
					$query .= "(s.Code LIKE '%{$keyword}%' ";
					$query .= "OR s.Description LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY s.Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['SubjectID']] = new Subject(
								$row['SubjectID'],
								$row['Code'],
								$row['Description'],
								$row['SubjectType'],
								$row['Units'],
								$row['SubjectGroup'],
								$row['Virtual'],
								$row['Created'],
								$row['Modified'],
								$row['UnitsLab'],
								$row['IsHalfFee']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetSubjectsForEdit($subject_id = null, $keyword = null){
			$subjects = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SubjectID, s.Code, s.Description, ";
				$query .= "st.TypeID AS SubjectType, Units, sg.SubjectGroupID AS SubjectGroup, Virtual, ";
				$query .= "Created, Modified, UnitsLab, IsHalfFee ";
				$query .= "FROM `sch-subjects` s ";
				$query .= "LEFT JOIN `sch-subject_type` st ON st.TypeID=s.SubjectType ";
				$query .= "LEFT JOIN `sch-subject_group` sg ON sg.SubjectGroupID=s.Group ";
				$query .= "WHERE 1=1 ";

				if($subject_id != null){
					$query .= "AND ";
					$query .= "SubjectID=";
					$query .= $subject_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND ";
					$query .= "(s.Code LIKE '%{$keyword}%' ";
					$query .= "OR s.Description LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY s.Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$ctr] = new Subject(
								$row['SubjectID'],
								$row['Code'],
								$row['Description'],
								$row['SubjectType'],
								$row['Units'],
								$row['SubjectGroup'],
								$row['Virtual'],
								$row['Created'],
								$row['Modified'],
								$row['UnitsLab'],
								$row['IsHalfFee']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetSubjectsByCode($subject_id = null, $keyword = null){
			$subjects = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SubjectID, s.Code, s.Description, ";
				$query .= "st.Description AS SubjectType, Units, sg.Description AS SubjectGroup, Virtual, ";
				$query .= "Created, Modified, UnitsLab, IsHalfFee ";
				$query .= "FROM `sch-subjects` s ";
				$query .= "LEFT JOIN `sch-subject_type` st ON st.TypeID=s.SubjectType ";
				$query .= "LEFT JOIN `sch-subject_group` sg ON sg.SubjectGroupID=s.Group ";
				$query .= "WHERE 1=1 ";

				if($subject_id != null){
					$query .= "AND ";
					$query .= "SubjectID=";
					$query .= $group_id;
					$query .= " ";
				}

				if($keyword != null){
					$query .= "AND ";
					$query .= "(s.Code LIKE '%{$keyword}%' ";
					$query .= "OR s.Description LIKE '%{$keyword}%') ";
				}

				$query .= "ORDER BY s.Code ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$ctr] = new Subject(
								$row['SubjectID'],
								$row['Code'],
								$row['Description'],
								$row['SubjectType'],
								$row['Units'],
								$row['SubjectGroup'],
								$row['Virtual'],
								$row['Created'],
								$row['Modified'],
								$row['UnitsLab'],
								$row['IsHalfFee']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Add Subject to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddSubject(
			$code,
			$description,
			$units, //int
			$subject_type, //int
			$group, //int
			$virtual, //int 0 or 1 only
			$unitsLab,
			$isHalf //determines whether the subject only pays half of the unit cost
		){

			//clean input
			$description = trim(addslashes(strip_tags($description)));
			$code = trim(addslashes(strip_tags($code)));
			$units = (int) $units;
			$unitsLab = (int) $unitsLab;
			$subject_type = (int) $subject_type;
			$group = (int) $group;
			$virtual = (int) $virtual;

			$err_ctr = 0;

			if($description == ''){
				$this->error[$err_ctr] = "Description cannot be blank.";
				$err_ctr++;
			}

			if($code == ''){
				$this->error[$err_ctr] = "Code cannot be blank.";
				$err_ctr++;
			}

			if($subject_type <= 0){
				$this->error[$err_ctr] = "Subject Type not found.";
				$err_ctr++;
			}

			if($units < 0){
				$this->error[$err_ctr] = "Lec Units: Cannot be less than or equal to zero.";
				$err_ctr++;
			}

			if($unitsLab < 0){
				$this->error[$err_ctr] = "Lab Units: Cannot be less than zero.";
				$err_ctr++;
			}

			if($group <= 0){
				$this->error[$err_ctr] = "Subject Group not found.";
				$err_ctr++;
			}

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-subjects`(Code, Description, SubjectType, ";
					$query .= "`Units`, `Group`,  `Created`, `Modified`,`Virtual`, `UnitsLab`, `IsHalfFee`) ";
					$query .= "VALUES('{$code}', '{$description}', ";
					$query .= "{$subject_type}, {$units}, {$group}, NOW(), NOW(), {$virtual}, {$unitsLab}, {$isHalf}";
					$query .= ")";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$err_ctr] = "Error adding subject. Duplicate found!";
						} else {
							$this->error[$err_ctr] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Edit Subject in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditSubject(
			$subject_id,
			$code,
			$description,
			$subject_type, //int
			$units, //int
			$group, //int
			$virtual, //int 0 or 1 only
			$unitsLab,
			$isHalf
		){

			$result = false;
			//clean input
			$description = trim(addslashes(strip_tags($description)));
			$code = trim(addslashes(strip_tags($code)));
			$units = (int) $units;
			$unitsLab = (int) $unitsLab;
			$subject_type = (int) $subject_type;
			$group = (int) $group;
			$virtual = (int) $virtual;
			$isHalf = (int) $isHalf;

			$err_ctr = 0;

			if($description == ''){
				$this->error[$err_ctr] = "Description cannot be blank.";
				$err_ctr++;
			}

			if($code == ''){
				$this->error[$err_ctr] = "Code cannot be blank.";
				$err_ctr++;
			}

			if($subject_type <= 0){
				$this->error[$err_ctr] = "Subject Type not found.";
				$err_ctr++;
			}

			if($units < 0){
				$this->error[$err_ctr] = "Lec Units: Cannot be less than or equal to zero.";
				$err_ctr++;
			}

			if($unitsLab < 0){
				$this->error[$err_ctr] = "Lab Units: Cannot be less than zero.";
				$err_ctr++;
			}

			if($group <= 0){
				$this->error[$err_ctr] = "Subject Group not found.";
				$err_ctr++;
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-subjects` SET Description='";
					$query .= $description;
					$query .= "', Code='{$code}', SubjectType={$subject_type}, Units={$units}, ";
					$query .= "`Group`={$group}, Virtual={$virtual}, Modified=NOW(), UnitsLab={$unitsLab}, IsHalfFee={$isHalf} ";
					$query .= "WHERE SubjectID=";
					$query .= $subject_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$err_ctr] = "Error editing subject. Duplicate found!";
						}  else {
							$this->error[$err_ctr] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Delete Subject in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteSubject($subject_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-subjects` ";
				$query .= "WHERE SubjectID=";
				$query .= $subject_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting subject. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}
	}


?>
