<?php
	if(!isset($send)){
		die("You are not supposed to read this file. (300: access denied)");
	}

	
	include("themes/admin/header.php");
	
	?>
		<div class="section section-breadcrumbs">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1><?php echo $pagename;?></h1>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="service-wrapper">
						<?php
							if(isset($function)){
								$function();
							}
						?>
					</div>
				</div>
			</div>
		</div>
	<?php
	include("themes/admin/footer.php");
	?>

