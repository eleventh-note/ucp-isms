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

	$to_replace = array("&Ntilde;","&ntilde;", "&amp;Ntilde;", "&amp;ntilde;");
	$replace_with = array("Ñ", "ñ", "Ñ", "ñ");

	if(isset($_GET['id'])){
		$sectionSubject = (int) $_GET['id'];
		$students = $hnd->getStudentsForEncoding($sectionSubject, $UserInfo->employee_id);
		$section = $students[0]['section'];
		$subjectCode = $students[0]['subjectCode'];
		$subjectDescription = $students[0]['subjectDescription'];
		$facultyName = str_replace($to_replace, $replace_with, $students[0]['facultyName']);

		//Sem & Year
		$semesters = $hnd_sc->GetActiveSemester();
		$semester = $semesters[0];
		$school_years = $hnd_sc->GetActiveSchoolYear();
		$school_year = $school_years[0];

		if(sizeof($students) > 0){

			//#CREATE PDF
			$pdf = new PDF('P','pt','Letter');
			//#CREATE FIRST PAGE
			$pdf->AliasNbPages();
			$pdf->AddPage();
			//#HEADER
			$pdf->CreateHeader2("OFFICE OF THE REGISTRAR SEMESTRAL GRADE REPORT {$semester->shorthand} S.Y {$school_year->start} - {$school_year->end}");
			$pdf->TitleStart("GRADE SHEET");
			$pdf->TitleEnd("{$semester->shorthand} S.Y. {$school_year->start} - {$school_year->end}");

			//#CREATE ROWS
			//#PRINT TABLE HEADERS
			$pdf->MultiCell(0,8,'');
			//##  MAIN HEADER
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetFillColor(180,180,180);
			$pdf->SetTextColor(0,0,0);
			$pdf->tablewidths = array(520);
			//$pdf->MultiCell(0,18,"LIST OF SUBJECTS",'LRTB','C',1,true);
			$data[] = array("{$section}    |    " . strtoupper($subjectDescription));
			$pdf->DrawTable($data, 8+4,'C',true);
			//##  COLUMNS
			$pdf->tablewidths = array(25, 200, 55, 65, 175);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(230,230,230);
			$pdf->SetFont('Arial', 'B', '10');
			$data = array();
			$data[] = array('No.', 'Name of Students', 'MIDTERM', 'FINAL', 'REMARKS');
			$pdf->DrawGrades($data, 10+4,'C', true);
			//##  DATA
			$data = array();
			$ctr=0;

			foreach($students as $student){
				//var_dump($student);
				$studentName = str_replace($to_replace, $replace_with, $student['studentName']);
				$midtermGrade = $student['midtermGrade'];
				$preFinalGrade = $student['finalGrade'];
				$ctr++;

				$remarks = "";
				$finalGrade = "";

				$finalGrade = ((((float) $midtermGrade + (float) $preFinalGrade)/2));

				if($midtermGrade == "" || $preFinalGrade == ""){
					$finalGrade = "";
				} elseif($midtermGrade=="NME"){
					$finalGrade= "No Midterm Exam";
				} elseif($preFinalGrade=="NFE"){
					$finalGrade= "No Final Exam";
				} elseif($midtermGrade=="DRP"){
					$finalGrade= "DROP";
				} elseif($preFinalGrade=="DRP"){
					$finalGrade= "DROP";
				}

				if((float)$finalGrade <= 3.00){
					$remarks = "PASSED";
				} elseif($finalGrade > 3.00){
					$remarks = "FAILED";
				}

				if ($midtermGrade == '5.00' || $preFinalGrade == '5.00') {
					$remarks = "FAILED";
				}

				if ($midtermGrade == 'NME') {
					$remarks = "No Midterm Exam";
				}

				if ($preFinalGrade == 'NFE') {
					$remarks = "No Final Exam";
				}

				switch($finalGrade){
					case 'INC':
						$remarks = "INCOMPLETE";
						break;
					case 'DRP':
						$remarks = "DROPPED";
						break;
				}

				if($midtermGrade == "" && $preFinalGrade == ""){
					$remarks = "";
				}

				if($midtermGrade == "DRP" || $preFinalGrade == "DRP"){
					$remarks = "DROPPED";
				}

				if($midtermGrade == "UD" || $preFinalGrade == "UD"){
					$remarks = "UNOFFICIALLY DROPPED";
				}

				$data[] = array($ctr, $studentName, $midtermGrade, $preFinalGrade, $remarks);
			}

			$pdf->SetFont('Arial', '', '8');
			$pdf->SetFillColor(255,255,255);

			$pdf->DrawGrades($data, $pdf->FontSize+4,'L',true);

			$pdf->Ln();
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(20,16, ""); $pdf->Cell(100,16, "Submitted By:", 0, "L"); $pdf->Cell(200, 16, ""); $pdf->MultiCell(100,16, "Approved By:");
			$pdf->Ln();
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->Cell(20,16, ""); $pdf->Cell(200,16, "{$facultyName}", 0, 0, "C"); $pdf->Cell(100, 16, ""); $pdf->MultiCell(200,16, "", 0, "C");
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(20,16, ""); $pdf->Cell(200,16, "Instructor", 'T', 0, "C"); $pdf->Cell(100, 16, ""); $pdf->MultiCell(200,16, "Approved By Dean/Chairperson", 'T', "C");
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
