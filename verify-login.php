<?php
	/* #-------------------------------------------------
	   #
	   #	Description:	Template for 00 Default Layout
	   #	Author:		Algefmarc A. L. Almocera
	   #	Date Started:	December 02, 2011
	   #	Last Modified:	December 02, 2011
	   #
	   #-------------------------------------------------
	*/

	//Set no caching
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	header("Cache-Control: no-store, no-cache, must-revalidate"); 
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

//::START OF 'SESSION DECLARATION'
	//open session here if needed (e.g: session_start())
	session_start();
//::END OF 'SESSION DECLARATION'

//::START OF 'CONFIGURATION'
	require_once("_system/_config/sys_config.php");
	//configurations can be overriden here
	include_once(CLASSLIST . "dataconnection.inc.php");
	include_once(CLASSLIST . "user.inc.php");
	
	//check if a post is found
	//ELSE
	//redirect to Login Location
	if($_POST){
		//create ISMS connection to database
		$ISMS = new ISMSConnection(CONNECTION_TYPE);
		$conn = $ISMS->GetConnection();
		
		//check if complete first
		if(!empty($_POST['username']) && !empty($_POST['password'])){
			$user = new User($conn);
			
			//if error
			if($user->error){
				echo $user->error;
				//No defined connection error
				header("Location: index.php?error=2"); 
				exit();
			}
			
			//if User Class successfully created\
			$username=$_POST['username'];
			$encrypted_password = md5($_POST['password']);
			
			if($user_info = $user->Authenticate($username,$encrypted_password)){
				if($user_info != null){
					$_SESSION['UserInfo'] = serialize($user_info);
					header("Location: isms.php");
				} else {
					//invalid username/password
					header("Location: index.php?error=1");
					exit();
				}
			} else {
					//invalid username/password
					header("Location: index.php?error=1");
					exit();
			}
		} else {
			//invalid username/password
			header("Location: index.php?error=1");
		}
				
	} else {
		header("Location:index.php");
		exit();
	}
	
//::END OF 'CONFIGURATION'
