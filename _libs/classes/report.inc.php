<?php
	/*-----------------------------------------------

		REPORTS
		---------

	-------------------------------------------------*/

	class Report {
		public $error = array();
		private $admission_id;
		private $conn = null;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}

		// Get List of Active Subjects on current Semester and School Year
		function GetActiveSubjects(){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT ";
				$query .= "`SectionSubjectID`, scs.CurriculumSubjectID, ss.SubjectID, es.SectionID, es.SY, es.Semester, es.Name AS SectionName, ";
				$query .= "ss.Code AS SubjectCode, ss.Description AS SubjectDescription, ss.Units, ss.UnitsLab ";
				$query .= "FROM `enl-section_subjects` ess ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON ess.Subject = scs.CurriculumSubjectID ";
				$query .= "LEFT JOIN `sch-subjects` ss ON scs.Subject = ss.SubjectID ";
				$query .= "LEFT JOIN `enl-sections` es ON ess.Section = es.SectionID ";
				$query .= "WHERE ";
				$query .= "1 AND es.SY = (SELECT SchoolYearID FROM `sch-school_years` WHERE Active=1) ";
				$query .= "AND es.Semester = (SELECT SemesterID FROM `sch-semesters` WHERE Active=1) ";
				$query .= "ORDER BY ss.Code ";

				$result = $conn->query($query);

				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$records[] = $row;
						}
					}
				}
			}

			return $records;
		} // End of GetActiveSubjects()

    // Get List of Students based on a selected Section Subjects on current Semester and School Year
		function GetStudentsFromSectionSubject($section_id, $sort = 1){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT ";
				$query .= "`SectionSubjectID`, scs.CurriculumSubjectID, ss.SubjectID, es.SectionID, ese.StudentID, es.SY, es.Semester, ";
				$query .= "es.Name AS SectionName, ss.Code AS SubjectCode, ss.Description AS SubjectDescription, ss.Units, ss.UnitsLab, ";
        $query .= "spd.FirstName, spd.LastName, spd.MiddleName, scl.Code AS CourseCode, scl.Description AS CourseDescription, spd.StudentNo ";
				$query .= "FROM `enl-section_subjects` ess ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON ess.Subject = scs.CurriculumSubjectID ";
				$query .= "LEFT JOIN `sch-subjects` ss ON scs.Subject = ss.SubjectID ";
				$query .= "LEFT JOIN `enl-sections` es ON ess.Section = es.SectionID ";
        $query .= "LEFT JOIN `enl-student_enlistment` ese ON ess.SectionSubjectID = ese.SectionSubject ";
        $query .= "LEFT JOIN `spr-personal_data` spd ON ese.StudentID = spd.StudentID ";
        $query .= "LEFT JOIN `spr-current_academic_background` scab ON spd.StudentID=scab.StudentID ";
        $query .= "LEFT JOIN `sch-course_list` scl ON scab.Course=scl.CourseID ";
				$query .= "WHERE ";
				$query .= "1 AND es.SY = (SELECT SchoolYearID FROM `sch-school_years` WHERE Active=1) ";
				$query .= "AND es.Semester = (SELECT SemesterID FROM `sch-semesters` WHERE Active=1) ";
        $query .= "AND SectionSubject={$section_id} ";

        switch($sort) {
        	case 1:
        		$query .= "ORDER BY spd.StudentNo ";
        		break;
        	case 2:
        		$query .= "ORDER BY spd.LastName, spd.FirstName ";
        		break;
        }

				$result = $conn->query($query);

				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$records[] = $row;
						}
					}
				}
			}

			return $records;
		} // End of GetStudentsFromSectionSubject($section_id)

		function GetStudentCountPerSubject($sort = 1, $sem = null, $sy = null) {
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT ";
				$query .= "ss.Code AS SubjectCode, ss.Description AS SubjectDescription, ";
				$query .= "es.Name AS Section, COUNT(*) AS NoStudents ";
				$query .= "FROM `enl-section_subjects` ess ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID = ess.Subject ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID = scs.Subject ";
				$query .= "LEFT JOIN `enl-sections` es ON es.SectionID = ess.Section ";
        $query .= "LEFT JOIN `enl-student_enlistment` ese ON ese.SectionSubject = ess.SectionSubjectID ";
        $query .= "WHERE 1=1 ";

        if ($sy != null) {
        	$query .= "AND es.SY = " . $sy . " ";
        }

				if ($sem != null) {
        	$query .= "AND es.Semester = " . $sem . " ";
        }

        $query .= "GROUP BY SubjectCode, SubjectDescription, Section ";
        $query .= "HAVING SubjectCode IS NOT NULL AND SubjectDescription IS NOT NULL ";

        switch($sort) {
        	case 1:
        		$query .= "ORDER BY SubjectCode, Section ";
        		break;
        	case 2:
        		$query .= "ORDER BY SubjectDescription, Section ";
        		break;
        	case 3:
        		$query .= "ORDER BY Section, SubjectCode ";
        		break;
        }

				$result = $conn->query($query);

				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$records[] = $row;
						}
					}
				}
			}

			return $records;
		}
	}

?>
