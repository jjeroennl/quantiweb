<?php
	system_check();
	include("themes/admin/header.php");
?>
		<div class="section section-breadcrumbs">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1>Login</h1>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="basic-login">
						<form role="form" action="admin.php?login" method="post">
							<?php
								if(isset($_GET['loginfailed'])){
									echo '<div class="alert alert-danger" role="alert"><strong>Oh no!</strong> Something went wrong, please try again.</div>';
								}
							
							?>
							Username<br>
							<input class="form-control" name="login-username" id="login-username" placeholder="" type="text">			
							Password<br>			
		       				 <input class="form-control" name="login-password" id="login-password" placeholder="" type="password">
							<button type="submit" class="btn pull-right">Login</button>
							
							
						</form>
							<?php
								if(system_getsetting("allow_registration") == "1"){ 
									echo '<a data-toggle="modal" data-target="#registermodal" class="register-link">Register a account</a>';
									echo '
											<div class="modal fade" id="registermodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<form action="admin.php?register" method="post">
													<div class="modal-dialog">
														<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
															<h4 class="modal-title" id="myModalLabel"><i class="fa fa-user"></i> Register</h4>
														</div>
														<div class="modal-body">
															
																<input class="form-control" name="register-username" type="text" placeholder="Username" requied><br>
																<input class="form-control" name="register-password" type="password" placeholder="Password" requied><br>
																<input class="form-control" name="register-email" type="email" placeholder="Email" requied>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn" data-dismiss="modal">Cancel</button>
															<button type="submit" class="btn btn-default">Register</button>
															
														</div>
														</div>
													</div>
												</form>
											</div>
									';
								}
							?>
							<div class="clearfix"></div>

					</div>
				</div>
			</div>
		</div>
<?php include("themes/admin/footer.php"); ?>
