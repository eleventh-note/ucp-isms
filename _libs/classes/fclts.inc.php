<?php
	/*-----------------------------------------------
	
		FACILITIES
		-contains rooms and buildings with their 
		 defined status and type.
		 
	-------------------------------------------------*/
	
	//Lab or Classroom
	class RoomTypes{
		public $type_id;
		public $description;
		
		function __construct($_type_id, $_description){ 
			$this->type_id		= $_type_id;
			$this->description	= $_description;
		}				
	}
	
	//Active or Inactive
	class RoomStatus{
		public $status_id;
		public $description;
		
		function __construct($_status_id, $_description){
			$this->status_id	= $_status_id;
			$this->description	= $_description;
		}
	}
	
	//Contains information about the schools buildings
	class Building{
		public $building_id;
		public $description;
		public $code;
		public $storeys;
		
		function __construct($_building_id, $_description, $_code, $_storeys){
			$this->building_id	= $_building_id;
			$this->description	= $_description;
			$this->code			= $_code;
			$this->storeys		= $_storeys;
		}
	}
	
	//Contains information about the rooms of the school
	//-->information regarding the buildings, rooms statuses and room types are needed
	class Rooms{
		public $building;
		public $status; //Room Status
		public $type; //Room Type
		
		public $room_id;
		public $code;
		public $description;
		public $floor;
		public $floor_area;
		public $seating_capacity;
		function __construct($_building, $_status, $_type, //all classes
							 $_room_id, $_code, $_description, 
							 $_floor, $_floor_area, $seating_capacity){
			$this->building = $_building;
			$this->status 	= $_status;
			$this->type		= $_type;
			
			$this->room_id	= $_room_id;
			$this->code		= $_code;
			$this->description = $_description;
			$this->floor	= $_floor;
			$this->floor_area = $_floor_area;
			$this->seating_capacity = $seating_capacity;
		}
	}
	
	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class FacilitiesManager{
	
		public $error = array();
		
		private $conn = null;
		
		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}
		
		/*--------------------------------------------------------
		
			ROOMS TYPES [ SELECT | INSERT | UPDATE | DELETE ]
		
		---------------------------------------------------------*/
		
		//Will always return null for errors else an array
		function GetRoomTypes($type_id = null){
			$types = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT TypeID, Description FROM `bldg-room_types` ";
				
				if($type_id != null){
					$query .= "WHERE ";
					$query .= "TypeID=";
					$query .= $type_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$types = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$types[$ctr] = new RoomTypes($row['TypeID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $types;
		}
		
		//Add Room Type to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddRoomType($description){
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Room Type cannot be blank.";
			}
			
			$result = false;
			
			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "INSERT INTO `bldg-room_types`(Description) ";
				$query .= "VALUES('";
				$query .= $description;
				$query .= "')";
				
				$conn->query($query);
				
				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error adding room type. Duplicate found!";
					}					
				}
			}
			
			return $result;
			
		}

		//Edit Room Type in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditRoomType($type_id, $description){
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Room Type cannot be blank.";
			}
			
			$result = false;
			
			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "UPDATE `bldg-room_types` SET Description='";
				$query .= $description;
				$query .= "' ";
				$query .= "WHERE TypeID=";
				$query .= $type_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error editing room type. Duplicate found!";
					} 					
				}
			}
			
			return $result;
			
		}
		
		//Delete Room Type in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteRoomType($type_id){

			$result = false;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `bldg-room_types` ";
				$query .= "WHERE TypeID=";
				$query .= $type_id;
				
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
		
		/*--------------------------------------------------------
		
			ROOMS STATUSES [ SELECT | INSERT | UPDATE | DELETE ]
		
		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetRoomStatuses($status_id = null){
			$statuses = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT StatusID, Description FROM `bldg-room_status` ";
				
				if($status_id != null){
					$query .= "WHERE ";
					$query .= "StatusID=";
					$query .= $status_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$statuses = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$statuses[$ctr] = new RoomStatus($row['StatusID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			
			return $statuses;
		}
		
		//Add Room Status to database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddRoomStatus($description){
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Room Status cannot be blank.";
			}
			
			$result = false;
			
			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "INSERT INTO `bldg-room_status`(Description) ";
				$query .= "VALUES('";
				$query .= $description;
				$query .= "')";
				
				$conn->query($query);
				
				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error adding room status. Duplicate found!";
					}					
				}
			}
			
			return $result;
			
		}
		
		//Edit Room Status in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditRoomStatus($status_id, $description){
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Room Status cannot be blank.";
			}
			
			$result = false;
			
			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "UPDATE `bldg-room_status` SET Description='";
				$query .= $description;
				$query .= "' ";
				$query .= "WHERE StatusID=";
				$query .= $status_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error = "Error editing room status. Duplicate found!";
					} 					
				}
			}
			
			return $result;
			
		}
		
		//Delete Room Status in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteRoomStatus($status_id){

			$result = false;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `bldg-room_status` ";
				$query .= "WHERE StatusID=";
				$query .= $status_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting room status. Information in use.";
					} 					
				}
			}
			
			return $result;
			
		}
		
		/*--------------------------------------------------------
		
			BUILDINGS [ SELECT | INSERT | UPDATE | DELETE ]
		
		---------------------------------------------------------*/
		
		//Will always return null for errors else an array
		function GetBuildings($building_id = null){
			$buildings = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT BuildingID, Description, Code, Storeys FROM `bldg-buildings` ";
				
				if($building_id != null){
					$query .= "WHERE ";
					$query .= "BuildingID=";
					$query .= $building_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Description ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$buildings = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$buildings[$ctr] = new Building($row['BuildingID'], $row['Description'],
															$row['Code'], $row['Storeys']);
							$ctr++;
						}
					}
				}
			}
			
			return $buildings;
		}
		
		//Will always return null for errors else an array
		function GetBuildingsByCode($building_id = null){
			$buildings = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT BuildingID, Description, Code, Storeys FROM `bldg-buildings` ";
				
				if($building_id != null){
					$query .= "WHERE ";
					$query .= "BuildingID=";
					$query .= $building_id;
					$query .= " ";
				}
				
				$query .= "ORDER BY Code ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$buildings = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$buildings[$ctr] = new Building($row['BuildingID'], $row['Description'],
															$row['Code'], $row['Storeys']);
							$ctr++;
						}
					}
				}
			}
			
			return $buildings;
		}
		
		//Add Buildings to the database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddBuilding($description, $code, $floors){
		
			$result = false;
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));
			$floors = (int) $floors;
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Building Description cannot be blank.";
			} 
			
			if($code == ""){
				$this->error[sizeof($this->error)] = "Building Code cannot be blank.";
			} 
			
			if($floors <= 0){
				$this->error[sizeof($this->error)] = "Invalid input for floors.";
			}
			
			if(sizeof($this->error) == 0){		
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {
				
					$conn = $this->conn;
					
					$query = "INSERT INTO `bldg-buildings`(Description, Code, Storeys) ";
					$query .= "VALUES('";
					$query .= $description;
					$query .= "','";
					$query .= $code;
					$query .= "',";
					$query .= $floors;
					$query .= ")";
					
					$conn->query($query);
					
					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error = "Error adding new building. Duplicate found!";
						}					
					}
				}				
			}
			
			return $result;
			
		}
		
		//Edit Building in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditBuilding($building_id, $description, $code, $floors){
		
			///clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));
			$floors = (int) $floors;
			
			if($description == ""){
				$this->error[sizeof($this->error)] = "Building Description cannot be blank.";
			} 
			
			if($code == ""){
				$this->error[sizeof($this->error)] = "Building Code cannot be blank.";
			} 
			
			if($floors <= 0){
				$this->error[sizeof($this->error)] = "Invalid input for floors.";
			}
			
			$result = false;
			
			if(sizeof($this->error) > 0){
			} elseif($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
			
				$query = "UPDATE `bldg-buildings` SET Description='";
				$query .= $description;
				$query .= "', Code='";
				$query .= $code;
				$query .= "', Storeys=";
				$query .= $floors;
				$query .= ", Modified=NOW() ";
				$query .= " WHERE BuildingID=";
				$query .= $building_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} 	else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[sizeof($this->error)] = "Error saving building info. Duplicate found!";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}
			
			return $result;
			
		}

		//Delete Building in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteBuilding($building_id){

			$result = false;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `bldg-buildings` ";
				$query .= "WHERE BuildingID=";
				$query .= $building_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting building info. Information in use.";
					} 					
				}
			}
			
			return $result;
			
		}
		
		/*--------------------------------------------------------
		
			ROOMS [ SELECT | INSERT | UPDATE | DELETE ]
		
		---------------------------------------------------------*/
		
		//Will always return null for errors else an array
		function GetRooms($room_id = null){
			$rooms = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT ";
				$query .= "br.RoomID, br.Code, br.Description, br.Floor, br.FloorArea, br.SeatingCapacity, ";
				$query .= "brs.StatusID, brs.Description AS StatusDescription, ";
				$query .= "brt.TypeID, brt.Description AS TypeDescription, ";
				$query .= "bb.BuildingID, bb.Description AS BuildingDescription, bb.Code AS BuildingCode, ";
				$query .= "bb.Storeys ";
				$query .= "FROM `bldg-rooms` br ";
				$query .= "LEFT JOIN `bldg-room_status` brs ON br.Status=brs.StatusID ";
				$query .= "LEFT JOIN `bldg-room_types` brt ON br.RoomType=brt.TypeID ";
				$query .= "LEFT JOIN `bldg-buildings` bb ON br.Building=bb.BuildingID ";
				
				if($room_id != null){
					$query .= "WHERE ";
					$query .= "RoomID=";
					$query .= $room_id;
				}
				
				$query .= "ORDER BY br.Description ";
								
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$rooms = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$rooms[$ctr] = new Rooms(
								new Building(
									$row['BuildingID'],
									$row['BuildingDescription'],
									$row['BuildingCode'],
									$row['Storeys']
								),
								new RoomStatus(
									$row['StatusID'],
									$row['StatusDescription']
								),
								new RoomTypes(
									$row['TypeID'],
									$row['TypeDescription']
								),
								$row['RoomID'],
								$row['Code'],
								$row['Description'],
								$row['Floor'],
								$row['FloorArea'],
								$row['SeatingCapacity']
							);
							$ctr++;
						}
					}
				}
			}
			
			return $rooms;
		}
		
		//Will always return null for errors else an array
		function GetRoomsByKey($room_id = null){
			$rooms = null;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "SELECT ";
				$query .= "br.RoomID, br.Code, br.Description, br.Floor, br.FloorArea, br.SeatingCapacity, ";
				$query .= "brs.StatusID, brs.Description AS StatusDescription, ";
				$query .= "brt.TypeID, brt.Description AS TypeDescription, ";
				$query .= "bb.BuildingID, bb.Description AS BuildingDescription, bb.Code AS BuildingCode, ";
				$query .= "bb.Storeys ";
				$query .= "FROM `bldg-rooms` br ";
				$query .= "LEFT JOIN `bldg-room_status` brs ON br.Status=brs.StatusID ";
				$query .= "LEFT JOIN `bldg-room_types` brt ON br.RoomType=brt.TypeID ";
				$query .= "LEFT JOIN `bldg-buildings` bb ON br.Building=bb.BuildingID ";
				
				if($room_id != null){
					$query .= "WHERE ";
					$query .= "RoomID=";
					$query .= $room_id;
				}
				
				$query .= "ORDER BY br.Code ";
				
				$result = $conn->query($query);
				
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$rooms = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$rooms[$row['RoomID']] = new Rooms(
								new Building(
									$row['BuildingID'],
									$row['BuildingDescription'],
									$row['BuildingCode'],
									$row['Storeys']
								),
								new RoomStatus(
									$row['StatusID'],
									$row['StatusDescription']
								),
								new RoomTypes(
									$row['TypeID'],
									$row['TypeDescription']
								),
								$row['RoomID'],
								$row['Code'],
								$row['Description'],
								$row['Floor'],
								$row['FloorArea'],
								$row['SeatingCapacity']
							);
							$ctr++;
						}
					}
				}
			}
			
			return $rooms;
		}
		
		//Add Rooms to the database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddRoom($description, $code, $floor, $floor_area, $seating_capacity, $building, $type, $status){
		
			$result = false;
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));
			$floor = (int) addslashes(strip_tags($floor));
			$floor_area = (int) addslashes(strip_tags($floor_area));
			$seating_capacity = (int) addslashes(strip_tags($seating_capacity));
			$building = (int) addslashes(strip_tags($building));
			$status = (int) addslashes(strip_tags($status));
			$type = (int) addslashes(strip_tags($type));
			
			if($description == ''){
				$this->error[sizeof($this->error)] = "Room Description cannot be blank.";
			}
			
			if($code == ''){
				$this->error[sizeof($this->error)] = "Room Code cannot be blank.";
			}
			
			if($type <= 0){
				$this->error[sizeof($this->error)] = "Room Type not selected.";
			}
			
			if($status <= 0){
				$this->error[sizeof($this->error)] = "Room Status not selected.";
			}
								
			if($floor <= 0){
				$this->error[sizeof($this->error)] = "Invalid Floor number.";
			}
							
			if($building <= 0){
				$this->error[sizeof($this->error)] = "Building not selected.";
			}
			
			if($seating_capacity <= 0){
				$this->error[sizeof($this->error)] = "Invalid Seating capacity.";
			}
			
			if($floor_area < 0){
				$this->error[sizeof($this->error)] = "Invalid Floor area.";
			}
			
			if(sizeof($this->error) > 0){
			} else {		
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {
				
					$conn = $this->conn;
					
					$query = "INSERT INTO `bldg-rooms`(Code, Description, Floor, SeatingCapacity, FloorArea, ";
					$query .= "Building, RoomType, Status) ";
					$query .= "VALUES('";
					$query .= $code; $query .= "','";
					$query .= $description; $query .= "',";
					$query .= $floor; $query .= ",";
					$query .= $seating_capacity; $query .= ",";
					$query .= $floor_area; $query .= ",";
					$query .= $building; $query .= ",";
					$query .= $type; $query .= ",";
					$query .= $status; $query .= ")";
					
					$conn->query($query);
					
					if($conn->insert_id > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error[] = "Error adding room. Duplicate found!";
						} else {
							$this->error[] = $conn->error;
						}
					}
				}				
			}
			
			return $result;
			
		}
		
		//Edit Rooms to the database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditRoom($room_id, $description, $code, $floor, $floor_area, $seating_capacity, $building, $type, $status){
		
			$result = false;
		
			//clean input
			$description = ucwords(addslashes(strip_tags($description)));
			$code = strtoupper(addslashes(strip_tags($code)));
			$floor = (int) addslashes(strip_tags($floor));
			$floor_area = (int) addslashes(strip_tags($floor_area));
			$seating_capacity = (int) addslashes(strip_tags($seating_capacity));
			$building = (int) addslashes(strip_tags($building));
			$status = (int) addslashes(strip_tags($status));
			$type = (int) addslashes(strip_tags($type));
			
			if($description == ''){
				$this->error[sizeof($this->error)] = "Room Description cannot be blank.";
			}
			
			if($code == ''){
				$this->error[sizeof($this->error)] = "Room Code cannot be blank.";
			}
			
			if($type <= 0){
				$this->error[sizeof($this->error)] = "Room Type not selected.";
			}
			
			if($status <= 0){
				$this->error[sizeof($this->error)] = "Room Status not selected.";
			}
								
			if($floor <= 0){
				$this->error[sizeof($this->error)] = "Invalid Floor number.";
			}
							
			if($building <= 0){
				$this->error[sizeof($this->error)] = "Building not selected.";
			}
			
			if($seating_capacity <= 0){
				$this->error[sizeof($this->error)] = "Invalid Seating capacity.";
			}
			
			if($floor_area < 0){
				$this->error[sizeof($this->error)] = "Invalid Floor area.";
			}
			
			if(sizeof($this->error) > 0){
			} else {
			
				if($this->conn == null){
					$this->error[sizeof($this->error)] = "No defined connection.";
					return null;
				} else {
				
					$conn = $this->conn;
					
					$query = "UPDATE `bldg-rooms` SET Code='";
					$query .= $code;
					$query .= "', ";
					$query .= "Description='";
					$query .= $description;
					$query .= "',";
					$query .= "Floor=";
					$query .= $floor;
					$query .= ", SeatingCapacity=";
					$query .= $seating_capacity;
					$query .= ", FloorArea=";
					$query .= $floor_area;
					$query .= ", ";
					$query .= "Building="; 
					$query .= $building;
					$query .= ",";
					$query .= "RoomType=";
					$query .= $type;
					$query .= ",";
					$query .= "Status=";
					$query .= $status;
					$query .= ",";
					$query .= "Modified=NOW() ";
					$query .= "WHERE RoomID=";
					$query .= $room_id;
					
					$conn->query($query);
					
					if($conn->affected_rows > 0){
						$result = true;
					} else {
						if(strpos($conn->error, "Duplicate entry") !== false){
							$this->error = "Error saving room. Duplicate found!";
						}					
					}
				}				
			}
			
			return $result;
			
		}
		
		//Delete Room in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteRoom($room_id){

			$result = false;
			
			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {
			
				$conn = $this->conn;
				
				$query = "DELETE FROM `bldg-rooms` ";
				$query .= "WHERE RoomID=";
				$query .= $room_id;
				
				$conn->query($query);
				
				if($conn->affected_rows > 0){

					$result = true;
					
				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting room information. Information in use.";
					} 					
				}
			}
			
			return $result;
			
		}
	}
?>