<?php
	function activatemodal($plugin){
		if(plugin_check_mate($plugin)){
			echo '
					<div class="modal fade" style="text-align: left;"id="activatemodal_' . $plugin . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

					';
					$div = "";
					if(_plugin_score($plugin) >= 6){
						$div = "alert-success";
					}
					if(_plugin_score($plugin) >= 2 && _plugin_score($plugin) <= 5){
						$div = "alert-warning";
					}
					if(_plugin_score($plugin) <=4){
						$div = "alert-danger";
					}
					if(_plugin_score($plugin)  == 10 || _plugin_score($plugin)  == 9){
						$div = "alert-info";
					}
					echo '
						<div class="modal-dialog">
							<div class="modal-content">
							<div class="modal-header ' . $div . '">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel"><i class="fa fa-puzzle-piece"></i> Activate ' . ucfirst($plugin) . '</h4>
							</div>
							<div class="modal-body">
							<div class="col-sm-9">
								This plugin requires access to the following items:

							';
							plugin_scan($plugin);
							echo '
								</div>
								<div class="col-sm-3">
								<h1 style="font-size: 70px;">
							';
							echo _plugin_score($plugin);
							echo '</h1>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="modal-footer ' . $div . '">
								<button type="button" class="btn btn" data-dismiss="modal">Cancel</button>
								<a href="admin.php?p=' . db_escape($_GET['p']) . '&activate=' . $plugin . '" class="btn btn-default">Activate</a>

							</div>
							</div>
						</div>
					</div>
			';
		}
		else{
			$div = "alert-danger";
			echo '
				<div class="modal fade" style="text-align: left;"id="activatemodal_' . $plugin . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
							<div class="modal-content">
							<div class="modal-header ' . $div . '">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel"><i class="fa fa-puzzle-piece"></i> Activate ' . ucfirst($plugin) . '</h4>
							</div>
							<div class="modal-body">
								This plugin is blocked. It tries to make a custom database connection, which enables it to bypass the ' . OFFICIAL_NAME . ' security.
								<div class="clearfix"></div>
							</div>
							<div class="modal-footer ' . $div . '">


							</div>
							</div>
						</div>
					</div>
			';
		}
	}

	if(!isset($send)){
		die("You are not supposed to read this file. (300: access denied)");
	}

	if(isset($_GET['activate'])){
		system_setsetting("plugins", system_getsetting("plugins") . "," . db_escape($_GET['activate']));
		include 'plugins/' . db_escape($_GET['activate']) . '/index.php';
		$function = db_escape($_GET['activate']) . "_install";

		if(function_exists($function)){
			$function();
		}
		header("Location: admin.php?p=" . db_escape($_GET['p']));
	}

	if(isset($_GET['disable'])){
		system_setsetting("plugins", str_replace(db_escape(",". $_GET['disable']), "", system_getsetting("plugins")));
		header("Location: admin.php?p=". db_escape($_GET['p']));
	}

	$plugins = system_getplugins();
	$system_plugins = array();

	$installed_plugins = array();
	$not_active_plugins = array();

	foreach($plugins as $plugin){
		if(substr($plugin,0,1) == "!"){
			array_push($system_plugins, substr($plugin,1));
		}
		else{
			array_push($system_plugins, $plugin);
		}
	}

	$files = scandir("plugins");
	foreach($files as $file){
		if($file != "." && $file != ".."  && $file != "system"){

			if(!in_array($file, $plugins)){
				array_push($not_active_plugins, $file);
				array_push($installed_plugins, $file);
			}
			else{
				array_push($installed_plugins, $file);

			}

		}
	}

?>
<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#n_plugins" aria-controls="home" role="tab" data-toggle="tab">Normal plugins</a></li>
    <li role="presentation"><a href="#s_plugins" aria-controls="profile" role="tab" data-toggle="tab">System plugins</a></li>

  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="n_plugins">
		<br>


		<?php
			foreach($installed_plugins as $installed_plugin){

					$url = 'plugins/' . $installed_plugin . '/info.txt';
					if($fh = fopen($url,"r")){
						while (!feof($fh)){
							$F1[] = fgets($fh,9999);

						}
						fclose($fh);
					}



					$description = str_replace("DESC: ", "", $F1[0]);
					$author = str_replace("AUTHOR: ", "", $F1[1]);
					$website = str_replace("WEBSITE: ", "", $F1[2]);

					unset($F1);

					$image = 'plugins/' . $installed_plugin . '/image.png';
					if(file_exists($image)){
						$image = '<img height="152px" src="plugins/' . $installed_plugin . '/image.png" alt="' . $installed_plugin . '">';
					}
					else{
						$image = "";
					}

					echo '
							<div class="col-sm-6">
								<div class="portfolio-item">
									<div class="portfolio-image">
										' . $image . '
									</div>
									<div class="portfolio-info">
										<ul>
											<li class="portfolio-project-name">' . $installed_plugin .'</li>
											<li>' . $description . '</li>
											<li>Author: <a href="' . $website . '">' . $author . '</a></li>';
											if(in_array($installed_plugin, $not_active_plugins)){
												echo'<li class="read-more"><a data-toggle="modal" data-target="#activatemodal_' . $installed_plugin . '" class="btn">Activate</a></li>';


											}
											else{
												echo'<li class="read-more"><a href="admin.php?p=' . db_escape($_GET['p']) . '&disable=' . $installed_plugin . '" class="btn">Disable</a></li>';
											}
											echo '
										</ul>
									</div>
								</div>
							</div>

					';

				$author = "";
				activatemodal($installed_plugin);
				unset($description);
				unset($author);
				unset($website);
			}
		?>
    </div>

    <div role="tabpanel" class="tab-pane" id="s_plugins"><br>
		<?php
			foreach($system_plugins as $installed_plugin){
					$author = "SYSTEM";
					$description= "Part of system";
					$website = "http://" . OFFICIAL_WEBSITE;

							if(!in_array($installed_plugin, $installed_plugins)){
							echo '
							<div class="col-sm-6">
										<div class="portfolio-item">

											<div class="portfolio-info">
												<ul>
													<li class="portfolio-project-name">' . $installed_plugin .'</li>
													<li>' . $description . '</li>
													<li>Author: <a href="' . $website . '">' . $author . '</a></li>';
													if(in_array($installed_plugin, $not_active_plugins)){
														echo'<li class="read-more"><a data-toggle="modal" data-target="#activatemodal" class="btn">Activate</a></li>';
														activatemodal($installed_plugin);
													}
													else{
													
													}
													echo '
												</ul>
											</div>
										</div>
									</div>
							';
						}
			}
		?>
    </div>
	<div class="clearfix"></div>
  </div>

</div>
