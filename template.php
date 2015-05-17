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
//::END OF 'SESSION DECLARATION'

//::START OF 'CONFIGURATION'
	require_once("_system/_config/sys_config.php");
	//configurations can be overriden here
//::END OF 'CONFIGURATION'

	//# General Variables - shown in all documents for easy modification
		$title = "00 - Default Layout";
		$keywords = "";
		$description = "";
		$author = "";
		$robots="index,nofollow";
		
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
	
	
	//# Other Javascript Loaded Here
?>
	</head>
	<body>
		<h1>Lorem Ipsum H1</h1>
		<h2>Lorem Ipsum H2</h2>
		<h3>Lorem Ipsum H3</h3>
		<h4>Lorem Ipsum H4</h4>
		<h5>Lorem Ipsum H5</h5>
		<h6>Lorem Ipsum H6</h6>
	
		<p class="indent1">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed felis dui, viverra gravida consectetur id, mattis et augue. Morbi sed nulla dolor. Nulla vitae tincidunt erat. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam vel massa vitae nisi rhoncus fringilla id et purus. Pellentesque rutrum gravida sodales. Fusce pretium vehicula purus, vitae auctor justo consectetur in. Phasellus et laoreet sapien. Morbi adipiscing arcu pellentesque nisi dignissim a lacinia odio adipiscing. Praesent dictum molestie tortor non venenatis. Vivamus augue ipsum, auctor ac interdum non, adipiscing at lacus. Nulla sodales aliquet suscipit. Aenean orci est, imperdiet non porta vitae, faucibus ac lorem. Aliquam erat volutpat. Aenean non lorem vitae erat dignissim suscipit. Suspendisse sit amet odio quis leo imperdiet ullamcorper. Quisque tincidunt ante vel risus tincidunt at luctus lacus consectetur. Nullam sed turpis mi.</p>
		
		<p class="lh15 indent2">Ut nibh justo, blandit vel facilisis ut, gravida sed diam. Integer vel bibendum dolor. Phasellus semper elit sed dui feugiat mattis. Donec congue ornare mi, in aliquam leo blandit eget. Quisque tempus mollis enim, eget dapibus eros dignissim at. Nam nulla mi, bibendum nec elementum eget, fermentum a ligula. Nam lectus mauris, rutrum ac ultrices a, dignissim eu nulla. Donec dui nisi, luctus eget facilisis interdum, congue eget sapien. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed eu libero id libero egestas bibendum.</p>
		
		<p class="lh2 indent3">Vivamus bibendum rutrum enim, at pharetra diam molestie nec. Aliquam sollicitudin vestibulum varius. Curabitur vitae odio mauris, ut eleifend tortor. Proin tincidunt nulla orci. Duis blandit vestibulum lacinia. Praesent auctor dapibus mi in hendrerit. Curabitur risus erat, egestas non vestibulum eu, semper vel orci. Nullam venenatis tellus sollicitudin elit interdum facilisis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus semper mauris sit amet nibh faucibus vestibulum. Maecenas felis mi, blandit nec hendrerit ac, mollis eu eros. Nulla faucibus diam id augue pellentesque id rhoncus arcu iaculis.</p>
		
		<h3>An (U)nordered (L)ist Follows:</h3>
		<ul>
			<li>(U)nordered (L)ist (I)tem 1</li>
			<li>(U)nordered (L)ist (I)tem 2</li>
			<li>(U)nordered (L)ist (I)tem 3</li>
			<li>(U)nordered (L)ist (I)tem 4</li>
		</ul>
		
		<h3>An (O)rdered (L)ist Follows:</h3>
		<ol>
			<li>(O)rdered (L)ist (I)tem 1</li>
			<li>(O)rdered (L)ist (I)tem 2</li>
			<li>(O)rdered (L)ist (I)tem 3</li>
			<li>(O)rdered (L)ist (I)tem 4</li>
		</ol>
		
		<table style="border: 1px solid #000;">
			<tr>	
				<th>Item 1</th>
				<th>Item 2</th>
				<th>Item 3</th>
				<th>Item 4</th>
			</tr>
			<tr>	
				<td>Row 1 Item 1</td>
				<td>Row 1 Item 2</td>
				<td>Row 1 Item 3</td>
				<td>Row 1 Item 4</td>
			</tr>
			<tr>	
				<td>Row 2 Item 1</td>
				<td>Row 2 Item 2</td>
				<td colspan="2" rowspan="2">Row 2 Item 3</td>
			</tr>
			<tr>	
				<td>Row 3 Item 1</td>
				<td>Row 3 Item 2</td>
			</tr>
			<tr>	
				<td>Row 4 Item 1</td>
				<td>Row 4 Item 2</td>
				<td>Row 4 Item 3</td>
				<td>Row 4 Item 4</td>
			</tr>
		</table>
	</body>
</html>
<?php
	//::START OF 'CLOSING REMARKS'
		//memory releasing and stuffs
	//::END OF 'CLOSING REMARKS'