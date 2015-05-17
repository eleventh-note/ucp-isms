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
				
				if($hnd->AddFee($description, $price, $fee_type, $is_virtual) == true){;
					$_SESSION['success'] = "School Fee successfully added!";
					header("Location: ecnanif-s.php");
					exit();
				} else {
					$_SESSION['error'] = $hnd->error;
					header("Location: ecnanif-s-a.php");
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
								
				if($hnd->EditFee($id, $description, $price, $fee_type, $is_virtual) == true){;
					$_SESSION['success'] = "School Fee successfully saved!";
					header("Location: ecnanif-s.php");
					exit();
				} else {
					$_SESSION['position'] = $position;
					$_SESSION['error'] = $hnd->error;
					header("Location: ecnanif-s-e.php?id=$id");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new FinanceManager($conn);
		$id = (int) $_GET['id'];
		if($hnd->DeleteFee($id) == true){
			$_SESSION['success'] = "School Fee successfully deleted!";
			header("Location: ecnanif-s.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: ecnanif-s.php");
			exit();
		}
	} else {
		header("Location: ecnanif-s.php");
		exit();
	}
		
	$conn->Close();
	
?>
