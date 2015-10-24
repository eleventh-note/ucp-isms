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
	require_once(CLASSLIST . "enl.inc.php");
	require_once("_libs/fpdf/" . "wrap_pdf.php");
	require_once(CLASSLIST . "pdf.inc.php");
  require_once(CLASSLIST . "report.inc.php");

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
		$PagePrivileges->AddPrivilege("Reports - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}

	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();

	//# HANDLERS
	$hnd_enl = new EnlistmentManager($conn);
	$hnd_sc = new SchoolManager($conn);
  $hnd_r = new Report($conn);

	if(isset($_GET['sort'])){
		//Sem & Year
		$semesters = $hnd_sc->GetActiveSemester();
		$semester = $semesters[0];
		$school_years = $hnd_sc->GetActiveSchoolYear();
		$school_year = $school_years[0];

		$sort = isset($_GET['sort']) ? (int) $_GET['sort'] : 1;
		$section_subjects = $hnd_r->GetStudentCountPerSubject($sort, $semester->semester_id, $school_year->year_id);

		//#CREATE PDF
		$pdf = new PDF('P','pt','Letter');
		//#CREATE FIRST PAGE
		$pdf->AliasNbPages();
		$pdf->AddPage();
		// //#HEADER
		$pdf->CreateHeader2("OFFICE OF THE REGISTRAR SEMESTRAL GRADE REPORT {$semester->shorthand} S.Y {$school_year->start} - {$school_year->end}");
	  //$pdf->TitleStart("SECTION: {$subjects[0]['SectionName']}      SUBJECT CODE: {$subjects[0]['SubjectCode']}");
		//$pdf->TitleEnd("{$semester->shorthand} S.Y. {$school_year->start} - {$school_year->end}");

		//#CREATE ROWS
		//#PRINT TABLE HEADERS
		$pdf->MultiCell(0,8,'');
		//##  MAIN HEADER
		$pdf->SetFont('Arial', 'B', '8');
		$pdf->SetFillColor(180,180,180);
		$pdf->SetTextColor(0,0,0);
		$pdf->tablewidths = array(550);
		//$pdf->MultiCell(0,18,"LIST OF SUBJECTS",'LRTB','C',1,true);
		$data[] = array('STUDENT COUNT PER SUBJECT PER SECTION');
		$pdf->DrawTable($data, 8+4,'C',true);
		##  COLUMNS
		$pdf->tablewidths = array(25, 100, 250, 75, 100);
		//$pdf->tablewidths = array(25, 75, 300, 25, 100);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(230,230,230);
		$pdf->SetFont('Arial', 'B', '10');
		$data = array();
		//$data[] = array('No.', 'Subject Code', 'Subject Description', 'Midterm', 'Pre-Finals', 'Final Grade', 'Remarks');
		$data[] = array('No.', 'Code', 'Subject', 'Section', '# of Students');
		$pdf->DrawGrades($data, 10+4,'C', true);
		//##  DATA
		$data = array();
		$ctr=0;

		$valid = true; //check if display value for general average
		$totalGrade = 0;
		foreach($section_subjects as $subject){
			$ctr++;
			$data[] = array($ctr, $subject['SubjectCode'], $subject['SubjectDescription'], $subject['Section'], $subject['NoStudents']);
		}


		$pdf->SetFont('Arial', '', '8');
		$pdf->SetFillColor(255,255,255);

		$pdf->DrawStudentCount($data, $pdf->FontSize+4,'L',true);
		$pdf->Output();

	} else {
		$_SESSION['error'] = array('Record not found.');
		header("Location: reports-student_count_per_subject_section.php");
		exit();
	}

	$conn->Close();
?>
