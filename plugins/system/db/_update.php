<?php
	class update extends db{
		public $updates;
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

			foreach($this->updates as $update=>$value){
				$columns .= $update . ", ";
				$values .= ":".$update . ", ";
				$queryvars[":".$update] = $this->values[":".$update];
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
			$sql = "UPDATE " . $vars['table'] . " SET " . $vars['columns'] . "=" . $vars['values'];

			$sql .= parent::makeSQL();

			$this->sql = $sql;
			return $this->sql;
		}

		function update($columnname, $value){
			$this->updates[$columnname] = ":$columnname";
			$this->values[":$columnname"] = $value;
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
