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
		$PagePrivileges->AddPrivilege("Users - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd = new User($conn);
	//modification - 20130706 - Audit Trail
	$auditor = new AuditTrail($conn);
	
	$audit = new Audit();
	$audit->userId = $UserInfo->id;
	$audit->tableName = "adm-users";
	$audit->newValue = "Username:{0}; EmployeeId:{1};";
	
	$auditPrivilege = new Audit();
	$auditPrivilege->userId = $UserInfo->id;
	$auditPrivilege->tableName = "adm-user_assigned_right";
	$auditPrivilege->newValue = "User: {0}; Right: {1}";
	
	if(isset($_POST['add_user'])){
		$action = $_POST['add_user'];
		
		switch($action){
			case 'Add User':

				//modification - 20130706 - Audit Trail
				$audit->action = "Add User";
				
				$username = $_POST['username'];
				$password = $_POST['password'];
				$cpassword = $_POST['cpassword'];
				$employee = $_POST['employee'];
				
				$data['username'] = $_POST['username'];
				$data['password'] = $_POST['password'];
				$data['cpassword'] = $_POST['cpassword'];
				$data['employee'] = $_POST['employee'];
				$data['privileges'] = array();
				
				if(isset($_POST['privileges'])){
					$data['privileges'] = $_POST['privileges'];
				}
				
				//modification - 20130706 - Audit Trail
				$audit->newValue = str_replace("{0}", $data['username'], $audit->newValue);
				$audit->newValue = str_replace("{1}", $data['employee'], $audit->newValue);
				
				if($hnd->Add($username, $password, $cpassword, $employee) == true){
					//modification - 20130706 - Audit Trail
					$auditor->Add($audit);
					
					//GRANT PRIVILEGES
					$id = $hnd->insert_id;
					foreach($data['privileges'] as $privilege){
						//modification - 20130706 - Audit Trail
						$auditPrivilege->action = "Grant Privilege";
						$auditPrivilege->newValue = "User: {0}; Right: {1}";
						$auditPrivilege->newValue = str_replace("{0}", $username, $auditPrivilege->newValue);
						$pDetails = $hnd->GetPrivilege($privilege);
						$auditPrivilege->newValue = str_replace("{1}", $pDetails['Title'], $auditPrivilege->newValue);
						
						$hnd->GrantPrivilege($id, $privilege);
						$auditor->Add($auditPrivilege);
					}
					
					$_SESSION['success'] = "New User successfully added!";
					header("Location: users.php");
					exit();
				} else {
					$_SESSION['data'] = $data;
					$_SESSION['error'] = $hnd->error;
					header("Location: user-add.php");
					exit();
				}
				break;
			case 'Save User':

				//modification - 20130706 - Audit Trail
				$audit->action = "Update User";
				
				$id = (int) $_POST['id'];
				$username = $_POST['username'];
				$password = $_POST['password'];
				$cpassword = $_POST['cpassword'];
				$employee = $_POST['employee'];
				
				$data['username'] = $_POST['username'];
				$data['password'] = $_POST['password'];
				$data['cpassword'] = $_POST['cpassword'];
				$data['employee'] = $_POST['employee'];
				$data['privileges'] = array();
				if(isset($_POST['privileges'])){
					$data['privileges'] = $_POST['privileges'];
				}
				
				//modification - 20130706 - Audit Trail
				$audit->newValue = str_replace("{0}", $data['username'], $audit->newValue);
				$audit->newValue = str_replace("{1}", $data['employee'], $audit->newValue);
				
				if($hnd->Edit($id, $username, $password, $cpassword, $employee) == true){
					//modification - 20130706 - Audit Trail
					$auditor->Add($audit);
					//modification - 20130706 - Audit Trail
					$auditPrivilege->action = "Remove Privileges";
					$auditPrivilege->newValue = str_replace("{0}", $username, $auditPrivilege->newValue);
					$auditPrivilege->newValue = str_replace("{1}", "DELETE CURRENT PRIVILEGES", $auditPrivilege->newValue);
					$auditor->Add($auditPrivilege);
					
					$hnd->RemovePrivileges($id);
					foreach($data['privileges'] as $privilege){
						//modification - 20130706 - Audit Trail
						$auditPrivilege->action = "Grant Privilege";
						$auditPrivilege->newValue = "User: {0}; Right: {1}";
						$auditPrivilege->newValue = str_replace("{0}", $username, $auditPrivilege->newValue);
						$pDetails = $hnd->GetPrivilege($privilege);
						$auditPrivilege->newValue = str_replace("{1}", $pDetails['Title'], $auditPrivilege->newValue);
						$hnd->GrantPrivilege($id, $privilege);
						$auditor->Add($auditPrivilege);
					}
					
					$_SESSION['success'] = "User details successfully saved!";
					header("Location: users.php");
					exit();
				} else {
					$_SESSION['data'] = $data;
					$_SESSION['error'] = $hnd->error;
					header("Location: user-edit.php?id={$id}");
					exit();
				}
				break;
		}//end switch
		
	} elseif(isset($_GET['action'])) {
		$hnd = new FacilitiesManager($conn);
		$id = (int) $_GET['id'];
		if($hnd->DeleteRoom($id) == true){
			$_SESSION['success'] = "Room successfully deleted!";
			header("Location: facilities-room.php");
			exit();
		} else {
			$_SESSION['error'] = $hnd->error;
			header("Location: facilities-room.php");
			exit();
		}
	} else {
		header("Location: facilities-room.php");
		exit();
	}
		
	$conn->Close();
	
?>