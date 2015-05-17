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
		$PagePrivileges->AddPrivilege("Colleges - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd_c = new CollegeManager($conn);
	$hnd_d = new DivisionsManager($conn);
	
	$college_types = $hnd_c->GetCollegeTypes();
	$divisions = $hnd_d->GetDivisions();
	
	$conn->Close();
	
	if(isset($_SESSION['description'])){ $description = $_SESSION['description']; unset($_SESSION['description']); }
	if(isset($_SESSION['code'])){ $code = $_SESSION['code']; unset($_SESSION['code']); }
	if(isset($_SESSION['college_type'])){ $_college_type = $_SESSION['college_type']; unset($_SESSION['college_type']); }
	if(isset($_SESSION['division'])){ $_division = $_SESSION['division']; unset($_SESSION['division']); }
	
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
	<body id="college">
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
								<span class="Highlight">College Administration &raquo; Add College</span> 
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
							<form action="colleges-process.php" method="post">
								<hr class="form_top"/>
								<div class="table_form">
									<table class="form" cellspacing="0">
										<tr>
											<td class="label">Description</td>
											<td class="input">: 
												<input type="text" name="description" class="input_description" value="<?php if(isset($description)){ echo $description; }?>"/>
											</td>
										</tr>
										<tr>
											<td class="label">Code</td>
											<td class="input">: <input type="text" name="code" class="input_code" value="<?php if(isset($code)){ echo $code; }?>"/>
											</td>
										</tr>
										<tr>
											<td class="label">Division</td>
											<td class="input">: 
												<select name="division" class="small">
													<option value="-1"></option>
													<?php
														foreach($divisions as $item){
															if(isset($_division)){
																if($item->division_id == $_division){
																	echo "<option value=\"{$item->division_id}\" selected=\"selected\">";
																		echo $item->description;
																	echo "</option>";
																} else {
																	echo "<option value=\"{$item->division_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$item->division_id}\">";
																	echo $item->description;
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="label">Type</td>
											<td class="input">: 
												<select name="college_type" class="small">
													<option value="-1"></option>
													<?php
														foreach($college_types as $item){
															if(isset($_college_type)){
																if($item->type_id == $_college_type){
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
									</table>						
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Back" onclick="window.location='colleges.php';"/>
												<input type="submit" class="button" name="college_save" value="Add" />
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