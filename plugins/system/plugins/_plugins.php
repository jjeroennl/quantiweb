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
	
function plugin_check($plugin){
		$code = _plugin_get($plugin);
		$totalscore = 10;
		$containsbad = array();

		$bad_words = array(
			"db" => "1",
			"system_" => "0",
			"db_delete" => "6",
			"content_" => "1",
			"exec" => "7",
			"passthru" => "7",
			"shell_exec" => "7",
			"popen" => "5",
			"assert" => "5",
			"eval" => "5",
			"fopen" => "5",
			"tmpfile" => "5",
			"move_uploaded_file" => "5"
		);

		foreach ($bad_words as $badword=>$score) {
			if(strpos($code, $badword) !== FALSE) {
				array_push($containsbad, $badword);
				$totalscore -= $score;
			}
		}
		
		if($totalscore <= 0){
			$totalscore = 0;
		}
		
		if(func_num_args() == 2){
			return $totalscore;
		}
		else{
			return $containsbad;
		}

	}
	
	function plugin_explain($badword){
		$explain = array(
			"db" => "Read and write from/to your database system",
			"system_" => "Use system functions",
			"db_delete" => "Delete entries in your database system",
			"content_" => "Read, write or delete content",
			"exec" => "Execute commands on your server",
			"passthru" => "Execute commands on your server without verification",
			"popen" => "Read and write files",
			"assert" => "Posibly stop quantiweb from working",
			"eval" => "Execute commands on your website",
			"fopen" => "Read and write files",
			"tmpfile" => "Read and write temporary files",
			"move_uploaded_file" => "Move temporary files",
		);
		return $explain[$badword];
	}
	
	function _plugin_get($plugin){
		$folderstoscan = plugin_scanfolder($plugin);
		$code = "";
		foreach($folderstoscan as $folder){
			$urls = scandir($folder);

			foreach($urls as $urlx){
				$url = $folder . '/' . $urlx;
				if (substr($url, -4) == '.php' || substr($url, -4) == '.PHP') {
				
					if(file_exists($url)){
						$myfile = fopen($url, "r") ;
						$code = $code . fread($myfile,filesize($url));
						fclose($myfile);
					}
				}
			}
		}
		$code = str_replace("<?php", "", $code);
		$code = str_replace("?>", "", $code);
		return $code;
	}
	
	function plugin_scanfolder($plugin){
		$folders = array("plugins/" . $plugin);
		
		$badloop = 1;
		
		while($badloop != 0){
			if(isset($folders[$badloop - 1])){
				$scan = scandir($folders[$badloop - 1 ]);
				foreach($scan as $results){
					if($results != '.' && $results != '..'){
						if(is_dir($folders[$badloop - 1 ].'/'.$results)){
							array_push($folders, $folders[$badloop - 1 ] . '/' .$results);		
						}
					}
				}
				$badloop++;
			}
			else{
				$badloop = 0;
			}

		}
		return $folders;
	}
	
	function plugin_recursivefolder($folders){
		
	}
	
	function plugin_checkmate($plugin){
		$code = _plugin_get($plugin);
		
		if(strpos($code, "mysqli_connect") || strpos($code, "mysql_connect") || strpos($code, "mysqli_real_connect") || strpos($code, "new pdo")){
			return false;
		}
		else{
			return true;
		}
	}
?>
