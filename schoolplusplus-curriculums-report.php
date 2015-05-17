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
	//header("Content-Type: application/pdf");
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
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "crrclm.inc.php");
	require_once("_libs/fpdf/" . "wrap_pdf.php");
	require_once(CLASSLIST . "pdf.inc.php");
	
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
	$hnd_cg = new CollegeManager($conn);
	$hnd_co = new CourseManager($conn);
	$hnd_sc = new SchoolManager($conn);
	$hnd_su = new SubjectManager($conn);
	$hnd_cu = new CurriculumManager($conn);

	if(isset($_GET['cid']) && isset($_GET['cud']) && isset($_GET['sy'])){
		$college_id = (int) $_GET['cid'];
		$course_id = (int) $_GET['cud'];
		$sy_id = (int) $_GET['sy'];
		
		//get selected college & course
		$colleges = $hnd_cg->GetColleges($college_id);
		$courses = $hnd_co->GetCourses(null, $course_id);
		$school_years = $hnd_sc->GetSchoolYears($sy_id);
		
		if(sizeof($colleges) > 0 && sizeof($courses) > 0 && sizeof($school_years) > 0){
			$college = $colleges[0];
			$course = $courses[0];
			$school_year = $school_years[0];

			$subjects = $hnd_su->GetSubjectsByCode();
						
			//redirect if no courses found
			if(sizeof($subjects) == 0){
				$_SESSION['error'] = array("There are no subjects available.");
				header("Location: schoolplusplus-curriculums-select-sy.php?cid={$college_id}&cud={$course_id}");
				exit();
			} else {
				//##Get Subject Records
				$curriculum_id = $hnd_cu->VerifyCurriculum($course_id, $sy_id);
					
				//##Prepare data needed for displaying
				if($curriculum_id != -1){
					$r_subjects = $hnd_cu->GetSubjectsByCode($curriculum_id);

					//var_dump($c_subjects);
					$max_level = $course->max_year_level;
					$levels = $hnd_co->GetYearLevels();
					$semesters = $hnd_sc->GetSemesters();
				
					//#CREATE PDF
					$pdf = new PDF('P','pt','Letter');
					//#CREATE FIRST PAGE
					$pdf->AddPage();
					//######################
					// CURRICULUM
					//######################

					//#HEADER
					$pdf->CreateHeader("List of Subjects for Curriculum {$course->code} SY {$school_year->start} - {$school_year->end}");
					//#COLUMN HEADER
					//#CREATE ROWS
					$pdf->SetFont('Arial', '', '8');	
					$ctr=0;
					
					if(isset($max_level)){							
						for($i = 0; $i < $max_level; $i++){
							foreach($semesters as $s_item){
								//#PRINT TABLE HEADERS
								$pdf->MultiCell(0,15,"");
								$pdf->SetFont('Arial', 'B', '10');
								$pdf->SetFillColor(180,180,180);
								$pdf->SetTextColor(0,0,0);
								$pdf->MultiCell(0,18,"{$levels[$i]->description} - {$s_item->description}",'LRTB','C',1,true);
								$pdf->SetFont('Arial', 'B', '8');
								$pdf->SetTextColor(0,0,0);
								$pdf->SetFillColor(230,230,230);
								$pdf->Cell(20, 15, 'No.','LRTB','L', 1, true);
								$pdf->Cell(70, 15, 'Code','LRTB','L', 1, true);
								$pdf->Cell(235, 15, 'Description','LRTB','L', 1, true);
								$pdf->Cell(70, 15, 'Units','LRTB','L', 1, true);
								$pdf->Cell(70, 15, 'Pre-Requisite','LRTB','L', 1, true);
								$pdf->Cell(0, 15, 'Co-Requisite','LRTB','L', 1, true);
								$pdf->Ln();
								//echo "<table class=\"curriculum_subjects\" style=\"margin-top:30px;\" cellspacing=\"0\" title=\"\">";
									//echo "<thead><th colspan=\"7\" class=\"year_level\">{$levels[$i]->description} - {$s_item->description}</th></thead>";
									//echo "<thead>";
										// echo "<th class=\"Count\">No.</th>";
										// echo "<th class=\"code\">Code</th>";
										// echo "<th class=\"description\">Description</th>";
										// echo "<th class=\"code\">Units</th>";
										// echo "<th class=\"prerequisite\">Pre-Requisite</th>";
										// echo "<th class=\"corequisite\">Co-Requisite</th>";
										// echo "<th class=\"Actions\"></th>";
									//echo "</thead>";
								
									// //define the odd even tables
									$total_units = 0;
									if(sizeof($r_subjects) > 0){
										foreach($r_subjects as $item){
											if($item->semester == $s_item->semester_id && $item->year_level == $levels[$i]->equivalent){
												$ctr++;
												if($ctr % 2 == 0){
													//echo "<tr class=\"even\">"; // onclick=\"window.location='schoolplusplus-curriculums-bycollege.php?id={$item->subject_id}';\">";
												} else {
													//echo "<tr class=\"odd\">"; // onclick=\"window.location='schoolplusplus-curriculums-bycollege.php?id={$item->subject_id}';\">";
												}
													$pdf->SetFont('Arial', '', '8');
													$pdf->SetFillColor(255,255,255);
													$pdf->Cell(20, 15, "{$ctr}",'LRTB','L', 1, true);
													$pdf->Cell(70, 15, "{$item->code}",'LRTB','L', 1, true);
													$pdf->Cell(235, 15, "{$item->subject}",'LRTB','L', 1, true);
													$pdf->Cell(70, 15, "{$item->units}",'LRTB','L', 1, true);
													$pdf->Cell(70, 15, '','LRTB','L', 1, true);
													$pdf->Cell(0, 15, '','LRTB','L', 1, true);
													$pdf->Ln();
													// echo "<td>{$ctr}</td>";
													// echo "<td>{$item->code}</td>";
													// echo "<td>{$item->subject}</td>";
													// echo "<td>{$item->units}</td>";
													$total_units += $item->units;
													
													// //#######################################
													// //	GET PRE-REQUISITES
													// //#######################################
													// $prerequisites = $hnd_cu->GetPrerequisitesByCode($item->curriculum_subject_id);
													// //var_dump($prerequisites);
													// $corequisites = $hnd_cu->GetCorequisitesByCode($item->curriculum_subject_id);
													// echo "<td>";
													// if(sizeof($prerequisites) > 0){
														// $data = "";
														// //get first data then delete 
														// $is_first = true;
														// //get display data
														// foreach($prerequisites as $pre){
															// if($is_first == true){
																// $data .= $pre->code;
																// $is_first = false;
															// } else {
																// $data .= ", " . $pre->code;
															// }
														// }
														// echo $data;
													// }
													// echo "</td>";
													// echo "<td>";
													// if(sizeof($corequisites) > 0){
														// $data = "";
														// //get first data then delete 
														// $is_first = true;
														// //get display data
														// foreach($corequisites as $co){
															// if($is_first == true){
																// $data .= $co->code;
																// $is_first = false;
															// } else {
																// $data .= ", " . $co->code;
															// }
														// }
														// echo $data;
													// }
													// echo "</td>";
													// echo "<td class=\"Actions\">";
														// //echo "<a href=\"schoolplusplus-curriculums-process.php?cid={$college_id}&cud={$course_id}&sy={$sy_id}&sem={$sem_id}&yr={$yr_id}&action=delete&id={$item->curriculum_subject_id}\" onclick=\"return confirm('Delete subject from curriculum? Click OK to continue.')\">Delete</a>";
													// echo "</td>";
												// echo "</tr>";
											}
										}
										
									}
									$pdf->SetFont('Arial', 'B', '10');
									$pdf->SetFillColor(180,180,180);
									$pdf->SetTextColor(0,0,0);
									$pdf->Cell(325,18,"Total Units",'LRTB','C',1,true);
									$pdf->Cell(0,18,"{$total_units} Units",'LRTB','C',1,true);
									$pdf->MultiCell(0,15,"");
									// if($ctr == 0){
										// echo "<tr class=\"odd\" class=\"reminder\"><td colspan=\"7\">No subjects assigned. </td></tr>";
									// }
									// echo "<thead><th colspan=\"3\" style=\"text-align: right;\">Total Units:</th><th colspan=\"1\">{$total_units}</th></thead>";
								//echo "</table>";
							}
							
						}									
					} //end of check isset
					
					$pdf->Output();
				}
				
			}
			
		} else {
			$_SESSION['error'] = array("College, Course or School Year not selected.");
			header("Location: schoolplusplus-curriculums-select-college.php");
			exit();
		}
		
	} else {
		$_SESSION['error'] = array("College, Course or School Year not selected.");
		header("Location: schoolplusplus-curriculums-select-college.php");
		exit();
	}
	
	$conn->Close();
?>