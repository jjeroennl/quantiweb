<?php
	function db_select($what, $from, $where = array("0" => "0"), $limit = 0, $sort = array("ROW" => "DESC/ASC"), $custom = ""){
		$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$db_what = db_escape($what);
		$db_from = db_escape($from);
		$_limit = db_escape($limit);
		$_sort = db_escape_array($sort, 1);

		$db1 = 1;
		foreach($where as $db_key => $db_where){
			$_db_key = mysqli_real_escape_string($con, $db_key);
			$_db_where = mysqli_real_escape_string($con, $db_where);
			if($db1 == 1){
				$wherequery = $db_key . " = '" . $_db_where . "'";
				$db1 = 0;
			}
			else{
				$wherequery = $wherequery . " AND " . $db_key . " = '" . $_db_where . "'";
			}
		}
		$query = "SELECT " . $what . " FROM " . $from . " WHERE " . $wherequery . $custom;
		if(isset($_sort["ROW"])){

		}
		else{
			foreach($_sort as $db_attr=>$db_sort){
				$query = $query . " ORDER BY " . $db_attr . " " . $db_sort;
			}
		}
		if($_limit != 0){
			$query = $query . " LIMIT " . $_limit;
		}
		$_query = mysqli_query($con, $query) or die(mysqli_error($con));
		return $_query;
	}

	function db_grab($query){

		return mysqli_fetch_array($query);


	}

	function db_insert($from, $values){
		$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$_table = mysqli_real_escape_string($con, $from);
		$db_attributes = "";
		$db_values = "";

		foreach($values as $key => $value){
			$_attribute = mysqli_real_escape_string($con, $key);
			$_value = mysqli_real_escape_string($con, $value);
			$db_attributes = $db_attributes . $_attribute . ',';
			$db_values = $db_values . "'" . $_value . "',";
		}
		$db_attributes = substr($db_attributes, 0, -1);
		$db_values = substr($db_values, 0, -1);
		$db_values = str_replace("\r", "", $db_values);
		$db_values = str_replace("\n", "", $db_values);
		$query = "INSERT INTO " . $_table . "(" . $db_attributes . ") VALUES(" . $db_values  . ")";
		mysqli_query($con, $query) or die(mysqli_error($con));
	}

	function db_update($from, $set, $where){
		$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$_from = mysqli_real_escape_string($con, $from);

		$db1 = 1;
		foreach($set as $db_key => $db_set){
			$_db_key = mysqli_real_escape_string($con, $db_key);
			$_db_set = mysqli_real_escape_string($con, $db_set);
			if($db1 == 1){
				$setquery = $db_key . " = '" . $_db_set . "',";
				$db1 = 0;
			}
			else{
				$setquery = $setquery . " , " . $db_key . " = '" . $_db_set. "',";
			}

		}
		$db1 = 1;
		foreach($where as $db_key => $db_where){
			$_db_key = mysqli_real_escape_string($con, $db_key);
			$_db_where = mysqli_real_escape_string($con, $db_where);
			if($db1 == 1){
				$wherequery = $db_key . " = '" . $_db_where . "'";
				$db1 = 0;
			}
			else{
				$wherequery = $wherequery . " AND " . $db_key . " = '" . $_db_where . "'";
			}
		}
		$setquery = substr($setquery, 0, -1);
		$query = "UPDATE " . $_from . " SET " . $setquery . " WHERE " . $wherequery;
		mysqli_query($con, $query) or die(mysqli_error($con));
	}

	function db_delete($from, $where, $y){
		if($y == 1){
			$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			$_from = mysqli_real_escape_string($con, $from);

			$db1 = 1;
			foreach($where as $db_key => $db_where){
				$_db_key = mysqli_real_escape_string($con, $db_key);
				$_db_where = mysqli_real_escape_string($con, $db_where);
				if($db1 == 1){
					$wherequery = $db_key . " = '" . $_db_where . "'";
					$db1 = 0;
				}
				else{
					$wherequery = $wherequery . " AND " . $db_key . " = '" . $_db_where . "'";
				}
			}
			$query = "DELETE FROM " . $_from . " WHERE " . $wherequery;
			mysqli_query($con, $query) or die(mysqli_error($con));
		}
	}

	function db_create($table, $attributes, $primary_key){
		$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$_table = mysqli_real_escape_string($con, $table);
		$_primary_key = mysqli_real_escape_string($con, $primary_key);
		$db_attributes = "";
		$db1 = 1;

		if(db_table_exist($_table) == 1){
			return null;
		}
		foreach($attributes as $attributes => $attr_key){
			$_attributes = mysqli_real_escape_string($con, $attributes);
			$_attr_key = mysqli_real_escape_string($con, $attr_key);
			if($db1 == 1){
				$db_attributes = $_attributes . " " . strtoupper($_attr_key) . ",";
				$db1 = 0;
			}
			else{
				$db_attributes = $db_attributes .  $_attributes . " " . strtoupper($_attr_key) . ",";
			}
		}
		$_primary_key = "PRIMARY KEY " . $_primary_key . "(" . $_primary_key . ")";
		$query = "CREATE TABLE " . $_table . "(" . $db_attributes . $_primary_key . ");";
		mysqli_query($con, $query) or die(mysqli_error($con));
	}

	function db_escape($string){
		$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		return mysqli_real_escape_string($con, $string);
	}

	function db_escape_array($array, $key = 0){
		$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$escapedarray = array();
		if($key == 0){
			foreach($array as $value){
				array_push($escapedarray, mysqli_real_escape_string($con, $value));
			}
		}
		else{
			foreach($array as $key=>$value){
				$escapedarray[mysqli_real_escape_string($con, $key)] =  mysqli_real_escape_string($con, $value);
			}
		}
		return $escapedarray;
	}

	function db_entry_exist($from, $where){
		$query = db_select("*", $from, $where);
		return db_numrows($query);
	}

	function db_custom($query){
		$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$check = strtolower($query);
		if(strpos($check, "delete") == 0){
			$query = mysqli_query($con, $query) or die(mysqli_error($con));
			return $query;
		}
		else{
			return null;
		}

	}

	function db_check($stat = 0){
		$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if($con){
			if($stat == 1){
				$query = db_custom("IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'system_settings' BEGIN PRINT 'Table Exists' END");
				if(db_numrows($query) == 1){
					return 1;
				}
				else{
					return 0;
				}
			}
			else{
				return 1;
			}
		}
		else{
			return 0;
		}
	}

	function db_numrows($query){
		return mysqli_num_rows($query);
	}

	function db_table_exist($table){
		$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$textquery = "SELECT COUNT(*) as counted FROM information_schema.tables WHERE table_schema = 'quantiweb' AND table_name = 'nieuws_comments' ";

		$query = db_select("count(*) as counted", "information_schema.tables", array(
			"table_schema" => DB_NAME,
			"table_name" => $table
		));

		while($row = db_grab($query)){
			return $row['counted'];
		}

	}
?>
