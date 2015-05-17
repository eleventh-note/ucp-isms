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
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "grds.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
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
		$PagePrivileges->AddPrivilege("Grades - Administrator");
		$PagePrivileges->AddPrivilege("Grades - Encoder");
		$PagePrivileges->AddPrivilege("Grades - Viewer");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
	
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	
	$hnd = new GradesManager($conn);
	$hnd_sc = new SchoolManager($conn);
	
	$sem = $hnd_sc->GetActiveSemester();
	$sy = $hnd_sc->GetActiveSchoolYear();
	
	$sort = 2;
	if(isset($_GET['sort'])){
		$sort = (int) $_GET['sort'];
		switch($sort){
			case 1:
				$students = $hnd->getIncompleteStudentsByStudentNo($sy[0]->year_id, $sem[0]->semester_id);
				break;
			case 2:
				$students = $hnd->getIncompleteStudents($sy[0]->year_id, $sem[0]->semester_id);
				break;
			case 3:
				$students = $hnd->getIncompleteStudentsBySection($sy[0]->year_id, $sem[0]->semester_id);
				break;
		}
	} else {
		//By LastName
		$students = $hnd->getIncompleteStudents($sy[0]->year_id, $sem[0]->semester_id);
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
	<body id="grades">
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
								<span class="Highlight">Grades Administration &raquo; Incomplete Grades</span> 
							</h1>
							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='grades.php'" />
								</p>
							</div>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
							?>
							<div id="sort_actions">
								<p class="sort">
									<input type="button" value="Sort by Student No" onclick="window.location='grades-incomplete.php?sort=1'" />
									<input type="button" value="Sort by Last Name" onclick="window.location='grades-incomplete.php?sort=2'" />
									<input type="button" value="Sort by Section" target="_new" onclick="window.location='grades-incomplete.php?sort=3'" />
								</p>
							</div>
							<p class="">Below are the list of students with incomplete grades:</p>
							<div class="table">
								<table class="" cellspacing="0" title="">
									<thead>
										<th class="Count">No.</th>
										<th class="application_number center">Student No</th>
										<th class="student_name">Name</th>
										<th class="section">Section</th>
										<th class="date_of_entry center"># of Incomplete Grades</th>
										<?php //<th class="Actions"></th> ?>
									</thead>
									<?php 
										$ctr = 0;
										if(sizeof($students) > 0){
											foreach($students as $item){
												$ctr++;
												//define the odd even tables
												if($ctr % 2 == 0){
													echo "<tr class=\"even\" onclick=\"window.location='grades-viewer.php?id={$item['StudentID']}';\">";
												} else {
													echo "<tr class=\"odd\" onclick=\"window.location='grades-viewer.php?id={$item['StudentID']}';\">";
												}
													echo "<td>{$ctr}</td>";
													echo "<td style=\"text-align: center;\">{$item['StudentNo']}</td>";
													echo "<td>{$item['studentName']}</td>";
													echo "<td>{$item['section']}</td>";
													echo "<td style=\"text-align: center;\">{$item['total']}</td>";
												echo "</tr>";
											}
										} else {
											echo "<td colspan=\"6\">There are no existing subjects that are for encoding.</td>";
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