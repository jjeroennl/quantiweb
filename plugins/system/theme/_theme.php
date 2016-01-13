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
					$mvc->_("#enable" . $themedata['name'])->set_attribute("href", "admin.php?p=" . $_GET['p'] . "&settingspage=" . $_GET['settingspage'] . "&activate=" . $themedata['name']);
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

	function theme_editor(){
		$mvc = new Mvc("themeedit.qhtml", __FILE__);
		// $mvc->add_controller("theme_getsteps");
		// if(isset($_COOKIE['step1_name'])){
		// 	$mvc->_("#part1")->hide();
		// }
		$mvc->_("#panel-area")->hide();
		echo "Comming soon!";
		$mvc->get_all();
	}

	function theme_getsteps($data){
		if(!isset($_COOKIE['step1_name'])){
			theme_editor_step1();
		}
	}

	function theme_editor_step1(){
		ini_set('display_errors', '1');
		if(!isset($_FILES['zipfile'])){
			return;
		}
		$target_dir =  __DIR__ . "/tmp/" . $_POST['themename'];
		$target_file = __DIR__ . "/tmp/" . $_POST['themename'] . ".zip";
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			if($imageFileType != "zip") {
				$uploadOk = 0;
			}
			if ($uploadOk == 1) {
				$moved = move_uploaded_file($_FILES["zipfile"]["tmp_name"], $target_file);
				if( $moved ) {
					$zip = new ZipArchive;
					$zip->open($target_file);
					$zip->extractTo($target_dir);
					$zip->close();
					setcookie("step1_name", $_POST['themename'], time()+86400);
					setcookie("step1_target", $target_dir, time()+86400);
					header("location:" . settings_get_url());
				} else {
  					echo "<div class=\"panel huge error\">Not uploaded because of error #".$_FILES["zipfile"]["error"] . "</div>";
				}
			}
			else{
				die($imageFileType . "is niet geldig");
			}
		}
	}

	function theme_activate($data){
		if(isset($data['activate'])){
			system_setsetting("theme", $data['activate']);
			header("location: admin.php?p=" . $data['p'] . "&settingspage=" . $_GET['settingspage']);
		}
	}
?>
