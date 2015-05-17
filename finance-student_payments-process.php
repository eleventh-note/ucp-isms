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
	require_once(CLASSLIST . "auditor.inc.php");
	require_once(CLASSLIST . "audit.inc.php");
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
	require_once(CLASSLIST . "stdnts.inc.php");
	require_once(CLASSLIST . "enl.inc.php");
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
	$hnd_cg = new CollegeManager($conn);
	$hnd_co = new CourseManager($conn);
	$hnd_sc = new SchoolManager($conn);
	$hnd_su = new SubjectManager($conn);
	$hnd_cu = new CurriculumManager($conn);
	$hnd_sh = new ScheduleManager($conn);
	$hnd_fa = new FacultyManager($conn);
	$hnd_fc = new FacilitiesManager($conn);
	$std = new StudentManager($conn);
	$hnd_enl = new EnlistmentManager($conn);
	$hnd_fin = new FinanceManager($conn);
	
	//modification - 20130706 - Audit Trail
	$auditor = new AuditTrail($conn);
	$audit = new Audit();
	$audit->userId = $UserInfo->id;
	$audit->tableName = "fin-payments";
	$audit->newValue = "TransactionNumber:{0}; StudentID: {1}; Fee: {2}; Amount: {3}; SchoolYear: {4}; Semester: {5}; ";
	
	//Sem & Year
	$semesters = $hnd_sc->GetActiveSemester();
	$semester = $semesters[0];
	$school_years = $hnd_sc->GetActiveSchoolYear();
	$school_year = $school_years[0];
	
	//## POST PAYMENT
	if(isset($_POST['post_payment'])){
		$id = (int) $_POST['student_id'];
		$count = sizeof($_SESSION['payments']);
		$ctr = 0;
		
		$transaction_no = $hnd_fin->GetNextTransactionNumber('bir');		
		
		foreach($_SESSION['payments'] as $item){
			if($hnd_fin->AddPayment($id, $transaction_no, $item['fee'], $item['amount'], $school_year->year_id, $semester->semester_id) == true){
				$ctr++;
				
				//modification - 20130706 - Audit Trail
				$audit->action = "Add Payment";
				$audit->newValue = "TransactionNumber:{0}; StudentID: {1}; Fee: {2}; Amount: {3}; SchoolYear: {4}; Semester: {5}; ";
				$audit->newValue = str_replace("{0}", $transaction_no, $audit->newValue);
				$audit->newValue = str_replace("{1}", $id, $audit->newValue);
				$audit->newValue = str_replace("{2}", $item['fee'], $audit->newValue);
				$audit->newValue = str_replace("{3}", $item['amount'], $audit->newValue);
				$audit->newValue = str_replace("{4}", $school_year->year_id, $audit->newValue);
				$audit->newValue = str_replace("{5}", $semester->semester_id, $audit->newValue);
				$auditor->Add($audit);
			}
		}
		
		if($ctr==$count){
			$_SESSION['success'] = "Payments successfully posted. <a href=\"finance-payment_history-get_receipt.php?id={$transaction_no}&tid=7&sid={$id}\" target=\"_blank\">Click here to get receipt</a>.";
			header("Location: finance-student_payments-search.php");
			exit();
		} else {
			var_dump($hnd_fin);
			exit();
			$_SESSION['error'] = array('Error posting payment. Please try again.');
			header("Location: finance-student_payments.php?id={$id}");
			exit();
		}
	}
	
	//## ADD PAYMENT
	if(isset($_POST['add_payment'])){
		$data['fee'] = (int) $_POST['fee'];
		$data['amount'] = (float) $_POST['amount'];
		$id = (int) $_POST['student_id'];
		
		$_SESSION['error'] = array();
		
		if($data['fee'] <= 0){
			$_SESSION['error'][] = "Please select a fee type.";
		}
		
		// if($data['amount'] == ""){
			// $_SESSION['error'][] = "Please enter amount. Zero is allowed.";
		// }
		
		if(sizeof($_SESSION['error']) == 0){
			$_SESSION['payments'][] = $data;
			$_SESSION['success'] = 'Payment successfully added.';
			header("Location: finance-student_payments.php?id={$id}");
		} else {
			header("Location: finance-student_payments.php?id={$id}");
			exit();
		}
		
	}

	//## DELETE PAYMENT FROM LIST
	if(isset($_GET['id']) && isset($_GET['key'])){
		$id = (int) $_GET['id'];
		$key = (int) $_GET['key'];
		
		if(isset($_SESSION['payments'][$key])){
			unset($_SESSION['payments'][$key]);
			$_SESSION['success'] = 'Payment successfully removed from list.';
			header("Location: finance-student_payments.php?id={$id}");
			exit();
		}
		
	}
	
	//close the connection
	$conn->Close();
?>