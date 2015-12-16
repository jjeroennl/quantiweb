<?php
	system_check();


	function nieuws_normal_view(){
		$content = content_query($type = content_get_type("Nieuwsitem"), "loop", 0, 0);

			if(db_numrows($content) == 0){
				echo 'You don\'t have any posts yet. <a href="admin.php?p=' . db_escape($_GET['p']) . '&createnew">Create one?</a>';
			}
			else{
				echo '<p style="text-align: left;"><a href="admin.php?p=' . db_escape($_GET['p']) . '&createnew" class="btn">New post</a></p>';
			}

		echo '<table class="events-list"><tbody>';

			?>



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
