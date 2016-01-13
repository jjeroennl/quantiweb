<?php
system_check();

function settings_addpage($name, $function, $icon = "cog"){
	if(!dbEntryExist("admin", array(
		"name" => $name,
		"function" => $function
	))){
		$insert = new Insert("admin");
		$insert->insert("name", $name);
		$insert->insert("function", $function);
		$insert->insert("icon", $icon);
		$insert->insert("type", "settings");
		$insert->execute();
	}
}

function settings_admin(){
	if(!array_key_exists("settingspage", $_GET)){
		settings_list();
	}
	else{
		settings_load(htmlspecialchars(strip_tags($_GET['settingspage'])));
	}
}

function settings_get_url(){
	return "admin.php?p=" . $_GET['p'] . "&settingspage=" . $_GET['settingspage'];
}

function settings_list(){
	$mvc = new Mvc("settings.qhtml", __FILE__);
	$select = new Select("admin");
	$select->where("type", "settings");
	$select->orderBy("name");
	$select->execute();

	foreach($select->fetch() as $row){
		$mvc->_("#panel-area")->append(
			new Panel("small",'
				<a class="noline" href="admin.php?p=3&settingspage=' . $row['admin_id'] . '">
					<i class="fa fa-' . $row['icon'] . ' fa-5x"></i>
					<h3>' . $row['name'] . '</h3>
				</a>
			')
		);
	}

	$mvc->get_all();
}

function settings_load($setting){
	$settings = new Select("admin");
	$settings->where("admin_id", (int)$setting);
	$settings->execute();
	foreach($settings->fetch() as $row){
		$row['function']();
	}
}

function settings_store(){

}

function settings_general(){
	$mvc = new Mvc("general.qhtml", __FILE__);
	$mvc->add_controller("settings_general_save");
	$mvc->_("#websitename")->set_value(system_getsetting("websitename"));
	$mvc->_("#slogan")->set_value(system_getsetting("websiteslogan"));
	$mvc->_("#timeformat")->set_value(system_getsetting("timeformat"));
	$mvc->_("#dateformat")->set_value(system_getsetting("dateformat"));
	if(system_getsetting("allow_registration")){
		$mvc->_("#allowregistration")->set_attribute("checked", 1);
	}
	$mvc->get_all();
}

function settings_general_save($data){
	if(array_key_exists("websitename", $_POST)){
		system_setsetting("websitename", $_POST['websitename']);
		system_setsetting("websiteslogan", $_POST['slogan']);
		system_setsetting("timeformat", $_POST['timeformat']);
		system_setsetting("dateformat", $_POST['dateformat']);
		if(array_key_exists('allowregistration', $_POST)){
			system_setsetting("allow_registration", 1);
		}
		else{
			system_setsetting("allow_registration", 0);
		}
	}
}
?>
