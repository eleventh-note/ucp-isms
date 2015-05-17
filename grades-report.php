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
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "crrclm.inc.php");
	require_once(CLASSLIST . "schdls.inc.php");
	require_once(CLASSLIST . "fclts.inc.php");
	require_once(CLASSLIST . "stdnts.inc.php");
	require_once(CLASSLIST . "enl.inc.php");
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
		$PagePrivileges->AddPrivilege("Enlistment - Administrator");
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
	$hnd_sh = new ScheduleManager($conn);
	$hnd_fa = new FacultyManager($conn);
	$hnd_fc = new FacilitiesManager($conn);
	$std = new StudentManager($conn);
	$hnd_enl = new EnlistmentManager($conn);

	if(isset($_GET['id'])){
		$id = $_GET['id'];
		$records = $std->GetSprs($id);
		if(sizeof($records) > 0){
			foreach($records as $item){ $record = $item; }
			$backgrounds = $std->GetCurrentAcademicBackgroundsByKey($record->student_no);
			foreach($backgrounds as $item){ $background = $item; }
			
			//#Dictionaries
			$dict_courses = $hnd_co->GetCoursesByKey();
			$dict_school_years = $hnd_sc->GetSchoolYearsByKey();
			$dict_semesters = $hnd_sc->GetSemestersByKey();
			$dict_times = $hnd_sc->GetSchoolTimesByKey();
			$dict_days = $hnd_sc->GetSchoolDaysByKey();
			$dict_rooms = $hnd_fc->GetRoomsByKey();
			$dict_faculties = $hnd_fa->GetFacultiesByKey();
			
			//Sem & Year
			$semesters = $hnd_sc->GetActiveSemester();
			$semester = $semesters[0];
			$school_years = $hnd_sc->GetActiveSchoolYear();
			$school_year = $school_years[0];
			
			$student_number = $record->student_no;
			$tmp = $dict_school_years[$background->entry_sy];

			// $school_year = "S.Y. " . $tmp->start . " - " . $tmp->end;
			// $semester = $dict_semesters[$background->entry_semester]->description;
		
			$student_name = $record->last_name . ", " . $record->first_name . " " . $record->middle_name;
			
			//List of Subjects
			$section_subjects = $hnd_sh->GetSectionSubjectsByKey(null, $semester->semester_id, $school_year->year_id);
			//List of Curriculum Subjects
			//$curriculum_subjects = $hnd_cu->GetAllSubjectsByCode();
			
			
			//##FILL DATE WITH CURRENTLY ENLISTED SUBJECTS
			$is_old = false;
			if(!isset($_SESSION['enlisted_subjects'])){
				$subs = $hnd_enl->GetStudentEnlistmentSubjects($id, $school_year->year_id, $semester->semester_id);
				$is_old = true;
				foreach($subs as $enl){
					$_SESSION['enlisted_subjects'][] = $enl->section_subject . "_" . $enl->curriculum_subject;
				}
			}
			
			//#CREATE PDF
			$pdf = new PDF('P','pt','Letter');
			//#CREATE FIRST PAGE
			$pdf->AddPage();
			//#HEADER
			$pdf->CreateHeader('GRADES SLIP');
			//#COLUMN HEADER
			$pdf->MultiCell(0,10,'');
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->Ln();
			$pdf->SetX(25);
			$pdf->Cell(150,15, "Student No.   {$record->student_no}");
			$pdf->Ln();
			$pdf->SetFont('Arial', 'B', '10');
			$pdf->SetX(25);
			$pdf->Cell(35,15, "Name: ");
			$pdf->SetFont('Arial', '', '10');
			$pdf->MultiCell(0,15,"{$student_name}");
			//#CREATE ROWS
			//#PRINT TABLE HEADERS
			$pdf->MultiCell(0,8,'');
			$pdf->SetFont('Arial', 'B', '10');
			$pdf->SetFillColor(180,180,180);
			$pdf->SetTextColor(0,0,0);
			$pdf->MultiCell(0,18,"LIST OF SUBJECTS",'LRTB','C',1,true);
			$pdf->SetFont('Arial', 'B', '8');
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(230,230,230);
			$pdf->Cell(70, 15, 'Code','LRTB','L', 1, true);
			$pdf->Cell(200, 15, 'Description','LRTB','L', 1, true);
			$pdf->Cell(40, 15, 'Grade','LRTB','L', 1, true);
			$pdf->Cell(0, 15, 'Instructor','LRTB','L', 1, true);
			$pdf->Ln();
			$ctr=0;
			// echo "<div class=\"table subject_for_enlistment\">";
				// echo "<table class=\"curriculum_subjects default\" style=\"margin-top:10px;\" cellspacing=\"0\" title=\"\">";
					// echo "<thead><th colspan=\"10\" class=\"year_level\">Enlisted Subjects</th></thead>";
					// echo "<thead>";
						// echo "<th class=\"Count\"></th>";
						// echo "<th class=\"code\">Code</th>";
						// echo "<th class=\"code\">Section</th>";
						// echo "<th class=\"units\">Units</th>";
						// echo "<th class=\"day\">Day(s)</th>";
						// echo "<th class=\"time\">From</th>";
						// echo "<th class=\"time\">To</th>";
						// echo "<th class=\"\">Room</th>";
						// echo "<th class=\"\">Instructor</th>";
						// //echo "<th class=\"Actions\"></th>";
					// echo "</thead>";
				
					// //define the odd even tables
					 $ctr = 0;
					 if(isset($_SESSION['enlisted_subjects'])){
						$enlisted_subjects = $_SESSION['enlisted_subjects'];
						if(sizeof($enlisted_subjects) > 0){
							$total_units = 0;
							foreach($section_subjects as $item){
								//#Variables for containing previously outputted data
								$prev_day = "";
								$prev_from = "";
								$prev_to = "";
								$prev_room = "";
								$prev_instructor = "";
								$prev_code = "";
								foreach($enlisted_subjects as $enl){
									$tmp = explode("_", $enl);
									if($item->section_subject_id==$tmp[0]){
										$schedules = $hnd_sh->GetSubjectSchedulesBySubject($item->section_subject_id);
										if(sizeof($schedules) > 0){
											$ctr++;
											// if($ctr % 2 == 0){
												// echo "<tr class=\"even\" title=\"Edit schedule for [{$item->code}] {$item->subject}\" onclick=\"toggleCheckBox('remove_enlistment_{$item->section_subject_id}',this)\" />"; // onclick=\"window.location='schedules-section-set_schedule.php?id={$item->curriculum_subject_id}';\">";
											// } else {
												// echo "<tr class=\"odd\" title=\"Edit schedule for [{$item->code}] {$item->subject}\" onclick=\"toggleCheckBox('remove_enlistment_{$item->section_subject_id}',this)\" />"; //onclick=\"window.location='schedules-section-set_schedule.php?id={$item->curriculum_subject_id}';\">";
											// }
											
												//FOR ENLISTMENT
												$pdf->SetFont('Arial', '', '8');
												$pdf->SetFillColor(255,255,255);
												$pdf->Cell(70, 15, "{$item->code}",'LRTB','L', 1, true);
												$pdf->Cell(200, 15, "{$item->subject}",'LRTB','L', 1, true);
												$pdf->Cell(40, 15, rand(80,97) . '%','LRTB','L', 1, true);
												
												$tmp_instructor = '';
												foreach($schedules as $sched){
													$tmp_instructor = $dict_faculties[$sched->instructor]->employee->last_name
													. ', ' . $dict_faculties[$sched->instructor]->employee->first_name . ' ' .
													$dict_faculties[$sched->instructor]->employee->middle_name
													;
												}
												$pdf->Cell(0, 15, $tmp_instructor,'LRTB','L', 1, true);
												
												$pdf->Ln();
													
										}
									}//end of enl=section_subject_id
									
								}//end of for each enlisted subject
								
							}
					
						}
					}
			
			$pdf->Output();
		} else {
			$_SESSION['error'] = array('Record not found.');
			header("Location: enlistment-search-student.php");
			exit();
		}
	} else {
		$_SESSION['error'] = array('Record not found.');
		header("Location: enlistment-search-student.php");
		exit();
	}
	
	$conn->Close();
?>