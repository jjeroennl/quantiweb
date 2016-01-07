<?php
	class Delete extends db{
		private $results;

		function __construct($tablename){
			parent::__construct($tablename);
		}

		function getVars(){
			$vars = parent::getVars();
			return $vars;
		}

		function makeSQL($vars = null){
			$vars = $this->getVars();
			$sql = "DELETE FROM " . $vars['table'];

			$sql .= parent::makeSQL();

			$this->sql = $sql;
			return $this->sql;
		}

		function select($column){
			$select[] = $column;
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
