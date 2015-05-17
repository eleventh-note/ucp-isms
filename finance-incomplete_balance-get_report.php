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
	require_once(CLASSLIST . "auditor.inc.php");
	require_once(CLASSLIST . "audit.inc.php");
	require_once(CLASSLIST . "user.inc.php");
	require_once(CLASSLIST . "sentry.inc.php");
	require_once(CLASSLIST . "menu.inc.php");
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "emp.inc.php");
	require_once("_libs/fpdf/" . "wrap_pdf.php");
	require_once(CLASSLIST . "pdf.inc.php");
	require_once(CLASSLIST . "fclts.inc.php");
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "grds.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "fin.inc.php");
	
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
		$PagePrivileges->AddPrivilege("Finance - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	$fin = new FinanceManager($conn);
	$hnd_sc = new SchoolManager($conn);	
	
	$semesters = $hnd_sc->GetActiveSemester();
	$semester = $semesters[0];
	$sem = $semester->semester_id;
	$school_years = $hnd_sc->GetActiveSchoolYear();
	$school_year = $school_years[0];
	$sy = $school_year->year_id;
	
	//modification - 20130706 - Audit Trail
	$auditor = new AuditTrail($conn);
	$audit = new Audit();
	$audit->userId = $UserInfo->id;
	$audit->tableName = "fin-payments";
	$audit->action = "Get Student Balances Report";
	$audit->newValue = "SY: {0}; Sem: {1} ";
	$audit->newValue = str_replace("{0}", $semester->semester_id, $audit->newValue);
	$audit->newValue = str_replace("{1}", $school_year->year_id, $audit->newValue);
	$auditor->Add($audit);
	
	$students = $fin->getStudents();
	
	//#CREATE PDF
	$pdf = new PDF('P','pt','Letter');
	//#CREATE FIRST PAGE
	$pdf->AddPage();
	//######################
	// BUILDINGS
	//######################
	//#HEADER
	$pdf->CreateHeader("STUDENT BALANCES FOR S.Y. {$school_year->start}-{$school_year->end} / {$semester->shorthand}");
	//#COLUMN HEADER
	$pdf->SetFont('Arial', 'B', '8');
					
	//#CREATE ROWS
	$pdf->SetFont('Arial', '', '8');	
	$ctr=0;
	
	//######################
	// STUDENT BALANCES
	//######################
	
	//#COLUMN HEADER
	$pdf->MultiCell(0,15,"");
	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetFillColor(180,180,180);
	$pdf->SetTextColor(0,0,0);
	//$pdf->MultiCell(0, 18,"List of Rooms",'LRTB','C',1,true);
	$pdf->SetFont('Arial', 'B', '8');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(230,230,230);
	$pdf->Cell(20, 15, 'No.','LRTB','L', 1, true);
	$pdf->Cell(60, 15, 'Student No.','LRTB','L', 1, true);
	$pdf->Cell(180, 15, 'Student Name','LRTB','L', 1, true);
	$pdf->Cell(70, 15, 'Bills','LRTB','L', 1, true);
	$pdf->Cell(70, 15, 'Payments','LRTB','L', 1, true);
	$pdf->Cell(70, 15, 'Collectibles','LRTB','L', 1, true);
	//$pdf->Cell(0, 15, 'Previous Balances','LRTB','L', 1, true);
	$pdf->SetFillColor(255,255,255);
	$pdf->Ln();
	
	//#CREATE ROWS
	$pdf->SetFont('Arial', '', '8');	
	$ctr=0;

	$totalBills = 0;
	$totalPayments = 0;
	foreach($students as $item){
		//for($i = 0; $i < 100; $i++){
			$studentBills = $fin->getStudentBillsSpecific($item['StudentID'], $sy, $sem);
			$studentPayments = $fin->getStudentPaymentsSpecific($item['StudentID'], $sy, $sem);
			$totalBills += $studentBills;
			$totalPayments += $studentPayments;
			$collectibles = $studentBills-$studentPayments;

			
			if($studentBills > 0){
				$ctr++;
				$to_replace = array("&Ntilde;", "&ntilde", "&amp;Ntilde;", "&amp;ntilde;");
				$replace_with = array("Ñ", "ñ", "Ñ", "ñ");
				$pdf->Cell(20, 12, "{$ctr}",'LRTB','L', 1, true);
				$pdf->Cell(60, 12, "{$item['StudentNo']}",'LRTB','L', 1, true);
				$pdf->Cell(180, 12, str_replace($to_replace, $replace_with,"{$item['studentName']}") ,'LRTB','L', 1, true);
				$pdf->Cell(70, 12, "Php " . number_format($studentBills,2),'LRTB','L', 1, true);
				$pdf->Cell(70, 12, "Php " . number_format($studentPayments,2),'LRTB','L', 1, true);
				$pdf->Cell(70, 12, "Php " . number_format($collectibles,2),'LRTB','L', 1, true);
				//$pdf->Cell(0, 12, "Php " . number_format($previousBalance,2),'LRTB','L', 1, true);

				$pdf->Ln();
			}
	}
	
	if($totalBills > 0){
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', '8');
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(230,230,230);
		$pdf->Cell(90, 15, 'Total Bills','LRTB','L', 1, true);
		$pdf->Cell(90, 15, 'Total Payments','LRTB','L', 1, true);
		$pdf->Cell(90, 15, 'Total Collectibles','LRTB','L', 1, true);
		//$pdf->Cell(0, 15, 'Previous Balances','LRTB','L', 1, true);
		$pdf->SetFillColor(255,255,255);
		$pdf->Ln();
		
		$to_replace = array("&Ntilde;", "&ntilde", "&amp;Ntilde;", "&amp;ntilde;");
		$replace_with = array("Ñ", "ñ", "Ñ", "ñ");
		$pdf->Cell(90, 12, "Php " . number_format($totalBills,2),'LRTB','L', 1, true);
		$pdf->Cell(90, 12, "Php " . number_format($totalPayments,2),'LRTB','L', 1, true);
		$pdf->Cell(90, 12, "Php " . number_format($totalBills-$totalPayments,2),'LRTB','L', 1, true);
		//$pdf->Cell(0, 12, "Php " . number_format($previousBalance,2),'LRTB','L', 1, true);

		$pdf->Ln();
	}
	$pdf->Output();
	
	$conn->close();
?>