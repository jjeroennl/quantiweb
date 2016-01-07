<?php
	class select extends db{
		private $select;
		private $results;

		function __construct($tablename){
			parent::__construct($tablename);
		}

		function getVars(){
			$vars = parent::getVars();
			$select = "";
			if($this->select != ""){
				foreach($this->select as $key=>$selectstatement){
					$selectstatement .= $select;
				}
			}
			else{
				$selectstatement = "*";
			}

			$vars['select'] = $selectstatement;
			return $vars;
		}

		function makeSQL($vars = null){
			$vars = $this->getVars();
			$sql = "SELECT " . $vars['select'] . " FROM " . $vars['table'];

			$sql .= parent::makeSQL();

			$this->sql = $sql;
			return $this->sql;
		}

		function select($column){
			$this->select[] = $column;
		}


		function numrows(){
			if($this->results){
				return $this->results->rowCount();
			}
			else{
				return 0;
			}
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
			if($this->results){
				return $this->results->fetchAll(PDO::FETCH_ASSOC);
			}
			else{
				return "null";
			}
		}

		function __toString(){
			$this->makeSQL();
			return $this->sql;
		}
	}
 ?>
