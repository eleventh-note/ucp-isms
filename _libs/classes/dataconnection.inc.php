<?php
	// Provides the data for data connections
	//	ONLINE and OFFLINE
	class ISMSConnection{

		const OFFLINE = 0;
		const ONLINE = 1;

		public $error;

		private $offline_server = "localhost";
		private $offline_database = "ucp-isms3";
		private $offline_username = "isms";
		private $offline_password = "1sms";

		private $online_server = "localhost";
		private $online_database = "ucp-isms3";
		private $online_username = "isms";
		private $online_password = "1sms";

		private $ConnectionType = 0;

		//set the ConnectionType
		function __construct($type=0){
			$this->ConnectionType = $type;
		}

		//Returns the connection
		public function GetConnection(){

			if($this->ConnectionType == self::OFFLINE){
				$conn = new mysqli($this->offline_server,
									$this->offline_username,
									$this->offline_password,
									$this->offline_database);

				if(mysqli_connect_error()){
					$this->error = mysqli_connect_error();
					return null;
				}

				return $conn;

			} else {
				$conn = new mysqli($this->online_server,
									$this->online_username,
									$this->online_password,
									$this->online_database);

				if(mysqli_connect_error()){
					$this->error = mysqli_connect_error();
					return null;
				}

				return $conn;
			}
		}

	}

?>
