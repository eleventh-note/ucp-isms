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
		$PagePrivileges->AddPrivilege("Facilities - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd = new FacilitiesManager($conn);
	$buildings = $hnd->GetBuildingsByCode();
	$statuses = $hnd->GetRoomStatuses();
	$types = $hnd->GetRoomTypes();
	
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
	
	
	if(isset($_SESSION['description'])){ $description = $_SESSION['description']; unset($_SESSION['description']); }
	if(isset($_SESSION['code'])){ $code = $_SESSION['code']; unset($_SESSION['code']); }
	if(isset($_SESSION['floor'])){ $floor = $_SESSION['floor']; unset($_SESSION['floor']); }
	if(isset($_SESSION['building'])){ $_building = $_SESSION['building']; unset($_SESSION['building']); }
	if(isset($_SESSION['seating_capacity'])){ $seating_capacity = $_SESSION['seating_capacity']; unset($_SESSION['seating_capacity']); }
	if(isset($_SESSION['floor_area'])){ $floor_area = $_SESSION['floor_area']; unset($_SESSION['floor_area']); }
	if(isset($_SESSION['room_status'])){ $_status = $_SESSION['room_status']; unset($_SESSION['room_status']); }
	if(isset($_SESSION['room_type'])){ $_type = $_SESSION['room_type']; unset($_SESSION['room_type']); }
	
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
	<body id="facilities">
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
								<span class="Highlight">Facility Administration &raquo; Manage Rooms &raquo; Add Room</span> 
							</h1>
							<p class="">Input new room details.</p>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
							?>
							<form action="facilities-room-process.php" method="post">
								<hr class="form_top"/>
								<div class="table_form">
									<table class="form" cellspacing="0">
										<tr>
											<td class="label">Room Description</td>
											<td class="input">: <input type="text" name="description" class="input_description"  value="<?php if(isset($description)){ echo $description; }?>"/></td>
										</tr>
										<tr>
											<td class="label">Room Code</td>
											<td class="input">: <input type="text" name="code" class="input_code" value="<?php if(isset($code)){ echo $code; }?>" /></td>
										</tr>
										<tr>
											<td class="label">Room Type</td>
											<td class="input">: 
												<select name="room_type" class="small">
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
											<td class="label">Room Status</td>
											<td class="input">: 
												<select name="room_status" class="small">
													<option value="-1"></option>
													<?php
														foreach($statuses as $item){
															if(isset($_status)){
																if($item->status_id == $_status){
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
											<td class="label">Floor</td>
											<td class="input">: <input type="text" name="floor" class="input_code" maxlength="2" value="<?php if(isset($floor)){ echo $floor; }?>" /></td>
										</tr>
										<tr>
											<td class="label">Building</td>
											<td class="input">: 
												<select name="building" class="small">
													<option value="-1"></option>
													<?php
														foreach($buildings as $item){
															if(isset($_building)){
																if($item->building_id == $_building){
																	echo "<option value=\"{$item->building_id}\" selected=\"selected\">";
																		echo "[" . $item->code . "] " . $item->description;
																	echo "</option>";
																} else {
																	echo "<option value=\"{$item->building_id}\">";
																		echo "[" . $item->code . "] " . $item->description;
																	echo "</option>";
																}
															} else {
																echo "<option value=\"{$item->building_id}\">";
																	echo "[" . $item->code . "] " . $item->description;
																echo "</option>";
															}
														}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="label">Seating Capacity</td>
											<td class="input">: <input type="text" name="seating_capacity" class="input_code" maxlength="3" value="<?php if(isset($seating_capacity)){ echo $seating_capacity; }?>" /></td>
										</tr>
										<tr>
											<td class="label">Floor Area (sqm.)</td>
											<td class="input">: <input type="text" name="building_area" class="input_code" maxlength="4" value="<?php if(isset($building_area)){ echo $building_area; }?>" /></td>
										</tr>
									</table>						
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Cancel" onclick="window.location='facilities-room.php';"/>
												<input type="submit" class="button" name="room_save" value="Add Room" />
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