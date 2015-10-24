<?php
	/*-----------------------------------------------

		SCHOOL++
		-contains school settings

		DEPENDENCIES:
	-------------------------------------------------*/

	class SchoolTime{
		public $time_id;
		public $description;

		function __construct($time_id, $description){
			$this->time_id = $time_id;
			$this->description = $description;
		}

		//returns the military time equivalent
		function Military(){
			$description = $this->description;
			$arr1 = explode(":", $description);
			$arr2 = explode(" ", $arr1[1]);

			$hh = $arr1[0];
			$mm = $arr2[0];
			$tt = $arr2[1];

			$total = 0;

			if($tt == "AM"){
				//add hours
				if($hh != 12){
					$total = $hh * 100;
				}
				$total += $mm;
			} else {
				//if 12 pm
				if($hh == 12){
					$total = $hh * 100;
				} else {
					$total = ($hh + 12) * 100;
				}
				$total += $mm;
			}

			return $total;
		}

		//swap values by reference
		function Swap(&$value1, &$value2){
			$tmp = $value1;
			$value1 = $value2;
			$value2 = $tmp;
		}

		//sorts an array of SchoolTime
		function Sort($times){
			if(is_array($times) == true){

				$is_swapped = false;
				$size = sizeof($times);

				do{
					$is_swapped = false;

					for($i = 0; $i < ($size-1); $i++){
						if($times[$i]->Military() > $times[$i+1]->Military()){
							$this->Swap($times[$i]->description, $times[$i+1]->description);
							$this->Swap($times[$i]->time_id, $times[$i+1]->time_id);
							$is_swapped = true;
						}
					}
				} while($is_swapped == true);

			}
		}
	}

	class SchoolDay{
		public $day_id;
		public $description;
		public $shorthand;

		function __construct($day_id, $description, $shorthand, $active){
			$this->day_id = $day_id;
			$this->description = $description;
			$this->shorthand = $shorthand;
			$this->active = $active;
		}
	}

	class SchoolYear{
		public $year_id;
		public $start;
		public $end;
		public $active;

		function __construct($year_id, $start, $end, $active){
			$this->year_id = $year_id;
			$this->start = $start;
			$this->end = $end;
			$this->active = $active;
		}
	}

	class Semester{
		public $semester_id;
		public $description;
		public $shorthand;
		public $active;

		function __construct($semester_id, $description, $shorthand, $active){
			$this->semester_id = $semester_id;
			$this->description = $description;
			$this->shorthand = $shorthand;
			$this->active = $active;
		}
	}

	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class SchoolManager{

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

			SCHOOL TIMES [ SELECT | INSERT | UPDATE | DELETE]
			//School Times (12:00 AM, 12:30AM, 1:00AM)

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetSchoolTimes($time_id = null){
			$times = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT TimeID, Description FROM `sch-time_of_classes` ";

				if($time_id != null){
					$query .= "WHERE ";
					$query .= "TimeID=";
					$query .= $time_id;
					$query .= " ";
				}

				$query .= "ORDER BY TimeID ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$times = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$times[$ctr] = new SchoolTime($row['TimeID'], $row['Description']);
							$ctr++;
						}

						$times[0]->Sort($times);
					}
				}
			}

			return $times;
		}

		//Will always return null for errors else an array
		function GetSchoolTimesByKey($time_id = null){
			$times = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT TimeID, Description FROM `sch-time_of_classes` ";

				if($time_id != null){
					$query .= "WHERE ";
					$query .= "TimeID=";
					$query .= $time_id;
					$query .= " ";
				}

				$query .= "ORDER BY TimeID ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$times = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$times[$row['TimeID']] = new SchoolTime($row['TimeID'], $row['Description']);
							$ctr++;
						}

					}
				}
			}

			return $times;
		}

		//Add School Time to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddSchoolTime($description){

			//clean input
			$description = addslashes(strip_tags($description));
			$reg = new RtRegExp;

			$result = false;

			$match = $reg->CheckTime($description);

			if($match <= 0){
				$this->error[sizeof($this->error)] = "School Time: Invalid format. Format should be (hh:mm AM or PM).";
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->$error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-time_of_classes`(Description) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "')";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding school time. Duplicate found!";
						}
					}
				}
			}

			return $result;

		}

		//Edit School Time in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditSchoolTime($time_id, $description){

			//clean input
			$description = addslashes(strip_tags($description));
			$reg = new RtRegExp;

			$result = false;

			$match = $reg->CheckTime($description);

			if($match <= 0){
				$this->error[sizeof($this->error)] = "School Time: Invalid format. Format should be (hh:mm AM or PM).";
			}

			if(sizeof($this->error) == 0){

				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-time_of_classes` SET Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE TimeID=";
					$query .= $time_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error = "Error editing school time. Duplicate found!";
						}
					}
				}

			}

			return $result;

		}

		//Delete School Time in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteSchoolTime($time_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-time_of_classes` ";
				$query .= "WHERE TimeID=";
				$query .= $time_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting school time. Information in use.";
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			SCHOOL DAYS [ SELECT | UPDATE ]
			//School Days (Monday-Mon, Friday-Fri)

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetSchoolDays($day_id = null){
			$days = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT DayID, Description, Shorthand, Active FROM `sch-days_of_classes` ";

				if($day_id != null){
					$query .= "WHERE ";
					$query .= "DayID=";
					$query .= $day_id;
					$query .= " ";
				}

				$query .= "ORDER BY DayID ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$days = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$days[$ctr] = new SchoolDay($row['DayID'], $row['Description'], $row['Shorthand'], $row['Active']);
							$ctr++;
						}

					}
				}
			}

			return $days;
		}

		//Will always return null for errors else an array
		function GetSchoolDaysByKey($day_id = null){
			$days = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT DayID, Description, Shorthand, Active FROM `sch-days_of_classes` ";

				if($day_id != null){
					$query .= "WHERE ";
					$query .= "DayID=";
					$query .= $day_id;
					$query .= " ";
				}

				$query .= "ORDER BY DayID ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$days = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$days[$row['DayID']] = new SchoolDay($row['DayID'], $row['Description'], $row['Shorthand'], $row['Active']);
							$ctr++;
						}

					}
				}
			}

			return $days;
		}

		//Edit School Day in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		//Set school day active or inactive
		function EditSchoolDay($day_id, $shorthand, $active){

			//clean input
			$shorthand = addslashes(strip_tags($shorthand));

			$result = false;

			if(sizeof($this->error) == 0){

				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-days_of_classes` SET Shorthand='";
					$query .= $shorthand;
					$query .= "', Active=" . $active . " ";
					$query .= "WHERE DayID=";
					$query .= $day_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error = "Error editing school day. Duplicate found!";
						}
					}
				}

			}

			return $result;

		}

		/*--------------------------------------------------------

			SCHOOL YEARS [ SELECT | INSERT | UPDATE | DELETE]
			//School Years (2010-2011, 2011-2012)

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetSchoolYears($school_year_id = null){
			$years = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SchoolYearID, Start, End, Active FROM `sch-school_years` ";

				if($school_year_id != null){
					$query .= "WHERE ";
					$query .= "SchoolYearID=";
					$query .= $school_year_id;
					$query .= " ";
				}

				$query .= "ORDER BY Start";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$years = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$years[$ctr] = new SchoolYear($row['SchoolYearID'], $row['Start'], $row['End'], $row['Active']);
							$ctr++;
						}

					}
				}
			}

			return $years;
		}

		//Will always return null for errors else an array
		function GetSchoolYearsByKey($school_year_id = null){
			$years = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SchoolYearID, Start, End, Active FROM `sch-school_years` ";

				if($school_year_id != null){
					$query .= "WHERE ";
					$query .= "SchoolYearID=";
					$query .= $school_year_id;
					$query .= " ";
				}

				$query .= "ORDER BY Start";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$years = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$years[$row['SchoolYearID']] = new SchoolYear($row['SchoolYearID'], $row['Start'], $row['End'], $row['Active']);
							$ctr++;
						}

					}
				}
			}

			return $years;
		}

		//Will always return null for errors else an array
		function GetActiveSchoolYear(){
			$years = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SchoolYearID, Start, End, Active FROM `sch-school_years` WHERE Active=1 ";

				$query .= "ORDER BY Start";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$years = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$years[$ctr] = new SchoolYear($row['SchoolYearID'], $row['Start'], $row['End'], $row['Active']);
							$ctr++;
						}

					}
				}
			}

			return $years;
		}

		//Add School Year to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddSchoolYear($start){

			//clean input
			$start = addslashes(strip_tags($start));

			$result = false;

			if($start < 1960){
				$this->error[sizeof($this->error)] = "Year cannot be below 1960.";
			}

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->$error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					//set the end year for the school year
					$end = $start+1;

					$conn = $this->conn;

					$query = "INSERT INTO `sch-school_years`(Start, End) ";
					$query .= "VALUES({$start},{$end})";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding school year. Duplicate found!";
						}
					}
				}
			}

			return $result;

		}

		//Edit School Year in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditSchoolYear($year_id, $start){

			//clean input
			$start = addslashes(strip_tags($start));

			$result = false;

			if($start < 1960){
				$this->error[sizeof($this->error)] = "School Year: Year cannot be below 1960.";
			}

			if(sizeof($this->error) == 0){

				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-school_years` SET start=";
					$query .= $start;
					$query .= ", end=" . ($start+1) . " ";
					$query .= "WHERE SchoolYearID=";
					$query .= $year_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error = "Error editing school year. Duplicate found!";
						}
					}
				}

			}

			return $result;

		}

		//Activates the School Year in database
		//-->Only 1 School Year is activated at a time
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function ActivateSchoolYear($year_id){

			$result = false;

			if(sizeof($this->error) == 0){

				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					//Deactivate all school years
					$query = "UPDATE `sch-school_years` SET Active=0 ";
					$conn->query($query);

					//Activate Selected Year
					$query = "UPDATE `sch-school_years` SET Active=1 ";
					$query .= "WHERE SchoolYearID={$year_id}";
					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error activating school year. School year may already be activated.";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}

			}

			return $result;

		}

		//Delete School Year in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteSchoolYear($year_id){

			$result = false;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-school_years` ";
				$query .= "WHERE SchoolYearID=";
				$query .= $year_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting school year. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			SCHOOL SEMESTERS [SELECT | UPDATE]
			//Semesters (1st Semester, 2nd Semester)
			//Defined Manually

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetActiveSemester(){
			$semesters = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SemesterID, Description, Shorthand, Active FROM `sch-semesters` ";
				$query .= "WHERE 1=1 AND Active=1 ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$semesters = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$semesters[$ctr] = new Semester($row['SemesterID'], $row['Description'], $row['Shorthand'], $row['Active']);
							$ctr++;
						}

					}
				}
			}
			return $semesters;
		}

		//Will always return null for errors else an array
		function GetSemesters($semester_id = null){
			$semesters = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SemesterID, Description, Shorthand, Active FROM `sch-semesters` ";
				$query .= "WHERE 1=1 ";
				if($semester_id != null){
					$query .= "AND ";
					$query .= "SemesterID=";
					$query .= $semester_id;
					$query .= " ";
				}
				$query .= "AND ForCurriculum=1 ";

				$query .= "ORDER BY SemesterID ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$semesters = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$semesters[$ctr] = new Semester($row['SemesterID'], $row['Description'], $row['Shorthand'], $row['Active']);
							$ctr++;
						}

					}
				}
			}

			return $semesters;
		}

		function GetSemestersByKey($semester_id = null){
			$semesters = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SemesterID, Description, Shorthand, Active FROM `sch-semesters` ";
				$query .= "WHERE 1=1 ";
				if($semester_id != null){
					$query .= "AND ";
					$query .= "SemesterID=";
					$query .= $semester_id;
					$query .= " ";
				}
				$query .= "AND ForCurriculum=1 ";

				$query .= "ORDER BY SemesterID ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$semesters = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$semesters[$row['SemesterID']] = new Semester($row['SemesterID'], $row['Description'], $row['Shorthand'], $row['Active']);
							$ctr++;
						}

					}
				}
			}

			return $semesters;
		}

		//Activate Semester in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function ActivateSemester($semester_id){

			$result = false;


			if(sizeof($this->error) == 0){

				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					//Deactivate all school years
					$query = "UPDATE `sch-semesters` SET Active=0 ";
					$conn->query($query);

					//Activate Selected Year
					$query = "UPDATE `sch-semesters` SET Active=1 ";
					$query .= "WHERE SemesterID={$semester_id}";
					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error = "Error activating semester. Duplicate found!";
						}
					}
				}

			}

			return $result;

		}

	}


?>
