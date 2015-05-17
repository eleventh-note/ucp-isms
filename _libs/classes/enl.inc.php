<?php
	/*-----------------------------------------------

		Enlistment
		-handle student enlistment

	-------------------------------------------------*/

	class EnlistmentSubject{
		public $enlistment_id;
		public $student_id;
		public $section_subject;
		public $curriculum_subject;
		public $sy;
		public $semester;
		public $enlistment_status;
		public $isHalf;

		function __construct(
			$enlistment_id, $student_id, $section_subject,
			$curriculum_subject, $sy, $semester, $enlistment_status
		){
			$this->enlistment_id = $enlistment_id;
			$this->student_id = $student_id;
			$this->section_subject = $section_subject;
			$this->curriculum_subject = $curriculum_subject;
			$this->sy = $sy;
			$this->semester = $semester;
			$this->enlistment_status = $enlistment_status;
			$this->isHalf = 0;

		}
	}

	class EnlistmentDetail{
		public $student_id;
		public $student_no;
		public $course;
		public $college;

		public $name;
		public $enrolled;
		public $payment_type;
		public $loading_status;
		public $enlistment_id;
		public $created;

		public $scholarship;
		public $discount;

		public $lecFee;
		public $labFee;
		public $otherFees;
		public $orNumber;

		function __construct($student_id, $student_no, $course, $college, $name, $enrolled, $payment_type, $loading_status,
												 $enlistment_id){
			$this->student_id = $student_id;
			$this->student_no = $student_no;
			$this->course = $course;
			$this->college = $college;
			$to_replace = array('&amp;Ntilde;','&Ntilde;','&amp;ntilde;','&ntilde;');
			$for_replace = array('Ñ','Ñ','ñ','ñ');
			$this->name = str_replace($to_replace, $for_replace, $name);
			$this->enrolled = $enrolled;
			$this->payment_type = $payment_type;
			$this->loading_status = $loading_status;
			$this->enlistment_id = $enlistment_id;
		}
	}

	class LoadingStatus{
		public $status_id;
		public $description;

		function __construct($status_id, $description){
			$this->status_id = $status_id;
			$this->description = $description;
		}
	}

	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class EnlistmentManager{

		public $error = array();
		private $conn = null;
		public $enlistment_id = null;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}

		/*--------------------------------------------------------

			LOADING STATUS [ SELECT ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetLoadingStatusesByKey(){
			$status = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `StatusID`, `Description` FROM `enl-loading_status` WHERE 1 ";
				$query .= "ORDER BY Description";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$status = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$status[$row['StatusID']] = new LoadingStatus($row['StatusID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $status;
		}


		/*--------------------------------------------------------

			STUDENT ENLISTMENT [ SELECT ]

		---------------------------------------------------------*/

		function GetCurrentEnlistmentID($student_id){
			$enlistment_id = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT EnlistmentID FROM `fin-billings` b ";
				$query .= "LEFT JOIN `enl-enlistment_details` enl ON enl.EnlistmentDetailID=b.EnlistmentID ";
				$query .= "LEFT JOIN `spr-personal_data` spr ON spr.StudentID=enl.StudentID ";
				$query .= "WHERE enl.SY=(SELECT SchoolYearID FROM `sch-school_years` WHERE Active=1) ";
				$query .= "AND enl.Semester=(SELECT SemesterID FROM `sch-semesters` WHERE Active=1) ";
				$query .= "AND spr.StudentID=$student_id ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$enlistment_id = $row['EnlistmentID'];
						}
					}
				}
			}

			return $enlistment_id;
		}

		//Will always return null for errors else an array
		function GetStudentEnlistmentSubjects($student_id, $sy, $sem){
			$subjects = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `EnlistmentID`, `StudentID`, `SectionSubject`, ese.`Subject`, ese.`SY`, ";
				$query .= "ese.`Semester`, `DateCreated`, ese.`Modified`, `EnlistmentStatus`, `IsHalfFee` ";
				$query .= "FROM `enl-student_enlistment` ese ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=ese.Subject ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=scs.Subject ";
				$query .= "WHERE 1 AND StudentID={$student_id} AND SY={$sy} AND ese.Semester={$sem} ";
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
							$subjects[$ctr] = new EnlistmentSubject($row['EnlistmentID'], $row['StudentID'],
								$row['SectionSubject'], $row['Subject'], $row['SY'], $row['Semester'], $row['EnlistmentStatus']
							);
							$subjects[$ctr]->isHalf = $row['IsHalfFee'];
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		//Will always return null for errors else an array
		//Will be used by Grading Manager to get all enlisted subjects
		function GetEnlistedSubjects($sy, $sem){
			$subjects = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `EnlistmentID`, `StudentID`, `SectionSubject`, ese.`Subject`, ese.`SY`, ese.`Semester`, `DateCreated`, ese.`Modified`, `EnlistmentStatus` ";
				$query .= "FROM `enl-student_enlistment` ese ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=ese.Subject ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectID=scs.Subject ";
				$query .= "WHERE 1 AND SY={$sy} AND ese.Semester={$sem} ";
				$query .= "ORDER BY ese.StudentID ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$subjects = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$subjects[$ctr] = new EnlistmentSubject($row['EnlistmentID'], $row['StudentID'],
								$row['SectionSubject'], $row['Subject'], $row['SY'], $row['Semester'], $row['EnlistmentStatus']
							);
							$ctr++;
						}
					}
				}
			}

			return $subjects;
		}

		function GetEnrollmentDate($student_id){
			$date = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT DateCreated FROM `enl-enlistment_details` WHERE StudentID={$student_id} ORDER BY DateCreated DESC LIMIT 0,1";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$details = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$date  = $row['DateCreated'];

						}
					}
				}
			}
			return $date;
		}

		//Will always return null for errors else an array
		function GetStudentEnlistmentDetails($sy, $sem, $student_id = null){
			$details = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT ";
				$query .= "enl.EnlistmentDetailID, enl.StudentID, bg.StudentNo, CONCAT('[',cou.Code,'] ', cou.Description) AS Course, enl.Scholarship, enl.Discount,";
				$query .= "CONCAT(data.LastName, ', ', data.FirstName, ' ', data.MiddleName) AS `Name`, ";
				$query .= "CONCAT('[', coll.Code,'] ', coll.Description) AS College, enl.Enrolled, enl.PaymentType, enl.LoadingStatus, enl.LecFee, enl.LabFee, enl.OtherFees, enl.OrNumber ";
				$query .= "FROM `enl-enlistment_details` enl ";
				$query .= "LEFT JOIN `spr-personal_data` data ON data.StudentID=enl.StudentID ";
				$query .= "LEFT JOIN `spr-current_academic_background` bg ON bg.StudentNo=data.StudentNo ";
				$query .= "LEFT JOIN `sch-course_list` cou ON cou.CourseID=bg.Course ";
				$query .= "LEFT JOIN `sch-colleges` coll ON coll.CollegeID = cou.College ";
				$query .= "WHERE Semester={$sem} AND SY={$sy} AND bg.CurrentAcademicBackgroundID=(SELECT CurrentAcademicBackgroundID FROM `spr-current_academic_background` bg2 WHERE bg2.StudentNo=bg.StudentNo ORDER BY CurrentAcademicBackgroundID DESC LIMIT 0,1) ";
				if($student_id != null){
					$query .= "AND enl.StudentID={$student_id} ";
				}
				$query .= " ORDER BY enl.DateCreated ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$details = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$details[$ctr] = new EnlistmentDetail($row['StudentID'], $row['StudentNo'],
								$row['Course'], $row['College'], $row['Name'], $row['Enrolled'],
								$row['PaymentType'], $row['LoadingStatus'], $row['EnlistmentDetailID']
							);
							$details[$ctr]->scholarship = $row['Scholarship'];
							$details[$ctr]->discount = $row['Discount'];
							$details[$ctr]->lecFee = $row['LecFee'];
							$details[$ctr]->labFee = $row['LabFee'];
							$details[$ctr]->otherFees = $row['OtherFees'];
							$details[$ctr]->orNumber = $row['OrNumber'];
							$ctr++;
						}
					}
				}
			}

			return $details;
		}

		//Will always return null for errors else an array
		function GetStudentEnlistmentDetailsSort($sy, $sem, $sort_type){
			$details = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT ";
				$query .= "enl.EnlistmentDetailID, enl.StudentID, bg.StudentNo, CONCAT('[',cou.Code,'] ', cou.Description) AS Course, enl.Scholarship, enl.Discount,";
				$query .= "CONCAT(data.LastName, ', ', data.FirstName, ' ', data.MiddleName) AS `Name`, ";
				$query .= "CONCAT('[', coll.Code,'] ', coll.Description) AS College, enl.Enrolled, enl.PaymentType, enl.LoadingStatus, enl.OrNumber ";
				$query .= "FROM `enl-enlistment_details` enl ";
				$query .= "LEFT JOIN `spr-personal_data` data ON data.StudentID=enl.StudentID ";
				$query .= "LEFT JOIN `spr-current_academic_background` bg ON bg.StudentNo=data.StudentNo ";
				$query .= "LEFT JOIN `sch-course_list` cou ON cou.CourseID=bg.Course ";
				$query .= "LEFT JOIN `sch-colleges` coll ON coll.CollegeID = cou.College ";
				$query .= "WHERE Semester={$sem} AND SY={$sy} AND bg.CurrentAcademicBackgroundID=(SELECT CurrentAcademicBackgroundID FROM `spr-current_academic_background` bg2 WHERE bg2.StudentNo=bg.StudentNo ORDER BY CurrentAcademicBackgroundID DESC LIMIT 0,1) ";

				switch($sort_type){
					case 'today': //get records of enlistment for the current date
						$query .= "AND DATE_FORMAT(DateCreated,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') ";
						$query .= " ORDER BY enl.DateCreated ";
					break;
					case 'alphabetical_sort': //get records of enlistment for the current date
						$query .= " ORDER BY data.LastName ";

					break;
					default:
						$query .= " ORDER BY enl.DateCreated ";
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$details = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$details[$ctr] = new EnlistmentDetail($row['StudentID'], $row['StudentNo'],
								$row['Course'], $row['College'], $row['Name'], $row['Enrolled'],
								$row['PaymentType'], $row['LoadingStatus'], $row['EnlistmentDetailID']
							);
							$details[$ctr]->scholarship = $row['Scholarship'];
							$details[$ctr]->discount = $row['Discount'];
							$details[$ctr]->lecFee = $row['LecFee'];
							$details[$ctr]->labFee = $row['LabFee'];
							$ctr++;
						}
					}
				}
			}

			return $details;
		}

		function EnlistStudent($student_id, $sy, $semester, $payment_type, $loading_status, $scholarship, $discount, $lecFee, $labFee, $other_fees, $orNumber){

			$result = false;

			$payment_type = (int) $payment_type;
			$loading_status = (int) $loading_status;

			if($payment_type <= 0){
				$this->error[] = 'Payment Mode must be selected.';
			}

			if($loading_status <= 0){
				$this->error[] = 'Loading Status must be selected.';
			}

			if(sizeof($this->error) > 0){
			}elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `enl-enlistment_details`(`StudentID`, `Semester`, `SY`, `PaymentType`, `LoadingStatus`, `DateCreated`, `DateModified`, LecFee, LabFee, OtherFees, OrNumber ";
				if($scholarship > 0){
					$query .= ",Scholarship";
				}
				if($discount > 0){
					$query .= ",Discount";
				}
				$query .= ") ";
				$query .= "VALUES ({$student_id}, {$semester}, {$sy}, {$payment_type}, {$loading_status}, NOW(), NOW(), {$lecFee}, {$labFee}, '{$other_fees}', '{$orNumber}' ";
				if($scholarship > 0){
					$query .= ",{$scholarship}";
				}
				if($discount > 0){
					$query .= ",{$discount}";
				}
				$query .= ")";

				$conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding student enlistment. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				} else {
					if($conn->insert_id > 0){
						$this->enlistment_id = $conn->insert_id;
						$result = true;
					}
				}
			}

			return $result;
		}

		function UpdateEnlistedStudent($student_id, $sy, $semester, $payment_type, $loading_status,$scholarship, $discount, $lecFee, $labFee, $other_fees, $orNumber){

			$result = false;

			$payment_type = (int) $payment_type;
			$loading_status = (int) $loading_status;

			if($payment_type <= 0){
				$this->error[] = 'Payment Mode must be selected.';
			}

			if($loading_status <= 0){
				$this->error[] = 'Loading Status must be selected.';
			}

			if(sizeof($this->error) > 0){
			}elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `enl-enlistment_details` SET `PaymentType`={$payment_type}, `LoadingStatus`={$loading_status},`DateModified`=NOW(), LecFee={$lecFee}, LabFee={$labFee}, OtherFees='{$other_fees}', OrNumber='{$orNumber}' ";
				if($scholarship > 0){ $query .= ", Scholarship={$scholarship} "; }
				if($discount > 0){ $query .= ", Discount={$discount} "; }

				$query .= "WHERE Semester={$semester} AND SY={$sy} AND StudentID={$student_id}";

				$conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error updating student enlistment record. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				} else {
					if($conn->affected_rows > 0){
						$result = true;
					}
				}
			}

			return $result;
		}


		function EnlistSubject($student_id, $section_subject, $curriculum_subject, $sy, $semester, $enlistment_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `enl-student_enlistment`(`StudentID`, `SectionSubject`, `Subject`, `DateCreated`, `Modified`, `EnlistmentStatus`, `SY`, `Semester`, `EnlistmentDetail`) ";
				$query .= "VALUES ({$student_id},{$section_subject},{$curriculum_subject},NOW(),NOW(),1,{$sy}, {$semester}, {$enlistment_id})";

				$conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding enlistment. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				} else {
					if($conn->insert_id > 0){
						$result = true;
					}
				}
			}

			return $result;
		}

		//Will always return null for errors else an array
		function RemoveEnlistment($student_id, $sem, $sy){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `enl-student_enlistment` WHERE StudentID={$student_id} AND Semester={$sem} AND SY={$sy} ";

				$conn->query($query);

				//check for errors first
				if($conn->error <> ""){
						$this->error[] = $conn->error;
				} else {
					if($conn->affected_rows > 0){
						$result = true;
					}
				}
			}

			return $result;
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

		//Will always return null for errors else an array
		function GetSectionStudents($section_id = null){
			$students = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query  = "SELECT DISTINCT ";
				$query .= "es.SectionId, es.Name AS Section, scl.Code AS CourseCode, scl.Description AS CourseDescription, CONCAT(spd.LastName, ', ', spd.FirstName, ' ', spd.MiddleName) AS StudentName ";
				$query .= "FROM `enl-sections` es ";
				$query .= "LEFT JOIN `enl-section_subjects` ess ON ess.Section = es.SectionId ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectId = ess.Subject ";
				$query .= "LEFT JOIN `sch-subjects` ss ON ss.SubjectId = scs.Subject ";
				$query .= "LEFT JOIN `enl-student_enlistment` ese On ese.SectionSubject = ess.SectionSubjectId ";
				$query .= "LEFT JOIN `spr-personal_data` spd ON spd.StudentId = ese.StudentId ";
				$query .= "LEFT JOIN `sch-curriculum` sc ON sc.CurriculumId = es.Curriculum ";
				$query .= "LEFT JOIN `sch-course_list` scl ON scl.CourseId = sc.Course ";
				$query .= "WHERE es.SectionId = {$section_id} ";
				$query .= "ORDER BY spd.LastName, spd.FirstName ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$students = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$students[] = $row;
						}
					}
				}
			}

			return $students;
		}
	}
?>
