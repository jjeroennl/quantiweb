<?php
	function news_install(){
		content_create_type("Nieuws", "nieuws_getcomments", 1, 1);
		content_create_type("Binnenland", "nieuws_getcomments", 1, 1);
		content_create_type("Buitenland", "nieuws_getcomments", 1, 1);
		content_create_type("Economie", "nieuws_getcomments", 1, 1);
		content_create_type("Tech", "nieuws_getcomments", 1, 1);
		content_create_type("Sport", "nieuws_getcomments", 1, 1);

		system_setindex("nieuws_index");
		admin_addpage("Nieuws", "nieuws_admin", "puzzle-piece");

		if(dbTableExist("nieuws_comments") == 0){
			$createTable = new Create("nieuws_comments", "comment_id");
			$createTable->addRow("comment", "text");
			$createTable->addRow("name", "text");
			$createTable->addRow("email", "text");
			$createTable->addRow("comment_id", "int", "not null auto_increment");
			$createTable->execute();
		}

		if(dbTableExist("nieuws_categories") == 0){
			$createTable = new Create("nieuws_categories", "category_id");
			$createTable->addRow("category", "text");
			$createTable->addRow("category_id", "int", "not null auto_increment");
			$createTable->execute();
		}

	}

	function nieuws_index(){
		nieuws_checkpost();
		$content = content_query(00, "loop");

		foreach($content->fetch() as $row){
			echo "<h1>" . $row['title'] . "</h1>";
            echo substr($row['content'], 0, 500) . "...</p>";
			echo "<br>" . content_readmore($row['content_id']);
		}
	}

	function nieuws_getcomments(){
		nieuws_checkpost();
		?>

		<a href="mailto:?body=<?php echo INSTALL_LOCATION . "/index.php?p=" . $_GET['p'] ;?>"><img src="themes/admin/img/social/mail.png" width="52px"> </a>

		<a href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=<?php echo INSTALL_LOCATION;?>"><img src="themes/admin/img/social/facebook.png" width="52px"> </a>
		<a href="https://twitter.com/intent/tweet?url=<?php echo INSTALL_LOCATION . "/index.php?p=" . $_GET['p'] ;?>&text=<?php echo content_get_page_title();?> - <?php echo system_getsetting("websitename");?>"><img src="themes/admin/img/social/twitter.png" width="52px"> </a>
		<?php
        echo "<hr><h2> Comments</h2>";
		$query = new Select("nieuws_comments");
		$query->execute();
        if($query->numrows() < 1){
            echo "There are no comments";
        }
		foreach($query->fetch() as $row){
			echo "<b>" . $row['name'] . "</b>";
			echo "<p>" . $row['comment'] . "</p>";
		}
		echo "<h4>Leave comment</h4>";
		nieuws_leavecomment();
	}

	function nieuws_checkpost(){
		if(isset($_POST['comment-name']) && isset($_POST['comment-email']) && isset($_POST['comment'])){
			$insert = new Insert("nieuws_comments");
			$insert->insert("comment", $_POST['comment']);
			$insert->insert("name", $_POST['comment-name']);
			$insert->insert("email", $_POST['comment-email']);
			$insert->execute();

			$url = "index.php?p=" . $_GET['p'];
			header("Location: " . $url);
		}
	}

	function nieuws_leavecomment(){
		?>
		<form method="POST">
			<input type="text" style="margin-top: 5px;" class="form-control" name="comment-name" placeholder="Name">
			<input type="email" style="margin-top: 5px;" class="form-control" name="comment-email" placeholder="Email">
			<textarea class="form-control" placeholder="Comment" name="comment" style="margin-top: 5px;"></textarea>
			<input type="submit" style="margin-top: 5px;" class="btn btn-primary">
		</form>
		<?php
	}

	function nieuws_createcomment($comment, $name, $email){
		$insert = new Insert("nieuws_comments");
		$insert->insert("comment", $comment);
		$insert->insert("name", $name);
		$insert->insert("email", $email);
		$insert->execute();
	}

	function nieuws_admin(){
		$send = 1;

		if(isset($_GET['edit'])){
			$mvc = new Mvc("news-edit.qhtml", __FILE__);
			$mvc->get_all();
		}
		else{
			$mvc = new Mvc("news-admin.qhtml", __FILE__);
			$content = content_query($type = content_get_type("Nieuwsitem"), "loop", 0, 0);
			if($content->numrows() == 0){
				$mvc->_("#newsitems")->append('You don\'t have any posts yet. <a href="admin.php?p=' . strip_tags($_GET['p']) . '&createnew">Create one?</a>');
			}
			else{
				foreach($content->fetch() as $row){
					$mvc->_("#newsitems")->append(nieuws_backend_item($row));
				}
			}
			$mvc->get_all();
		}

	}

	function nieuws_insert(){
		$title = $_POST['title'];
		$content = $_POST['content'];
		$user = system_currentuser();
		if(isset($_POST['submit-publish'])){
			content_add($title, $content, $user, 1, content_get_type($_POST['categorie']));
		}
		else{
			content_add($title, $content, $user, 0, content_get_type($_POST['categorie']));
		}
		header("Location: admin.php?p=" . $_GET['p']);
	}

	function nieuws_update(){
		$id = $_POST['edit'];
		$content = $_POST['content'];
		content_modify($id, $content);
		header("Location: admin.php?p=" . $_GET['p']);
	}

	function nieuws_backend_item($row){
		return '

		<tr>
			<td style="width: 112px;">
				<div class="event-date">
					<div class="event-day">' . date("d", strtotime($row['date'])) . '</div>
					<div class="event-month">' .  strtoupper(date("M", strtotime($row['date']))) . '</div>
					<div style="margin-top: -5px;"class="event-month">' . date("Y", strtotime($row['date'])) . '</div>
				</div>

			</td>
			<td>
				<b>' . substr($row['title'], 0, 51) . '</b>
			</td>
			<td class="event-venue hidden-xs"><i class="icon-map-marker"></i>' . $row['views'] . '</td>
			<td class="event-venue hidden-xs"><i class="icon-map-marker"></i>' . ucfirst(system_getuserinfo($row['author'], "username")) . '</td>
			<td><a href="#" data-toggle="modal" data-target="#remove_' . $row['content_id'] . '" class="btn btn-red btn-sm"><i class="fa  fa-trash-o"></i></a> <a href="admin.php?p=' . strip_tags($_GET['p']) . '&edit=' .$row['content_id'] . '" class="btn btn-blue btn-sm event-more"><i class="fa  fa-pencil"></i></a></td>
		</tr>';
	}


?>
