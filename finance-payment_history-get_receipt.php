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

//::START OF 'CONFIGURATION'
	require_once("_system/_config/sys_config.php");
	//configurations can be overriden here
	include_once(CLASSLIST . "dataconnection.inc.php");
	require_once(CLASSLIST . "auditor.inc.php");
	require_once(CLASSLIST . "audit.inc.php");
	require_once(CLASSLIST . "user.inc.php");
	require_once(CLASSLIST . "sentry.inc.php");
	require_once(CLASSLIST . "menu.inc.php");
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "fin.inc.php");
	require_once("_libs/fpdf/" . "wrap_pdf.php");
	require_once(CLASSLIST . "pdf.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "crrclm.inc.php");
	require_once(CLASSLIST . "schdls.inc.php");
	
	
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
	$hnd = new FinanceManager($conn);
	$hnd_sc = new SchoolManager($conn);	
	
	$semesters = $hnd_sc->GetActiveSemester();
	$semester = $semesters[0];
	$school_years = $hnd_sc->GetActiveSchoolYear();
	$school_year = $school_years[0];
	
	$id = (int) $_GET['id'];
	$sid = (int) $_GET['sid'];
	$type = (int) $_GET['tid'];
	$data = $hnd->GetReceipt($id, $sid, $type);
	
	$data['semester'] = $semester->description;
	$data['sy'] = $school_year->start . "-" . $school_year->end;

	//modification - 20130706 - Audit Trail
	$auditor = new AuditTrail($conn);
	$audit = new Audit();
	$audit->userId = $UserInfo->id;
	$audit->tableName = "fin-payments";
	$audit->action = "View Payment / Get Receipt";
	$audit->newValue = "PaymentID:{0}; ";
	$audit->newValue = str_replace("{0}", $id, $audit->newValue);
	$auditor->Add($audit);
	
	#CREATE PDF
	$pdf = new PDF('P','pt','Letter');
	$pdf->AddPage();
	
	$pdf->OutputReceipt($data);
	
	$pdf->Output();
	$conn->Close();

?>
