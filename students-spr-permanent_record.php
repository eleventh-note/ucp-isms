<?php
	/* #-------------------------------------------------
	   #
	   #	Description:	Template for 00 Default Layout
	   #	Autdor:		Algefmarc A. L. Almocera
	   #	Date Started:	May 30, 2011
	   #	Last Modified:	June 8, 2011
	   #
	   #-------------------------------------------------
	*/
//::START OF 'SESSION DECLARATION'
	//open session here if needed (e.g: session_start())
	session_start();
//::END OF 'SESSION DECLARATION'

	//Set no caching
	//header("Content-Type: application/pdf");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	header("Cache-Control: no-store, no-cache, must-revalidate"); 
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

//::START OF 'CONFIGURATION'
	require_once("_system/_config/sys_config.php");
	//configurations can be overriden here
	include_once(CLASSLIST . "dataconnection.inc.php");
	require_once(CLASSLIST . "user.inc.php");
	require_once(CLASSLIST . "sentry.inc.php");
	require_once(CLASSLIST . "menu.inc.php");
	require_once(CLASSLIST . "dvsns.inc.php");
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "crrclm.inc.php");
	require_once(CLASSLIST . "schdls.inc.php");
	require_once(CLASSLIST . "fclts.inc.php");
	require_once(CLASSLIST . "fin.inc.php");
	require_once(CLASSLIST . "stdnts.inc.php");
	require_once(CLASSLIST . "enl.inc.php");
	require_once("_libs/fpdf/" . "wrap_pdf.php");
	require_once(CLASSLIST . "pdf.inc.php");
	
//::END OF 'CONFIGURATION'
		
	//# General Variables - shown in all documents for easy modification
		$title = SCHOOL_NAME . " Integrated School Management System";
		$keywords = "";
		$description = "";
		$autdor = "";
		$robots="noindex,nofollow";
	
	//Sentry/Security Measures must be done here
	if(isset($_SESSION['UserInfo'])){
		//autdenticate user privileges
		$UserInfo = unserialize($_SESSION['UserInfo']);
		$Sentry = new Sentry($UserInfo);
		
		$PagePrivileges = new PagePrivileges();
		$PagePrivileges->AddPrivilege("SUPERADMIN");
		$PagePrivileges->AddPrivilege("Student - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd_cg = new CollegeManager($conn);
	$hnd_co = new CourseManager($conn);
	$hnd_sc = new SchoolManager($conn);
	$hnd_su = new SubjectManager($conn);
	$hnd_cu = new CurriculumManager($conn);
	$hnd_sh = new ScheduleManager($conn);
	$hnd_fa = new FacultyManager($conn);
	$hnd_fc = new FacilitiesManager($conn);
	$hnd_fi = new FinanceManager($conn);
	$std = new StudentManager($conn);
	$hnd_enl = new EnlistmentManager($conn);
	
	if(isset($_GET['sid'])){
		$id = (int) $_GET['sid'];
		$cid = (int) $_GET['cid'];
		
		$date = $hnd_enl->GetEnrollmentDate($id);
		
		$records = $std->GetSprs($id);
		
		if(sizeof($records) > 0){
			foreach($records as $item){ $record = $item; }
			$backgrounds = $std->GetCurrentAcademicBackgroundsByKey($record->student_id);
			foreach($backgrounds as $item){ $background = $item; }
			
			//#Dictionaries
			$dict_courses = $hnd_co->GetCoursesByKey();
			$dict_school_years = $hnd_sc->GetSchoolYearsByKey();
			$dict_semesters = $hnd_sc->GetSemestersByKey();
			$dict_times = $hnd_sc->GetSchoolTimesByKey();
			$dict_days = $hnd_sc->GetSchoolDaysByKey();
			$dict_rooms = $hnd_fc->GetRoomsByKey();
			$dict_faculties = $hnd_fa->GetFacultiesByKey();
			
			//Sem & Year
			$semesters = $hnd_sc->GetActiveSemester();
			$semester = $semesters[0];
			$school_years = $hnd_sc->GetActiveSchoolYear();
			$school_year = $school_years[0];
	
			$sem = $semester->semester_id;
			$sy = $school_year->year_id;
			
			//### GET SUBJECT GROUPS
			$groups = $std->getStudentSubjectGroups($sy, $sem, $id, $cid);

			$student_number = $record->student_no;
			$tmp = $dict_school_years[$background->entry_sy];

			// $school_year = "S.Y. " . $tmp->start . " - " . $tmp->end;
			// $semester = $dict_semesters[$background->entry_semester]->description;
		
			$student_name = $record->last_name . ", " . $record->first_name . " " . $record->middle_name;
			
			$birthday = $record->birthday;
			$secondsInAYear = 365.25 * 86400;
			$age = floor((time() - strtotime($birthday)) / $secondsInAYear);
			$gender = ($record->gender==1)? 'Male': 'Female';
			$birthday = date("F d, Y", strtotime($record->birthday));
			$birthplace = $record->place_of_birth;
			$address = $record->provincial_address; 
			$guardian = "";
			
			//$course = $dict_courses[$background->course]->description;
			$course = $hnd_co->GetCourseByKey($cid);
			
			$to_replace = array("&Ntilde;","&ntilde;", "&amp;Ntilde;", "&amp;ntilde;");
			$replace_with = array("Ñ", "ñ", "Ñ", "ñ");
			$student_name = str_replace($to_replace, $replace_with, $student_name);
			
			//List of Curriculum Subjects
			//$curriculum_subjects = $hnd_cu->GetAllSubjectsByCode();
			
			//##FILL DATE WITH CURRENTLY ENLISTED SUBJECTS
			$is_old = false;
			if(!isset($_SESSION['enlisted_subjects'])){
				$subs = $hnd_enl->GetStudentEnlistmentSubjects($id, $school_year->year_id, $semester->semester_id);
				$is_old = true;
				foreach($subs as $enl){
					$_SESSION['enlisted_subjects'][] = $enl->section_subject . "_" . $enl->curriculum_subject;
				}
			}
			
			//#CREATE PDF
			$pdf = new PDF('P','pt','Legal');
			//#CREATE FIRST PAGE
			$pdf->AliasNbPages();
			$pdf->AddPage();
			//#HEADER

			$pdf->CreateHeader("OFFICE OF THE REGISTRAR");

			$pdf->SetFont('Arial', 'B', '12');
			$pdf->MultiCell(0,20,'OFFICIAL TRANSCRIPT OF RECORD',0,'C');
			//#COLUMN HEADER
			$pdf->MultiCell(0,3,'',0,'C');
			$pdf->SetFont('Arial', 'B', '7');
			$pdf->SetX(25); $pdf->Cell(250, 15, '');
			$pdf->SetFont('Arial', '', '7');
			$pdf->Cell(0, 15, 'MAI-REG-005-F04',0,0,'R');
			

			$pdf->SetFont('Arial', '', '7');
			$pdf->SetY($pdf->y-6);
//			$pdf->Cell(0, 15, "Rev 00 / June 2011", 0,0,'R');
			$pdf->SetY($pdf->y+4);
			$pdf->Ln();
			//# FIRST LINE
			$pdf->SetFont('Arial', '', '9');
			$pdf->SetX(25);
			$pdf->Cell(35,13, "Name: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(200,13,"{$student_name}", 'B');
			$pdf->Cell(10,13, "");
			$pdf->SetFont('Arial', '', '9');
			//$pdf->Cell(25,13, "Age: ");
			$pdf->SetFont('Arial', '', '9');
			//$pdf->Cell(22,13,"{$age}", 'B');
			$pdf->Cell(40,13, "");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(40,13, "Gender: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(40,13,"{$gender}", 'B');
			$pdf->Ln();

			//# SECOND LINE
			$pdf->SetFont('Arial', '', '9');
			$pdf->SetX(25);
			//$pdf->Cell(65,13, "Date of Birth: ");
			$pdf->SetFont('Arial', '', '9');
			//$pdf->Cell(170,13,"{$birthday}", 'B');
			//$pdf->Cell(10,13, "");
			$pdf->SetFont('Arial', '', '9');
			//$pdf->Cell(70,13, "Place of Birth: ");
			$pdf->SetFont('Arial', '', '9');
			//$pdf->Cell(200,13,"{$birthplace}", 'B');
			$pdf->SetFont('Arial', 'B', '9');
			//var_dump($record);exit();
			//$pdf->Ln();

			//# THIRD LINE
			$pdf->SetFont('Arial', '', '9');
			$pdf->SetX(25);
			//$pdf->Cell(95,13, "Parent or Guardian: ");
			$pdf->SetFont('Arial', '', '9');
			//$pdf->Cell(140,13,"{$guardian}", 'B');
			//$pdf->Cell(10,13, "");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(45,13, "Address: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(225,13,"{$address}", 'B');
			$pdf->Cell(16,13, "");
			$pdf->Cell(90,13, "Admission Credential: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(20,13,"", 'B');
			$pdf->SetFont('Arial', 'B', '9');
			//var_dump($record);exit();
			$pdf->Ln();
			
			//# FOURTH LINE
			$pdf->SetFont('Arial', '', '9');
			$pdf->SetX(25);
			$pdf->Cell(38,13, "Course: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(237,13,"{$course[0]->description}", 'B');
			$pdf->Cell(10,13, "");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(95,13, "Date of Graduation: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(135,13,"", 'B');
			$pdf->SetFont('Arial', 'B', '9');
			//var_dump($record);exit();
			$pdf->Ln();
			
			//# FIFTH LINE
			$pdf->SetFont('Arial', 'B', '9');
			$pdf->SetX(25);
			$pdf->Cell(38,13, "");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(247,13,"");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(50,13, "S.O. No.: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(180,13,"", 'B');
			$pdf->SetFont('Arial', 'B', '9');
			$pdf->Ln();
			
			//# SIXTH LINE
			$pdf->SetFont('Arial', 'B', '9');
			$pdf->SetX(25);
			$pdf->Cell(38,13, "");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(247,13,"");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(65,13, "Date Issued: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(165,13,"", 'B');
			$pdf->SetFont('Arial', 'B', '9');
			//var_dump($record);exit();
			$pdf->Ln();
			
			//# SEVENTH LINE
			$pdf->SetFont('Arial', '', '9');
			$pdf->SetX(25);
			$pdf->Cell(146,13, "Intermediate Grade Completed at:");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(250,13,"", 'B');
			$pdf->Cell(8,13,"");
			$pdf->Cell(30,13, "Year: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(40,13, "", 'B');
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(165,13,"");
			$pdf->SetFont('Arial', 'B', '9');
			//var_dump($record);exit();
			$pdf->Ln();
			
			//# SIXTH LINE
			$pdf->SetFont('Arial', '', '9');
			$pdf->SetX(25);
			$pdf->Cell(126,13, "High School Completed at:");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(270,13,"", 'B');
			$pdf->Cell(8,13,"");
			$pdf->Cell(30,13, "Year: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(40,13, "", 'B');
			$pdf->SetFont('Arial', '', '9');
			$pdf->Cell(165,13,"");
			$pdf->SetFont('Arial', 'B', '9');
			//var_dump($record);exit();
			$pdf->Ln();
			
			//#CREATE ROWS
			//#PRINT TABLE HEADERS
			$pdf->MultiCell(0,8,'');
			//##  MAIN HEADER
			//$pdf->MultiCell(0,18,"LIST OF SUBJECTS",'LRTB','C',1,true);
			//##  COLUMNS
			$pdf->tablewidths = array(70,250,70,80,70);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(230,230,230);
			$pdf->SetFont('Arial', 'B', '8');
			$data = array();
			$data[] = array('Code', 'UNIT OF COMPETENCIES (Descriptive Title)', 'Rating', 'Nominal Duration', 'Remarks');
			$pdf->DrawGrades2($data, 8+4,'C`', true);
			$pdf->SetFont('Arial', '', '8');
			$pdf->SetFillColor(255,255,255);
			//##  DATA
			
			$newGroup = true;
			$start = true;
			$currentGroup = null;
			
			//blank line
			$data = array();
			$data[] = array("", "", "", "", "");
			$pdf->DrawGrades2($data, $pdf->FontSize+4,'L',true);
			
			foreach($groups as $group){
				//new group
				if($start == true){
					$data = array();
					$currentGroup = $group;
					$data[] = array("", strtoupper($currentGroup['subjectGroup']), "", "", "");
					if($currentGroup['hidden'] != null || $currentGroup['hidden'] != ""){
						$data[] = array("", $currentGroup['hidden'], "", "", "");
					}
					$pdf->SetFont('Arial', 'B', '8');
					$pdf->DrawGrades2($data, $pdf->FontSize+4,'L',true);
					
				}
				//group changed
				if($currentGroup['id'] != $group['id']){
					$data = array();
					$currentGroup = $group;
					$data[] = array("", strtoupper($currentGroup['subjectGroup']), "", "", "");
					if($currentGroup['hidden'] != null || $currentGroup['hidden'] != ""){
						$data[] = array("", $currentGroup['hidden'], "", "", "");
					}
					$pdf->SetFont('Arial', 'B', '8');
					$pdf->DrawGrades2($data, $pdf->FontSize+4,'L',true);
				}
				
				$subjects = $std->getStudentSubjectsByGroup($sy, $sem, $id, $cid, $group['id']);
				foreach($subjects as $subject){
					$grades = array();
					$grades['midtermGrade'] = $subject['midtermGrade'];
					$grades['preFinalGrade'] = $subject['finalGrade'];
					$grades['remarks'] = "";
					$grades['finalGrade'] = "";
					
					$grades['finalGrade'] = round((((int) $grades['midtermGrade'] + (int) $grades['preFinalGrade'])/2));
					
					if($grades['midtermGrade'] == "" || $grades['preFinalGrade'] == ""){
						$grades['finalGrade'] = "";
						$valid = false;
					} elseif($grades['midtermGrade']=="INC"){
						$grades['finalGrade']= "INC";
						$valid = false;
					} elseif($grades['preFinalGrade']=="INC"){
						$grades['finalGrade']= "INC";
						$valid = false;
					} elseif($grades['midtermGrade']=="DRP"){
						$grades['finalGrade']= "DRP";
						$valid = false;
					} elseif($grades['preFinalGrade']=="DRP"){
						$grades['finalGrade']= "DRP";
						$valid = false;
					}
					
					if($grades['finalGrade'] >= 75){
						$grades['remarks'] = "COMPETENT";
					} elseif($grades['finalGrade'] < 75){
						$grades['remarks'] = "NOT YET COMPETENT";
					}
					
					switch($grades['finalGrade']){
						case 'INC':
							$valid = false;
							$grades['remarks'] = "INCOMPLETE";
							break;
						case 'DRP':
							$valid = false;
							$grades['remarks'] = "DROPPED";
							break;
					}
					
					if($grades['midtermGrade'] == "" && $grades['preFinalGrade'] == ""){		
						$valid = false;
						$grades['remarks'] = "";
					}
				
					$data = array();
					$data[] = array($subject['subjectCode'], $subject['subjectDescription'], $grades['remarks'] , $subject['nominalDuration'], $grades['remarks']);
					$pdf->SetFont('Arial', '', '');
					$pdf->DrawGrades2($data, $pdf->FontSize+4,'L',true);
				
				}
			}	
			
			$pdf->Ln();
			//# FIXED DATA
			$pdf->SetFont('Arial', 'B', '9');
			$pdf->SetX(25);
			$pdf->Cell(38,13, "GRADING SYSTEM: ");
			$pdf->SetFont('Arial', '', '9');
			$pdf->Ln();
			$pdf->SetFont('Arial', '', '9');
			$pdf->SetX(55);
			$pdf->Cell(38,13, "75% and above     C   -   Competent");
			$pdf->Ln();
			$pdf->SetX(55);
			$pdf->Cell(38,13, "74% and below    NC   -   Not Yet Competent / Incompetent");
			$pdf->Ln();
			$pdf->SetFont('Arial', '', '8');
			$pdf->SetX(25);
			$pdf->Cell(38,13, "An erasure or alteration of any entry invalidates this document");
			$pdf->SetFont('Arial', '', '8');
			$pdf->Ln();$pdf->Ln();
			$pdf->SetX(25);
			$pdf->Cell(50,12, "Prepared By: "); $pdf->Cell(100, 13, "", 'B'); $pdf->Cell(20, 13, "");
			$pdf->Cell(50,12, "Date Issued: "); $pdf->Cell(100, 13, "", 'B'); $pdf->Cell(20, 13, "");
			$pdf->Cell(40,12, "Remarks: "); $pdf->Cell(100, 13, "", 'B'); $pdf->Cell(20, 13, "");
			
			$pdf->Ln(); $pdf->Ln();
			$pdf->SetX(25);
			$pdf->Cell(50,12, "Checked & Evaluated by: "); 
			$pdf->Ln();
			$pdf->Cell(100, 12, "", ''); $pdf->Cell(150, 12, "", 'B'); $pdf->Cell(50, 12, "", ''); $pdf->Cell(150, 12, "", 'B');
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->Ln();
			//$pdf->Cell(100, 12, "", ''); $pdf->Cell(150, 12, "MARIVIC D. FERNANDEZ, MBA", '', 0, 'C'); $pdf->Cell(50, 12, "", ''); $pdf->Cell(150, 12, "ATTY. ARMANDO S. FERNANDEZ", '', 0, 'C');
			//$pdf->Ln();
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(100, 12, "", ''); $pdf->Cell(150, 12, "Registrar", '', 0, 'C'); $pdf->Cell(50, 12, "", ''); $pdf->Cell(150, 12, "VP for Finance", '', 0, 'C');
			$pdf->Output();
			
		} else {
			$_SESSION['error'] = array('Record not found.');
			header("Location: enlistment-search-student.php");
			exit();
		}
	} else {
		$_SESSION['error'] = array('Record not found.');
		header("Location: enlistment-search-student.php");
		exit();
	}
	
	$conn->Close();
?>