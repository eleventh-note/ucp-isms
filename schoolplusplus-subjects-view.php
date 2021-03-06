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

	//###### CHECK IF ID IS FOUND ELSE REDIRECT
	if(isset($_GET['id'])){
		$id = (int) $_GET['id'];
		if($id <= 0){
			header("Location: schoolplusplus-subjects.php");
			exit();
		} else {
			//# GET INFORMATION
			$subjects = $hnd->GetSubjects($id);

			if(sizeof($subjects) == 0){
				//redirect if nothing is found
				$_SESSION['error'] = array("Subject not found.");
				header("Location: schoolplusplus-subjects.php");
				exit();
			} else {
				$subject = $subjects[0];

				//## Transferring data to variables
				$code = $subject->code;
				$description = $subject->description;
				$type = $subject->subject_type;
				$units = $subject->units;
				$unitsLab = $subject->unitsLab;
				$group = $subject->group;
				$virtual = $subject->virtual;
				$added = $subject->created;
				$isHalf = $subject->isHalf;

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

	//# Other Javascript Loaded Here
	echo "<script type=\"text/javascript\" src=\"" . $DIR_JS_PLUGINS . "jquery.mini.js" . "\"></script>"
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
								<span class="Highlight">Subject Administration &raquo; View Information</span>
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
							<form action="employment-employee-process.php" method="post">
								<input type="hidden" name="employee_id" value="<?php echo $id; ?>" />

								<div class="table_form">
									<h2>SUBJECT INFORMATION</h2>
									<table class="form subject_view" cellspacing="0">
										<tr class="info">
											<td>Subject Code</td>
											<td class="column">:</td>
											<td><?php echo $code; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Subject Description</td>
											<td class="column">:</td>
											<td><?php echo $description; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Lec Units</td>
											<td class="column">:</td>
											<td><?php echo $units; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Lab Units</td>
											<td class="column">:</td>
											<td><?php echo $unitsLab; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Type</td>
											<td class="column">:</td>
											<td><?php echo $type; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Subject Group</td>
											<td class="column">:</td>
											<td><?php echo $group; ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Is Virtual</td>
											<td class="column">:</td>
											<td><?php if($virtual == 1){ echo "Yes"; } else { echo "No"; } ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Pay Half Fee</td>
											<td class="column">:</td>
											<td><?php if($isHalf == 1){ echo "Yes"; } else { echo "No"; } ?></td>
											<td></td>
										</tr>
										<tr class="info">
											<td>Date Added</td>
											<td class="column">:</td>
											<td><?php echo date("F d, Y", strtotime($added)); ?></td>
											<td></td>
										</tr>

										<tr class="input">
											<td colspan="4"></td>
										</tr>
									</table>
									<div id="subject_fees">
										<h2>SUBJECT FEES</h2>
										<table class="form employee" cellspacing="0">
											<?php // ### SCHOOL, DEGREE and YEAR ?>
											<tr class="info">
												<td>School</td>
												<td class="column">:</td>
												<td><?php if(isset($educ_school)){ echo $educ_school; }?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Degree</td>
												<td class="column">:</td>
												<td><?php if(isset($educ_degree)){ echo $educ_degree; }?></td>
												<td></td>
											</tr>
											<tr class="info">
												<td>Year</td>
												<td class="column">:</td>
												<td><?php if(isset($educ_year)){ if($educ_year > 0){ echo $educ_year; }}?></td>
												<td></td>
											</tr>
										</table>
									</div>
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Back" onclick="window.location='schoolplusplus-subjects.php';"/>
												<input type="button" class="button" onclick="window.location='schoolplusplus-subjects-edit.php?id=<?php echo $id; ?>';" value="Edit Information" />
												<input type="button" class="button" onclick="
														if(confirm('Are you sure you want to delete this subject? Click OK to continue.')){
															window.location='schoolplusplus-subjects-process.php?id=<?php echo $id; ?>&action=delete';
														}
													" value="Delete Subject" />
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
