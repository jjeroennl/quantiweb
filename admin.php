<?php
	include 'load.php';

	if(isset($_GET['loginform'])){
        	if(isset($_SESSION['login'])){
        	   header("Location: admin.php?p=3");
        	}
        	else{
		  system_loginform();
        	}
	}
	elseif(isset($_GET['login'])){
		system_login_backend();
	}
	elseif(isset($_GET['register'])){
		system_register($_POST['register-username'], $_POST['register-password'], $_POST['register-email']);
	}
	else{
		if(!isset($_SESSION['login'])){
			header("Location: admin.php?loginform");
			die('<a href="admin.php?loginform">Click here</a> if you didn\'t get redirected.');
		}
		else{
			if(isset($_GET['logout'])){
				unset($_SESSION['login']);
				header("Location: index.php");
			}
			if(isset($_GET['p'])){
				if($_GET['p'] == 1){
					admin_main();
				}
				else{
					admin_loadpage($_GET['p']);
				}
			}
			else{
				admin_main();
			}


		}

	}
?>
