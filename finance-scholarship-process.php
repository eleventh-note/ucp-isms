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
	require_once(CLASSLIST . "emp.inc.php");
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
	//modification - 20130706 - Audit Trail
	$auditor = new AuditTrail($conn);
	$audit = new Audit();
	$audit->userId = $UserInfo->id;
	$audit->tableName = "fin-discounts";
	$audit->newValue = "Description:{0}; Price: {1}; Percentage: {2}; ";
	
	if(isset($_POST['scholarship_save'])){
		$action = $_POST['scholarship_save'];
		
		switch($action){
			case 'Add':
				$hnd = new FinanceManager($conn);
				$description = $_POST['description'];
				$price = $_POST['price'];
				$percentage = $_POST['percentage'];
				
				if($hnd->AddScholarship($description, $price, $percentage) == true){
				
					//modification - 20130706 - Audit Trail
					$audit->action = "Add Scholarship";
					$audit->newValue = str_replace("{0}", $description, $audit->newValue);
					$audit->newValue = str_replace("{1}", $price, $audit->newValue);
					$audit->newValue = str_replace("{2}", $percentage, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "Scholarship successfully added!";
					header("Location: finance-scholarship.php");
					exit();
				} else {
					$_SESSION['error'] = $hnd->error;
					header("Location: finance-scholarship-add.php");
					exit();
				}
				break;
			case 'Save':
				$hnd = new FinanceManager($conn);
				$id = $_POST['id'];
				$description = $_POST['description'];
				$price = $_POST['price'];
				$percentage = $_POST['percentage'];
	
				if($hnd->EditScholarship($id, $description, $price, $percentage) == true){
					//modification - 20130706 - Audit Trail
					$audit->action = "Update Scholarship";
					$audit->newValue = str_replace("{0}", $description, $audit->newValue);
					$audit->newValue = str_replace("{1}", $price, $audit->newValue);
					$audit->newValue = str_replace("{2}", $percentage, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "Scholarship successfully updated!";
					header("Location: finance-scholarship.php");
					exit();
				} else {
					$_SESSION['position'] = $position;
					$_SESSION['error'] = $hnd->error;
					header("Location: finance-scholarship-edit.php?id=$id");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new FinanceManager($conn);
		$id = (int) $_GET['id'];
		if($hnd->DeleteScholarship($id) == true){
			//modification - 20130706 - Audit Trail
			$audit->action = "Delete Scholarship";
			$audid->newValue = "DiscountId: {0}";
			$audit->newValue = str_replace("{0}", $id, $audit->newValue);
			$auditor->Add($audit);
			
			$_SESSION['success'] = "Scholarship successfully deleted!";
			header("Location: finance-scholarship.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: finance-scholarship.php");
			exit();
		}
	} else {
		header("Location: finance-scholarship.php.php");
		exit();
	}
		
	$conn->Close();
	
?>
