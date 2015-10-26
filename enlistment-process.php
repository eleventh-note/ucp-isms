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
	require_once(CLASSLIST . "stdnts.inc.php");
	require_once(CLASSLIST . "enl.inc.php");
	require_once(CLASSLIST . "fin.inc.php");

//::END OF 'CONFIGURATION'

	//# General Variables - shown in all documents for easy modification
		$title = SCHOOL_NAME . " Integrated School Management System";
		$keywords = "";
		$description = "";
		$autdor = "";
		$robots="noindex,nofollow";

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
	$hnd_fin = new FinanceManager($conn);
	$std = new StudentManager($conn);
	$hnd_enl = new EnlistmentManager($conn);

	$dict_scholarships = $hnd_fin->GetScholarshipsByKey();
	$dict_discounts = $hnd_fin->GetDiscountsByKey();

	//Sem & Year
	$semesters = $hnd_sc->GetActiveSemester();
	$semester = $semesters[0];
	$school_years = $hnd_sc->GetActiveSchoolYear();
	$school_year = $school_years[0];

	if(isset($_POST['student_id'])){
		$id = (int) $_POST['student_id'];
		$sy = (int) $_POST['sy'];
		$sem = (int) $_POST['sem'];
		$scholarship = (int) $_POST['scholarship'];
		$discount = (int) $_POST['discount'];
		$other_fees = array();
		if (isset($_POST['other_fees'])) {
			$other_fees = $_POST['other_fees'];
		};

		$total_other_fees = 0;
		$other_fees_value = array();
		$other_fees_id = array();

		if (count($other_fees) > 0) {
			foreach ($other_fees as $fee) {
				$arr = explode('_', $fee);
				$other_fees_id[] = $arr[0];
				$other_fees_value[] = (float) $arr[1];
				$total_other_fees += (float) $arr[1];
			}
		}

		//#ADD ENLISTMENT
		if(isset($_POST['add_enlist'])){
			$loading_status = (int) $_POST['loading_status'];
			$payment_mode = (int) $_POST['payment_mode'];
			$lecFee = (int) $_POST['tuition_fee_lec'];
			$labFee = (int) $_POST['tuition_fee_lab'];
			$orNumber = $_POST['or_number'];

			$_SESSION['payment_mode'] = $payment_mode;
			$_SESSION['loading_status'] = $loading_status;
			$_SESSION['scholarship'] = $scholarship;
			$_SESSION['lecFee'] = $lecFee;
			$_SESSION['labFee'] = $labFee;
			$_SESSION['other_fees'] = implode('_', $other_fees_id);
			$_SESSION['or_number'] = $orNumber;

			$not_unique_ctr = 0;
			if(isset($_POST['for_enlistment'])){
				$is_unique = true;
				foreach($_POST['for_enlistment'] as $item){
					$tmp = explode("_",$item); //0 is section_subject_id; 1 is curriculum_subject_id
					//check if unique here
					if(isset($_SESSION['enlisted_subjects'])){
						foreach($_SESSION['enlisted_subjects'] as $enl){
							$tmp2 = explode("_", $enl);
							if($tmp[1] == $tmp2[1]){
								$is_unique = false;
								$not_unique_ctr++;
								break;
							}
						}
					}

					//then add here
					if($is_unique==true){
						$_SESSION['enlisted_subjects'][] = $item;
					}
				}

				if($not_unique_ctr > 0){
					$_SESSION['error'] = array("{$not_unique_ctr} subject(s) was/were not added. No two same subjects can be added for enlistment.");
				}

				//inform the session that something was changed
				$_SESSION['mode'] = 'edited';

				header("Location: enlistment-view.php?id={$id}");
				exit();
			} else {
				$_SESSION['error'] = array('There were no subjects selected for enlistment.');
				header("Location: enlistment-view.php?id={$id}");
				exit();
			}
		}
		//#REMOVE ENLISTMENT
		if(isset($_POST['remove_enlist'])){
			$loading_status = (int) $_POST['loading_status'];
			$payment_mode = (int) $_POST['payment_mode'];
			$lecFee = (int) $_POST['tuition_fee_lec'];
			$labFee  = (int) $_POST['tuition_fee_lab'];
			$orNumber = $_POST['or_number'];

			$_SESSION['payment_mode'] = $payment_mode;
			$_SESSION['loading_status'] = $loading_status;
			$_SESSION['scholarship'] = $scholarship;
			$_SESSION['discount'] = $discount;
			$_SESSION['lecFee'] = $lecFee;
			$_SESSION['labFee'] = $labFee;
			$_SESSION['other_fees'] = implode('_', $other_fees_id);
			$_SESSION['or_number'] = $orNumber;

			if(isset($_POST['remove_enlistment'])){
				foreach($_SESSION['enlisted_subjects'] as $key => $item){
					//then remove here
					foreach($_POST['remove_enlistment'] as $rem){
						$tmp = explode("_", $item);
						if($tmp[0] == $rem){
							unset($_SESSION['enlisted_subjects'][$key]);
						}
					}
				}

				$_SESSION['enlisted_subjects'] = array_values($_SESSION['enlisted_subjects']);
				//inform the session that something was changed
				$_SESSION['mode'] = 'edited';

				header("Location: enlistment-view.php?id={$id}");
				exit();
			} else {
				$_SESSION['error'] = array('There were no subjects selected to be removed from enlistment.');
				header("Location: enlistment-view.php?id={$id}");
				exit();
			}
		}

		//#CANCEL ENLISTMENT
		if(isset($_POST['cancel_enlist'])){
			unset($_SESSION['enlisted_subjects']);
			unset($_SESSION['mode']);
			header("Location: enlistment-search-student.php");
			exit();
		}

		//#SAVE ENLISTMENT
		if(isset($_POST['save_enlist'])){
			$loading_status = (int) $_POST['loading_status'];
			$payment_mode = (int) $_POST['payment_mode'];
			$lecFee = (int) $_POST['tuition_fee_lec'];
			$labFee = (int) $_POST['tuition_fee_lab'];
			$s = (int) $_POST['scholarship'];
			$d = (int) $_POST['discount'];
			$orNumber = $_POST['or_number'];

			$std_fees = $hnd_fin->GetStdFees();

			//Get Registration Fee
			foreach($std_fees as $fee){
				if(strpos($fee->description, "Registration Fee")){
					$fees['registration_fee'] = (float) $fee->price;
				}
			}

			$semesters = $hnd_sc->GetActiveSemester();
			$semester = $semesters[0];
			$school_years = $hnd_sc->GetActiveSchoolYear();
			$school_year = $school_years[0];

			//List of Subjects
			$section_subjects = $hnd_sh->GetSectionSubjectsByKey(null, $semester->semester_id, $school_year->year_id);

			$all_units = 0;
			$total_units = 0;
			$lec_units = 0;
			$lab_units = 0;
			$total_half_priced_subjects = 0;

			$enlisted_subjects = array();
			if(isset($_SESSION['enlisted_subjects'])){
				$enlisted_subjects = $_SESSION['enlisted_subjects'];
				if(sizeof($enlisted_subjects) > 0){
					$total_units = 0;
					foreach($section_subjects as $item){
						//#Variables for containing previously outputted data
						foreach($enlisted_subjects as $enl){
							$tmp = explode("_", $enl);
							if($item->section_subject_id==$tmp[0]){
								$total_units += $item->units + $item->unitsLab;
							}

							if ($item->curriculum_subject_id == $tmp[1] && $item->section_subject_id == $tmp[0]) {
								$unit = $item->units;

								if((int) $tmp[2] === 1) {
									$unit *= .5;
									$total_half_priced_subjects += $unit * $lecFee;
								}

								$all_units += $unit + $item->unitsLab;
								$lec_units += $unit;
								$lab_units += $item->unitsLab;
							}
						}
					}
				}
			}

			$fees['registration_fee'] = (float) $hnd_fin->GetRegistrationFee();
			//Tuition Fee
			$fees['tuition_fee'] = (float) $hnd_fin->ComputeTuitionFee_Cash($lec_units, $lecFee, $lab_units, $labFee);
			//Miscellaneous Fee
			$fees['miscellaneous_fee'] = (float) $hnd_fin->GetMiscFee();
			//Entrance Fee = Registration + Miscellaneous
			$fees['entrance_fee'] = (float) $fees['miscellaneous_fee'] + $fees['registration_fee'];
			//Installment Fee
			$fees['installment_fee'] = (float) $hnd_fin->GetInstallmentFee();

			define("INSTALLMENT", 1);
			define("CASH", 2);
			define("FULL_LOAD", 1);
			define("PARTIAL_LOAD", 2);

			$_SESSION['payment_mode'] = $payment_mode;
			$_SESSION['loading_status'] = $loading_status;
			$_SESSION['scholarship'] = $scholarship;
			$_SESSION['discount'] = $discount;
			$_SESSION['lecFee'] = $lecFee;
			$_SESSION['labFee'] = $labFee;
			$_SESSION['other_fees'] = implode('_', $other_fees_id);
			$_SESSION['or_number'] = $orNumber;

			$sch = null;
			$dsc = null;
			$discount_total = 0;
			$computed_scholarship = 0;

			if($s > 0){ $sch = $dict_scholarships[$s]; }
			if($d > 0){ $dsc = $dict_discounts[$d]; }

			$total = 0;
			//## CONVERT ALL TO NUMBER FORMAT
			if($payment_mode==INSTALLMENT && ($loading_status == FULL_LOAD || $loading_status == PARTIAL_LOAD)){

				$total = $fees['registration_fee'] + $total_other_fees + $fees['miscellaneous_fee'] + $fees['tuition_fee'];
				$tpercentage = 0;
				if(isset($sch)){ $tpercentage += $sch->percentage; }
				$discount_total = (isset($dsc)) ? $dsc->price : 0;

				$total = $total * 1.05 - $discount_total - ($fees['tuition_fee'] * ($tpercentage/100));

			} elseif($payment_mode==CASH && ($loading_status == FULL_LOAD || $loading_status == PARTIAL_LOAD)){

				$total = $fees['registration_fee'] + $total_other_fees + $fees['miscellaneous_fee'] + $fees['tuition_fee'];
				$cash_discount = ($fees['tuition_fee'] - $total_half_priced_subjects) * .05;

				$tpercentage = 0;
				if(isset($sch)){ $tpercentage += $sch->percentage; }
				$discount_total = (isset($dsc)) ? $dsc->price : 0;

				$total = $fees['registration_fee'] + $total_other_fees + $fees['miscellaneous_fee'] + ($fees['tuition_fee'] * (1-$tpercentage/100)) - $cash_discount;
				$total -= $discount_total;
			}

			if(sizeof($_SESSION['enlisted_subjects']) > 0){

				//$total = sizeof($_SESSION['enlisted_subjects']);
				$ctr = 0;

				$data = $hnd_enl->GetStudentEnlistmentDetails($sy, $sem, $id);

				if(sizeof($data) == 0){

					if($hnd_enl->EnlistStudent($id, $sy, $sem, $payment_mode, $loading_status, $scholarship, $discount, $lecFee, $labFee, implode('_', $other_fees_id), $orNumber) == true){
						//remove subjects first
						$hnd_enl->RemoveEnlistment($id, $sem, $sy);

						$hnd_fin->AddBill($id, 21, $total, $hnd_enl->enlistment_id, $school_year->year_id, $semester->semester_id); //Add Total to the system
						foreach($_SESSION['enlisted_subjects'] as $item){
							$tmp = explode("_", $item);
							if($hnd_enl->EnlistSubject($id, $tmp[0], $tmp[1], $sy, $sem, $hnd_enl->enlistment_id) == true){
								$ctr++;
							}
						}
					} else {
						$_SESSION['error'] = $hnd_enl->error;
						header("Location: enlistment-view.php?id={$id}");
						exit();
					}
				} else {
					if($hnd_enl->UpdateEnlistedStudent($id, $sy, $sem, $payment_mode, $loading_status, $scholarship, $discount, $lecFee, $labFee, implode('_', $other_fees_id), $orNumber) == true){
						$enlistment_id = -1;
						foreach($data as $d){
							$enlistment_id = $d->enlistment_id;
						}

						//remove subjects first
						$hnd_enl->RemoveEnlistment($id, $sem, $sy);

						$resultOfUpdate = $hnd_fin->UpdateBill($enlistment_id, $total, $sy, $sem);

						foreach($_SESSION['enlisted_subjects'] as $item){
							$tmp = explode("_", $item);
							if($hnd_enl->EnlistSubject($id, $tmp[0], $tmp[1], $sy, $sem, $enlistment_id) == true){

								$ctr++;
							}
						}
					} else {
						$_SESSION['error'] = $hnd_enl->error;
					}
				}

				if($ctr > 0){
					unset($_SESSION['mode']);
					$_SESSION['success'] = "Student enlistment successfully saved.";
					header("Location: enlistment-view.php?id={$id}");
					exit();
				} else {
					$_SESSION['error'][] = "No new subject has been added to the current student enlistment record.";
					header("Location: enlistment-view.php?id={$id}");
					exit();
				}
			}
		}
	} else {
		$_SESSION['error'] = array('Record not found.');
		header("Location: enlistment-search-student.php");
		exit();
	}

	$conn->Close();
?>
