<?php
	function nieuws_install(){
		content_create_type("Nieuws", "nieuws_getcomments");
		system_setindex("nieuws_index");
		admin_addpage("Nieuws", "nieuws_admin");

		if(db_table_exist("nieuws_comments") == 0){
			db_create("nieuws_comments", array(
				"comment" => "TEXT",
				"name" => "TEXT",
				"email" => "TEXT",
				"comment_id" => "INT NOT NULL AUTO_INCREMENT "
			), "comment_id");
		}

		if(db_table_exist("nieuws_categories") == 0){
			db_create("nieuws_categories", array(
				"category" => "TEXT",
				"category_id" => "INT NOT NULL AUTO_INCREMENT "
			), "category_id");
		}

	}

	function nieuws_index(){
		$content = content_query(content_get_type("Nieuws"), "loop");

		while($row = db_grab($content)){
			echo "<h1>" . $row['title'] . "</h1>";
			if(strlen($row['content']) >= 500){
				echo substr($row['content'], 0, 500);
			}
			echo "<br>" . content_readmore($row['content_id']);
		}
	}

	function nieuws_getcomments(){
        echo "<h2> Comments</h2>";
		$query = db_select("*", "nieuws_comments");
        if(db_numrows($query) < 1){
            echo "There are no comments";
        }
		while($row = db_grab($query)){
			echo "<b>" . $row['name'] . "</b>";
			echo "<p>" . $row['comment'] . "</p>";
		}
	}

	function nieuws_createcomment($comment, $name, $email){
		db_insert("nieuws_comments", array(
			"comment" => $comment,
			"name" => $name,
			"email" => $email
		));
	}

	function nieuws_admin(){
		$send = 1;
		include 'news_admin.php';
	}


?>
