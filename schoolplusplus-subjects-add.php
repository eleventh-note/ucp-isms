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
	require_once(CLASSLIST . "sbjcts.inc.php");
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
		$PagePrivileges->AddPrivilege("School - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}

	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd = new SubjectManager($conn);

	$types = $hnd->GetSubjectTypes();
	$groups = $hnd->GetSubjectGroups();

	$conn->Close();

	if(isset($_SESSION['description'])){ $description = $_SESSION['description']; unset($_SESSION['description']); }
	if(isset($_SESSION['code'])){ $code = $_SESSION['code']; unset($_SESSION['code']); }
	if(isset($_SESSION['type'])){ $_type = $_SESSION['type']; unset($_SESSION['type']); }
	if(isset($_SESSION['group'])){ $_group = $_SESSION['group']; unset($_SESSION['group']); }
	if(isset($_SESSION['units'])){ $units = $_SESSION['units']; unset($_SESSION['units']); }
	if(isset($_SESSION['unitsLab'])){ $units = $_SESSION['unitsLab']; unset($_SESSION['unitsLab']); }
	if(isset($_SESSION['isHalf'])){ $units = $_SESSION['isHalf']; unset($_SESSION['isHalf']); }

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
	<body id="school">
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
								<span class="Highlight">Subject Administration &raquo; Subjects &raquo; Add Subject</span>
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
							<form action="schoolplusplus-subjects-process.php" method="post">
								<hr class="form_top"/>
								<div class="table_form">
									<table class="form" cellspacing="0">
										<tr>
											<td class="label">Subject Code</td>
											<td class="input">: <input type="text" name="code" class="input_code" value="<?php if(isset($code)){ echo $code; }?>"/></td>
										</tr>
										<tr>
											<td class="label">Subject Description</td>
											<td class="input">: <input type="text" name="description" class="input_description" value="<?php if(isset($description)){ echo $description; }?>"/></td>
										</tr>
										<tr>
											<td class="label">Subject Type</td>
											<td class="input">:
												<select name="type" class="small">
													<option value="-1"></option>
													<?php
														foreach($types as $item){
															if(isset($_type)){
																if($item->type_id == $_type){
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
										<tr>
											<td class="label">Lec Units</td>
											<td class="input">: <input type="text" name="units" class="input_units" maxlength="3" value="<?php if(isset($units)){ echo $units; }?>"/></td>
										</tr>
										<tr>
											<td class="label">Lab Units</td>
											<td class="input">: <input type="text" name="unitsLab" class="input_units" maxlength="3" value="<?php if(isset($unitsLab)){ echo $unitsLab; }?>"/></td>
										</tr>
										<tr>
											<td class="label">Group</td>
											<td class="input">:
												<select name="group" class="small">
													<option value="-1"></option>
													<?php
														foreach($groups as $item){
															if(isset($_group)){
																if($item->group_id == $_group){
																	echo "<option value=\"{$item->group_id}\" selected=\"selected\">";
																		echo $item->description;
																	echo "</option>";
																} else {
																	echo "<option value=\"{$item->group_id}\">";
																		echo $item->description;
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$item->group_id}\">";
																	echo $item->description;
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="label">Unit is Virtual</td>
											<td class="input">:<input type="checkbox" name="virtual" value="1" /></td>
										</tr>
										<tr>
											<td class="label">Pay Half Fee</td>
											<td class="input">:<input type="checkbox" name="isHalf" value="1" /></td>
										</tr>
									</table>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Back" onclick="window.location='schoolplusplus-subjects.php';"/>
												<input type="submit" class="button" name="subject_save" value="Add" />
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
