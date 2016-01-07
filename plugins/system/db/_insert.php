<?php
	class insert extends db{
		public $insertments;
		public $values;
		private $results;

		function __construct($tablename){
			parent::__construct($tablename);
		}

		function getVars(){
			$vars = parent::getVars();

			$columns = "";
			$values = "";
			$queryvars = array();

			foreach($this->insertments as $insertment=>$value){
				$columns .= $insertment . ", ";
				$values .= ":".$insertment . ", ";
				$queryvars[":".$insertment] = $this->values[":".$insertment];
			}

			$columns = substr($columns, 0, -2);
			$values = substr($values, 0, -2);

			$vars['queryvars'] = $vars['queryvars'] + $queryvars;
			$vars['columns'] = $columns;
			$vars['values'] = $values;
			return $vars;
		}

		function makeSQL($vars = null){
			$vars = $this->getVars();
			$sql = "INSERT INTO " . $vars['table'] . " (" . $vars['columns'] .") VALUES(" . $vars['values'] . ")";

			$sql .= parent::makeSQL();

			$this->sql = $sql;
			return $this->sql;
		}

		function insert($columnname, $value){
			$this->insertments[$columnname] = ":$columnname";
			$this->values[":$columnname"] = $value;
			return $this;
		}

		function where($array){
			echo '<b>Quantiweb Database error</b><br>This function is not usable for an insert statement<br>';
			return $this;
		}

		function execute(){
			$this->makeSQL();

			$vars = $this->getVars();

			$con = $this->connect();
			$statement = $con->prepare($this->sql);
			$statement->execute($vars['queryvars']) or die($this->debug($statement->errorInfo()));

			$this->results = $statement;
			return $this;
		}


		function fetch(){
			// $this->execute();
			return $this->results->fetchAll(PDO::FETCH_ASSOC);
		}

		function __toString(){
			$this->makeSQL();
			return $this->sql;
		}
	}
 ?>
