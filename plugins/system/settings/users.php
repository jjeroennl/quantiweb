<?php
    system_check();
    if(isset($_GET['remuser'])){
        _remove_user();
    }
    function _remove_user(){
        if(system_getuserinfo(system_currentuser(), "role") == 2){
            if(isset($_GET['remuser'])){
                _system_removeuser($_GET['remuser']);
            }
        }
    }

    function _edit_userinfo(){
        system_setuserinfo($_POST['userid'], "email", $_POST['email']);
        if($_POST['role'] == "Contributor"){
             system_setuserinfo($_POST['userid'], "role", 3);
        }
        if($_POST['role'] == "Writer"){
             system_setuserinfo($_POST['userid'], "role", 1);
        }
        if($_POST['role'] == "Subscriber"){
             system_setuserinfo($_POST['userid'], "role", 0);
        }
        if($_POST['role'] == "Admin"){
             system_setuserinfo($_POST['userid'], "role", 2);
        }
       
    }
    if(isset($_GET['editinfo'])){
        _edit_userinfo();
    }
    function remove_user($username, $id){
        echo '
					<div class="modal fade" style="text-align: left;"id="removeusermodal' . $id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    ';
					echo '
						<div class="modal-dialog">
							<div class="modal-content">
							<div class="modal-header alert-danger">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel"><i class="fa fa-user"></i> Remove user</h4>
							</div>
							<div class="modal-body">
								Are you sure you want to remove ' . $username . '? This action can not be undone!
							</div>
							<div class="modal-footer alert-danger">
                                <a class="btn btn-red" href="?p=3&remuser=' . $id . '" >Remove</a>
								<button type="button" data-dismiss="modal" class="btn">Cancel</button>
								

							</div>
							</div>
						</div>
					</div>';
    }

    function editinfo($username, $email, $role, $id){
        if($role == 0){
            $textrole = "Subscriber";
        }
        elseif($role == 1){
            $textrole = "Writer";
        }
        elseif($role == 2){
            $textrole = "Admin";
        }
        elseif($role == 3){
            $textrole = "Contributor";
        }
        echo '
					<div class="modal fade" style="text-align: left;"id="editusermodal' . $id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    ';
					echo '
						<div class="modal-dialog">
							<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel"><i class="fa fa-user"></i> Edit user information</h4>
							</div>
							<div class="modal-body">
								<form action="admin.php?p=3&editinfo" method="POST">
                                    <input type="text" class="form-control" value="' . $username. '"disabled>
                                    <input style="margin-top: 5px; margin-bottom: 5px;" type="text" class="form-control" value="' . $email . '" name="email">
Default userrole:
    <select name="role" class="form-control" style="width: 200px; display: inline;">
        <option>' . $textrole .'</option>
        <option>Admin</option>
        <option>Contributor</option>
        <option>Writer</option>
        <option>Subscriber</option>
    </select>
    <input type="hidden" value="' . $id . '" name="userid">
                               
							</div>
							<div class="modal-footer">
                                
								<button type="button" data-dismiss="modal" class="btn">Cancel</button>
								<button class="btn btn-default" type="submit">Save</button>
                                 </form>
                                

							</div>
							</div>
						</div>
					</div>';
    }

    function registermodal(){
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
    
    echo '<table class="events-list"><tbody>';
    registermodal();
    echo '<a class="btn btn-default" data-toggle="modal" data-target="#registermodal">Add a user</a>';
    $users = db_select("username, email, user_id, role, registrationdate", "users");
    while($row = db_grab($users)){

?>
						<tr>
							<td style="width: 112px;">
								<div class="event-date">
									<div class="event-day"><?php echo $row['username'];?></div>

									<div style=""class="event-month"></div>
								</div>

							</td>
							<td>
								<?php echo $row['email'];?>
							</td>
                            <td>
								<?php echo $row['user_id'];?>
							</td>
						

                            <td>
                                <?php
                                    if($row['role'] != 2){
                                        remove_user($row['username'], $row['user_id']);
                                        echo '<a href="#" data-toggle="modal" data-target="#removeusermodal' . $row['user_id'] . '" class="btn btn-red btn-sm"><i class="fa  fa-trash-o"></i></a> ';
                                    }  
                                ?>
                               <?php
                                    
                                        editinfo($row['username'],  $row['email'], $row['role'], $row['user_id']);
                                        echo '<a href="#" data-toggle="modal" data-target="#editusermodal' . $row['user_id'] . '" class="btn btn-default btn-sm"><i class="fa  fa-pencil"></i></a> ';
                                  
                                     
                                ?>
                            </td>
						
						</tr>

<?php
    }
    echo '</table>';
?>