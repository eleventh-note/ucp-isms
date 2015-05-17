<?php
	class Statistics{
	
		public $error = array();
		private $conn = null;
		public $enlistment_id = null;
		
		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}
		
		function getCurrentSemesterEnrollees(){	
			
			$conn = $this->conn;
			
			$query  = "SELECT COUNT(*) AS `total` FROM `enl-enlistment_details` ed ";
			$query .= "LEFT JOIN `sch-school_years` ssy ON ssy.SchoolYearID=ed.SY ";
			$query .= "LEFT JOIN `sch-semesters` ss ON ss.SemesterID=ed.Semester ";
			$query .= "WHERE ";
			$query .= "ssy.Active=1 AND ss.Active=1 ";
			
			$result = $conn->query($query);
			
			$row = $result->fetch_assoc();
			
			return $row['total'];
			
		}
		
		function getCurrentSyEnrollees(){	
			
			$conn = $this->conn;
			
			$query  = "SELECT COUNT(*) AS `total` FROM `enl-enlistment_details` ed ";
			$query .= "LEFT JOIN `sch-school_years` ssy ON ssy.SchoolYearID=ed.SY ";
			$query .= "WHERE ";
			$query .= "ssy.Active=1 ";
			
			$result = $conn->query($query);
			
			$row = $result->fetch_assoc();
			
			return $row['total'];
			
		}
		
		function getTotalEnrollees(){	
			
			$conn = $this->conn;
			
			$query  = "SELECT COUNT(*) AS `total` FROM `enl-enlistment_details` ed ";
			$result = $conn->query($query);
			
			$row = $result->fetch_assoc();
			
			return $row['total'];
			
		}
		
		function getTotalPaymentsReceivedRIB(){
		
			$conn = $this->conn;
			
			$query  = "SELECT SUM(fp.Price) as `total` FROM `fin-payments` fp ";
			$query .= "LEFT JOIN `fin-fees` ff ON ff.FeeID=fp.Fee ";
			$query .= "LEFT JOIN `fin-fee_types` ft ON ft.TypeID=ff.FeeType ";
			$query .= "WHERE FeeType != 3 AND Deleted=0";
			$result = $conn->query($query);
			
			$row = $result->fetch_assoc();
			
			return $row['total'];
			
		}

		function getTotalPaymentsReceivedRIBNON(){
		
			$conn = $this->conn;
			
			$query  = "SELECT SUM(fp.Price) as `total` FROM `fin-payments` fp ";
			$query .= "LEFT JOIN `fin-fees` ff ON ff.FeeID=fp.Fee ";
			$query .= "LEFT JOIN `fin-fee_types` ft ON ft.TypeID=ff.FeeType ";
			$query .= "WHERE FeeType = 3 AND Deleted=0 ";
			$result = $conn->query($query);
			
			$row = $result->fetch_assoc();
			
			return $row['total'];
			
		}
		
		function getTotalBillings(){
		
			$conn = $this->conn;
			
			$query  = "SELECT SUM(Price) as `total` FROM `fin-billings` WHERE 1 ";
			$result = $conn->query($query);
			
			$row = $result->fetch_assoc();
			
			return $row['total'];
			
		}
		
	}
?>