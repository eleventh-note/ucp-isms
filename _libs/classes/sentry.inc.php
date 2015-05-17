<?php
	// Provides CLASSES that will be handling all security concerns
	// REQUIRES the user.inc.php file
	
	//Main Sentry/Security Class
	
	class PagePrivileges{
		private $index = 0;
		private $privileges = Array(); //only privileges allowed
		
		//add privilege to list
		function AddPrivilege($privilege){
			$this->privileges[$this->index] = $privilege;
			$this->index++;
		}
		
		//check if the array contains the Contains one of the user privileges
		function Contains($UserPrivileges){
			if($UserPrivileges!=null){
				foreach($UserPrivileges as $UserPrivilege){
					foreach($this->privileges as $privilege){
						//echo "Checking if " . $privilege . " = " . $UserPrivilege->title . "<br/>";
						if($privilege == $UserPrivilege->title){
							return true;
						}
					}
				}
			}
			
			return false;
		}
	}
	
	class Sentry{
	
		private $UserInfo;
		private $UserPrivileges;
		
		function __construct($UserInfo){
			$this->UserInfo = $UserInfo;
			$this->UserPrivileges = $UserInfo->privileges;
		}
		
		function CheckPrivilege($PagePrivileges,$redirectURL){
			
			if($PagePrivileges!=null){
				if($PagePrivileges->Contains($this->UserPrivileges) == false){
					header("Location:{$redirectURL}?error=3");
				}
			}
		}
		
		//for displaying error or success
		static function ShowStatus($type='success', $list){
			$result = null;
			
			if($type == 'success'){
				$success = "<div class=\"status\">";
					$success .= "<ul class=\"success\">";
							if(is_array($list) == false){
								$success .= "<li>";
									$success .= $list;
								$success .= "</li>";
							} else {
								foreach($list as $item){
									$success .= "<li>";
										$success .= $item;
									$success .= "</li>";
								}
							}
					$success .= "</ul>";
				$success .= "</div>";
				$result = $success;
			} elseif($type =='error'){
				if(sizeof($list) > 0){
					$error = "<div class=\"status\">";
						$error .= "<ul class=\"error\">";
							foreach($list as $item){
								$error .= "<li>";
									$error .= $item;
								$error .= "</li>";
							}
						$error .= "</ul>";
					$error .= "</div>";
					$result = $error;
				}
			} else {
				echo "Unknown type.";
			}
			
			return $result;
		}	
	}
	
?>