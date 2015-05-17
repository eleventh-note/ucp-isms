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
		$PagePrivileges->AddPrivilege("Finance - Administrator");
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
	
	if(isset($_GET['id'])){
		$id = $_GET['id'];
		$records = $std->GetSprs($id);
		$balance = $hnd_fin->GetTotalBalance($id);
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
			$dict_gen_fees = $hnd_fin->GetGenFees();
			
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

			function CheckStatusType(targetStatus, targetType){
				result = true;
				if(document.getElementById(targetStatus).value == -1){ result = false; }
				if(document.getElementById(targetType).value== -1) { result = false; }
				return result;
			}
			
			function GetStatusType(targetStatus, targetType){
				result = "status=";
				result = result + document.getElementById(targetStatus).value
				result = result + "&type=";
				result = result + document.getElementById(targetType).value;
				return result;
			}
		</script>
	</head>
	<body id="finance">
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
									echo "<span class=\"Highlight\">Finance Administration &raquo; Student Payments</span>";					
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
							<form action="finance-student_payments-process.php" method="post">
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
											<td class="label">Balance</td>
											<td class="input">: 
												<span class="magnify3">
													<?php echo $balance; ?>
												</span>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Fee Type</td>
											<td class="input">: 
												<select name="fee" id="oFee" class="small">
													<option value="-1"></option>
													<?php
														foreach($dict_gen_fees as $key => $item){
															echo "<option value=\"{$item->fee_id}\">{$item->description}</option>";
														}
													?>
												</select>
											</td>
										</tr>
										<tr class="info">
											<td class="label">Amount</td>
											<td class="input">:
												<input type="text" name="amount" />
											</td>
										</tr>
									</table>								
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
											<td>
												
												<input type="button" class="button" name="cancel" value="Back" onclick="window.location='finance-student_payments-search.php'" />
												<input type="submit" class="button" name="add_payment" value="Add Payment" />
												<?php
													if(isset($_SESSION['payments'])){
														if(sizeof($_SESSION['payments']) > 0){
															echo "<input type=\"submit\" class=\"button\" name=\"post_payment\" value=\"Post Payment & Get Receipt\" />";
														}
													}
												?>
												<?php //<input type="submit" class="button" name="college_save" value="Add" /> ?>
											</td>
										</tr>
									</table>
									<div class="table">
										<?php 
											if(isset($_SESSION['payments'])){
												$ctr = 0;
												echo "<table class=\"default\" cellspacing=\"0\">";
													echo "<thead>";
														echo "<th class=\"Count\">";
															echo "No.";
														echo "</th>";
														echo "<th>";
															echo "Description";
														echo "</th>";
														echo "<th>";
															echo "Amount";
														echo "</th>";
														echo "<th>";
															echo "";
														echo "</th>";
													echo "</thead>";
													//# UPDATE ROWS WITH PAYMENT
													foreach($_SESSION['payments'] as $key => $p){
														$ctr++;
														if($ctr % 2 == 0){
															echo "<tr class=\"even\">";
														} else {
															echo "<tr class=\"odd\">";
														}
															echo "<td>";
																echo $ctr;
															echo "</td>";
															echo "<td>{$dict_gen_fees[$p['fee']]->description}</td>";
															echo "<td>Php " . number_format($p['amount'],2,".",",") . "</td>";
															echo "<td><a onclick=\"result = confirm('Continue deleting payment from list? Click OK to continue.');\" href=\"finance-student_payments-process.php?id={$id}&key={$key}\">Delete</a></td>";
														echo "</tr>";
													}
													if($ctr == 0){
														echo "<tr><td colspan=\"4\">No payments in the list.</td></tr>";
													}
												echo "</table>";
											}
										
										?>
									</div>
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