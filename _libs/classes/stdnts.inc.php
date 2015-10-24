<?php
	/*-----------------------------------------------

		STUDENTS
		---------

	-------------------------------------------------*/

	class PreAdmissionRecord{
		public $preadmission_id;
		public $last_name;
		public $first_name;
		public $middle_name;
		public $birthday;
		public $place_of_birth;
		public $gender;
		public $marital_status;
		public $mailing_address;
		public $email;
		public $telephone_number;
		public $mobile_number;
		public $last_school;
		public $application_type;
		public $school_year;
		public $semester;
		public $application_number;
		public $first_choice;
		public $second_choice;
		public $third_choice;
		public $application_status;
		public $course_passed;
		public $spr_created;
		public $created;

		function __construct(
			$preadmission_id, $last_name, $first_name, $middle_name, $birthday, $place_of_birth, $gender, $marital_status,
			$mailing_address, $email, $telephone_number, $mobile_number, $last_school, $application_type, $school_year,
			$semester, $application_number, $first_choice, $second_choice, $third_choice, $application_status, $course_passed,
			$spr_created
		){
			$this->preadmission_id = $preadmission_id;

			$to_replace = array('&amp;Ntilde;','&Ntilde;','&amp;ntilde;','&ntilde;');
			$for_replace = array('Ñ','Ñ','ñ','ñ');

			$this->last_name = str_replace($to_replace, $for_replace, $last_name);
			$this->first_name = str_replace($to_replace, $for_replace, $first_name);
			$this->middle_name = str_replace($to_replace, $for_replace, $middle_name);
			$this->birthday = $birthday;
			$this->place_of_birth = str_replace($to_replace, $for_replace, $place_of_birth);
			$this->gender = $gender;
			$this->marital_status = $marital_status;
			$this->mailing_address = str_replace($to_replace, $for_replace, $mailing_address);
			$this->email = $email;
			$this->telephone_number = $telephone_number;
			$this->mobile_number = $mobile_number;
			$this->last_school = str_replace($to_replace, $for_replace, $last_school);
			$this->application_type = $application_type;
			$this->school_year = $school_year;
			$this->semester = $semester;
			$this->application_number = $application_number;
			$this->first_choice = $first_choice;
			$this->second_choice = $second_choice;
			$this->third_choice = $third_choice;
			$this->application_status = $application_status;
			$this->course_passed = $course_passed;
			$this->spr_created = $spr_created;
		}

	}

	class Spr{
		public $student_id;
		public $student_no;
		public $first_name;
		public $last_name;
		public $middle_name;
		public $gender;
		public $birthday;
		public $place_of_birth;
		public $mailing_address;
		public $email;
		public $telephone_number;
		public $mobile_number;
		public $marital_status;
		public $religion;
		public $citizenship;
		public $region;
		public $country;
		public $acr;
		public $city_address;
		public $provincial_address;
		public $created;
		function __construct(
			$student_id, $student_no, $first_name, $last_name,
			$middle_name, $gender, $birthday, $place_of_birth,
			$mailing_address, $email, $telephone_number,
			$mobile_number, $marital_status, $religion,
			$citizenship, $region, $country, $acr,
			$city_address, $provincial_address
		){
			$this->student_id = $student_id;
			$this->student_no = $student_no;
			$to_replace = array('&amp;Ntilde;','&Ntilde;','&amp;ntilde;','&ntilde;');
			$for_replace = array('Ñ','Ñ','ñ','ñ');
			$this->first_name = str_replace($to_replace, $for_replace,$first_name);
			$this->last_name = str_replace($to_replace, $for_replace,$last_name);
			$this->middle_name = str_replace($to_replace, $for_replace,$middle_name);
			$this->gender = $gender;
			$this->birthday = $birthday;
			$this->place_of_birth = $place_of_birth;
			$this->mailing_address = $mailing_address;
			$this->email = $email;
			$this->telephone_number = $telephone_number;
			$this->mobile_number = $mobile_number;
			$this->marital_status = $marital_status;
			$this->religion = str_replace($to_replace, $for_replace,$religion);
			$this->citizenship = str_replace($to_replace, $for_replace,$citizenship);
			$this->region = str_replace($to_replace, $for_replace,$region);
			$this->country = str_replace($to_replace, $for_replace,$country);
			$this->acr = $acr;
			$this->city_address = str_replace($to_replace, $for_replace,$city_address);
			$this->provincial_address = str_replace($to_replace, $for_replace,$provincial_address);
		}
	}

	class AcademicBackground{
		public $current_academic_background_id;
		public $student_no;
		public $course;
		public $student_type;
		public $student_status;
		public $enrollment_status;
		public $entry_semester;
		public $entry_sy;
		public $year_of_graduation;
		public $application_type;

		function __construct(
			$current_academic_background_id, $student_no, $course, $student_type,
			$student_status, $enrollment_status, $entry_semester, $entry_sy,
			$year_of_graduation, $application_type
		){
			$this->current_academic_background_id = $current_academic_background_id;
			$this->student_no = $student_no;
			$this->course = $course;
			$this->student_type = $student_type;
			$this->student_status = $student_status;
			$this->enrollment_status = $enrollment_status;
			$this->entry_semester = $entry_semester;
			$this->entry_sy = $entry_sy;
			$this->year_of_graduation = $year_of_graduation;
			$this->application_type = $application_type;
		}
	}

	//Excluded, Regular, Suspended, Irregular, etc..
	class StudentStatus{
		public $status_id;
		public $code;
		public $description;

		function __construct($status_id, $code, $description){
			$this->status_id = $status_id;
			$this->code = $code;
			$this->description = $description;
		}
	}

	class EnrollmentStatus{
		public $status_id;
		public $description;

		function __construct($status_id, $description){
			$this->status_id = $status_id;
			$this->description = $description;
		}
	}

	class ApplicationType {
		public $type_id;
		public $code;
		public $description;

		function __construct($type_id, $code, $description){
			$this->type_id = $type_id;
			$this->code = $code;
			$this->description = $description;
		}
	}

	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above;
	class StudentManager{
		public $error = array();
		private $admission_id;
		private $conn = null;
		public $student_id;
		public $student_no;
		public $application_no;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}

		/*--------------------------------------------------------

			ADMISSION RECORDS [SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetLatestApplicationNumber(){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT ApplicationNo FROM `ads-preadm_records` WHERE EntrySY=(SELECT SchoolYearID FROM `sch-school_years` WHERE Active=1) ";
				$query .= "ORDER BY Created DESC LIMIT 0,1";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$records = $row['ApplicationNo'];
						}
					}
				}
			}

			return $records;
		}

		function GetNextApplicationNumber(){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT (SELECT Start FROM `sch-school_years` WHERE Active=1) AS `Start`, ApplicationNo ";
				$query .= "FROM `ads-preadm_records` a ";
				$query .= "LEFT JOIN `sch-school_years` sy on a.EntrySY=sy.SchoolYearID ";
				$query .= "WHERE sy.Active=1 ORDER BY ApplicationNo DESC LIMIT 0,1";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					if($result->num_rows > 0){
						$row = $result->fetch_assoc();

						$tmp = explode("-",$row['ApplicationNo']);
						$last_number = (int) $tmp[1];
						$records = $row['Start'] . '-' . str_pad(($last_number+1) ,5,'0', STR_PAD_LEFT);

						$row = $result->fetch_assoc();
					} else {
						$query = "SELECT Start FROM `sch-school_years` WHERE Active=1";
						$result = $conn->query($query);
						$row = $result->fetch_assoc();
						$records = $row['Start'] . "-" . "00001";
					}
				}
			}

			return $records;
		}

		//Will always return null for errors else an array
		function GetPreAdmissionRecords(
			$admission_id = null,
			$keyword = null,
			$page = null,
			$item_count = null
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SprCreated, apr.Created, `PreadmissionID`, `LastName`, `FirstName`, `MiddleName`, `Birthday`, ";
				$query .= "`PlaceOfBirth`, gg.Description AS `Gender`, gms.Description AS `MaritalStatus`, `MailingAddress`, `EmailAddress`, ";
				$query .= "`TelephoneNumber`, `MobileNumber`, `LastSchoolAttended`, aat.Description AS `ApplicationType`, ";
				$query .= " CONCAT('SY ', ssy.start, ' - ', ssy.end) AS `EntrySY`, ss.Description AS `EntrySemester`, `ApplicationNo`,CONCAT('[',scl1.Code,'] ', scl1.Description) AS `FirstChoice`, CONCAT('[',scl2.Code,'] ', scl2.Description) AS `SecondChoice`, ";
				$query .= "CONCAT('[',scl3.Code,'] ', scl3.Description) AS `ThirdChoice`, aas.Description AS `ApplicationStatus`, CONCAT('[',scl.Code,'] ', scl.Description) AS `CoursePassed` ";
				$query .= "FROM `ads-preadm_records` apr ";
				$query .= "LEFT JOIN `ads-application_status` aas ON apr.ApplicationStatus=aas.StatusID ";
				$query .= "LEFT JOIN `ads-application_type` aat ON apr.ApplicationType=aat.TypeID ";
				$query .= "LEFT JOIN `sch-course_list` scl ON apr.CoursePassed=scl.CourseID ";
				$query .= "LEFT JOIN `sch-course_list` scl1 ON apr.FirstChoice=scl1.CourseID ";
				$query .= "LEFT JOIN `sch-course_list` scl2 ON apr.SecondChoice=scl2.CourseID ";
				$query .= "LEFT JOIN `sch-course_list` scl3 ON apr.ThirdChoice=scl3.CourseID ";
				$query .= "LEFT JOIN `sch-school_years` ssy ON apr.EntrySY=ssy.SchoolYearID ";
				$query .= "LEFT JOIN `sch-semesters` ss ON apr.EntrySemester=ss.SemesterID ";
				$query .= "LEFT JOIN `gen-genders` gg ON apr.Gender=gg.GenderID ";
				$query .= "LEFT JOIN `gen-marital_status` gms ON apr.MaritalStatus=gms.StatusID ";
				$query .= "WHERE 1 AND ssy.Active=1 AND ss.Active=1 ";

				if($admission_id != null){
					$query .= "AND PreadmissionID={$admission_id} ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				$query .= "ORDER BY LastName ";

				if($page > 0 && $item_count > 0){
					 $limit = $item_count;
					 $start = ($page-1) * $item_count;
					 $query .= "LIMIT {$start}, {$limit} ";
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

							$records[sizeof($records)] = new PreAdmissionRecord(
								$row['PreadmissionID'], $row['LastName'],
								$row['FirstName'], $row['MiddleName'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['Gender'], $row['MaritalStatus'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['LastSchoolAttended'], $row['ApplicationType'],
								$row['EntrySY'], $row['EntrySemester'],
								$row['ApplicationNo'], $row['FirstChoice'],
								$row['SecondChoice'], $row['ThirdChoice'],
								$row['ApplicationStatus'], $row['CoursePassed'],
								$row['SprCreated']
							);
							$records[sizeof($records)-1]->created = $row['Created'];
						}
					}
				}
			}

			return $records;
		}

		//Will always return null for errors else an array
		function GetPreAdmissionRecordsByApplicationNoDesc(
			$admission_id = null,
			$keyword = null,
			$page = null,
			$item_count = null
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SprCreated, apr.Created, `PreadmissionID`, `LastName`, `FirstName`, `MiddleName`, `Birthday`, ";
				$query .= "`PlaceOfBirth`, gg.Description AS `Gender`, gms.Description AS `MaritalStatus`, `MailingAddress`, `EmailAddress`, ";
				$query .= "`TelephoneNumber`, `MobileNumber`, `LastSchoolAttended`, aat.Description AS `ApplicationType`, ";
				$query .= " CONCAT('SY ', ssy.start, ' - ', ssy.end) AS `EntrySY`, ss.Description AS `EntrySemester`, `ApplicationNo`,CONCAT('[',scl1.Code,'] ', scl1.Description) AS `FirstChoice`, CONCAT('[',scl2.Code,'] ', scl2.Description) AS `SecondChoice`, ";
				$query .= "CONCAT('[',scl3.Code,'] ', scl3.Description) AS `ThirdChoice`, aas.Description AS `ApplicationStatus`, CONCAT('[',scl.Code,'] ', scl.Description) AS `CoursePassed` ";
				$query .= "FROM `ads-preadm_records` apr ";
				$query .= "LEFT JOIN `ads-application_status` aas ON apr.ApplicationStatus=aas.StatusID ";
				$query .= "LEFT JOIN `ads-application_type` aat ON apr.ApplicationType=aat.TypeID ";
				$query .= "LEFT JOIN `sch-course_list` scl ON apr.CoursePassed=scl.CourseID ";
				$query .= "LEFT JOIN `sch-course_list` scl1 ON apr.FirstChoice=scl1.CourseID ";
				$query .= "LEFT JOIN `sch-course_list` scl2 ON apr.SecondChoice=scl2.CourseID ";
				$query .= "LEFT JOIN `sch-course_list` scl3 ON apr.ThirdChoice=scl3.CourseID ";
				$query .= "LEFT JOIN `sch-school_years` ssy ON apr.EntrySY=ssy.SchoolYearID ";
				$query .= "LEFT JOIN `sch-semesters` ss ON apr.EntrySemester=ss.SemesterID ";
				$query .= "LEFT JOIN `gen-genders` gg ON apr.Gender=gg.GenderID ";
				$query .= "LEFT JOIN `gen-marital_status` gms ON apr.MaritalStatus=gms.StatusID ";
				$query .= "WHERE 1 AND ssy.Active=1 AND ss.Active=1 ";

				if($admission_id != null){
					$query .= "AND PreadmissionID={$admission_id} ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				$query .= "ORDER BY apr.Created DESC";

				if($page > 0 && $item_count > 0){
					 $limit = $item_count;
					 $start = ($page-1) * $item_count;
					 $query .= "LIMIT {$start}, {$limit} ";
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[sizeof($records)] = new PreAdmissionRecord(
								$row['PreadmissionID'], $row['LastName'],
								$row['FirstName'], $row['MiddleName'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['Gender'], $row['MaritalStatus'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['LastSchoolAttended'], $row['ApplicationType'],
								$row['EntrySY'], $row['EntrySemester'],
								$row['ApplicationNo'], $row['FirstChoice'],
								$row['SecondChoice'], $row['ThirdChoice'],
								$row['ApplicationStatus'], $row['CoursePassed'],
								$row['SprCreated']
							);
							$records[sizeof($records)-1]->created = $row['Created'];
						}
					}
				}
			}

			return $records;
		}

		//Will always return null for errors else an array
		function GetPreAdmissionRecordsSort(
			$sort=0
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SprCreated, apr.Created, `PreadmissionID`, `LastName`, `FirstName`, `MiddleName`, `Birthday`, ";
				$query .= "`PlaceOfBirth`, gg.Description AS `Gender`, gms.Description AS `MaritalStatus`, `MailingAddress`, `EmailAddress`, ";
				$query .= "`TelephoneNumber`, `MobileNumber`, `LastSchoolAttended`, aat.Description AS `ApplicationType`, ";
				$query .= " CONCAT('SY ', ssy.start, ' - ', ssy.end) AS `EntrySY`, ss.Description AS `EntrySemester`, `ApplicationNo`,CONCAT('[',scl1.Code,'] ', scl1.Description) AS `FirstChoice`, CONCAT('[',scl2.Code,'] ', scl2.Description) AS `SecondChoice`, ";
				$query .= "CONCAT('[',scl3.Code,'] ', scl3.Description) AS `ThirdChoice`, aas.Description AS `ApplicationStatus`, CONCAT('[',scl.Code,'] ', scl.Description) AS `CoursePassed` ";
				$query .= "FROM `ads-preadm_records` apr ";
				$query .= "LEFT JOIN `ads-application_status` aas ON apr.ApplicationStatus=aas.StatusID ";
				$query .= "LEFT JOIN `ads-application_type` aat ON apr.ApplicationType=aat.TypeID ";
				$query .= "LEFT JOIN `sch-course_list` scl ON apr.CoursePassed=scl.CourseID ";
				$query .= "LEFT JOIN `sch-course_list` scl1 ON apr.FirstChoice=scl1.CourseID ";
				$query .= "LEFT JOIN `sch-course_list` scl2 ON apr.SecondChoice=scl2.CourseID ";
				$query .= "LEFT JOIN `sch-course_list` scl3 ON apr.ThirdChoice=scl3.CourseID ";
				$query .= "LEFT JOIN `sch-school_years` ssy ON apr.EntrySY=ssy.SchoolYearID ";
				$query .= "LEFT JOIN `sch-semesters` ss ON apr.EntrySemester=ss.SemesterID ";
				$query .= "LEFT JOIN `gen-genders` gg ON apr.Gender=gg.GenderID ";
				$query .= "LEFT JOIN `gen-marital_status` gms ON apr.MaritalStatus=gms.StatusID ";
				$query .= "WHERE 1 AND ssy.Active=1 AND ss.Active=1 ";

				//choose sorting order
				switch($sort){
					case 1:
						$query .= "ORDER BY apr.ApplicationNo ";
						break;
					case 2:
						$query .= "ORDER BY apr.LastName ";
						break;
					case 3:
						$query .= "ORDER BY apr.Created DESC";
						break;
					case 4:
						$query .= "ORDER BY apr.SprCreated ";
						break;
					default:
						$query .= "ORDER BY apr.ApplicationNo DESC";
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[sizeof($records)] = new PreAdmissionRecord(
								$row['PreadmissionID'], $row['LastName'],
								$row['FirstName'], $row['MiddleName'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['Gender'], $row['MaritalStatus'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['LastSchoolAttended'], $row['ApplicationType'],
								$row['EntrySY'], $row['EntrySemester'],
								$row['ApplicationNo'], $row['FirstChoice'],
								$row['SecondChoice'], $row['ThirdChoice'],
								$row['ApplicationStatus'], $row['CoursePassed'],
								$row['SprCreated']
							);
							$records[sizeof($records)-1]->created = $row['Created'];
						}
					}
				}
			}

			return $records;
		}

		//Will always return null for errors else an array
		function GetPreAdmissionRecordForEdit(
			$admission_id
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SprCreated, `PreadmissionID`, `LastName`, `FirstName`, `MiddleName`, `Birthday`, ";
				$query .= "`PlaceOfBirth`, `Gender`, `MaritalStatus`, `MailingAddress`, `EmailAddress`, ";
				$query .= "`TelephoneNumber`, `MobileNumber`, `LastSchoolAttended`, `ApplicationType`, ";
				$query .= " `EntrySY`,  `EntrySemester`, `ApplicationNo`, `FirstChoice`,  `SecondChoice`, ";
				$query .= "`ThirdChoice`, `ApplicationStatus`, `CoursePassed` ";
				$query .= "FROM `ads-preadm_records` apr ";
				$query .= "WHERE PreadmissionID={$admission_id} ";

				$result = $conn->query($query);

				//echo $conn->error;
				//exit();
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[sizeof($records)] = new PreAdmissionRecord(
								$row['PreadmissionID'], $row['LastName'],
								$row['FirstName'], $row['MiddleName'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['Gender'], $row['MaritalStatus'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['LastSchoolAttended'], $row['ApplicationType'],
								$row['EntrySY'], $row['EntrySemester'],
								$row['ApplicationNo'], $row['FirstChoice'],
								$row['SecondChoice'], $row['ThirdChoice'],
								$row['ApplicationStatus'], $row['CoursePassed'],
								$row['SprCreated']
							);

						}
					}
				}
			}

			return $records;
		}

		//Add Admission to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddAdmission(
				$last_name,
				$first_name,
				$middle_name,
				$birthday, //null
				$gender, //int CONSTRAINT
				$marital_status, //int CONSTRAINT
				$mailing_address, //varchar
				$place_of_birth, //varchar
				$telephone_number, //null
				$mobile_number, //null
				$email, //null
				$last_school,  //null
				$school_year, //int CONSTRAINT
				$semester, //int CONSTRAINT
				$first_choice, //int CONSTRAINT
				$second_choice, //int CONSTRAINT
				$third_choice, //int CONSTRAINT
				$course_passed, //int CONSTRAINT
				$application_type, //int CONSTRAINT
				$application_status, //int CONSTRAINT
				$application_number //int CONSTRAINT
			){

			$this->error = array();
			$this->error_count = 0;

			//clean input
			$last_name = htmlentities(ucwords(addslashes(strip_tags($last_name))));
			$first_name = htmlentities(ucwords(addslashes(strip_tags($first_name))));
			$middle_name = htmlentities(ucwords(addslashes(strip_tags($middle_name))));

			$birthday = addslashes(strip_tags($birthday));
			$gender = (int) addslashes(strip_tags($gender));
			$marital_status = (int) addslashes(strip_tags($marital_status));
			$mailing_address = ucwords(addslashes(strip_tags($mailing_address)));
			$place_of_birth = ucwords(addslashes(strip_tags($place_of_birth)));
			$telephone_number = ucwords(addslashes(strip_tags($telephone_number)));
			$mobile_number = ucwords(addslashes(strip_tags($mobile_number)));
			$email = addslashes(strip_tags($email));
			$last_school = ucwords(addslashes(strip_tags($last_school)));

			$school_year = (int) addslashes(strip_tags($school_year));
			$semester = (int) addslashes(strip_tags($semester));
			$first_choice = (int) addslashes(strip_tags($first_choice));
			$second_choice = (int) addslashes(strip_tags($second_choice));
			$third_choice = (int) addslashes(strip_tags($third_choice));
			$course_passed = (int) addslashes(strip_tags($course_passed));
			$application_type = (int) addslashes(strip_tags($application_type));
			$application_status = (int) addslashes(strip_tags($application_status));
			$application_number = $this->GetNextApplicationNumber();

			$result = false;

			//create RegEx Hanlder
			$reg = new RtRegExp;


			// if($last_name <> ''){
				// if($reg->CheckName($last_name) <= 0 || $reg->CheckName($last_name) == false){
					// $this->error[$this->error_count] = "Last name is in invalid format.";
					// $this->error_count++;
				// }
			// } else {
				// $this->error[$this->error_count] = "Last name cannot be empty.";
				// $this->error_count++;
			// }
			// if($first_name <> ''){
				// if($reg->CheckName($first_name) <= 0 || $reg->CheckName($first_name) == false){
					// $this->error[$this->error_count] = "First name is in invalid format.";
					// $this->error_count++;
				// }
			// } else {
				// $this->error[$this->error_count] = "First name cannot be empty.";
				// $this->error_count++;
			// }
			// if($middle_name <> ''){
				// if($reg->CheckName($middle_name) <= 0 || $reg->CheckName($middle_name) == false){
					// $this->error[$this->error_count] = "Middle name is in invalid format.";
					// $this->error_count++;
				// }
			// }
			if($email <> ''){
				if($reg->CheckEmail($email) <= 0 || $reg->CheckEmail($email) == false){
					$this->error[$this->error_count] = "Email is in invalid format.";
					$this->error_count++;
				}
			}

			if($gender <= 0 || $gender == ''){
				$this->error[$this->error_count] = "Gender not found.";
				$this->error_count++;
			}
			if($marital_status <= 0 || $marital_status == ''){
				$this->error[$this->error_count] = "Marital Status not found.";
				$this->error_count++;
			}
			if($birthday == '1970-01-01'){
				$this->error[$this->error_count] = "Birthday not inputted.";
				$this->error_count++;
			}

			if($place_of_birth == ''){
				$this->error[sizeof($this->error)] = "Place of Birth cannot be blank.";
			}

			if($school_year < 0){
				$this->error[sizeof($this->error)] = "Entry School Year not found.";
			}

			if($semester < 0){
				$this->error[sizeof($this->error)] = "Entry Semester not found.";
			}

			if($first_choice < 0){
				$this->error[sizeof($this->error)] = "First Choice not found.";
			}

			if($second_choice < 0){
				$this->error[sizeof($this->error)] = "Second Choice not found.";
			}

			if($third_choice < 0){
				$this->error[sizeof($this->error)] = "Third Choice not found.";
			}

			if($course_passed < 0){
				$this->error[sizeof($this->error)] = "Course Passed not found.";
			}

			if($application_type < 0){
				$this->error[sizeof($this->error)] = "Application Type not found.";
			}

			if($application_status < 0){
				$this->error[sizeof($this->error)] = "Application Status not found.";
			}


			//check if error is found first
			//-->else, just show the result
			if(count($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;
					/*
					INSERT INTO `ads-preadm_records`(`PreaadmissionID`, `LastName`, `FirstName`, `MiddleName`, `Birthday`, `PlaceOfBirth`, `Gender`, `MaritalStatus`, `MailingAddress`, `EmailAddress`, `TelephoneNumber`, `MobileNumber`, `LastSchoolAttended`, `ApplicationType`, `EntrySY`, `EntrySemester`, `ApplicationNo`, `FirstChoice`, `SecondChoice`, `ThirdChoice`, `ApplicationStatus`, `CoursePassed`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8],[value-9],[value-10],[value-11],[value-12],[value-13],[value-14],[value-15],[value-16],[value-17],[value-18],[value-19],[value-20],[value-21],[value-22])
					*/
					$query = "INSERT INTO `ads-preadm_records`(`LastName`, `FirstName`, `MiddleName`, `Birthday`, `PlaceOfBirth`, `Gender`, `MaritalStatus`, `MailingAddress`, `EmailAddress`, `TelephoneNumber`, `MobileNumber`, `LastSchoolAttended`, `ApplicationType`, `EntrySY`, `EntrySemester`, `ApplicationNo`, `FirstChoice`, `SecondChoice`, `ThirdChoice`, `ApplicationStatus`, `CoursePassed`) ";
					$query .= "VALUES('{$last_name}', '{$first_name}', '{$middle_name}', ";
					$query .= "'{$birthday}', '{$place_of_birth}', ";
					$query .= "{$gender}, {$marital_status}, '{$mailing_address}', ";
					$query .= "'{$email}', '{$telephone_number}', '{$mobile_number}', ";
					$query .= "'{$last_school}', {$application_type}, {$school_year}, {$semester}, ";
					$query .= "'{$application_number}', {$first_choice}, {$second_choice}, {$third_choice}, ";
					$query .= "{$application_status}, {$course_passed})";

					$conn->query($query);

					if($conn->insert_id > 0){
						//# set the pre-admission id
						$this->admission_id = $conn->insert_id;
						$result = true;
						$this->application_no = $application_number;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$this->error_count] = "Error adding student pre-admission record. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		//Edit Admission Record in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditAdmission(
				$id,
				$last_name,
				$first_name,
				$middle_name,
				$birthday, //null
				$gender, //int CONSTRAINT
				$marital_status, //int CONSTRAINT
				$mailing_address, //varchar
				$place_of_birth, //varchar
				$telephone_number, //null
				$mobile_number, //null
				$email, //null
				$last_school,  //null
				$school_year, //int CONSTRAINT
				$semester, //int CONSTRAINT
				$first_choice, //int CONSTRAINT
				$second_choice, //int CONSTRAINT
				$third_choice, //int CONSTRAINT
				$course_passed, //int CONSTRAINT
				$application_type, //int CONSTRAINT
				$application_status, //int CONSTRAINT
				$application_number //int CONSTRAINT
			){

			$this->error = array();
			$this->error_count = 0;

			//clean input
			$last_name = htmlentities(ucwords(addslashes(strip_tags($last_name))));
			$first_name = htmlentities(ucwords(addslashes(strip_tags($first_name))));
			$middle_name = htmlentities(ucwords(addslashes(strip_tags($middle_name))));

			$birthday = addslashes(strip_tags($birthday));
			$gender = (int) addslashes(strip_tags($gender));
			$marital_status = (int) addslashes(strip_tags($marital_status));
			$mailing_address = ucwords(addslashes(strip_tags($mailing_address)));
			$place_of_birth = ucwords(addslashes(strip_tags($place_of_birth)));
			$telephone_number = ucwords(addslashes(strip_tags($telephone_number)));
			$mobile_number = ucwords(addslashes(strip_tags($mobile_number)));
			$email = addslashes(strip_tags($email));
			$last_school = ucwords(addslashes(strip_tags($last_school)));

			$school_year = (int) addslashes(strip_tags($school_year));
			$semester = (int) addslashes(strip_tags($semester));
			$first_choice = (int) addslashes(strip_tags($first_choice));
			$second_choice = (int) addslashes(strip_tags($second_choice));
			$third_choice = (int) addslashes(strip_tags($third_choice));
			$course_passed = (int) addslashes(strip_tags($course_passed));
			$application_type = (int) addslashes(strip_tags($application_type));
			$application_status = (int) addslashes(strip_tags($application_status));
			$application_number = addslashes(strip_tags($application_number));

			$result = false;

			//create RegEx Hanlder
			$reg = new RtRegExp;


			if($last_name <> ''){
				if($reg->CheckName($last_name) <= 0 || $reg->CheckName($last_name) == false){
					$this->error[$this->error_count] = "Last name is in invalid format.";
					$this->error_count++;
				}
			} else {
				$this->error[$this->error_count] = "Last name cannot be empty.";
				$this->error_count++;
			}
			if($first_name <> ''){
				if($reg->CheckName($first_name) <= 0 || $reg->CheckName($first_name) == false){
					$this->error[$this->error_count] = "First name is in invalid format.";
					$this->error_count++;
				}
			} else {
				$this->error[$this->error_count] = "First name cannot be empty.";
				$this->error_count++;
			}
			if($middle_name <> ''){
				if($reg->CheckName($middle_name) <= 0 || $reg->CheckName($middle_name) == false){
					$this->error[$this->error_count] = "Middle name is in invalid format.";
					$this->error_count++;
				}
			}
			if($email <> ''){
				if($reg->CheckEmail($email) <= 0 || $reg->CheckEmail($email) == false){
					$this->error[$this->error_count] = "Email is in invalid format.";
					$this->error_count++;
				}
			}

			if($gender <= 0 || $gender == ''){
				$this->error[$this->error_count] = "Gender not found.";
				$this->error_count++;
			}
			if($marital_status <= 0 || $marital_status == ''){
				$this->error[$this->error_count] = "Marital Status not found.";
				$this->error_count++;
			}
			if($birthday == '1970-01-01'){
				$this->error[$this->error_count] = "Birthday not inputted.";
				$this->error_count++;
			}

			if($place_of_birth == ''){
				$this->error[sizeof($this->error)] = "Place of Birth cannot be blank.";
			}

			if($school_year < 0){
				$this->error[sizeof($this->error)] = "Entry School Year not found.";
			}

			if($semester < 0){
				$this->error[sizeof($this->error)] = "Entry Semester not found.";
			}

			if($first_choice < 0){
				$this->error[sizeof($this->error)] = "First Choice not found.";
			}

			if($second_choice < 0){
				$this->error[sizeof($this->error)] = "Second Choice not found.";
			}

			if($third_choice < 0){
				$this->error[sizeof($this->error)] = "Third Choice not found.";
			}

			if($course_passed < 0){
				$this->error[sizeof($this->error)] = "Course Passed not found.";
			}

			if($application_type < 0){
				$this->error[sizeof($this->error)] = "Application Type not found.";
			}

			if($application_status < 0){
				$this->error[sizeof($this->error)] = "Application Status not found.";
			}


			//check if error is found first
			//-->else, just show the result
			if(count($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `ads-preadm_records` SET `LastName`='{$last_name}', ";
					$query .= "`FirstName`='{$first_name}', `MiddleName`='{$middle_name}', ";
					$query .= "`Birthday`='{$birthday}', `PlaceOfBirth`='{$place_of_birth}', ";
					$query .= "`Gender`='{$gender}', `MaritalStatus`='{$marital_status}', ";
					$query .= "`MailingAddress`='{$mailing_address}', ";
					$query .= "`EmailAddress`='{$email}', `TelephoneNumber`='{$telephone_number}', ";
					$query .= "`MobileNumber`='{$mobile_number}', ";
					$query .= "`LastSchoolAttended`='{$last_school}', ";
					$query .= "`ApplicationType`={$application_type}, ";
					$query .= "`EntrySY`={$school_year}, `EntrySemester`={$semester}, ";
					$query .= "`ApplicationNo`='{$application_number}', ";
					$query .= "`FirstChoice`={$first_choice}, `SecondChoice`={$second_choice}, ";
					$query .= "`ThirdChoice`={$third_choice}, ";
					$query .= "`ApplicationStatus`={$application_status}, ";
					$query .= "`CoursePassed`={$course_passed}, Modified=NOW() ";
					$query .= "WHERE PreadmissionID={$id} ";

					$conn->query($query);

					if($conn->affected_rows > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$this->error_count] = "Error saving student pre-admission record. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		//Toggle SPR in database
		//-->will set SprCreated to 1 which is true
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function ToggleAdmissionSPRCreated($id){

			$this->error = array();
			$this->error_count = 0;

			$result = false;

			//check if error is found first
			//-->else, just show the result
			if(count($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `ads-preadm_records` SET `SprCreated`=1, Modified=NOW() ";
					$query .= "WHERE PreadmissionID={$id} ";

					$conn->query($query);

					if($conn->affected_rows > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$this->error_count] = "Error saving student pre-admission record. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		function DeleteAdmission($admission_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `ads-preadm_records` ";
				$query .= "WHERE PreadmissionID=";
				$query .= $admission_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting student admission records. Information in use.";
					}	else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		/* ################################################################

			STUDENT PERSONAL RECORDS [SELECT | INSERT | UPDATE | DELETE]

		##################################################################*/

		function GetNextStudentNumber(){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT (SELECT Start FROM `sch-school_years` WHERE Active=1) AS `Start`, StudentNo  ";
				$query .= "FROM `spr-personal_data` a ";
				$query .= "LEFT JOIN `sch-school_years` sy on a.EntrySY=sy.SchoolYearID ";
				$query .= "WHERE sy.Active=1 ORDER BY StudentNo DESC LIMIT 0,1";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					if($result->num_rows > 0){
						$row = $result->fetch_assoc();
						$tmp = explode("-",$row['StudentNo']);
						$last_number = (int) $tmp[1];
						$records = $row['Start'] . '-' . str_pad(($last_number+1) ,5,'0', STR_PAD_LEFT);
					} else {
						$query = "SELECT Start FROM `sch-school_years` WHERE Active=1";
						$result = $conn->query($query);
						$row = $result->fetch_assoc();
						$records = $row['Start'] . "-" . "00001";
					}
				}
			}

			return $records;
		}

		//Will always return null for errors else an array
		function GetSprs(
			$student_id = null,
			$keyword = null,
			$page = null,
			$item_count = null
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT spr.`StudentID`, spr.`StudentNo`, `FirstName`, `LastName`, `MiddleName`, `Gender`, `Birthday`, `PlaceOfBirth`, `MailingAddress`, ";
				$query .= "`EmailAddress`, `TelephoneNumber`, `MobileNumber`, `MaritalStatus`, `Religion`, `Citizenship`, `Region`, `Country`, `ACR`, ";
				$query .= "`CityAddress`, `ProvincialAddress`, spr.Created FROM `spr-personal_data` spr ";
				$query .= "LEFT JOIN `spr-current_academic_background` sca ON sca.StudentId=spr.StudentId ";
				$query .= "LEFT JOIN `sch-school_years` sy ON sca.EntrySY=sy.SchoolYearID ";
				$query .= "LEFT JOIN `sch-semesters` sem ON sca.EntrySemester=sem.SemesterID ";
				$query .= "WHERE  sy.Start  <=  (SELECT Start FROM `sch-school_years` WHERE Active=1) ";
				//$query .= "WHERE 1 AND sy.Active=1 AND sem.Active=1 ";


				if($student_id != null){
					$query .= "AND spr.StudentID={$student_id} ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				if($student_id == null){
					$query .= "ORDER BY LastName ";
				}

				if($page > 0 && $item_count > 0){
					 $limit = $item_count;
					 $start = ($page-1) * $item_count;
					 $query .= "LIMIT {$start}, {$limit} ";
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[$row['StudentID']] = new Spr(
								$row['StudentID'], $row['StudentNo'],
								$row['FirstName'], $row['LastName'],
								$row['MiddleName'], $row['Gender'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['MaritalStatus'], $row['Religion'],
								$row['Citizenship'], $row['Region'],
								$row['Country'], $row['ACR'],
								$row['CityAddress'], $row['ProvincialAddress']
							);
							$records[$row['StudentID']]->created = $row['Created'];
						}
					}
				}
			}

			return $records;
		}

		//Will always return null for errors else an array
		function GetSprsBySY(
			$student_id = null,
			$keyword = null,
			$page = null,
			$item_count = null
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT spr.`StudentID`, spr.`StudentNo`, `FirstName`, `LastName`, `MiddleName`, `Gender`, `Birthday`, `PlaceOfBirth`, `MailingAddress`, ";
				$query .= "`EmailAddress`, `TelephoneNumber`, `MobileNumber`, `MaritalStatus`, `Religion`, `Citizenship`, `Region`, `Country`, `ACR`, ";
				$query .= "`CityAddress`, `ProvincialAddress`, spr.Created FROM `spr-personal_data` spr ";
				$query .= "LEFT JOIN `spr-current_academic_background` sca ON sca.StudentNo=spr.StudentNo ";
				$query .= "LEFT JOIN `sch-school_years` sy ON sca.EntrySY=sy.SchoolYearID ";
				$query .= "LEFT JOIN `sch-semesters` sem ON sca.EntrySemester=sem.SemesterID ";
				$query .= "WHERE 1 AND sy.Active=1 AND sem.Active=1 ";


				if($student_id != null){
					$query .= "AND spr.StudentID={$student_id} ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				if($student_id == null){
					$query .= "ORDER BY LastName ";
				}

				if($page > 0 && $item_count > 0){
					 $limit = $item_count;
					 $start = ($page-1) * $item_count;
					 $query .= "LIMIT {$start}, {$limit} ";
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[$row['StudentID']] = new Spr(
								$row['StudentID'], $row['StudentNo'],
								$row['FirstName'], $row['LastName'],
								$row['MiddleName'], $row['Gender'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['MaritalStatus'], $row['Religion'],
								$row['Citizenship'], $row['Region'],
								$row['Country'], $row['ACR'],
								$row['CityAddress'], $row['ProvincialAddress']
							);
							$records[$row['StudentID']]->created = $row['Created'];
						}
					}
				}
			}

			return $records;
		}

		//Will always return null for errors else an array
		function GetSprsSort(
			$sort=0
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT spr.`StudentID`, spr.`StudentNo`, `FirstName`, `LastName`, `MiddleName`, `Gender`, `Birthday`, `PlaceOfBirth`, `MailingAddress`, ";
				$query .= "`EmailAddress`, `TelephoneNumber`, `MobileNumber`, `MaritalStatus`, `Religion`, `Citizenship`, `Region`, `Country`, `ACR`, ";
				$query .= "`CityAddress`, `ProvincialAddress`, spr.Created FROM `spr-personal_data` spr ";
				$query .= "LEFT JOIN `spr-current_academic_background` sca ON sca.StudentId=spr.StudentId ";
				$query .= "LEFT JOIN `sch-school_years` sy ON sca.EntrySY=sy.SchoolYearID ";
				$query .= "LEFT JOIN `sch-semesters` sem ON sca.EntrySemester=sem.SemesterID ";
				$query .= "LEFT JOIN `sch-course_list` cl ON cl.CourseID=sca.Course ";
				$query .= "WHERE  sy.Start  <=  (SELECT Start FROM `sch-school_years` WHERE Active=1) ";
				//$query .= "WHERE 1 AND sy.Active=1 AND sem.Active=1 ";

				//choose sorting order
				switch($sort){
					case 1:
						$query .= "ORDER BY spr.StudentNo ";
						break;
					case 2:
						$query .= "ORDER BY LastName ";
						break;
					case 3:
						$query .= "ORDER BY cl.Code DESC";
						break;
					default:
						$query .= "ORDER BY spr.Created DESC ";
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[$row['StudentID']] = new Spr(
								$row['StudentID'], $row['StudentNo'],
								$row['FirstName'], $row['LastName'],
								$row['MiddleName'], $row['Gender'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['MaritalStatus'], $row['Religion'],
								$row['Citizenship'], $row['Region'],
								$row['Country'], $row['ACR'],
								$row['CityAddress'], $row['ProvincialAddress']
							);
							$records[$row['StudentID']]->created = $row['Created'];
						}
					}
				}
			}

			return $records;
		}

		//Will always return null for errors else an array
		function GetSprById($studentId){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query  = "SELECT DISTINCT CONCAT(pd.LastName, ', ', pd.FirstName, ' ', pd.MiddleName) AS `studentName`, ";
				$query .= "pd.StudentNo ";
				$query .= "FROM `spr-personal_data` pd ";
				$query .= "WHERE pd.StudentID={$studentId} ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[] = array();
							$records[count($records) - 1]['studentName'] = $row['studentName'];
							$records[count($records) - 1]['StudentNo'] = $row['StudentNo'];

						}
					}
				}
			}

			return $records;
		}

		//Will always return null for errors else an array
		function GetSprsSortBySY(
			$sort=0
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT spr.`StudentID`, spr.`StudentNo`, `FirstName`, `LastName`, `MiddleName`, `Gender`, `Birthday`, `PlaceOfBirth`, `MailingAddress`, ";
				$query .= "`EmailAddress`, `TelephoneNumber`, `MobileNumber`, `MaritalStatus`, `Religion`, `Citizenship`, `Region`, `Country`, `ACR`, ";
				$query .= "`CityAddress`, `ProvincialAddress`, spr.Created FROM `spr-personal_data` spr ";
				$query .= "LEFT JOIN `spr-current_academic_background` sca ON sca.StudentNo=spr.StudentNo ";
				$query .= "LEFT JOIN `sch-school_years` sy ON sca.EntrySY=sy.SchoolYearID ";
				$query .= "LEFT JOIN `sch-semesters` sem ON sca.EntrySemester=sem.SemesterID ";
				$query .= "LEFT JOIN `sch-course_list` cl ON cl.CourseID=sca.Course ";
				$query .= "WHERE 1 AND sy.Active=1 AND sem.Active=1 ";

				//choose sorting order
				switch($sort){
					case 1:
						$query .= "ORDER BY spr.StudentNo ";
						break;
					case 2:
						$query .= "ORDER BY LastName ";
						break;
					case 3:
						$query .= "ORDER BY cl.Code DESC";
						break;
					default:
						$query .= "ORDER BY spr.Created DESC ";
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[$row['StudentID']] = new Spr(
								$row['StudentID'], $row['StudentNo'],
								$row['FirstName'], $row['LastName'],
								$row['MiddleName'], $row['Gender'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['MaritalStatus'], $row['Religion'],
								$row['Citizenship'], $row['Region'],
								$row['Country'], $row['ACR'],
								$row['CityAddress'], $row['ProvincialAddress']
							);
							$records[$row['StudentID']]->created = $row['Created'];
						}
					}
				}
			}

			return $records;
		}

		function SearchSprs(
			$student_number = null, $keyword = null
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `StudentID`, `StudentNo`, `FirstName`, `LastName`, `MiddleName`, `Gender`, `Birthday`, `PlaceOfBirth`, `MailingAddress`, ";
				$query .= "`EmailAddress`, `TelephoneNumber`, `MobileNumber`, `MaritalStatus`, `Religion`, `Citizenship`, `Region`, `Country`, `ACR`, ";
				$query .= "`CityAddress`, `ProvincialAddress` FROM `spr-personal_data` spr ";
				$query .= "LEFT JOIN `sch-school_years` sy ON sy.SchoolYearID=spr.EntrySY ";
				$query .= "WHERE 1 ";
				$query .= "AND sy.Start  <=  (SELECT Start FROM `sch-school_years` WHERE Active=1) ";

				if($student_number != null){
					$query .= "AND StudentNo LIKE '%{$student_number}%' ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				$query .= "ORDER BY LastName ";

				$result = $conn->query($query);

				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[$row['StudentID']] = new Spr(
								$row['StudentID'], $row['StudentNo'],
								$row['FirstName'], $row['LastName'],
								$row['MiddleName'], $row['Gender'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['MaritalStatus'], $row['Religion'],
								$row['Citizenship'], $row['Region'],
								$row['Country'], $row['ACR'],
								$row['CityAddress'], $row['ProvincialAddress']
							);

						}
					}
				}
			}

			return $records;
		}

		function SearchSprsAll(
			$student_number = null, $keyword = null
		){
			$records = null;

			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `StudentID`, `StudentNo`, `FirstName`, `LastName`, `MiddleName`, `Gender`, `Birthday`, `PlaceOfBirth`, `MailingAddress`, ";
				$query .= "`EmailAddress`, `TelephoneNumber`, `MobileNumber`, `MaritalStatus`, `Religion`, `Citizenship`, `Region`, `Country`, `ACR`, ";
				$query .= "`CityAddress`, `ProvincialAddress` FROM `spr-personal_data` spr ";
				$query .= "LEFT JOIN `sch-school_years` sy ON sy.SchoolYearID=spr.EntrySY ";
				$query .= "WHERE 1 ";

				// Checks whether the student have existing subjects


				if($student_number != null){
					$query .= "AND StudentNo LIKE '%{$student_number}%' ";
				}

				if($keyword != null){
					$query .= "AND (";
					$query .= "LastName LIKE '%{$keyword}%' ";
					$query .= "OR FirstName LIKE '%{$keyword}%' ";
					$query .= "OR MiddleName LIKE '%{$keyword}%' ";
					$query .= ")";
				}

				$query .= "ORDER BY LastName ";

				$result = $conn->query($query);

				echo $conn->error;
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$records = array();
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){

							$records[$row['StudentID']] = new Spr(
								$row['StudentID'], $row['StudentNo'],
								$row['FirstName'], $row['LastName'],
								$row['MiddleName'], $row['Gender'],
								$row['Birthday'], $row['PlaceOfBirth'],
								$row['MailingAddress'], $row['EmailAddress'],
								$row['TelephoneNumber'], $row['MobileNumber'],
								$row['MaritalStatus'], $row['Religion'],
								$row['Citizenship'], $row['Region'],
								$row['Country'], $row['ACR'],
								$row['CityAddress'], $row['ProvincialAddress']
							);

						}
					}
				}
			}

			return $records;
		}

		//Add SPR to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddSPR(
				$last_name,
				$first_name,
				$middle_name,
				$birthday, //null
				$gender, //int CONSTRAINT
				$marital_status, //int CONSTRAINT
				$mailing_address, //varchar
				$place_of_birth, //varchar
				$telephone_number, //null
				$mobile_number, //null
				$email, //null
				$entry_sy,
				$entry_semester,
				$religion=null,
				$citizenship=null,
				$region=null,
				$country=null,
				$acr=null,
				$city_address=null,
				$provincial_address =null
			){

			$this->error = array();
			$this->error_count = 0;

			//clean input
			$last_name = htmlentities(ucwords(addslashes(strip_tags($last_name))));
			$first_name = htmlentities(ucwords(addslashes(strip_tags($first_name))));
			$middle_name = htmlentities(ucwords(addslashes(strip_tags($middle_name))));

			$birthday = addslashes(strip_tags($birthday));
			$gender = (int) addslashes(strip_tags($gender));
			$marital_status = (int) addslashes(strip_tags($marital_status));
			$mailing_address = ucwords(addslashes(strip_tags($mailing_address)));
			$place_of_birth = ucwords(addslashes(strip_tags($place_of_birth)));
			$telephone_number = ucwords(addslashes(strip_tags($telephone_number)));
			$mobile_number = ucwords(addslashes(strip_tags($mobile_number)));
			$email = addslashes(strip_tags($email));


			$result = false;

			//create RegEx Hanlder
			$reg = new RtRegExp;

			// if($last_name <> ''){
				// if($reg->CheckName($last_name) <= 0 || $reg->CheckName($last_name) == false){
					// $this->error[$this->error_count] = "Last name is in invalid format.";
					// $this->error_count++;
				// }
			// } else {
				// $this->error[$this->error_count] = "Last name cannot be empty.";
				// $this->error_count++;
			// }
			// if($first_name <> ''){
				// if($reg->CheckName($first_name) <= 0 || $reg->CheckName($first_name) == false){
					// $this->error[$this->error_count] = "First name is in invalid format.";
					// $this->error_count++;
				// }
			// } else {
				// $this->error[$this->error_count] = "First name cannot be empty.";
				// $this->error_count++;
			// }
			// if($middle_name <> ''){
				// if($reg->CheckName($middle_name) <= 0 || $reg->CheckName($middle_name) == false){
					// $this->error[$this->error_count] = "Middle name is in invalid format.";
					// $this->error_count++;
				// }
			// }
			if($email <> ''){
				if($reg->CheckEmail($email) <= 0 || $reg->CheckEmail($email) == false){
					$this->error[$this->error_count] = "Email is in invalid format.";
					$this->error_count++;
				}
			}

			if($gender <= 0 || $gender == ''){
				$this->error[$this->error_count] = "Gender not found.";
				$this->error_count++;
			}
			if($marital_status <= 0 || $marital_status == ''){
				$this->error[$this->error_count] = "Marital Status not found.";
				$this->error_count++;
			}
			if($birthday == '1970-01-01'){
				$this->error[$this->error_count] = "Birthday not inputted.";
				$this->error_count++;
			}

			if($place_of_birth == ''){
				$this->error[sizeof($this->error)] = "Place of Birth cannot be blank.";
			}

			//check if error is found first
			//-->else, just show the result
			if(count($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;
					/*
					INSERT INTO `ads-preadm_records`(`PreaadmissionID`, `LastName`, `FirstName`, `MiddleName`, `Birthday`, `PlaceOfBirth`, `Gender`, `MaritalStatus`, `MailingAddress`, `EmailAddress`, `TelephoneNumber`, `MobileNumber`, `LastSchoolAttended`, `ApplicationType`, `EntrySY`, `EntrySemester`, `ApplicationNo`, `FirstChoice`, `SecondChoice`, `ThirdChoice`, `ApplicationStatus`, `CoursePassed`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8],[value-9],[value-10],[value-11],[value-12],[value-13],[value-14],[value-15],[value-16],[value-17],[value-18],[value-19],[value-20],[value-21],[value-22])
					*/

					$next_student_number = $this->GetNextStudentNumber();

					if($religion != null){
						$query = "INSERT INTO `spr-personal_data`(`LastName`, `FirstName`, `MiddleName`, `Birthday`, `PlaceOfBirth`, `Gender`, `MaritalStatus`, `MailingAddress`, `EmailAddress`, `TelephoneNumber`, `MobileNumber` ";
						$query .= "Religion, Citizenship, Region, Country, ACR, CityAddress, ProvincialAddress, StudentNo, EntrySY, EntrySemester) ";
						$query .= "VALUES('{$last_name}', '{$first_name}', '{$middle_name}', ";
						$query .= "'{$birthday}', '{$place_of_birth}', ";
						$query .= "{$gender}, {$marital_status}, '{$mailing_address}', ";
						$query .= "'{$email}', '{$telephone_number}', '{$mobile_number}', ";
						$query .= "{$religion}, {$citizenship}, {$region}, {$country}, '{$acr}', '{$city_address}', '{$provincial_address}','";
						$query .= $next_student_number;
						$query .= "',{$entry_sy}, {$entry_semester}) ";
					} else {
						$query = "INSERT INTO `spr-personal_data`(`LastName`, `FirstName`, `MiddleName`, `Birthday`, `PlaceOfBirth`, `Gender`, `MaritalStatus`, `MailingAddress`, `EmailAddress`, `TelephoneNumber`, `MobileNumber`, StudentNo, EntrySY, EntrySemester) ";
						$query .= "VALUES('{$last_name}', '{$first_name}', '{$middle_name}', ";
						$query .= "'{$birthday}', '{$place_of_birth}', ";
						$query .= "{$gender}, {$marital_status}, '{$mailing_address}', ";
						$query .= "'{$email}', '{$telephone_number}', '{$mobile_number}','";
						$query .= $next_student_number;
						$query .= "',{$entry_sy}, {$entry_semester}) ";
					}

					$conn->query($query);

					if($conn->insert_id > 0){
						//# set the pre-admission id
						$this->student_id = $conn->insert_id;
						$this->student_no = $next_student_number;
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$this->error_count] = "Error creating student personal record. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		//Edit SPR in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditSPR(
				$student_id,
				$student_no,
				$last_name,
				$first_name,
				$middle_name,
				$birthday, //null
				$gender, //int CONSTRAINT
				$marital_status, //int CONSTRAINT
				$mailing_address, //varchar
				$place_of_birth, //varchar
				$telephone_number, //null
				$mobile_number, //null
				$email, //null
				$entry_sy,
				$entry_semester,
				$religion=null,
				$citizenship=null,
				$region=null,
				$country=null,
				$acr=null,
				$city_address=null,
				$provincial_address =null
			){

			$this->error = array();
			$this->error_count = 0;

			//clean input
			$last_name = htmlentities(ucwords(addslashes(strip_tags($last_name))));
			$first_name = htmlentities(ucwords(addslashes(strip_tags($first_name))));
			$middle_name = htmlentities(ucwords(addslashes(strip_tags($middle_name))));

			$birthday = addslashes(strip_tags($birthday));
			$gender = (int) addslashes(strip_tags($gender));
			$marital_status = (int) addslashes(strip_tags($marital_status));
			$mailing_address = ucwords(addslashes(strip_tags($mailing_address)));
			$place_of_birth = ucwords(addslashes(strip_tags($place_of_birth)));
			$telephone_number = ucwords(addslashes(strip_tags($telephone_number)));
			$mobile_number = ucwords(addslashes(strip_tags($mobile_number)));
			$email = addslashes(strip_tags($email));

			$result = false;
			//create RegEx Hanlder
			$reg = new RtRegExp;

			// if($last_name <> ''){
				// if($reg->CheckName($last_name) <= 0 || $reg->CheckName($last_name) == false){
					// $this->error[$this->error_count] = "Last name is in invalid format.";
					// $this->error_count++;
				// }
			// } else {
				// $this->error[$this->error_count] = "Last name cannot be empty.";
				// $this->error_count++;
			// }
			// if($first_name <> ''){
				// if($reg->CheckName($first_name) <= 0 || $reg->CheckName($first_name) == false){
					// $this->error[$this->error_count] = "First name is in invalid format.";
					// $this->error_count++;
				// }
			// } else {
				// $this->error[$this->error_count] = "First name cannot be empty.";
				// $this->error_count++;
			// }
			// if($middle_name <> ''){
				// if($reg->CheckName($middle_name) <= 0 || $reg->CheckName($middle_name) == false){
					// $this->error[$this->error_count] = "Middle name is in invalid format.";
					// $this->error_count++;
				// }
			// }
			if($email <> ''){
				if($reg->CheckEmail($email) <= 0 || $reg->CheckEmail($email) == false){
					$this->error[$this->error_count] = "Email is in invalid format.";
					$this->error_count++;
				}
			}
			if($gender <= 0 || $gender == ''){
				$this->error[$this->error_count] = "Gender not found.";
				$this->error_count++;
			}
			if($marital_status <= 0 || $marital_status == ''){
				$this->error[$this->error_count] = "Marital Status not found.";
				$this->error_count++;
			}
			if($birthday == '1970-01-01'){
				$this->error[$this->error_count] = "Birthday not inputted.";
				$this->error_count++;
			}
			if($place_of_birth == ''){
				$this->error[sizeof($this->error)] = "Place of Birth cannot be blank.";
			}
			//check if error is found first
			//-->else, just show the result
			if(count($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;
					/*
					INSERT INTO `ads-preadm_records`(`PreaadmissionID`, `LastName`, `FirstName`, `MiddleName`, `Birthday`, `PlaceOfBirth`, `Gender`, `MaritalStatus`, `MailingAddress`, `EmailAddress`, `TelephoneNumber`, `MobileNumber`, `LastSchoolAttended`, `ApplicationType`, `EntrySY`, `EntrySemester`, `ApplicationNo`, `FirstChoice`, `SecondChoice`, `ThirdChoice`, `ApplicationStatus`, `CoursePassed`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8],[value-9],[value-10],[value-11],[value-12],[value-13],[value-14],[value-15],[value-16],[value-17],[value-18],[value-19],[value-20],[value-21],[value-22])
					*/

					$query = "UPDATE `spr-personal_data` SET ";
					$query .= "StudentNo='{$student_no}', ";
					$query .= "`FirstName`='{$first_name}', ";
					$query .= "`LastName`='{$last_name}', ";
					$query .= "`MiddleName`='{$middle_name}', ";
					$query .= "`Gender`={$gender}, ";
					$query .= "`Birthday`='{$birthday}', ";
					$query .= "`PlaceOfBirth`='{$place_of_birth}', ";
					$query .= "`MailingAddress`='{$mailing_address}', ";
					$query .= "`EmailAddress`='{$email}', ";
					$query .= "`TelephoneNumber`='{$telephone_number}', ";
					$query .= "`MobileNumber`='{$mobile_number}', ";
					$query .= "`MaritalStatus`=$marital_status, ";
					if($religion>0){ $query .= "`Religion`=$religion, "; }
					if($citizenship>0){ $query .= "`Citizenship`=$citizenship, "; }
					if($region>0){ $query .= "`Region`=$region, "; }
					if($country>0){ $query .= "`Country`=$country, "; }
					$query .= "`ACR`='{$acr}', ";
					$query .= "`CityAddress`='{$city_address}', ";
					$query .= "`ProvincialAddress`='{$provincial_address}', ";
					$query .= "`EntrySY`=$entry_sy, ";
					$query .= "`EntrySemester`=$entry_semester, Modified=NOW() ";
					$query .= "WHERE StudentID={$student_id}";

					$conn->query($query);

					if($conn->affected_rows > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$this->error_count] = "Error updating student personal record. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		/*--------------------------------------------------------------------------------

			STUDENT CURRENT ACADEMIC BACKGROUND [SELECT | INSERT | UPDATE | DELETE]

		--------------------------------------------------------------------------------*/

		//Returns the Student's Current Academic Background
		//-->Key is Student No
		function GetCurrentAcademicBackgroundsByKey($student_id = null){
			$records = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT StudentID, `CurrentAcademicBackgroundID`, `StudentNo`, `Course`, `StudentType`, `StudentStatus`, `EnrollmentStatus`, ";
				$query .= "`EntrySemester`, `EntrySY`, `YearOfGraduation`, `ApplicationType` FROM `spr-current_academic_background` WHERE 1 ";

				if($student_id != null){
					$query .= "AND ";
					$query .= "StudentID=";
					$query .= $student_id;
					$query .= " ";
					$query .= " ORDER BY CurrentAcademicBackgroundID DESC LIMIT 0,1";
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$records = array();
					$ctr = 0;
					if($result->num_rows > 0){
					// function __construct(
			// $current_academic_background_id, $student_no, $course, $student_type,
			// $student_status, $enrollment_status, $entry_semester, $entry_sy,
			// $year_of_graduation, $application_type
		// )
						while($row = $result->fetch_assoc()){
							$records[$row['StudentID']] = new AcademicBackground(
								$row['CurrentAcademicBackgroundID'], $row['StudentNo'], $row['Course'],
								$row['StudentType'], $row['StudentStatus'], $row['EnrollmentStatus'],
								$row['EntrySemester'], $row['EntrySY'], $row['YearOfGraduation'],
								$row['ApplicationType']
							);
							$ctr++;
						}
					}
				}
			}

			return $records;
		}

		//Add Current Academic Background to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddCurrentAcademicBackground(
				$id, $student_no, $course, $student_type, $student_status, $enrollment_status,
				$entry_semester, $entry_sy, $application_type, $year_of_graduation = null,
				$sy, $sem
			){

			$this->error = array();
			$this->error_count = 0;

			//clean input
			$student_no = ucwords(addslashes(strip_tags($student_no)));

			$course = (int) addslashes(strip_tags($course));
			$student_type = (int) addslashes(strip_tags($student_type));
			$student_status = (int) addslashes(strip_tags($student_status));
			$enrollment_status = (int) addslashes(strip_tags($enrollment_status));
			$entry_semester = (int) addslashes(strip_tags($entry_semester));
			$entry_sy = (int) addslashes(strip_tags($entry_sy));
			$application_type = (int) addslashes(strip_tags($application_type));
			$year_of_graduation = (int) addslashes(strip_tags($year_of_graduation));

			$result = false;

			if($course <= 0 || $course == ''){
				$this->error[$this->error_count] = "Academic Background: Course not found.";
				$this->error_count++;
			}

			if($student_type <= 0 || $student_type == ''){
				$this->error[$this->error_count] = "Academic Background: Student Type not found.";
				$this->error_count++;
			}

			if($student_status <= 0 || $student_status == ''){
				$this->error[$this->error_count] = "Academic Background: Student Status not found.";
				$this->error_count++;
			}

			if($enrollment_status <= 0 || $enrollment_status == ''){
				$this->error[$this->error_count] = "Academic Background: Enrollment Status not found.";
				$this->error_count++;
			}

			if($entry_semester <= 0 || $entry_semester == ''){
				$this->error[$this->error_count] = "Academic Background: Entry Semester not found.";
				$this->error_count++;
			}

			if($entry_sy <= 0 || $entry_sy == ''){
				$this->error[$this->error_count] = "Academic Background: Entry School Year not found.";
				$this->error_count++;
			}

			if($application_type <= 0 || $application_type == ''){
				$this->error[$this->error_count] = "Academic Background: Application Type not found.";
				$this->error_count++;
			}

			//check if error is found first
			//-->else, just show the result
			if(count($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `spr-current_academic_background`(StudentID, `StudentNo`, `Course`, `StudentType`, `StudentStatus`, ";
					$query .= "`EnrollmentStatus`, `EntrySemester`, `EntrySY`, `YearOfGraduation`, `ApplicationType`, Created, Modified, SY2, Sem2) ";
					$query .= "VALUES ({$id},'{$student_no}',{$course},{$student_type},{$student_status},{$enrollment_status},{$entry_semester}, ";
					$query .= "{$entry_sy},{$year_of_graduation}, {$application_type},NOW(),NOW(), {$sy}, {$sem}) ";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$this->error_count] = "Error adding student academic background. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		//Edit Current Academic Background to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditCurrentAcademicBackground(
				$id, $student_no, $curr_course, $course, $student_status, $enrollment_status,
				$entry_semester, $entry_sy, $graduated = 0, $sy, $sem
			){

			$this->error = array();
			$this->error_count = 0;

			//clean input
			$student_no = ucwords(addslashes(strip_tags($student_no)));
			$course = (int) addslashes(strip_tags($course));
			$student_status = (int) addslashes(strip_tags($student_status));
			$enrollment_status = (int) addslashes(strip_tags($enrollment_status));
			$entry_semester = (int) addslashes(strip_tags($entry_semester));
			$entry_sy = (int) addslashes(strip_tags($entry_sy));
			$graduated = (int) $graduated;

			$result = false;

			if($course <= 0 || $course == ''){
				$this->error[$this->error_count] = "Academic Background: Course not found.";
				$this->error_count++;
			}

			if($student_status <= 0 || $student_status == ''){
				$this->error[$this->error_count] = "Academic Background: Student Status not found.";
				$this->error_count++;
			}

			if($enrollment_status <= 0 || $enrollment_status == ''){
				$this->error[$this->error_count] = "Academic Background: Enrollment Status not found.";
				$this->error_count++;
			}

			if($entry_semester <= 0 || $entry_semester == ''){
				$this->error[$this->error_count] = "Academic Background: Entry Semester not found.";
				$this->error_count++;
			}

			if($entry_sy <= 0 || $entry_sy == ''){
				$this->error[$this->error_count] = "Academic Background: Entry School Year not found.";
				$this->error_count++;
			}

			//check if error is found first
			//-->else, just show the result
			if(count($this->error) == 0){
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "SELECT CurrentAcademicBackgroundID FROM `spr-current_academic_background` WHERE StudentID={$id} ORDER BY CurrentAcademicBackgroundID DESC LIMIT 0,1";
					$data = $conn->query($query);
					$row = $data->fetch_assoc();
					$curr_id = $row['CurrentAcademicBackgroundID'];

					if($curr_course == $course){
						$query = "UPDATE `spr-current_academic_background` SET ";
						$query .= "`StudentNo`='{$student_no}', ";
						$query .= "`Course`={$course}, ";
						$query .= "`StudentStatus`={$student_status}, ";
						$query .= "`EnrollmentStatus`={$enrollment_status}, ";
						$query .= "`EntrySemester`={$entry_semester}, ";
						$query .= "`EntrySY`={$entry_sy}, ";
						$query .= "Modified=NOW(), ";
						$query .= "SY2={$sy}, ";
						$query .= "Sem2={$sem} ";
						$query .= "WHERE CurrentAcademicBackgroundID={$curr_id}";
					} else {

						$query = "INSERT INTO `spr-current_academic_background` ( `StudentID`, `StudentNo`, `Course`, `StudentType`, `StudentStatus`, `EnrollmentStatus`, `EntrySemester`, `EntrySY`, `YearOfGraduation`, `ApplicationType`, `Graduate`, `Created`, `Modified`) ";
						$query .= "SELECT  b.`StudentID`, b.`StudentNo`, b.`Course`, b.`StudentType`, b.`StudentStatus`, b.`EnrollmentStatus`, b.`EntrySemester`, b.`EntrySY`, b.`YearOfGraduation`, b.`ApplicationType`, b.`Graduate`, b.`Created`, b.`Modified` FROM `spr-current_academic_background` b ";
						$query .= "WHERE CurrentAcademicBackgroundID={$curr_id} ORDER BY CurrentAcademicBackgroundID DESC LIMIT 0,1";

						$conn->query($query);

						$new_id = 0;
						if($conn->insert_id > 0){
							$new_id = $conn->insert_id;
						}

						$query = "UPDATE `spr-current_academic_background` SET ";
						$query .= "`StudentNo`='{$student_no}', ";
						$query .= "`Course`={$course}, ";
						$query .= "`StudentStatus`={$student_status}, ";
						$query .= "`EnrollmentStatus`={$enrollment_status}, ";
						$query .= "`EntrySemester`={$entry_semester}, ";
						$query .= "`EntrySY`={$entry_sy}, ";
						$query .= "Modified=NOW() ";
						$query .= "WHERE CurrentAcademicBackgroundID={$new_id}";

					}

					$conn->query($query);

					if($conn->affected_rows > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[$this->error_count] = "Error adding student academic background. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;
		}

		/*--------------------------------------------------------

			STUDENT STATUS [SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetStatuses($status_id = null){
			$statuses = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `StatusID`, `Code`, `Description` FROM `ads-student_status` WHERE 1 ";

				if($status_id != null){
					$query .= "AND ";
					$query .= "StatusID=";
					$query .= $status_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$statuses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$statuses[$ctr] = new StudentStatus($row['StatusID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $statuses;
		}

		//Will always return null for errors else an array
		function GetStatusesByKey($status_id = null){
			$statuses = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `StatusID`, `Code`, `Description` FROM `ads-student_status` WHERE 1 ";

				if($status_id != null){
					$query .= "AND ";
					$query .= "StatusID=";
					$query .= $status_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$statuses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$statuses[$row['StatusID']] = new StudentStatus($row['StatusID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $statuses;
		}

		//Add Status to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddStatus($description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			$result = false;

			if($description == ""){ $this->error[sizeof($this->error)] = "Student Status cannot be blank."; }

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `ads-student_status`(Description) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "')";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding student status. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Updates the Student Status in the database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function UpdateStatus($status_id, $description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			if($description == ""){ $this->error[sizeof($this->error)] = "Student Status cannot be blank."; }

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `ads-student_status` SET Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE StatusID=";
					$query .= $status_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error updating student status. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Delete Status in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteStatus($status_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `ads-student_status` ";
				$query .= "WHERE StatusID=";
				$query .= $status_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting student status. Information in use.";
					}	else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			APPLICATION STATUS [SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetApplicationStatuses($status_id = null){
			$statuses = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `StatusID`, `Code`, `Description` FROM `ads-application_status` WHERE 1 ";

				if($status_id != null){
					$query .= "AND ";
					$query .= "StatusID=";
					$query .= $status_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$statuses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							//used StudentStatus since the data needed has the same member as the Student Status
							$statuses[$ctr] = new StudentStatus($row['StatusID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $statuses;
		}

		//Add Status to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddApplicationStatus($description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			$result = false;

			if($description == ""){ $this->error[sizeof($this->error)] = "Application Status cannot be blank."; }

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `ads-application_status`(Description) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "')";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding application status. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Updates the Student Status in the database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function UpdateApplicationStatus($status_id, $description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			if($description == ""){ $this->error[sizeof($this->error)] = "Application Status cannot be blank."; }

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `ads-application_status` SET Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE StatusID=";
					$query .= $status_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error updating application status. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Delete Status in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteApplicationStatus($status_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `ads-application_status` ";
				$query .= "WHERE StatusID=";
				$query .= $status_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting application status. Information in use.";
					}	else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			ENROLLMENT STATUS [SELECT]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetEnrollmentStatusesByKey($status_id = null){
			$statuses = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `StatusID`, `Description` FROM `spr-enrollment_status` WHERE 1 ";

				if($status_id != null){
					$query .= "AND ";
					$query .= "StatusID=";
					$query .= $status_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$statuses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$statuses[$row['StatusID']] = new EnrollmentStatus($row['StatusID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $statuses;
		}

		/*--------------------------------------------------------

			APPLICATION TYPE [SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetApplicationTypes($type_id = null){
			$types = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `TypeID`, `Code`, `Description` FROM `ads-application_type` WHERE 1 ";

				if($type_id != null){
					$query .= "AND ";
					$query .= "TypeID=";
					$query .= $type_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$types = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$types[$ctr] = new ApplicationType($row['TypeID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $types;
		}

		//Will always return null for errors else an array
		function GetApplicationTypesByKey($type_id = null){
			$types = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `TypeID`, `Code`, `Description` FROM `ads-application_type` WHERE 1 ";

				if($type_id != null){
					$query .= "AND ";
					$query .= "TypeID=";
					$query .= $type_id;
					$query .= " ";
				}

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$types = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$types[$row['TypeID']] = new ApplicationType($row['TypeID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $types;
		}

		//Add Type to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddApplicationType($description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			$result = false;

			if($description == ""){ $this->error[sizeof($this->error)] = "Application Type cannot be blank."; }

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "INSERT INTO `ads-application_type`(Description) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "')";

					$conn->query($query);

					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding application status. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Updates the Application Type in the database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function UpdateApplicationType($type_id, $description){

			//clean input
			$description = ucwords(addslashes(strip_tags($description)));

			if($description == ""){ $this->error[sizeof($this->error)] = "Application Type cannot be blank."; }

			$result = false;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "UPDATE `ads-application_type` SET Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE TypeID=";
					$query .= $type_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error updating application type. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}

			return $result;

		}

		//Delete Type in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteApplicationType($type_id){

			$result = false;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `ads-application_type` ";
				$query .= "WHERE TypeID=";
				$query .= $type_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting application type. Information in use.";
					}	else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Get Admission counts
		function GetNextAppNumber(){

			$count = -1;

			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {

					$conn = $this->conn;

					$query = "SELECT ApplicationNo FROM  `ads-preadm_records` ORDER BY ApplicationNo DESC LIMIT 0,1";

					$result = $conn->query($query);

					if($result->num_rows > 0){
						$row = $result->fetch_assoc();
						$application_number = $row['ApplicationNo'];
						$splitted = explode("-",$application_number);
						$num = (int) $splitted[1];
						$count = $num+1;
					} else {
						$count = 1;
					}
				}
			}

			return $count;
		}

		//get Student Courses
		function getStudentCourses($student_id){
			$rows = array();

			$conn = $this->conn;

			$query  = "";
			$query .= "SELECT ";
			$query .= "scl.CourseID as `id`, ";
			$query .= "scl.Code AS `courseCode`, ";
			$query .= "scl.Description AS `courseDescription`, ";
			$query .= "CONCAT('[ ', sc.Code, ' ] ', sc.Description) AS `collegeDescription`, ";
			$query .= "CONCAT(ssy.Start, ' - ', ssy.End) AS `sy`, ";
			$query .= "ss.Description AS `semester`, ";
			$query .= "cab.Graduate AS `isGraduate` ";
			$query .= "FROM `spr-current_academic_background` cab ";
			$query .= "LEFT JOIN `sch-course_list` scl ON scl.CourseID=cab.Course ";
			$query .= "LEFT JOIN `sch-colleges` sc ON sc.CollegeID=scl.College ";
			$query .= "LEFT JOIN `sch-school_years` ssy ON ssy.SchoolYearID=cab.EntrySY ";
			$query .= "LEFT JOIN `sch-semesters` ss ON ss.SemesterID=cab.EntrySY ";
			$query .= "WHERE StudentID={$student_id} ";

			$result = $conn->query($query);

			while($row = $result->fetch_assoc()){
				$rows[] = $row;
			}

			return $rows;
		}

		function getStudentSubjectGroups($sy, $sem, $student_id, $course){
			$rows = array();

				$conn = $this->conn;

				$query  = "";
				$query .= "SELECT DISTINCT ";
				$query .= "ssg.Description AS `subjectGroup`, ssg.SubjectGroupID AS `id`, ssg.HiddenText AS `hidden` ";
				$query .= "FROM `spr-current_academic_background` cab ";
				$query .= "LEFT JOIN `sch-course_list` scl ON scl.CourseID=cab.Course ";
				$query .= "LEFT JOIN `sch-colleges` sc ON sc.CollegeID=scl.College ";
				$query .= "LEFT JOIN `sch-school_years` ssy ON ssy.SchoolYearID=cab.EntrySY ";
				$query .= "LEFT JOIN `sch-semesters` ss ON ss.SemesterID=cab.EntrySY ";
				$query .= "LEFT JOIN `enl-student_enlistment` ese ON ese.StudentID=cab.StudentID ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=ese.Subject ";
				$query .= "LEFT JOIN `sch-subjects` sss ON sss.SubjectID=scs.Subject ";
				$query .= "LEFT JOIN `sch-subject_group` ssg ON ssg.SubjectGroupID=sss.Group ";
				$query .= "WHERE cab.StudentID={$student_id} ";
				$query .= "AND cab.Course={$course} ";
				$query .= "AND cab.SY2={$sy} AND cab.Sem2={$sem} ";
				$query .= "AND ese.SY=cab.SY2 AND ese.Semester=cab.Sem2 ";
				$query .= "ORDER BY ssg.Sequence ";

				$result = $conn->query($query);

				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}

				return $rows;
		}

		function getStudentSubjectsByGroup($sy, $sem, $student_id, $course, $group){
			$rows = array();

				$conn = $this->conn;

				$query  = "";
				$query .= "SELECT ";
				$query .= "ssg.SubjectGroupID AS `groupId`, sss.Description AS `subjectDescription`, sss.Code AS `subjectCode`, ";
				$query .= "sss.Units AS `nominalDuration`, ";
				$query .= "gg.midtermGrade, gg.finalGrade ";
				$query .= "FROM `spr-current_academic_background` cab ";
				$query .= "LEFT JOIN `sch-course_list` scl ON scl.CourseID=cab.Course ";
				$query .= "LEFT JOIN `sch-colleges` sc ON sc.CollegeID=scl.College ";
				$query .= "LEFT JOIN `sch-school_years` ssy ON ssy.SchoolYearID=cab.EntrySY ";
				$query .= "LEFT JOIN `sch-semesters` ss ON ss.SemesterID=cab.EntrySY ";
				$query .= "LEFT JOIN `enl-student_enlistment` ese ON ese.StudentID=cab.StudentID ";
				$query .= "LEFT JOIN `sch-curriculum_subjects` scs ON scs.CurriculumSubjectID=ese.Subject ";
				$query .= "LEFT JOIN `sch-subjects` sss ON sss.SubjectID=scs.Subject ";
				$query .= "LEFT JOIN `sch-subject_group` ssg ON ssg.SubjectGroupID=sss.Group ";
				$query .= "LEFT JOIN `grd-grades` gg ON gg.enlistedSubject=ese.EnlistmentID ";
				$query .= "WHERE cab.StudentID={$student_id} ";
				$query .= "AND cab.Course={$course} ";
				$query .= "AND cab.SY2={$sy} AND cab.Sem2={$sem} ";
				$query .= "AND ese.SY=cab.SY2 AND ese.Semester=cab.Sem2 ";
				$query .= "AND ssg.SubjectGroupID={$group} ";
				$result = $conn->query($query);

				while($row = $result->fetch_assoc()){
					$rows[] = $row;
				}

				return $rows;
		}
	}

?>
