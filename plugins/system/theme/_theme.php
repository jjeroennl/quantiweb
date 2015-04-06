<?php
	function theme_loadtheme(){
		$theme = system_getsetting("theme");
		if($theme != "admin" && $theme != "" && $theme != null && isset($theme)){
			if(file_exists("themes/" . $theme . "/header.php")){
				include "themes/" . $theme . "/header.php";
			}
			if(file_exists("themes/" . $theme . "/index.php")){
				include "themes/" . $theme . "/index.php";
			}
			if(file_exists("themes/" . $theme . "/footer.php")){
				include "themes/" . $theme . "/footer.php";
			}
		}
	}
	
	function theme_admin(){
		include 'theme_admin.php';
	}

?>
