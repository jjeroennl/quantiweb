<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Quantiweb</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo INSTALL_LOCATION;?>/themes/admin/theme/style.css">
  <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="<?php echo INSTALL_LOCATION;?>themes/admin/ckeditor/ckeditor.js"></script>
</head>

<body>
	<?php if(!isset($_GET['loginform'])){
		?>
		<div class="navigation">
		  <div class="menu">
			<li><a> </a></li>
			<?php
			if(function_exists("admin_nav")){
				admin_nav();
			}
			else{
				if($pagename == "Setup"){
					setup_nav_menu();
				}
			}
			?>
		  </div>
		</div>
		<?php
	}?>

  <div class="container">
