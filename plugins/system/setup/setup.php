<?php
  ini_set('display_errors', '1');
  //getting things ready
  SESSION_START();

  if(!array_key_exists("checksetup", $_SESSION)){
	  include 'config.php';
	  include 'plugins/system/db/_db.php';
	  if(db_testconnection() == 'FAILED'){
		  $_SESSION['checksetup'] = 1;
		  header("location: setup.php");
	  }
	  else{
		  if(db_table_exist("system_settings")){
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
  define('INSTALL_LOCATION', $url);
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

          if (db_testconnection() != 'FAILED') {
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
                  $_SESSION['db'] = 1;
              }
              if (fwrite($file, $data)) {
                  $part = 2;
                  $_SESSION['db'] = 1;
                  header('location: setup.php?user_setup');
              } else {
                  $part = 'dbfailed';
                  $_SESSION['db'] = 1;
                  header('location: setup.php?user_setup');
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

	  if(db_table_exist("system_settings")){
		  $create_error = 1;
		  header("location: setup.php?user_setup&display_error");
	  }

      //create system settings DB
      db_create('system_settings', array(
          'setting' => 'TEXT',
          'value' => 'TEXT',
          'setting_id' => 'INT NOT NULL AUTO_INCREMENT ',
      ), 'setting_id');

      db_insert('system_settings', array(
              'setting' => 'websitename',
              'value' => $_POST['websitename'],
          ));

      db_insert('system_settings', array(
              'setting' => 'websiteslogan',
              'value' => $_POST['websiteslogan'],
          ));
      db_insert('system_settings', array(
              'setting' => 'default_db',
              'value' => 'SYSTEM',
          ));
      db_insert('system_settings', array(
              'setting' => 'plugins',
              'value' => '!system,!admin,!settings,!content,!theme,!quantimvc',
          ));
      db_insert('system_settings', array(
              'setting' => 'index',
              'value' => '',
          ));
      db_insert('system_settings', array(
              'setting' => 'wantindex',
              'value' => '',
          ));
      db_insert('system_settings', array(
              'setting' => 'c_perpage',
              'value' => '10',
          ));
      db_insert('system_settings', array(
              'setting' => 'allow_registration',
              'value' => '0',
          ));
      db_insert('system_settings', array(
              'setting' => 'default_role',
              'value' => '0',
          ));
      db_insert('system_settings', array(
              'setting' => 'theme',
              'value' => 'solid',
          ));
      db_insert('system_settings', array(
              'setting' => 'timeformat',
              'value' => 'H:i:s',
          ));
      db_insert('system_settings', array(
              'setting' => 'dateformat',
              'value' => 'Y-m-d',
          ));

      //create content DB
      db_create('content', array(
          'title' => 'TEXT',
          'author' => 'INT',
          'content' => 'TEXT',
          'type' => 'INT',
          'status' => 'INT DEFAULT 0',
          'date' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
          'views' => 'INT',
          'content_id' => 'INT NOT NULL AUTO_INCREMENT ',
      ), 'content_id');

      //create users DB
      db_create('users', array(
          'username' => 'TEXT',
          'password' => 'TEXT',
          'email' => 'TEXT',
          'role' => 'INT',
          'registrationdate' => 'DATETIME',
          'user_id' => 'INT NOT NULL AUTO_INCREMENT ',
      ), 'user_id');

      //register admin user
      system_register($_POST['register-username'], $_POST['register-password'], $_POST['register-email'], 'ADMIN');

      //create admin DB
      db_create('admin', array(
          'name' => 'TEXT',
          'function' => 'TEXT',
          'icon' => 'TEXT',
          'admin_id' => 'INT NOT NULL AUTO_INCREMENT ',
      ), 'admin_id');

      db_insert('admin', array(
              'name' => 'Plugins',
              'function' => 'plugins_config',
              'admin_id' => 2,
          ));

      db_insert('admin', array(
              'name' => 'Settings',
              'function' => 'settings_admin',
              'admin_id' => 3,
          ));
      db_insert('admin', array(
              'name' => 'Settings',
              'function' => 'theme_admin',
              'admin_id' => 4,
          ));
      //create roles DB
      db_create('role', array(
          'role' => 'TEXT',
          'role_id' => 'INT NOT NULL AUTO_INCREMENT ',
      ), 'role_id');

      //create content type database
      db_create('content_types', array(
          'type' => 'TEXT',
          'menu' => 'INT',
          'way' => 'INT',
          'aditional' => 'TEXT',
          'type_id' => 'INT NOT NULL AUTO_INCREMENT ',
      ), 'type_id');

      //create content metadata
      db_create('content_metadata', array(
          'content' => 'INT',
          'metadata' => 'TEXT',
          'value' => 'TEXT',
          'meta_id' => 'INT NOT NULL AUTO_INCREMENT ',
      ), 'meta_id');
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
      $mvc->_('#f_location')->set_value($url);
      $mvc->get_all();
  }

  if ($part == 'dbfailed') {
      $mvc->_('#db_failed_textarea')->set_html($data);
      $mvc->_('#part_1')->hide()->_('#part_2')->hide()->_('#part_3')->hide();
      $mvc->get_all();
  }

  if ($part == 2) {
	  if(array_key_exists("display_error", $_GET)){
		 $mvc->_("#create_error")->show();
	  }
      $mvc->_('#db_failed')->hide()->_('#part_1')->hide()->_('#part_3')->hide();
      $mvc->get_all();
  }

  if ($part == 3) {
      $mvc->_('#db_failed')->hide()->_('#part_1')->hide()->_('#part_2')->hide();
      $mvc->get_all();
  }

    include 'themes/admin/footer.php';
