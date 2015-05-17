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
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "emp.inc.php");
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
		$PagePrivileges->AddPrivilege("Employment - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd = new FacultyManager($conn);
	$hnd_c = new CollegeManager($conn);
	$hnd_e = new EmployeeManager($conn);
		
	$employees = $hnd_e->GetUnassignedEmployees();
	$ranks = $hnd->GetFacultyRanks();
	$statuses = $hnd->GetFacultyStatuses();
	$colleges = $hnd_c->GetColleges();
	
	$conn->Close();
	
	if(isset($_SESSION['rank'])){ $rank = $_SESSION['rank']; unset($_SESSION['rank']); }
	if(isset($_SESSION['status'])){ $status = $_SESSION['status']; unset($_SESSION['status']); }
	if(isset($_SESSION['college'])){ $college = $_SESSION['college']; unset($_SESSION['college']); }
	if(isset($_SESSION['employee'])){ $employee_id = $_SESSION['employee']; unset($_SESSION['employee']); }
	
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
	
	//# Otder Javascript Loaded Here
	//Replace Timer Below witd script for javascript logout`
	//###TIMER###
?>
	</head>
	<body id="employment">
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
								<span class="Highlight">Faculty Administration &raquo; Manage Faculties&raquo; Add Faculty</span> 
							</h1>
							<p class="">Please complete the information below.</p>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>
							<form action="employment-faculty-process.php" method="post">
								<hr class="form_top"/>
								<div class="table_form">
									<table class="form" cellspacing="0">
										<tr>
											<td class="label">Faculty Name</td>
											<td class="input">: 
												<select name="faculty" class="default">
													<option value="-1"></option>
													<?php
														foreach($employees as $employee){
															if(isset($employee_id)){
																if($employee_id == $employee->employee_id){
																	echo "<option value=\"{$employee->employee_id}\" selected=\"selected\">";
																	echo $employee->last_name . ", " . $employee->first_name . " " . $employee->middle_name;
																	echo "</option>";
																} else {
																	echo "<option value=\"{$employee->employee_id}\">";
																	echo $employee->last_name . ", " . $employee->first_name . " " . $employee->middle_name;
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$employee->employee_id}\">";
																	echo $employee->last_name . ", " . $employee->first_name . " " . $employee->middle_name;
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="label">Faculty Rank</td>
											<td class="input">: 
												<select name="rank" class="default">
													<option value="-1"></option>
													<?php
														foreach($ranks as $item){
															if(isset($rank)){
																if($item->rank_id == $rank){
																	echo "<option value=\"{$item->rank_id}\" selected=\"selected\">";
																		echo $item->description;
																	echo "</option>";
																} else {
																	echo "<option value=\"{$item->rank_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$item->rank_id}\">";
																	echo $item->description;
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="label">Faculty Status</td>
											<td class="input">: 
												<select name="status" class="default">
													<option value="-1"></option>
													<?php
														foreach($statuses as $item){
															if(isset($status)){
																if($item->status_id == $status){
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
										<tr>
											<td class="label">College Department</td>
											<td class="input">: 
												<select name="college" class="default">
													<option value="-1"></option>
													<?php
														foreach($colleges as $item){
															if(isset($college)){
																if($item->college_id == $college){
																	echo "<option value=\"{$item->college_id}\" selected=\"selected\">";
																		echo $item->description;
																	echo "</option>";
																} else {
																	echo "<option value=\"{$item->college_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$item->college_id}\">";
																	echo $item->description;
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
									</table>						
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Back" onclick="window.location='employment-faculty.php';"/>
												<input type="submit" class="button" name="faculty_save" value="Assign" />
											</td>
										</tr>
									</table>
								</div>
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