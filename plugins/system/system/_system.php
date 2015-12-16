<?php
	function system_getindex(){
		$result = system_getsetting("index");
		if($result != ""){
			if(function_exists($result)){
				$result();
			}
		}
		else{
			echo "You don't seem to have any plugins installed that you can use as a homepage. Install one from <a href=\"http://" . OFFICIAL_WEBSITE ."\">" . OFFICIAL_WEBSITE . "</a>";
		}
	}

    function system_setindex($function){
        if(system_getsetting("index") == ""){
            system_setsetting("index", $function);
            system_setsetting("wantindex", $function);
        }
        else{
            system_setsetting("wantindex", system_getsetting("wantindex") . "," . $function);
        }
    }
    function _system_setindex($function){
     system_setsetting("index", $function);
    }
	function system_getplugins(){
		$plugins = system_getsetting("plugins");
		return $plugin_array = explode(',', $plugins);
	}

	function system_getsetting($setting){
		$query = db_select("*", "system_settings", array("setting" => $setting));

		while($row = db_grab($query)){
			return $row['value'];
		}
	}

    function _system_removeuser($userid){
        if(system_getuserinfo(system_currentuser(), "role") == 2){
            db_delete("users", array(
                "user_id" => $userid
            ), 1);
        }
    }

	function system_setsetting($setting, $value){
		$db_value = array( "value" => $value);
		$db_where = array("setting" => $setting);
		db_update("system_settings", $db_value, $db_where);
	}

	function system_newsetting($setting, $defaultvalue){
		db_insert("system_settings", array("value" => $defaultvalue));
	}

	function system_login($username, $password){
		$_username = db_escape($username);
		$_password = db_escape($password);
		$_password = system_password_salt($_password, $_username, system_getuserinfo($_username, "registrationdate"), system_getuserinfo($_username, "email"));

		$query = db_select("user_id", "users", array(
			"username" => $_username,
			"password" => $_password
		));

		if(db_numrows($query) == 1){
			while($row = db_grab($query)){
				$_SESSION['login'] = $row['user_id'];
				return 1;
			}
		}
		else{
			return 0;
		}

	}

	function system_login_verify($result){
		if($result == 1){
			if(isset($_SESSION['login'])){
				header("location: admin.php");
			}
			else{
				header("location: admin.php?loginform&loginfailed");
			}
		}
		else{
			header("location: admin.php?loginform&loginfailed");
		}
	}

	function system_getuserinfo($username, $info){
		if (!preg_match('#[0-9]#',$username)){
			$_info = db_escape($info);
			if($_info != "password"){
				$query = db_select($_info, "users", array(
					"username" => db_escape($username)
				));

				while($row = db_grab($query)){
					$result =  $row[$_info];
				}

				if(isset($result)){
					return $result;
				}
				else{
					return "The specified information is non existing or not accesable";
				}
			}
			else{
				return "Access denied";
			}
		}
		else{

			$_info = db_escape($info);
			if($_info != "password"){
				$query = db_select($_info, "users", array(
					"user_id" => db_escape($username)
				));

				while($row = db_grab($query)){
					$result =  $row[$_info];
				}

				if(isset($result)){
					return $result;
				}
				else{
					return "The specified information is non existing or not accesable";
				}
			}
			else{
				return "Access denied";
			}
		}
	}

	function system_getusers(){
		return db_select("username, user_id", "users");
	}

    function system_setuserinfo($user, $setting, $value){
        db_update("users", array(
            $setting => $value
        ), array(
            "user_id" => $user
        ));
    }

	function system_getuserstats($stat){
		$_stat = db_escape($stat);
		if($_stat == "registered"){
			$query = db_select("user_id", "users");

			return db_numrows($query);
		}
	}

	function system_currentuser(){
		return $_SESSION['login'];
	}

	function system_password_salt($password, $username, $registrationdate, $email){
		$_username = hash("sha512", $username);
		$date = hash("sha512", date("Y-m-d H:i:s", strtotime($registrationdate . "+" . strlen($email) * strlen($password) . "days")));
		$date = date("Y-m-d H:i:s", strtotime($date . $registrationdate . "+" . strlen($email) * strlen($password) . "days"));
		$date = date("Y-m-d H:i:s", strtotime($date . "+" . strlen($username)*strlen($email)*1000 . "seconds"));
		$_email = hash("sha512", $email);
		return $final_hash = hash("sha512", $_username . $date . $password . INSTALL_SALT  . $_email);
	}

	function system_loginform(){
		include "themes/admin/header.php";
		$mvc = new Mvc('/../admin/loginform.qhtml', __FILE__);
		$mvc->get_all();
		include "themes/admin/footer.php";
	}

	function system_login_backend(){
		if(isset($_POST['login-username']) && isset($_POST['login-password']) && $_POST['login-username'] != "" && $_POST['login-password'] != ""){
			$result = system_login($_POST['login-username'], $_POST['login-password']);
			system_login_verify($result);
		}
		else{
			header("location: admin.php?loginform&loginfailed");
		}
	}

	function system_register($_username, $_password, $_email, $admin = "no"){
		$_date = date("Y-m-d H:i:s");
		$query = db_select("user_id", "users", array(
			"username" => $_username
		));
		if(db_numrows($query) == 0){
            if($admin == "no"){
			     $_password = system_password_salt($_password, $_username, $_date, $_email);
			     $default_role = system_getsetting("default_role");
			     db_insert("users", array(
			     	"username" => $_username,
			     	"password" => $_password,
			     	"email" => $_email,
			     	"registrationdate" => $_date,
			     	"role" => $default_role
			     ));
			     header("location: admin.php?loginform");
            }
            else{
                 $_password = system_password_salt($_password, $_username, $_date, $_email);
			     db_insert("users", array(
			     	"username" => $_username,
			     	"password" => $_password,
			     	"email" => $_email,
			     	"registrationdate" => $_date,
			     	"role" => 2
			     ));
			     header("location: admin.php?loginform");
            }
		}
		else{
			header("location: admin.php?loginform&registerfailed");
		}
	}

	function system_check($error = "You are not supposed to read this file. (300: access denied)"){
		if (session_status() != PHP_SESSION_ACTIVE) {
			die($error);
		}

	}

	function system_redirect($url){
		header("location: " . $url);
		?>
			<script>
				window.location = <?php echo $url;?>;
			</script>
		<?php
	}

?>
