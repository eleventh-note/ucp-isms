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
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "emp.inc.php");
	require_once("_libs/fpdf/" . "wrap_pdf.php");
	require_once(CLASSLIST . "pdf.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "stdnts.inc.php");
	
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
	
	$hnd = new EmployeeManager($conn);
	$std = new StudentManager($conn);
	$hnd_cu = new CourseManager($conn);
	$hnd_co = new CollegeManager($conn);
	$hnd_sc = new SchoolManager($conn);
		
	//Define Sorting Order
	$sort = 0;
	if(!isset($_GET['sort'])){
		$records = $std->GetSprsSort($sort);
	} else {
		$sort = (int) $_GET['sort'];
		$records = $std->GetSprsSort($sort);
	}

	$backgrounds = $std->GetCurrentAcademicBackgroundsByKey();
	$courses = $hnd_cu = $hnd_cu->GetCoursesByKey();
	$college_types = $hnd_co->GetCollegeTypesByKey();
	$student_statuses = $std->GetStatusesByKey();
	$enrollment_statuses = $std->GetEnrollmentStatusesByKey();
	$enrollment_statuses = $std->GetEnrollmentStatusesByKey();
	$semesters = $hnd_sc->GetSemestersByKey();
	$school_years = $hnd_sc->GetSchoolYearsByKey();
	$sy = $hnd_sc->GetActiveSchoolYear();
	$sem = $hnd_sc->GetActiveSemester();
	$sy = $sy[0];
	$sem = $sem[0];
	
	//#CREATE PDF
	$pdf = new PDF('P','pt','Letter');
	//#CREATE FIRST PAGE
	$pdf->AddPage();
	//#HEADER
	$pdf->CreateHeader('LIST OF ACCEPTED STUDENTS for S.Y. ' . $sy->start . '-' . $sy->end . ' / ' . $sem->description);
	//#COLUMN HEADER
	$pdf->SetFont('Arial', 'B', '8');
	$pdf->MultiCell(0,10,'');
	$pdf->tablewidths = array(30,150,70,70, 175,60);
	$data = array();
	$data[] = array('No.','Name', 'Student No.', 'Mobile Number', 'Course', 'Date Entered');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(230,230,230);
	$pdf->DrawTable($data, $pdf->FontSize+6, 'L', true);
	//#CREATE ROWS
	$pdf->SetFont('Arial', '', '8');	
	$ctr=0;

	$data = array();
	foreach($records as $item){
		//for($i = 0; $i < 100; $i++){
			$ctr++;
			
			$name = $item->last_name . ", " . $item->first_name . " " . $item->middle_name;
			$to_replace = array("&Ntilde;","&ntilde;");
			$replace_with = array("�", "�");
			$name = str_replace($to_replace, $replace_with, $name);
			
			$data[] = array(
				$ctr,
				$name,
				$item->student_no,
				$item->mobile_number,
				"[" . $courses[$backgrounds[$item->student_id]->course]->code . "] " . $courses[$backgrounds[$item->student_id]->course]->description,
				date("M-d-Y",strtotime($item->created))
			);
		//}
	}
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->DrawTable($data, $pdf->FontSize+6, 'L', true);
	
	$pdf->Output();
	
	$conn->close();
?>