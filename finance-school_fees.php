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
	$hnd = new FinanceManager($conn);

	//dictionaries
	$dict_fee_types = $hnd->GetFeeTypesByKey();
	$misc_fees = $hnd->GetMiscFees();
	$other_fees = $hnd->GetOtherFees();
	$laboratory_fees = $hnd->GetLabFees();
	$energy_fees = $hnd->GetEnergyFees();
	$misc_fee = $hnd->GetMiscFee();
	$std_fees = $hnd->GetStdFees();
	$gen_fees = $hnd->GetGenFees();
	$cash_basis = $hnd->GetCashBasis();
	$installment_basis = $hnd->GetInstallmentBasis();
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
								<span class="Highlight">Finance Administration &raquo; School Fees</span>
							</h1>
							<div id="actions">
								<p class="action">
									<input type="button" value="Go Back" onclick="window.location='finance.php'" />
									<input type="button" value="Add School Fee" onclick="window.location='finance-school_fees-add.php'" />
								</p>
							</div>
							<?php
								//##### PASS ERROR IF FOUND
								if(isset($success)){
									echo Sentry::ShowStatus('success',$success);
								}
							?>
							<div class="table">
								<?php
									/*#####################################
									  ##	STANDARD FEES
									  #####################################*/
								?>
								<table class="positions" cellspacing="0" title="">
									<thead>
										<th colspan="5" class="year_level">
											STANDARD FEES
										</th>
									</thead>
									<thead>
										<th id="Count">No.</th>
										<th id="Position">School Fee</th>
										<th id="">Amount</th>
										<th id="Actions"></th>
									</thead>
									<?php
										$ctr = 0;
										foreach($std_fees as $item){
											if ($item->description == 'Tuition Fee - Cash Basis') continue;
											if($item->is_virtual == 0){
												$ctr++;
												//define the odd even tables
												if($ctr % 2 == 0){
													echo "<tr class=\"even\">";
												} else {
													echo "<tr class=\"odd\">";
												}
													echo "<td>{$ctr}</td>";
													echo "<td>{$item->description}</td>";
													if($item->description == 'Miscellaneous Fee'){
														echo "<td>PHP " . number_format($misc_fee,2 ,".",",") . "</td>";
													} else {
														echo "<td>PHP " . number_format($item->price,2 ,".",",") . "</td>";
													}
													echo "<td class=\"Actions\">";
														if($item->description != 'Miscellaneous Fee'){
															echo "<a href=\"finance-school_fees-edit.php?id={$item->fee_id}\">Edit</a>";
														}
													echo "</td>";
												echo "</tr>";
											}
										}
										if($ctr==0){
											echo "<tr><td colspan=\"5\">No miscellaneous fees defined.</td></tr>";
										} else {

									?>
									<!--
									<thead>
										<th colspan="4" style="font-size: 1.2em; text-align: center;">
											Total Cash Basis: (Php <?php echo number_format($cash_basis,2 ,".",","); ?>)
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											Total Installment Basis: (Php <?php echo number_format($installment_basis,2 ,".",","); ?>)
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<!--
											<img class="pdf_icon" style="margin-bottom:-8px;" src="<?php echo $DIR_IMAGE_DEFAULT . "icons/file_pdf.png"; ?>"/>
											<input type="button" style="padding: 5px 10px 5px 10px" class="button" value="Get Assessment" />
											-->
										</th>
									</thead>
									<?php
										}
									?>

								</table>

								<?php
									/*#####################################
									  ##	OTHER FEES
									  #####################################*/
								?>
								<br/>
								<table class="positions" cellspacing="0" title="">
									<thead>
										<th colspan="5" class="year_level">
											OTHER FEES
										</th>
									</thead>
									<thead>
										<th id="Count">No.</th>
										<th id="Position">School Fee</th>
										<th id="">Amount</th>
										<th id="Actions"></th>
									</thead>
									<?php
										$ctr = 0;
										foreach($other_fees as $item){
											if ($item->description == 'Tuition Fee - Cash Basis') continue;
											//if($item->is_virtual == 0){
												$ctr++;
												//define the odd even tables
												if($ctr % 2 == 0){
													echo "<tr class=\"even\">";
												} else {
													echo "<tr class=\"odd\">";
												}
													echo "<td>{$ctr}</td>";
													echo "<td>{$item->description}</td>";
													echo "<td>PHP " . number_format($item->price,2 ,".",",") . "</td>";

													echo "<td class=\"Actions\">";
														if($item->description != 'Miscellaneous Fee'){
															echo "<a href=\"finance-school_fees-edit.php?id={$item->fee_id}\">Edit</a>";
															echo " | ";
															echo "<a href=\"finance-school_fees-process.php?id={$item->fee_id}&action=delete\" onclick=\"return confirm('Delete fee? Click OK to continue.')\">Delete</a>";
														}
													echo "</td>";
												echo "</tr>";
											//}
										}
										if($ctr==0){
											echo "<tr><td colspan=\"5\">No other fees defined.</td></tr>";
										} else {

									?>
									<!--
									<thead>
										<th colspan="4" style="font-size: 1.2em; text-align: center;">
											Total Cash Basis: (Php <?php echo number_format($cash_basis,2 ,".",","); ?>)
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											Total Installment Basis: (Php <?php echo number_format($installment_basis,2 ,".",","); ?>)
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<!--
											<img class="pdf_icon" style="margin-bottom:-8px;" src="<?php echo $DIR_IMAGE_DEFAULT . "icons/file_pdf.png"; ?>"/>
											<input type="button" style="padding: 5px 10px 5px 10px" class="button" value="Get Assessment" />
											-->
										</th>
									</thead>
									<?php
										}
									?>

								</table>

								<?php
									/*#####################################
									  ##	LABORATORY FEES
									  #####################################*/
								?>
								<br/>
								<table class="positions" cellspacing="0" title="">
									<thead>
										<th colspan="5" class="year_level">
											LABORATORY FEES
										</th>
									</thead>
									<thead>
										<th id="Count">No.</th>
										<th id="Position">School Fee</th>
										<th id="">Amount</th>
										<th id="Actions"></th>
									</thead>
									<?php
										$ctr = 0;
										foreach($laboratory_fees as $item){
											if ($item->description == 'Tuition Fee - Cash Basis') continue;
											//if($item->is_virtual == 0){
												$ctr++;
												//define the odd even tables
												if($ctr % 2 == 0){
													echo "<tr class=\"even\">";
												} else {
													echo "<tr class=\"odd\">";
												}
													echo "<td>{$ctr}</td>";
													echo "<td>{$item->description}</td>";
													echo "<td>PHP " . number_format($item->price,2 ,".",",") . "</td>";

													echo "<td class=\"Actions\">";
														if($item->description != 'Miscellaneous Fee'){
															echo "<a href=\"finance-school_fees-edit.php?id={$item->fee_id}\">Edit</a>";
															echo " | ";
															echo "<a href=\"finance-school_fees-process.php?id={$item->fee_id}&action=delete\" onclick=\"return confirm('Delete fee? Click OK to continue.')\">Delete</a>";
														}
													echo "</td>";
												echo "</tr>";
											//}
										}
										if($ctr==0){
											echo "<tr><td colspan=\"5\">No other fees defined.</td></tr>";
										} else {

									?>
									<!--
									<thead>
										<th colspan="4" style="font-size: 1.2em; text-align: center;">
											Total Cash Basis: (Php <?php echo number_format($cash_basis,2 ,".",","); ?>)
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											Total Installment Basis: (Php <?php echo number_format($installment_basis,2 ,".",","); ?>)
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<!--
											<img class="pdf_icon" style="margin-bottom:-8px;" src="<?php echo $DIR_IMAGE_DEFAULT . "icons/file_pdf.png"; ?>"/>
											<input type="button" style="padding: 5px 10px 5px 10px" class="button" value="Get Assessment" />
											-->
										</th>
									</thead>
									<?php
										}
									?>

								</table>
								<br/>

								<?php
									/*#####################################
									  ##	ENERGY FEES
									  #####################################*/
								?>
								<table class="positions" cellspacing="0" title="">
									<thead>
										<th colspan="5" class="year_level">
											ENERGY FEES
										</th>
									</thead>
									<thead>
										<th id="Count">No.</th>
										<th id="Position">School Fee</th>
										<th id="">Amount</th>
										<th id="Actions"></th>
									</thead>
									<?php
										$ctr = 0;
										foreach($energy_fees as $item){
											if ($item->description == 'Tuition Fee - Cash Basis') continue;
											//if($item->is_virtual == 0){
												$ctr++;
												//define the odd even tables
												if($ctr % 2 == 0){
													echo "<tr class=\"even\">";
												} else {
													echo "<tr class=\"odd\">";
												}
													echo "<td>{$ctr}</td>";
													echo "<td>{$item->description}</td>";
													echo "<td>PHP " . number_format($item->price,2 ,".",",") . "</td>";

													echo "<td class=\"Actions\">";
														if($item->description != 'Miscellaneous Fee'){
															echo "<a href=\"finance-school_fees-edit.php?id={$item->fee_id}\">Edit</a>";
															echo " | ";
															echo "<a href=\"finance-school_fees-process.php?id={$item->fee_id}&action=delete\" onclick=\"return confirm('Delete fee? Click OK to continue.')\">Delete</a>";
														}
													echo "</td>";
												echo "</tr>";
											//}
										}
										if($ctr==0){
											echo "<tr><td colspan=\"5\">No other fees defined.</td></tr>";
										} else {

									?>
									<!--
									<thead>
										<th colspan="4" style="font-size: 1.2em; text-align: center;">
											Total Cash Basis: (Php <?php echo number_format($cash_basis,2 ,".",","); ?>)
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											Total Installment Basis: (Php <?php echo number_format($installment_basis,2 ,".",","); ?>)
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<!--
											<img class="pdf_icon" style="margin-bottom:-8px;" src="<?php echo $DIR_IMAGE_DEFAULT . "icons/file_pdf.png"; ?>"/>
											<input type="button" style="padding: 5px 10px 5px 10px" class="button" value="Get Assessment" />
											-->
										</th>
									</thead>
									<?php
										}
									?>
								</table>

								<?php
									/*#####################################
									  ##	MISCELLANEOUS FEES
									  #####################################*/
								?>
								<table class="positions" style="margin-top: 10px;" cellspacing="0" title="">
									<thead>
										<th colspan="5" class="year_level">
											MISCELLANEOUS FEES
										</th>
									</thead>
									<thead>
										<th id="Count">No.</th>
										<th id="Position">School Fee</th>
										<th id="">Amount</th>
										<th id="">Is Added</th>
										<th id="Actions"></th>
									</thead>
									<?php
										$ctr = 0;
										foreach($misc_fees as $item){
											$ctr++;
											//define the odd even tables
											if($ctr % 2 == 0){
												echo "<tr class=\"even\">";
											} else {
												echo "<tr class=\"odd\">";
											}
												echo "<td>{$ctr}</td>";
												echo "<td>{$item->description}</td>";
												echo "<td>PHP " . number_format($item->price,2 ,".",",") . "</td>";
												echo "<td>";
													if($item->is_virtual==1){
														echo "YES";
													} else {
														echo "NO";
													}
												echo "</td>";
												echo "<td class=\"Actions\">";
													echo "<a href=\"finance-school_fees-edit.php?id={$item->fee_id}\">Edit</a>";
													echo " | ";
													echo "<a href=\"finance-school_fees-process.php?id={$item->fee_id}&action=delete\" onclick=\"return confirm('Delete fee? Click OK to continue.')\">Delete</a>";
												echo "</td>";
											echo "</tr>";
										}
										if($ctr==0){
											echo "<tr><td colspan=\"5\">No miscellaneous fees defined.</td></tr>";
										}
									?>

								</table>
								<?php
									/*#####################################
									  ##	GENERAL FEES
									  #####################################*/
								?>
								<table class="" style="margin-top: 10px; width: 400px; " cellspacing="0" title="">
									<thead>
										<th colspan="2" class="year_level">
											GENERAL FEES
										</th>
									</thead>
									<thead>
										<th id="Count">No.</th>
										<th id="Position">School Fee</th>
									</thead>
									<?php
										$ctr = 0;
										foreach($gen_fees as $item){
											$ctr++;
											//define the odd even tables
											if($ctr % 2 == 0){
												echo "<tr class=\"even\">";
											} else {
												echo "<tr class=\"odd\">";
											}
												echo "<td>{$ctr}</td>";
												echo "<td>{$item->description}</td>";
											echo "</tr>";
										}
										if($ctr==0){
											echo "<tr><td colspan=\"5\">No general fees defined.</td></tr>";
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
