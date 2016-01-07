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
		$query = new Select("system_settings");
		$query->where("setting", $setting);
		$query->execute();

		foreach($query->fetch() as $row){
			return $row['value'];
		}
	}

    function _system_removeuser($userid){
        if(system_getuserinfo(system_currentuser(), "role") == 2){
            $delete = new Delete("users");
			$delete->where("user_id", $userid);
			$delete->execute();
        }
    }

	function system_setsetting($setting, $value){
		$update = new Update("system_settings");
		$update->where("setting", $setting);
		$update->update("value", $value);
		$update->execute();
	}

	function system_newsetting($setting, $defaultvalue){
		$insert = new Insert("system_settings");
		$insert->insert("value", $defaultvalue);
		$insert->execute();
	}

	function system_login($_username, $_password){
		$_password = system_password_salt($_password, $_username, system_getuserinfo($_username, "registrationdate"), system_getuserinfo($_username, "email"));

		$query = new Select("users");
		$query->select("user_id");
		$query->where("username", $_username);
		$query->where("password", $_password);
		$query->execute();

		if($query->numrows() == 1){
			foreach($query->fetch() as $row){
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
			if($info != "password"){
				$query = new Select("users");
				$query->select($info);
				$query->where("username", $username);
				$query->execute();

				foreach($query->fetch() as $row){
					$result = $row[$info];
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
			if($info != "password"){
				$query = new Select("users");
				$query->select($info);
				$query->where("user_id", $username);
				$query->execute();

				foreach($query->fetch() as $row){
					$result =  $row[$info];
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
		$query = new Select("users");
		$query->select("username");
		$query->select("user_id");
		$query->execute();

		return $query;
	}

    function system_setuserinfo($user, $setting, $value){
        $update = new Update("users");
		$update->update($setting, $value);
		$update->where("userid", $user);
		$update->execute();
    }

	function system_getuserstats($stat){
		if($stat == "registered"){
			$query = new Select("users");
			$query->execute();

			return $query->numrows();
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
		$query = new Select("users");
		$query->select("user_id");
		$query->where("username", $_username);

		if($query->numrows() == 0){
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

			     new Insert("users");
				 $insert->insert("username", $_username);
				 $insert->insert("password", $_password);
				 $insert->insert("email", $_email);
				 $insert->insert("date", $_date);
				 $insert->insert("role", 2);
				 $insert->execute();

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
