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
		function GetStudentsFromSectionSubject($section_id){
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
				$query .= "ORDER BY spd.LastName ";

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
	}

?>
