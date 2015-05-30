<?php
	SESSION_START();
	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$url = str_replace("setup.php", "", $url);

	//defaults
	define('OFFICIAL_WEBSITE', 'localhost');
    define('OFFICIAL_NAME', 'Quantiweb');
    define('FORK_NAME', 'Quantiweb');
    define('OFFICIAL_VERSION', '0.02');
    define('INSTALL_LOCATION', $url);
    define('LOCAL_INSTALL_LOCATION', dirname("__FILENAME__"));

    $pagename = "Setup";
	include("themes/admin/header.php");

	if(isset($_GET['db'])){

		unset($_SESSION['db']);
		if(!isset($status)){
				$status = 3;
		}

		?>
		<div class="section section-breadcrumbs">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<h1>Setup</h1>
						</div>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="service-wrapper left-align">
							<b>Welcome to the instalation process of <?php echo OFFICIAL_NAME;?>. This setup will let you configure and install your website in a few easy steps.</b>
							<h2>Database setup</h2>
							<p>This is the most complex part of the setup. For this you will need to setup a database. How to do that depends of your hosting provider. If you don't know your database configuration, contact your hosting provider.</p>
							<?php if($status == 0){
								echo '
								<div class="alert alert-danger" role="alert">
									<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
									<span class="sr-only">Error:</span>
									Something in your configuration isn\'t right.
								</div>';

							}
							?>
							<form method="post" action="setup.php?set_db">
								<input type="text" class="form-control" required name="db_name" placeholder="Database name"
								<?php if($status == 0){ echo 'value="' . $_POST['db_name'] . '"';}?>
								><br>
								<input type="text" class="form-control" required name="db_user" placeholder="Database username"
								<?php if($status == 0){ echo 'value="' . $_POST['db_user'] . '"';}?>><br>
								<input type="password" class="form-control" required name="db_pass" placeholder="Database password" ><br>
								<input type="text" class="form-control" required name="db_host" placeholder="Database hostname"
								<?php if($status == 0){ echo 'value="' . $_POST['db_host'] . '"';}?>
								<small style="color: grey;">If you don't know which hostname you have to enter than it's usually localhost.</small><br>
								<input type="text" class="form-control" required name="f_location" placeholder="Instalation URL" value="<?php echo $url;?>"><br>

								<input class="btn btn-grey" value="Send" type="submit">
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	elseif(isset($_GET['set_db'])){
		//Databaseconfig

		//databasename
		define('DB_NAME', $_POST['db_name']);
		//username
		define('DB_USER', $_POST['db_user']);
		//password
		define('DB_PASSWORD', $_POST['db_pass']);
		//hostname
		define('DB_HOST', $_POST['db_host']);
		include 'plugins/system/db/_db.php';
	
			$status = 1;

			$data = "<?php
//Databaseconfig

//databasename
define('DB_NAME', '" . DB_NAME . "');
//username
define('DB_USER', '" . DB_USER ."');
//password
define('DB_PASSWORD', '". DB_PASSWORD ."');
//hostname
define('DB_HOST', '" . DB_HOST . "');

//frameworkconfig
define('OFFICIAL_WEBSITE', '". OFFICIAL_WEBSITE ."');
define('OFFICIAL_NAME', '". OFFICIAL_NAME ."');
define('FORK_NAME', '". FORK_NAME ."');
define('OFFICIAL_VERSION', '". OFFICIAL_VERSION ."');
define('INSTALL_LOCATION', '" . $_POST['f_location'] . "');
define('LOCAL_INSTALL_LOCATION', '../" . LOCAL_INSTALL_LOCATION . "');

\$navpage = array();
?>";
			$file = fopen("config.php", "w");
			if(fwrite($file, $data)){
				$db_status = 1;
				$_SESSION['db'] = 1;
			}
			else{
				$db_status = 2;
				$_SESSION['db'] = 1;
			}
			fclose($file);
		
		if($db_status == 2){
			?>
			<div class="section section-breadcrumbs">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<h1>Setup</h1>
						</div>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="service-wrapper left-align">
							<h2>Finalizing database setup</h2>
							It seems that the setup doesn't have rights over the config.php file. You need to manualy copy and paste this into config.php, then click the next button.
							<textarea class="form-control" style = "height: 300px;"><?php echo $data;?></textarea>
							<a href="setup.php?user_setup">Next</a>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		elseif($status == 1){
			header("Location: setup.php?user_setup");
		}
	}
	elseif(isset($_GET['user_setup']) && isset($_SESSION['db'])){
		?>
		<div class="section section-breadcrumbs">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<h1>Setup</h1>
						</div>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="service-wrapper left-align">
							<h2>Finalizing setup</h2>
							<p>Now it's time to setup the nice (and last) part: your website! Tell us what you want to call your site, and which username and password you want to manage it.</p>
							<form method="post" action="setup.php?finish">
								<hr>
								<input type="text" class="form-control" name="websitename" required placeholder="Websitename"><br>
								<input type="text" class="form-control" name="websiteslogan" placeholder="Slogan">
								<hr>
								<input type="text" class="form-control" name="register-username" required placeholder="Username"><br>
								<input type="password" class="form-control" name="register-password" required placeholder="Password"><br>
								<input type="email" class="form-control" name="register-email" required placeholder="Email adress"><br>
								<input type="submit" class="btn btn-grey" value="Setup!">
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	elseif(isset($_GET['finish']) && isset($_SESSION['db']) && isset($_POST['websitename']) && isset($_POST['register-username']) && isset($_POST['register-password'])){
		//grab database system
			include 'config.php';
			include 'plugins/system/db/_db.php';
			include 'plugins/system/system/_system.php';

			//create system settings DB
			db_create("system_settings", array(
				"setting" => "TEXT",
				"value" => "TEXT",
				"setting_id" => "INT NOT NULL AUTO_INCREMENT "
			), "setting_id");

				db_insert("system_settings", array(
					"setting" => "websitename",
					"value" => $_POST['websitename']
				));

				db_insert("system_settings", array(
					"setting" => "websiteslogan",
					"value" => $_POST['websiteslogan']
				));
				db_insert("system_settings", array(
					"setting" => "default_db",
					"value" => "SYSTEM"
				));
				db_insert("system_settings", array(
					"setting" => "plugins",
					"value" => "!system,!admin,!settings,!content,!theme"
				));
				db_insert("system_settings", array(
					"setting" => "index",
					"value" => ""
				));
                db_insert("system_settings", array(
					"setting" => "wantindex",
					"value" => ""
				));
				db_insert("system_settings", array(
					"setting" => "c_perpage",
					"value" => "10"
				));
				db_insert("system_settings", array(
					"setting" => "allow_registration",
					"value" => "0"
				));
				db_insert("system_settings", array(
					"setting" => "default_role",
					"value" => "0"
				));
				db_insert("system_settings", array(
					"setting" => "theme",
					"value" => "solid"
				));
                db_insert("system_settings", array(
					"setting" => "timeformat",
					"value" => "H:i:s"
				));            
                db_insert("system_settings", array(
					"setting" => "dateformat",
					"value" => "Y-m-d"
				));

			//create content DB
			db_create("content", array(
				"title" => "TEXT",
				"author" => "INT",
				"content" => "TEXT",
				"type" => "INT",
				"status" => "INT DEFAULT 0",
				"date" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                "views" => "INT",
				"content_id" => "INT NOT NULL AUTO_INCREMENT "
			), "content_id");

			//create users DB
			db_create("users", array(
				"username" => "TEXT",
				"password" => "TEXT",
				"email" => "TEXT",
				"role" => "INT",
				"registrationdate" => "DATETIME",
				"user_id" => "INT NOT NULL AUTO_INCREMENT "
			), "user_id");

			//register admin user
			system_register($_POST['register-username'], $_POST['register-password'], $_POST['register-email'], "ADMIN");

			//create admin DB
			db_create("admin", array(
				"name" => "TEXT",
				"function" => "TEXT",
				"admin_id" => "INT NOT NULL AUTO_INCREMENT "
			), "admin_id");

				db_insert("admin", array(
					"name" => "Plugins",
					"function" => "plugins_config",
					"admin_id" => 2
				));

                db_insert("admin", array(
					"name" => "Settings",
					"function" => "settings_admin",
					"admin_id" => 3
				));
                db_insert("admin", array(
					"name" => "Settings",
					"function" => "theme_admin",
					"admin_id" => 4
				));
			//create roles DB
			db_create("role", array(
				"role" => "TEXT",
				"role_id" => "INT NOT NULL AUTO_INCREMENT "
			), "role_id");

			//create content type database
			db_create("content_types", array(
				"type" => "TEXT",
				"menu" => "INT",
				"way" => "INT",
				"aditional" => "TEXT",
				"type_id" => "INT NOT NULL AUTO_INCREMENT "
			), "type_id");

			//create content metadata
			db_create("content_metadata", array(
				"content" => "INT",
				"metadata" => "TEXT",
				"value" => "TEXT",
				"meta_id" => "INT NOT NULL AUTO_INCREMENT "
			), "meta_id");

			header("Location: index.php");
	}
	else{
		header("Location: setup.php?db");
	}
	include("themes/admin/footer.php");
?>
