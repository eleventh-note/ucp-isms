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
	$audit->tableName = "fin-fees";
	$audit->newValue = "Description:{0}; Price: {1}; Fee Type: {2}; ";
	
	if(isset($_POST['fee_save'])){
		$action = $_POST['fee_save'];
		
		switch($action){
			case 'Add':
				$hnd = new FinanceManager($conn);
				$description = $_POST['description'];
				$price = $_POST['price'];
				$fee_type = $_POST['fee_type'];
				
				$is_virtual = 0;
				if(isset($_POST['virtual'])){
					$is_virtual = 1;
				}
				
				echo $description . "<br/>";
				echo $price . "<br/>";
				echo $fee_type . "<br/>";
				
				if($hnd->AddFee($description, $price, $fee_type, $is_virtual) == true){
				
					//modification - 20130706 - Audit Trail
					$audit->action = "Add School Fee";
					$audit->newValue = str_replace("{0}", $description, $audit->newValue);
					$audit->newValue = str_replace("{1}", $price, $audit->newValue);
					$audit->newValue = str_replace("{2}", $fee_type, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "School Fee successfully added!";
					header("Location: finance-school_fees.php");
					exit();
				} else {
					$_SESSION['error'] = $hnd->error;
					header("Location: finance-school_fees-add.php");
					exit();
				}
				break;
			case 'Save':
				$hnd = new FinanceManager($conn);
				$id = $_POST['id'];
				$description = $_POST['description'];
				$price = $_POST['price'];
				$fee_type = $_POST['fee_type'];
				
				$is_virtual = 0;
				if(isset($_POST['virtual'])){
					$is_virtual = 1;
				}
				
				echo $description . "<br/>";
				echo $price . "<br/>";
				echo $fee_type . "<br/>";
								
				if($hnd->EditFee($id, $description, $price, $fee_type, $is_virtual) == true){
				
					//modification - 20130706 - Audit Trail
					$audit->action = "Update School Fee";
					$audit->newValue = str_replace("{0}", $description, $audit->newValue);
					$audit->newValue = str_replace("{1}", $price, $audit->newValue);
					$audit->newValue = str_replace("{2}", $fee_type, $audit->newValue);
					$auditor->Add($audit);
					
					$_SESSION['success'] = "School Fee successfully saved!";
					header("Location: finance-school_fees.php");
					exit();
				} else {
					$_SESSION['position'] = $position;
					$_SESSION['error'] = $hnd->error;
					header("Location: finance-school_fees-edit.php?id=$id");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new FinanceManager($conn);
		$id = (int) $_GET['id'];
		if($hnd->DeleteFee($id) == true){
			//modification - 20130706 - Audit Trail
			$audit->action = "Delete School Fee";
			$audit->newValue = "FeeID: {0}";
			$audit->newValue = str_replace("{0}", $id, $audit->newValue);
			$auditor->Add($audit);
			
			$_SESSION['success'] = "School Fee successfully deleted!";
			header("Location: finance-school_fees.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: finance-school_fees.php");
			exit();
		}
	} else {
		header("Location: finance-school_fees.php.php");
		exit();
	}
		
	$conn->Close();
	
?>
