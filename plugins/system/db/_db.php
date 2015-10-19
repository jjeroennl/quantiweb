<?php
	function db_connect(){
	  try {
        $username = DB_USER;
        $password = DB_PASSWORD;
        $hostname = DB_HOST;
        $database = DB_NAME;

        $con = new PDO('mysql:host=' . $hostname . ';dbname=' . $database, $username, $password);

        return $con;
      }
      catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
      }
	}
	function db_testconnection(){
		try {
				$username = DB_USER;
				$password = DB_PASSWORD;
				$hostname = DB_HOST;
				$database = DB_NAME;

				$con = new PDO('mysql:host=' . $hostname . ';dbname=' . $database, $username, $password);

				return $con;
			}
			catch (PDOException $e) {
				return "FAILED";
			}
	}
	function db_select($what, $from, $where = array("0" => "0"), $limit = 0, $sort = array("ROW" => "DESC/ASC"), $custom = ""){
		$con = db_connect();
		$db1 = 1;
		$values = array();
		foreach($where as $db_key => $db_where){
			if($db1 == 1){
				$wherequery = $db_key . " = :" . $db_key . "";
				$values[":" . $db_key] = $db_where;
				$db1 = 0;
			}
			else{
				$values[":" . $db_key] = $db_where;
				$wherequery = $wherequery . " AND " . $db_key . " = :" . $db_key;
			}
		}
		$query = "SELECT " . $what . " FROM " . $from . " WHERE " . $wherequery . $custom;

		if($limit != 0){
			$query = $query . " LIMIT " . $_limit;
		}

		$statement = $con->prepare($query) or die(print_r($statement->errorInfo(), true));
		$statement->execute($values) or die(print_r($statement->errorInfo(), true));
		return $statement;
	}

	function db_grab($query){

		return $query->fetch();


	}

	function db_escape($string){
		//this function will be removed! dont use!
		return $string;
	}

	function db_insert($from, $values){
		$con = db_connect();
		$_table = $from;
		$db_attributes = "";
		$db_values = "";
		$executevalues = array();

		foreach($values as $key => $value){
			$_attribute = $key;
			$_value = $value;
			$db_attributes = $db_attributes . $_attribute . ',';
			$db_values = $db_values . ":" . $_attribute . ",";
			$executevalues[":" . $_attribute] = $value;
		}
		$db_attributes = substr($db_attributes, 0, -1);
		$db_values = substr($db_values, 0, -1);
		$db_values = str_replace("\r", "", $db_values);
		$db_values = str_replace("\n", "", $db_values);
		$query = "INSERT INTO " . $_table . "(" . $db_attributes . ") VALUES(" . $db_values  . ")";
		$statement = $con->prepare($query) or die(print_r($statement->errorInfo(), true));
		$statement->execute($executevalues) or die(print_r($statement->errorInfo(), true));
	}

	function db_update($from, $set, $where){
		$con = db_connect();

		$executevalues = array();
		$db1 = 1;
		foreach($set as $db_key => $db_set){
			if($db1 == 1){
				$setquery = $db_key . " = :set_" . $db_key . ",";
				$executevalues[":set_" . $db_key] = $db_set;
				$db1 = 0;
			}
			else{
				$setquery = $setquery . " , " . $db_key . " = :set_" . $db_key. ",";
				$executevalues[":set_" . $db_key] = $db_set;
			}

		}
		$db1 = 1;
		foreach($where as $db_key => $db_where){
			if($db1 == 1){
				$wherequery = $db_key . " = :wh_" . $db_key;
				$executevalues[":wh_" . $db_key] = $db_where;
				$db1 = 0;
			}
			else{
				$wherequery = $wherequery . " AND " . $db_key . " = :wh_" . $db_key . "";
				$executevalues[":wh_" . $db_key] = $db_where;
			}
		}
		$setquery = substr($setquery, 0, -1);
		$query = "UPDATE " . $from . " SET " . $setquery . " WHERE " . $wherequery;
		$statement = $con->prepare($query) or die(print_r($statement->errorInfo(), true));
		$statement->execute($executevalues) or die(print_r($statement->errorInfo(), true));
	}

	function db_delete($from, $where, $y){
		if($y == 1){
			$con = db_connect();

			$db1 = 1;

			$executevalues = array();
			foreach($where as $db_key => $db_where){
				if($db1 == 1){
					$wherequery = $db_key . " = :" . $db_key;
					$executevalues[":" . $db_key] = $db_where;
					$db1 = 0;
				}
				else{
					$wherequery = $wherequery . " AND " . $db_key . " = :" . $db_key;
					$executevalues[":" . $db_key] = $db_where;
				}
			}
			$query = "DELETE FROM " . $from . " WHERE " . $wherequery;
			$statement = $con->prepare($query) or die(print_r($statement->errorInfo(), true));
		$statement->execute($executevalues) or die(print_r($statement->errorInfo(), true));
		}
	}

	function db_create($table, $attributes, $primary_key){
		$con = db_connect();

		$query = "CREATE TABLE " . $table . " (";

		foreach($attributes as $name=>$type){
				$query.= $name . " " .  $type . ",";
		}

		$query.=" PRIMARY KEY (" . $primary_key . ")";
		$query.= ")";
		$statement = $con->query($query) or die(print_r($con->errorInfo(), true)); ;
	}

	function db_strip($string){
		return strip_tags($string);
	}

	function db_quote($string){
		$con = db_connect();
		return $con->quote($string);
	}

	function db_entry_exist($from, $where){
		$query = db_select("*", $from, $where);
		return db_numrows($query);
	}

	function db_custom($query){
		$con = db_connect();
		$check = strtolower($query);
		if(strpos($check, "delete") == 0){
			$statement = $con->query($query) or die(print_r($con->errorInfo(), true));
			$con = null;
			return $statement;
		}
		else{
			return null;
		}

	}

	function db_numrows($query){
		return $query->rowCount();
	}

	function db_table_exist($table){
		if(defined('DB_USER')){
			$con = db_connect();
			$textquery = "SELECT COUNT(*) as counted FROM information_schema.tables WHERE table_schema = 'quantiweb' AND table_name = 'nieuws_comments' ";

			$query = db_select("count(*) as counted", "information_schema.tables", array(
				"table_schema" => DB_NAME,
				"table_name" => $table
			));

			while($row = db_grab($query)){
				return $row['counted'];
			}
		}
		else{
			return 0;
		}

	}
?>
