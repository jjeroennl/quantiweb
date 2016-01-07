<?php
function mijnplugin_install(){
admin_addpage("mijnplugin", "adminpage");
}

function adminpage(){
$mvc = new Mvc("index.qhtml", __FILE__);
for($i = 0; $i < 10; $i++){
   $mvc->_("#panel-area")->append(new Panel("large", $i * 2));
}
$mvc->get_all();
}

?>