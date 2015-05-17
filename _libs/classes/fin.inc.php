<?php
	/*-----------------------------------------------

		FINANCE

	-------------------------------------------------*/


	class Fee{
		public $fee_id;
		public $description;
		public $price;
		public $fee_type;
		public $is_virtual;

		function __construct($fee_id, $description, $price, $fee_type, $is_virtual){
			$this->fee_id = $fee_id;
			$this->description = $description;
			$this->price = $price;
			$this->fee_type = $fee_type;
			$this->is_virtual = $is_virtual;
		}
	}

	class FeeType{
		public $type_id;
		public $description;

		function __construct($type_id, $description){
			$this->type_id = $type_id;
			$this->description = $description;
		}
	}

	class PaymentType{
		public $type_id;
		public $description;

		function __construct($type_id, $description){
			$this->type_id = $type_id;
			$this->description = $description;
		}
	}

	class Discount{
		public $discount_id;
		public $description;
		public $price;
		public $percentage;
		public $type;

		function __construct($discount_id, $description, $price, $percentage, $type){
			$this->discount_id = $discount_id;
			$this->description = $description;
			$this->price = $price;
			$this->percentage= $percentage;
			$this->type = $type;

		}
	}

	//[ SELECT | INSERT | UPDATE | DELETE ] Functions for the classes above
	class FinanceManager{

		public $error = array();
		public $error_count = 0;

		private $conn = null;

		function __construct($conn = null){
			if($conn !== null){
				$this->conn = $conn;
			} else {
				$this->error = "No defined connection.";
			}
		}


		/*--------------------------------------------------------

			GET PAYMENTS [ SELECT ]

		---------------------------------------------------------*/

		//KEY is PaymentID
		//$type is 0 and 1....0 is RIB and 1 is RIB-NON
		function GetReceipt($transaction_no, $student_id, $tid=0){
			$payments = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT TransactionNumber, CONCAT(spr.LastName , ', ' , spr.FirstName , ' ' , spr.MiddleName) AS Name, DateCreated, p.Price AS Amount, CONCAT('[', cou.Code, '] ') ";
				$query .= " AS Course, ff.Description AS Transaction, spr.MailingAddress ";
				$query .= "FROM `fin-payments` p ";
				$query .= "LEFT JOIN `spr-personal_data` spr ON spr.StudentID=p.StudentID ";
				$query .= "LEFT JOIN `spr-current_academic_background` cab ON cab.StudentID=spr.StudentID ";
				$query .= "LEFT JOIN `sch-course_list` cou ON cou.CourseID=cab.Course ";
				$query .= "LEFT JOIN `fin-fees` ff ON ff.FeeID=p.Fee ";
				$query .= "WHERE TransactionNumber={$transaction_no} AND spr.StudentID={$student_id} ";
				if($tid==0){ //RIB
					$query .= "AND ff.FeeID=3 ";
				} else { // RIB-NON
					$query .= "AND NOT ff.FeeID=3 ";
				}
				// $query .= "AND cab.CurrentAcademicBackgroundID=( ";
				// $query .= "SELECT CurrentAcademicBackgroundID FROM `spr-current_academic_background` ";
				// $query .= "WHERE EntrySemester=1 AND EntrySY=1 AND StudentID=185 ";
				// $query .= "ORDER BY CurrentAcademicBackgroundID DESC LIMIT 0,1) ";

				$result = $conn->query($query);
				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$payments = array();
					$ctr = 0;
					if($result->num_rows > 0){
						$to_replace = array("&Ntilde;","&ntilde;", "&amp;Ntilde;", "&amp;ntilde;");
						$replace_with = array("Ñ", "ñ", "Ñ", "ñ");
						$payments['particulars'] = array();
						while($row = $result->fetch_assoc()){
							$payments['receipt_no'] = str_pad($row['TransactionNumber'],5,"0", STR_PAD_LEFT);
							$payments['date'] = $row['DateCreated'];
							$payments['received_from'] = str_replace($to_replace, $replace_with, $row['Name']);
							$payments['course'] = $row['Course'];
							$payments['amount'] = $row['Amount'];
							if(sizeof($payments['particulars']) > 0){
								if($payments['particulars'][sizeof($payments['particulars'])-1] != $row['Transaction']){
									$payments['particulars'][] = $row['Transaction'];
								}
							} else {
								$payments['particulars'][] = $row['Transaction'];
							}
							$payments['mailing_address'] = $row['MailingAddress'];
						}
					}
				}
			}

			return $payments;
		}

		//KEY is PaymentID
		function GetNextTransactionNumber($type){
			$transaction_no = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				switch($type){
					case 'bir':
						$query = "SELECT MAX(TransactionNumber) AS Total FROM `fin-payments` fin ";
						$query .= "LEFT JOIN `fin-fees` ff ON ff.FeeID=fin.Fee ";
						$query .= "WHERE NOT ff.FeeType=3 ";
						break;
					case 'non-bir':
						$query = "SELECT MAX(TransactionNumber) AS Total FROM `fin-payments` fin ";
						$query .= "LEFT JOIN `fin-fees` ff ON ff.FeeID=fin.Fee ";
						$query .= "WHERE ff.FeeType=3 ";
						break;
				}

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$payments = array();
					$ctr = 0;
					if($result->num_rows > 0){
						$data = $result->fetch_assoc();
						$transaction_no = (int) $data['Total'] + 1;
					} else {
						$transactino_no = 1;
					}
				}
			}

			return $transaction_no;
		}

		//KEY is PaymentID
		function GetPaymentsByKey($student_id=null, $date_from=null, $date_to=null){
			$payments = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT PaymentID, p.StudentID, CONCAT(spr.LastName, ', ', spr.FirstName, ' ', spr.MiddleName) AS StudentName, p.Price, ";
				$query .= "f.Description AS FeeType, DateCreated AS DatePaid, Deleted, DateModified, TransactionNumber ";
				$query .= "FROM `fin-payments` p ";
				$query .= "LEFT JOIN `spr-personal_data` spr ON spr.StudentID=p.StudentID ";
				$query .= "LEFT JOIN `fin-fees` f ON f.FeeID=p.Fee ";
				$query .= "LEFT JOIN `fin-fee_types` ft ON ft.TypeID=f.FeeType ";
				$query .= "WHERE NOT ft.TypeID=3 AND Deleted=0 ";

				if($date_from != null && $date_to != null){
					$query .= "AND DateCreated BETWEEN '{$date_from}' AND '{$date_to}' ";
				}

				if($student_id!=null){
					$query .= "AND p.StudentID={$student_id} ";
				}

				$query .= "ORDER BY DateCreated DESC ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$payments = array();
					$ctr = 0;
					if($result->num_rows > 0){
						$to_replace = array("&Ntilde;","&ntilde;", "&amp;Ntilde;", "&amp;ntilde;");
						$replace_with = array("Ñ", "ñ", "Ñ", "ñ");
						while($row = $result->fetch_assoc()){
							$student_name = str_replace($to_replace, $replace_with,$row['StudentName']);
							$payments[$row['PaymentID']] = array(
								'payment_id' => $row['PaymentID'],
								'student_id' => $row['StudentID'],
								'student_name' => $student_name,
								'price' => $row['Price'],
								'transaction' => $row['FeeType'],
								'date_paid' => $row['DatePaid'],
								'deleted' => $row['Deleted'],
								'date_modified' => $row['DateModified'],
								'transaction_no' => $row['TransactionNumber']
							);
						}
					}
				}
			}

			return $payments;
		}

		//KEY is PaymentID
		function GetPaymentsByKey2($student_id=null, $date_from=null, $date_to=null){
			$payments = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT PaymentID, p.StudentID, CONCAT(spr.LastName, ', ', spr.FirstName, ' ', spr.MiddleName) AS StudentName, p.Price, ";
				$query .= "f.Description AS FeeType, DateCreated AS DatePaid, Deleted, DateModified, TransactionNumber ";
				$query .= "FROM `fin-payments` p ";
				$query .= "LEFT JOIN `spr-personal_data` spr ON spr.StudentID=p.StudentID ";
				$query .= "LEFT JOIN `fin-fees` f ON f.FeeID=p.Fee ";
				$query .= "LEFT JOIN `fin-fee_types` ft ON ft.TypeID=f.FeeType ";
				$query .= "WHERE ft.TypeID=3 AND Deleted=0 AND f.IsDeleted=0 ";

				if($date_from != null && $date_to != null){
					$query .= "AND DateCreated BETWEEN '{$date_from}' AND '{$date_to}' ";
				}

				if($student_id!=null){
					$query .= "AND p.StudentID={$student_id} ";
				}

				$query .= "ORDER BY DateCreated DESC ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$payments = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$payments[$row['PaymentID']] = array(
								'payment_id' => $row['PaymentID'],
								'student_id' => $row['StudentID'],
								'student_name' => $row['StudentName'],
								'price' => $row['Price'],
								'transaction' => $row['FeeType'],
								'date_paid' => $row['DatePaid'],
								'deleted' => $row['Deleted'],
								'date_modified' => $row['DateModified'],
								'transaction_no' => $row['TransactionNumber']
							);
						}
					}
				}
			}

			return $payments;
		}

		/*--------------------------------------------------------

			PAYMENT TYPES [ SELECT ]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetPaymentTypesByKey(){
			$types = null;

			if($this->conn == null){
				$this->error[sizeof($this->error)] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `TypeID`, `Description` FROM `fin-payment_types` WHERE 1 ";
				$query .= "ORDER BY Description";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$types = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$types[$row['TypeID']] = new PaymentType($row['TypeID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $types;
		}

		/*--------------------------------------------------------

			Billings [ SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		function UpdateBill($id, $price, $sy=null, $sem=null){

			//clean input
			$price = (float) $price;
			$result = false;

			//check for errors
			if(sizeof($this->error) > 0){
			}elseif($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `fin-billings` SET ";
				$query .= "`Price`={$price}, ";
				$query .= "`DateModified`=NOW(), ";
				$query .= "Sem={$sem}, ";
				$query .= "SY={$sy} ";
				$query .= "WHERE EnlistmentID={$id}";

				$conn->query($query);

				if($conn->affected_rows > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[] = "Error updating bills. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Add New Billing
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddBill($student_id, $fee, $price, $enlistment_id, $sy=null, $sem=null){

			//clean input
			$student_id = (int) $student_id;
			$fee = (int) $fee;
			$price = (float) $price;

			$result = false;

			//check for errors

			if($price <= 0){ $this->error[] = "Price cannot be less than or equal to zero."; }

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `fin-billings`(`StudentID`, `Fee`, `Price`, `DateCreated`, DateModified,EnlistmentID, Sem, SY) ";
				$query .= "VALUES ({$student_id},{$fee},{$price},NOW(), NOW(), $enlistment_id, {$sy}, {$sem})";

				$conn->query($query);

				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[] = "Error adding bill. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			SCHOLARSHIPS [ SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetScholarshipsByKey(){
			$discounts = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `DiscountID`, `Description`, `Price`, `Percentage`, `Type`, `DateCreated`, `DateModified` FROM `fin-discounts` WHERE Type=1 ";

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$discounts = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$discounts[$row['DiscountID']] = new Discount($row['DiscountID'], $row['Description'], $row['Price'], $row['Percentage'], $row['Type']);
							$ctr++;
						}
					}
				}
			}

			return $discounts;
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

		//Edit Scholarship
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditScholarship($id, $description, $price, $percentage){

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

				$query = "UPDATE `fin-discounts` SET ";
				$query .= "`Description`='{$description}', ";
				$query .= "`Price`={$price}, ";
				$query .= "`Percentage`={$percentage}, ";
				$query .= "`DateModified`=NOW() ";
				$query .= "WHERE DiscountID={$id}";

				$conn->query($query);

				if($conn->affected_rows > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[] = "Error updating scholarship. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Delete Scholarship from database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteScholarship($scholarship_id){

			$result = false;
			$this->error = array();

			if($this->IsScholarshipUsed($scholarship_id)){
				$this->error[] = "Scholarship is in use.";
			}

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				if(sizeof($this->error) == 0){
					$conn = $this->conn;

					$query = "DELETE FROM `fin-discounts` ";
					$query .= "WHERE DiscountID=";
					$query .= $scholarship_id;

					$conn->query($query);

					if($conn->affected_rows > 0){

						$result = true;

					} else {
						if(strpos($conn->error, "a foreign key constraint fails") !== false){
							$this->error = "Error deleting scholarship. Information in use.";
						}
					}
				}
			}

			return $result;

		}

		private function IsScholarshipUsed($id){
			$isUsed = false;

			$query = "SELECT * FROM `enl-enlistment_details` WHERE Scholarship={0} ";
			$query = str_replace("{0}", $id, $query);

			$conn = $this->conn;
			$result = $conn->query($query);

			if($result->num_rows != 0){
				$isUsed = true;
			}

			return $isUsed;
		}

		/*--------------------------------------------------------

			DISCOUNTS [ SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetDiscountsByKey(){
			$discounts = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `DiscountID`, `Description`, `Price`, `Percentage`, `Type`, `DateCreated`, `DateModified` FROM `fin-discounts` WHERE Type=2 ";

				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$discounts = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$discounts[$row['DiscountID']] = new Discount($row['DiscountID'], $row['Description'], $row['Price'], $row['Percentage'], $row['Type']);
							$ctr++;
						}
					}
				}
			}

			return $discounts;
		}

		//Add Scholarship
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddDiscount($description, $price, $percentage){

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
				$query .= "VALUES ('{$description}',{$price},{$percentage},2,NOW(), NOW())";

				$conn->query($query);

				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[] = "Error adding discount. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Edit Discount
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditDiscount($id, $description, $price, $percentage){

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

				$query = "UPDATE `fin-discounts` SET ";
				$query .= "`Description`='{$description}', ";
				$query .= "`Price`={$price}, ";
				$query .= "`Percentage`={$percentage}, ";
				$query .= "`DateModified`=NOW() ";
				$query .= "WHERE DiscountID={$id}";

				$conn->query($query);

				if($conn->affected_rows > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[] = "Error updating discount. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Delete Discount from database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteDiscount($discount_id){

			$result = false;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "DELETE FROM `fin-discounts` ";
				$query .= "WHERE DiscountID=";
				$query .= $discount_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting discount. Information in use.";
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			Payments [SELECT | INSERT | DELETE]

		---------------------------------------------------------*/

		//Returns the Downpayments of the student
		function GetTotalDownpayment($student_id){
			$total = 0;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$tuition_fee_id = 22;

				$query = "SELECT `PaymentID`, fp.`StudentID`, `Fee`, `Price`, fp.`DateCreated`, `Deleted` FROM `fin-payments` fp ";
				//$query .= "LEFT JOIN `enl-enlistment_details` enl ON enl.StudentID=fp.StudentID ";
				$query .= "WHERE Deleted=0 AND fp.StudentID={$student_id} AND Fee={$tuition_fee_id} ";
				$query .= "AND fp.SY=(SELECT SchoolYearID FROM `sch-school_years` WHERE Active=1) ";
				$query .= "AND fp.Sem=(SELECT SemesterID FROM `sch-semesters` WHERE Active=1) ";
				$query .= "ORDER BY DateCreated DESC LIMIT 0,1";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[] = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;

					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$total += $row['Price'];
							$ctr++;
						}
					}
				}
			}
			return $total;
		}

		//Returns the Downpayments of the student
		function GetTotalBalance($student_id){
			$total = 0;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT ";
				$query .= "((SELECT CAST(SUM(fb.Price) AS DECIMAL(10,2)) FROM `fin-billings` fb WHERE StudentID={$student_id})- ";
				$query .= "CAST(SUM(fp.Price) AS DECIMAL(10,2))) AS Total ";
				$query .= "FROM `fin-payments` fp ";
				$query .= "LEFT JOIN `fin-fees` ff ON ff.FeeID=fp.Fee ";
				$query .= "WHERE StudentID={$student_id} AND Deleted=0 AND ff.FeeType=4";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[] = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$total = $row['Total'];
							$ctr++;
						}
					} else {
						$total = 0;
					}

				}
			}
			return $total;
		}

		//Add Payment
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddPayment($student_id, $transaction_no, $fee, $price, $sy=null, $sem=null){

			//clean input
			$student_id = (int) $student_id;
			$fee = (int) $fee;
			$price = (float) $price;

			$result = false;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `fin-payments`(`StudentID`, TransactionNumber, `Fee`, `Price`, `DateCreated`, SY, Sem) ";
				$query .= "VALUES ({$student_id},{$transaction_no}, {$fee},{$price},NOW(),{$sy}, {$sem})";

				$conn->query($query);

				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[] = "Error adding payment. Duplicate found!";
					} else {
						$this->error[sizeof($this->error)] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Delete Payment from database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeletePayment($payment_id){

			$result = false;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `fin-payments` SET Deleted=1 ";
				$query .= "WHERE PaymentID=";
				$query .= $payment_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting payment. Information in use.";
					}
				}
			}

			return $result;

		}


		/*--------------------------------------------------------

			FEES [ SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetFees($fee_id=null){
			$fees = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `FeeID`, `Description`, `Price`, `FeeType`, `IsVirtual` FROM `fin-fees` WHERE 1 AND IsDeleted=0 ";
				if($fee_id != null){
					$query .= "AND FeeID={$fee_id} ";
				}
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$fees[$row['FeeID']] = new Fee($row['FeeID'], $row['Description'], $row['Price'], $row['FeeType'], $row['IsVirtual']);
							$ctr++;
						}
					}
				}
			}

			return $fees;
		}

		//Will always return null for errors else an array
		function GetMiscFees($fee_id=null){
			$fees = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `FeeID`, `Description`, `Price`, `FeeType`, `IsVirtual` FROM `fin-fees` ";
				$query .= "WHERE FeeType=1 AND IsDeleted=0 ";

				if($fee_id != null){
					$query .= "AND FeeID={$fee_id} ";
				}
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$fees[$row['FeeID']] = new Fee($row['FeeID'], $row['Description'], $row['Price'], $row['FeeType'], $row['IsVirtual']);
							$ctr++;
						}
					}
				}
			}

			return $fees;
		}

		//Will always return null for errors else an array
		function GetOtherFees($fee_id=null){
			$fees = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `FeeID`, `Description`, `Price`, `FeeType`, `IsVirtual` FROM `fin-fees` ";
				$query .= "WHERE FeeType=3 AND IsDeleted=0 ";

				if($fee_id != null){
					$query .= "AND FeeID={$fee_id} ";
				}
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$fees[$row['FeeID']] = new Fee($row['FeeID'], $row['Description'], $row['Price'], $row['FeeType'], $row['IsVirtual']);
							$ctr++;
						}
					}
				}
			}

			return $fees;
		}

		//Will always return null for errors else an array
		function GetLabFees($fee_id=null){
			$fees = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `FeeID`, `Description`, `Price`, `FeeType`, `IsVirtual` FROM `fin-fees` ";
				$query .= "WHERE FeeType=5 AND IsDeleted=0 ";

				if($fee_id != null){
					$query .= "AND FeeID={$fee_id} ";
				}
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$fees[$row['FeeID']] = new Fee($row['FeeID'], $row['Description'], $row['Price'], $row['FeeType'], $row['IsVirtual']);
							$ctr++;
						}
					}
				}
			}

			return $fees;
		}

		//Will always return null for errors else an array
		function GetEnergyFees($fee_id=null){
			$fees = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `FeeID`, `Description`, `Price`, `FeeType`, `IsVirtual` FROM `fin-fees` ";
				$query .= "WHERE FeeType=6 AND IsDeleted=0 ";

				if($fee_id != null){
					$query .= "AND FeeID={$fee_id} ";
				}
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$fees[$row['FeeID']] = new Fee($row['FeeID'], $row['Description'], $row['Price'], $row['FeeType'], $row['IsVirtual']);
							$ctr++;
						}
					}
				}
			}

			return $fees;
		}

		//Will always return null for errors else an array
		function GetMixedOtherFees($fee_id=null){
			$fees = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `FeeID`, `Description`, `Price`, `FeeType`, `IsVirtual` FROM `fin-fees` ";
				$query .= "WHERE (FeeType=6 OR FeeType=5 OR FeeType=3) AND IsDeleted=0 ";

				if($fee_id != null){
					$query .= "AND FeeID={$fee_id} ";
				}
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$fees[$row['FeeID']] = new Fee($row['FeeID'], $row['Description'], $row['Price'], $row['FeeType'], $row['IsVirtual']);
							$ctr++;
						}
					}
				}
			}

			return $fees;
		}

		//Will always return null for errors else an array
		function GetMiscFee(){
			$total = 0;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SUM(Price) AS Total FROM `fin-fees` ";
				$query .= "WHERE FeeType=1 AND IsVirtual=1 AND IsDeleted=0 ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$total = $row['Total'];
							$ctr++;
						}
					}
				}
			}
			return $total;
		}


		//Will always return null for errors else an array
		function GetRegFee(){
			$total = 0;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT Price AS Total FROM `fin-fees` ";
				$query .= "WHERE FeeType=2 AND FeeID=3 AND IsDeleted=0 ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$total = $row['Total'];
							$ctr++;
						}
					}
				}
			}
			return $total;
		}

		//Will always return null for errors else an array
		function GetTuitionFee_Cash(){
			$total = 0;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT Price AS Total FROM `fin-fees` ";
				$query .= "WHERE FeeType=2 AND FeeID=2 AND IsDeleted=0 ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$total = $row['Total'];
							$ctr++;
						}
					}
				}
			}
			return $total;
		}

		function ComputeTuitionFee_Cash($lecUnits, $lecRate, $labUnits, $labRate){
			$total = 0;
			$total += $lecUnits * $lecRate;
			$total += $labUnits * 3 * $labRate;

			return $total;
		}

		//Will always return null for errors else an array
		function GetInstallmentFee(){
			$total = 0;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT Price AS Total FROM `fin-fees` ";
				$query .= "WHERE FeeType=2 AND FeeID=4 AND IsDeleted=0 ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$total = $row['Total'];
							$ctr++;
						}
					}
				}
			}
			return $total;
		}

		//Will always return null for errors else an array
		function GetRegistrationFee(){
			$total = 0;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT Price AS Total FROM `fin-fees` ";
				$query .= "WHERE FeeType=2 AND FeeID=3 AND IsDeleted=0 ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$total = $row['Total'];
							$ctr++;
						}
					}
				}
			}
			return $total;
		}

		function GetCashBasis(){
			$total = 0;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SUM(Price) AS Total FROM `fin-fees` ";
				$query .= "WHERE FeeType=2 AND IsDeleted=0 AND Description NOT LIKE '%Installment%' ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$total = $row['Total'];
							$ctr++;
						}
					}
				}
			}
			return $total+$this->GetMiscFee();
		}

		function GetInstallmentBasis(){
			$total = 0;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT SUM(Price) AS Total FROM `fin-fees` ";
				$query .= "WHERE FeeType=2 AND IsDeleted=0 AND Description NOT LIKE '%Installment%' ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$total = $row['Total'];
							$ctr++;
						}
					}
				}
			}
			return $total+$this->GetMiscFee();
		}

		//Will always return null for errors else an array
		function GetStdFees($fee_id=null){
			$fees = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `FeeID`, `Description`, `Price`, `FeeType`, `IsVirtual` FROM `fin-fees` ";
				$query .= "WHERE FeeType=2 AND IsDeleted=0 ";

				if($fee_id != null){
					$query .= "AND FeeID={$fee_id} ";
				}
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$fees[$row['FeeID']] = new Fee($row['FeeID'], $row['Description'], $row['Price'], $row['FeeType'], $row['IsVirtual']);
							$ctr++;
						}
					}
				}
			}

			return $fees;
		}

		//Will always return null for errors else an array
		function GetGenFees($fee_id=null){
			$fees = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `FeeID`, `Description`, `Price`, `FeeType`, `IsVirtual` FROM `fin-fees` ";
				$query .= "WHERE FeeType=4 AND IsDeleted=0 ";

				if($fee_id != null){
					$query .= "AND FeeID={$fee_id} ";
				}
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$fees[$row['FeeID']] = new Fee($row['FeeID'], $row['Description'], $row['Price'], $row['FeeType'], $row['IsVirtual']);
							$ctr++;
						}
					}
				}
			}

			return $fees;
		}

		//Others
		function GetMyFees($fee_id=null){
			$fees = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `FeeID`, `Description`, `Price`, `FeeType`, `IsVirtual` FROM `fin-fees` ";
				$query .= "WHERE FeeType=3 AND IsDeleted=0 ";

				if($fee_id != null){
					$query .= "AND FeeID={$fee_id} ";
				}
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error = $conn->error;
				} else {
					$fees = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$fees[$row['FeeID']] = new Fee($row['FeeID'], $row['Description'], $row['Price'], $row['FeeType'], $row['IsVirtual']);
							$ctr++;
						}
					}
				}
			}

			return $fees;
		}

		//Add New Fee
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function AddFee($description, $price, $fee_type, $is_virtual=0){

			//clean input
			$description = (string) trim(addslashes(strip_tags($description)));
			$price = (float) str_replace(",","",addslashes(strip_tags($price)));
			$fee_type = (int) $fee_type;
			$is_virtual = (int) $is_virtual;

			$result = false;

			//check for errors
			if($description == ''){ $this->error[] = "Description cannot be blank."; }
			if($price <= 0){ $this->error[] = "Price cannot be less than or equal to zero."; }
			if($fee_type <= 0){ $this->error[] = "Fee Type not selected."; }

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "INSERT INTO `fin-fees`(`Description`, `Price`, `FeeType`, `IsVirtual`, Modified) ";
				$query .= "VALUES ('{$description}',{$price},{$fee_type},{$is_virtual}, NOW())";

				$conn->query($query);

				if($conn->insert_id > 0){
					$result = true;
				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[] = "Error adding fee. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Edit Fee in database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function EditFee($id, $description, $price, $fee_type, $is_virtual=0){

			//clean input
			$description = (string) trim(addslashes(strip_tags($description)));
			$price = (float) str_replace(",","",addslashes(strip_tags($price)));
			$fee_type = (int) $fee_type;
			$is_virtual = (int) $is_virtual;

			$result = false;

			//check for errors
			if($description == ''){ $this->error[] = "Description cannot be blank."; }
			if($price <= 0){ $this->error[] = "Price cannot be less than or equal to zero."; }
			if($fee_type <= 0){ $this->error[] = "Fee Type not selected."; }

			$result = false;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `fin-fees` SET `Description`='{$description}', ";
				$query .= "`Price`={$price},`FeeType`={$fee_type},`IsVirtual`={$is_virtual}, Modified=NOW() ";

				$query .= "WHERE FeeID=";
				$query .= $id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "Duplicate entry") !== false){
						$this->error[] = "Error editing school fee. Duplicate found!";
					} else {
						$this->error[] = $conn->error;
					}
				}
			}

			return $result;

		}

		//Delete Fee from database
		//-->returns true on success else false
		//-->if FALSE, sets self::error
		function DeleteFee($fee_id){

			$result = false;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "UPDATE `fin-fees` SET IsDeleted=1 ";
				$query .= "WHERE FeeID=";
				$query .= $fee_id;

				$conn->query($query);

				if($conn->affected_rows > 0){

					$result = true;

				} else {
					if(strpos($conn->error, "a foreign key constraint fails") !== false){
						$this->error = "Error deleting school fee. Information in use.";
					}
				}
			}

			return $result;

		}

		/*--------------------------------------------------------

			FEE TYPES [ SELECT | INSERT | UPDATE | DELETE]

		---------------------------------------------------------*/

		//Will always return null for errors else an array
		function GetFeeTypesByKey(){
			$types = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `TypeID`, `Description` FROM `fin-fee_types` WHERE 1 AND IsVirtual=0 ";
				$query .= "ORDER BY Description ";


				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[] = $conn->error;
				} else {
					$types = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$types[$row['TypeID']] = new FeeType($row['TypeID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $types;
		}

		//Will always return null for errors else an array
		function GetFeeTypesByKey2(){
			$types = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `TypeID`, `Description` FROM `fin-fee_types` WHERE TypeID=3";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[] = $conn->error;
				} else {
					$types = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$types[$row['TypeID']] = new FeeType($row['TypeID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}
			return $types;
		}

		//Will always return null for errors else an array
		function GetFeeTypesByKeyAll(){
			$types = null;

			if($this->conn == null){
				$this->error[] = "No defined connection.";
				return null;
			} else {

				$conn = $this->conn;

				$query = "SELECT `TypeID`, `Description` FROM `fin-fee_types` WHERE 1 ";
				$query .= "ORDER BY Description ";

				$result = $conn->query($query);

				//check for errors first
				if($conn->error <> ""){
					$this->error[] = $conn->error;
				} else {
					$types = array();
					$ctr = 0;
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$types[$row['TypeID']] = new FeeType($row['TypeID'], $row['Description']);
							$ctr++;
						}
					}
				}
			}

			return $types;
		}

		function getStudents(){

			$data = array();

			$conn = $this->conn;
			$query = "SELECT StudentID, StudentNo, CONCAT(TRIM(LastName), ', ', FirstName, ' ', MiddleName) AS `studentName` FROM `spr-personal_data` ORDER BY LastName";

			$result = $conn->query($query);
			while($row = $result->fetch_assoc()){
				$data[] = $row;
			}

			return $data;
		}

		function getStudentBills($studentId){
			$conn = $this->conn;
			$query = "SELECT SUM(Price) AS `total` FROM `fin-billings` WHERE StudentID={$studentId} ";
			$result = $conn->query($query);
			$row = $result->fetch_assoc();

			return $row['total'];
		}

		function getStudentBillsSpecific($studentId, $sy, $sem){
			$conn = $this->conn;
			$query  = "SELECT SUM(Price) AS `total` FROM `fin-billings` WHERE StudentID={$studentId} ";
			$query .= "AND Sem={$sem} AND SY={$sy} ";
			$result = $conn->query($query);
			$row = $result->fetch_assoc();

			return $row['total'];
		}

		function getStudentPayments($studentId){

			$conn = $this->conn;

			$query  = "SELECT SUM(fp.Price) as `total` FROM `fin-payments` fp ";
			$query .= "LEFT JOIN `fin-fees` ff ON ff.FeeID=fp.Fee ";
			$query .= "LEFT JOIN `fin-fee_types` ft ON ft.TypeID=ff.FeeType ";
			$query .= "WHERE FeeType != 3 AND Deleted=0 AND fp.StudentID=" . $studentId;
			$query .= " ";

			$result = $conn->query($query);

			$row = $result->fetch_assoc();

			return $row['total'];

		}

		function getStudentPaymentsSpecific($studentId, $sy, $sem){

			$conn = $this->conn;

			$query  = "SELECT SUM(fp.Price) as `total` FROM `fin-payments` fp ";
			$query .= "LEFT JOIN `fin-fees` ff ON ff.FeeID=fp.Fee ";
			$query .= "LEFT JOIN `fin-fee_types` ft ON ft.TypeID=ff.FeeType ";
			$query .= "WHERE FeeType != 3 AND Deleted=0 AND fp.StudentID=" . $studentId;
			$query .= " ";
			$query .= "AND SY={$sy} AND Sem={$sem} ";

			$result = $conn->query($query);
			$row = $result->fetch_assoc();

			return $row['total'];

		}
	}


?>
