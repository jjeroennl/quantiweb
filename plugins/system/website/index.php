<?php

	plugin_install("website", website_install());
	
	function website_grabpage(){
		$content = content_load("page");
		while($row = db_grab($content)){
			echo '<h3>' . $row['title'] . '</h3>';
			echo $row['the_content'];
			
			
		}
		
	}
	
	function website_install(){
		system_setsetting("index", "website_grabpage");
	}
	
	
?>
