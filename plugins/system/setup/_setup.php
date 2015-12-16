<?php
	function setup_nav_menu(){

		if(array_key_exists("db", $_SESSION) && isset($_GET['user_setup'])){
	  	  	$part = 2;
	  	}
	   	elseif(array_key_exists("db", $_SESSION) == 1 && isset($_GET['finish'])){
		  	$part = 3;
	   	}
		else{
			$part = 1;
		}

		echo '<li class="' . setup_is_menu($part, "db"). '"><a href="#"><i class="fa fa-table "></i></a></li>';
		echo '<li class="' . setup_is_menu($part, "usersetup"). '"><a href="#"><i class="fa fa-user"></i></a></li>';
		echo '<li class="' . setup_is_menu($part, "finish"). '"><a href="#"><i class="fa fa-check"></i></a></li>';
		echo '<li></li>';
	}

	function setup_is_menu($part, $item){
		if($item == "db" && $part == 1){
			return "active";
		}
		elseif($item == "usersetup" && $part == 2){
			return "active";
		}
		elseif($item == "finish" && $part == 3){
			return "active";
		}
	}
 ?>
