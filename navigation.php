<?php header("Content-Type: text/xml") ?>
<navigation>
	<menu>
		<display>Users</display>
		<rightNeeded>Users: Administrator</rightNeeded>
		<subMenus>
			<submenu>
				<display>Manage Users</display>
				<href>manage-users.php</href>
				<rightNeeded>Users: Administrator</rightNeeded>
			</submenu>
			<submenu>
				<display>Manage Rights</display>
				<href>manage-rights.php</href>
				<rightNeeded>Users: Administrator</rightNeeded>
			</submenu>
		</subMenus>
	</menu>
	<menu>
		<display>Facilities</display>
		<rightNeeded>Facilities: Administrator</rightNeeded>
		<subMenus>
			<submenu>
				<display>General Information</display>
				<href>manage-users.php</href>
				<rightNeeded>Super Administrator</rightNeeded>
			</submenu>
			<submenu>
				<display>Manage Rights</display>
				<href>manage-rights.php</href>
				<rightNeeded>Super Administrator</rightNeeded>
			</submenu>
		</subMenus>
	</menu>
	
</navigation>