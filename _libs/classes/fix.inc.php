<?php
	/*-----------------------------------------------
	
		CONSTAINS LATE FIXES	
		
		DEPENDENCIES:
	-------------------------------------------------*/

	class SchoolYear2{
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
	
	class Semester2{
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
	class FixManager{
	
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
							$years[$ctr] = new SchoolYear2($row['SchoolYearID'], $row['Start'], $row['End'], $row['Active']);
							$ctr++;
						}
						
					}
				}
			}
			
			return $years;
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
							$semesters[$ctr] = new Semester2($row['SemesterID'], $row['Description'], $row['Shorthand'], $row['Active']);
							$ctr++;
						}
						
					}
				}
			}
			
			return $semesters;
		}
	
	}


?>