<?php
	class CustomQuery{
		private $results;

		function __construct($query){
			$con = $this->connect();
			if(strpos(strtolower($query), "delete") === false){
				$this->results = $con->query($query);
			}
			else{
				echo $this->debug($query, array("You can't delete rows, tables or databases using CustomQuery. Use Delete instead."));
				die();
			}
		}

		function debug($query, $error = null){
			$errormessage = "";
			$sql = $query;

			$result = array(
				"query" => $sql
			);

			$backtrace = debug_backtrace();
			$num = count($backtrace) -1;

			$result['file'] = $backtrace[$num]['file'];
			$result['line'] = $backtrace[$num]['line'];
			$result['function'] = $backtrace[$num]['function'];

			if($error != null){
				$errormessage = '<b>Quantiweb Database error</b><br>Something is wrong with your <b>' . ucfirst(get_class($this)) .'</b> statement ( ' . $error[count($error) -1] . ' ):';

				$result['error'] = $error[count($error) - 1];
			}

			return "<br>" . $errormessage . "<code><pre>" . print_r($result, true) . "</pre></code>";
		}

		function fetch(){
			if($this->results){
				return $this->results->fetchAll(PDO::FETCH_ASSOC);
			}
			else{
				return null;
			}
		}

		private function connect(){
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
	}
?>
