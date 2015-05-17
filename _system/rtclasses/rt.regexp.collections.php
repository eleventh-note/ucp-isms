<?php
	/*	+-------------------------------------------------------------------------------------------------------+
		|  Name			: RTFormCreator					   														|
		|  Author		: Algefmarc Anthony L. Almocera    														|
		|  Date			: October 22, 2011				   														|
		|  Description	: This class will allow you to create forms using only the available classMethods.		| 
		|				  This is created, especially, for controlling form inputs passed from $_POST and $_GET.|
		+-------------------------------------------------------------------------------------------------------+
	*/ 
	class RT_RegExp{
		public static function CheckName($value){
			$result = false;
			
			$pattern = '/^([A-Za-z\',-\.ñ]\s?)+$/';
			
			if(preg_match($pattern,$value) > 0){
				$result = true;
			}
			
			return $result;
		}
		
		
	}	
	
	

?>