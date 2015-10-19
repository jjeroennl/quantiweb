<?php
  //getting things ready
  $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
  $url = str_replace("newsetup.php", "", $url);
  define('OFFICIAL_WEBSITE', 'localhost');
  define('OFFICIAL_NAME', 'Quantiweb');
  define('FORK_NAME', 'Quantiweb');
  define('OFFICIAL_VERSION', '0.02');
  define('INSTALL_LOCATION', $url);
  define('LOCAL_INSTALL_LOCATION', dirname("__FILENAME__"));
  $pagename = "Setup";
  include("themes/admin/header.php");
  include("plugins/system/quantimvc/_quantimvc.php");

  $part = 1;

  if(isset($_GET['set_db'])){
    if(isset($_POST['db_name']) && isset($_POST['db_user']) && isset($_POST['db_pass']) && isset($_POST['db_host']) && isset($_POST['f_location'])){
      define('DB_NAME', $_POST['db_name']);
  		define('DB_USER', $_POST['db_user']);
  		define('DB_PASSWORD', $_POST['db_pass']);
  		define('DB_HOST', $_POST['db_host']);

  		include 'plugins/system/db/_db.php';

      if(db_testconnection() != "FAILED"){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()\'"';
    		$charactersLength = strlen($characters);
    		$length = 28;
    		$randomString = '';
    		for ($i = 0; $i < $length; $i++) {
    			$randomString .= $characters[rand(0, $charactersLength - 1)];
    		}
    		$randomString = hash("sha512", $randomString);
        
        $part = 2;
      }
      else{

      }
    }
  }


  $mvc = new Mvc(__FILE__);

  if($part == 1){
    $mvc->_("#db_failed")->hide()->_("#part_2")->hide();
    $mvc->_("#forkname")->set_html(FORK_NAME);
    $mvc->_("#f_location")->set_value($url);
    $mvc->get_all();
  }

  if($part == "dbfailed"){
    $mvc->_("#part_1")->hide()->_("#part_2")->hide();
    $mvc->get_all();
  }

  if($part == 2){
    $mvc->_("#db_failed")->hide()->_("#part_1")->hide();
    $mvc->get_all();
  }

  if($part == 3){
    $mvc->_("#part1")->hide()->_("#db_failed")->hide()->_("#part_2")->hide();
  }

	include("themes/admin/footer.php");
?>
