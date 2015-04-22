<?php
	function admin_addpage($name, $function){
		$_name = db_escape($name);
		$_function = db_escape($function);

		if(!db_entry_exist("admin", array(
			"name" => $_name,
			"function" => $_function
		))){
			db_insert("admin", array(
				"name" => $_name,
				"function" => $_function
			));
		}
	}

	function admin_removepage($name){

	}

	function admin_main(){
		$send = 1;
		$pagename = "Dash";
		include 'main.php';
	}

	function admin_nav(){
		if(isset($_SESSION['login'])){
			$query = db_select("*", "admin");
			while($row = db_grab($query)){
				if($row['name'] != "Plugins" && $row['name'] != "Settings"){
					if(function_exists($row['function'])){
						echo "<li><a href=\"admin.php?p=" . $row['admin_id'] ."\">" . ucfirst($row['name']) . "</a></li>";
					}
				}
			}
            echo "<li><a href=\"admin.php?p=4\">Themes</a></li>";
            echo "<li><a href=\"admin.php?p=2\">Plugins</a></li>";
            echo "<li><a href=\"admin.php?p=3\">Settings</a></li>";

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
					include 'adminpage.php';
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
