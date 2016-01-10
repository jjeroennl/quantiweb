<?php
	class db{
		protected $tablenames = array();
		protected $where;
		protected $query;
		protected $sql;
		protected $limit = -1;
		protected $offset = -1;
		protected $groupby;
		protected $orderby;

		function __construct($tablename){
			$this->tablenames[] = $tablename;
			$this->sql = "";
			return $this;
		}

		protected function connect(){
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

		function where( $where ){
			$results = func_get_args();
			if(is_array($results[0])){
				$this->where[] = $results[0];
			}
			else{
				$this->where[] = array($results[0] => $results[1]);
			}
			return $this;
		}

		function join($table, $on){

		}

		function orderby($order){
			$this->orderby[] = $order;
		}

		function groupby($group){
			$this->groupby[] = $group;
		}

		function limit($limit){
			$this->limit = $limit;
		}

		function offset($offset){
			$this->offset = $offset;
		}

		function getVars(){
			$queryvars = array();

			$table = "";
			foreach($this->tablenames as $key=>$tablename){
				if($this->tablenames[0] == $tablename){
					$table .= $tablename;
				}
				else{
					$table .= "," . $tablename;
				}
			}

			$wherestatement = "";
			if($this->where != ""){
				$count = 0;
				foreach($this->where as $wherearray){
					foreach($wherearray as $where=>$is){
						if(strtolower(substr($is,0,3)) == 'not'){
							$diff = " <> ";
							$is = substr($is,4);
						}
						elseif(strtolower(substr($is,0,4)) == 'like'){
							$diff =  " LIKE ";
							$is = substr($is,5);
						}
						else{
							$diff =  " = ";
						}

						if($wherestatement == ""){
							$wherestatement .= $where . $diff . ":where" . $count;
						}
						else{
							if(strtolower(substr($is,0,2)) == 'or'){
								$wherestatement .= " OR " . $where . $diff . ":where" . $count;
								$is = substr($is,3);
							}
							else{
								$wherestatement .= " AND " . $where . $diff . ":where" . $count;
							}
						}
						$queryvars[":where" . $count] = $is;
						$count++;
					}
				}
			}

			$limit = $this->limit;
			$offset = $this->offset;

			$groupby = "";
			if($this->groupby != ""){
				foreach($this->groupby as $key=>$group){
					if($this->groupby[0] == $group){
						$groupby .= $group;
					}
					else{
						$groupby .= ", " . $group;
					}
				}
			}

			$orderby = "";
			if($this->orderby != ""){
				foreach($this->orderby as $order){
					if($orderby == ""){
						$orderby .= $order;
					}
					else{
						$orderby .= ", " . $order;
					}
				}
			}

			return array(
				'table' => $table,
				'where' => $wherestatement,
				'orderby' => $orderby,
				'groupby' => $groupby,
				'limit' => $limit,
				'offset' => $offset,
				'queryvars' => $queryvars
			);
		}

		function makeSQL($vars = null){
			$vars = $this->getVars();
			$sql = "";

			if($vars['where'] != "" && $vars != "nowhere"){
				$sql .= " WHERE " . $vars['where'];
			}

			if($vars['groupby'] != ""){
				$sql .= " GROUP BY " . $vars['groupby'];
			}

			if($vars['orderby'] != ""){
				$sql .= " ORDER BY " . $vars['orderby'];
			}

			$this->sql = $sql;

			return $sql;
		}

		function __toString(){
			$this->getVars();
			return $this->sql;
		}

		function debug($error = null){
			$this->makeSQL();
			$errormessage = "";
			$sql = $this->sql;

			$vars = print_r($this->getVars(), true);

			$result = array(
				"vars" => $vars,
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
	}

	function dbTestConnection(){
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

	function dbTableExist($table){
		if(defined('DB_USER')){
			$con = dbTestConnection();
			$textquery = "SELECT COUNT(*) as counted FROM information_schema.tables WHERE table_schema = 'quantiweb' AND table_name = 'nieuws_comments' ";

			$query = new Select("information_schema.tables");
			$query->select("count(*) as counted");
			$query->where(
				array(
					"table_schema" => DB_NAME,
					"table_name" => $table
				)
			);

			$query->execute();

			foreach($query->fetch() as $row){
				return $row['counted'];
			}
		}
		else{
			return 0;
		}
	}

	function dbEntryExist($from, $where){
		$query = new Select($from);
		$query->where($where);
		$query->execute();
		return $query->numrows();
	}
?>
