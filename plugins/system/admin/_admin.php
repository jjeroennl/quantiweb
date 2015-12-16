<?php
	function admin_addpage($name, $function, $icon = "cog"){
		if(!db_entry_exist("admin", array(
			"name" => $name,
			"function" => $function
		))){
			db_insert("admin", array(
				"name" => $name,
				"function" => $function,
				"icon" => $icon
			));
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
		$num = db_numrows($content);
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
			$query = db_select("*", "admin");

			if(!isset($_GET['p'])){
				echo "<li class=\"active\"><a href=\"admin.php\"><i class=\"fa fa-home\"></i></a></li>";
			}
			else{
				echo "<li ><a href=\"admin.php\"><i class=\"fa fa-home\"></i></a></li>";
			}

			while($row = db_grab($query)){
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
			if($_GET['p'] == 4){
				echo "<li class=\"active\"><a href=\"admin.php?p=4\"><i class=\"fa fa-paint-brush\"></i></a></li>";
			}
			else{
				echo "<li><a href=\"admin.php?p=4\"><i class=\"fa fa-paint-brush\"></i></a></li>";
			}

			if($_GET['p'] == 2){
            	echo "<li class=\"active\"><a href=\"admin.php?p=2\"><i class=\"fa fa-puzzle-piece\"></i></a></li>";
			}
			else{
				echo "<li><a href=\"admin.php?p=2\"><i class=\"fa fa-puzzle-piece\"></i></a></li>";
			}

			if($_GET['p'] == 3){
            	echo "<li class=\"active\"><a href=\"admin.php?p=3\"><i class=\"fa fa-cog\"></i></a></li>";
			}
			else{
				echo "<li><a href=\"admin.php?p=3\"><i class=\"fa fa-cog\"></i></a></li>";
			}

		}
	}

	function admin_loadpage($id){

		$_id = db_escape($id);
		$query = db_select("*", "admin", array(
			"admin_id" => $_id
		));

		if(db_numrows($query) == 1){
			while($row = db_grab($query)){
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
