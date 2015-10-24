<?php
	/* #-------------------------------------------------
	   #
	   #	Description:	Template for 00 Default Layout
	   #	Autdor:		Algefmarc A. L. Almocera
	   #	Date Started:	May 30, 2011
	   #	Last Modified:	June 8, 2011
	   #
	   #-------------------------------------------------
	*/
//::START OF 'SESSION DECLARATION'
	//open session here if needed (e.g: session_start())
	session_start();
//::END OF 'SESSION DECLARATION'

	//Set no caching
	//header("Content-Type: application/pdf");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

//::START OF 'CONFIGURATION'
	require_once("_system/_config/sys_config.php");
	//configurations can be overriden here
	include_once(CLASSLIST . "dataconnection.inc.php");
	require_once(CLASSLIST . "user.inc.php");
	require_once(CLASSLIST . "sentry.inc.php");
	require_once(CLASSLIST . "menu.inc.php");
	require_once(CLASSLIST . "dvsns.inc.php");
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "crrclm.inc.php");
	require_once(CLASSLIST . "schdls.inc.php");
	require_once(CLASSLIST . "fclts.inc.php");
	require_once(CLASSLIST . "fin.inc.php");
	require_once(CLASSLIST . "stdnts.inc.php");
	require_once(CLASSLIST . "enl.inc.php");
	require_once("_libs/fpdf/" . "wrap_pdf.php");
	require_once(CLASSLIST . "pdf.inc.php");

//::END OF 'CONFIGURATION'

	//# General Variables - shown in all documents for easy modification
		$title = SCHOOL_NAME . " Integrated School Management System";
		$keywords = "";
		$description = "";
		$autdor = "";
		$robots="noindex,nofollow";

	define('OTHER_FEE', 3);
	define('LAB_FEE', 5);
	define('ENERGY_FEE', 6);

	//Sentry/Security Measures must be done here
	if(isset($_SESSION['UserInfo'])){
		//autdenticate user privileges
		$UserInfo = unserialize($_SESSION['UserInfo']);
		$Sentry = new Sentry($UserInfo);

		$PagePrivileges = new PagePrivileges();
		$PagePrivileges->AddPrivilege("SUPERADMIN");
		$PagePrivileges->AddPrivilege("Enlistment - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}

	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd_cg = new CollegeManager($conn);
	$hnd_co = new CourseManager($conn);
	$hnd_sc = new SchoolManager($conn);
	$hnd_su = new SubjectManager($conn);
	$hnd_cu = new CurriculumManager($conn);
	$hnd_sh = new ScheduleManager($conn);
	$hnd_fa = new FacultyManager($conn);
	$hnd_fc = new FacilitiesManager($conn);
	$hnd_fi = new FinanceManager($conn);
	$std = new StudentManager($conn);
	$hnd_enl = new EnlistmentManager($conn);

	//COMPUTATION OF FEES
	$std_fees = $hnd_fi->GetStdFees();
	$scholarships = $hnd_fi->GetScholarshipsByKey();
	$discounts = $hnd_fi->GetDiscountsByKey();

	$scholarship = null;
	$discount = null;

	$scholarship_id = (int) $_GET['s'];
	$discount_id = (int) $_GET['d'];

	if($scholarship_id > 0){
		$scholarship = $scholarships[$scholarship_id];
	}

	if($discount_id > 0){
		$discount = $discounts[$discount_id];
	}

	//## TOTAL DISCOUNT
	$total_discount = 0;

	if(isset($_GET['id']) && isset($_GET['type']) && isset($_GET['status'])){
		$id = $_GET['id'];
		$downpayment = $hnd_fi->GetTotalDownpayment($id);

		$loading_status = (int) $_GET['status'];
		$payment_mode = (int) $_GET['type'];
		$labFee = (float) $_GET['lab'];
		$lecFee = (float) $_GET['lec'];
		$nstpFee = 0;
		$other_fees = $_GET['other_fees'];
		$total_other_fees = 0;
		$total_energy_fee = 0;
		$total_laboratory_fee = 0;
		$total_mixed_fees = 0;

		if ($other_fees != '') {

			$other_fees = explode(',', $other_fees);
			foreach ($other_fees as $fee) {
				$arr = explode('_', $fee);

				$feeDetails = null;
				foreach ($hnd_fi->GetFees($arr[0]) as $feeItem)
					$feeDetails = $feeItem;

				if ($feeDetails->fee_type == LAB_FEE) {
					$total_laboratory_fee += (float) $arr[1];
					//var_dump($feeDetails);
				}
				if ($feeDetails->fee_type == ENERGY_FEE)
					$total_energy_fee += (float) $arr[1];
				if ($feeDetails->fee_type == OTHER_FEE)
					$total_other_fees += (float) $arr[1];
			}

			$total_mixed_fees = $total_laboratory_fee + $total_energy_fee + $total_other_fees;
		} else {
			$other_fees = array();
		}

		$date = $hnd_enl->GetEnrollmentDate($id);
		define("INSTALLMENT", 1);
		define("CASH", 2);
		define("FULL_LOAD", 1);
		define("PARTIAL_LOAD", 2);
		define("MINIMUM_DOWNPAYMENT", .4);

		$records = $std->GetSprs($id);
		if(sizeof($records) > 0){
			foreach($records as $item){ $record = $item; }
			$backgrounds = $std->GetCurrentAcademicBackgroundsByKey($record->student_id);
			foreach($backgrounds as $item){ $background = $item; }

			//#Dictionaries
			$dict_courses = $hnd_co->GetCoursesByKey();
			$dict_school_years = $hnd_sc->GetSchoolYearsByKey();
			$dict_semesters = $hnd_sc->GetSemestersByKey();
			$dict_times = $hnd_sc->GetSchoolTimesByKey();
			$dict_days = $hnd_sc->GetSchoolDaysByKey();
			$dict_rooms = $hnd_fc->GetRoomsByKey();
			$dict_faculties = $hnd_fa->GetFacultiesByKey();

			//Sem & Year
			$semesters = $hnd_sc->GetActiveSemester();
			$semester = $semesters[0];
			$school_years = $hnd_sc->GetActiveSchoolYear();
			$school_year = $school_years[0];

			$total_units = 0;
			$student_number = $record->student_no;
			$tmp = $dict_school_years[$background->entry_sy];

			// $school_year = "S.Y. " . $tmp->start . " - " . $tmp->end;
			// $semester = $dict_semesters[$background->entry_semester]->description;

			$student_name = $record->last_name . ", " . $record->first_name . " " . $record->middle_name;

			$to_replace = array("&Ntilde;","&ntilde;");
			$replace_with = array("Ñ", "ñ");
			$student_name = str_replace($to_replace, $replace_with, $student_name);

			//List of Subjects
			$section_subjects = $hnd_sh->GetSectionSubjectsByKey(null, $semester->semester_id, $school_year->year_id);

			$all_units = 0;
			$lec_units = 0;
			$lab_units = 0;
			$nstp_units = 0;
		  $total_half_priced_subjects = 0;

			//##FILL DATE WITH CURRENTLY ENLISTED SUBJECTS
			$is_old = false;
			if(!isset($_SESSION['enlisted_subjects'])){
				$subs = $hnd_enl->GetStudentEnlistmentSubjects($id, $school_year->year_id, $semester->semester_id);
				$is_old = true;
				foreach($subs as $enl){
					$_SESSION['enlisted_subjects'][] = $enl->section_subject . "_" . $enl->curriculum_subject;
				}
			} else {
				foreach($_SESSION['enlisted_subjects'] as $subject) {
					foreach ($section_subjects as $ssub) {
						$arr = explode('_', $subject);
						$section_subject_id = $arr[0];
						$curriculum_subject_id = $arr[1];
						$is_half = $arr[2];

						if ($curriculum_subject_id == $ssub->curriculum_subject_id && $section_subject_id == $ssub->section_subject_id) {
							$unit = $ssub->units;

							// add nstp
							if ((int) $ssub->group == 12) {
								$nstp_units = $ssub->units;
								$nstpFee += $ssub->units * .5 * $lecFee;
							}

							if((int) $is_half === 1) {
								$unit *= .5;
								$total_half_priced_subjects = $unit * $lecFee;
							}

							$all_units += $unit + $ssub->unitsLab;
							$lec_units += $unit;
							$lab_units += $ssub->unitsLab;
						}
					}
				}
			}

			$fees['registration_fee'] = (float) $hnd_fi->GetRegistrationFee();
			//Tuition Fee
			$fees['tuition_fee'] = $hnd_fi->ComputeTuitionFee_Cash($lec_units, $lecFee, $lab_units, $labFee);

			//Miscellaneous Fee
			$fees['miscellaneous_fee'] = (float) $hnd_fi->GetMiscFee();
			//Entrance Fee = Registration + Miscellaneous
			$fees['entrance_fee'] = (float) $fees['miscellaneous_fee'] + $fees['registration_fee'];
			//Installment Fee
			$fees['installment_fee'] = (float) $hnd_fi->GetInstallmentFee();

			//#CREATE PDF
			$pdf = new PDF('P','pt','Letter');
			//#CREATE FIRST PAGE
			$pdf->AliasNbPages();
			$pdf->AddPage();
			//#HEADER
			$pdf->CreateHeader('CERTIFICATE OF REGISTRATION');
			//#COLUMN HEADER
			$pdf->MultiCell(0,3,'');
			$pdf->SetFont('Arial', 'B', '7');
			$pdf->SetX(25); $pdf->Cell(250, 15, 'REGISTRATION NO. ________________');
			$pdf->SetFont('Arial', '', '7');
			$pdf->Cell(0, 15, 'SR-REG-003-F02',0,0,'R');
			$pdf->Ln();
			$pdf->SetX(25);	$pdf->Cell(70,15, 'NEW (   ) OLD (   )');
			$pdf->Cell(150,15, "Student No. {$record->student_no}");
			$pdf->SetX(25);	$pdf->Cell(70,15, 'NEW (   ) OLD (   )');
			$pdf->Cell(95,15, "Student No. __________");
			$pdf->SetFont('Arial', 'B', '7');
			$pdf->Cell(70, 15, "S.Y. " . $school_year->start . "-" . $school_year->end);
			$pdf->Cell(60, 15, "Enrollment Date:");
			$pdf->Cell(60, 15, date("M d, Y", strtotime($date)));
			$pdf->SetFont('Arial', '', '7');
			$pdf->SetY($pdf->y-6);
			$pdf->Cell(0, 15, "Rev 03 / 01 April 2012", 0,0,'R');
			$pdf->SetY($pdf->y+4);
			$pdf->Ln();
			//# NAME, ADDRESS, COURSE and SCHOOL YEAR
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetX(25);
			$pdf->Cell(45,13, "Name: ");
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(0,13,"{$student_name}");
			$pdf->Ln();

			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetX(25); $pdf->Cell(45, 13, "Address: ");
			$pdf->SetFont('Arial', '', '8');

			$mailing_address = $record->mailing_address;
			$to_replace = array("&Ntilde;","&ntilde;");
			$replace_with = array("Ñ", "ñ");
			$mailing_address = str_replace($to_replace, $replace_with, $mailing_address);

			$pdf->Cell(45,13, "{$mailing_address}");

			$pdf->Ln();

			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetX(25); $pdf->Cell(45, 13, "Course: ");
			$pdf->SetFont('Arial', '', '8');
			$pdf->Cell(45,13, "[{$dict_courses[$background->course]->code}] {$dict_courses[$background->course]->description}");
			$pdf->Ln();

			//#CREATE ROWS
			//#PRINT TABLE HEADERS
			$pdf->MultiCell(0,8,'');
			//##  MAIN HEADER
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetFillColor(180,180,180);
			$pdf->SetTextColor(0,0,0);
			$pdf->tablewidths = array(555);
			//$pdf->MultiCell(0,18,"LIST OF SUBJECTS",'LRTB','C',1,true);
			$data[] = array('LIST OF SUBJECTS');
			$pdf->DrawTable($data, 8+4,'C',true);
			//##  COLUMNS
			$pdf->tablewidths = array(70,160,40,40,70,90,85);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(230,230,230);
			$pdf->SetFont('Arial', 'B', '10');
			$data = array();
			$data[] = array('Code', 'Description', 'LEC', 'LAB', 'Day', 'Time', 'Room');
			$pdf->DrawTable($data, 10+4,'C`', true);
			//##  DATA
			$data = array();
			$ctr=0;
				// //define the odd even tables
				 $ctr = 0;
				 if(isset($_SESSION['enlisted_subjects'])){
					$enlisted_subjects = $_SESSION['enlisted_subjects'];
					if(sizeof($enlisted_subjects) > 0){
						$total_units = 0;
						foreach($section_subjects as $item){
							//#Variables for containing previously outputted data
							$prev_day = "";
							$prev_from = "";
							$prev_to = "";
							$prev_room = "";
							$prev_instructor = "";
							$prev_code = "";
							foreach($enlisted_subjects as $enl){
								$tmp = explode("_", $enl);
								if($item->section_subject_id==$tmp[0]){
									$schedules = $hnd_sh->GetSubjectSchedulesBySubject($item->section_subject_id);
									if(sizeof($schedules) > 0){
										$ctr++;
										$total_units += $item->units + $item->unitsLab;

										//Day
										// echo "<td>";
											$tmp_day = "";
											foreach($schedules as $sched){
												if($tmp_day == ""){
													$tmp_day .= $dict_days[$sched->day]->shorthand;
												} else {
													$tmp_day .= "," . $dict_days[$sched->day]->shorthand;
												}
											}

											$tmp_times = "";
											foreach($schedules as $sched){
												if($tmp_times == ""){
													$tmp_times = $dict_times[$sched->from]->description;
													$tmp_times .= "-" . $dict_times[$sched->to]->description;
												} else {
													$tmp_times .= ",";
													$tmp_times .= $dict_times[$sched->from]->description;
													$tmp_times .= "-" . $dict_times[$sched->to]->description;
												}
											}

										//Room
											$tmp_room = "";
											foreach($schedules as $sched){
												if($tmp_room == ""){
													$tmp_room .= $dict_rooms[$sched->room]->description;
												} else {
													$tmp_room .= ', ' . $dict_rooms[$sched->room]->description;
												}
											}

											//### GATHER DATA
											$data[] = array($item->code, $item->subject, $item->units, $item->unitsLab, $tmp_day, $tmp_times, $tmp_room);
										}
									}//end of enl=section_subject_id

								}//end of for each enlisted subject

							}

							$pdf->SetFont('Arial', '', '8');
							$pdf->SetFillColor(255,255,255);

							$pdf->DrawTable($data, $pdf->FontSize+4,'L',true);

							$pdf->SetFont('Arial', 'B', '10');
							$pdf->SetFillColor(180,180,180);
							$pdf->SetTextColor(0,0,0);
							$pdf->Cell(230,18,"Total Units",'LRTB','C',1,true);
							$pdf->Cell(0,18,"{$total_units} Units",'LRTB','C',1,true);
							$pdf->MultiCell(0,15,"");

						}
					}
					 if($ctr == 0){
						// echo "<tr class=\"odd\" class=\"reminder\"><td colspan=\"10\">No subjects enlisted. </td></tr>";
					 }

			//#TITLE
			$pdf->MultiCell(0,2,'');
			$pdf->Title("ASSESSMENT");
			$pdf->MultiCell(0,8,'');

				//## CONVERT ALL TO NUMBER FORMAT
				if($payment_mode==INSTALLMENT && ($loading_status == FULL_LOAD || $loading_status == PARTIAL_LOAD)){

					$total = $fees['registration_fee'] + $total_mixed_fees + $fees['miscellaneous_fee'] + $fees['tuition_fee'];
					$computed_scholarship_discount = 0;
					$fixed_discount = (isset($discount)) ? $discount->price : 0;

					$minimum_downpayment = ($total) * MINIMUM_DOWNPAYMENT;

					if (isset($scholarship)) {
						$computed_scholarship_discount = $fees['tuition_fee'] * ($scholarship->percentage/100);
					}

					$total_less_scholarship = $total - $computed_scholarship_discount; // TOTAL FEE - SCHOLARSHIP

					$total_less_downpayment = ($total_less_scholarship - $downpayment); // LESS DOWNPAYMENT
					$total_add_surcharge = $total_less_downpayment  * 1.05; // ADD SURCHARGE from INSTALLMENT
					$partials = $total_add_surcharge / 3;

					$finals_fee = $partials - $fixed_discount; // FINALS_FEE - FIXED DISCOUNT
					$total_less_fixed_discount = round($partials, 2) * 2 + round($finals_fee, 2);
					$total_discount = $computed_scholarship_discount + $fixed_discount;

					$pdf->SetFont('Arial', 'B', '10');
					$pdf->Cell(250,15,'INSTALLMENT BASIS:',1,0,'C');
					$pdf->Ln();
					$pdf->Ln();
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Entrance / Registration Fee:',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($fees['registration_fee'],2,".",","),0,0,'R');
					$pdf->Cell(30, 15,'');
					//$pdf->SetFont('Arial', '', '9');
					//$pdf->Cell(130,15,'(minimum downpayment =       ' . number_format($minimum_downpayment,2,".",",") . ')',0,0,'L');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Downpayment:',0,0,'L');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(120,15,"Php " . number_format($downpayment ,2,".",","),0,0,'R');
					$pdf->Ln();

					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Tuition Fee: Lecture',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format(($lec_units * $lecFee) - $nstpFee, 2, ".",","),0,0,'R');
					$pdf->Ln();

					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'                     Faculty Lab',0,0,'L');

					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($lab_units * $labFee * 3,2,".",","),0,0,'R');
					$pdf->Cell(30, 15,'');

					if ($nstp_units > 0) {
						$pdf->Ln();
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(130,15,'                     NSTP',0,0,'L');

						$pdf->SetFont('Arial', '', '9');
						$pdf->Cell(120,15,"Php " . number_format($nstpFee,2,".",","),0,0,'R');
						$pdf->Cell(30, 15,'');
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(130,15,'PAYMENT:',0,0,'L');
					} else {
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(130,15,'PAYMENT:',0,0,'L');
					}

					$pdf->Ln();
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Total Tuition Fee:',0,0,'L');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(120,15,"Php " . number_format($fees['tuition_fee'] ,2,".",","),0,0,'R');

					$pdf->Cell(30, 15,'');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(130,15,"Prelim: ",0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($partials,2,".",","),0,0,'R');

					$pdf->Ln();
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Miscellaneous Fee:',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($fees['miscellaneous_fee'],2,".",","),0,0,'R');

					$pdf->Cell(30, 15,'');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(130,15,"Midterm: ",0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($partials,2,".",","),0,0,'R');

					$pdf->Ln();
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Other Fees:',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($total_other_fees ,2,".",","),0,0,'R');

					$pdf->Cell(30, 15,'');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(130,15,"Finals (less fixed discount): ",0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($finals_fee,2,".",","),0,0,'R');
					$pdf->Ln();

					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Energy Fee:',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($total_energy_fee,2,".",","),0,0,'R');

					$pdf->Cell(30, 15,'');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(185,15,"TOTAL",0,0,'L');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(65,15,"Php " . number_format($total_less_fixed_discount ,2,".",","), 'T',0,'R');

					$pdf->Ln();

					if(count($other_fees) > 0) {
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(250,15,'Laboratory Fee',0,0,'L');
						$pdf->SetFont('Arial', '', '9');
						//$pdf->Ln();
					}

					$scholarship_displayed = !(isset($scholarship));
					$discount_displayed = !(isset($discount));
					$fee_total_displayed = false;
					$discount_details = array();

					if(isset($scholarship) || isset($discount)) {
						//array_push($discount_details, "#####");
						array_push($discount_details, "DISCOUNTS:##########B#####");

						if(isset($scholarship)) {
							$discount_string = $scholarship->description . " (" . $scholarship->percentage . "%)#####" . number_format($computed_scholarship_discount, 2, ".", ",") . "##########";
							array_push($discount_details, $discount_string);
						}

						if(isset($discount)) {
							$discount_string = $discount->description . "#####" . number_format($discount->price, 2, ".", ",") . "##########";
							array_push($discount_details, $discount_string);
						}

						$discount_string = "TOTAL DISCOUNT#####" . number_format($total_discount, 2, ".", ",") . "#####B#####T";
						array_push($discount_details, $discount_string);
					}

					// each loop is a line
					$fee_total_displayed_toggle = false;
					while (
						count($other_fees) > 0
						|| !$fee_total_displayed
						|| count($discount_details) > 0
					) {

						$fee = array_shift($other_fees);

						if (isset($fee)) {
							$arr = explode('_', $fee);
							$feeDetails = null;
							foreach ($hnd_fi->GetFees($arr[0]) as $feeItem) $feeDetails = $feeItem;

							if ($feeDetails->fee_type == LAB_FEE) {
								$pdf->SetFont('Arial', '', '9');
								$pdf->Cell(130,15, '  - ' . $feeDetails->description,0,0,'L');
								$pdf->SetFont('Arial', '', '9');
								$pdf->Cell(120,15,"Php " . number_format($feeDetails->price,2,".",","),0,0,'R');
							}
						} else {
							if(!$fee_total_displayed) {
								$pdf->SetFont('Arial', 'B', '9');
								$pdf->Cell(185,15,'Total Fee:',0,0,'L');
								$pdf->SetFont('Arial', 'B', '9');
								$pdf->Cell(65,15,"Php " . number_format($total,2,".",","),'T',0,'R');
								$fee_total_displayed_toggle = true;
							}
						}

						$disc = array_shift($discount_details);
						if (isset($disc)) {
							$split = explode("#####", $disc);
							$space = (!$fee_total_displayed) ? 30 : 280;
							$pdf->Cell($space, 15, '', 0, 0, '');
							$pdf->SetFont('Arial', $split[2], '9');
							$pdf->Cell(185, 15, $split[0], 0, 0, 'L');
							$pdf->Cell(65, 15, $split[1], $split[3], 0, 'R');
						}


						$pdf->Ln();
						$fee_total_displayed = $fee_total_displayed_toggle;
					}

				} elseif($payment_mode==CASH && ($loading_status == FULL_LOAD || $loading_status == PARTIAL_LOAD)){

					$total = $fees['registration_fee'] + $total_mixed_fees + $fees['miscellaneous_fee'] + $fees['tuition_fee'];
					$total_less_all = $total - ($fees['tuition_fee'] - $total_half_priced_subjects) * .05;
					$computed_scholarship_discount = 0;

					$fixed_discount = (isset($discount)) ? $discount->price : 0;
					if (isset ($scholarship)) {
						$computed_scholarship_discount = $fees['tuition_fee'] * ($scholarship->percentage/100);
					}
					$total_less_all -= $computed_scholarship_discount;
					$total_discount = $fixed_discount + $computed_scholarship_discount;

					$pdf->SetFont('Arial', 'B', '10');
					$pdf->Cell(250,15,'CASH BASIS:',1,0,'C');
					$pdf->Ln();
					$pdf->Ln();
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Entrance / Registration Fee:',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($fees['registration_fee'],2,".",","),0,0,'R');
					$pdf->Cell(30, 15,'');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Downpayment:',0,0,'L');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(120,15,"Php " . number_format($downpayment ,2,".",","),0,0,'R');
					$pdf->Ln();

					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Tuition Fee: Lecture',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format(($lec_units * $lecFee) - $nstpFee, 2,".",","),0,0,'R');
					$pdf->Ln();

					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'                     Faculty Lab',0,0,'L');

					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($lab_units * $labFee * 3,2,".",","),0,0,'R');
					$pdf->Cell(30, 15,'');

					if ($nstp_units > 0) {
						$pdf->Ln();
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(130,15,'                     NSTP',0,0,'L');

						$pdf->SetFont('Arial', '', '9');
						$pdf->Cell(120,15,"Php " . number_format($nstpFee,2,".",","),0,0,'R');
						$pdf->Cell(30, 15,'');
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(130,15,'PAYMENT:',0,0,'L');
					} else {
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(130,15,'PAYMENT:',0,0,'L');
					}

					$pdf->Ln();
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Total Tuition Fee:',0,0,'L');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(120,15,"Php " . number_format($fees['tuition_fee'] ,2,".",","),0,0,'R');

					$pdf->Cell(30, 15,'');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(130,15,"Prelim: ",0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format(0,2,".",","),0,0,'R');

					$pdf->Ln();
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Miscellaneous Fee:',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($fees['miscellaneous_fee'],2,".",","),0,0,'R');

					$pdf->Cell(30, 15,'');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(130,15,"Midterm: ",0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format(0,2,".",","),0,0,'R');

					$pdf->Ln();
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Other Fees:',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($total_other_fees ,2,".",","),0,0,'R');

					$pdf->Cell(30, 15,'');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(130,15,"Finals (less fixed discount): ",0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format(0,2,".",","),0,0,'R');
					$pdf->Ln();

					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(130,15,'Energy Fee:',0,0,'L');
					$pdf->SetFont('Arial', '', '9');
					$pdf->Cell(120,15,"Php " . number_format($total_energy_fee,2,".",","),0,0,'R');

					$pdf->Cell(30, 15,'');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(185,15,"TOTAL",0,0,'L');
					$pdf->SetFont('Arial', 'B', '9');
					$pdf->Cell(65,15,"Php " . number_format(0 ,2,".",","), 'T',0,'R');
					$pdf->Ln();


					if(count($other_fees) > 0) {
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(250,15,'Laboratory Fee',0,0,'L');
						$pdf->SetFont('Arial', '', '9');
						//$pdf->Ln();
					}

					$scholarship_displayed = !(isset($scholarship));
					$discount_displayed = !(isset($discount));
					$fee_total_displayed = false;
					$discount_details = array();

					if(isset($scholarship) || isset($discount)) {
						//array_push($discount_details, "#####");
						array_push($discount_details, "DISCOUNTS:##########B#####");

						if(isset($scholarship)) {
							$discount_string = $scholarship->description . " (" . $scholarship->percentage . "%)#####" . number_format($computed_scholarship_discount, 2, ".", ",") . "##########";
							array_push($discount_details, $discount_string);
						}

						if(isset($discount)) {
							$discount_string = $discount->description . "#####" . number_format($discount->price, 2, ".", ",") . "##########";
							array_push($discount_details, $discount_string);
						}

						$discount_string = "TOTAL DISCOUNT#####" . number_format($total_discount, 2, ".", ",") . "#####B#####T";
						array_push($discount_details, $discount_string);
					}

					// each loop is a line
					$fee_total_displayed_toggle = false;
					while (
						count($other_fees) > 0
						|| !$fee_total_displayed
						|| count($discount_details) > 0
					) {

						$fee = array_shift($other_fees);

						if (isset($fee)) {
							$arr = explode('_', $fee);
							$feeDetails = null;
							foreach ($hnd_fi->GetFees($arr[0]) as $feeItem) $feeDetails = $feeItem;

							if ($feeDetails->fee_type == LAB_FEE) {
								$pdf->SetFont('Arial', '', '9');
								$pdf->Cell(130,15, '  - ' . $feeDetails->description,0,0,'L');
								$pdf->SetFont('Arial', '', '9');
								$pdf->Cell(120,15,"Php " . number_format($feeDetails->price,2,".",","),0,0,'R');
							}
						} else {
							if(!$fee_total_displayed) {
								$pdf->SetFont('Arial', 'B', '9');
								$pdf->Cell(185,15,'Total Fee:',0,0,'L');
								$pdf->SetFont('Arial', 'B', '9');
								$pdf->Cell(65,15,"Php " . number_format($total,2,".",","),'T',0,'R');
								$fee_total_displayed_toggle = true;
							}
						}

						$disc = array_shift($discount_details);
						if (isset($disc)) {
							$split = explode("#####", $disc);
							$space = (!$fee_total_displayed) ? 30 : 280;
							$pdf->Cell($space, 15, '', 0, 0, '');
							$pdf->SetFont('Arial', $split[2], '9');
							$pdf->Cell(185, 15, $split[0], 0, 0, 'L');
							$pdf->Cell(65, 15, $split[1], $split[3], 0, 'R');
						}


						$pdf->Ln();
						$fee_total_displayed = $fee_total_displayed_toggle;
					}
/*
					foreach ($other_fees as $fee) {
						$arr = explode('_', $fee);
						$feeDetails = null;
						foreach ($hnd_fi->GetFees($arr[0]) as $feeItem)
							$feeDetails = $feeItem;

						if ($feeDetails->fee_type == LAB_FEE) {
							$pdf->SetFont('Arial', '', '9');
							$pdf->Cell(130,15, '  - ' . $feeDetails->description,0,0,'L');
							$pdf->SetFont('Arial', '', '9');
							$pdf->Cell(120,15,"Php " . number_format($feeDetails->price,2,".",","),0,0,'R');
							$pdf->Ln();
						}
					}

					if(isset($scholarship)){
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(130,15,"Less {$scholarship->percentage}%:",0,0,'L');
						$pdf->SetFont('Arial', '', '9');
						$pdf->Cell(120,15,"Php " . number_format($fees['tuition_fee'] * ($scholarship->percentage/100) ,2,".",","),0,0,'R');
						$pdf->Ln();
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(130,15,'Total Fee:',0,0,'L');
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(120,15,"Php " . number_format($total-$fees['tuition_fee'] * ($scholarship->percentage/100),2,".",","),'T',0,'R');
					} else {
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(130,15,'Total Fee:',0,0,'L');
						$pdf->SetFont('Arial', 'B', '9');
						$pdf->Cell(120,15,"Php " . number_format($total,2,".",","),'T',0,'R');
					}
*/
				}

			$pdf->Ln(); $pdf->Ln();
			$pdf->SetFont('Arial', '', '8');
			$pdf->MultiCell(0,9+4, "           In consideration of my admission to Universal Colleges of Parañaque (UCP), I hereby comply and pledge to fully settle my accounts on the schedule stipulated by this institution which I am enrolled.");
			$pdf->Ln();
			$pdf->Cell(300,16, ""); $pdf->MultiCell(170,16, "SIGNATURE OVER PRINTED NAME", 'T', "C");


			$pdf->Output();

		} else {
			$_SESSION['error'] = array('Record not found.');
			header("Location: enlistment-search-student.php");
			exit();
		}
	} else {
		$_SESSION['error'] = array('Record not found.');
		header("Location: enlistment-search-student.php");
		exit();
	}

	$conn->Close();
?>
