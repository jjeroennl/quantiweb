<?php
	function admin_addpage($name, $function, $icon = "cog"){
		if(!dbEntryExist("admin", array(
			"name" => $name,
			"function" => $function
		))){
			$insert = new Insert("admin");
			$insert->insert("name", $name);
			$insert->insert("function", $function);
			$insert->insert("icon", $icon);
			$insert->insert("type", "page");
			$insert->execute();
		}
	}

	function admin_removepage($name){

	}

	function admin_main(){
		$send = 1;
		$pagename = "Dash";
		include("themes/admin/header.php");

		$mvc = new Mvc("main.qhtml", __FILE__);
		$registered_users = system_getuserstats("registered");
		if($registered_users == 1){
			$users = " user ";
			$isare = "is";
		}
		else{
			$users = " users ";
			$isare = "are";
		}

		$mvc->set_var('$isareusers', $isare);
		$mvc->set_var('$users', $users);
		$mvc->set_var('$numberofusers', $registered_users);
		$mvc->set_var('$title', "Home");
		$mvc->set_var('$username', ucfirst(system_getuserinfo(system_currentuser(), "username")));
		$mvc->set_var('$datum', date("Y-m-d"));

		$content = content_query(00, "loop");
		$num = $content->numrows();
		if($num == 1){
			$posts = " post ";
			$isare = "is";
		}
		else{
			$posts = " posts ";
			$isare = "are";
		}
		$mvc->set_var('$isarecontent', $isare);
		$mvc->set_var('$content', $num);
		$mvc->set_var('$posts', $posts);

		$mvc->_("#showsite")->set_attribute("href", INSTALL_LOCATION);
		$mvc->_("#manageuser")->set_attribute("href", "admin.php?p=3#users");

		$mvc->get_all();

		include("themes/admin/footer.php");
	}

	function admin_nav(){
		global $pagename;
		if(basename($_SERVER['PHP_SELF']) == "newsetup.php"){
			return;
		}
		if(isset($_SESSION['login'])){
			$query = new Select("admin");
			$query->where("type", "page");
			$query->execute();

			if(!isset($_GET['p'])){
				echo "<li class=\"active\"><a href=\"admin.php\"><i class=\"fa fa-home\"></i></a></li>";
			}
			else{
				echo "<li ><a href=\"admin.php\"><i class=\"fa fa-home\"></i></a></li>";
			}

			foreach($query->fetch() as $row){
				if($row['name'] != "Plugins" && $row['name'] != "Settings" && $row['name'] != "Themes"){
					if(function_exists($row['function'])){
						if(!isset($_GET['p'])){
							$_GET['p'] = 1;
						}
						if($row['admin_id'] == $_GET['p']){
							echo "<li class=\"active\"><a href=\"admin.php?p=" . $row['admin_id'] ."\"><i class=\"fa fa-" . $row['icon']. "\"></i></a></li>";
						}
						else{
							echo "<li><a href=\"admin.php?p=" . $row['admin_id'] ."\"><i class=\"fa fa-" . $row['icon']. "\"></i></a></li>";
						}
					}
				}
			}
			if(!isset($_GET['p'])){
				$_GET['p'] = 1;
			}

			if($_GET['p'] == 3){
            	echo "<li class=\"active\"><a href=\"admin.php?p=3\"><i class=\"fa fa-cog\"></i></a></li>";
			}
			else{
				echo "<li><a href=\"admin.php?p=3\"><i class=\"fa fa-cog\"></i></a></li>";
			}

		}
	}

	function admin_loadpage($_id){
		$query = new Select("admin");
		$query->where("admin_id", $_id);
		$query->execute();

		if($query->numrows() == 1){
			foreach($query->fetch() as $row){
			$function = $row['function'];
				if(function_exists($function)){
					$send = 1;
					$pagename = $row['name'];

					include("themes/admin/header.php");
					include 'adminpage.php';
					include("themes/admin/footer.php");

				}
				else{
					echo "Whoops!";
				}
			}
		}
		else{
			echo "404 Page not found.";
		}

	}

	function admin_getpageid($name){

	}


?>
