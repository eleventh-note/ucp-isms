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
		$id = $UserInfo->id;
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	$hnd_std = new User($conn);
	$UserInfo = $hnd_std->GetUserInformations($id);
	$UserInfo = $UserInfo[0];
	
	
	//###### CHECK IF ID IS FOUND ELSE REDIRECT
	//-->Check CourseID[id] & CollegeID[cid]
	if(isset($_GET['id']) && isset($_GET['cid'])){
		$id = (int) $_GET['id'];
		$cid = (int) $_GET['cid']; //college id
		if($id <= 0){
			header("Location: colleges.php");
			exit();
		} else {
			//# GET INFORMATION
			$courses = $hnd_c->GetCoursesForDisplay($cid,$id);
			$colleges = $hnd->GetColleges($cid);

			if(sizeof($colleges) == 0 || sizeof($courses) == 0){
				//redirect if nothing is found
				$_SESSION['error'] = array("College or Course not found.");
				header("Location: colleges.php");
				exit();
			} else {
				$college = $colleges[0];
				$course = $courses[0];
				
				//## Transferring data to variables
				$code = $college->code;
				$description = $college->description;
				$type = $college->college_type[0]->description;
				$division = $college->division[0]->description;
				$added = $college->created;
				
				$course_code = $course->code;
				$course_description = $course->description;
				$course_level = $course->max_year_level;
			}
		}
	}
	
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
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_PLUGINS . "jquery.mini.js" . "\"></script>";
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_DEFAULT . "general.js" . "\"></script>";
	
	//Replace Timer Below witd script for javascript logout`
	//###TIMER###
?>
	</head>
	<body id="">
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
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>
							<form action="account-settings-process.php" method="post">
								<input type="hidden" name="id" value="<?php echo $id; ?>" />
								<div class="table_form">
									<h2>ACCOUNT INFORMATION</h2>
									<table class="form subject_view" cellspacing="0">
										<tr class="info">
											<td>Username</td>
											<td class="column">:</td>
											<td><?php echo $UserInfo->username; ?></td>
											<td></td>
										</tr>
										<!--
										<tr class="info">
											<td>Privileges</td>
											<td class="column">:</td>
											<td>
												<ul class="privileges">
													<?php 
														foreach($UserInfo->privileges as $p){
															//echo "<li>&raquo; {$p->display}</li>";
														}
													?>
												</ul>
											</td>
											<td></td>
										</tr>
										-->
										<tr class="info">
											<td>Change Password</td>
											<td class="column">:</td>
											<td>
												<ul class="privileges">
													<li><label><span></span></label><input type="password" name="new_password" value="" /> (New Password)</li>
													<li><label><span></span></label><input type="password" name="c_password" value="" /> (Confirm Password)</li>
													<li><label><span></span></label><input type="password" name="o_password" value="" /> (Old Password)</li>
													<li><label><span></span></label><input type="submit" name="change_password" value="Change Password" /></li>
												</ul>
											</td>
											<td></td>
										</tr>													
									</table>	
									
									<?php
										if($UserInfo->employee_id != 0){
									?>
										<h2>EMPLOYEE INFORMATION</h2>
										<table class="form subject_view" cellspacing="0">
											<tr class="info">
												<td>Username</td>
												<td class="column">:</td>
												<td><?php echo $UserInfo->username; ?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Privileges</td>
												<td class="column">:</td>
												<td><?php echo $course_code; ?></td>
												<td></td>
											</tr>							
											<tr class="info">
												<td>Course Description</td>
												<td class="column">:</td>
												<td><?php echo $course_description; ?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Max Year Level</td>
												<td class="column">:</td>
												<td><?php echo $course_level; ?></td>
												<td></td>
											</tr>				
											<tr class="input">
												<td colspan="4"></td>
											</tr>
										</table>	
									<?php
										}
									?>
										
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
											<!--
												<input type="button" class="button" value="Back" onclick="window.location='colleges-view.php?id=<?php echo $cid; ?>';"/>
												<input type="button" class="button" onclick="window.location='colleges-course-edit.php?id=<?php echo $id; ?>&cid=<?php echo $cid; ?>';" value="Edit Course Information" />
												<input type="button" class="button" onclick="if(confirm('Are you sure you want to delete this course? Click OK to continue.')){
															window.location='colleges-course-process.php?id=<?php echo $id; ?>&cid=<?php echo $cid; ?>&action=delete';
														}
													" value="Delete Course" />
											-->
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