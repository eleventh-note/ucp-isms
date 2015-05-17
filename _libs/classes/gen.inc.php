<?php
	/*-----------------------------------------------
	
		GENERAL DEFINITIONS
		-contains genders, marital_status, country_listing,
		 citizenship, region_listing, relationships, religion
		 
	-------------------------------------------------*/
	
	//Gender Information
	class Gender{
		public $gender_id;
		public $description;
		
		function __construct($_gender_id, $_description){
			$this->gender_id 	= $_gender_id;
			$this->description 	= $_description;
		}
	}

	//Marital Status
	class MaritalStatus{
		public $status_id;
		public $description;
		
		function __construct($_status_id, $_description){
			$this->status_id 	= $_status_id;
			$this->description 	= $_description;
		}
	}
	
	//Relationship Information
	class Relationship{
		public $relationship_id;
		public $description;
		
		function __construct($_relationship_id, $_description){
			$this->relationship_id 	= $_relationship_id;
			$this->description 	= $_description;
		}
	}
	
	//Country Information
	class Country{
		public $country_id;
		public $code;
		public $description;
		
		function __construct($_country_id, $_code, $_description){
			$this->country_id 	= $_country_id;
			$this->code			= $_code;
			$this->description 	= $_description;
		}
	}
	
	//Citizenship Information
	class Citizenship{
		public $citizenship_id;
		public $description;
		
		function __construct($_citizenship_id, $_description){
			$this->citizenship_id 	= $_citizenship_id;
			$this->description 	= $_description;
		}
	}
	
	//Religion Information
	class Religion{
		public $religion_id;
		public $description;
		
		function __construct($_religion_id, $_description){
			$this->religion_id 	= $_religion_id;
			$this->description 	= $_description;
		}
	}
	
	//Region Information
	class Region{
		public $region_id;
		public $code;
		public $description;
		
		function __construct($_region_id, $_code, $_description){
			$this->region_id 	= $_region_id;
			$this->code			= $_code;
			$this->description 	= $_description;
		}
		
	}
		
	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class GeneralInformationManager{
	
		public $error = array();
		
		private $conn = null;
		
		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error[sizeof($this->error)] = "No defined connection.";
			}
		}
		
		/*--------------------------------------------------------
		
			GENDER [ SELECT ]
		
		---------------------------------------------------------*/
		
		//Will always return null for errors else an array
		function GetGenders($id = null){
			$genders = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT GenderID, Description FROM `gen-genders` ";
				
				if($id != null){
					$query .= "WHERE GenderID={$id} ";
				}
				
				$query .= "ORDER BY GenderID ";
						
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$genders = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$genders[$ctr] = new Gender($row['GenderID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $genders;
		}
		
		//Will always return null for errors else an array
		function GetGendersByKey($id = null){
			$genders = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT GenderID, Description FROM `gen-genders` ";
				
				if($id != null){
					$query .= "WHERE GenderID={$id} ";
				}
				
				$query .= "ORDER BY GenderID ";
						
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$genders = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$genders[$row['GenderID']] = new Gender($row['GenderID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $genders;
		}
		
		/*--------------------------------------------------------
		
			MARITAL STATUS [ SELECT ]
		
		---------------------------------------------------------*/
		
		//Will always return null for errors else an array
		function GetMaritalStatus($id=null){
			$marital_status = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT StatusID, Description FROM `gen-marital_status` ";
				
				if($id != null){
					$query .= "WHERE StatusID={$id}";
				}
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$marital_status = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$marital_status[$ctr] = new MaritalStatus($row['StatusID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $marital_status;
		}
		
		//Will always return null for errors else an array
		function GetMaritalStatusesByKey($id=null){
			$marital_status = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT StatusID, Description FROM `gen-marital_status` ";
				
				if($id != null){
					$query .= "WHERE StatusID={$id}";
				}
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$marital_status = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$marital_status[$row['StatusID']] = new MaritalStatus($row['StatusID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $marital_status;
		}
		
		/*--------------------------------------------------------
		
			RELIGION [ SELECT | INSERT | UPDATE | DELETE ]
		
		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetReligions($religion_id=null){
			$religions = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT ReligionID, Description FROM `gen-religion` ";
				
				if($religion_id != null){
					$query .= "WHERE ";
					$query .= "ReligionID=";
					$query .= $religion_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$religions = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$religions[$ctr] = new Religion($row['ReligionID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $religions;
		}
		
		//Will always return null for errors else an array
		function GetReligionsByKey($religion_id=null){
			$religions = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT ReligionID, Description FROM `gen-religion` ";
				
				if($religion_id != null){
					$query .= "WHERE ";
					$query .= "ReligionID=";
					$query .= $religion_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$religions = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$religions[$row['ReligionID']] = new Religion($row['ReligionID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $religions;
		}
		
		//Add Religion to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddReligion($description){
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			
			$result = false;
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Religion cannot be blank.";
			}
			
			if(sizeof($this->error) > 0){
			
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "INSERT INTO `gen-religion`(Description) ";
				$query .= "VALUES('";
				$query .= $description;
				$query .= "')";
				
				$conn->query($query);
				
				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding religion. Duplicate found!";
					}					
				}
			}
			
			return $result;
			
		}
		
		//Edit Religion in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditReligion($religion_id, $description){
		
			//clean input
			$description = addslashes(strip_tags($description));
			
			$result = false;
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Religion cannot be blank.";
			}
			
			if(sizeof($this->error) > 0){
			
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "UPDATE `gen-religion` SET Description='";
				$query .= $description;
				$query .= "' ";
				$query .= "WHERE ReligionID=";
				$query .= $religion_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error editing religion. Duplicate found!";
					} 	else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
		
		//Delete Religion in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteReligion($religion_id){

			$result = false;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `gen-religion` ";
				$query .= "WHERE ReligionID=";
				$query .= $religion_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting religion. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
		
		/*--------------------------------------------------------
		
			RELIGION [ SELECT | INSERT | UPDATE | DELETE ]
		
		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetRelationships($relationship_id=null){
			$relationships = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT RelationshipID, Description FROM `gen-relationships` ";
				
				if($relationship_id != null){
					$query .= "WHERE ";
					$query .= "RelationshipID=";
					$query .= $relationship_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$relationships = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$relationships[$ctr] = new Relationship($row['RelationshipID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $relationships;
		}
		
		//Add Relationship to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddRelationship($description){
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			
			$result = false;
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Description of relationship cannot be blank.";
			}
			
			if(sizeof($this->error) > 0){
			
			} elseif ($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "INSERT INTO `gen-relationships`(Description) ";
				$query .= "VALUES('";
				$query .= $description;
				$query .= "')";
				
				$conn->query($query);
				
				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding relationship. Duplicate found!";
					}					
				}
			}
			
			return $result;
			
		}
		
		//Edit Relationship in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditRelationship($relationship_id, $description){
		
			//clean input
			$description = addslashes(strip_tags($description));
			
			$result = false;
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Description of relationship cannot be blank.";
			}
			
			if(sizeof($this->error) > 0){
			
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "UPDATE `gen-relationships` SET Description='";
				$query .= $description;
				$query .= "' ";
				$query .= "WHERE RelationshipID=";
				$query .= $relationship_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error editing relationship. Duplicate found!";
					} 	else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
		
		//Delete Relationship in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteRelationship($relationship_id){

			$result = false;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `gen-relationships` ";
				$query .= "WHERE RelationshipID=";
				$query .= $relationship_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting relationship. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
		
		/*--------------------------------------------------------
		
			CITIZENSHIP [ SELECT | INSERT | UPDATE | DELETE ]
		
		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetCitizenships($citizenship_id=null){
			$citizenships = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT CitizenshipID, Description FROM `gen-citizenship` ";
				
				if($citizenship_id != null){
					$query .= "WHERE ";
					$query .= "CitizenshipID=";
					$query .= $citizenship_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$citizenships = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$citizenships[$ctr] = new Citizenship($row['CitizenshipID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $citizenships;
		}
		
		//Will always return null for errors else an array
		function GetCitizenshipsByKey($citizenship_id=null){
			$citizenships = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT CitizenshipID, Description FROM `gen-citizenship` ";
				
				if($citizenship_id != null){
					$query .= "WHERE ";
					$query .= "CitizenshipID=";
					$query .= $citizenship_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$citizenships = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$citizenships[$row['CitizenshipID']] = new Citizenship($row['CitizenshipID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $citizenships;
		}
		
		//Add Citizenship to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddCitizenship($description){
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			
			$result = false;
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Citizenship cannot be blank.";
			}
			
			if(sizeof($this->error) > 0){
			} elseif ($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "INSERT INTO `gen-citizenship`(Description) ";
				$query .= "VALUES('";
				$query .= $description;
				$query .= "')";
				
				$conn->query($query);
				
				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding citizenship. Duplicate found!";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
		
		//Edit Citizenship in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditCitizenship($citizenship_id, $description){
		
			//clean input
			$description = addslashes(strip_tags($description));
			
			$result = false;
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Citizenship cannot be blank.";
			}
			
			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "UPDATE `gen-citizenship` SET Description='";
				$query .= $description;
				$query .= "' ";
				$query .= "WHERE CitizenshipID=";
				$query .= $citizenship_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error editing citizenship. Duplicate found!";
					} 	else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
		
		//Delete Citizenship in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteCitizenship($citizenship_id){

			$result = false;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `gen-citizenship` ";
				$query .= "WHERE CitizenshipID=";
				$query .= $citizenship_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting citizenship. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
		
		/*--------------------------------------------------------
		
			COUNTRY [ SELECT | INSERT | UPDATE | DELETE ]
		
		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetCountries($country_id=null){
			$countries  = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT CountryID, Code, Description FROM `gen-country_listing` ";
				
				if($country_id != null){
					$query .= "WHERE ";
					$query .= "CountryID=";
					$query .= $country_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$countries  = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$countries [$ctr] = new Country($row['CountryID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $countries ;
		}
	
		//Will always return null for errors else an array
		function GetCountriesByKey($country_id=null){
			$countries  = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT CountryID, Code, Description FROM `gen-country_listing` ";
				
				if($country_id != null){
					$query .= "WHERE ";
					$query .= "CountryID=";
					$query .= $country_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$countries  = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$countries [$row['CountryID']] = new Country($row['CountryID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $countries ;
		}
		
		//Add Country to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddCountry($code, $description){
		
			//clean input
			$description = trim(ucwords(addslashes(strip_tags($description))));
			$code = trim(strtoupper(addslashes(strip_tags($code))));
			
			if($description == ''){
				$this->error[sizeof($this->error)] = "Country Name cannot be blank.";
			}
			
			if($code == ''){
				$this->error[sizeof($this->error)] = "Country Acronym cannot be blank.";
			}
			
			$result = false;
			
			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {
				
					$conn = $this->conn;
					
					$query = "INSERT INTO `gen-country_listing`(Code, Description) ";
					$query .= "VALUES('{$code}','";
					$query .= $description;
					$query .= "')";
					
					$conn->query($query);
					
					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error adding country. Duplicate found!";
						} else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}
			return $result;
			
		}
	
		//Edit Country in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function UpdateCountry($country_id, $code, $description){
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));
			
			if($description == ''){
				$this->error[sizeof($this->error)] = "Country Name cannot be blank.";
			}
			
			if($code == ''){
				$this->error[sizeof($this->error)] = "Country Acronym cannot be blank.";
			}
			
			$result = false;
			
			if(sizeof($this->error) == 0){
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {
				
					$conn = $this->conn;
					
					$query = "UPDATE `gen-country_listing` SET Code='";
					$query .= $code;
					$query .= "', Description='";
					$query .= $description;
					$query .= "' ";
					$query .= "WHERE CountryID=";
					$query .= $country_id;
					
					$conn->query($query);
					
					if($conn->affected_rows > 0){

						$result = true;
						
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[sizeof($this->error)] = "Error updating country details. Duplicate found!";
						} 	else {
							$this->error[sizeof($this->error)] = $conn->error;
						}
					}
				}
			}
			return $result;
			
		}

		//Delete Country in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteCountry($country_id){

			$result = false;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `gen-country_listing` ";
				$query .= "WHERE CountryID=";
				$query .= $country_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting country. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
		
		/*--------------------------------------------------------
		
			/For philippines only?/
			REGIONS [ SELECT | INSERT | UPDATE | DELETE ]
		
		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetRegions($region_id=null){
			$regions  = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT RegionID, Code, Description FROM `gen-region_listing` ";
				$query .= "WHERE 1 ";
					
				if($region_id != null){

					$query .= "AND RegionID=";
					$query .= $region_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$regions  = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$regions [$ctr] = new Region($row['RegionID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $regions ;
		}
		
		//Will always return null for errors else an array
		function GetRegionsByKey($region_id=null){
			$regions  = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT RegionID, Code, Description FROM `gen-region_listing` ";
				$query .= "WHERE 1 ";
					
				if($region_id != null){

					$query .= "AND RegionID=";
					$query .= $region_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[sizeof($this->error)] = $conn->error;
				} else {
					$regions  = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$regions [$row['RegionID']] = new Region($row['RegionID'], $row['Code'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $regions ;
		}
		
		//Add Region to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddRegion($code, $description){
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));
						
			if($description == ''){
				$this->error[sizeof($this->error)] = "Region name cannot be blank.";
			}
			
			$result = false;
			
			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "INSERT INTO `gen-region_listing`(Code, Description) ";
				$query .= "VALUES('{$code}','";
				$query .= $description;
				$query .= "')";

				$conn->query($query);
				
				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error adding region. Duplicate found!";
					} elseif(strpos($conn->error, "Cannot add or update a child row: a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Country not found. ";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
	
		//Edit Region in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function UpdateRegion($region_id, $code, $description){
		
			//clean input
			$description = addslashes(strip_tags($description));
			$code = addslashes(strip_tags($code));
			
			if($description == ''){
				$this->error[sizeof($this->error)] = "Region name cannot be blank.";
			}
			
			$result = false;
			
			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "UPDATE `gen-region_listing` SET Code='";
				$query .= $code;
				$query .= "', Description='";
				$query .= $description;
				$query .= "' ";
				$query .= "WHERE RegionID=";
				$query .= $region_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error editing region details. Duplicate found!";
					} elseif(strpos($conn->error, "Cannot add or update a child row: a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Country not found. ";
					} 	else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}

		//Delete Region in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteRegion($region_id){

			$result = false;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `gen-region_listing` ";
				$query .= "WHERE RegionID=";
				$query .= $region_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error[sizeof($this->error)] = "Error deleting region. Information in use.";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}
		
	}
?>