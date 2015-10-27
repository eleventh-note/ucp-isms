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
	require_once(CLASSLIST . "grds.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "stdnts.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
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
		$PagePrivileges->AddPrivilege("Grades - Administrator");
		$PagePrivileges->AddPrivilege("Grades - Viewer");
		$PagePrivileges->AddPrivilege("Grades - Encoder");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}

	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd = new GradesManager($conn);
	$hnd_sc = new SchoolManager($conn);
	$std = new StudentManager($conn);
	$hnd_co = new CourseManager($conn);

	if(isset($_GET['id'])){
		$studentId = $_GET['id'];
		//Sem & Year
		$semesters = $hnd_sc->GetActiveSemester();
		$semester = $semesters[0];
		$school_years = $hnd_sc->GetActiveSchoolYear();
		$school_year = $school_years[0];

		$grades = $hnd->getStudentGrades($studentId, $school_year->year_id, $semester->semester_id);
		$studentName = $grades[0]['studentName'];
		$studentNo = $grades[0]['StudentNo'];

		$records = $std->GetSprs($studentId);
		foreach($records as $item){ $record = $item; }
		$backgrounds = $std->GetCurrentAcademicBackgroundsByKey($record->student_id);
		foreach($backgrounds as $item){ $background = $item; }

		$dict_courses = $hnd_co->GetCoursesByKey();

		if(sizeof($grades) > 0){

			// $school_year = "S.Y. " . $tmp->start . " - " . $tmp->end;
			// $semester = $dict_semesters[$background->entry_semester]->description;

			$student_name = $studentName;
			$to_replace = array("&Ntilde;","&ntilde;");
			$replace_with = array("Ñ", "ñ");
			$student_name = str_replace($to_replace, $replace_with, $student_name);

			//#CREATE PDF
			$pdf = new PDF('P','pt','Letter');
			//#CREATE FIRST PAGE
			$pdf->AliasNbPages();
			$pdf->AddPage();
			//#HEADER
			$pdf->CreateHeader2("OFFICE OF THE REGISTRAR SEMESTRAL GRADE REPORT {$semester->shorthand} S.Y {$school_year->start} - {$school_year->end}");
			$pdf->TitleStart("OFFICE OF THE REGISTRAR");
			$pdf->TitleNext("SEMESTRAL GRADE REPORT");
			$pdf->TitleEnd("{$semester->shorthand} S.Y. {$school_year->start} - {$school_year->end}");
			//#COLUMN HEADER
			// $pdf->MultiCell(0,3,'');
			// $pdf->SetFont('Arial', 'B', '7');
			// $pdf->SetX(25);
			// $pdf->Cell(70, 15, "S.Y. " . $school_year->start . "-" . $school_year->end . " / " . $semester->description);
			// $pdf->Ln();
			//# NAME, ADDRESS, COURSE and SCHOOL YEAR
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetX(25);
			$pdf->Cell(55,13, "Student No.: ");
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(0,13,"{$studentNo}");
			$pdf->Ln();

			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetX(25);
			$pdf->Cell(55,13, "Name: ");
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(0,13,"{$student_name}");
			$pdf->Ln();

			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetX(25); $pdf->Cell(55, 13, "Address: ");
			$pdf->SetFont('Arial', '', '8');

			$mailing_address = $record->mailing_address;
			$to_replace = array("&Ntilde;","&ntilde;");
			$replace_with = array("Ñ", "ñ");
			$mailing_address = str_replace($to_replace, $replace_with, $mailing_address);

			$pdf->Cell(45,13, "{$mailing_address}");

			$pdf->Ln();

			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetX(25); $pdf->Cell(55, 13, "Course: ");
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(45,13, "[{$dict_courses[$background->course]->code}] {$dict_courses[$background->course]->description}");
			$pdf->Ln();

			//#CREATE ROWS
			//#PRINT TABLE HEADERS
			$pdf->MultiCell(0,8,'');
			//##  MAIN HEADER
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetFillColor(180,180,180);
			$pdf->SetTextColor(0,0,0);
			$pdf->tablewidths = array(550);
			//$pdf->MultiCell(0,18,"LIST OF SUBJECTS",'LRTB','C',1,true);
			$data[] = array('LIST OF SUBJECTS');
			$pdf->DrawTable($data, 8+4,'C',true);
			//##  COLUMNS
			//$pdf->tablewidths = array(25, 70, 180, 45, 55, 65, 110);

			$pdf->tablewidths = array(25, 70, 220, 60, 65, 110);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(230,230,230);
			$pdf->SetFont('Arial', 'B', '10');
			$data = array();
			//$data[] = array('No.', 'Subject Code', 'Subject Description', 'Midterm', 'Pre-Finals', 'Final Grade', 'Remarks');
			$data[] = array('No.', 'Subject Code', 'Subject Description', 'Midterm Grade', 'Final Grade', 'Remarks');
			$pdf->DrawGrades($data, 10+4,'C`', true);
			//##  DATA
			$data = array();
			$ctr=0;

			$valid = true; //check if display value for general average
			$totalGrade = 0;
			foreach($grades as $grade){
				$ctr++;
				$midtermGrade = $grade['midtermGrade'];
				$preFinalGrade = $grade['finalGrade'];

				$remarks = "";
				$finalGrade = "";

				$finalGrade = ((((float) $midtermGrade + (float) $preFinalGrade)/2));
				$totalGrade += $finalGrade;
				if($midtermGrade == "" || $preFinalGrade == ""){
					$finalGrade = "";
				} elseif($midtermGrade=="NME"){
					$finalGrade= "No Midterm Exam";
					$valid = false;
				} elseif($preFinalGrade=="NFE"){
					$finalGrade= "No Final Exam";
					$valid = false;
				} elseif($midtermGrade=="DRP"){
					$finalGrade= "DROP";
					$valid = false;
				} elseif($preFinalGrade=="DRP"){
					$finalGrade= "DROP";
					$valid = false;
				}

				switch($finalGrade){
					case 'INC':
						$valid = false;
						$remarks = "INCOMPLETE";
						break;
					case 'DRP':
						$valid = false;
						$remarks = "DROPPED";
						break;
				}

				if((float)$finalGrade <= 3.00){
					$remarks = "PASSED";
				} elseif($finalGrade > 3.00){
					$remarks = "FAILED";
				}

				if($midtermGrade == "" && $preFinalGrade == ""){
					$valid = false;
					$remarks = "";
				}

				if ($preFinalGrade == '5.00') {
					$remarks = "FAILED";
				}

				if ($midtermGrade == 'NME') {
					$remarks = "No Midterm Exam";
				}

				if ($preFinalGrade == 'NFE') {
					$remarks = "No Final Exam";
				}

				if($midtermGrade == "DRP" || $preFinalGrade == "DRP"){
					$valid = false;
					$remarks = "DROPPED";
				}

				if($midtermGrade == "UD" || $preFinalGrade == "UD"){
					$valid = false;
					$remarks = "UNOFFICIALLY DROPPED";
				}

				if($preFinalGrade == "PASSED") {
					$remarks = "PASSED";
				}

				//$data[] = array($ctr, $grade['subjectCode'], $grade['subjectDescription'], $midtermGrade, $preFinalGrade, $finalGrade, $remarks);
				$data[] = array($ctr, $grade['subjectCode'], $grade['subjectDescription'], $midtermGrade, $preFinalGrade, $remarks);
			}


			$pdf->SetFont('Arial', '', '8');
			$pdf->SetFillColor(255,255,255);

			$pdf->DrawGrades($data, $pdf->FontSize+4,'L',true);

			$pdf->SetFont('Arial', '', '10');
			$pdf->Cell(348, 18, "GENERAL AVERAGE: ", 0, 0, 'R');
			$pdf->SetFont('Arial', 'B', '10');
			$pdf->Cell(2, 18, "");
			if($valid == true){
				$pdf->Cell(50,18, round($totalGrade/$ctr));
			} else {
				$pdf->Cell(50,18, "INCOMPLETE");
			}
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->Cell(300,16, ""); $pdf->MultiCell(170,16, "REGISTRAR", 0, "C");
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(300,16, ""); $pdf->MultiCell(170,16, "APPROVED BY", 'T', "C");
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(300,16, ""); $pdf->MultiCell(170,16, "REGISTRATION ASSISTANT", 0, "C");
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(300,16, ""); $pdf->MultiCell(170,16, "RELEASED BY", 'T', "C");
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(300,16, ""); $pdf->MultiCell(170,16, "DATE RELEASED", 'T', "C");
			$pdf->Output();

		} else {
			$_SESSION['error'] = array('Record not found.');
			header("Location: grades-viewer-search.php");
			exit();
		}
	} else {
		$_SESSION['error'] = array('Record not found.');
		header("Location: grades-viewer-search.php");
		exit();
	}

	$conn->Close();
?>
