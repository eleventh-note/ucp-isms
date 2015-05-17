<?php
	/* #-------------------------------------------------
	   #
	   #	Description:	Template for 00 Default Layout
	   #	Author:		Algefmarc A. L. Almocera
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
	echo CLASSLIST . "sentry.inc.php";
	require_once(CLASSLIST . "sentry.inc.php");
	//configurations can be overriden here
	
//::END OF 'CONFIGURATION'

	//# General Variables - shown in all documents for easy modification
		$title = SCHOOL_NAME . " Integrated School Management System";
		$keywords = "";
		$description = "";
		$author = "";
		$robots="noindex,nofollow";
		
	//Before continuing output to system. Authenticate User Again
	//Redirect user to isms.php if logged in
	if(isset($_SESSION['UserInfo'])){
		header("Location:isms.php");
		exit();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xml:lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
	<head>
<?php
//::START OF 'DEFAULT HEAD CONFIG'
	require_once("_system/_config/head_config.php");
//::END OF 'DEFAULT HEAD CONFIG'
	
	//# Process PHP
		//includes PHP Classes here
		
	//# Other CSS Loaded Here
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "loginform.css\" />";
	
	if(isset($_GET['error'])){
		$error = $_GET['error'];
		
		switch($error){
			case 1:
				$error = array();
				$error[] = 'Invalid username/password.';
				break;
			case 2:
				$error = array();
				$error[] = 'You do not have enough privileges to view the previous page.';
				break;
		}
	}//# end if [error]
?>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div class="content">
					<?php require_once("_system/main/banner.inc.php"); ?>
				</div>
				<div id="dashboard">
					<div class="inner">
					</div>
				</div>
			</div><?php //end of header ?>
			<div id="body">
				<div class="content">
					<?php
						//##### PASS ERROR IF FOUND
						if(isset($success)){
							echo Sentry::ShowStatus('success',$success);
						}
						if(isset($error)){
							echo Sentry::ShowStatus('error',$error);
						}
					?>
					<div id="login-form">
						<div class="inner">
							<?php //LOGIN ?>
							<h2>Login</h2>
							<p>
								If you have forgotten your password, Please inform your administrator.
							</p>
							<form method="post" action="verify-login.php">
								<p class="adjust-padding-top">
									<label><span>Username:</span></label>
									<input type="text" class="input_login" name="username" />
								</p>
								<p class="adjust-padding-bottom">
									<label><span>Password:</span></label>
									<input type="password" class="input_login" name="password" />
								</p>
								<p class="button">
									<label><span>&nbsp;</span></label>
									<input type="submit" value="Log In" name="login"/>
								</p>
							</form>
						</div>
					</div>
				</div>
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