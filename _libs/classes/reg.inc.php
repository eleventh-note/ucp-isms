<?php
	/*-----------------------------------------------
	
		REGULAR EXPRESSIONS
		-
		Date Started: 2012-02-01
		 
	-------------------------------------------------*/
	
	class RtRegExp{
		public $match;
		public $formatted;
		
		function CheckTime($time){
			$result = preg_match("/^(1[0-2]{1}|0?[1-9]{1}):(0?[0-9]{1}|[1-5]{1}[0-9]{1}|60)\s?(AM|PM)$/", $time, $this->match);
			if($result > 0){
				$this->formatted = $this->match[1] . ":" . $this->match[2] . " " . $this->match[3];
			}
			return $result;
		}
		
		function CheckName($name){
			$result = preg_match("/^[^\d!@#$%&*()_+|\/-=\\;:]*$/", $name, $this->match);
			return $result;		
		}
		
		function CheckEmail($email){
			$result = preg_match("/^([a-z0-9]+[\-_\.]?[a-z0-9]+)+@([a-z0-9]+[\-_\.]?[a-z0-9]+)+\.([a-z0-9]{2,3})+$/", $email, $this->match);
			return $result;
		}
	
		function CheckTin($tin){	
			$result = preg_match("/^([0-9]{3})-?([0-9]{3})-?([0-9]{3})-?([0-9]{3})$/", $tin, $this->match);
			if($result > 0){
				$this->formatted = $this->match[1] . "-" . $this->match[2] . "-" . $this->match[3] . "-" . $this->match[4];
			}
			return $result;
		}
		
		function CheckSss($sss){
		$result = preg_match("/^([0-9]{2})-?([0-9]{7})-?([0-9]{1})$/", $sss, $this->match);
			if($result > 0){
				$this->formatted=$this->match[1] . "-" . $this->match[2] . "-" . $this->match[3];
			}
			return $result;
		}
		
		function CheckPhilhealth($philhealth){
		}
		
		function CheckPagibig($pagibig){
		}
		
	}
?>