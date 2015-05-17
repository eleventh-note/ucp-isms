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
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "emp.inc.php");
	require_once("_libs/fpdf/" . "wrap_pdf.php");
	require_once(CLASSLIST . "pdf.inc.php");
	require_once(CLASSLIST . "fclts.inc.php");
	
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
		$PagePrivileges->AddPrivilege("Facilities - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	$hnd = new FacilitiesManager($conn);
	$buildings = $hnd->GetBuildings();
	$rooms = $hnd->GetRooms();
	
	//#CREATE PDF
	$pdf = new PDF('P','pt','Letter');
	//#CREATE FIRST PAGE
	$pdf->AddPage();
	//######################
	// BUILDINGS
	//######################
	//#HEADER
	$pdf->CreateHeader('FACILITIES');
	//#COLUMN HEADER
	$pdf->SetFont('Arial', 'B', '8');


	$pdf->MultiCell(0,15,"");
	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetFillColor(180,180,180);
	$pdf->SetTextColor(0,0,0);
	$pdf->MultiCell(390, 18,"List of Buildings",'LRTB','C',1,true);
	$pdf->SetFont('Arial', 'B', '8');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(230,230,230);
	$pdf->Cell(20, 15, 'No.','LRTB','L', 1, true);
	$pdf->Cell(200, 15, 'Building','LRTB','L', 1, true);
	$pdf->Cell(70, 15, 'Code','LRTB','L', 1, true);
	$pdf->Cell(100, 15, 'No. of Floors','LRTB','L', 1, true);
	$pdf->Ln();
								
	//#CREATE ROWS
	$pdf->SetFont('Arial', '', '8');	
	$ctr=0;

	foreach($buildings as $item){
		//for($i = 0; $i < 100; $i++){
			$ctr++;
			$pdf->Cell(20, 12, $ctr ,'LTRB');
			$pdf->Cell(200, 12, $item->description, 'LTRB');
			$pdf->Cell(70, 12, $item->code, 'LTRB');
			$pdf->Cell(100, 12, $item->storeys, 'LTRB');
			//$pdf->Cell(100, 12, $item->semester);
			$pdf->Ln();
		//}
	}
	
	//######################
	// ROOMS
	//######################
	
	//#COLUMN HEADER
	$pdf->MultiCell(0,15,"");
	$pdf->SetFont('Arial', 'B', '10');
	$pdf->SetFillColor(180,180,180);
	$pdf->SetTextColor(0,0,0);
	$pdf->MultiCell(0, 18,"List of Rooms",'LRTB','C',1,true);
	$pdf->SetFont('Arial', 'B', '8');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFillColor(230,230,230);
	$pdf->Cell(20, 15, 'No.','LRTB','L', 1, true);
	$pdf->Cell(120, 15, 'Room','LRTB','L', 1, true);
	$pdf->Cell(70, 15, 'Code','LRTB','L', 1, true);
	$pdf->Cell(100, 15, 'Building','LRTB','L', 1, true);
	$pdf->Cell(50, 15, 'Floor','LRTB','L', 1, true);
	$pdf->Cell(100, 15, 'Type','LRTB','L', 1, true);
	$pdf->Cell(0, 15, 'Status','LRTB','L', 1, true);
	$pdf->Ln();
	
	//#CREATE ROWS
	$pdf->SetFont('Arial', '', '8');	
	$ctr=0;

	foreach($rooms as $item){
		//for($i = 0; $i < 100; $i++){
			$ctr++;
			$pdf->Cell(20, 12, $ctr ,'LTRB');
			$pdf->Cell(120, 12, $item->description,'LTRB');
			$pdf->Cell(70, 12, $item->code,'LTRB');
			$pdf->Cell(100, 12, $item->building->description,'LTRB');
			$pdf->Cell(50, 12, $item->floor,'LTRB');
			$pdf->Cell(100, 12, $item->type->description,'LTRB');
			$pdf->Cell(0, 12, $item->status->description,'LTRB');
			//$pdf->Cell(100, 12, $item->semester);
			$pdf->Ln();
		//}
	}
	$pdf->Output();
	
	$conn->close();
?>