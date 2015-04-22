<?php
	function plugin_install($plugin, $function){
		$installed_plugins = system_getplugins();
	
		if(!in_array($plugin, $installed_plugins)){
			$curplugin = system_getsetting("plugins");
			if($curplugin != ""){
				$newplugin = $curplugin . ',' .$plugin;
			}
			else{
				$newplugin = $plugin;
			}
			
			
			
			db_update("system_settings", array("value" => $newplugin), array("setting" => "plugins"));
			$function();
		}
	}
	
	function plugins_load(){
		$allplugins = db_select("value", "system_settings", array(
			"setting" => "plugins"
		));
		
		while($row = db_grab($allplugins)){
			$plugins = $row['value'];
		}
		
		$plugin_array = explode(",", $plugins);
		
		foreach($plugin_array as $plugin){
			if(substr($plugin,0,1) == "!"){
				$plugin = substr($plugin, 1);
				$plugin = "plugins/system/" . $plugin . "/_" . $plugin . ".php";
				if(file_exists($plugin)){
					include $plugin;
				}
			}
			
			else{
				$plugin = "plugins/" . $plugin . "/index.php";
				if(file_exists($plugin)){
					include $plugin;
				}
			}
		}
		
	}
	
	function plugins_config(){
		$send = 1;
		include 'pluginconfig.php';
	}
	
	function plugin_scan($plugin){
		//score points
		$score = 10;

		
	
		 $return = _plugin_terms($plugin);
		

		
		
		echo "<ul>";
		if(isset($return["db"])){
			echo "<li>Get content out of the database</li>";
			$score = $score - 1;
		}
		if(isset($return["setting"])){
			echo "<li>Get settings from the system</li>";
		}
		if(isset($return["db_delete"])){
			echo "<li>Delete content from your database.</li>";
			$score = $score - 6;
		}
			if(isset($return["content_"])){
			echo "<li>Create, modify or delete content</li>";
			
		}
		
		echo "</ul>";
		
		if(!in_array(1, $return)){
			echo "This plugin requires no access to any " . OFFICIAL_NAME . " or database function.";
		}
		//echo $score;
		//return $clearance;
	}
	
	function _plugin_score($plugin){
		$score = 10;
		$return['db'] = 0;
		$return['setting'] = 0;
		$return['mysql'] = 0;
		$return['mysqli'] = 0;
		$return['db_delete'] = 0;
		$return['content_'] = 0;
		
		$return = _plugin_terms($plugin);
		if(isset($return["db"])){
			$score = $score - 1;
		}
		if(isset($return["setting"])){

		}
		if(isset($return["db_delete"])){
			
			$score = $score - 6;
		}
		if(isset($return["content_"])){
			
			$score = $score - 1;
		}
		
		return $score;
	}
	
	function _plugin_terms($plugin){
		$return['db'] = 0;
		$return['setting'] = 0;
		$return['mysql'] = 0;
		$return['mysqli'] = 0;
		$return['db_delete'] = 0;
		$return['content_'] = 0;
		$return['content_delete'] = 0;
		$clearance = 1;
		
		$return = array();
		
		$code = _plugin_get($plugin);
		
		if(strpos($code, "db_")){
			$return['db'] = 1;
		}
		
		if(strpos($code, "system_setsetting") || strpos($code, "system_getsetting") ){
			$return['setting'] = 1;
		}
		if(strpos($code, "db_delete")){
			$return['db_delete'] = 1;
		}
		if(strpos($code, "content_")){
			$return['content_'] = 1;
		}
		if(strpos($code, "mysql_connect")){
			$return['mysql'] = 1;
		}
		if(strpos($code, "mysqli_connect") || strpos($code, "mysqli_real_connect") ){
			$return['mysqli'] = 1;
		}
		
		return $return;
		
	}
	
	function _plugin_get($plugin){
		
		$urls = scandir("plugins/" . $plugin);
		
		$code = "";
		foreach($urls as $urlx){
			$url = "plugins/" . $plugin . "/" .$urlx;
			if (substr($url, -4) == '.php' || substr($url, -4) == '.PHP') {
				if(file_exists($url)){
					$myfile = fopen($url, "r") ;
					$code = $code . fread($myfile,filesize($url));
					fclose($myfile);
				}
			}
		}
		$code = str_replace("<?php", "", $code);
		$code = str_replace("?>", "", $code);
		return $code;
	}
	
	function plugin_check_mate($plugin){
		$code = _plugin_get($plugin);
		
		if(strpos($code, "mysqli_connect") || strpos($code, "mysql_connect") || strpos($code, "mysqli_real_connect") || strpos($code, "new pdo")){
			return false;
		}
		else{
			return true;
		}
	}
?>
