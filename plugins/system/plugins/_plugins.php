<?php
    function plugin_install($plugin, $function)
    {
        $installed_plugins = system_getplugins();

        if (!in_array($plugin, $installed_plugins)) {
            $curplugin = system_getsetting('plugins');
            if ($curplugin != '') {
                $newplugin = $curplugin.','.$plugin;
            } else {
                $newplugin = $plugin;
            }

            db_update('system_settings', array('value' => $newplugin), array('setting' => 'plugins'));
            $function();
        }
    }

    function plugins_load()
    {
		$allplugins = new Select("system_settings");
		$allplugins->select("value");
		$allplugins->where("setting", "plugins");
		$allplugins->execute();

        foreach($allplugins->fetch() as $row){
            $plugins = $row['value'];
        }

        $plugin_array = explode(',', $plugins);

        foreach ($plugin_array as $plugin) {
            if (substr($plugin, 0, 1) == '!') {
                $plugin = substr($plugin, 1);
                $plugin = 'plugins/system/'.$plugin.'/_'.$plugin.'.php';
                if (file_exists($plugin)) {
                    include $plugin;
                }
            } else {
                $plugin = 'plugins/'.$plugin.'/index.php';
                if (file_exists($plugin)) {
                    include $plugin;
                }
            }
        }
    }

    function plugins_config(){
        $send = 1;

        $installedPlugins = system_getplugins();
        $plugins = scanFolderForPlugins();

        $mvc = new Mvc('adminpage.qhtml', __FILE__);
        foreach ($plugins as $plugin) {
            $url = 'plugins/'.$plugin.'/info.txt';
            if ($filehandler = fopen($url, 'r')) {
                while (!feof($filehandler)) {
                    $fileContent[] = fgets($filehandler, 9999);
                }
                fclose($filehandler);
            }

            $plugindata = array();
            $plugindata['name'] = $plugin;
            $plugindata['description'] = str_replace('DESC: ', '', $fileContent[0]);
            $plugindata['author'] = str_replace('AUTHOR: ', '', $fileContent[1]);
            $plugindata['website'] = str_replace('WEBSITE: ', '', $fileContent[2]);

            if (in_array($plugin, $installedPlugins)) {
                $plugindata['installed'] = 1;
            }
			else{
				$plugindata['installed'] = 0;
			}

            $mvc->_('#panelarea')->append(new Panel('small', $plugindata, 'plugins'));

			if($plugindata['installed'] != 1){
				$mvc->_("#modelarea_" . $plugindata['name'])->append(new Modal("activate" . $plugindata['name'], ucfirst($plugindata['name']),plugins_activateInfo($plugindata['name']), '<a class="button" id="activatelink' . $plugindata['name'] .'">Activate</a>'));
				$mvc->_("#activatelink" . $plugindata['name'])->set_attribute("href", '?p=' . $_GET['p'] .'&activate=' .  $plugindata['name']);
			}
			else{
				$mvc->_("#modelarea_" . $plugindata['name'])->append(new Modal("disable" . $plugindata['name'], ucfirst($plugindata['name']), "Are you sure you want do disable " . ucfirst($plugindata['name']) . "?", '<a class="button" id="disablelink' . $plugindata['name'] .'">Disable</a>'));
				$mvc->_("#disablelink" . $plugindata['name'])->set_attribute("href", '?p=' . $_GET['p'] .'&disable=' .  $plugindata['name']);
			}


            unset($plugindata);
            unset($filehandler);
            unset($fileContent);
        }
		$mvc->add_controller("plugin_controller");
        $mvc->get_all();
    }

	function plugin_controller($data){
		if(isset($data['activate'])){
			system_setsetting("plugins", system_getsetting("plugins") . "," . $data['activate']);
			include 'plugins/' .$data['activate'] . '/index.php';
			$function = $data['activate'] . "_install";

			if(function_exists($function)){
				$function();
			}
			header("Location: admin.php?p=" . strip_tags($_GET['p']));
		}
		if(isset($data['disable'])){
			system_setsetting("plugins", str_replace(strip_tags(",". $data['disable']), "", system_getsetting("plugins")));
			header("Location: admin.php?p=". strip_tags($data['p']));
		}
	}

	function plugins_activateInfo($plugin){
		$output = array();
		$output['large'] = "This plugin wants to: <ul>";
		if(plugin_checkmate($plugin)){
			foreach(plugin_check($plugin) as $bad){
				$output['large'].= "<li>" .plugin_explain($bad) . "</li>";
			}
		}
		$output['large'] .= "</ul>";
		$output['small'] = "<h1 class=\"pluginmark\">" . plugin_check($plugin,1) . "</h1>";
		return $output;
	}

    function scanFolderForPlugins(){
        $files = scandir('plugins');
        $installedPlugins = array();
        foreach ($files as $file) {
            if (substr($file, 1) != '.' && $file != '.' && $file != '..' && $file != 'system') {
                $installedPlugins[] = $file;
            }
        }

        return $installedPlugins;
    }

    function plugin_check($plugin){
        $code = _plugin_get($plugin);
        $totalscore = 10;
        $containsbad = array();

        $bad_words = array(
                'db' => '1',
                'system_' => '0',
                'db_delete' => '6',
                'content_' => '1',
                'exec(' => '7',
                'passthru' => '7',
                'shell_exec' => '7',
                'popen' => '5',
                'assert' => '5',
                'eval' => '5',
                'fopen' => '5',
                'tmpfile' => '5',
                'move_uploaded_file' => '5',
            );

        foreach ($bad_words as $badword => $score) {
            if (strpos($code, $badword) !== false) {
                array_push($containsbad, $badword);
                $totalscore -= $score;
            }
        }

        if ($totalscore <= 0) {
            $totalscore = 0;
        }

        if (func_num_args() == 2) {
            return $totalscore;
        } else {
            return $containsbad;
        }
    }

    function plugin_explain($badword){
        $explain = array(
            'db' => 'Read and write from/to your database system',
            'system_' => 'Use system functions',
            'db_delete' => 'Delete entries in your database system',
            'content_' => 'Read, write or delete content',
            'exec(' => 'Execute commands on your server',
            'passthru' => 'Execute commands on your server without verification',
            'popen' => 'Read and write files',
            'assert' => 'Posibly stop quantiweb from working',
            'eval' => 'Execute commands on your website',
            'fopen' => 'Read and write files',
            'tmpfile' => 'Read and write temporary files',
            'move_uploaded_file' => 'Move temporary files',
        );

        return $explain[$badword];
    }

    function _plugin_get($plugin){
        $folderstoscan = plugin_scanfolder($plugin);
        $code = '';
        foreach ($folderstoscan as $folder) {
            $urls = scandir($folder);

            foreach ($urls as $urlx) {
                $url = $folder.'/'.$urlx;
                if (substr($url, -4) == '.php' || substr($url, -4) == '.PHP') {
                    if (file_exists($url)) {
                        $myfile = fopen($url, 'r');
                        $code = $code.fread($myfile, filesize($url));
                        fclose($myfile);
                    }
                }
            }
        }
        $code = str_replace('<?php', '', $code);
        $code = str_replace('?>', '', $code);

        return $code;
    }

    function plugin_scanfolder($plugin){
        $folders = array('plugins/'.$plugin);

        $badloop = 1;

        while ($badloop != 0) {
            if (isset($folders[$badloop - 1])) {
                $scan = scandir($folders[$badloop - 1 ]);
                foreach ($scan as $results) {
                    if ($results != '.' && $results != '..') {
                        if (is_dir($folders[$badloop - 1 ].'/'.$results)) {
                            array_push($folders, $folders[$badloop - 1 ].'/'.$results);
                        }
                    }
                }
                ++$badloop;
            } else {
                $badloop = 0;
            }
        }

        return $folders;
    }

    function plugin_recursivefolder($folders){
    }

    function plugin_checkmate($plugin){
        $code = _plugin_get($plugin);

        if (strpos($code, 'mysqli_connect') || strpos($code, 'mysql_connect') || strpos($code, 'mysqli_real_connect') || strpos($code, 'new pdo')) {
            return false;
        } else {
            return true;
        }
    }
