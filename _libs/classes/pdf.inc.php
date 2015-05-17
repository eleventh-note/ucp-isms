<?php
	/*-----------------------------------------------

		PDF Extender
		-requires FPDF
	-------------------------------------------------*/


	class PDF extends WrapPDF{
		function PrimaryWords($number){
			$value = "";
			switch($number){
				case 1: $value = "One"; break;
				case 2: $value = "Two"; break;
				case 3: $value = "Three"; break;
				case 4: $value = "Four"; break;
				case 5: $value = "Five"; break;
				case 6: $value = "Six"; break;
				case 7: $value = "Seven"; break;
				case 8: $value = "Eight"; break;
				case 9: $value = "Nine"; break;
				case 10: $value = "Ten"; break;
				case 11: $value = "Eleven"; break;
				case 12: $value = "Twelve"; break;
				case 13: $value = "Thirteen"; break;
				case 14: $value = "Fourteen"; break;
				case 15: $value = "Fifteen"; break;
				case 16: $value = "Sixteen"; break;
				case 17: $value = "Seventeen"; break;
				case 18: $value = "Eighteen"; break;
				case 19: $value = "Nineteen"; break;
				case 20: $value = "Twenty"; break;
				case 30: $value = "Thirty"; break;
				case 40: $value = "Fourty"; break;
				case 50: $value = "Fifty"; break;
				case 60: $value = "Sixty"; break;
				case 70: $value = "Seventy"; break;
				case 80: $value = "Eighty"; break;
				case 90: $value = "Ninety"; break;
			}
			return $value;
		}

		function ProcessHundreds($value){
			$words = "";
			$number = $value;
			$hundred = 0;
			$ten = 0;
			$hundred = floor($value/100);
			$number -= floor($value/100) * 100;
			$ten = floor($number / 10) * 10;
			$number -= $ten;

			if($hundred > 0){
				$words = $this->PrimaryWords($hundred) . " Hundred";
			}
			if($ten < 20){
				$words .= $this->PrimaryWords($ten+$number);
			} else {
				$words .= " " . $this->PrimaryWords($ten) . "-" . $this->PrimaryWords($number);
			}

			return $words;
		}

		function RemoveExtraChars($value){
			$value = str_replace(",","", $value);
			$value = str_replace("-","");
		}

		function GetWords($value){
			$words = "";
			$number = $value;
			$billion = 0;
			$million = 0;
			$thousand = 0;
			$billion = floor($number/1000000000);
			$number -= $billion * 1000000000;
			$million = floor($number/ 1000000);
			$number -= $million * 1000000;
			$thousand = floor($number/1000);
			$number -= $thousand * 1000;

			if($billion > 0){ $words .= $this->ProcessHundreds($billion) . " Billion "; }
			if($million > 0){ $words .= $this->ProcessHundreds($million) . " Million "; }
			if($thousand > 0){ $words .= $this->ProcessHundreds($thousand) . " Thousand "; }
			$words .= $this->ProcessHundreds($number);

			return $words;
		}

		function CreateHeader($title){
			$this->SetXY(25,15);
			$this->Logo();
			$this->SetXY(522,17);
			$this->Dnv();
			$this->SetY(35);
			$this->SetFont('Arial', 'B', 13);
			$this->Cell(0, 18, SCHOOL_NAME, 0, 1, 'C');
			$this->SetFont('Arial', '', 9);
			$this->Cell(0, 10, SCHOOL_ADDRESS, 0, 1, 'C');

			$this->Cell(0, 12, "E-mail Add: info@ucp.edu.ph", 0, 1, 'C');
			$this->Cell(0, 12, "Tel Nos.: (02) 820-2222 / (02) 820-4276 / (02) 829-3624", 0, 1, 'C');
			$this->MultiCell(0,0,'');
			$this->Title($title);
		}

		function CreateHeader2($title){
			$this->SetXY(25,15);
			$this->Logo();
			$this->SetXY(522,17);
			$this->Dnv();
			$this->SetY(35);
			$this->SetFont('Arial', 'B', 13);
			$this->Cell(0, 18, SCHOOL_NAME, 0, 1, 'C');
			$this->SetFont('Arial', '', 9);
			$this->Cell(0, 10, SCHOOL_ADDRESS, 0, 1, 'C');

			$this->Cell(0, 12, "E-mail Add: info@.ucp.edu.ph", 0, 1, 'C');
			$this->Cell(0, 12, "Telefax No.: 820-2222", 0, 1, 'C');
			$this->MultiCell(0,0,'');
			// $this->Title($title);
		}

		function Title($message){
			$this->SetFont('Arial', '', 10);
			$this->Cell(0,10,'',0,1, 'C');
			$this->Cell(0,0,'', 1,1, 'C');
			$this->Cell(0, 16, $message, 0, 1, 'C');
			$this->Cell(0,0,'', 1,1, 'C');
		}

		function TitleStart($message){
			$this->SetFont('Arial', '', 10);
			$this->MultiCell(0,10,'');
			$this->Cell(0,10,'','T',0, 'C');
			$this->Cell(0,0,'', 0,1, 'C');
			$this->Cell(0, 16, $message, 0, 1, 'C');
			$this->Cell(0,0,'', 0,1, 'C');
		}

		function TitleNext($message){
			$this->SetFont('Arial', '', 10);
			$this->Cell(0,10,'',0,0, 'C');
			$this->Cell(0,0,'', 0,1, 'C');
			$this->Cell(0, 8, $message, 0, 1, 'C');
			$this->Cell(0,0,'', 0,1, 'C');
		}

		function TitleEnd($message){
			$this->SetFont('Arial', '', 10);
			$this->MultiCell(0,0,'');
			$this->Cell(0,0,'', 0,1, 'C');
			$this->Cell(0, 16, $message, 0, 1, 'C');
			$this->Cell(0,0,'', 'B',1, 'C');
		}

		function Border(){
			$this->Cell(0,10,'',0,1, 'C');
			$this->Cell(0,0,'', 1,1, 'C');
		}
		function Logo($x=null, $y=null){
			$this->Image("pdf_images/logo.jpg");
		}
		function Dnv($x=null, $y=null){
			$this->Image("pdf_images/dnv.jpg");
		}

		function DisplayReceipt($data=null){
			$this->SetFont('Times','B','14');

			//DATE
			$this->SetXY(40, 60);
			$this->SetFont('Arial', '', '11');
			$this->MultiCell(130, 30, date("M d, Y", strtotime($data['date'])), 0, 'C');
			$this->SetFont('Arial', '', '11');
			$this->SetXY(200, 60);
			$this->MultiCell(345, 30, $data['received_from'], 0, 'L');
			$this->SetXY(100, 90);
			$this->SetFont('Arial', '', '10');
			$this->MultiCell(148, 10, $data['course'], 0, 'L');
			$this->SetXY(100, 112);
			$this->MultiCell(80, 10, $data['sy'], 0, 'L');
			$this->SetXY(100, 134);
			$this->MultiCell(80, 10, substr($data['semester'],0,3), 0, 'L');

			$this->SetXY(210, 146);
			$particulars = "";
			foreach($data['particulars'] as $item){
				if($particulars == ""){
					$particulars .= $item;
				} else {
					$particulars .= ", {$item}";
				}
			}
			$this->SetFont('Arial', '', '10');
			$this->MultiCell(345,15, $particulars, 0, 'L');

			//AMOUNT
			$this->SetXY(460, 123);
			$this->SetFont('Arial', '', '11');
			$this->MultiCell(85,15, number_format($data['amount'],2,".",","), 0, 'C');

			//AMOUNT
			$this->SetXY(210, 123);
			$this->SetFont('Arial', '', '10');
			$this->MultiCell(263,15,str_replace("-","",$this->GetWords($data['amount'])) . " Pesos", 0, 'L');

			//MAILING ADDRESS
			$this->SetXY(210, 97);
			$this->SetFont('Arial', '', '10');
			$this->MultiCell(263,15, $data['mailing_address'], 0, 'L');

		}

		function DisplayReceipt20131207($data=null){
			$this->SetFont('Times','B','14');

			//DATE
			$this->SetXY(40, 55);
			$this->SetFont('Arial', '', '14');
			$this->MultiCell(130, 30, date("M d, Y", strtotime($data['date'])), 0, 'C');
			$this->SetFont('Arial', '', '12');
			$this->SetXY(210, 65);
			$this->MultiCell(345, 15, $data['received_from'], 0, 'L');
			$this->SetXY(90, 90);
			$this->SetFont('Arial', '', '10');
			$this->MultiCell(148, 10, $data['course'], 0, 'L');
			$this->SetXY(90, 106);
			$this->MultiCell(80, 10, $data['sy'], 0, 'L');
			$this->SetXY(90, 122);
			$this->MultiCell(80, 10, $data['semester'], 0, 'L');

			$this->SetXY(210, 130);
			$particulars = "";
			foreach($data['particulars'] as $item){
				if($particulars == ""){
					$particulars .= $item;
				} else {
					$particulars .= ", {$item}";
				}
			}
			$this->SetFont('Arial', '', '12');
			$this->MultiCell(345,15, $particulars, 0, 'L');

			//AMOUNT
			$this->SetXY(460, 92);
			$this->SetFont('Arial', '', '12');
			$this->MultiCell(85,15, number_format($data['amount'],2,".",","), 0, 'C');

			//AMOUNT
			$this->SetXY(210, 98);
			$this->SetFont('Arial', '', '10');
			$this->MultiCell(263,15,str_replace("-","",$this->GetWords($data['amount'])) . " Pesos", 0, 'L');
		}

		function OutputReceipt($data){

			$this->DisplayReceipt($data);
		}
	}

?>
