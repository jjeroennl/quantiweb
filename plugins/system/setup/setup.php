<?php
  ini_set('display_errors', '1');
  //getting things ready
  SESSION_START();

  if(!array_key_exists("checksetup", $_SESSION)){
	  include 'config.php';
	  include 'plugins/system/db/_db.php';
	  if(dbTestConnection() == 'FAILED'){
		  $_SESSION['checksetup'] = 1;
		  header("location: setup.php");
	  }
	  else{
		  if(dbTableExist("system_settings")){
			  die("It is currently not necessarily to run the setup. Try again later.");
		  }
		  else{
			  $_SESSION['checksetup'] = 1;
			  header("location: setup.php");
		  }
	  }
  }

  $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
  $url = str_replace('/setup.php', '', $url);
  $url = str_replace('setup.php', '', $url);
  define('OFFICIAL_WEBSITE', 'localhost');
  define('OFFICIAL_NAME', 'Quantiweb');
  define('FORK_NAME', 'Quantiweb');
  define('OFFICIAL_VERSION', '0.02');
  define('INSTALL_LOCATION', $url . "/");
  define('LOCAL_INSTALL_LOCATION', dirname('__FILENAME__'));
  $pagename = 'Setup';
  include 'plugins/system/setup/_setup.php';
  include 'themes/admin/header.php';
  include 'plugins/system/quantimvc/_quantimvc.php';

  $part = 1;

  if (isset($_GET['set_db'])) {
      if (isset($_POST['db_name']) && isset($_POST['db_user']) && isset($_POST['db_pass']) && isset($_POST['db_host']) && isset($_POST['f_location'])) {
          define('DB_NAME', $_POST['db_name']);
          define('DB_USER', $_POST['db_user']);
          define('DB_PASSWORD', $_POST['db_pass']);
          define('DB_HOST', $_POST['db_host']);

          include 'plugins/system/db/_db.php';

          if (dbTestConnection() != 'FAILED') {
              $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()\'"';
              $charactersLength = strlen($characters);
              $length = 28;
              $randomString = '';
              for ($i = 0; $i < $length; ++$i) {
                  $randomString .= $characters[rand(0, $charactersLength - 1)];
              }
              $randomString = hash('sha512', $randomString);
              $data =
"<?php
	//Databaseconfig

	//databasename
	define('DB_NAME', '".DB_NAME."');
	//username
	define('DB_USER', '".DB_USER."');
	//password
	define('DB_PASSWORD', '".DB_PASSWORD."');
	//hostname
	define('DB_HOST', '".DB_HOST."');

	//frameworkconfig
	define('OFFICIAL_WEBSITE', '".OFFICIAL_WEBSITE."');
	define('OFFICIAL_NAME', '".OFFICIAL_NAME."');
	define('FORK_NAME', '".FORK_NAME."');
	define('OFFICIAL_VERSION', '".OFFICIAL_VERSION."');
	define('INSTALL_LOCATION', '".$_POST['f_location']."');
	define('LOCAL_INSTALL_LOCATION', '../".LOCAL_INSTALL_LOCATION."');
	define('INSTALL_SALT', '".$randomString."');

	\$navpage = array();
?>";
              unset($randomString);
              $file = fopen('config.php', 'w');
              if ($file === false) {
                  $part = 'dbfailed';
              }
			  else{
				  if (fwrite($file, $data)) {
					  $part = 2;
					  $_SESSION['db'] = 1;
					  header('location: setup.php?user_setup');
				  } else {
					  $part = 'dbfailed';
					  $_SESSION['db'] = 1;
					  header('location: setup.php?user_setup');
				  }
			  }
              fclose($file);
          } else {
			  header('location: setup.php?dbfail');
          }
      }
  }

  if (array_key_exists("db", $_SESSION) && isset($_GET['user_setup'])) {
      $part = 2;
  }

  if (array_key_exists("db", $_SESSION) == 1 && isset($_GET['finish'])) {
      //grab database system
      include 'config.php';
      include 'plugins/system/db/_db.php';
      include 'plugins/system/system/_system.php';

	  if(dbTableExist("system_settings")){
		  $create_error = 1;
		  header("location: setup.php?user_setup&display_error");
	  }

      //create system settings DB
	  $systemSettings = new Create("system_settings", "setting_id");
	  $systemSettings->addRow("setting", "text");
	  $systemSettings->addRow("value", "text");
	  $systemSettings->addRow("setting_id", "int", "not null auto_increment");
	  $systemSettings->execute();

	  //insert default settings
	  $systemSettingsInserts = array(
		  "websitename" => $_POST['websitename'],
		  "websiteslogan" => $_POST['websiteslogan'],
		  "plugins" => '!system,!admin,!settings,!content,!theme,!quantimvc',
		  "index" => "",
		  "indexRequest" => "",
		  "content_per_page" => "10",
		  "allow_registration" => "0",
		  "default_role" => "0",
		  "theme" => "solid",
		  "timeformat" => "H:i:s",
		  "dateformat" => "Y-m-d",
	  );

	  foreach($systemSettingsInserts as $setting=>$value){
		  $insert = new Insert("system_settings");
		  $insert->insert("setting", $setting);
		  $insert->insert("value", $value);
		  $insert->execute();
	  }

      //create content DB
      $content = new Create('content', "content_id");
	  $content->addRow("title", "text");
	  $content->addRow("author", "int");
	  $content->addRow("content", "text");
	  $content->addRow("type", "int");
	  $content->addRow("status", "int", "default 0");
	  $content->addRow("date", "timestamp", "default current_timestamp");
	  $content->addRow("views", "int");
	  $content->addRow("content_id", "int", "not null auto_increment");
	  $content->execute();

	  //create user DB
      $user = new Create('users', "user_id");
	  $user->addRow("username", "text");
	  $user->addRow("password", "text");
	  $user->addRow("email", "text");
	  $user->addRow("role", "int");
	  $user->addRow("registrationdate", "datetime");
	  $user->addRow("user_id", "int");
	  $user->execute();

      //register admin user
      system_register($_POST['register-username'], $_POST['register-password'], $_POST['register-email'], 'ADMIN', false);

      //create admin DB
      $admin = new Create('admin', "admin_id");
	  $admin->addRow("name", "text");
	  $admin->addRow("function", "text");
	  $admin->addRow("icon", "text");
	  $admin->addRow("type", "text");
	  $admin->addRow("admin_id", "int", "not null auto_increment");
	  $admin->execute();

	  $adminInsert = new Insert("admin");
	  $adminInsert->insert("name", "Plugins");
	  $adminInsert->insert("function", "plugins_config");
	  $adminInsert->insert("admin_id", 2);
	  $adminInsert->execute();

	  $adminInsert = new Insert("admin");
	  $adminInsert->insert("name", "Settings");
	  $adminInsert->insert("function", "settings_admin");
	  $adminInsert->insert("admin_id", 3);
	  $adminInsert->execute();

	  $adminInsert = new Insert("admin");
	  $adminInsert->insert("name", "Themes");
	  $adminInsert->insert("function", "theme_admin");
	  $adminInsert->insert("admin_id", 4);
	  $adminInsert->execute();

      //create roles DB
      $role = new Create('role', "role_id");
	  $role->addRow("role", "text");
	  $role->addRow("role_id", "int", "not null auto_increment");
	  $role->execute();

      //create content type database
	  $contentTypes = new Create('content_types', "type_id");
	  $contentTypes->addRow("type", "text");
	  $contentTypes->addRow("menu", "int");
	  $contentTypes->addRow("way", "int");
	  $contentTypes->addRow("aditional", "text");
	  $contentTypes->addRow("type_id", "int", "not null auto_increment");
	  $contentTypes->execute();

      //create content metadata
	  $contentMetadata = new Create('content_metadata', "meta_id");
	  $contentMetadata->addRow("content", "int");
	  $contentMetadata->addRow("metadata", "text");
	  $contentMetadata->addRow("value", "text");
	  $contentMetadata->addRow("meta_id", "int", "not null auto_increment");
	  $contentMetadata->execute();

      $part = 3;
  }

  $mvc = new Mvc(__FILE__);

  if(!isset($_GET['dbfail'])){
	  $mvc->_("#db_error")->hide();
  }

  $mvc->_("#create_error")->hide();
  if ($part == 1) {
      $mvc->_('#db_failed')->hide()->_('#part_2')->hide()->_('#part_3')->hide();
      $mvc->_('#forkname')->set_html(FORK_NAME);
      $mvc->_('#f_location')->set_value($url . "/");
      $mvc->get_all();
  }

  elseif ($part == 'dbfailed') {
      $mvc->_('#db_failed_textarea')->set_html($data);
      $mvc->_('#part_1')->hide()->_('#part_2')->hide()->_('#part_3')->hide();
      $mvc->get_all();
	  $_SESSION['db'] = 1;
  }

  elseif ($part == 2) {
	  if(array_key_exists("display_error", $_GET)){
		 $mvc->_("#create_error")->show();
	  }
      $mvc->_('#db_failed')->hide()->_('#part_1')->hide()->_('#part_3')->hide();
      $mvc->get_all();
  }

  elseif ($part == 3) {
      $mvc->_('#db_failed')->hide()->_('#part_1')->hide()->_('#part_2')->hide();
      $mvc->get_all();
  }

    include 'themes/admin/footer.php';
