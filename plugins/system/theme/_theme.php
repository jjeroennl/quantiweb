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
		$mvc = new Mvc("theme.qhtml", __FILE__);
		$files = scandir("themes");
		$files = array_diff($files, array('.', '..', 'admin', 'admin.old'));
		foreach($files as $file){
		    $url = 'themes/' . $file . '/info.txt';
		    if($fh = fopen($url,"r")){
			   while (!feof($fh)){
		            $F1[] = fgets($fh,9999);
			   }
		        fclose($fh);

				$themedata = array();
		        $themedata['name'] =  $file;
		        $themedata['description'] =  str_replace("DESC: ", "", $F1['0']);
		        $themedata['author'] =  str_replace("AUTHOR: ", "", $F1['1']);
		        $themedata['website'] =  str_replace("WEBSITE: ", "", $F1['2']);
		        $themedata['version'] = str_replace("VERSION: ", "", $F1['3']);

				$mvc->_("#panel-area")->append(new Panel("small", $themedata, "themes"));
				if(system_getsetting("theme") != $themedata['name']){
					$mvc->_("#enable" . $themedata['name'])->set_attribute("href", "admin.php?p=" . $_GET['p'] . "&activate=" . $themedata['name']);
				}
		        unset($description);
		        unset($author);
		        unset($website);
		        unset($version);
		        unset($F1);
		    }
		    else{

		    }
		}
		$mvc->add_controller("theme_activate");
		$mvc->get_all();
	}

	function theme_activate($data){
		if(isset($data['activate'])){
			system_setsetting("theme", $data['activate']);
			header("location: admin.php?p=" . $data['p']);
		}
	}
?>
