<?php
	/*-----------------------------------------------

		SCHEDULES
		-handle sections and schedules

	-------------------------------------------------*/

	class Section{
		public $section_id;
		public $type;
		public $name;
		public $curriculum;
		public $sy;
		public $semester;
		public $year_level;
		public $date_created;
		public $date_modified;
		//these are taken from other tables
		public $college; //course
		public $course; //curriculum
		function __construct(
			$section_id, $type, $name, $curriculum, $sy, $semester, $year_level, $date_created, $date_modified
		){
			$this->section_id = $section_id;
			$this->type = $type;
			$this->name = $name;
			$this->curriculum = $curriculum;
			$this->sy = $sy;
			$this->semester = $semester;
			$this->year_level = $year_level;
			$this->date_created = $date_created;
			$this->date_modified = $date_modified;
		}
	}

	class SectionSubject{
		public $section_subject_id;
		public $section;
		public $subject;
		public $subject_id;
		public $curriculum_subject_id;
		public $date_created;
		public $modified;
		public $units;
		public $unitsLab;
		public $isHalf;
		public $code;
		function __construct(
			$section_subject_id, $section, $subject, $date_created, $modified
		){
			$this->section_subject_id = $section_subject_id;
			$this->section = $section;
			$this->subject = $subject;
			$this->date_created = $date_created;
			$this->modified = $modified;
		}
	}

	//Block or Free Section
	class SectionTypes{
		public $type_id;
		public $description;

		function __construct($_type_id, $_description){
			$this->type_id		= $_type_id;
			$this->description	= $_description;
		}
	}

	class SubjectSchedule{
		public $schedule_id;
		public $day;
		public $from;
		public $to;
		public $room;
		public $instructor; //faculty
		public $isHalf;
		public $max_students;
		public $section_subject_id;

		function __construct($schedule_id, $day, $from, $to, $room, $instructor = null){
			$this->schedule_id = $schedule_id;
			$this->day = $day;
			$this->from = $from;
			$this->to = $to;
			$this->room = $room;
			$this->instructor = $instructor;
			$this->isHalf = false;
		}
	}

	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class ScheduleManager{

		public $error = array();
		public $section_id = -1; //will be > 0 if successfully added section
		public $section_subject_id = -1; //will be > 0 if success added subject
		private $conn = null;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}

		/*--------------------------------------------------------

			SECTION TYPES [ SELECT ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetSectionTypes($type_id = null){
			$types = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT TypeID, Description FROM `enl-section_type` ";

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
							$types[$ctr] = new SectionTypes($row['TypeID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $types;
		}

		/*--------------------------------------------------------

			SECTIONS [ SELECT | INSERT | UPDATE | DELETE ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetSections($section_id = null, $sy_id = null, $sem_id = null, $level = null){
			$sections = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `SectionID`, `Type`, `Name`, sc.Course, scl.College, `Curriculum`, `SY`, `Semester`, `YearLevel`, es.`DateCreated`, es.`Modified` FROM `enl-sections` es ";
				$query .= "LEFT JOIN `sch-curriculum` sc ON sc.CurriculumID=es.Curriculum ";
				$query .= "LEFT JOIN `sch-course_list` scl ON scl.CourseID=sc.Course ";
				$query .= "WHERE 1=1 ";

				if($section_id != null){
					$query .= "AND ";
					$query .= "SectionID=";
					$query .= $section_id;
					$query .= " ";
				}

				if($sy_id != null){
					$query .= "AND ";
					$query .= "SY=";
					$query .= $sy_id;
					$query .= " ";
				}

				$query .= "ORDER BY Name ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$sections = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$sections[$ctr] = new Section(
								$row['SectionID'],
								$row['Type'],
								$row['Name'],
								$row['Curriculum'],
								$row['SY'],
								$row['Semester'],
								$row['YearLevel'],
								$row['DateCreated'],
								$row['Modified']
								);
							$sections[$ctr]->college = $row['College'];
							$sections[$ctr]->course = $row['Course'];
							$ctr++;
						}
					}
				}
			}

			return $sections;
		}

		//Will always return null for errors else an array
		function GetSectionsBySectionSubject($section_subject){
			$sections = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `SectionID`, `Type`, `Name`, sc.Course, scl.College, `Curriculum`, `SY`, `Semester`, `YearLevel`, es.`DateCreated`, es.`Modified` FROM `enl-sections` es ";
				$query .= "LEFT JOIN `sch-curriculum` sc ON sc.CurriculumID=es.Curriculum ";
				$query .= "LEFT JOIN `sch-course_list` scl ON scl.CourseID=sc.Course ";
				$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.Section=es.SectionID ";
				$query .= "WHERE 1=1 AND ess.SectionSubjectID={$section_subject} ";

				$query .= "ORDER BY Name ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$sections = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$sections[$ctr] = new Section(
								$row['SectionID'],
								$row['Type'],
								$row['Name'],
								$row['Curriculum'],
								$row['SY'],
								$row['Semester'],
								$row['YearLevel'],
								$row['DateCreated'],
								$row['Modified']
								);
							$sections[$ctr]->college = $row['College'];
							$sections[$ctr]->course = $row['Course'];
							$ctr++;
						}
					}
				}
			}

			return $sections;
		}

		//Will always return null for errors else an array
		function GetSectionsByKey($section_id = null, $sy_id = null, $sem_id = null, $level_id = null){
			$sections = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `SectionID`, `Type`, `Name`, sc.Course, scl.College, `Curriculum`, `SY`, `Semester`, `YearLevel`, es.`DateCreated`, es.`Modified` FROM `enl-sections` es ";
				$query .= "LEFT JOIN `sch-curriculum` sc ON sc.CurriculumID=es.Curriculum ";
				$query .= "LEFT JOIN `sch-course_list` scl ON scl.CourseID=sc.Course ";
				$query .= "WHERE 1=1 ";

				if($section_id != null){
					$query .= "AND ";
					$query .= "SectionID=";
					$query .= $section_id;
					$query .= " ";
				}

				if($sy_id != null){
					$query .= "AND ";
					$query .= "SY=";
					$query .= $sy_id;
					$query .= " ";
				}

				if($sem_id != null){
					$query .= "AND ";
					$query .= "Semester=";
					$query .= $sem_id;
					$query .= " ";
				}

				if($level_id != null){
					$query .= "AND ";
					$query .= "YearLevel=";
					$query .= $level_id;
					$query .= " ";
				}

				$query .= "ORDER BY Name ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$sections = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$sections[$row['SectionID']] = new Section(
								$row['SectionID'],
								$row['Type'],
								$row['Name'],
								$row['Curriculum'],
								$row['SY'],
								$row['Semester'],
								$row['YearLevel'],
								$row['DateCreated'],
								$row['Modified']
								);
							$sections[$row['SectionID']]->college = $row['College'];
							$sections[$row['SectionID']]->course = $row['Course'];
							$ctr++;
						}
					}
				}
			}

			return $sections;
		}

		function GetSectionsByCollege($college_id, $sy = null, $sem = null, $level = null){
			$sections = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `SectionID`, `Type`, `Name`, sc.Course, scl.College, `Curriculum`, `SY`, `Semester`, `YearLevel`, es.`DateCreated`, es.`Modified` FROM `enl-sections` es ";
				$query .= "LEFT JOIN `sch-curriculum` sc ON sc.CurriculumID=es.Curriculum ";
				$query .= "LEFT JOIN `sch-course_list` scl ON scl.CourseID=sc.Course ";
				$query .= "WHERE 1=1 ";
				$query .= "AND scl.College={$college_id} ";

				if($sy != null){
					$query .= "AND es.SY={$sy} ";
				}

				if($sy != null){
					$query .= "AND es.Semester={$sem} ";
				}

				if($level != null){
					$query .= "AND es.YearLevel={$level} ";
				}

				$query .= "ORDER BY Name ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$sections = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$sections[$row['SectionID']] = new Section(
								$row['SectionID'],
								$row['Type'],
								$row['Name'],
								$row['Curriculum'],
								$row['SY'],
								$row['Semester'],
								$row['YearLevel'],
								$row['DateCreated'],
								$row['Modified']
								);
							$sections[$row['SectionID']]->college = $row['College'];
							$sections[$row['SectionID']]->course = $row['Course'];
							$ctr++;
						}
					}
				}
			}

			return $sections;
		}

		function GetSectionsByCourse($course_id, $sy = null, $sem = null, $level = null){
			$sections = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `SectionID`, `Type`, `Name`, sc.Course, scl.College, `Curriculum`, `SY`, `Semester`, `YearLevel`, es.`DateCreated`, es.`Modified` FROM `enl-sections` es ";
				$query .= "LEFT JOIN `sch-curriculum` sc ON sc.CurriculumID=es.Curriculum ";
				$query .= "LEFT JOIN `sch-course_list` scl ON scl.CourseID=sc.Course ";
				$query .= "WHERE 1=1 ";
				$query .= "AND sc.Course={$course_id} ";

				if($sy != null){
					$query .= "AND es.SY={$sy} ";
				}

				if($sem != null){
					$query .= "AND es.Semester={$sem} ";
				}

				if($level != null){
					$query .= "AND es.YearLevel={$level} ";
				}

				$query .= "ORDER BY Name ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$sections = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$sections[$row['SectionID']] = new Section(
								$row['SectionID'],
								$row['Type'],
								$row['Name'],
								$row['Curriculum'],
								$row['SY'],
								$row['Semester'],
								$row['YearLevel'],
								$row['DateCreated'],
								$row['Modified']
								);
							$sections[$row['SectionID']]->college = $row['College'];
							$sections[$row['SectionID']]->course = $row['Course'];
							$ctr++;
						}
					}
				}
			}

			return $sections;
		}

		//Add Section to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddSection($name, $type, $curriculum, $sy, $semester, $year_level){

			//clean input
			$name = strtoupper(ucwords(addslashes(strip_tags($name))));
			$type = (int) $type;
			$curriculum = (int) $curriculum;
			$sy = (int) $sy;
			$semester = (int) $semester;
			$year_level = (int) $year_level;

			if($name == ""){
				$this->error[sizeof($this->error)] = "Section Name cannot be blank.";
			}

			if($type <= 0){
				$this->error[sizeof($this->error)] = "Section Type not found.";
			}

			if($curriculum <= 0){
				$this->error[sizeof($this->error)] = "Curriculum not found.";
			}

			if($sy <= 0){
				$this->error[sizeof($this->error)] = "School Year not found.";
			}

			if($semester <= 0){
				$this->error[sizeof($this->error)] = "Semester not found.";
			}

			if($year_level <= 0){
				$this->error[sizeof($this->error)] = "Year Level not found.";
			}

			$result = false;

			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `enl-sections`(`Type`, `Name`, `Curriculum`, `SY`, `Semester`, `YearLevel`, `DateCreated`, `Modified`) ";
				$query .= "VALUES ({$type},'{$name}',{$curriculum},{$sy},{$semester},{$year_level},NOW(),NOW())";

				$conn->query($query);

				if($conn->insert_id > 0){
					$this->section_id = $conn->insert_id;
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding section. Duplicate found!";
					}
				}
			}

			return $result;

		}

		//Delete Section in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteSection($section_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `enl-sections` ";
				$query .= "WHERE SectionID=";
				$query .= $section_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting section. Information in use.";
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			SECTION SUBJECTS [ SELECT | INSERT | UPDATE | DELETE ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetSectionSubjects($section_id = null){
			$subjects = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;
				$hnd = new SubjectManager($conn);

				$query = "SELECT `SectionSubjectID`, `Section`, ess.`Subject` AS CurriculumSubjectID, ss.`SubjectID` AS Subject, ess.`DateCreated`, ess.`Modified` FROM `enl-section_subjects` ess ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=ess.Subject "; //Curriculum Subjects
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=scs.Subject "; //Subjects
				$query .= "WHERE 1=1 ";

				if($section_id != null){
					$query .= "AND ";
					$query .= "Section=";
					$query .= $section_id;
					$query .= " ";
				}

				$query .= "ORDER BY ss.Code ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['CurriculumSubjectID']] = new SectionSubject(
								$row['SectionSubjectID'],
								$row['Section'],
								$row['Subject'],
								$row['DateCreated'],
								$row['Modified']
								);

							$subjects[$row['CurriculumSubjectID']]->subject_id = $row['Subject'];
							$subjects[$row['CurriculumSubjectID']]->curriculum_subject_id = $row['CurriculumSubjectID'];
							$subjects[$row['CurriculumSubjectID']]->section = $this->GetSections($row['Section']);
							$subject = $hnd->GetSubjects($subjects[$row['CurriculumSubjectID']]->subject);
							$subjects[$row['CurriculumSubjectID']]->subject = $subject[0]->description;
							$subjects[$row['CurriculumSubjectID']]->code = $subject[0]->code;
							$subjects[$row['CurriculumSubjectID']]->units = $subject[0]->units;
							$subjects[$row['CurriculumSubjectID']]->unitsLab = $subject[0]->unitsLab;
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetSectionSubjectsByKey($section_id = null, $sem_id = null, $sy_id = null){
			$subjects = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;
				$hnd = new SubjectManager($conn);

				$query = "SELECT `SectionSubjectID`, `Section`, ess.`Subject` AS CurriculumSubjectID, ss.`SubjectID` AS Subject, ";
				$query .= "ess.`DateCreated`, ess.`Modified`, ss.IsHalfFee ";
				$query .= "FROM `enl-section_subjects` ess ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=ess.Subject "; //Curriculum Subjects
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=scs.Subject "; //Subjects
				$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
				$query .= "WHERE 1=1 ";
				$query .= "AND (SELECT COUNT(*) FROM `enl-subject_schedule` WHERE SectionSubject=ess.SectionSubjectID) > 0 ";

				if($section_id != null){
					$query .= "AND ";
					$query .= "ess.Section=";
					$query .= $section_id;
					$query .= " ";
				}

				if($sem_id != null){
					$query .= "AND ";
					$query .= "es.Semester=";
					$query .= $sem_id;
					$query .= " ";
				}

				if($sy_id != null){
					$query .= "AND ";
					$query .= "es.SY=";
					$query .= $sy_id;
					$query .= " ";
				}

				$query .= "ORDER BY es.Name, ss.Code ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['SectionSubjectID']] = new SectionSubject(
								$row['SectionSubjectID'],
								$row['Section'],
								$row['Subject'],
								$row['DateCreated'],
								$row['Modified']
								);

							$subjects[$row['SectionSubjectID']]->curriculum_subject_id = $row['CurriculumSubjectID'];
							$subjects[$row['SectionSubjectID']]->section = $this->GetSections($row['Section']);
							$subject = $hnd->GetSubjects($subjects[$row['SectionSubjectID']]->subject);
							$subjects[$row['SectionSubjectID']]->subject = $subject[0]->description;
							$subjects[$row['SectionSubjectID']]->code = $subject[0]->code;
							$subjects[$row['SectionSubjectID']]->units = $subject[0]->units;
							$subjects[$row['SectionSubjectID']]->unitsLab = $subject[0]->unitsLab;
							$subjects[$row['SectionSubjectID']]->isHalf = $row['IsHalfFee'];
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetSectionSubjectById($section_subject_id){
			$subjects = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;
				$hnd = new SubjectManager($conn);

				$query = "SELECT `SectionSubjectID`, `Section`, ess.`Subject` AS CurriculumSubjectID, ss.`SubjectID` AS Subject, ess.`DateCreated`, ess.`Modified` FROM `enl-section_subjects` ess ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=ess.Subject "; //Curriculum Subjects
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=scs.Subject "; //Subjects
				$query .= "WHERE 1=1 AND SectionSubjectID={$section_subject_id} ";

				$query .= "ORDER BY ss.Code ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['CurriculumSubjectID']] = new SectionSubject(
								$row['SectionSubjectID'],
								$row['Section'],
								$row['Subject'],
								$row['DateCreated'],
								$row['Modified']
								);

							$subjects[$row['CurriculumSubjectID']]->curriculum_subject_id = $row['CurriculumSubjectID'];
							$subjects[$row['CurriculumSubjectID']]->section = $this->GetSections($row['Section']);
							$subject = $hnd->GetSubjects($subjects[$row['CurriculumSubjectID']]->subject);
							$subjects[$row['CurriculumSubjectID']]->subject = $subject[0]->description;
							$subjects[$row['CurriculumSubjectID']]->code = $subject[0]->code;
							$subjects[$row['CurriculumSubjectID']]->units = $subject[0]->units;
							$subjects[$row['CurriculumSubjectID']]->unitsLab = $subject[0]->unitsLab;
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Add Section Subject to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddSectionSubject($section, $subject){

			//clean input
			$section = (int) $section;
			$subject = (int) $subject;

			if($section <= 0){
				$this->error[sizeof($this->error)] = "Section not found.";
			}

			if($subject <= 0){
				$this->error[sizeof($this->error)] = "Subject not found.";
			}

			$result = false;

			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `enl-section_subjects`(`Section`, `Subject`, `DateCreated`, `Modified`) ";
				$query .= "VALUES ({$section},{$subject},NOW(), NOW())";

				$conn->query($query);

				if($conn->insert_id > 0){
					$this->section_subject_id = $conn->insert_id;
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding section subject. Duplicate found!";
					}
				}
			}

			return $result;

		}

		//Delete Section Subject in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteSectionSubject($section_subject_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$this->DeleteSubjectSchedules($section_subject_id);

				$query = "DELETE FROM `enl-section_subjects` ";
				$query .= "WHERE SectionSubjectID=";
				$query .= $section_subject_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting section subject. Information in use.";
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			SUBJECT SCHEDULES [ SELECT | INSERT | UPDATE | DELETE ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		//-->section_subject_id
		function GetAllSubjectSchedulesBySemYear($sem_id, $sy_id){

			$schedules = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				//Query for getting the schedules of subjects in section
				$query = "SELECT `ScheduleID`, `SectionSubject`, `Day`, `From`, `To`, `Faculty`, `Room`, `MaxStudents`, ssc.`DateCreated`, ssc.`Modified` ";
				$query .= "FROM `enl-subject_schedule` ssc ";
				$query .= "LEFT JOIN `enl-section_subjects` ss ON ss.SectionSubjectID=ssc.SectionSubject "; //Section Subjects
				$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ss.Section "; //Section Subjects
				$query .= " ";
				$query .= "WHERE 1 ";
				$query .= "AND es.SY={$sy_id} AND Semester={$sem_id} "; //Curriculum Subject

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$schedules = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$schedules[$row['ScheduleID']] = new SubjectSchedule($row['ScheduleID'], $row['Day'], $row['From'], $row['To'], $row['Room'], $row['Faculty']);
							$schedules[$row['ScheduleID']]->max_students = $row['MaxStudents'];
							$schedules[$row['ScheduleID']]->section_subject_id = $row['SectionSubject'];
							$ctr++;
						}
					}
				}
			}

			return $schedules;
		}

		//Will always return null for errors else an array
		//-->section_subject_id
		function GetSubjectSchedulesBySubject($section_subject, $section_id=null){

			$schedules = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				//Query for getting the schedules of subjects in section
				$query = "SELECT `ScheduleID`, `SectionSubject`, `Day`, `From`, `To`, `Faculty`, `Room`, ";
				$query .= "`MaxStudents`, ssc.`DateCreated`, ssc.`Modified`, ssb.IsHalfFee ";
				$query .= "FROM `enl-subject_schedule` ssc ";
				$query .= "LEFT JOIN `enl-section_subjects` ss ON ss.SectionSubjectID=ssc.SectionSubject "; //Section Subjects
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=ss.Subject ";
				$query .= "LEFT JOIN `sch-subjects` ssb ON ssb.SubjectID=scs.Subject ";
				$query .= "WHERE 1 ";
				$query .= "AND ss.Subject=ss.Subject AND ss.SectionSubjectID={$section_subject} "; //Curriculum Subject

				if($section_id != null){
					$query .= "AND ";
					$query .= "ss.Section={$section_id} ";
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$schedules = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$schedules[$row['ScheduleID']] = new SubjectSchedule($row['ScheduleID'], $row['Day'], $row['From'], $row['To'], $row['Room'], $row['Faculty']);
							$schedules[$row['ScheduleID']]->max_students = $row['MaxStudents'];
							$schedules[$row['ScheduleID']]->section_subject_id = $row['SectionSubject'];
							$schedules[$row['ScheduleID']]->isHalf = $row['IsHalfFee'];
							$ctr++;
						}
					}
				}
			}

			return $schedules;
		}

		//Add Subject Schedule to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddSubjectSchedule($section_subject, $day, $from, $to, $faculty, $room, $max_students){

			//clean input
			$section_subject = (int) $section_subject;
			$day = (int) $day;
			$from = (int) $from;
			$to = (int) $to;
			$faculty = (int) $faculty;
			$room = (int) $room;
			$max_students = (int) $max_students;

			if($section_subject <= 0){
				$this->error[sizeof($this->error)] = "Section Subject not found.";
			}

			if($day <= 0){
				$this->error[sizeof($this->error)] = "Day not found.";
			}

			if($from <= 0){
				$this->error[sizeof($this->error)] = "From not found.";
			}

			if($to <= 0){
				$this->error[sizeof($this->error)] = "To not found.";
			}

			if($faculty <= 0){
				$this->error[sizeof($this->error)] = "Faculty not found.";
			}

			if($room <= 0){
				$this->error[sizeof($this->error)] = "Room not found.";
			}

			$result = false;

			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `enl-subject_schedule`(`SectionSubject`, `Day`, `From`, `To`, `Faculty`, `Room`, `MaxStudents`, `DateCreated`, `Modified`) ";
				$query .= "VALUES ({$section_subject},{$day},{$from},{$to},{$faculty},{$room},{$max_students},NOW(),NOW())";

				$conn->query($query);

				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding subject schedule. Duplicate found!";
					}
				}
			}

			return $result;

		}

		//Add Subject Schedule to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function UpdateSubjectSchedule($schedule_id, $day, $from, $to, $faculty, $room, $max_students){

			//clean input
			$day = (int) $day;
			$from = (int) $from;
			$to = (int) $to;
			$faculty = (int) $faculty;
			$room = (int) $room;
			$max_students = (int) $max_students;

			if($schedule_id <= 0){
				$this->error[sizeof($this->error)] = "Section Schedule not found.";
			}

			if($day <= 0){
				$this->error[sizeof($this->error)] = "Day not found.";
			}

			if($from <= 0){
				$this->error[sizeof($this->error)] = "From not found.";
			}

			if($to <= 0){
				$this->error[sizeof($this->error)] = "To not found.";
			}

			if($faculty <= 0){
				$this->error[sizeof($this->error)] = "Faculty not found.";
			}

			if($room <= 0){
				$this->error[sizeof($this->error)] = "Room not found.";
			}

			$result = false;

			if(sizeof($this->error) > 0){
				print_r($this->error); exit();
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query  = "UPDATE `enl-subject_schedule` SET `Day`={$day}, ";
				$query .= "`From`={$from}, `To`={$to}, `Faculty`={$faculty}, ";
				$query .= "`Room`={$room}, `MaxStudents`={$max_students},`Modified`=NOW() ";
				$query .= "WHERE ScheduleID={$schedule_id} ";

				$conn->query($query);

				if($conn->affected_rows > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error updating subject schedule. Duplicate found!";
					}
				}
			}

			return $result;

		}

		//Delete Subject Schedule in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteSubjectSchedules($section_subject){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `enl-subject_schedule` ";
				$query .= "WHERE SectionSubject=";
				$query .= $section_subject;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting subject schedules. Information in use.";
					}
				}
			}

			return $result;

		}

		//Delete Subject Schedule in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteSubjectSchedule($section_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `enl-subject_schedule` ";
				$query .= "WHERE ScheduleID=";
				$query .= $section_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting subject schedule. Information in use.";
					}
				}
			}

			return $result;

		}

	}
?>
