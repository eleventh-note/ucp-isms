<?php
	/*-----------------------------------------------
	
		USERS
		-contains users and their privileges.
		 
	-------------------------------------------------*/
	
	// Provides CLASSES for verifying the user identity
	// and verifiying user privileges
	// ONLINE and OFFLINE
	
	//User Info Definition
	class UserInfo{
		public $id = null;
		public $username = null;
		public $password = null; //md5 encrypted
		public $privileges = array();
		public $employee_info =null;
		public $employee_id = null;
		public $override_enlistment=null;
		public $override_grading=null;
		public $college_domain=null;
		public $enabled=null;
		public $enlistment_toggling=null;
		public $grading_toggling=null;
		public $enlistment_sy_override=null;
		public $enlistment_sem_override=null;
		public $grading_sy_override=null;
		public $grading_sem_override=null;
		
		function __construct($id, $username, 
			$employee_id=null,
			$employee_info=null,
			$override_enlistment=null,
			$override_grading=null,
			$college_domain=null,
			$enabled=null,
			$enlistment_toggling=null,
			$grading_toggling=null,
			$enlistment_sy_override=null,
			$enlistment_sem_override=null,
			$grading_sy_override=null,
			$grading_sem_override=null
		){
			$this->id = $id;
			$this->username = $username;
			$this->employee_id = $employee_id;
			$this->employee_info = $employee_info;
			$this->override_enlistment = $override_enlistment;
			$this->override_grading = $override_grading;
			$this->college_domain = $college_domain;
			$this->enabled = $enabled;
			$this->enlistment_toggling = $enlistment_toggling;
			$this->grading_toggling = $grading_toggling;
			$this->enlistment_sy_override = $enlistment_sy_override;
			$this->enlistment_sem_override = $enlistment_sem_override;
			$this->grading_sy_override = $grading_sy_override;
			$this->grading_sem_override = $grading_sem_override;			
		}
	}
	
	$query = "SELECT Username, ";
				$query .= "OverrideEnlistment, OverrideGrading, ";
				$query .= "CollegeDomain, Enabled, ";
				$query .= "EnlistmentToggling, GradingToggling, ";
				$query .= "EnlistmentSYOverride, EnlistmentSemOverride, ";
				$query .= "GradingSYOverride, GradingSemOverride ";
				$query .= "FROM `adm-users` ";
				
	//User Privilege Definition
	class UserPrivilege{
		public $id = null;
		public $title = null;
		public $description = null;
		public $display = null;
		
		function __construct($id, $title, $description, $display){
			$this->id=$id;
			$this->title=$title;
			$this->description=$description;
			$this->display = $display;
		}
	}
	
	//Main User Class
	class User{
	
		public $error = '';
		public $insert_id = null;
		//pages
		public $num_pages = 0;
		
		private $conn = null;
		
		
		function __construct($conn = null){
			if($conn != null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}
		
		//checks whether the account is a legitimate account or not
		//returns null if no privileges and returns the UserInfo of
		//authenticated
		public function Authenticate($user, $pass){
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
				//do authentication here
				$conn = $this->conn;
				
				if($result = $conn->query("SELECT UserID, EmployeeID FROM `adm-users` WHERE Username='" . $user . "' AND Password='" . $pass . "' LIMIT 1")){
				
					if($result->num_rows > 0){
						//get the row
						$row = $result->fetch_assoc();
						//authenticated
						//get information
						$UserInfo = new UserInfo($row['UserID'], $user, $row['EmployeeID']);
						$UserInfo->password = $pass;
					
						//get user privileges
						$UserInfo->privileges = $this->GetPrivileges($row['UserID']);
						
						return $UserInfo;
						
					} else {
						return null;
					}
					
				} else {
					echo $conn->error;
				}
			}
		}
		
		//get user information
		public function GetUserInformations($user_id = null){
			$users = array();
			$this->error = array();
			
			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {
				//do authentication here
				$conn = $this->conn;
				
				$query = "SELECT `UserID`, `Username`, `Password`, `EmployeeID`, `OverrideEnlistment`, `OverrideGrading`, `CollegeDomain`, `Enabled`, `EnlistmentToggling`, `GradingToggling`, `EnlistmentSYOverride`, `EnlistmentSemOverride`, `GradingSYOverride`, `GradingSemOverride`, `Created`, `Modified` FROM `adm-users` WHERE 1 ";
				
				if($user_id != null){
					$query .= "AND UserID={$user_id} ";
				}

				$result = $conn->query($query);
				
				if($conn->error == ''){
					
					while($row = $result->fetch_assoc()){

						//get information
						$users[] = new UserInfo(
							$row['UserID'], 
							$row['Username'],
							null,
							$row['EmployeeID'],
							$row['OverrideEnlistment'],
							$row['OverrideGrading'],
							$row['CollegeDomain'],
							$row['Enabled'],
							$row['EnlistmentToggling'],
							$row['GradingToggling'],
							$row['EnlistmentSYOverride'],
							$row['EnlistmentSemOverride'],
							$row['GradingSYOverride'],
							$row['GradingSemOverride']
						);
						
						//get user privileges
						$users[sizeof($users)-1]->privileges = $this->GetPrivileges($row['UserID']);
											
					}
					
				} else {
					$this->error[] = $conn->error;
				}
			}

			return $users;
		}
		
		//returns the privilege of the user or all privileges
		function GetPrivileges($user_id=null){
			$privileges = null;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;
				
				if($user_id==null){
					//return all privileges
					// $query = "SELECT RightsID, Title, Description, Display FROM `adm-user_rights`";
					$query = "SELECT RightsID, Title, Description FROM `adm-user_rights`";
				} else {
					//return user specific privileges
					// $query = "SELECT a.Right AS RightsID, b.Title, b.Description, b.Display FROM `adm-user_assigned_rights` a ";
					// $query .= "LEFT JOIN `adm-user_rights` b ON a.Right=b.RightsID WHERE a.User=" . $user_id;
					// $query .= " ORDER BY b.Title";
					$query = "SELECT a.Right AS RightsID, b.Title, b.Description FROM `adm-user_assigned_rights` a ";
					$query .= "LEFT JOIN `adm-user_rights` b ON a.Right=b.RightsID WHERE a.User=" . $user_id;
					$query .= " ORDER BY b.Title";
				}

				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$privileges = array();

					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							//$privileges[$ctr] = new UserPrivilege($row['RightsID'], $row['Title'], $row['Description'], $row['Display']);
							$privileges[$ctr] = new UserPrivilege($row['RightsID'], $row['Title'], $row['Description'], null);
							$ctr++;
						}
					}
				}
			}
			
			return $privileges;
		}
		
		function GetPrivilege($id){
			$privileges = null;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;
				
					$query = "SELECT * FROM `adm-user_rights` a ";
					$query .= "WHERE a.RightsID=" . $id;
					$query .= " ORDER BY a.Title";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$privileges = $row;
						}
					}
				}
			}
			
			return $privileges;
		}
		
		//Function: Search for users using LIKE '% %'
		//Return: array of users found
		function Search($keyword, $page_num=null, $item_count=null){
			
			$users = null;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;
				
				$query = "SELECT UserID, Username, ";
				$query .= "OverrideEnlistment, OverrideGrading, ";
				$query .= "CollegeDomain, Enabled, ";
				$query .= "EnlistmentToggling, GradingToggling, ";
				$query .= "EnlistmentSYOverride, EnlistmentSemOverride, ";
				$query .= "GradingSYOverride, GradingSemOverride ";
				$query .= "FROM `adm-users` au ";
				$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=au.EmployeeID ";
				
				$query .= "WHERE au.Username LIKE '%{$keyword}%' ";
				$query .= "OR se.LastName LIKE '%{$keyword}%' ";
				$query .= "OR se.FirstName LIKE '%{$keyword}%' ";
			
				$query .= "ORDER BY Username ";
				
				if($page_num > 0 && $item_count > 0){
					$query .= "LIMIT " . (($page_num-1) * $item_count) . ", " . $item_count;
				} else {
					$query .= "LIMIT 0,30";
				}
				
				$result = $conn->query($query);
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$users = array();

					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$users[$ctr] = new UserInfo($row['UserID'], $row['Username']);
							$users[$ctr]->privileges = $this->GetPrivileges($row['UserID']);
							$ctr++;
						}
					}
				}
			}
			
			return $users;
			
		}
		
		//returns all the users except if specified
		function GetUsers($sort=null, $sortField=null, $user_id=null){
			
			$users = null;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;
				
				$query = "SELECT UserID, Username, au.EmployeeID, ";
				$query .= "OverrideEnlistment, OverrideGrading, ";
				$query .= "CollegeDomain, Enabled, ";
				$query .= "EnlistmentToggling, GradingToggling, ";
				$query .= "EnlistmentSYOverride, EnlistmentSemOverride, ";
				$query .= "GradingSYOverride, GradingSemOverride, ";
				$query .= "CONCAT(se.LastName, ', ', se.FirstName, ' ', se.MiddleName) AS `employeeName` ";
				$query .= "FROM `adm-users` au ";
				$query .= "LEFT JOIN `sch-employees` se ON se.EmployeeID=au.EmployeeID ";
				
				if($user_id!=null){
					$query .= "WHERE UserID={$user_id} ";
				}
				
				if($sortField==null){
					$query .= "ORDER BY Username ";
				}
				
				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$users = array();

					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$superadmin = false;
							$data = array();
							$data['employeeName'] = $row['employeeName'];
							$users[$ctr] = new UserInfo($row['UserID'], $row['Username'], $row['EmployeeID'], $data);
							$users[$ctr]->privileges = $this->GetPrivileges($row['UserID']);
							foreach($users[$ctr]->privileges as $privilege){
								if($privilege->id == 1){
									$superadmin = true;
									break;
								}
							}
							if($superadmin==false){
								$ctr++;
							} else {
								unset($users[$ctr]);
							}
						}
					}
				}
			}
			return $users;
		}
		
		function Add($username, 
					 $password, 
					 $cpassword,
					 $employee_id=0, 
					 $override_enlistment=1,
					 $override_grading=1,
					 $college_domain=0,
					 $enabled=1,
					 $enlistment_toggling=1,
					 $grading_toggling=1,
					 $enlistment_sy_override=0,
					 $enlistment_sem_override=0,
					 $grading_sy_override=0,
					 $grading_sem_override=0
		){
			//clean input
			$username = addslashes(strip_tags($username));
			$password = $password;
			$cpassword = $cpassword;
			$result = false;
			
			$this->error = array();
			
			if($username == ""){
				$this->error[] = "Username cannot be blank!";
			}
			
			if($password == ""){
				$this->error[] = "Password cannot be blank!";
			}
			
			if($password != $cpassword){
				$this->error[] = "Password does not match!";
			}
			if($employee_id==-1){
				$this->error[] = "Employee not selected!";
			} else {
				if(!$this->IsEmployeeAvailable($employee_id)){
					$this->error[] = "Employee already had a username assigned!";
				}
			}
			
			
			if(sizeof($this->error) > 0){
			} else {

				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {
				
					$conn = $this->conn;
					
					$query = "INSERT INTO `adm-users`(Username, Password, ";
					$query .= "EmployeeID, OverrideEnlistment, OverrideGrading, ";
					$query .= "CollegeDomain, Enabled, EnlistmentToggling, ";
					$query .= "GradingToggling, EnlistmentSYOverride, ";
					$query .= "EnlistmentSemOverride, GradingSYOverride, ";
					$query .= "GradingSemOverride ";
					$query .= ") ";
					$query .= "VALUES('";
					$query .= $username;
					$query .= "','";
					$query .= $password;
					$query .= "',{$employee_id},{$override_enlistment},{$override_grading},";
					$query .= "{$college_domain},{$enabled},{$enlistment_toggling}, {$grading_toggling},";
					$query .= "{$enlistment_sy_override},{$enlistment_sem_override},{$grading_sy_override},";
					$query .= "{$grading_sem_override}";
					$query .= ")";
					
					$conn->query($query);
					
					if($conn->insert_id > 0){
						$this->insert_id = $conn->insert_id;
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[] = "Error adding user. Duplicate found!";
						}					
					}
				}
			}
			return $result;
		}
		
		private function IsEmployeeAvailable($employeeId){
			$isAvailable = false;
			$conn = $this->conn;
			
			$query = "SELECT Username FROM `adm-users` WHERE EmployeeID={0} ";
			$query = str_replace("{0}", $employeeId, $query);
			
			$result = $conn->query($query);
			
			if($conn->error == ''){
				if($result->num_rows == 0){
					$isAvailable = true;
				}
			} else {
				$this->error[] = "Error checking IsEmployeeAvailable(). " . $conn->error;
			}
			
			return $isAvailable;
		}
		
		function Edit(
					 $user_id,
					 $username, 
					 $password, 
					 $cpassword,
					 $employee_id=0, 
					 $override_enlistment=1,
					 $override_grading=1,
					 $college_domain=0,
					 $enabled=1,
					 $enlistment_toggling=1,
					 $grading_toggling=1,
					 $enlistment_sy_override=0,
					 $enlistment_sem_override=0,
					 $grading_sy_override=0,
					 $grading_sem_override=0
		){
			//clean input
			$username = addslashes(strip_tags($username));
			$password = $password;
			$cpassword = $cpassword;
			$result = false;
			
			$this->error = array();
			
			if($username == ""){
				$this->error[] = "Username cannot be blank!";
			}
			
			if($password == ""){
				$this->error[] = "Password cannot be blank!";
			}
			
			if($password != $cpassword){
				$this->error[] = "Password does not match!";
			}
			if($employee_id==-1){
				$this->error[] = "Employee not selected!";
			}
			
			$password = md5($password);
			if(sizeof($this->error) > 0){
			} else {
				if($this->conn == null){
					$error = "No defined connection.";
					return null;
				} else {
					$conn = $this->conn;
					
					$query = "UPDATE `adm-users` SET Username='{$username}', Password='{$password}', ";
					$query .= "EmployeeID={$employee_id}, OverrideEnlistment={$override_enlistment},";
					$query .= "OverrideGrading={$override_grading}, CollegeDomain={$college_domain},";
					$query .= "Enabled={$enabled}, EnlistmentToggling={$enlistment_toggling}, ";
					$query .= "GradingToggling={$grading_toggling}, ";
					$query .= "EnlistmentSYOverride={$enlistment_sy_override}, ";
					$query .= "EnlistmentSemOverride={$enlistment_sem_override}, ";
					$query .= "GradingSYOverride={$grading_sy_override}, ";
					$query .= "GradingSemOverride={$grading_sem_override}, Modified=NOW() ";
					$query .= "WHERE ";
					$query .= "UserID=";
					$query .= $user_id;
					
					$conn->query($query);
					
					if($conn->affected_rows > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[] = "Error adding user. Duplicate found!";
						} else {
							$this->error[] = "No change has been done. If you are trying to change your password, please make sure that your old Password is correct.";
						}
					}
				}
			}
			return $result;
		}
		
		function ChangePassword(
					 $user_id,
					 $password, 
					 $cpassword,
					 $opassword
		){
		
			$this->error = array();
			
			if($password == '' || $cpassword=='' || $opassword==''){
				$this->error[] = 'Password inputs incomplete!';
			}
			
			//clean input
			$password = md5($password);
			$cpassword = md5($cpassword);
			$opassword = md5($opassword);

			$result = false;
			
			if($password != $cpassword){
				$this->error[] = 'Password does not match!';
			}
			
			if(sizeof($this->error) > 0){
			}elseif($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "UPDATE `adm-users` SET Password='{$password}', Modified=NOW() ";
				$query .= "WHERE ";
				$query .= "Password='{$opassword}' ";
				$query .= "AND UserID=";
				$query .= $user_id;
				
				$conn->query($query);

				if($conn->affected_rows > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[] = "Error adding user. Duplicate found!";
					} else {
						$this->error[] = "No change has been done. If you are trying to change your password, please make sure that your old Password is correct.";
					}
				}
			}
			
			return $result;
		}
		
		//Delete User in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function Delete($user_id){
			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `adm-users` ";
				$query .= "WHERE UserID=";
				$query .= $user_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting room type. Information in use.";
					} 			
				}
			}
			
			return $result;
		}
		
		//Grant Privilege to User
		function GrantPrivilege($user_id, $privilege_id){
			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "INSERT INTO `adm-user_assigned_rights`(`Right`,`User`) ";
				$query .= "VALUES($privilege_id,$user_id)";
				
				$conn->query($query);
				
				if($conn->insert_id > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error adding privilege. Duplicate found!";
					} else {
						$this->error = $conn->error;
					}
				}
			}
			
			return $result;
		}
		//Remove Privilege fromhtt User
		function RemovePrivilege($user_id, $privilege_id){
			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `adm-user_assigned_rights` ";
				$query .= "WHERE `Right`=$privilege_id AND `User`=$user_id";
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					$this->error = $conn->error;
				}
			}
			
			return $result;
		}
		
		//Remove Privilege fromhtt User
		function RemovePrivileges($user_id){
			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `adm-user_assigned_rights` ";
				$query .= "WHERE `User`=$user_id";
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					$this->error = $conn->error;
				}
			}
			
			return $result;
		}
		
	}
	
?>