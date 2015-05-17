<?php
//::START OF 'CONFIGURATION'

	//# FLAGS
		//A special FLAG for checking if the site using my PHP files are from my sites only :) haha!
		$flgProtectAJAX_PHP = true;
		//A FLAG to show or hide Favicon true=show || false=hide
		$flgFavicon = false;
		//Set the connection type 1 is online; 0 is offline
		define("CONNECTION_TYPE", 0); //offline

	//# ERROR HANDLING
		error_reporting(E_ALL);

	//# FOLDERS
		//Cascading Stylesheet Folders
		$DIR_SERVER_FOLDER = "";
		$DIR_CSS_DEFAULT = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/design/default/css/";

		$DIR_CSS_PLUGINS = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/design/plugins/css/";
		//Image Folders
		$DIR_IMAGE_DEFAULT = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/design/default/images/";
		$DIR_IMAGE_PLUGINS = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/design/plugins/images/";
		//Javascript Folders
		$DIR_JS_DEFAULT = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/jscript/default/";
		$DIR_JS_PLUGINS = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/jscript/plugins/";
		//Flash Folder
		$DIR_FLASH_DEFAULT = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/flash/default/";
		$DIR_FLASH_PLUGINS = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/flash/plugins/";
		//Gallery Folder (for uploaded images)
		$DIR_GALLERY_LARGE = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/gallery/large/"; //add other folder by purpose in site
		$DIR_GALLERY_THUMB = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/gallery/thumb/"; //add other folder by purpose in site
		//Font Awesome
    $DIR_FONT_AWESOME = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/plugins/font-awesome/css/";
    //JQuery
    $DIR_JQUERY = "http://" . $_SERVER['HTTP_HOST'] . $DIR_SERVER_FOLDER . "/plugins/js/";


    //Classes Folder
		define("RTCLASSLIST", "_libs/rtclasses/");
		define("CLASSLIST","_libs/classes/");


		define("SCHOOL_NAME","UNIVERSAL COLLEGES OF PARAÑAQUE");
		define("SCHOOL_ADDRESS","8273 Dr. A. Santos Ave. Sucat Parañaque");
		define("VERSION", "v1.0");
//::END OF 'CONFIGURATION'
?>
