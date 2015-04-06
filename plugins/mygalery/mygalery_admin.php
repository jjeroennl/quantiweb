<?php
  system_check();
  if(isset($_POST['picture'])){
    $url = '<img src="' . $_POST['picture'] . '">';
    content_add("", $url, system_currentuser(), 1, content_get_type("picture"));

    echo '<font color="green">Your photo if succesfully added!</font>';
  }
?>
<form method="post">
  <input type="text" name="picture">
  <input type="submit">
</form>
