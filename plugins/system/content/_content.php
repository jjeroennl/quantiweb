<?php
	function content_create_type($type, $aditional = null, $menu = 0, $way = 0){
		if(dbEntryExist("content_types", array("type" => $type)) == 0){
			$insert = new Insert("content_types");
			$insert->insert("type", $type);
			$insert->insert("type", $menu);
			$insert->insert("type", $way);
			if($aditional != null){
				$insert->insert("aditional", $aditional);
			}
			$insert->execute();
		}
	}


	function content_get_type($name){
		$query = new Select("content_types");
		$query->where("type", $name);
		$query->execute();

		if($query->numrows() == 0){
			return "There is no content type with this name";
		}

		foreach($query->fetch() as $row){
			return $row['type_id'];
		}
	}

	function content_get_type_name($id){
		$query = new Select("content_types");
		$query->where("type", $name);
		$query->execute();

		if($query->numrows() == 0){
			return "There is no content type with this id";
		}

		foreach($query->fetch() as $row){
			return $row['type'];
		}
	}

	function content_get_way($type){
		$query = new Select("content_type");
		if(is_numeric($type)){
			$query->where("type_id", $type);
		}
		else{
			$query->where("type", $type);

		}
		$query->execute();
		foreach($query->fetch() as $row){
			return $row['way'];
		}
	}

	function content_search($query){
		$_query = "%" . $query . "%";
		$sqlquery = new Select("content");
		$sqlquery->where("content", "LIKE $_query");
		$sqlquery->execute();
		return $sqlquery;
	}

	function content_query($type = 00, $way = "loop", $id = 0, $status = 1){
		$values = array(1 => 1);
		$order = array();
		$query = new Select("content");

		if(content_isStatic($way) == 1){
			$query->where('content_id', $id);
		}
		else{
			$query->orderBy('content_id DESC');
		}

		if(content_isTypeSet($type)){
			$query->where('type', $type);
		}

		if(content_hasStatus($status) == 1){
			$query->where('status', $status);
		}

		$query->execute();

		return $query;
	}

	function content_isTypeSet($type){
		if($type == 00){
			return 0;
		}
		else{
			return 1;
		}
	}

	function content_isStatic($way){
		if($way == "static"){
			return 1;
		}
		else{
			return 0;
		}
	}

	function content_hasStatus($status){
		return $status;
	}

	function content_get_title($id){
		$query = new Select("content");
		$query->where("content_id", $id);
		$query->execute();

		foreach($query->fetch() as $row){
			return $row['title'];
		}
	}

	function content_get_page_title(){
		if(isset($_GET['p'])){
			$page = $_GET['p'];
			if(is_numeric($page)){
				$get_pages = new Select( "content");
				$get_pages->select("title");
				$get_pages->select("content_id");
				$get_pages->where("content_id", $page);
				$get_pages->execute();

				foreach($get_pages as $row){
					return $row['title'];
				}
			}
			else{
				return ucfirst($_GET['p']);
			}
		}
		else{
			return "Home";
		}

	}

	function content_get_content(){
		if(isset($_GET['p'])){
			$id = $_GET['p'];
			if(is_numeric($id)){
				$query = content_query(00, "static", $id);
				foreach($query->fetch() as $row){
					echo $row['content'];
					content_addview($id);
                    $additionalfunction = content_getadditionalfunction($row['type']);
                    if(function_exists($additionalfunction)){
                        $additionalfunction();
                    }
				}
			}
			else{
				$query = content_query(content_get_type($id), "loop", $id);

				foreach($query->fetch() as $row){
					echo "<h2>" . $row['title'] . "</h2>";
					echo substr($row['content'], 0, 500) . "...";
					echo "</p><br>" . content_readmore($row['content_id']);

				}
			}
		}
		elseif(isset($_GET['s'])){
			$query = strip_tags($_GET['s']);
			content_makeSearchPage($query);
		}
		else{
			content_makeIndexPage();
		}

	}

	function content_makeSearchPage($query){
		$getresult = str_replace("%20", " " , $query);
		$result = content_search($getresult);
		foreach($result->fetch() as $row){
			echo "<h2>" . $row['title'] . "</h2>";
			echo substr($row['content'], 0, 500) . "...";
			echo "<br>" . content_readmore($row['content_id']);
		}
	}

	function content_makeIndexPage(){
		$function = system_getsetting("index");
		if(function_exists($function)){
			$function();
		}
		else{
			echo "You don't have any plugins activated. <a href=\"admin.php\">Activate a plugin</a>";
		}
	}

	function content_addView($id){
		return;
	}

	function content_get_sidebar(){
		//TODO: editable!
		echo "<h2>Search</h2>";
		?>
		<form method="GET" action="index.php">
			<div class="input-group">
				<input type="text" name="s" class="form-control" placeholder="Search for...">
    			<span class="input-group-btn">
        			<button class="btn btn-default" type="submit">Go!</button>
      			</span>
    		</div><!-- /input-group -->

		</form>
		<?php
	}

    function content_getadditionalfunction($contenttype){
        $query = new Select("content_types");
		$query->where("type_id", $contenttype);
		$query->execute();

		foreach($query->fetch() as $row){
            return $row['aditional'];
        }

    }

	function content_add($title, $content, $author, $status, $type){
		$insert = new Insert("content");
		$insert->insert("title", $title);
		$insert->insert("content", $content);
		$insert->insert("author", $author);
		$insert->insert("status", $status);
		$insert->insert("type", $type);
		$insert->execute();
	}

	function content_modify($id, $content){
		$update = new Update("content");
		$update->update("content", $content);
		$update->where("content_id", $id);
		$update->execute();
	}

	function content_readmore($id){
		return '<a href="?p=' . $id . '">Read more...</a>';
	}

	function content_modify_info($id, $info, $value){
		$update = new Update("content");
		$update->update($info,$value);
		$update->where("content_id", $id);
		$update->execute();
	}

	function content_delete($id){
		$delete = new Delete("content");
		$delete->where("content_id", $id);
		$delete->execute();
	}

	function content_nav(){
		$query = new Select("content_types");
		$query->where("menu", 1);
		$query->execute();

		//TODO: Add join instead of 2 seperate querys

		$nav = "";
		$nav = $nav . '<li><a href="index.php">Home</a></li>';

		foreach($query->fetch() as $row){
			if($row['way'] == 0){
				//static page per content
				$get_pages = new Select( "content");
				$get_pages->select("title, content_id");
				$get_pages->where("type", $row['type_id']);

				foreach($get_pages->fetch() as $row2){
					$nav = $nav . "<li><a>" . $row2['title'] . "</a></li>";
				}
			}
			else{
				//loop on 1 page (blog)
				$nav = $nav . '<li><a href="index.php?p=' . $row['type'] . '">' . ucfirst($row['type']) . '</a></li>';

			}
		}
		return $nav;
	}
?>
