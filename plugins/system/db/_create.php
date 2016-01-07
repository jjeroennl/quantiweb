<?php
	class create extends db{
		public $rows;
		private $primarykey;

		function __construct($tablename, $primarykey){
			parent::__construct($tablename);
			$this->primarykey = $primarykey;
			$this->rows = array();
		}

		function makeSQL($vars = null){
			$vars = $this->getVars();

			$rows = "";
			foreach($this->rows as $row){
				$rows .= $row['name'] . " " . strtoupper($row['type']) . " " . strtoupper($row['options']) . ",";
			}

			$sql = "CREATE TABLE " . $vars['table'] . " (
				$rows
				PRIMARY KEY ($this->primarykey)
			)";

			$this->sql = $sql;
			return $this->sql;
		}

		function addRow($name, $type, $options = null){
			$this->rows[] = array(
				"name" => $name,
				"type" => $type,
				"options" => $options
			);
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
			return "<pre>" . $this->sql . "</pre>";
		}
	}
 ?>
