<?php
	/*-----------------------------------------------

		FINANCE

	-------------------------------------------------*/


class FinOtherFees{

  private $conn = null;

  function __construct($conn = null){
    if($conn !== null){
      $this->conn = $conn;
    } else {
      $this->error = "No defined connection.";
    }
  }

  //Will always return null for errors else an array
  function GetAll(){
    $fees = array();

    if($this->conn == null){
      $this->error[] = "No defined connection.";
      return null;
    } else {

      $conn = $this->conn;

      $query = "SELECT `FeeId`, `Name`, `Price` FROM `fin-other-fees` ORDER BY `Name`";
      $result = $conn->query($query);

      //check for errors first
      if($conn->error <> ""){
        $this->error = $conn->error;
      } else {
        $discounts = array();
        $ctr = 0;
        if($result->num_rows > 0){
          $fees = array();
          while($row = $result->fetch_assoc()){
            $fees[] = $row;
          }
        }
      }
    }

    return $fees;
  }

  //Add Scholarship
  //-->returns true on success else false
  //-->if FALSE, sets self::error
  function AddScholarship($description, $price, $percentage){

    //clean input
    $description = (string) trim(addslashes(strip_tags($description)));
    $price = (float) $price;
    $percentage = (float) $percentage;

    $result = false;

    //check for errors

    if($description == ""){
      $this->error[] = "Description cannot be blank.";
    }

    if(sizeof($this->error) > 0){
    }elseif($this->conn == null){
      $this->error[] = "No defined connection.";
      return null;
    } else {

      $conn = $this->conn;

      $query = "INSERT INTO `fin-discounts`(`Description`, `Price`, `Percentage`, `Type`, `DateCreated`, `DateModified`) ";
      $query .= "VALUES ('{$description}',{$price},{$percentage},1,NOW(), NOW())";

      $conn->query($query);

      if($conn->insert_id > 0){
        $result = true;
      } else {
        if(strpos($conn->error, "Duplicate entry") !== false){
          $this->error[] = "Error adding scholarship. Duplicate found!";
        } else {
          $this->error[] = $conn->error;
        }
      }
    }

    return $result;

  }
}

?>
