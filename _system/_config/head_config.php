<?php
//::START OF 'DEFAULT HEAD CONFIG'

	echo "<title>" . $title . "</title>";

	if($flgFavicon == true){
		echo "<link rel=\"shortcut icon\" href=\"/favicon.ico\" type=\"image/x-icon\" />";
	}

	//# CSS RESET by MAYERS
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "cssReset.css\" />";
	//# DEFAULT CSS - override as needed
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_DEFAULT . "general.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_CSS_PLUGINS . "general.css\" />";
	echo "<link rel=\"stylesheet\" href=\"" . $DIR_FONT_AWESOME . "font-awesome.min.css\" />";

	//# JAVASCRIPT
	echo '<script type="text/javascript" src="' . $DIR_JQUERY . 'jquery-1.10.2.min.js"></script>';

	//# META TAGS
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\" />";

	if(isset($keywords) && !empty($keywords)){
		echo "<meta name=\"keywords\" content=\"$keywords\" />";
	}

	if(isset($description) && !empty($description)){
		echo "<meta name=\"description\" content=\"$description\" />";
	}

	if(isset($author) && !empty($author)){
		echo "<meta name=\"author\" content=\"$author\" />";
	}

	if(isset($robots) && !empty($robots)){
		echo "<meta name=\"robots\" content=\"$robots\" />";
	}

//::END OF 'DEFAULT HEAD CONFIG'
?>
