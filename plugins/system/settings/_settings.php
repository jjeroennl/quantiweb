<?php
system_check();

function settings_admin(){
    if(system_getuserinfo(system_currentuser(), "role") == 2){
        include 'settings_admin.php';
    }
    else{
        echo "You don't have the permision to change the settings.";
    }
}
?>