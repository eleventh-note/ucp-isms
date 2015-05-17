<?php
	/*-----------------------------------------------

		GRADES
		-contain grades detail

		DEPENDENCIES:

	-------------------------------------------------*/


	class GradingPeriod{
		public $periodId;
		public $description;
		public $sequence;

		function __construct($periodId, $description, $sequence){
			$this->periodId = $periodId;
			$this->description = $description;
			$this->sequence = $sequence;
		}
	}


	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class GradesManager{

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

		/*============================================================
			SECTION: Grading Periods
		  ============================================================*/
		function getGradingPeriods(){

			$rows = array();

			$conn = $this->conn;

			$query = "SELECT periodId, description, sequence FROM `grd-grading_periods` ORDER BY sequence";

			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[$row['periodId']] = new GradingPeriod($row['periodId'], $row['description'], $row['sequence']);
				}
			}

			return $rows;
		}

		function activateAllByGrading($periodId, $deadline, $sy, $sem){

			$enl = new EnlistmentManager($this->conn);

			$periodId = (int) $periodId;
			$sy = (int) $sy;
			$sem = (int) $sem;
			$date = strtotime($deadline);
			$deadline = strtotime($deadline);
			$result = false;

			$conn = $this->conn;

			//### CHECK.ERRORS
			if($periodId == -1){
				$this->error[] = "Please select a period.";
			}
			if(!$deadline){
				$this->error[] = "Invalid date format. 'mm/dd/yyyy'.";
			}
			//check validity of deadline
			if($deadline <= time()){
				$this->error[] = "Invalid date, must be ahead of the date today.";
			}

			if(sizeof($this->error) == 0){

				$date = date("Y-m-d", $date);
				//get all enlisted subjects
				$enlistedSubjects = $enl->GetEnlistedSubjects($sy, $sem);
				$successCount = 0;

				//adjust the timelimit
				set_time_limit(600);

				echo "Total of " . sizeof($enlistedSubjects) . " record(s).";
				$timeStart = time();
				foreach ($enlistedSubjects as $item){
					//### INSERT
					$query  = "INSERT INTO `grd-grades` ";
					$query .= "(enlistedSubject, created, modified, active, activeUntil, activeUntil2) ";
					$query .= "VALUES ";
					$query .= "({$item->enlistment_id}, NOW(), NOW(), 1, ";

					if($periodId==1){ //midterm
						$query .= "'{$date} 23:59:59', NOW()) ";
					} else { //finals
						$query .= "NOW(), '{$date} 23:59:59') ";
					}
					$conn->query($query);

					if($conn->insert_id > 0){
						$successCount++;
					} else {
						//### UPDATE
						$query  = "UPDATE `grd-grades` ";
						$query .= "SET modified=NOW(), active=1, ";
						if($periodId==1){ //midterm
							$query .= "activeUntil='{$date} 23:59:59' ";
						} else { //finals
							$query .= "activeUntil2='{$date} 23:59:59' ";
						}
						$query .= "WHERE enlistedSubject={$item->enlistment_id} ";

						$conn->query($query);

						if($conn->affected_rows > 0){
							$successCount++;
						} else {
							$this->error[0] = $conn->error;
						}
					}
				}

				set_time_limit(60);

				echo "<br/>Success: " . $successCount;
				echo "<br/>Time Elapsed: " . (time() - $timeStart);
				if($successCount == sizeof($enlistedSubjects)){
					$result = true;
				} else {
					$this->error[] = "Unable to activate all accounts!";
				}

			}

			return $result;
		}

		function getSubjectsForEncoding($employeeId){
			$rows = array();
			$conn = $this->conn;

			$query  = "SELECT DISTINCT ";
			$query .= "section, subjectCode , subjectDescription, activeUntil, activeUntil2, id ";
			//$query .= "* ";
			$query .= "FROM ";
			$query .= "( ";
			$query .= "SELECT enlistedSubject, activeUntil, activeUntil2, ss.Faculty, es.Name AS `section`, ";
			$query .= "s.Code AS `subjectCode`, s.Description AS `subjectDescription`, ";
			$query .= "CONCAT(se.LastName, ', ', se.FirstName, ' ', se.MiddleName) AS `facultyName`, ";
			$query .= "enl.SectionSubject AS `id` ";
			$query .= "FROM `grd-grades` gg ";
			$query .= "LEFT JOIN `enl-student_enlistment` enl ON enl.EnlistmentID=gg.enlistedSubject ";
			$query .= "LEFT JOIN `enl-subject_schedule` ss ON ss.SectionSubject=enl.SectionSubject ";
			$query .= "LEFT JOIN `sch-college_faculties` cf ON cf.FacultyID=ss.Faculty ";
			$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=cf.Employee ";
			$query .= "LEFT JOIN `sch-curriculum_subjects` cs ON cs.CurriculumSubjectID=enl.Subject ";
			$query .= "LEFT JOIN `sch-subjects` s ON s.SubjectID=cs.Subject ";
			$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.SectionSubjectID=ss.SectionSubject ";
			$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
			$query .= "WHERE cf.Employee={$employeeId} AND (DATEDIFF(activeUntil,NOW()) > -1 OR DATEDIFF(activeUntil2,NOW()) > -1)";
			$query .= ") AS `facultySubjects` ";
			$query .= "GROUP BY section, subjectCode ";
			$query .= "ORDER BY section ";

			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			} else {
				var_dump($conn->error);
			}

			return $rows;
		}

		function getStudentsForEncoding($sectionSubject, $employeeId){
			$rows = array();
			$conn = $this->conn;

			$query = "SELECT gradeId, midtermGrade, finalGrade, CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) AS `studentName`, ";
			$query .= "pd.StudentNo, ";
			$query .= "enlistedSubject AS `enlistedSubjectId`, activeUntil, activeUntil2, ss.Faculty, es.Name AS `section`, ";
			$query .= "s.Code AS `subjectCode`, s.Description AS `subjectDescription`, ";
			$query .= "CONCAT(se.LastName, ', ', se.FirstName, ' ', se.MiddleName) AS `facultyName`, ";
			$query .= "enl.SectionSubject AS `sectionSubjectId` ";
			$query .= "FROM `grd-grades` gg ";
			$query .= "LEFT JOIN `enl-student_enlistment` enl ON enl.EnlistmentID=gg.enlistedSubject ";
			$query .= "LEFT JOIN `enl-subject_schedule` ss ON ss.SectionSubject=enl.SectionSubject ";
			$query .= "LEFT JOIN `sch-college_faculties` cf ON cf.FacultyID=ss.Faculty ";
			$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=cf.Employee ";
			$query .= "LEFT JOIN `sch-curriculum_subjects` cs ON cs.CurriculumSubjectID=enl.Subject ";
			$query .= "LEFT JOIN `sch-subjects` s ON s.SubjectID=cs.Subject ";
			$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.SectionSubjectID=ss.SectionSubject ";
			$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
			$query .= "LEFT JOIN `spr-personal_data` pd ON pd.StudentID=enl.StudentID ";
			$query .= "WHERE cf.Employee={$employeeId} AND enl.SectionSubject={$sectionSubject} AND ";
			$query .= "(DATEDIFF(activeUntil,NOW()) > -1 or DATEDIFF(activeUntil2,NOW()) > -1) ";
			$query .= "GROUP BY gradeId ";
			$query .= "ORDER BY pd.LastName ";
			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			} else {
				var_dump($conn->error);
			}

			return $rows;
		}

		function getIncompleteStudents($sy, $sem){
			$rows = array();
			$conn = $this->conn;

			$query = "SELECT pd.StudentID, pd.StudentNo, es.Name AS `section`, CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) AS `studentName`, COUNT(*) AS `total` ";
			$query .= "FROM `grd-grades` gg ";
			$query .= "LEFT JOIN `enl-student_enlistment` enl ON enl.EnlistmentID=gg.enlistedSubject ";
			$query .= "LEFT JOIN `enl-subject_schedule` ss ON ss.SectionSubject=enl.SectionSubject ";
			$query .= "LEFT JOIN `sch-college_faculties` cf ON cf.FacultyID=ss.Faculty ";
			$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=cf.Employee ";
			$query .= "LEFT JOIN `sch-curriculum_subjects` cs ON cs.CurriculumSubjectID=enl.Subject ";
			$query .= "LEFT JOIN `sch-subjects` s ON s.SubjectID=cs.Subject ";
			$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.SectionSubjectID=ss.SectionSubject ";
			$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
			$query .= "LEFT JOIN `spr-personal_data` pd ON pd.StudentID=enl.StudentID ";
			$query .= "WHERE enl.SY={$sy} AND enl.Semester={$sem} AND (midtermGrade='INC' OR midtermGrade IS NULL OR finalGrade='INC' OR finalGrade IS NULL) ";
			$query .= "GROUP BY pd.StudentID, pd.StudentNo, CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) ";
			$query .= "ORDER BY TRIM(pd.LastName) ";

			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			} else {
				var_dump($conn->error);
			}

			return $rows;
		}

		function getIncompleteStudentsBySection($sy, $sem){
			$rows = array();
			$conn = $this->conn;

			$query = "SELECT pd.StudentID, pd.StudentNo, es.Name AS `section`, CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) AS `studentName`, COUNT(*) AS `total` ";
			$query .= "FROM `grd-grades` gg ";
			$query .= "LEFT JOIN `enl-student_enlistment` enl ON enl.EnlistmentID=gg.enlistedSubject ";
			$query .= "LEFT JOIN `enl-subject_schedule` ss ON ss.SectionSubject=enl.SectionSubject ";
			$query .= "LEFT JOIN `sch-college_faculties` cf ON cf.FacultyID=ss.Faculty ";
			$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=cf.Employee ";
			$query .= "LEFT JOIN `sch-curriculum_subjects` cs ON cs.CurriculumSubjectID=enl.Subject ";
			$query .= "LEFT JOIN `sch-subjects` s ON s.SubjectID=cs.Subject ";
			$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.SectionSubjectID=ss.SectionSubject ";
			$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
			$query .= "LEFT JOIN `spr-personal_data` pd ON pd.StudentID=enl.StudentID ";
			$query .= "WHERE enl.SY={$sy} AND enl.Semester={$sem} AND (midtermGrade='INC' OR midtermGrade IS NULL OR finalGrade='INC' OR finalGrade IS NULL) ";
			$query .= "GROUP BY pd.StudentID, pd.StudentNo, CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) ";
			$query .= "ORDER BY es.Name ";

			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			} else {
				var_dump($conn->error);
			}

			return $rows;
		}

		function getIncompleteStudentsByStudentNo($sy, $sem){
			$rows = array();
			$conn = $this->conn;

			$query = "SELECT pd.StudentID, pd.StudentNo, es.Name AS `section`, CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) AS `studentName`, COUNT(*) AS `total` ";
			$query .= "FROM `grd-grades` gg ";
			$query .= "LEFT JOIN `enl-student_enlistment` enl ON enl.EnlistmentID=gg.enlistedSubject ";
			$query .= "LEFT JOIN `enl-subject_schedule` ss ON ss.SectionSubject=enl.SectionSubject ";
			$query .= "LEFT JOIN `sch-college_faculties` cf ON cf.FacultyID=ss.Faculty ";
			$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=cf.Employee ";
			$query .= "LEFT JOIN `sch-curriculum_subjects` cs ON cs.CurriculumSubjectID=enl.Subject ";
			$query .= "LEFT JOIN `sch-subjects` s ON s.SubjectID=cs.Subject ";
			$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.SectionSubjectID=ss.SectionSubject ";
			$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
			$query .= "LEFT JOIN `spr-personal_data` pd ON pd.StudentID=enl.StudentID ";
			$query .= "WHERE enl.SY={$sy} AND enl.Semester={$sem} AND (midtermGrade='INC' OR midtermGrade IS NULL OR finalGrade='INC' OR finalGrade IS NULL) ";
			$query .= "GROUP BY pd.StudentID, pd.StudentNo, CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) ";
			$query .= "ORDER BY pd.StudentNo ";

			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			} else {
				var_dump($conn->error);
			}

			return $rows;
		}

		function getStudentGrades($studentId, $sy, $sem){
			$rows = array();
			$conn = $this->conn;

			$query = "SELECT gradeId, midtermGrade, finalGrade, CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) AS `studentName`, ";
			$query .= "pd.StudentNo, ";
			$query .= "enlistedSubject AS `enlistedSubjectId`, activeUntil, ss.Faculty, es.Name AS `section`, ";
			$query .= "s.Code AS `subjectCode`, s.Description AS `subjectDescription`, ";
			$query .= "CONCAT(se.LastName, ', ', se.FirstName, ' ', se.MiddleName) AS `facultyName`, ";
			$query .= "enl.SectionSubject AS `sectionSubjectId` ";
			$query .= "FROM `grd-grades` gg ";
			$query .= "LEFT JOIN `enl-student_enlistment` enl ON enl.EnlistmentID=gg.enlistedSubject ";
			$query .= "LEFT JOIN `enl-subject_schedule` ss ON ss.SectionSubject=enl.SectionSubject ";
			$query .= "LEFT JOIN `sch-college_faculties` cf ON cf.FacultyID=ss.Faculty ";
			$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=cf.Employee ";
			$query .= "LEFT JOIN `sch-curriculum_subjects` cs ON cs.CurriculumSubjectID=enl.Subject ";
			$query .= "LEFT JOIN `sch-subjects` s ON s.SubjectID=cs.Subject ";
			$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.SectionSubjectID=ss.SectionSubject ";
			$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
			$query .= "LEFT JOIN `spr-personal_data` pd ON pd.StudentID=enl.StudentID ";
			$query .= "WHERE pd.StudentID={$studentId} AND enl.SY={$sy} AND enl.Semester={$sem} ";
			$query .= "GROUP BY gradeId ";
			$query .= "ORDER BY s.Code ";
			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			} else {
				var_dump($conn->error);
			}

			return $rows;
		}

		function getSchoolYearsWithGrades($studentId){
			$rows = array();
			$conn = $this->conn;

			$query = "SELECT DISTINCT CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) AS `studentName`, ";
			$query .= "pd.StudentNo, ";
			//$query .= "enlistedSubject AS `enlistedSubjectId`, ss.Faculty, es.Name AS `section`, ";
			//$query .= "s.Code AS `subjectCode`, s.Description AS `subjectDescription`, ";
			//$query .= "CONCAT(se.LastName, ', ', se.FirstName, ' ', se.MiddleName) AS `facultyName`, ";
			//$query .= "enl.SectionSubject AS `sectionSubjectId`, ";
			$query .= "sem.SemesterId, sem.Description AS `semester`, ";
			$query .= "sy.SchoolYearID, CONCAT('SY ', sy.Start, '-', sy.End) AS schoolYear ";
			$query .= "FROM `grd-grades` gg ";
			$query .= "LEFT JOIN `enl-student_enlistment` enl ON enl.EnlistmentID=gg.enlistedSubject ";
			$query .= "LEFT JOIN `enl-subject_schedule` ss ON ss.SectionSubject=enl.SectionSubject ";
			$query .= "LEFT JOIN `sch-college_faculties` cf ON cf.FacultyID=ss.Faculty ";
			$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=cf.Employee ";
			$query .= "LEFT JOIN `sch-curriculum_subjects` cs ON cs.CurriculumSubjectID=enl.Subject ";
			$query .= "LEFT JOIN `sch-subjects` s ON s.SubjectID=cs.Subject ";
			$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.SectionSubjectID=ss.SectionSubject ";
			$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
			$query .= "LEFT JOIN `spr-personal_data` pd ON pd.StudentID=enl.StudentID ";
			$query .= "LEFT JOIN `sch-school_years` sy ON sy.SchoolYearId=enl.SY ";
			$query .= "LEFT JOIN `sch-semesters` sem ON sem.SemesterId=enl.Semester ";
			$query .= "WHERE enl.StudentID={$studentId} ";
			$query .= "GROUP BY gradeId ";
			$query .= "ORDER BY schoolYear, semester, s.Code ";
			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			} else {
				var_dump($conn->error);
			}

			return $rows;
		}

		function getSemesterWithGrades($studentId){
			$rows = array();
			$conn = $this->conn;

			$query = "SELECT DISTINCT CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) AS `studentName`, ";
			$query .= "pd.StudentNo, ";
			//$query .= "enlistedSubject AS `enlistedSubjectId`, ss.Faculty, es.Name AS `section`, ";
			//$query .= "s.Code AS `subjectCode`, s.Description AS `subjectDescription`, ";
			//$query .= "CONCAT(se.LastName, ', ', se.FirstName, ' ', se.MiddleName) AS `facultyName`, ";
			//$query .= "enl.SectionSubject AS `sectionSubjectId`, ";
			$query .= "sem.SemesterId, sem.Description AS `semester`, ";
			$query .= "sy.SchoolYearID, CONCAT('SY ', sy.Start, '-', sy.End) AS schoolYear ";
			$query .= "FROM `grd-grades` gg ";
			$query .= "LEFT JOIN `enl-student_enlistment` enl ON enl.EnlistmentID=gg.enlistedSubject ";
			$query .= "LEFT JOIN `enl-subject_schedule` ss ON ss.SectionSubject=enl.SectionSubject ";
			$query .= "LEFT JOIN `sch-college_faculties` cf ON cf.FacultyID=ss.Faculty ";
			$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=cf.Employee ";
			$query .= "LEFT JOIN `sch-curriculum_subjects` cs ON cs.CurriculumSubjectID=enl.Subject ";
			$query .= "LEFT JOIN `sch-subjects` s ON s.SubjectID=cs.Subject ";
			$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.SectionSubjectID=ss.SectionSubject ";
			$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
			$query .= "LEFT JOIN `spr-personal_data` pd ON pd.StudentID=enl.StudentID ";
			$query .= "LEFT JOIN `sch-school_years` sy ON sy.SchoolYearId=enl.SY ";
			$query .= "LEFT JOIN `sch-semesters` sem ON sem.SemesterId=enl.Semester ";
			$query .= "WHERE enl.StudentID={$studentId} ";
			$query .= "GROUP BY gradeId ";
			$query .= "ORDER BY schoolYear, semester, s.Code ";
			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			} else {
				var_dump($conn->error);
			}

			return $rows;
		}

		function getStudentSubjectsWithGrades($studentId){
			$rows = array();
			$conn = $this->conn;

			$query = "SELECT DISTINCT CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) AS `studentName`, ";
			$query .= "pd.StudentNo, ";
			$query .= "enlistedSubject AS `enlistedSubjectId`, ss.Faculty, es.Name AS `section`, ";
			$query .= "s.Code AS `subjectCode`, s.Description AS `subjectDescription`, ";
			$query .= "CONCAT(se.LastName, ', ', se.FirstName, ' ', se.MiddleName) AS `facultyName`, ";
			$query .= "enl.SectionSubject AS `sectionSubjectId`, sem.Description AS `semester`, ";
			$query .= "sy.SchoolYearID, CONCAT('SY ', sy.Start, '-', sy.End) AS schoolYear ";
			$query .= "FROM `grd-grades` gg ";
			$query .= "LEFT JOIN `enl-student_enlistment` enl ON enl.EnlistmentID=gg.enlistedSubject ";
			$query .= "LEFT JOIN `enl-subject_schedule` ss ON ss.SectionSubject=enl.SectionSubject ";
			$query .= "LEFT JOIN `sch-college_faculties` cf ON cf.FacultyID=ss.Faculty ";
			$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=cf.Employee ";
			$query .= "LEFT JOIN `sch-curriculum_subjects` cs ON cs.CurriculumSubjectID=enl.Subject ";
			$query .= "LEFT JOIN `sch-subjects` s ON s.SubjectID=cs.Subject ";
			$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.SectionSubjectID=ss.SectionSubject ";
			$query .= "LEFT JOIN `enl-sections` es ON es.SectionID=ess.Section ";
			$query .= "LEFT JOIN `spr-personal_data` pd ON pd.StudentID=enl.StudentID ";
			$query .= "LEFT JOIN `sch-school_years` sy ON sy.SchoolYearId=enl.SY ";
			$query .= "LEFT JOIN `sch-semesters` sem ON sem.SemesterId=enl.Semester ";
			$query .= "WHERE pd.StudentID={$studentId} ";
			$query .= "GROUP BY gradeId ";
			$query .= "ORDER BY schoolYear, semester, s.Code ";
			$result = $conn->query($query);

			if($result){
				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}
			} else {
				var_dump($conn->error);
			}

			return $rows;
		}

		function saveMidtermGrade($gradeId, $value){
			$conn = $this->conn;

			$query = "UPDATE `grd-grades` SET midtermGrade='{$value}' WHERE gradeId={$gradeId}";
			$result = $conn->query($query);
		}

		function saveFinalGrade($gradeId, $value){
			$conn = $this->conn;

			$query = "UPDATE `grd-grades` SET finalGrade='{$value}' WHERE gradeId={$gradeId}";
			$result = $conn->query($query);
		}
	}


?>
