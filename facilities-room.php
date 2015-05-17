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
	$rooms = $hnd->GetRooms();
		
	$conn->Close();

	//##### PROCESS ERROR or SUCCESS
	if(isset($_SESSION['success'])){
		$success = $_SESSION['success'];
		unset($_SESSION['success']);
	}
	
	if(isset($_SESSION['error'])){
		$error = $_SESSION['error'];
		unset($_SESSION['error']);
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
								<span class="Highlight">Facility Administration &raquo; Manage Rooms</span> 
							</h1>
							<p class="">The list of rooms below are used to define classrooms for each subject schedule.</p>
							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='facilities.php'" />
									<input type="button" value="Add Room" onclick="window.location='facilities-room-add.php'" />
								</p>
							</div>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>
							<div class="table">
								<table class="positions" cellspacing="0" title="">
									<thead>
										<th id="Count">No.</th>
										<th id="Position">Room Description</th>
										<th id="">Room Code</th>
										<th id="">Floor</th>
										<th id="">Type</th>
										<th id="">Status</th>
										<th id="">Building</th>
										<th id="Actions"></th>
									</thead>
									<?php 
										if(sizeof($rooms) > 0){
											$ctr = 0;
											foreach($rooms as $item){
												$ctr++;
												//define the odd even tables
												if($ctr % 2 == 0){
													echo "<tr class=\"even\">";
												} else {
													echo "<tr class=\"odd\">";
												}
													echo "<td>{$ctr}</td>";
													echo "<td>{$item->description}</td>";
													echo "<td>{$item->code}</td>";
													echo "<td>{$item->floor}</td>";
													echo "<td>{$item->type->description}</td>";
													echo "<td>{$item->status->description}</td>";
													echo "<td>{$item->building->description}</td>";
													echo "<td class=\"Actions\">";
														echo "<a href=\"facilities-room-edit.php?id={$item->room_id}\">Edit</a>";
														echo " | ";
														echo "<a href=\"facilities-room-process.php?id={$item->room_id}&action=delete\" onclick=\"return confirm('Delete room? Click OK to continue.')\">Delete</a>";
													echo "</td>";
												echo "</tr>";
											}
										} else {
											echo "<td colspan=\"8\">No rooms exist.</td>";
										}
									?>
									
								</table>
							</div>
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