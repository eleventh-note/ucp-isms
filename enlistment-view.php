<?php
/* #-------------------------------------------------
	 #
	 #	Description:	Template for 00 Default Layout
	 #	Autdor:		Algefmarc A. L. Almocera
	 #	Date Started:	May 30, 2011
	 #	Last Modified:	June 8, 2011
	 #
	 #	Modifications:
	 #	20141015 - Add Tuition Entry for Lec and Lab; Add List of Laboratories
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
	require_once(CLASSLIST . "fin-other-fees.inc.php");

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
	$std = new StudentManager($conn);
	$hnd_enl = new EnlistmentManager($conn);
	$hnd_fin = new FinanceManager($conn);
	$hnd_fin_other_fees = new FinOtherFees($conn);

	$_lecFee = "";
	$_labFee = "";
	$_orNumber = "";
	$_otherFees = "";

	if(isset($_GET['id'])){
		$id = $_GET['id'];
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
			$dict_loading_status = $hnd_enl->GetLoadingStatusesByKey();
			$dict_payment_types = $hnd_fin->GetPaymentTypesByKey();
			$dict_scholarships = $hnd_fin->GetScholarshipsByKey();
			$dict_discounts = $hnd_fin->GetDiscountsByKey();

			//Sem & Year
			$semesters = $hnd_sc->GetActiveSemester();
			$semester = $semesters[0];
			$school_years = $hnd_sc->GetActiveSchoolYear();
			$school_year = $school_years[0];

			$student_number = $record->student_no;
			$tmp = $dict_school_years[$background->entry_sy];

			// $school_year = "S.Y. " . $tmp->start . " - " . $tmp->end;
			// $semester = $dict_semesters[$background->entry_semester]->description;

			$student_name = $record->last_name . ", " . $record->first_name . " " . $record->middle_name;

			//List of Subjects
			$section_subjects = $hnd_sh->GetSectionSubjectsByKey(null, $semester->semester_id, $school_year->year_id);

			//List of Curriculum Subjects
			//$curriculum_subjects = $hnd_cu->GetAllSubjectsByCode();

			//##FILL DATE WITH CURRENTLY ENLISTED SUBJECTS
			$is_old = false;
			$other_fees = $hnd_fin->GetMixedOtherFees();


			if(!isset($_SESSION['enlisted_subjects']) && !isset($_SESSION['mode'])){
				$subs = $hnd_enl->GetStudentEnlistmentSubjects($id, $school_year->year_id, $semester->semester_id);
				$data = $hnd_enl->GetStudentEnlistmentDetails($school_year->year_id, $semester->semester_id, $id);

				foreach($data as $d){
					$_payment_type = $d->payment_type;
					$_loading_status = $d->loading_status;
					$_scholarship = $d->scholarship;
					$_discount = $d->discount;
					$_labFee = $d->labFee;
					$_lecFee = $d->lecFee;
					$_otherFees = $d->otherFees;
					$_orNumber = $d->orNumber;
				}

				$is_old = true;
				foreach($subs as $enl){
					$_SESSION['enlisted_subjects'][] = $enl->section_subject . "_" . $enl->curriculum_subject . "_" . $enl->isHalf;
				}
			}

			if(isset($_SESSION['loading_status']) && isset($_SESSION['payment_mode']) ){
				$_payment_type = (int) $_SESSION['payment_mode'];
				$_loading_status = (int) $_SESSION['loading_status'];
				$_scholarship = (int) $_SESSION['scholarship'];
				$_discount = (int) $_SESSION['discount'];
				$_lecFee = (int) $_SESSION['lecFee'];
				$_labFee = (int) $_SESSION['labFee'];
				$_otherFees = $_SESSION['other_fees'];
				$_orNumber = $_SESSION['or_number'];

				unset($_SESSION['payment_mode']);
				unset($_SESSION['loading_status']);
				unset($_SESSION['scholarship']);
				unset($_SESSION['discount']);
				unset($_SESSION['lecFee']);
				unset($_SESSION['labFee']);
				unset($_SESSION['other_fees']);
				unset($_SESSION['or_number']);
			}

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


	//##### PROCESS ERROR or SUCCESS
	if(isset($_SESSION['error'])){
		$error = $_SESSION['error'];
		unset($_SESSION['error']);
	}
	if(isset($_SESSION['success'])){
		$success = $_SESSION['success'];
		unset($_SESSION['success']);
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xml:lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
	<head>
<?php
//::START OF 'DEFAULT HEAD CONFIG'
	require_once("_system/_config/head_config.php");
//::END OF 'DEFAULT HEAD CONFIG'

	//# Otder CSS Loaded Here
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "home.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "verticalnav.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "columns.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "content.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "actions.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "tables.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "tweaks.css\" />";

	//# Other Javascript Loaded Here
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_PLUGINS . "jquery.mini.js" . "\"></script>";
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_DEFAULT . "scroll.js" . "\"></script>";
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_DEFAULT . "general.js" . "\"></script>";

	//Replace Timer Below witd script for javascript logout`
	//###TIMER###
?>
		<script type="text/javascript">
			function getSelectValue(target){
				target = document.getElementById(target);
				content = target.value;

				return content;
			}

			function redirectTo(url, extension){
				window.location = url + extension;
			}

			function toggleCheckBox(target, row){
				target = document.getElementById(target);
				target.checked=!target.checked;
			}

			function UncheckAll(info){
				arrInfo = info.split("-");

				for(i=0; i < arrInfo.length; i++){
					document.getElementById("for_enlistment_" + arrInfo[i]).checked=false;;
				}
			}

			function CheckAll(info){
				arrInfo = info.split("-");

				for(i=0; i < arrInfo.length; i++){
					document.getElementById("for_enlistment_" + arrInfo[i]).checked=true;;
				}
			}

			function CheckStatusType(targetStatus, targetType, lec, lab){
				result = true;
				if(document.getElementById(targetStatus).value == -1){ result = false; }
				if(document.getElementById(targetType).value== -1) { result = false; }
				if(document.getElementById(lec).value == '') { result = false; }
				if(document.getElementById(lab).value == '') { result = false; }

				return result;
			}

			function GetStatusType(targetStatus, targetType, s, d, lec, lab, other_fees){
				result = "status=";
				result = result + document.getElementById(targetStatus).value
				result = result + "&type=";
				result = result + document.getElementById(targetType).value;
				result = result + "&s=";
				result = result + document.getElementById(s).value;
				result = result + "&d=";
				result = result + document.getElementById(d).value;
				result = result + "&lec=";
				result = result + document.getElementById(lec).value;
				result = result + "&lab=";
				result = result + document.getElementById(lab).value;
				result = result + "&other_fees=";

				var selected_other_fees = '';
				var select = document.getElementById(other_fees);
				for (var i = 0, o; i < select.options.length; i++) {
					o = select.options[i];
					if (o.selected == true) {
						if (selected_other_fees == '') {
							selected_other_fees = o.value;
						} else {
							selected_other_fees += ',' + o.value;
						}
					}
				}
				result = result + selected_other_fees;
				return result;
			}
		</script>
	</head>
	<body id="enlistment">
		<div id="container">
			<div id="header">
				<?php require_once("_system/main/banner.inc.php"); ?>
				<?php require_once("_system/main/dashboard.inc.php"); ?>
			</div><?php //end of header ?>

			<div id="body">
				<?php
					//Replace witd error_handling script below
					//###ERROR SCRIPT###
				?>
				<div class="content">
					<div class="column" id="column-first">
						<?php require_once("_system/main/mainmenu.inc.php"); ?>
					</div>
					<div class="column" id="column-second">
						<div class="inner">
							<h1>
								<?php
									echo "<span class=\"Highlight\">Student Enlistment &raquo; View Enlistment Record</span>";
								?>
							</h1>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>

							<form action="enlistment-process.php" method="post">
								<input type="hidden" name="student_id" value="<?php echo $id; ?>" />
								<input type="hidden" name="sy" value="<?php echo $school_year->year_id; ?>" />
								<input type="hidden" name="sem" value="<?php echo $semester->semester_id; ?>" />
								<hr class="form_top"/>
								<div class="table_form">
									<table class="form" cellspacing="0">
										<tr class="info">
											<td class="label">Student Name:</td>
											<td class="input">:
												<span class="magnify1">
													<?php echo $student_name; ?>
												</span>
											</td>
										</tr>
										<tr class="info">
											<td class="label"></td>
											<td class="input">:
												<?php echo $semester->description . " / S.Y. " . $school_year->start . " - " . $school_year->end; ?>
											</td>
										</tr>
										<tr class="info">
											<td class="label"></td>
											<td class="input">:
												<span class="magnify3">
													<?php echo $student_number . " / " . $dict_courses[$background->course]->code; ?>
												</span>
											</td>
										</tr>
										<tr class="info">
											<td class="label">OR Number:</td>
											<td class="input" style="">:
												<input type="text" id="or_number" name="or_number" value="<?php echo $_orNumber; ?>" class="small" />
											</td>
										</tr>
										<tr class="info">
											<td class="label">Loading Status</td>
											<td class="input">:
												<select name="loading_status" id="oLoadingStatus" class="small">
													<option value="-1"></option>
													<?php
														foreach($dict_loading_status as $key => $item){
															if(isset($_loading_status)){
																if($item->status_id == $_loading_status){
																	echo "<option value=\"{$item->status_id}\" selected=\"selected\">";
																		echo $item->description;
																	echo "</option>";
																} else {
																	echo "<option value=\"{$item->status_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$item->status_id}\">";
																	echo $item->description;
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Payment Mode</td>
											<td class="input">:
												<select name="payment_mode" id="oPaymentMode" class="small">
													<option value="-1"></option>
													<?php
														foreach($dict_payment_types as $key => $item){
															if(isset($_payment_type)){
																if($item->type_id == $_payment_type){
																	echo "<option value=\"{$item->type_id}\" selected=\"selected\">";
																		echo $item->description;
																	echo "</option>";
																} else {
																	echo "<option value=\"{$item->type_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$item->type_id}\">";
																	echo $item->description;
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Tuition Fee</td>
											<td class="input" style="">:
												<b>Lec</b> <input type="text" id="tuition_fee_lec" name="tuition_fee_lec" style="width: 50px" value="<?php echo $_lecFee; ?>" />
												&nbsp;&nbsp;
												<b>Lab</b> <input type="text" id="tuition_fee_lab" name="tuition_fee_lab" style="width: 50px" value="<?php echo $_labFee; ?>" />

											</td>
										</tr>
										<tr class="info">
											<td class="label" style="vertical-align: top">Other Fees</td>
											<td class="input" style="vertical-align: top">:
											  <select name="other_fees[]" id="other_fees" class="large" multiple="true" size="8">
													<?php


														//if (isset($_otherFees)) {

															$arr_other_fees = explode('_', $_otherFees);

															if ($other_fees) {
																foreach ($other_fees as $fee) {
																	$markup = '<option ';
																	$markup .= (in_array($fee->fee_id, $arr_other_fees)) ? 'selected="selected" ' : '';
																	$markup .= 'value="' . $fee->fee_id . '_' . $fee->price . '">' . $fee->description . ' - ' . number_format($fee->price,2 ,".",",") . '</option>';
																	echo $markup;
																}
															}
														//}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Scholarships</td>
											<td class="input" style="">:
												<select name="scholarship" id="oScholarship" class="extra-large">
													<option value="-1"></option>
													<?php
														foreach($dict_scholarships as $key => $item){
															if(isset($_scholarship)){
																if($item->discount_id == $_scholarship){
																	echo "<option value=\"{$item->discount_id}\" selected=\"selected\">";
																		echo $item->description . " ({$item->percentage}%)";
																	echo "</option>";
																} else {
																	echo "<option value=\"{$item->discount_id}\">";
																		echo $item->description . " ({$item->percentage}%)";
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$item->discount_id}\">";
																	echo $item->description . " ({$item->percentage}%)";
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Discounts</td>
											<td class="input" >:
												<select name="discount" id="oDiscount" class="extra-large">
													<option value="-1"></option>
													<?php
														foreach($dict_discounts as $key => $item){
															if(isset($_discount)){
																if($item->discount_id == $_discount){
																	echo "<option value=\"{$item->discount_id}\" selected=\"selected\">";
																		echo $item->description .  " [" . number_format($item->price, 2 ,".",",") . "]";
																	echo "</option>";
																} else {
																	echo "<option value=\"{$item->discount_id}\">";
																		echo $item->description .  " [" . number_format($item->price, 2 ,".",",") . "]";
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$item->discount_id}\">";
																	echo $item->description .  " [" . number_format($item->price, 2 ,".",",") . "]";
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
									</table>
								<?php
									echo "<div class=\"table subject_for_enlistment\">";
											echo "<table class=\"curriculum_subjects default\" style=\"margin-top:10px;\" cellspacing=\"0\" title=\"\">";
												echo "<thead><th colspan=\"11\" class=\"year_level\">Enlisted Subjects</th></thead>";
												echo "<thead>";
													echo "<th class=\"Count\"></th>";
													echo "<th class=\"code\">Code</th>";
													echo "<th class=\"code\">Section</th>";
													echo "<th class=\"units\">Lec Units</th>";
													echo "<th class=\"units\">Lab Units</th>";
													echo "<th class=\"day\">Day(s)</th>";
													echo "<th class=\"time\">From</th>";
													echo "<th class=\"time\">To</th>";
													echo "<th class=\"\">Room</th>";
													echo "<th class=\"\">Instructor</th>";
													//echo "<th class=\"Actions\"></th>";
												echo "</thead>";

												//define the odd even tables
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
																		if($ctr % 2 == 0){
																			echo "<tr class=\"even\" title=\"Edit schedule for [{$item->code}] {$item->subject}\" onclick=\"toggleCheckBox('remove_enlistment_{$item->section_subject_id}',this)\" />"; // onclick=\"window.location='schedules-section-set_schedule.php?id={$item->curriculum_subject_id}';\">";
																		} else {
																			echo "<tr class=\"odd\" title=\"Edit schedule for [{$item->code}] {$item->subject}\" onclick=\"toggleCheckBox('remove_enlistment_{$item->section_subject_id}',this)\" />"; //onclick=\"window.location='schedules-section-set_schedule.php?id={$item->curriculum_subject_id}';\">";
																		}

																			//FOR ENLISTMENT
																			echo "<td><input type=\"checkbox\" id=\"remove_enlistment_{$item->section_subject_id}\" name=\"remove_enlistment[]\" value=\"{$item->section_subject_id}\" /></td>";
																			echo "<td>{$item->code}</td>";
																			echo "<td>{$item->section[0]->name}</td>";
																			echo "<td class=\"center\">{$item->units}</td>";
																			echo "<td class=\"center\">{$item->unitsLab}</td>";
																			$total_units += $item->units + $item->unitsLab;
																			//Day
																			echo "<td>";
																				foreach($schedules as $sched){
																					echo $dict_days[$sched->day]->shorthand . "<br/>";
																				}
																			echo "</td>";
																			//From
																			echo "<td>";
																				foreach($schedules as $sched){
																					echo $dict_times[$sched->from]->description . "<br/>";
																				}
																			echo "</td>";
																			//To
																			echo "<td>";
																				foreach($schedules as $sched){
																					echo $dict_times[$sched->to]->description . "<br/>";
																				}
																			echo "</td>";
																			//Room
																			echo "<td>";
																				foreach($schedules as $sched){
																					if($prev_room != $dict_rooms[$sched->room]->description){
																						echo $dict_rooms[$sched->room]->description . "<br/>";
																						$prev_room = $dict_rooms[$sched->room]->description;
																					} else {
																						echo "<br/>";
																					}
																				}
																			echo "</td>";
																			//Instructor
																			echo "<td>";
																				foreach($schedules as $sched){
																					$fac = $dict_faculties[$sched->instructor]->employee;
																					$name = $fac->last_name . ", " . $fac->first_name . " " . $fac->middle_name;
																					if($prev_instructor != $name){
																						echo $name;
																						echo "<br/>";
																						$prev_instructor = $name;
																					} else {
																						echo "<br/>";
																					}
																				}
																			echo "</td>";
																			//echo "<td class=\"Actions\">";
																				//echo "<a href=\"schedules-section-process.php?action=delete&id={$item->curriculum_subject_id}\" onclick=\"return confirm('Delete s from curriculum? Click OK to continue.')\">Delete</a>";
																			//echo "</td>";
																		echo "</tr>";
																	}
																}//end of enl=section_subject_id
															}//end of for each enlisted subject
														}
														echo "<thead><th colspan=\"3\" style=\"text-align: right;\">Total Units:</th><th colspan=\"1\">{$total_units}</th><th colspan=\"6\"></th></thead>";
													}
												} else {
												}
												if($ctr == 0){
													echo "<tr class=\"odd\" class=\"reminder\"><td colspan=\"10\">No subjects enlisted. </td></tr>";
												}
												echo "<thead>";
													echo "<th colspan=\"9\">";
														echo "<input type=\"button\" class=\"button\" value=\"View Enlisted Students\" onclick=\"window.location='enlistment.php'\"/>";
														echo "<input type=\"submit\" name=\"remove_enlist\" class=\"button\" value=\"Remove Selected\" />";
														if($is_old == false && isset($_SESSION['mode'])){
															echo "<input type=\"submit\" name=\"cancel_enlist\" class=\"button\" value=\"Cancel Enlistment\" onclick=\"return confirm('Cancel enlistment? Click OK to continue.'); \" />";
														}

														if(isset($_SESSION['enlisted_subjects'])){// && isset($_SESSION['mode'])){
															if(sizeof($_SESSION['enlisted_subjects']) > 0){
																echo "<input type=\"submit\" name=\"save_enlist\" class=\"button\" value=\"Save Enlistment\" />";
															}
														}
														//echo "<br/>";
														echo "<img class=\"icon\" style=\"margin-bottom:-8px;\" src=\"";
														echo $DIR_IMAGE_DEFAULT . "icons/file_pdf.png\"";
														echo "/>";
														echo "<input type=\"button\" name=\"cancel_enlist\" class=\"button\" value=\"Get Registry Form\" onclick=\"if(CheckStatusType('oLoadingStatus','oPaymentMode', 'tuition_fee_lec', 'tuition_fee_lab', 'other_fees')==true){ window.open('enlistment-registry_form.php?id={$id}&' + GetStatusType('oLoadingStatus','oPaymentMode','oScholarship','oDiscount','tuition_fee_lec','tuition_fee_lab', 'other_fees')); } else { alert('Please select Payment Mode, Loading Status, Tuition Fee for Lecture and Laboratory.'); }\" />";
														echo "<input type=\"button\" name=\"cancel_enlist\" class=\"button\" value=\"Get Temp Registry Form\" onclick=\"if(CheckStatusType('oLoadingStatus','oPaymentMode', 'tuition_fee_lec', 'tuition_fee_lab', 'other_fees')==true){ window.open('enlistment-temp_registry_form.php?id={$id}&' + GetStatusType('oLoadingStatus','oPaymentMode','oScholarship','oDiscount','tuition_fee_lec','tuition_fee_lab', 'other_fees')); } else { alert('Please select Payment Mode, Loading Status, Tuition Fee for Lecture and Laboratory.'); }\" />";
													echo "</th>";
												echo "</thead>";
											echo "</table>";

									echo "</div>";

								?>
								<?php
									echo "<div class=\"table subject_for_enlistment\">";
											echo "<table class=\"curriculum_subjects default\" style=\"margin-top:10px;\" cellspacing=\"0\" title=\"\">";
												echo "<thead><th colspan=\"11\" class=\"year_level\">Subject Available for Enlistment</th></thead>";
												echo "<thead>";
													echo "<th class=\"Count\"></th>";
													echo "<th class=\"code\">Code</th>";
													echo "<th class=\"code\">Section</th>";
													echo "<th class=\"units\">Lec Units</th>";
													echo "<th class=\"units\">Lab Units</th>";
													echo "<th class=\"day\">Day(s)</th>";
													echo "<th class=\"time\">From</th>";
													echo "<th class=\"time\">To</th>";
													echo "<th class=\"\">Room</th>";
													echo "<th class=\"\">Instructor</th>";
													//echo "<th class=\"Actions\"></th>";
												echo "</thead>";

												//define the odd even tables
												$ctr = 0;
												$for_enlistment_array = "";
												if(sizeof($section_subjects) > 0){
													$total_units = 0;
													foreach($section_subjects as $item){
														$is_found = false;
														if(isset($_SESSION['enlisted_subjects'])){
															$enlisted_subjects = $_SESSION['enlisted_subjects'];
															foreach($enlisted_subjects as $enl){
																$tmp = explode("_", $enl);
																//check if already added
																if($item->curriculum_subject_id == $tmp[1]){
																	$is_found = true;
																	break;
																}
															}
														}

														if($is_found == false){
															//#Variables for containing previously outputted data
															$prev_day = "";
															$prev_from = "";
															$prev_to = "";
															$prev_room = "";
															$prev_instructor = "";
															$prev_code = "";
															$schedules = $hnd_sh->GetSubjectSchedulesBySubject($item->section_subject_id);
															if(sizeof($schedules) > 0){
																$ctr++;
																if($ctr % 2 == 0){
																	echo "<tr class=\"even\" title=\"Edit schedule for [{$item->code}] {$item->subject}\" onclick=\"toggleCheckBox('for_enlistment_{$item->section_subject_id}',this)\" />"; // onclick=\"window.location='schedules-section-set_schedule.php?id={$item->curriculum_subject_id}';\">";
																} else {
																	echo "<tr class=\"odd\" title=\"Edit schedule for [{$item->code}] {$item->subject}\" onclick=\"toggleCheckBox('for_enlistment_{$item->section_subject_id}',this)\" />"; //onclick=\"window.location='schedules-section-set_schedule.php?id={$item->curriculum_subject_id}';\">";
																}
																	//##UPDATE FOR ENLISTMENT ARRAY
																	$for_enlistment_array .= "{$item->section_subject_id}-";
																	//FOR ENLISTMENT
																	echo "<td><input type=\"checkbox\" id=\"for_enlistment_{$item->section_subject_id}\" name=\"for_enlistment[]\" value=\"{$item->section_subject_id}_{$item->curriculum_subject_id}_{$item->isHalf}\" /></td>";
																	echo "<td>{$item->code}</td>";
																	echo "<td>{$item->section[0]->name}</td>";
																	echo "<td class=\"center\">{$item->units}</td>";
																	echo "<td class=\"center\">{$item->unitsLab}</td>";
																	$total_units += $item->units;
																	//Day
																	echo "<td>";
																		foreach($schedules as $sched){
																			echo $dict_days[$sched->day]->shorthand . "<br/>";
																		}
																	echo "</td>";
																	//From
																	echo "<td>";
																		foreach($schedules as $sched){
																			echo $dict_times[$sched->from]->description . "<br/>";
																		}
																	echo "</td>";
																	//To
																	echo "<td>";
																		foreach($schedules as $sched){
																			echo $dict_times[$sched->to]->description . "<br/>";
																		}
																	echo "</td>";
																	//Room
																	echo "<td>";
																		foreach($schedules as $sched){
																			if($prev_room != $dict_rooms[$sched->room]->description){
																				echo $dict_rooms[$sched->room]->description . "<br/>";
																				$prev_room = $dict_rooms[$sched->room]->description;
																			} else {
																				echo "<br/>";
																			}
																		}
																	echo "</td>";
																	//Instructor
																	echo "<td>";
																		foreach($schedules as $sched){
																			if (isset($dict_faculties[$sched->instructor])) {
																				$fac = $dict_faculties[$sched->instructor]->employee;
																				$name = $fac->last_name . ", " . $fac->first_name . " " . $fac->middle_name;
																				if($prev_instructor != $name){
																					echo $name;
																					echo "<br/>";
																					$prev_instructor = $name;
																				} else {
																					echo "<br/>";
																				}
																			}
																		}
																	echo "</td>";
																	//echo "<td class=\"Actions\">";
																		//echo "<a href=\"schedules-section-process.php?action=delete&id={$item->curriculum_subject_id}\" onclick=\"return confirm('Delete s from curriculum? Click OK to continue.')\">Delete</a>";
																	//echo "</td>";
																echo "</tr>";
															}
														}//end if (enl = section_subject_id)
													}
													//echo "<thead><th colspan=\"3\" style=\"text-align: right;\">Total Units:</th><th colspan=\"1\">{$total_units}</th></thead>";
												}
												if($ctr == 0){
													echo "<tr class=\"odd\" class=\"reminder\"><td colspan=\"10\">No subjects available for enlistment. </td></tr>";
												}
												echo "<thead>";
													echo "<th colspan=\"9\">";
														echo "<input type=\"button\" name=\"add_enlist\" class=\"button\" value=\"Uncheck All\" onclick=\"UncheckAll('{$for_enlistment_array}')\"/>";
														echo "<input type=\"button\" name=\"add_enlist\" class=\"button\" value=\"Check All\" onclick=\"CheckAll('{$for_enlistment_array}')\"/>";
														echo "<input type=\"submit\" name=\"add_enlist\" class=\"button\" value=\"Enlist Selected\" />";
													echo "</th>";
												echo "</thead>";
											echo "</table>";

									echo "</div>";

								?>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
											<td>

											</td>
										</tr>
									</table>
								</div><?php //end TABLE FORM ?>
							</form>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div><?php //end of body ?>
			<div id="footer">
				<?php require_once("_system/main/footer.inc.php"); ?>
			</div><?php //end of footer ?>
		</div>
	</body>
</html>
<?php
	//::START OF 'CLOSING REMARKS'
		//memory releasing and stuffs
	//::END OF 'CLOSING REMARKS'
	//close the connection
	$conn->Close();
?>
