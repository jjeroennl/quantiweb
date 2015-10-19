<?php
system_check();
function mygalery_install(){
  content_create_type("picture", null, 1, 1);
  admin_addpage("Galery", "mygalery_admin");
}
function mygalery_admin(){
  include 'mygalery_admin.php';
  content_delete();
}
?>
