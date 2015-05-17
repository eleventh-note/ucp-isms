<?php
	/*	Filename: ConfigFactory.inf.php
	*/
	class Config{
		const CONFIG_DIR = "_conf/";
		const ONLINE = 0; //variable that defines whether the returned
						 //database settings will for the online or the Offline
		
		
		public static function Load($Config){
			//suppress ERROR since this is a very private code
			error_reporting(0);	
			
			//check first if the data requested is mySQL Server
			if($Config="DBServer"){
				if(self::ONLINE == 0){ //Offline
					if(include_once(self::CONFIG_DIR . $Config . ".inc.php")){
						//Concat the namespace and the Class Name
						$Class = $Config . "\OfflineMySQLServer";
						return new $Class;
					} else {
						throw new Exception("Error: Unknown config. \n Error Code: 0001"); //0001 - MySQL Server Error
					}
				} else { //return Online
					if(include_once(self::CONFIG_DIR . $Config . ".inc.php")){
						//Concat the namespace and the Class Name
						$Class = $Config . "\OnlineMySQLServer";
						return new $Class;
					} else {
						throw new Exception("Error: Unknown config. \n Error Code: 0001"); //0001 - MySQL Server Error
					}
				}
			}
			
			//if config is not DBServer
			if(include_once($self::CONFIG_DIR . $Config . ".inc.php")){
				return new $Config;
			}
			
			//return ERROR reporting to normal
			
		}
	}
?>