<?php
	SESSION_START();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	//grab configuration file
	include 'config.php';

	//grab database system
	include 'plugins/system/db/_db.php';

	if(dbTableExist("system_settings") == 0){
		header("Location: setup.php?db");
	}

	//load plugin system
	include 'plugins/system/plugins/_plugins.php';
	plugins_load();
?>
