<?php
	/*-----------------------------------------------

		CURRICULUM
		-contains curriculum details

		DEPENDENCIES:
			1) cllgs.inc.php - class for colleges
			2) grds.inc.php - class for grades
			3) sbjcts.inc.php - class for subjects
			4) schl.inc.php - class for school++

		Last Modified: 2012-03-04

	-------------------------------------------------*/

	class CurriculumSubject{
		public $curriculum_subject_id; //int
		public $subject_id;
		public $subject; //subject class
		public $code;
		public $curriculum; //int
		public $semester; //semester class
		public $year_level; //year level class
		public $created;
		public $modified;
		public $units;
		public $unitsLab;

		function __construct(
			$curriculum_subject_id,
			$subject_id,
			$subject,
			$code,
			$curriculum,
			$semester,
			$year_level,
			$created,
			$modified,
			$units,
			$unitsLab
		){
			$this->curriculum_subject_id = $curriculum_subject_id;
			$this->subject_id = $subject_id;
			$this->subject = $subject;
			$this->code = $code;
			$this->curriculum = $curriculum;
			$this->semester = $semester;
			$this->year_level = $year_level;
			$this->created = $created;
			$this->modified = $modified;
			$this->units = $units;
			$this->unitsLab = $unitsLab;
		}
	}

	class Curriculum{

		public $curriculum_id;
		public $school_year;
		public $course;
		public $created;
		public $modified;
		public $info;
		public $subjects; //array of the CurriculumSubjectsClass
		public $school_year_id;

		function __construct(
			$curriculum_id, $school_year, $course,
			$created, $modified, $info, $school_year_id
		)
		{
			$this->curriculum_id = $curriculum_id;
			$this->school_year = $school_year;
			$this->course = $course;
			$this->created = $created;
			$this->modified = $modified;
			$this->info = $info;
			$this->school_year_id = $school_year_id;
		}

	}

	class Prerequisite{
		public $prerequisite_id;
		public $curriculum_subject_id;
		public $description;
		public $code;
		public $created;
		public $modified;
		public $units;

		function __construct($id, $subject_id, $description, $code, $created, $modified, $units){
			$this->prerequisite_id = $id;
			$this->curriculum_subject_id = $subject_id;
			$this->description = $description;
			$this->code = $code;
			$this->created = $created;
			$this->modified = $modified;
			$this->units = $units;
		}
	}

	class Corequisite{
		public $corequisite_id;
		public $curriculum_subject_id;
		public $description;
		public $code;
		public $created;
		public $modified;
		public $units;

		function __construct($id, $subject_id, $description, $code, $created, $modified, $units){
			$this->corequisite_id = $id;
			$this->curriculum_subject_id = $subject_id;
			$this->description = $description;
			$this->code = $code;
			$this->created = $created;
			$this->modified = $modified;
			$this->units = $units;
		}
	}

	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class CurriculumManager{

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

			CURRICULUM [ SELECT | INSERT | DELETE]
			//2010-2011, 2011-2012

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetCurriculums($course_id, $curriculum_id = null){
			$curriculums = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CurriculumID, SchoolYear, c.Created, sc.Start, sc.End, Course, c.Modified, cl.Code FROM `sch-curriculum` c ";
				$query .= "LEFT JOIN `sch-school_years` sc ON SchoolYear=sc.SchoolYearID ";
				$query .= "LEFT JOIN `sch-course_list` cl ON Course=cl.CourseID ";
				$query .= "WHERE Course={$course_id} ";


				if($curriculum_id != null){
					$query .= "AND ";
					$query .= "CurriculumID=";
					$query .= $curriculum_id;
					$query .= " ";
				}

				$query .= "ORDER BY cl.Code, sc.Start ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$curriculums = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$curriculums[$row['CurriculumID']] = new Curriculum($row['CurriculumID'], $row['Start'] . "-" . $row['End'], $row['Course'], $row['Created'], $row['Modified'], $row['Code'] . " " . $row['Start'] . "-" . $row['End'], $row['SchoolYear']);
							$ctr++;
						}
					}
				}
			}

			return $curriculums;
		}

		//Will always return null for errors else an array
		function GetCurriculumById($curriculum_id){
			$curriculums = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CurriculumID, SchoolYear, c.Created, sc.Start, sc.End, Course, c.Modified, cl.Code FROM `sch-curriculum` c ";
				$query .= "LEFT JOIN `sch-school_years` sc ON SchoolYear=sc.SchoolYearID ";
				$query .= "LEFT JOIN `sch-course_list` cl ON Course=cl.CourseID ";
				$query .= "WHERE CurriculumID={$curriculum_id} ";

				$query .= "ORDER BY cl.Code, sc.Start ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$curriculums = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$curriculums[$ctr] = new Curriculum($row['CurriculumID'], $row['Start'] . "-" . $row['End'], $row['Course'], $row['Created'], $row['Modified'], $row['Code'] . " " . $row['Start'] . "-" . $row['End'], $row['SchoolYear']);
							$ctr++;
						}
					}
				}
			}

			return $curriculums;
		}

		//Will always return null for errors else an array
		function VerifyCurriculum($course_id, $sy_id){
			$curriculum_id = -1;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CurriculumID FROM `sch-curriculum` ";
				$query .= "WHERE Course={$course_id} AND SchoolYear={$sy_id} ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$curriculums = array();
					$ctr = 0;
					if($result->num_rows > 0){
						$row = $result->fetch_assoc();
						$curriculum_id = $row['CurriculumID'];
					}
				}

			}

			return $curriculum_id;
		}

		//Add Curriculum to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddCurriculum($school_year, $course){

			//clean input
			if($school_year <= 0){
				$this->error[sizeof($this->error)] = "School Year: Invalid selection.";
			}

			if($course <= 0){
				$this->error[sizeof($this->error)] = "Course: Invalid selection.";
			}

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-curriculum`(SchoolYear, Course, Modified, Created) ";
					$query .= "VALUES({$school_year}, {$course}, NOW(), NOW())";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding curriculum. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}
			return $result;

		}

		//Delete Curriculum in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteCurriculum($curriculum_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-curriculum` ";
				$query .= "WHERE CurriculumID=";
				$query .= $curriculum_id;

				$conn->query($query);


				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting curriculum. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

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

			if($this->conn == null){
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
						$this->error = "Error adding subject group. Duplicate found!";
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

			if($this->conn == null){
				$error = "No defined connection.";
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
						$this->error = "Error editing subject group. Duplicate found!";
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
		function GetSubjects($curriculum_id, $sort_by='Description'){
			$subjects = null;
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CurriculumSubjectID, Curriculum, ss.Description AS Subject, ss.Code AS SubjectCode, ss.SubjectID, Semester, yl.Description AS YearLevel, s.Created, s.Modified, ss.Units, ss.UnitsLab ";
				$query .= "FROM `sch-curriculum_subjects` s ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=s.Subject ";
				$query .= "LEFT JOIN `sch-year_levels` yl ON yl.YearLevelID=s.YearLevel ";
				$query .= "WHERE Curriculum={$curriculum_id} ";

				//$query .= "ORDER BY ss.{$sort_by} ";
				$query .= "ORDER BY s.YearLevel, ss.Description";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['CurriculumSubjectID']] = new CurriculumSubject(
								$row['CurriculumSubjectID'],
								$row['SubjectID'],
								$row['Subject'],
								$row['SubjectCode'],
								$row['Curriculum'],
								$row['Semester'],
								$row['YearLevel'],
								$row['Created'],
								$row['Modified'],
								$row['Units'],
								$row['UnitsLab']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}


		//Will always return null for errors else an array
		function GetAllSubjectsByKey($sort_by='Description'){
			$subjects = null;
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CurriculumSubjectID, Curriculum, ss.Description AS Subject, ss.Code AS SubjectCode, ss.SubjectID, Semester, yl.Description AS YearLevel, s.Created, s.Modified, ss.Units, ss.UnitsLab ";
				$query .= "FROM `sch-curriculum_subjects` s ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=s.Subject ";
				$query .= "LEFT JOIN `sch-year_levels` yl ON yl.YearLevelID=s.YearLevel ";

				//$query .= "ORDER BY ss.{$sort_by} ";
				$query .= "ORDER BY s.YearLevel, ss.Description";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['CurriculumSubjectID']] = new CurriculumSubject(
								$row['CurriculumSubjectID'],
								$row['SubjectID'],
								$row['Subject'],
								$row['SubjectCode'],
								$row['Curriculum'],
								$row['Semester'],
								$row['YearLevel'],
								$row['Created'],
								$row['Modified'],
								$row['Units'],
								$row['UnitsLab']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetSectionSubjects($curriculum_id, $year, $semester){
			$subjects = null;
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CurriculumSubjectID, Curriculum, ss.Description AS Subject, ss.Code AS SubjectCode, ss.SubjectID, Semester, yl.Description AS YearLevel, s.Created, s.Modified, ss.Units, ss.UnitsLab ";
				$query .= "FROM `sch-curriculum_subjects` s ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=s.Subject ";
				$query .= "LEFT JOIN `sch-year_levels` yl ON yl.YearLevelID=s.YearLevel ";
				$query .= "WHERE Curriculum={$curriculum_id} ";
				$query .= "AND s.YearLevel={$year} ";
				$query .= "AND s.Semester={$semester} ";

				//$query .= "ORDER BY ss.{$sort_by} ";
				$query .= "ORDER BY s.YearLevel, ss.Description";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['CurriculumSubjectID']] = new CurriculumSubject(
								$row['CurriculumSubjectID'],
								$row['SubjectID'],
								$row['Subject'],
								$row['SubjectCode'],
								$row['Curriculum'],
								$row['Semester'],
								$row['YearLevel'],
								$row['Created'],
								$row['Modified'],
								$row['Units'],
								$row['UnitsLab']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetPrerequisitesByCode($subject_id){
			$subjects = null;
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `PrerequisiteID`, scs.CurriculumSubjectID,ss.Code, ss.Description, sp.`Created`, sp.`Modified`, ss.Units, ss.UnitsLab ";
				$query .= "FROM `sch-prerequisites` sp ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=sp.Prerequisite ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=scs.Subject ";
				$query .= "WHERE CurriculumSubject={$subject_id} ";

				$query .= "ORDER BY ss.Code";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['CurriculumSubjectID']] = new Prerequisite(
								$row['PrerequisiteID'],
								$row['CurriculumSubjectID'],
								$row['Description'],
								$row['Code'],
								$row['Created'],
								$row['Modified'],
								$row['Units'],
								$row['UnitsLab']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetCorequisitesByCode($subject_id){
			$subjects = null;
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT DISTINCT `CorequisiteID`, scs.CurriculumSubjectID,ss.Code, ss.Description, sp.`Created`, sp.`Modified`, ss.Units, ss.UnitsLab ";
				$query .= "FROM `sch-corequisites` sp ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=sp.Corequisite ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=scs.Subject ";
				$query .= "WHERE CurriculumSubject={$subject_id} ";

				$query .= "ORDER BY ss.Code";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$ctr] = new Corequisite(
								$row['CorequisiteID'],
								$row['CurriculumSubjectID'],
								$row['Description'],
								$row['Code'],
								$row['Created'],
								$row['Modified'],
								$row['Units'],
								$row['UnitsLab']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetSubjectsByCode($curriculum_id, $sort_by='Description'){
			$subjects = null;
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CurriculumSubjectID, Curriculum, ss.Description AS Subject, ss.Code AS SubjectCode, ss.SubjectID, Semester, yl.equivalent AS YearLevel, s.Created, s.Modified, ss.Units, ss.UnitsLab ";
				$query .= "FROM `sch-curriculum_subjects` s ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=s.Subject ";
				$query .= "LEFT JOIN `sch-year_levels` yl ON yl.YearLevelID=s.YearLevel ";
				$query .= "WHERE Curriculum={$curriculum_id} ";

				//$query .= "ORDER BY ss.{$sort_by} ";
				$query .= "ORDER BY s.YearLevel, Semester, ss.Code";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['CurriculumSubjectID']] = new CurriculumSubject(
								$row['CurriculumSubjectID'],
								$row['SubjectID'],
								$row['Subject'],
								$row['SubjectCode'],
								$row['Curriculum'],
								$row['Semester'],
								$row['YearLevel'],
								$row['Created'],
								$row['Modified'],
								$row['Units'],
								$row['UnitsLab']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetSubjectsByCodeForPrerequisite($curriculum_id, $level, $sem){
			$subjects = null;
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CurriculumSubjectID, Curriculum, ss.Description AS Subject, ss.Code AS SubjectCode, ss.SubjectID, Semester, yl.equivalent AS YearLevel, s.Created, s.Modified, ss.Units, ss.UnitsLab ";
				$query .= "FROM `sch-curriculum_subjects` s ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=s.Subject ";
				$query .= "LEFT JOIN `sch-year_levels` yl ON yl.YearLevelID=s.YearLevel ";
				$query .= "WHERE Curriculum={$curriculum_id} AND (yl.equivalent < {$level} OR (yl.equivalent={$level} AND Semester < {$sem})) ";

				//$query .= "ORDER BY ss.{$sort_by} ";
				$query .= "ORDER BY s.YearLevel, Semester, ss.Code";

				$result = $conn->query($query);
				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['CurriculumSubjectID']] = new CurriculumSubject(
								$row['CurriculumSubjectID'],
								$row['SubjectID'],
								$row['Subject'],
								$row['SubjectCode'],
								$row['Curriculum'],
								$row['Semester'],
								$row['YearLevel'],
								$row['Created'],
								$row['Modified'],
								$row['Units'],
								$row['UnitsLab']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		function GetSubjectsByCodeForCorequisite($curriculum_id, $level, $sem){
			$subjects = null;
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT CurriculumSubjectID, Curriculum, ss.Description AS Subject, ss.Code AS SubjectCode, ss.SubjectID, Semester, yl.equivalent AS YearLevel, s.Created, s.Modified, ss.Units, ss.UnitsLab ";
				$query .= "FROM `sch-curriculum_subjects` s ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=s.Subject ";
				$query .= "LEFT JOIN `sch-year_levels` yl ON yl.YearLevelID=s.YearLevel ";
				$query .= "WHERE Curriculum={$curriculum_id} AND (yl.equivalent={$level} AND Semester = {$sem}) ";

				//$query .= "ORDER BY ss.{$sort_by} ";
				$query .= "ORDER BY s.YearLevel, Semester, ss.Code";

				$result = $conn->query($query);
				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$row['CurriculumSubjectID']] = new CurriculumSubject(
								$row['CurriculumSubjectID'],
								$row['SubjectID'],
								$row['Subject'],
								$row['SubjectCode'],
								$row['Curriculum'],
								$row['Semester'],
								$row['YearLevel'],
								$row['Created'],
								$row['Modified'],
								$row['Units'],
								$row['UnitsLab']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Add Curriculum Subject to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		//-->also adds the prerequisite and corequisite if found
		function AddSubject(
			$curriculum,
			$subject,
			$semester, //int
			$year_level, //int
			$prerequisites = null,
			$corequisites = null
		){

			$this->error = array();

			//clean input
			if($subject <= 0){
				$this->error[sizeof($this->error)] = "Subject: Invalid Selection.";
			}

			if($curriculum <= 0){
				$this->error[sizeof($this->error)] = "Curriculum: Invalid Selection.";
			}

			if($semester <= 0){
				$this->error[sizeof($this->error)] = "Semester: Invalid Selection.";
			}

			if($year_level <= 0){
				$this->error[sizeof($this->error)] = "Year Level: Invalid Selection.";
			}

			//remove -1 from the prerequisites
			if($prerequisites != null){
				$ctr = 0;
				foreach($prerequisites as $item){
					if($prerequisites == -1){
						unset($prerequisites[$ctr]);
					}
					$ctr++;
				}
				$prerequisites = array_values($prerequisites);
			}

			//remove -1 from the corequisites
			if($corequisites != null){
				$ctr = 0;
				foreach($corequisites as $item){
					if($corequisites == -1){
						unset($corequisites[$ctr]);
					}
					$ctr++;
				}
				$corequisites = array_values($corequisites);
			}

			//check if the prerequisite is same as the subject being added
			if($prerequisites != null){
				$test = false;
				foreach($prerequisites as $item){
					if($item == $subject && $test == false){
						$this->error[sizeof($this->error)] = "Pre-requisite: Cannot be the same as the subject being added.";
						$test = true;
					}
				}
			}

			//check if the corequisite is same as the subject being added
			if($corequisites != null){
				$test = false;
				foreach($corequisites as $item){
					if($item == $subject && $test == false){
						$this->error[sizeof($this->error)] = "Co-requisite: Cannot be the same as the subject being added.";
						$test = true;
					}
				}
			}

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-curriculum_subjects`(Curriculum, Subject, Semester, ";
					$query .= "YearLevel, Created, Modified) ";
					$query .= "VALUES({$curriculum}, {$subject}, {$semester}, {$year_level}, NOW(), NOW())";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;

						//getting the latest_id found
						$latest_id = $conn->insert_id;

						//insert prerequisites to database
						foreach($prerequisites as $item){
							echo "Adding ID[" . $latest_id . "] & Item[" . $item . "]";
							$this->AddPrerequisite($latest_id, $item);
						}

						//insert corequisites to database
						foreach($corequisites as $item){
							echo "Adding ID[" . $latest_id . "] & Item[" . $item . "]";
							$this->AddCorequisite($latest_id, $item);
							var_dump($this->error);
						}
//						exit();
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding subject to curriculum. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Edit Curriculum Subject in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditSubject(
			$subject_id,
			$curriculum,
			$subject,
			$semester, //int
			$year_level //int
		){

			//reset array
			$this->array = array();

			//clean input
			if($subject <= 0){
				$this->error[sizeof($this->error)] = "Subject: Invalid Selection.";
			}

			if($curriculum <= 0){
				$this->error[sizeof($this->error)] = "Curriculum: Invalid Selection.";
			}
			if($semester <= 0){
				$this->error[sizeof($this->error)] = "Semester: Invalid Selection.";
			}

			if($year_level <= 0){
				$this->error[sizeof($this->error)] = "Year Level: Invalid Selection.";
			}

			$result = false;

			if(sizeof($this->error) == 0){

				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `sch-curriculum_subjects` ";
					$query .= "SET ";
					$query .= "Curriculum={$curriculum}, Subject={$subject}, Semester={$semester}, ";
					$query .= "YearLevel={$year_level}, Modified=NOW() ";
					$query .= "WHERE CurriculumSubjectID=";
					$query .= $subject_id;

					$conn->query($query);
					echo $conn->error;
					if($conn->affected_rows > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error editing Curriculum Subject. Duplicate found!";
						}  else {
							$this->error[sizeof($this->error)] = $conn->error;
						}

					}
				}
			}

			return $result;

		}

		//Delete Curriculum Subject in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteSubject($subject_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$this->DeletePrerequisiteBySubject($subject_id);
				$this->DeleteCorequisiteBySubject($subject_id);

				$query = "DELETE FROM `sch-curriculum_subjects` ";
				$query .= "WHERE CurriculumSubjectID=";
				$query .= $subject_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting Curriculum Subject. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Add Curriculum Subject Prerequisite to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddPrerequisite(
			$curriculum_subject_id,
			$prerequisite
		){


			//clean input

			if($curriculum_subject_id <= 0){
				$this->error[sizeof($this->error)] = "Curriculum Subject: Invalid Selection.";
			}

			if($prerequisite <= 0){
				$this->error[sizeof($this->error)] = "Pre-requisite: Invalid Selection.";
			}

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-prerequisites`(CurriculumSubject, Prerequisite, Created, Modified) ";
					$query .= "VALUES({$curriculum_subject_id}, {$prerequisite}, NOW(), NOW())";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding prerequisite of subject. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Delete Curriculum Subject Prerequisites from database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeletePrerequisiteBySubject(
			$curriculum_subject_id
		){

			$result = true;


			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-prerequisites` WHERE ";
				$query .= "CurriculumSubject={$curriculum_subject_id}";

				$conn->query($query);

				if($conn->affected_rows > 0){
					$result = true;
				} else {
					//$this->error[sizeof($this->error)] = $conn->error;
				}
			}


			return $result;

		}

		//Add Curriculum Subject Corequisite to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddCorequisite(
			$curriculum_subject_id,
			$corequisite
		){


			//clean input

			if($curriculum_subject_id <= 0){
				$this->error[sizeof($this->error)] = "Curriculum Subject: Invalid Selection.";
			}

			if($corequisite <= 0){
				$this->error[sizeof($this->error)] = "Co-requisite: Invalid Selection.";
			}

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `sch-corequisites`(CurriculumSubject, Corequisite, Created, Modified) ";
					$query .= "VALUES({$curriculum_subject_id}, {$corequisite}, NOW(), NOW())";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding corequisite of subject. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Delete Curriculum Subject Corequisites from database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteCorequisiteBySubject(
			$curriculum_subject_id
		){

			$result = true;


			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `sch-corequisites` WHERE ";
				$query .= "CurriculumSubject={$curriculum_subject_id}";

				$conn->query($query);

				if($conn->affected_rows > 0){
					$result = true;
				} else {
					//$this->error[sizeof($this->error)] = $conn->error;
				}
			}

			return $result;

		}

	}


?>
