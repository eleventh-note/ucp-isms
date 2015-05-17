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
	require_once(CLASSLIST . "emp.inc.php");
	require_once(CLASSLIST . "cllgs.inc.php");
	require_once(CLASSLIST . "schl.inc.php");
	require_once(CLASSLIST . "sbjcts.inc.php");
	require_once(CLASSLIST . "crrclm.inc.php");
	require_once(CLASSLIST . "schdls.inc.php");
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
		$PagePrivileges->AddPrivilege("Schedules - Administrator");
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

	// Add Schedule
	if(isset($_POST['add_schedule'])){

		//get list of subjects from session that is passed
		$subjects = unserialize($_SESSION['section']['subjects']);

		$subject_id = (int) $_POST['subject'];

		if(isset($_POST['to']) && isset($_POST['from']) && isset($_POST['day'])){
			$to = (int) $_POST['to'];
			$from = (int) $_POST['from'];
			$day = (int) $_POST['day'];

			if(isset($_POST['to'])){
				$to = (int) $_POST['to'];
				if($to < 1){ $to = null; }
			} else {
				$to = null;
			}

			if(isset($_POST['from'])){
				$from = (int) $_POST['from'];
				if($from < 1){ $from = null; }
			} else {
				$from = null;
			}

			if(isset($_POST['day'])){
				$day = (int) $_POST['day'];
				if($day < 1){ $day = null; }
			} else {
				$day = null;
			}

			if(isset($_POST['instructor'])){
				$instructor = (int) $_POST['instructor'];
				if($instructor < 1){ $instructor = null; }
			} else {
				$instructor = null;
			}

			if(isset($_POST['room'])){
				$room = (int) $_POST['room'];
				if($room < 1){ $room = null; }
			} else {
				$room = null;
			}

			//GET LIST OF TIMES
			$school_times = $hnd_sc->GetSchoolTimesByKey();
			$school_days = $hnd_sc->GetSchoolDaysByKey();
			$rooms = $hnd_fc->GetRoomsByKey();

			//#############################################
			//# ADD CHECKING OF CONFLICT HERE FIRST
			//#############################################
			$is_valid = true; //will be invalid if conflict found

			//#############################
			//## CONDITIONS  ##############
			//#############################
			//-- 1> must not have any conflict with subjects on the SAME SECTION.
					//-Check if subject is already entered on the same section.
			//-- 2> must not have any conflict in rooms (Same Room at the Same Time & Day)
			//-- 3> must not have any conflict in instructor (Same Instructor at the Same Time & Day)

			//## CHECK IF A SUBJECT SCHEDULE IS AVAILABLE
			//--> This is for the same section only
			//--> Must check the database for conflicts too.
			if(sizeof($_SESSION['section']['schedules']) > 0){
				//## GET ALL SUBJECT SCHEDULES on the current semester and school year
				$other_subjects = $hnd_sh->GetAllSubjectSchedulesBySemYear($_SESSION['section']['sem_id'], $_SESSION['section']['sy_id']);

				//## CHECK EACH SUBJECT FOR CONFLICT [DATABASE SIDE]
				foreach($other_subjects as $sub){
					//Get Times to be used in checking
					//-->OLD
					$old_from = $school_times[$sub->from]->Military();
					$old_to = $school_times[$sub->to]->Military();
					//-->NEW
					$new_from = $school_times[$from]->Military();
					$new_to = $school_times[$to]->Military();

					//### THE FF CODE will check for validity of schedule in the current section only
					if(
						//checks for room, day and time
						$sub->room == $room && $sub->day == $day &&
						(
							//Same Time
							($old_from == $new_from && $old_to == $new_to) ||
							//Overlapping
							(
								($old_from < $new_to && $new_to <= $old_to) ||
								($old_from <= $new_from &&  $new_from < $old_to)
							) ||
							//New Schedule is Inside Old
							($old_from <= $new_from && $new_to <= $old_to) ||
							//Old Schedule is Inside New
							($new_from <= $old_from && $old_to <= $new_to)
						)
					){
						//disallow adding of schedule
						$is_valid = false;

						$faculties = $hnd_fa->GetFacultiesByKey();
						$tmp = $hnd_sh->GetSectionSubjectById($sub->section_subject_id);
						foreach($tmp as $grabbed){ $conflict = $grabbed; }

						//disallow adding of schedule
						$is_valid = false;
						//get subject with conflict along with professor details
						$curriculums = $hnd_cu->GetCurriculumById($conflict->section[0]->curriculum);
						$curriculum = $curriculums[0];
						//create the error message
						$middle_initial = "";
						if(trim($faculties[$instructor]->employee->middle_name) <> ''){
							$middle_initial . " " . substr($faculties[$instructor]->employee->middle_name,0,1) . ".";
						}

						//create the error message
						$_SESSION['error'] = array("<p>Room Schedule conflict found. See details below: 	<br/>
													<b>Curriculum</b>: {$curriculum->info} 						<br/>
													<b>Section: </b>: {$conflict->section[0]->name} 			<br/>
													<b>Subject Description</b>: {$conflict->subject} 			<br/>
													<b>Subject Code</b>: {$conflict->code} 		 				<br/>
													<b>Room</b>: {$rooms[$sub->room]->description}	 	<br/>
													<b>Day</b>: {$school_days[$sub->day]->description}	<br/>
													<b>From</b>: {$school_times[$sub->from]->description}<br/>
													<b>To</b>: {$school_times[$sub->to]->description}	<br/>
													</p>");

						break;
					} elseif(
						//checks for instructor, day and time
						$sub->instructor == $instructor && $sub->day == $day &&
						(
							//Same Time
							($old_from == $new_from && $old_to == $new_to) ||
							//Overlapping
							(
								($old_from < $new_to && $new_to <= $old_to) ||
								($old_from <= $new_from &&  $new_from < $old_to)
							) ||
							//New Schedule is Inside Old
							($old_from <= $new_from && $new_to <= $old_to) ||
							//Old Schedule is Inside New
							($new_from <= $old_from && $old_to <= $new_to)
						)
					){
						// // // // $faculties = $hnd_fa->GetFacultiesByKey();
						// // // // $tmp = $hnd_sh->GetSectionSubjectById($sub->section_subject_id);
						// // // // foreach($tmp as $grabbed){ $conflict = $grabbed; }

						// // // // //disallow adding of schedule
						// // // // $is_valid = false;
						// // // // //get subject with conflict along with professor details
						// // // // $curriculums = $hnd_cu->GetCurriculumById($conflict->section[0]->curriculum);
						// // // // $curriculum = $curriculums[0];
						// // // // //create the error message
						// // // // $middle_initial = "";
						// // // // if(trim($faculties[$instructor]->employee->middle_name) <> ''){
							// // // // $middle_initial . " " . substr($faculties[$instructor]->employee->middle_name,0,1) . ".";
						// // // // }
						// // // // $_SESSION['error'] = array("<p><b>Teaching Load</b> conflict found. See details below: 	<br/>
													// // // // <b>Faculty</b>: " .
													// // // // $faculties[$instructor]->employee->last_name . ", " . $faculties[$instructor]->employee->first_name . $middle_initial
													// // // // . " <br/>
													// // // // <b>Section: </b>: {$conflict->section[0]->name} 			<br/>
													// // // // <b>Subject Description</b>: {$conflict->subject} 			<br/>
													// // // // <b>Subject Code</b>: {$conflict->code} 		 				<br/>
													// // // // <b>Room</b>: {$rooms[$sub->room]->description}	 	<br/>
													// // // // <b>Day</b>: {$school_days[$sub->day]->description}	<br/>
													// // // // <b>From</b>: {$school_times[$sub->from]->description}<br/>
													// // // // <b>To</b>: {$school_times[$sub->to]->description}	<br/>
													// // // // </p>");

						break;
					} elseif(
						//conflict in section subject schedule
						//-->Same day and same time
						$sub->day == $day &&
						(
							//Same Time
							($old_from == $new_from && $old_to == $new_to) ||
							//Overlapping
							(
								($old_from < $new_to && $new_to <= $old_to) ||
								($old_from <= $new_from &&  $new_from < $old_to)
							) ||
							//New Schedule is Inside Old
							($old_from <= $new_from && $new_to <= $old_to) ||
							//Old Schedule is Inside New
							($new_from <= $old_from && $old_to <= $new_to)
						)
					){
						// $faculties = $hnd_fa->GetFacultiesByKey();
						// $tmp = $hnd_sh->GetSectionSubjectById($sub->section_subject_id);
						// foreach($tmp as $grabbed){ $conflict = $grabbed; }

						// //disallow adding of schedule
						// $is_valid = false;
						// //get subject with conflict along with professor details
						// $curriculums = $hnd_cu->GetCurriculumById($conflict->section[0]->curriculum);
						// $curriculum = $curriculums[0];

						// //create the error message
						// $_SESSION['error'] = array("<p>Subject Schedule conflict found. See details below: 	<br/>
													// <b>Curriculum</b>: {$curriculum->info} 						<br/>
													// <b>Section: </b>: {$conflict->section[0]->name} 			<br/>
													// <b>Subject Description</b>: {$conflict->subject} 			<br/>
													// <b>Subject Code</b>: {$conflict->code} 		 				<br/>
													// <b>Room</b>: {$rooms[$sub->room]->description}	 	<br/>
													// <b>Day</b>: {$school_days[$sub->day]->description}	<br/>
													// <b>From</b>: {$school_times[$sub->from]->description}<br/>
													// <b>To</b>: {$school_times[$sub->to]->description}	<br/>
													// </p>");

						break;
					}
				}

				//## CHECK EACH SUBJECT FOR CONFLICT [SECTION SIDE]
				foreach($_SESSION['section']['schedules'] as $k => $subject){
					//## CHECK EACH SCHEDULE FOR CONFLICT
					foreach($subject as $l => $schedule){
						echo "{$rooms[$schedule['room']]->code	} == {$rooms[$room]->code} && {$school_days[$schedule['day']]->shorthand} == {$school_days[$day]->shorthand} && ";
						echo "{$school_times[$from]->Military()} >= {$school_times[$schedule['from']]->Military()} && ";
						echo "{$school_times[$from]->Military()} <= {$school_times[$schedule['to']]->Military()} <br/>";
						//$schedule (old)

						//Get Times to be used in checking
						//-->OLD
						$old_from = $school_times[$schedule['from']]->Military();
						$old_to = $school_times[$schedule['to']]->Military();
						//-->NEW
						$new_from = $school_times[$from]->Military();
						$new_to = $school_times[$to]->Military();

						//### THE FF CODE will check for validity of schedule in the current section only
						if(
							//checks for room, day and time
							$schedule['room'] == $room && $schedule['day'] == $day &&
							(
								//Same Time
								($old_from == $new_from && $old_to == $new_to) ||
								//Overlapping
								(
									($old_from < $new_to && $new_to <= $old_to) ||
									($old_from <= $new_from &&  $new_from < $old_to)
								) ||
								//New Schedule is Inside Old
								($old_from <= $new_from && $new_to <= $old_to) ||
								//Old Schedule is Inside New
								($new_from <= $old_from && $old_to <= $new_to)
							)
						){
							//disallow adding of schedule
							$is_valid = false;
							//get subject with conflict
							var_dump(($old_from == $new_from && $old_to == $new_to));
							var_dump(($old_from < $new_to && $new_to <= $old_to) ||
									($old_from <= $new_from &&  $new_from < $old_to));
							var_dump(($old_from <= $new_from && $new_to <= $old_to));
							echo "$old_from <= $new_from && $new_to <= $old_to";
							var_dump(($new_from <= $old_from && $old_to <= $new_to));
							//exit();
							$conflict = $subjects[$k];
							$curriculums = $hnd_cu->GetCurriculumById($conflict->curriculum);
							$curriculum = $curriculums[0];
							//create the error message
							$_SESSION['error'] = array("<p>Room Schedule conflict found. See details below: 	<br/>
														<b>Curriculum</b>: {$curriculum->info} 						<br/>
														<b>Subject Description</b>: {$conflict->subject} 			<br/>
														<b>Subject Code</b>: {$conflict->code} 		 				<br/>
														<b>Room</b>: {$rooms[$schedule['room']]->description}	 	<br/>
														<b>Day</b>: {$school_days[$schedule['day']]->description}	<br/>
														<b>From</b>: {$school_times[$schedule['from']]->description}<br/>
														<b>To</b>: {$school_times[$schedule['to']]->description}	<br/>
														</p>");

							break;
						} elseif(
							//checks for instructor, day and time
							$schedule['instructor'] == $instructor && $schedule['day'] == $day &&
							(
								//Same Time
								($old_from == $new_from && $old_to == $new_to) ||
								//Overlapping
								(
									($old_from < $new_to && $new_to <= $old_to) ||
									($old_from <= $new_from &&  $new_from < $old_to)
								) ||
								//New Schedule is Inside Old
								($old_from <= $new_from && $new_to <= $old_to) ||
								//Old Schedule is Inside New
								($new_from <= $old_from && $old_to <= $new_to)
							)
						){
							// // // // $faculties = $hnd_fa->GetFacultiesByKey();

							// // // // //disallow adding of schedule
							// // // // $is_valid = false;
							// // // // //get subject with conflict along with professor details
							// // // // $conflict = $subjects[$k];
							// // // // $curriculums = $hnd_cu->GetCurriculumById($conflict->curriculum);
							// // // // $curriculum = $curriculums[0];
							// // // // //create the error message
							// // // // $middle_initial = "";
							// // // // if(trim($faculties[$instructor]->employee->middle_name) <> ''){
								// // // // $middle_initial . " " . substr($faculties[$instructor]->employee->middle_name,0,1) . ".";
							// // // // }
							// // // // $_SESSION['error'] = array("<p><b>Teaching Load</b> conflict found. See details below: 	<br/>
														// // // // <b>Faculty</b>: " .
														// // // // $faculties[$instructor]->employee->last_name . ", " . $faculties[$instructor]->employee->first_name . $middle_initial
														// // // // . " <br/>
														// // // // <b>Subject Description</b>: {$conflict->subject} 			<br/>
														// // // // <b>Subject Code</b>: {$conflict->code} 		 				<br/>
														// // // // <b>Room</b>: {$rooms[$schedule['room']]->description}	 	<br/>
														// // // // <b>Day</b>: {$school_days[$schedule['day']]->description}	<br/>
														// // // // <b>From</b>: {$school_times[$schedule['from']]->description}<br/>
														// // // // <b>To</b>: {$school_times[$schedule['to']]->description}	<br/>
														// // // // </p>");

							break;
						} elseif(
							//conflict in section subject schedule
							//-->Same day and same time
							$schedule['day'] == $day &&
							(
								//Same Time
								($old_from == $new_from && $old_to == $new_to) ||
								//Overlapping
								(
									($old_from < $new_to && $new_to <= $old_to) ||
									($old_from <= $new_from &&  $new_from < $old_to)
								) ||
								//New Schedule is Inside Old
								($old_from <= $new_from && $new_to <= $old_to) ||
								//Old Schedule is Inside New
								($new_from <= $old_from && $old_to <= $new_to)
							)
						){
							//disallow adding of schedule
							$is_valid = false;
							//get subject with conflict

							$conflict = $subjects[$k];
							$curriculums = $hnd_cu->GetCurriculumById($conflict->curriculum);
							$curriculum = $curriculums[0];
							//create the error message
							$_SESSION['error'] = array("<p>Subject Schedule conflict found. See details below: 	<br/>
														<b>Curriculum</b>: {$curriculum->info} 						<br/>
														<b>Subject Description</b>: {$conflict->subject} 			<br/>
														<b>Subject Code</b>: {$conflict->code} 		 				<br/>
														<b>Room</b>: {$rooms[$schedule['room']]->description}	 	<br/>
														<b>Day</b>: {$school_days[$schedule['day']]->description}	<br/>
														<b>From</b>: {$school_times[$schedule['from']]->description}<br/>
														<b>To</b>: {$school_times[$schedule['to']]->description}	<br/>
														</p>");

							break;
						}
					}
				}
			}

			//Add data to Schedules if VALID
			//-->else, issue an error
			if($is_valid == true){

				$_SESSION['section']['schedules'][$subject_id][sizeof($_SESSION['section']['schedules'][$subject_id])]['day'] = $day;
				$_SESSION['section']['schedules'][$subject_id][sizeof($_SESSION['section']['schedules'][$subject_id])-1]['from'] = $from;
				$_SESSION['section']['schedules'][$subject_id][sizeof($_SESSION['section']['schedules'][$subject_id])-1]['to'] = $to;
				$_SESSION['section']['schedules'][$subject_id][sizeof($_SESSION['section']['schedules'][$subject_id])-1]['instructor'] = $instructor;
				$_SESSION['section']['schedules'][$subject_id][sizeof($_SESSION['section']['schedules'][$subject_id])-1]['room'] = $room;
				$_SESSION['section']['schedules'][$subject_id][sizeof($_SESSION['section']['schedules'][$subject_id])-1]['capacity'] = (int) $rooms[$room]->seating_capacity;
				$_SESSION['section']['schedules'][$subject_id][sizeof($_SESSION['section']['schedules'][$subject_id])-1]['action'] = 'add'; // if SET, means new schedule.
				$_SESSION['section']['changed'] = true;
				// var_dump($to);
				// var_dump($from);
				// var_dump($day);
				// var_dump($instructor);
				// var_dump($room);

				$_SESSION['success'] = "Subject schedule successfully added.";
				header("Location: schedules-section-set_schedule.php?id=$subject_id");
				exit();
			} else {

				//$_SESSION['error'] = array('Subject schedule had a conflict with');
				header("Location: schedules-section-set_schedule.php?id=$subject_id");
				exit();
			}
		}

	// Delete Schedule
	} elseif(isset($_GET['action']) && isset($_GET['id']) && isset($_GET['sid'])){
		$action = (string) $_GET['action'];
		$id = (int) $_GET['id'];
		$sid = (int) $_GET['sid'];

		switch($action){
			case 'delete':
				$_SESSION['section']['schedules'][$sid][$id]['action'] = 'delete';
				//unset($_SESSION['section']['schedules'][$sid][$id]);
				//$_SESSION['section']['schedules'][$sid] = array_values($_SESSION['section']['schedules'][$sid]);
				$_SESSION['section']['changed'] = true;
				$_SESSION['success'] ="Subject schedule successfully removed.";
				header("Location: schedules-section-set_schedule.php?id={$sid}");
				exit();
				break;
		}

	}

	// Edit Schedule
	elseif(isset($_POST['save_schedule'])){
		//get list of subjects from session that is passed
		$subjects = unserialize($_SESSION['section']['subjects']);
		$subject_id = (int) $_POST['subject'];
		$schedule_id = (int) $_POST['schedule'];

		// Get to From and Day
		if(isset($_POST['to']) && isset($_POST['from']) && isset($_POST['day'])){
			$to = (int) $_POST['to'];
			$from = (int) $_POST['from'];
			$day = (int) $_POST['day'];

			if(isset($_POST['to'])){
				$to = (int) $_POST['to'];
				if($to < 1){ $to = null; }
			} else {
				$to = null;
			}

			if(isset($_POST['from'])){
				$from = (int) $_POST['from'];
				if($from < 1){ $from = null; }
			} else {
				$from = null;
			}

			if(isset($_POST['day'])){
				$day = (int) $_POST['day'];
				if($day < 1){ $day = null; }
			} else {
				$day = null;
			}

			if(isset($_POST['instructor'])){
				$instructor = (int) $_POST['instructor'];
				if($instructor < 1){ $instructor = null; }
			} else {
				$instructor = null;
			}

			if(isset($_POST['room'])){
				$room = (int) $_POST['room'];
				if($room < 1){ $room = null; }
			} else {
				$room = null;
			}

			//GET LIST OF TIMES
			$school_times = $hnd_sc->GetSchoolTimesByKey();
			$school_days = $hnd_sc->GetSchoolDaysByKey();
			$rooms = $hnd_fc->GetRoomsByKey();

			//#############################################
			//# ADD CHECKING OF CONFLICT HERE FIRST
			//#############################################
			$is_valid = true; //will be invalid if conflict found

			//#############################
			//## CONDITIONS  ##############
			//#############################
			//-- 1> must not have any conflict with subjects on the SAME SECTION.
					//-Check if subject is already entered on the same section.
			//-- 2> must not have any conflict in rooms (Same Room at the Same Time & Day)
			//-- 3> must not have any conflict in instructor (Same Instructor at the Same Time & Day)

			//## CHECK IF A SUBJECT SCHEDULE IS AVAILABLE
			//--> This is for the same section only
			//--> Must check the database for conflicts too.
			if(sizeof($_SESSION['section']['schedules']) > 0){
				//## GET ALL SUBJECT SCHEDULES on the current semester and school year
				$other_subjects = $hnd_sh->GetAllSubjectSchedulesBySemYear($_SESSION['section']['sem_id'], $_SESSION['section']['sy_id']);

				//## CHECK EACH SUBJECT FOR CONFLICT [DATABASE SIDE]
				foreach($other_subjects as $sub){

					// Don't evaluate same record
					if ($sub->schedule_id == $schedule_id) continue;

					//Get Times to be used in checking
					//-->OLD
					$old_from = $school_times[$sub->from]->Military();
					$old_to = $school_times[$sub->to]->Military();
					//-->NEW
					$new_from = $school_times[$from]->Military();
					$new_to = $school_times[$to]->Military();

					//### THE FF CODE will check for validity of schedule in the current section only
					if(
						//checks for room, day and time
						$sub->room == $room && $sub->day == $day &&
						(
							//Same Time
							($old_from == $new_from && $old_to == $new_to) ||
							//Overlapping
							(
								($old_from < $new_to && $new_to <= $old_to) ||
								($old_from <= $new_from &&  $new_from < $old_to)
							) ||
							//New Schedule is Inside Old
							($old_from <= $new_from && $new_to <= $old_to) ||
							//Old Schedule is Inside New
							($new_from <= $old_from && $old_to <= $new_to)
						)
					){
						//disallow adding of schedule
						$is_valid = false;

						$faculties = $hnd_fa->GetFacultiesByKey();
						$tmp = $hnd_sh->GetSectionSubjectById($sub->section_subject_id);
						foreach($tmp as $grabbed){ $conflict = $grabbed; }

						//disallow adding of schedule
						$is_valid = false;
						//get subject with conflict along with professor details
						$curriculums = $hnd_cu->GetCurriculumById($conflict->section[0]->curriculum);
						$curriculum = $curriculums[0];
						//create the error message
						$middle_initial = "";
						if(trim($faculties[$instructor]->employee->middle_name) <> ''){
							$middle_initial . " " . substr($faculties[$instructor]->employee->middle_name,0,1) . ".";
						}

						//create the error message
						$_SESSION['error'] = array("<p>Room Schedule conflict found. See details below: 	<br/>
													<b>Curriculum</b>: {$curriculum->info} 						<br/>
													<b>Section: </b>: {$conflict->section[0]->name} 			<br/>
													<b>Subject Description</b>: {$conflict->subject} 			<br/>
													<b>Subject Code</b>: {$conflict->code} 		 				<br/>
													<b>Room</b>: {$rooms[$sub->room]->description}	 	<br/>
													<b>Day</b>: {$school_days[$sub->day]->description}	<br/>
													<b>From</b>: {$school_times[$sub->from]->description}<br/>
													<b>To</b>: {$school_times[$sub->to]->description}	<br/>
													</p>");

						break;
					} elseif(
						//checks for instructor, day and time
						$sub->instructor == $instructor && $sub->day == $day &&
						(
							//Same Time
							($old_from == $new_from && $old_to == $new_to) ||
							//Overlapping
							(
								($old_from < $new_to && $new_to <= $old_to) ||
								($old_from <= $new_from &&  $new_from < $old_to)
							) ||
							//New Schedule is Inside Old
							($old_from <= $new_from && $new_to <= $old_to) ||
							//Old Schedule is Inside New
							($new_from <= $old_from && $old_to <= $new_to)
						)
					){
						// // // // $faculties = $hnd_fa->GetFacultiesByKey();
						// // // // $tmp = $hnd_sh->GetSectionSubjectById($sub->section_subject_id);
						// // // // foreach($tmp as $grabbed){ $conflict = $grabbed; }

						// // // // //disallow adding of schedule
						// // // // $is_valid = false;
						// // // // //get subject with conflict along with professor details
						// // // // $curriculums = $hnd_cu->GetCurriculumById($conflict->section[0]->curriculum);
						// // // // $curriculum = $curriculums[0];
						// // // // //create the error message
						// // // // $middle_initial = "";
						// // // // if(trim($faculties[$instructor]->employee->middle_name) <> ''){
							// // // // $middle_initial . " " . substr($faculties[$instructor]->employee->middle_name,0,1) . ".";
						// // // // }
						// // // // $_SESSION['error'] = array("<p><b>Teaching Load</b> conflict found. See details below: 	<br/>
													// // // // <b>Faculty</b>: " .
													// // // // $faculties[$instructor]->employee->last_name . ", " . $faculties[$instructor]->employee->first_name . $middle_initial
													// // // // . " <br/>
													// // // // <b>Section: </b>: {$conflict->section[0]->name} 			<br/>
													// // // // <b>Subject Description</b>: {$conflict->subject} 			<br/>
													// // // // <b>Subject Code</b>: {$conflict->code} 		 				<br/>
													// // // // <b>Room</b>: {$rooms[$sub->room]->description}	 	<br/>
													// // // // <b>Day</b>: {$school_days[$sub->day]->description}	<br/>
													// // // // <b>From</b>: {$school_times[$sub->from]->description}<br/>
													// // // // <b>To</b>: {$school_times[$sub->to]->description}	<br/>
													// // // // </p>");

						break;
					} elseif(
						//conflict in section subject schedule
						//-->Same day and same time
						$sub->day == $day &&
						(
							//Same Time
							($old_from == $new_from && $old_to == $new_to) ||
							//Overlapping
							(
								($old_from < $new_to && $new_to <= $old_to) ||
								($old_from <= $new_from &&  $new_from < $old_to)
							) ||
							//New Schedule is Inside Old
							($old_from <= $new_from && $new_to <= $old_to) ||
							//Old Schedule is Inside New
							($new_from <= $old_from && $old_to <= $new_to)
						)
					){
						// $faculties = $hnd_fa->GetFacultiesByKey();
						// $tmp = $hnd_sh->GetSectionSubjectById($sub->section_subject_id);
						// foreach($tmp as $grabbed){ $conflict = $grabbed; }

						// //disallow adding of schedule
						// $is_valid = false;
						// //get subject with conflict along with professor details
						// $curriculums = $hnd_cu->GetCurriculumById($conflict->section[0]->curriculum);
						// $curriculum = $curriculums[0];

						// //create the error message
						// $_SESSION['error'] = array("<p>Subject Schedule conflict found. See details below: 	<br/>
													// <b>Curriculum</b>: {$curriculum->info} 						<br/>
													// <b>Section: </b>: {$conflict->section[0]->name} 			<br/>
													// <b>Subject Description</b>: {$conflict->subject} 			<br/>
													// <b>Subject Code</b>: {$conflict->code} 		 				<br/>
													// <b>Room</b>: {$rooms[$sub->room]->description}	 	<br/>
													// <b>Day</b>: {$school_days[$sub->day]->description}	<br/>
													// <b>From</b>: {$school_times[$sub->from]->description}<br/>
													// <b>To</b>: {$school_times[$sub->to]->description}	<br/>
													// </p>");

						break;
					}
				}

				//## CHECK EACH SUBJECT FOR CONFLICT [SECTION SIDE]
				$schedule_being_edited = null;
				foreach ($_SESSION['section']['schedules'] as $k => &$subject) {
					//## CHECK EACH SCHEDULE FOR CONFLICT
					foreach ($subject as $l => &$schedule) {

						// Ignore this record if same schedule and subject
						if ($k == $subject_id && $l == $schedule_id) {
							$schedule_being_edited = &$schedule;
							continue;
						}

						echo "{$rooms[$schedule['room']]->code	} == {$rooms[$room]->code} && {$school_days[$schedule['day']]->shorthand} == {$school_days[$day]->shorthand} && ";
						echo "{$school_times[$from]->Military()} >= {$school_times[$schedule['from']]->Military()} && ";
						echo "{$school_times[$from]->Military()} <= {$school_times[$schedule['to']]->Military()} <br/>";
						//$schedule (old)

						//Get Times to be used in checking
						//-->OLD
						$old_from = $school_times[$schedule['from']]->Military();
						$old_to = $school_times[$schedule['to']]->Military();
						//-->NEW
						$new_from = $school_times[$from]->Military();
						$new_to = $school_times[$to]->Military();

						//### THE FF CODE will check for validity of schedule in the current section only
						if(
							//checks for room, day and time
							$schedule['room'] == $room && $schedule['day'] == $day &&
							(
								//Same Time
								($old_from == $new_from && $old_to == $new_to) ||
								//Overlapping
								(
									($old_from < $new_to && $new_to <= $old_to) ||
									($old_from <= $new_from &&  $new_from < $old_to)
								) ||
								//New Schedule is Inside Old
								($old_from <= $new_from && $new_to <= $old_to) ||
								//Old Schedule is Inside New
								($new_from <= $old_from && $old_to <= $new_to)
							)
						){
							//disallow adding of schedule
							$is_valid = false;
							//get subject with conflict
							$conflict = $subjects[$k];
							$curriculums = $hnd_cu->GetCurriculumById($conflict->curriculum);
							$curriculum = $curriculums[0];
							//create the error message
							$_SESSION['error'] = array("<p>Room Schedule conflict found. See details below: 	<br/>
														<b>Curriculum</b>: {$curriculum->info} 						<br/>
														<b>Subject Description</b>: {$conflict->subject} 			<br/>
														<b>Subject Code</b>: {$conflict->code} 		 				<br/>
														<b>Room</b>: {$rooms[$schedule['room']]->description}	 	<br/>
														<b>Day</b>: {$school_days[$schedule['day']]->description}	<br/>
														<b>From</b>: {$school_times[$schedule['from']]->description}<br/>
														<b>To</b>: {$school_times[$schedule['to']]->description}	<br/>
														</p>");

							break;
						} elseif(
							//checks for instructor, day and time
							$schedule['instructor'] == $instructor && $schedule['day'] == $day &&
							(
								//Same Time
								($old_from == $new_from && $old_to == $new_to) ||
								//Overlapping
								(
									($old_from < $new_to && $new_to <= $old_to) ||
									($old_from <= $new_from &&  $new_from < $old_to)
								) ||
								//New Schedule is Inside Old
								($old_from <= $new_from && $new_to <= $old_to) ||
								//Old Schedule is Inside New
								($new_from <= $old_from && $old_to <= $new_to)
							)
						){
							// // // // $faculties = $hnd_fa->GetFacultiesByKey();

							// // // // //disallow adding of schedule
							// // // // $is_valid = false;
							// // // // //get subject with conflict along with professor details
							// // // // $conflict = $subjects[$k];
							// // // // $curriculums = $hnd_cu->GetCurriculumById($conflict->curriculum);
							// // // // $curriculum = $curriculums[0];
							// // // // //create the error message
							// // // // $middle_initial = "";
							// // // // if(trim($faculties[$instructor]->employee->middle_name) <> ''){
								// // // // $middle_initial . " " . substr($faculties[$instructor]->employee->middle_name,0,1) . ".";
							// // // // }
							// // // // $_SESSION['error'] = array("<p><b>Teaching Load</b> conflict found. See details below: 	<br/>
														// // // // <b>Faculty</b>: " .
														// // // // $faculties[$instructor]->employee->last_name . ", " . $faculties[$instructor]->employee->first_name . $middle_initial
														// // // // . " <br/>
														// // // // <b>Subject Description</b>: {$conflict->subject} 			<br/>
														// // // // <b>Subject Code</b>: {$conflict->code} 		 				<br/>
														// // // // <b>Room</b>: {$rooms[$schedule['room']]->description}	 	<br/>
														// // // // <b>Day</b>: {$school_days[$schedule['day']]->description}	<br/>
														// // // // <b>From</b>: {$school_times[$schedule['from']]->description}<br/>
														// // // // <b>To</b>: {$school_times[$schedule['to']]->description}	<br/>
														// // // // </p>");

							break;
						} elseif(
							//conflict in section subject schedule
							//-->Same day and same time
							$schedule['day'] == $day &&
							(
								//Same Time
								($old_from == $new_from && $old_to == $new_to) ||
								//Overlapping
								(
									($old_from < $new_to && $new_to <= $old_to) ||
									($old_from <= $new_from &&  $new_from < $old_to)
								) ||
								//New Schedule is Inside Old
								($old_from <= $new_from && $new_to <= $old_to) ||
								//Old Schedule is Inside New
								($new_from <= $old_from && $old_to <= $new_to)
							)
						){
							//disallow adding of schedule
							$is_valid = false;
							//get subject with conflict

							$conflict = $subjects[$k];
							$curriculums = $hnd_cu->GetCurriculumById($conflict->curriculum);
							$curriculum = $curriculums[0];
							//create the error message
							$_SESSION['error'] = array("<p>Subject Schedule conflict found. See details below: 	<br/>
														<b>Curriculum</b>: {$curriculum->info} 						<br/>
														<b>Subject Description</b>: {$conflict->subject} 			<br/>
														<b>Subject Code</b>: {$conflict->code} 		 				<br/>
														<b>Room</b>: {$rooms[$schedule['room']]->description}	 	<br/>
														<b>Day</b>: {$school_days[$schedule['day']]->description}	<br/>
														<b>From</b>: {$school_times[$schedule['from']]->description}<br/>
														<b>To</b>: {$school_times[$schedule['to']]->description}	<br/>
														</p>");

							break;
						}
					}
				}
			}

			//Add data to Schedules if VALID
			//-->else, issue an error
			if($is_valid == true){
				$_SESSION['section']['schedules'][$subject_id][$schedule_id]['day'] = $day;
				$_SESSION['section']['schedules'][$subject_id][$schedule_id]['from'] = $from;
				$_SESSION['section']['schedules'][$subject_id][$schedule_id]['to'] = $to;
				$_SESSION['section']['schedules'][$subject_id][$schedule_id]['instructor'] = $instructor;
				$_SESSION['section']['schedules'][$subject_id][$schedule_id]['room'] = $room;
				$_SESSION['section']['schedules'][$subject_id][$schedule_id]['capacity'] = (int) $rooms[$room]->seating_capacity;
				$_SESSION['section']['schedules'][$subject_id][$schedule_id]['action'] = 'update';
				$_SESSION['section']['changed'] = true;

				$_SESSION['success'] = "Subject schedule successfully updated.";
				header("Location: schedules-section-set_schedule.php?id=$subject_id");
				exit();
			} else {

				//$_SESSION['error'] = array('Subject schedule had a conflict with');
				header("Location: schedules-section-set_schedule.php?id=$subject_id");
				exit();
			}
		}
	}

	else {
		header("Location: schedules-set_schedule.php");
		exit();
	}

	$conn->Close();
?>
