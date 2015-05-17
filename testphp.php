<?php
	/* #-------------------------------------------------
	   #
	   #	Description:	Template for 00 Default Layout
	   #	Author:		Algefmarc A. L. Almocera
	   #	Date Started:	December 02, 2011
	   #	Last Modified:	December 02, 2011
	   #
	   #-------------------------------------------------
	*/

	//Set no caching
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	header("Cache-Control: no-store, no-cache, must-revalidate"); 
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

//::START OF 'SESSION DECLARATION'
	//open session here if needed (e.g: session_start())
	session_start();
//::END OF 'SESSION DECLARATION'

//::START OF 'CONFIGURATION'
	require_once("_system/_config/sys_config.php");
	//configurations can be overriden here
	include_once(CLASSLIST . "dataconnection.inc.php");
	include_once(CLASSLIST . "user.inc.php");
	include_once(CLASSLIST . "gen.inc.php");
	include_once(CLASSLIST . "reg.inc.php");
	include_once(CLASSLIST . "emp.inc.php");
	include_once(CLASSLIST . "dvsns.inc.php");
	include_once(CLASSLIST . "fclts.inc.php");
	include_once(CLASSLIST . "cllgs.inc.php");
	include_once(CLASSLIST . "grds.inc.php");
	include_once(CLASSLIST . "sbjcts.inc.php");
	include_once(CLASSLIST . "crrclm.inc.php");
	include_once(CLASSLIST . "schl.inc.php");
		
	$isms = new ISMSConnection(CONNECTION_TYPE);
	$conn = $isms->GetConnection();
		
	//process data to be saved from add subjects
	if(isset($_POST['type'])){
		$type = $_POST['type'];
		$group = $_POST['group'];
		$code = $_POST['code'];
		$description = $_POST['description'];
		$units = $_POST['units'];
		$virtual = 0;

		$hnd = new SubjectManager($conn);
				
		if($hnd->AddSubject($code, $description, $units, $type, $group, $virtual) == true){
			echo "success <br/>";
			unset($group);
			unset($code);
			unset($description);
			unset($units);
			unset($virtual);
			unset($type);
		} else {
			var_dump($hnd->error);
		}
		
	}
	
	if($conn != null){
		$hndler = new SubjectManager($conn);

		$subjects = $hndler->GetSubjects();
		$types = $hndler->GetSubjectTypes();
		$groups = $hndler->GetSubjectGroups();
		
		
		$cur = new CurriculumManager($conn);
		
		$curriculums = $cur->GetCurriculums(1); //engineering only
		
		$sch = new SchoolManager($conn);		
		
		$sems = $sch->GetSemesters();
		
		$cou = new CourseManager($conn);
		
		$levels = $cou->GetYearLevels();
		
		if(1) {
			//echo "success";
		} else {
			var_dump($hndler->error);
		}
	}
	
//::END OF 'CONFIGURATION'

?>

<html>
	<body>
	
		<!----------------------------------------------------------------------
		
			LOADING OF SUBJECTS IN CURRICULUM
			
		----------------------------------------------------------------------->
		<?php if(1==1){ ?>
		
			<h3>Add Curriculum Subjects</h3>
		
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<select name="curriculum">
					<option value="-1"></option>
					<?php 

						foreach($curriculums as $c){
							if(isset($curriculum)){
							
								if($curriculum == $c->curriculum_id){
									echo "<option value=\"{$c->curriculum_id}\" selected=\"selected\">{$c->info}</option>";
								} else {
									echo "<option value=\"{$c->curriculum_id}\" >{$c->info}</option>";
								}
							} else {
								echo "<option value=\"{$c->curriculum_id}\" >{$c->info}</option>";
							}				
						}
						
					?>
					
				</select>
				
				<select name="subject">
					<option value="-1"></option>
					<?php 

						foreach($subjects as $c){
							if(isset($subject)){
							
								if($subject == $c->subject_id){
									echo "<option value=\"{$c->subject_id}\" selected=\"selected\">{$c->description}</option>";
								} else {
									echo "<option value=\"{$c->subject_id}\" >{$c->description}</option>";
								}
							} else {
								echo "<option value=\"{$c->subject_id}\" >{$c->description}</option>";
							}				
						}
						
					?>
					
				</select>
				
				<select name="semester">
					<option value="-1"></option>
					<?php 

						foreach($sems as $c){
							if(isset($semester)){
							
								if($semester == $c->semester_id){
									echo "<option value=\"{$c->semester_id}\" selected=\"selected\">{$c->description}</option>";
								} else {
									echo "<option value=\"{$c->semester_id}\" >{$c->description}</option>";
								}
							} else {
								echo "<option value=\"{$c->semester_id}\" >{$c->description}</option>";
							}				
						}
						
					?>
					
				</select>
				
				<select name="level">
					<option value="-1"></option>
					<?php 

						foreach($levels as $c){
							if(isset($level)){
							
								if($level == $c->level_id){
									echo "<option value=\"{$c->level_id}\" selected=\"selected\">{$c->description}</option>";
								} else {
									echo "<option value=\"{$c->level_id}\" >{$c->description}</option>";
								}
							} else {
								echo "<option value=\"{$c->level_id}\" >{$c->description}</option>";
							}				
						}
						
					?>
					
				</select>
				
				<input type="submit" name="add_subject" value="Add Subject" />
			</form>
		<?php } ?>		
		<!----------------------------------------------------------------------
		
			LOADING OF SUBJECTS
			
		----------------------------------------------------------------------->	
		<?php if(1==0){ ?>
		<h3>Add Subjects</h3>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<select name="type">
				<option value="-1"></option>
				<?php 

					foreach($types as $t){
						if(isset($type)){
						
							if($type == $t->type_id){
								echo "<option value=\"{$t->type_id}\" selected=\"selected\">{$t->description}</option>";
							} else {
								echo "<option value=\"{$t->type_id}\">{$t->description}</option>";
							}
						} else {
							echo "<option value=\"{$t->type_id}\">{$t->description}</option>";
						}				
					}
				?>
				
			</select>
			
			<select name="group">
				<option value="-1"></option>
				<?php 
					foreach($groups as $g){
					
						if(isset($group)){
							if($group == $g->group_id){
								echo "<option value=\"{$g->group_id}\" selected=\"selected\">{$g->description}</option>";
							} else {
								echo "<option value=\"{$g->group_id}\">{$g->description}</option>";
							}
						} else {
							echo "<option value=\"{$g->group_id}\">{$g->description}</option>";
						}
					}
				?>
				
			</select>
			<p>Code: <input type="text" name="code" <?php if(isset($code)){ echo "value=\"$code\""; } ?> /></p>
			<p>Description: <input type="text" name="description"  <?php if(isset($description)){ echo "value=\"$description\""; } ?>/></p>
			<p>Units: <input type="text" name="units"  <?php if(isset($units)){ echo "value=\"$units\""; } ?>/></p>
			<p><input type="submit" /></p>
		</form>

		<?php } ?>		
		
		<ol>
		<?php
			//display subjects
			foreach($subjects as $data){
				//echo "<li>{$data->code} - {$data->description}</li>";
			}
		?>		
	
		</ol>
	</body>
</html>
