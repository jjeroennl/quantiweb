<?php
system_check();

function settings_admin(){
    if(system_getuserinfo(system_currentuser(), "role") == 2){
        $mvc = new Mvc("general.qhtml", __FILE__);
		$mvc->get_all();
    }
    else{
        echo "You don't have the permision to change the settings.";
    }
}
?>
