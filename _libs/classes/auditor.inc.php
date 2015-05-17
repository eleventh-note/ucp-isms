<?php
	// Provides the functions for audit trailing
	class AuditTrail{
	
		public $error;
		private $conn;
		
		function __construct($conn = null){
			if($conn != null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}
		
		function GetAll(){
			$list = array();
			$this->error = array();
			
			if($this->conn == null){
				$this->error[] = "No defined connection.";
			} else {
				$conn = $this->conn;
				
				$query  = "SELECT ";
				$query .= "au.Username, at.action, at.tableName, at.newValue, ";
				$query .= "at.dateCreated ";
				$query .= "FROM `audit-trail` at ";
				$query .= "LEFT JOIN `adm-users` au ON au.UserID = at.userId ";
				$query .= "ORDER BY dateCreated DESC ";
				
				$result = $conn->query($query);
				if($conn->error == ""){
					while($row = $result->fetch_assoc()){
						$list[] = $row;
					}
				}
				
			}
			
			return $list;
		}
		
		function GetByDate($dateFrom, $dateTo){
		
		}
		
		function Search($username){
		}
		
		function Add($newRecord){
			$result = false;
			
			if($this->conn == null){
				$this->error = "No defined connection.";
			} else {
				$conn = $this->conn;
				
				$query  = "INSERT INTO `audit-trail`(userId, action, tableName, newValue, dateCreated) ";
				$query .= "VALUES('{0}', '{1}', '{2}', '{3}', NOW()) ";
				
				$query = str_replace("{0}", $newRecord->userId, $query);
				$query = str_replace("{1}", $newRecord->action, $query);
				$query = str_replace("{2}", $newRecord->tableName, $query);
				$query = str_replace("{3}", $newRecord->newValue, $query);
				
				$conn->query($query);
				
				if($conn->insert_id > -1){
					$result = true;
				} else {
					$this->error = $conn->error;
				}
			}
			
			return $result;
		}
		
	}
	
?>