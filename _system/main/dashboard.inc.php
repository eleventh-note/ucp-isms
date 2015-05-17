<div id="dashboard">
	<div class="inner">
		<p class="greetings">Welcome! You are currently logged in as 
			<span class="highlight">
				<?php echo $UserInfo->username; ?>
			</span>
			<?php
				//SHOW SCHOOL YEAR AND SEMESTER DETAILS
				require_once(CLASSLIST . 'fix.inc.php');
				$ISMS2 = new ISMSConnection(CONNECTION_TYPE);
				$conn2 = $ISMS2->GetConnection();	
				$dash = new FixManager($conn2);
				$tmp_sy = $dash->GetActiveSchoolYear();
				$tmp_sem = $dash->GetActiveSemester();
				$curr_sy = $tmp_sy[0]->start . "-" . $tmp_sy[0]->end;
				$curr_sem = $tmp_sem[0]->description;
				
				if(isset($curr_sy)){
					echo " ---- | ---- S.Y <span class=\"highlight\">" . $curr_sy;
					echo "</span> / <span class=\"highlight\">" . $curr_sem . "</span>";
					$conn2->close();
				}
			?>
		</p>
		<div class="options">
			<ul>
				<li><a href="account-settings.php">Account Settings</a>&nbsp;&nbsp;&nbsp;|</li>
				<li><a href="logout.php">Log Out</a></li>
			</ul>
		</div>
	</div>
</div>