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
	require_once(CLASSLIST . "fclts.inc.php");
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
		$PagePrivileges->AddPrivilege("Users - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd = new User($conn);
	$emp = new EmployeeManager($conn);
	
	$employees = $emp->GetEmployeesWithoutUser();
	$privileges = $hnd->GetPrivileges();
	
	unset($privileges[0]);
	$conn->Close();
	
	//##### PROCESS ERROR or SUCCESS
	if(isset($_SESSION['error'])){
		$error = $_SESSION['error'];
		unset($_SESSION['error']);
	}
	
	if(isset($_SESSION['success'])){
		$success = $_SESSION['success'];
		unset($_SESSION['success']);
	}
	
	$username = "";
	$employee = -1;
	$priv = array();
	if(isset($_SESSION['data'])){
		$username = $_SESSION['data']['username'];
		$employee = $_SESSION['data']['employee'];
		$priv = $_SESSION['data']['privileges'];
		unset($_SESSION['data']);
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
	<body id="users">
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
								<span class="Highlight">User Administration &raquo; Add User</span> 
							</h1>
							<p class="">Input user details.</p>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
							?>
							<form action="	user-add-process.php" method="post">
								<hr class="form_top"/>
								<div class="table_form">
									<table class="form" cellspacing="0">
										<tr>
											<td class="label">Username</td>
											<td class="input">: <input type="text" name="username" class="input_description"  value="<?php echo $username; ?>"/></td>
										</tr>
										<tr>
											<td class="label">Password</td>
											<td class="input">: <input type="password" name="password" class="input_code" value="" /></td>
										</tr>
										<tr>
											<td class="label">Confirm Password</td>
											<td class="input">: <input type="password" name="cpassword" class="input_code" value="" /></td>
										</tr>
										<tr>
											<td class="label">Account Linked To</td>
											<td class="input">: 
												<select name="employee" class="medium">
													<option value="-1"></option>
													<?php
														foreach($employees as $item){
															if($employee == $item->employee_id){
																echo "<option value=\"{$item->employee_id}\" selected=\"selected\">{$item->last_name}, {$item->first_name} {$item->middle_name}</option>";
															} else {
																echo "<option value=\"{$item->employee_id}\">{$item->last_name}, {$item->first_name} {$item->middle_name}</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="label" style="vertical-align: top;">Privileges</td>
											<td class="input" style="vertical-align: top;">: 
												<select name="privileges[]" class="medium" multiple="true" size="15">
													<?php
														foreach($privileges as $item){
															$found = false;
															if(sizeof($priv) > 0){
																foreach($priv as $p){
																	if($p == $item->id){
																		$found = true;
																	}
																}
															}
															if($found == true){
																echo "<option value=\"{$item->id}\" selected=\"selected\">{$item->title}</option>";
															} else {
																echo "<option value=\"{$item->id}\">{$item->title}</option>";
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
												<input type="button" class="button" value="Cancel" onclick="window.location='users.php';"/>
												<input type="submit" class="button" name="add_user" value="Add User" />
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