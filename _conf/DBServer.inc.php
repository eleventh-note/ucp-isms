<?php
	namespace DBServer{
		interface IServer{
			function GetMySQLConnection();
		}

		//abstract to used for the Connection Details
		abstract class MySQLServerAbstract implements IServer{
			private $SERVER;
			private $DATABASE;
			private $USER;
			private $PASSWORD;

			abstract function __construct();
		}

		//Connection used locally
		class OfflineMySQLServer extends MySQLServerAbstract{
			function __construct(){
				$this->SERVER = "localhost";
				$this->DATABASE = "ucp-isms";
				$this->USER = "";
				$this->PASSWORD = "";
			}

			function GetMySQLConnection(){
				$conn = new \mysqli($this->SERVER, $this->USER, $this->PASSWORD, $this->DATABASE);

				if($conn){
					return $conn;
				} else {
					throw new Exception("Unable to connect to Local Server. Please check connection details.");
				}
			}
		}
		//Connection available in the online server
		class OnlineMySQLServer extends MySQLServerAbstract{
			function __construct(){
				$this->SERVER = "localhost";
				$this->DATABASE = "ucp-isms";
				$this->USER = "";
				$this->PASSWORD = "";
			}

			function GetMySQLConnection(){
				$conn = new \mysqli($this->SERVER, $this->USER, $this->PASSWORD, $this->DATABASE);

				if($conn){
					return $conn;
				} else {
					throw new Exception("Unable to connect to Online Server. Please check connection details.");
				}
			}
		}

	}
?>
