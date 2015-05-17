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
	
	if(isset($_POST['room_save'])){
		$action = $_POST['room_save'];
		
		switch($action){
			case 'Add Room':

				$hnd = new FacilitiesManager($conn);
				$description = $_POST['description'];
				$code = $_POST['code'];
				$floor = $_POST['floor'];
				$building = $_POST['building'];
				$seating_capacity = $_POST['seating_capacity'];
				$floor_area = $_POST['floor_area'];
				$room_type = $_POST['room_type'];
				$room_status = $_POST['room_status'];
				
				if($hnd->AddRoom($description, $code, $floor, $floor_area, $seating_capacity, $building, $room_type, $room_status) == true){
					$_SESSION['success'] = "New Room successfully added!";
					header("Location: facilities-room.php");
					exit();
				} else {
					
					$_SESSION['description'] = $description;
					$_SESSION['code'] = $code;
					$_SESSION['floor'] = $floor;
					$_SESSION['building'] = $building;
					$_SESSION['seating_capacity'] = $seating_capacity;
					$_SESSION['floor_area'] = $floor_area;
					$_SESSION['room_status'] = $room_status;
					$_SESSION['room_type'] = $room_type;
										
					$_SESSION['error'] = $hnd->error;
					header("Location: facilities-room-add.php");
					exit();
				}
				break;
			case 'Save Details':

				$hnd = new FacilitiesManager($conn);
				
				$id = $_POST['id'];

				$description = $_POST['description'];
				$code = $_POST['code'];
				$floor = $_POST['floor'];
				$building = $_POST['building'];
				$seating_capacity = $_POST['seating_capacity'];
				$floor_area = $_POST['floor_area'];
				$room_type = $_POST['room_type'];
				$room_status = $_POST['room_status'];
				
				if($hnd->EditRoom($id, $description, $code, $floor, $floor_area, $seating_capacity, $building, $room_type, $room_status) == true){
					$_SESSION['success'] = "Room details successfully saved!";
					header("Location: facilities-room.php");
					exit();
				} else {
				
					$_SESSION['description'] = $description;
					$_SESSION['code'] = $code;
					$_SESSION['floor'] = $floor;
					$_SESSION['building'] = $building;
					$_SESSION['seating_capacity'] = $seating_capacity;
					$_SESSION['floor_area'] = $floor_area;
					$_SESSION['room_status'] = $room_status;
					$_SESSION['room_type'] = $room_type;
					
					$_SESSION['error'] = $hnd->error;
					header("Location: facilities-room-edit.php?id={$id}");
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