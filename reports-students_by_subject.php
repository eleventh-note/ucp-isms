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
	require_once(CLASSLIST . "reg.inc.php");
	require_once(CLASSLIST . "dvsns.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "schdls.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
  require_once(CLASSLIST . "report.inc.php");
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
		$PagePrivileges->AddPrivilege("Reports - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}

	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();

	//# HANDLERS
	$hnd_cg = new CollegeManager($conn);
	$hnd_co = new CourseManager($conn);
	$hnd_sh = new ScheduleManager($conn);
	$hnd_sc = new SchoolManager($conn);
  $hnd_r = new Report($conn);

	//# OPTION IS INITIALLY SHOWN
	$sy = $hnd_sc->GetActiveSchoolYear();
	$sem = $hnd_sc->GetActiveSemester();
  $subjects = $hnd_r->GetActiveSubjects();

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
	<body id="reports">
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
								<span class="Highlight">List of Active Subjects</span>
								<?php
									//##### PASS ERROR IF FOUND
									if(isset($success)){
										echo Sentry::ShowStatus('success',$success);
									}
									if(isset($error)){
										echo Sentry::ShowStatus('error',$error);
									}
								?>
							</h1>

							<p class=""><b></b></p>
							<p>
								Sort report by:
								<select id="sort">
									<option value="1">Student No</option>
									<option value="2">Student Name</option>
								</select>
								then click on a row to open a report.
							</p>
							<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
								<div class="table_form">

							<?php
									echo "<a id=\"list_of_subjects\"></a>";
									echo "<p class=\"margin-top: 20px;\">";
									echo "</p>";
									echo "<div class=\"table\">";
										echo "<table class=\"curriculum_subjects default\" style=\"margin-top:10px;\" cellspacing=\"0\" title=\"\">";
											echo "<thead><th colspan=\"10\" class=\"year_level\">ACTIVE SUBJECTS of ";

												if(isset($sy)){
													if(sizeof($sy) > 0){
														echo "[SY " . $sy[0]->start . " - " . $sy[0]->end . "]";
													}
												}

												if(isset($sem)){
													if(sizeof($sem) > 0){
														echo " [" . $sem[0]->description . "]";
													}
												}

											echo "</th></thead>";
											echo "<thead>";
												echo "<th class=\"Count\">No.</th>";
												echo "<th class=\"code\">Section</th>";
												echo "<th class=\"code\">Subject Code</th>";
												echo "<th class=\"description\">Subject Desc</th>";
												echo "<th class=\"units\">Lec Units</th>";
												echo "<th class=\"units\">Lab Units</th>";
                        echo "<th class=\"units\">Total Units</th>";
												//echo "<th class=\"Actions\"></th>";
											echo "</thead>";
											$ctr = 0;

											foreach($subjects as $item){
                        $ctr++;

                        //define the odd even tables
                        if($ctr % 2 == 0){
                          echo "<tr class=\"even\" title=\"Edit/View schedule for [{$item['SubjectCode']}]\" data-id=\"{$item['SectionSubjectID']}\">";
                        } else {
                          echo "<tr class=\"odd\" title=\"Edit/View schedule for [{$item['SubjectCode']}]\" data-id=\"{$item['SectionSubjectID']}\">";
                        }

                        echo "<td>{$ctr}</td>";
                        echo "<td>{$item['SectionName']}</td>";
                        echo "<td>{$item['SubjectCode']}</td>";
                        echo "<td>{$item['SubjectDescription']}</td>";
                        echo "<td>{$item['Units']}</td>";
                        echo "<td>{$item['UnitsLab']}</td>";
                        echo "<td>";
                          echo ($item['Units'] + $item['UnitsLab']);
                        echo "</td>";
											}
										echo "</table>";
									echo "</div>";

							?>
									<hr class="form_top"/>
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
		<script type="text/javascript">
			$(document).ready(function(){
				$('tr').each(function(i, target) {
					var id = $(target).attr('data-id');
					$(target).bind('click', function() {
						var sort = $('#sort').val();
						var link = "reports-students_by_subject-pdf.php?id=" + id + "&sort=" + sort;
						window.open(link);
					});
				});
			});
		</script>
	</body>
</html>
<?php
	//::START OF 'CLOSING REMARKS'
		//memory releasing and stuffs
	//::END OF 'CLOSING REMARKS'
	$conn->Close();
?>
