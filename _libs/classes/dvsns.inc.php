<?php
	/*-----------------------------------------------
	
		DIVISIONS
		-contains division details.
		
		DEPENDENCIES:
			1) emp.inc.php - Employee Class
	-------------------------------------------------*/
	
	//Division Admins
	class DivisionAdmin{
		public $admin_id;
		public $position; //class Position
		public $employee; //class Employee
		public $division; //class Division
		public $created; //DATETIME
		public $modified; //DATETIME
	}
	
	//Divisions
	class Division{
		public $division_id;
		public $description;
		
		function __construct($_division_id, $_description){ 
			$this->division_id		= $_division_id;
			$this->description	= $_description;
		}				
	}
	
	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class DivisionsManager{
	
		public $error = '';
		
		private $conn = null;
		
		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}
		
		/*--------------------------------------------------------
		
			DIVISIONS [SELECT | INSERT | UPDATE | DELETE]
		
		---------------------------------------------------------*/
		
		//Function: Search for users using LIKE '% %'
		//Return: array of Divisions found
		function Search($keyword, $page_num=null, $item_count=null){
			$divisions = null;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT DivisionID, Description FROM `sch-divisions` ";
							
				$query .= "WHERE Description LIKE '%{$keyword}%'";
				$query .= "ORDER BY Description ";
				
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
					$divisions = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$divisions[$ctr] = new Division($row['DivisionID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $divisions;
		}
		
		//Will always return null for errors else an array
		function GetDivisions($division_id=null){
			$divisions = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT DivisionID, Description FROM `sch-divisions` ";
				
				if($division_id != null){
					$query .= "WHERE ";
					$query .= "DivisionID=";
					$query .= $division_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$divisions = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$divisions[$ctr] = new Division($row['DivisionID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $divisions;
		}
		
		//Add Division to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddDivision($description){
		
			//clean input
			$description = addslashes(strip_tags($description));
			
			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "INSERT INTO `sch-divisions`(Description) ";
				$query .= "VALUES('";
				$query .= $description;
				$query .= "')";
				
				$conn->query($query);
				
				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error adding division. Duplicate found!";
					}					
				}
			}
			
			return $result;
			
		}

		//Edit Division in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditDivision($division_id, $description){
		
			//clean input
			$description = addslashes(strip_tags($description));
			
			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "UPDATE `sch-divisions` SET Description='";
				$query .= $description;
				$query .= "' ";
				$query .= "WHERE DivisionID=";
				$query .= $division_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error editing division. Duplicate found!";
					} 					
				}
			}
			
			return $result;
			
		}
		
		//Delete Division in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteDivision($division_id){

			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `sch-divisions` ";
				$query .= "WHERE DivisionID=";
				$query .= $division_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting division. Information in use.";
					} 					
				}
			}
			
			return $result;
			
		}
	
		/*--------------------------------------------------------
		
			DIVISION ADMINS [SELECT | INSERT | UPDATE | DELETE]
		
		---------------------------------------------------------*/
		
		//Returns the list of Admins on the specified division
		function GetDivisionAdmins($division_id){
			$admins = null;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT AdminID, Position, Employee, Division, Created, Modified ";
				$query .= "FROM `sch-division_admins` ";
				$query .= "LEFT JOIN `sch-employees` emp ON emp.EmployeeID=Employee ";
				$query .= "WHERE Division={$division_id} ";
				
				$query .= "ORDER BY emp.LastName";
				
				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$admins = array();
					$ctr = 0;
					
					if($result->num_rows > 0){

						//create the other handlers
						$emp_hdr = new EmployeeManager($conn);
						
						while($row = $result->fetch_assoc()){
							$positions = $emp_hdr->GetPositions($row['Position']);
							$employees = $emp_hdr->GetEmployees($row['Employee']);
							$divisions = $this->GetDivisions($row['Division']);
							
							$admins[$ctr] = new DivisionAdmin;
							$admins[$ctr]->admin_id = $row['AdminID'];
							$admins[$ctr]->position = $positions[0];
							$admins[$ctr]->employee = $employees[0];
							$admins[$ctr]->division = $divisions[0];
							$admins[$ctr]->created = $row['Created'];
							$admins[$ctr]->modified = $row['Modified'];
							$ctr++;
						}
					}
				}
			}
			return $admins;
		}
		
		function AddDivisionAdmin($employee_id, $position_id, $division_id){
				
			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "INSERT INTO `sch-division_admins`(Employee, Position, Division, Created, Modified) ";
				$query .= "VALUES(";
				$query .= $employee_id;
				$query .= ", ";
				$query .= $position_id;
				$query .= ", ";
				$query .= $division_id;
				$query .= ",NOW(),NOW())";
				
				$conn->query($query);
				
				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error adding division admin. Duplicate found!";
					}					
				}
			}
			
			return $result;
			
		}

		function EditDivisionAdmin($admin_id, $employee_id,$position_id, $division_id){
				
			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "UPDATE `sch-division_admins` ";
				$query .= "SET Employee={$employee_id}, ";
				$query .= "Position={$position_id}, ";
				$query .= "Division={$division_id}, ";
				$query .= "Modified=NOW() ";
				$query .= "WHERE AdminID={$admin_id}";
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error editing division admin. Duplicate found!";
					} 					
				}
			}
			
			return $result;
			
		}
	
		function DeleteDivisionAdmin($admin_id){
		
			$result = false;
			
			if($this->conn == null){
				$error = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `sch-division_admins` ";
				$query .= "WHERE AdminID=";
				$query .= $admin_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting division admin. Information in use.";
					} 					
				}
			}
			
			return $result;
		}
		
	}

?>