<?php

class SetupController extends Zend_Controller_Action {

	const STATUS_LOCAL_RUNNING = 1;
	const STATUS_ONLY_EXTERNAL = 2;

    public function init() {
    	$this->config = new Application_Model_ConfigManager();

    	// prevent access to setup if it's already run
    	if ($this->config->isSetup()) {
    		$this->_redirect('/');
    	}

    	Zend_Layout::getMvcInstance()->assign('show_menu', false);
    }

    public function indexAction() {
    	// begin install

    	$step = $this->_getParam('step', 0);

		$setup_vars = new Zend_Session_Namespace('setup_vars');
		$view = $step;

		$config = Zend_Registry::get('config');

    	switch ($step) {
    		case 1: // push login credentials into session

    			$auth = new Application_Model_AuthManager();

    			$setup_vars->password_salt = $auth->createSalt();

    			$setup_vars->username = $this->_getParam('username');
    			$setup_vars->password = $auth->encryptPassword($this->_getParam('password1'), $setup_vars->password_salt);
    			break;

    		case 2: // choose minecraft server state

    			$setup_vars->status = $this->_getParam('status');

    			if ($this->_getParam('status') == self::STATUS_LOCAL_RUNNING) {
    				$view = $step . '-local';

    				$this->view->defaults = $config->mwa->default->toArray();
    				$this->view->mc_defaults = $config->minecraft->default->toArray();

    				// prep the storage path
    				$this->view->defaults['server_storage'] =
    					realpath($this->view->defaults['data_path']) . DIRECTORY_SEPARATOR .
    					$config->mwa->default->storage_folder . DIRECTORY_SEPARATOR .
    					$config->mwa->default->servername;

    				$this->view->defaults['jar_storage'] =
    				realpath($this->view->defaults['data_path']) . DIRECTORY_SEPARATOR .
    				$config->mwa->default->jar_folder;
    			}
    			else {
    				$view = $step . '-remote';
    			}

    			break;

    		case 3: // save config

    			if ($setup_vars->status == self::STATUS_LOCAL_RUNNING) {
    				//type
    				$setup_vars->server_name = $this->_getParam('name');
    				$setup_vars->server_type = $this->_getParam('type');
    				$setup_vars->server_host = $this->_getParam('host');
    				$setup_vars->server_port = $this->_getParam('port');
    				$setup_vars->server_rcon_port = $this->_getParam('rcon_port');
    				$setup_vars->jar_storage = $this->_getParam('jar_storage');
    				$setup_vars->server_storage = $this->_getParam('server_storage');
    			}

    			break;

    		case 4: // build server via ajax
    			// turn off all output buffering to stream output

    			//header('Content-type: application/octet-stream');

				ini_set('output_buffering', 'off');
				ini_set('zlib.output_compression', false);
				ini_set('implicit_flush', true);
				set_time_limit(300);
				ob_implicit_flush(true);

				// Clear, and turn off output buffering
				while (ob_get_level() > 0) {
				    $level = ob_get_level();
				    ob_end_clean();

				    if (ob_get_level() == $level) break;
				}

				echo "Starting build\n";

    			try {
    				echo "Saving config\n";
    				$status = $this->config->setupConfig();

    				if ($status) {
    					echo "Config saved\n";

    					if ($setup_vars->status == SetupController::STATUS_LOCAL_RUNNING) {
							$server = new Application_Model_MinecraftServer();
							$server->create(
								$setup_vars->server_name,
								$setup_vars->server_type,
								$setup_vars->server_host,
								$setup_vars->server_port,
								true,
								$setup_vars->server_rcon_port,
								$setup_vars->server_storage);

							echo "Building server\n";
							$server->buildServer();

							echo "Build Complete\n";

						}

    					echo "Checking Java\n";

	    				$cmd = "java -version 2>&1";
						exec($cmd, $output, $return);

						if ($return == 0) {
							if (isset($output[0]) && $output[0] != '') {
								$version = str_replace(array('java version ', '"'), '', $output[0]);
								$versions = explode('.', $version);

								echo "Found Java\n";

								if (count($versions) >= 2) {
									if ($versions[0] >= 1 && $versions[1] >= 6) {
										echo "Checked Java meets version requirement: $version >= 1.6\n";

										$updates = explode('_', $versions[2]);

										if ($versions[1] == 6 && intval($updates[1]) < 32) {
											echo 'Mojang recommends update 32 for Java SE 6. You currently have: ' . $updates[1] . "\n";
										}
										elseif ($versions[1] == 7 && intval($updates[1]) < 4) {
											echo 'Mojang recommends update 4 for Java SE 7. You currently have: ' . $updates[1] . "\n";
										}
									}
									else {
										throw new Exception('Java does not appear to meet version requirement: ' . $version . ' < 1.6', Application_Model_ErrorManager::JAVA_VERSION);
									}
								}
								else {
									throw new Exception('Unsupported version of Java: ' . $version, Application_Model_ErrorManager::JAVA_VERSION);
								}
							}
						}
						else {
							throw new Exception('Could not start Java', Application_Model_ErrorManager::JAVA_NOT_FOUND);
						}

						echo "Creating server.properties\n";

						// create starting server files
						$data = file_get_contents(APPLICATION_PATH . '/../data/server.properties.default');

						$data = str_replace("###RCON_PASS###", $server->rcon_pass, $data);
						$data = str_replace("###RCON_PORT###", $server->rcon_port, $data);

						file_put_contents($server->store . DIRECTORY_SEPARATOR . 'server.properties', $data);

						echo "Creating whitelists\n";

						touch($server->store . DIRECTORY_SEPARATOR . 'white-list.txt');
						touch($server->store . DIRECTORY_SEPARATOR . 'ops.txt');

						$this->config->flagSetupRun();

						echo "Build complete!\n";

						echo "Logging you in...\n";

						$auth = new Application_Model_AuthManager();
						$auth->setAuthed();

						echo "#complete#";

    				}
    			}
    			catch (Exception $e) {
    				echo "Error: " . $e->getMessage() . "\n";
    			}
    			exit;

    	}

    	$this->renderScript('setup/index-' . $view . '.phtml');

    }


}

