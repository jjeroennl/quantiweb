<?php
	system_check();
	include("themes/admin/header.php");
?>
		<div class="section section-breadcrumbs">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1>Dash</h1>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-sm-6">
	        		<div class="service-wrapper">
		        		<i class="fa  fa-tachometer fa-5x"></i>
		        		<h3>Welcome!</h3>
		        		<p>Welcome to the dash! You can see here what happened to your website when you left.</p><br>
		        		<a href="<?php echo INSTALL_LOCATION;?>" class="btn">Show your site</a>
		        	</div>
	        	</div>
	        	<div class="col-md-4 col-sm-6">
	        		<div class="service-wrapper">
		        		<i class="fa  fa-users fa-5x"></i>
		        		<h3>Users</h3>
		        		<?php 
							$registered_users = system_getuserstats("registered"); 
							if($registered_users == 1){
								$users = " user ";
								$isare = "is";
							}
							else{
								$users = " users ";
								$isare = "are";
							}
						?>
		        		<p>There <?php echo $isare;?> currently <?php echo $registered_users . $users ;?> registered to your website.</p>
		        		<a href="<?php echo INSTALL_LOCATION;?>" class="btn">Manage users</a>
		        	</div>
	        	</div>
	        	<div class="col-md-4 col-sm-6">
	        		<div class="service-wrapper">
		        		<i class="fa fa-pencil fa-5x"></i>
		        		<h3>Content</h3>
		        		<?php 
							$content = content_query(00, "loop");
							$num = db_numrows($content);
							if($num == 1){
								$posts = " post ";
								$isare = "is";
							}
							else{
								$posts = " posts ";
								$isare = "are";
							}
						?>
		        		<p>There <?php echo $isare;?> currently <?php echo $num . $posts ;?> on your website.</p>
		        		
		        	</div>
	        	</div>
			</div>
			
		</div>
<?php include("themes/admin/footer.php"); ?>
