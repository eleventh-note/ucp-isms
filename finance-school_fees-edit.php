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
	require_once(CLASSLIST . "fin.inc.php");
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
		$PagePrivileges->AddPrivilege("Finance - Administrator");
		$Sentry->CheckPrivilege($PagePrivileges,"isms.php");
		//exit();
	} else {
		header("Location: index.php?error=2");
		exit();
	}
		
	$ISMS = new ISMSConnection(CONNECTION_TYPE);
	$conn = $ISMS->GetConnection();
	$hnd = new EmployeeManager($conn);
	$hnd_fin = new FinanceManager($conn);
	
	//block if id not selected
	if(isset($_GET['id'])){
		$id = (int) $_GET['id'];
		$fees = $hnd_fin->GetFees($id);
		$fee = $fees[$id];
		
		$description = $fee->description;
		$price  = $fee->price;
		$fee_type = $fee->fee_type;
		$is_virtual = $fee->is_virtual;
		
	} else {
		header("Location: finance-school_fees.php");
		exit();
	}
	
	//Dictionaries
	$dict_fee_types = $hnd_fin->GetFeeTypesByKey();
	
	$conn->Close();
	
	//##### PROCESS ERROR or SUCCESS
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
	//Replace Timer Below witd script for javascript logout`
	//###TIMER###
?>
	</head>
	<body id="finance">
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
								<span class="Highlight">Finance Administration &raquo; School Fees &raquo; Edit Fee</span> 
							</h1>
							<p class="">Input new price for this school fee.</p>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($error)){
									echo Sentry::ShowStatus('error',$error);
								}
							?>
							<form action="finance-school_fees-process.php" method="post">
								<input type="hidden" name="id" value="<?php echo $id; ?>" />
								<hr class="form_top"/>
								<div class="table_form">
									<table class="form" cellspacing="0">
										<tr>
											<td class="label">Description</td>
											<td class="input">: <input type="text" name="description" class="input_description" 
												<?php
													if(isset($description)){
														echo " value=\"";
														echo $description;
														echo "\"";
													}
													if($fee_type == 2){
														echo " readonly=\"readonly\" ";
													}
												?>
											/></td>
										</tr>
										<?php												
													if($fee_type!=2){
														echo "<tr>";
															echo "<td class=\"label\">Fee Type</td>";
															echo "<td class=\"input\">: ";
																//#MISCELLANEOUS
																echo "<select name=\"fee_type\" class=\"small\">";
																		
																	foreach($dict_fee_types as $key => $item){
																		if(isset($fee_type)){
																			if($fee_type==$key){
																				echo "<option ";
																				echo "value=\"{$key}\" selected=\"selected\">";
																				echo $item->description;
																				echo "</option>";
																			} else {
																				echo "<option ";
																				echo "value=\"{$key}\">";
																				echo $item->description;
																				echo "</option>";
																			}
																		} else {
																			echo "<option ";
																			echo "value=\"{$key}\">";
																			echo $item->description;
																			echo "</option>";
																		}
																	}
																			
																echo "</select>";
															echo "</td>";
														echo "</tr>";
													} else {
														//# STANDARD
														echo "<input type=\"hidden\" name=\"fee_type\" value=\"2\" />";
													}
												
												
										?>
										<tr>
											<td class="label">Amount</td>
											<td class="input">: <input type="text" name="price" class="input_position" 
												<?php
													if(isset($price)){
														echo " value=\"";
														echo number_format($price,2,".",",");
														echo "\"";
													}
												?>
											/></td>
										</tr>
<?php
									if($fee_type==1){
										echo "<tr>";
											echo "<td class=\"label\">Is Added</td>";
											echo "<td class=\"input\">: <input type=\"checkbox\" name=\"virtual\" value=\"1\"";
												
													if(isset($is_virtual)){
														if($is_virtual==1){
															echo " checked=\"checked\"";
														}
													}
												
											echo "/></td>";
										echo "</tr>";
									}
?>
									</table>						
									<hr class="form_top"/>
									<table class="form" cellspacing="0">
										<tr class="button">
											<td colspan="2">
												<input type="button" class="button" value="Cancel" onclick="window.location='finance-school_fees.php';"/>
												<input type="submit" class="button" name="fee_save" value="Save" />
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