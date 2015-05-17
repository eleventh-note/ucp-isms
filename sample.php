<?php
	error_reporting(E_ALL);
	include_once("_classes/form/rt.formgenerator.php");
	
	$Form = new RTFormCreator("TheTransformer");
	
	$Form->addText("Name", "Name:", "", null, 10);
	$Form->addText("Name2", "Name:", "");
	$Form->addTextarea("Address", "Address:", "", 0);
	$Form->addTextarea("Address2", "Address:", "", 0);
	$Form->addSubmitButton("Submit","","Submit", 0);
?>
<html>
	<head>
		<style type="text/css">
			html{
				font: 67.5%/1.5 Tahoma;
			}
			p.FormField label{
				display: block;
			}
			
			p.FormField input, p.FormField textarea{
				width: 200px;
				font: 1em/1.5 Tahoma;
			}
			
			p.FormField_Submit input{
				width: 200px;
				border: 1px solid #000;
				background-color: #000;
				color: #fff;
			}
		</style>
	</head>
	<body>

	<?php

		if(isset($_POST)){
			echo $Form->GetProcessedForm($_POST);
		}
		if(isset($Form)){
			echo $Form->GetForm();
		}
		
	?>
		
	</body>
</html>