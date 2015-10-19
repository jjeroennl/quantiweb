<?php
	system_check();
	function removemodal($id){
		echo '
					<div class="modal fade" style="text-align: left;"id="remove_' . $id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

					';
					$div = "alert-danger";

					echo '
						<div class="modal-dialog">
							<div class="modal-content">
							<div class="modal-header ' . $div . '">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel"><i class="fa fa-trash"></i> Remove ' . substr(content_get_title($id),0, 20) . '</h4>
							</div>
							<div class="modal-body">
							Are you totaly sure you want to remove this post "' . substr(content_get_title($id), 0, 20). '"?
							</div>
							<div class="modal-footer ' . $div . '">
								<button type="button" class="btn btn" data-dismiss="modal">No, get me out of here!</button>
								<a href="admin.php?p=' . $_GET['p'] . '&remove=' . $id . '" class="btn btn-red">Remove "' . substr(content_get_title($id),0, 20) . '"</a>

							</div>
							</div>
						</div>
					</div>
			';
		}


	function nieuws_normal_view(){


		$content = content_query($type = content_get_type("Nieuwsitem"), "loop", 0, 0);

			if(db_numrows($content) == 0){
				echo 'You don\'t have any posts yet. <a href="admin.php?p=' . db_escape($_GET['p']) . '&createnew">Create one?</a>';
			}
			else{
				echo '<p style="text-align: left;"><a href="admin.php?p=' . db_escape($_GET['p']) . '&createnew" class="btn">New post</a></p>';
			}

		echo '<table class="events-list"><tbody>';

			while($row = db_grab($content)){

?>
						<tr>
							<td style="width: 112px;">
								<div class="event-date">
									<div class="event-day"><?php echo date("d", strtotime($row['date']));?></div>
									<div class="event-month"><?php echo strtoupper(date("M", strtotime($row['date'])));?></div>
									<div style="margin-top: -5px;"class="event-month"><?php echo date("Y", strtotime($row['date']));?></div>
								</div>

							</td>
							<td>
								<b><?php echo substr($row['title'], 0, 51);?></b>
							</td>
							<td class="event-venue hidden-xs"><i class="icon-map-marker"></i><?php echo $row['views'];?></td>
							<td class="event-venue hidden-xs"><i class="icon-map-marker"></i><?php echo ucfirst(system_getuserinfo($row['author'], "username"));?></td>

							<td><a href="#" data-toggle="modal" data-target="#remove_<?php echo $row['content_id']; ?>" class="btn btn-red btn-sm"><i class="fa  fa-trash-o"></i></a> <a href="admin.php?p=<?php echo db_escape($_GET['p'])?>&edit=<?php echo $row['content_id'];?>" class="btn btn-blue btn-sm event-more"><i class="fa  fa-pencil"></i></a></td>
							<?php removemodal($row['content_id']);?>
						</tr>


<?php
			}




		echo '</tbody></table>';
	}

	function nieuws_create_new(){
		?>
		<div class="col-sm-8 left-align">
			<form action="admin.php?p=<?php echo db_escape($_GET['p']);?>&postnew" method="POST">

				<textarea name="content" id="editor"></textarea>



			<script>
			CKEDITOR.replace( 'editor', {
				coreStyles_bold: { element: 'b' },
				coreStyles_italic: { element: 'i' },

				fontSize_style: {
					element: 'font',
					attributes: { 'size': '#(size)' }
				}
			});
			</script>

		</div>
		<div class="col-sm-4 left-align">
			<input style="margin-bottom: 10px;" type="text" class="form-control" name="title" placeholder="Post title">
			<select class="form-control" style="margin-bottom: 10px;">
				<?php
					$users = system_getusers();

					while($user = db_grab($users)){
						echo "<option>" . ucfirst($user['username']) . "</option>";
					}
				?>
			</select>
			<select name="categorie" class="form-control" style="margin-bottom: 10px;">
				<option>Nieuws</option>
				<option>Binnenland</option>
				<option>Buitenland</option>
				<option>Economie</option>
				<option>Tech</option>
				<option>Sport</option>
			</select>
			<input type="submit" style="float: right; margin-left: 5px;" value="Publish" class="btn" name="submit-publish"> <input type="submit" value="Save as draft" style="float: right;"class="btn" name="submit-draft">
		</div>
		</form>
		<div class="clearfix"></div>
		<?php
	}

	function nieuws_edit(){
		$post = db_escape($_GET['edit']);
		$content = content_query(00, "static", $post, 0);

		while($row = db_grab($content)){

			$title = $row['title'];
			$post = $row['content'];
			$id = $row['content_id'];
			$cat = $row['type'];
			$status = $row['status'];
		}
		?>
		<div class="col-sm-8 left-align">
			<form action="admin.php?p=<?php echo db_escape($_GET['p']);?>&postedit" method="POST">

				<textarea name="content" id="editor"><?php echo $post;?></textarea>



			<script>
			CKEDITOR.replace( 'editor', {
				coreStyles_bold: { element: 'b' },
				coreStyles_italic: { element: 'i' },

				fontSize_style: {
					element: 'font',
					attributes: { 'size': '#(size)' }
				}
			});
			</script>

		</div>
		<div class="col-sm-4 left-align">
			<input style="margin-bottom: 10px;" type="text" class="form-control" value="<?php echo $title;?>" name="title" placeholder="Post title">
			<input type="hidden" name="edit" value="<?php echo $id;?>">
			<select class="form-control">
				<?php
					$users = system_getusers();

					while($user = db_grab($users)){
						echo "<option>" . ucfirst($user['username']) . "</option>";
					}
				?>
			</select> <br>
			
			<select name="categorie" class="form-control" style="margin-bottom: 10px;">
				<option><?php echo content_get_type_name($cat);?></option>
				<option>Nieuws</option>
				<option>Binnenland</option>
				<option>Buitenland</option>
				<option>Economie</option>
				<option>Tech</option>
				<option>Sport</option>
			</select>
			<?php
				if($status == 1){
					echo '<input type="submit" style="float: right; margin-left: 5px;" value="Update" class="btn" name="submit-publish">';
				}
				else{
					echo '<input type="submit" style="float: right; margin-left: 5px;" value="Publish" class="btn" name="submit-publish"> 			 <input type="submit" value="Save as draft" style="float: right;"class="btn" name="submit-draft">';	
				}
			?>

		</div>
		</form>
		<div class="clearfix"></div>
		<?php
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



	if(isset($_GET['createnew'])){
		nieuws_create_new();
	}
	elseif(isset($_GET['postnew'])){
		nieuws_insert();
	}
	elseif(isset($_GET['postedit'])){
		nieuws_update();
	}
	elseif(isset($_GET['edit'])){
		nieuws_edit();
	}
	elseif(isset($_GET['remove'])){
		content_delete($_GET['remove']);
		header("Location: admin.php?p=" . $_GET['p']);
	}
	else{
		nieuws_normal_view();
	}
?>
