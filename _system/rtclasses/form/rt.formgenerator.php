<?php
	/*	+-------------------------------------------------------------------------------------------------------+
		|  Name			: RTFormCreator					   														|
		|  Author		: Algefmarc Anthony L. Almocera    														|
		|  Date			: October 22, 2011				   														|
		|  Description	: This class will allow you to create forms using only the available classMethods.		| 
		|				  This is created, especially, for controlling form inputs passed from $_POST and $_GET.|
		+-------------------------------------------------------------------------------------------------------+
	*/ 
	class RTFormCreator{
		private $formName="";
		private $method="";
		private $action="";
		private $enctype="";
		//array for containing all fields
		//	contents:
		//		array(
		//			"name" => array(
		//				"label" => "label_value",
		//				"name" => "name_value",
		//				"class" => "class_value",
		//				"id" => "id_value",
		//				"value" => "data",
		//				"disabled" => 0 or 1,
		//				"selected" => 0 or 1,
		//				"checked" => 0 or 1,
		//				"rows" => 0 if none textarea,
		//				"cols" => 0 if none textarea,
		//				"maxlength" => 5
		//			)
		//		)
		private $formFields = array();
		//form data
		private $data = array();
		//content of the program in HTML format
		private $formHTML="";
		
		//defines the Form Name, Method used, Form to be transferred on submit and EncType..
		function __construct($formName, $method="post", $action=null, $enctype=null){
			$this->formName = $formName;
			$this->method = $method;
			if($action){
				$this->action = $action;
			} else {
				$this->action = $_SERVER['PHP_SELF'];
			}
			if($enctype){
				$this->enctype = $enctype;
			}
			
			$this->startForm();
		}
		
		//cleans data using htmlspecialchars($value)
		private function Clean($value){
			return htmlspecialchars(strip_tags($value));
		}
		
		//inputs the initial data for the form tag
		private function StartForm(){
			$this->formHTML .= "<form method=\"{$this->method}\" action=\"{$this->action}\"";
			if($this->enctype){
				$this->formHTML .= " enctype=\"{$this->enctype}\"";
			}
			$this->formHTML .= " name=\"{$this->formName}\" class=\"c{$this->formName}\" id=\"i{$this->formName}\"";
			$this->formHTML .= ">";
		}
		
		//adds a text field to the form
		function addText($name,$display=null,$value=null, $disabled=null, $maxlength=null, $class=null, $id=null){
			$this->formFields[$name] = array(
					"name" => $name,
					"type" => "text", //defines the type of the input
					"label" => $display,
					"value" => $value,
					"disabled" => $disabled,
					"maxlength" => $maxlength,
					"class" => $class,
					"id" => $id
			);
			
			//add class if null
			if($class == null){
				$this->formFields[$name]["class"] = "c" . $name;
			}
			
			//add id
			if($id == null){
				$this->formFields[$name]["id"] = "i" . $name;
			}
			
			//add display
			if($display == null){
				$this->formFields[$name]["label"] = ucfirst($name) . ":";
			}
			
		}//>end of add text
		
		//adds a textarea field to the form
		function addTextarea($name,$display=null,$value=null, $disabled=null, $rows=3, $cols=25, $class=null, $id=null){
			$this->formFields[$name] = array(
				"name" => $name,
				"type" => "textarea", //defines the type of the input
				"label" => $display,
				"value" => $value,
				"disabled" => $disabled,
				"rows" => $rows,
				"cols" => $cols,
				"class" => $class,
				"id" => $id
			);		
			
			//add class if null
			if($class == null){
				$this->formFields[$name]["class"] = "c" . $name;
			}
			
			//add id
			if($id == null){
				$this->formFields[$name]["id"] = "i" . $name;
			}
			
			//add display
			if($display == null){
				$this->formFields[$name]["label"] = ucfirst($name) . ":";
			}
			
		}//>end of add textarea

		//adds a Button field to the form
		function addSubmitButton($name,$display=null,$value=null, $disabled=null, $class=null, $id=null){
			$this->formFields[$name] = array(
				"name" => $name,
				"type" => "submit", //defines the type of the input
				"label" => $display,
				"value" => $value,
				"disabled" => $disabled,
				"class" => $class,
				"id" => $id
			);		
			
			//add class if null
			if($class == null){
				$this->formFields[$name]["class"] = "c" . $name;
			}
			
			//add id
			if($id == null){
				$this->formFields[$name]["id"] = "i" . $name;
			}
			
			//add display
			if($display == null){
				$this->formFields[$name]["label"] = "";
			}
			
		}//>end of add button
		
		//convert formField to HTML
		function ConvertToHTML($field){
			//input label
			$buffer = "<p class=\"FormField\"><label><span>{$field['label']}</span></label>";
			switch($field["type"]){
				case "text":
					//input details
					$buffer .= "<input type=\"{$field['type']}\" value=\"{$field['value']}\" maxlength=\"{$field['maxlength']}\" name=\"{$field['name']}\" class=\"{$field['class']}\" id=\"{$field['id']}\"";
						//optional attributes
						//disabled
						if($field['disabled'] != null){
							$buffer .= " disabled=\"disabled\"";
						}				
					//close input tag
					$buffer .= " />";						
					//close <p>
					$buffer .= "</p>";
					return $buffer;
					break;
				case "textarea":
					//input details
					$buffer .= "<textarea name=\"{$field['name']}\" class=\"{$field['class']}\" id=\"{$field['id']}\"";
						//optional attributes
							//rows
							if($field['rows'] != null){
								$buffer .= " rows=\"{$field['rows']}\"";
							}
							//cols
							if($field['cols'] != null){
								$buffer .= " cols=\"{$field['cols']} \"";
							}
							//disabled
							if($field['disabled'] != null){
								$buffer .= " disabled=\"disabled\"";
							}
					//close textarea
					$buffer .= " >";
					//input value [optional]
					if($field['value'] != null){
						$buffer .= $field['value'];
					}
					//close
					$buffer .= "</textarea>";							
					
					return $buffer;
					break;
				case "radio":
					break;
				case "select":
					break;
				case "submit":
				
					if($field['label'] != null){
						$buffer = "<p class=\"FormField_Submit\"><label><span>{$field['label']}</span></label>";
					} else {
						$buffer = "<p class=\"FormField_Submit\"><label><span></span></label>";
					}
					
					//input details
					$buffer .= "<input type=\"{$field['type']}\" value=\"{$field['value']}\" name=\"{$field['name']}\" class=\"{$field['class']}\" id=\"{$field['id']}\"";
						//optional attributes
						//disabled
						if($field['disabled'] != null){
							$buffer .= " disabled=\"disabled\"";
						}				
					//close input tag
					$buffer .= " />";						
					//close <p>
					$buffer .= "</p>";
					return $buffer;
					
					break;				 
			}
		}
		
		//outputs the form
		function GetForm(){
		
			//loop and get all field definitions
			foreach($this->formFields as $field){	
				//convert to HTML
				$this->formHTML .= $this->ConvertToHTML($field);
			}
			
			//add form closing tag
			$this->formHTML .= "</form>";
			
			return $this->formHTML;
		}
		
		//output processed form
		function GetProcessedForm($data){
			//check first if data passed is array
			if(is_array($data)){
				foreach($data as $key => $value){
					$this->formFields[$key]['value'] = $value;
				}
			} else {
				throw new Exception("Error: Data passed is not an array.");
			}
		}
		
	}	

?>