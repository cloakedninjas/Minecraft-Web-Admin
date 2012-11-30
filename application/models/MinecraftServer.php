<?php

class Application_Model_MinecraftServer extends Application_Model_Base {

	const GAMEMODE_SURVIVAL = 0;
	const GAMEMODE_CREATIVE = 1;
	const GAMEMODE_ADVENTURE = 2;

	const TYPE_MINECRAFT = 1;
	const TYPE_BUKKIT = 2;

	const LEVEL_TYPE_DEFAULT = 'DEFAULT';
	const LEVEL_TYPE_FLAT = 'FLAT';
	const LEVEL_TYPE_LARGEBIOMES = 'LARGEBIOMES';

	const DIFFICULTY_PEACEFUL = 0;
	const DIFFICULTY_EASY = 1;
	const DIFFICULTY_NORMAL = 2;
	const DIFFICULTY_HARD = 3;

	const JAR_MINECRAFT = 'minecraft_server.jar';
	const JAR_BUKKIT = 'minecraft_server.jar';

	const CHAT_COLOR_WHITE = '�f';
	const CHAT_COLOR_RED = '�4';
	const CHAT_COLOR_NAVY = '�1';
	const CHAT_COLOR_YELLOW = '�e';

	protected $_table_name = 'servers';

	protected $rcon_client;
	protected $rcon_authed;

	protected $properties = array();
	protected $players_online;
	protected $players_max;

	public static $_level_type_list = array(
		self::LEVEL_TYPE_DEFAULT => 'Default',
		self::LEVEL_TYPE_FLAT => 'Superflat',
		self::LEVEL_TYPE_LARGEBIOMES => 'Large biomes'
	);

	public static $_difficulty_list = array(
		self::DIFFICULTY_PEACEFUL => 'Peaceful',
		self::DIFFICULTY_EASY => 'Easy',
		self::DIFFICULTY_NORMAL => 'Normal',
		self::DIFFICULTY_HARD => 'Hard'
	);

	public static $_game_mode_list = array(
		self::GAMEMODE_SURVIVAL => 'Survival',
		self::GAMEMODE_CREATIVE => 'Creative',
		self::GAMEMODE_ADVENTURE => 'Adventure'
	);

	public function __construct($id=null) {

		parent::__construct($id);

		if ($this->_loaded && $this->built) {
			//read properties file
			$this->readServerProperties();

			// probably dont want to do this on construct
			$this->rcon_client = new Application_Model_MinecraftRconClient($this->host, $this->rcon_port, $this->rcon_pass);

			if ($this->rcon_client->isRunning()) {
				// attempt auth
				if ($this->rcon_client->auth()) {
					$this->rcon_authed = true;
				}
			}
		}
	}

	public function create($name, $type, $host, $port, $local, $rcon_port, $store, $rcon_pass=null, $built=false) {
		$built = intval($built);

		$this->name = $name;
		$this->type = $type;
		$this->host = $host;
		$this->port = $port;
		$this->local = intval($local);
		$this->rcon_port = $rcon_port;

		if ($rcon_pass === null) {
			$this->rcon_pass = $this->generatePassword(16);
		}
		else {
			$this->rcon_pass = $rcon_pass;
		}
		$this->store = $store;
		$this->built = $built;
		$this->has_changes = 0;

		$this->save();

		// prevent future saves inserting new row
		$this->_loaded = true;
	}

	public function buildServer() {
		$config = Zend_Registry::get('config');

		echo "Creating server storage\n";

		// ensure server storage folder exists
		if (!is_dir($this->store)) {
			$result = mkdir($this->store, $config->minecraft->default->storage_perms, true);

			if ($result !== true) {
				throw new Exception('Unable to create server storage directory at :' . $this->store);
			}
		}

		switch ($this->type) {
			case self::TYPE_MINECRAFT:
				$url = $config->minecraft->jar->url;
				$filename = self::JAR_MINECRAFT;
				break;

			case self::TYPE_BUKKIT:
				$url = $config->craftbukkit->jar->url;
				$filename = self::JAR_BUKKIT;
				break;

			default:
				throw new Exception('Unknown server type');
				break;
		}

		$setup_vars = new Zend_Session_Namespace('setup_vars');
		$jar_dest = $setup_vars->jar_storage . DIRECTORY_SEPARATOR . $filename;

		echo "Downloading .jar from $url\n";

		function progressCallback($download_size, $downloaded_size, $upload_size, $uploaded_size) {
			static $previousProgress = 0;

			if ( $download_size == 0 ) {
				$progress = 0;
			}
			else {
				$progress = round( $downloaded_size * 100 / $download_size );
			}

			if ( $progress > $previousProgress) {
				$previousProgress = $progress;

				echo "#dlp#$progress";
			}
		}

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_NOPROGRESS, false);
		curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback');
		curl_setopt($ch, CURLOPT_FILE, fopen($jar_dest, 'w'));
		curl_exec($ch);
		curl_close($ch);

		echo "Downloaded complete\n";

		copy($jar_dest, $this->store . DIRECTORY_SEPARATOR . $filename);

		$this->built = 1;
		$this->save();

	}

	public function isRunning() {
		return $this->rcon_client->isRunning();
	}

	public function isAuthed() {
		return $this->rcon_authed;
	}

	public function getOnlinePlayers() {
		if ($this->rcon_authed) {
			$resp = $this->rcon_client->listPlayers();

			preg_match("/There are (\d)+\//", $resp, $matches);

			return $matches[1];
		}

		return 0;
	}

	public function getMaxPlayers() {
		return $this->properties['max-players'];
	}

	public function getPlayerList() {
		//$resp = $this->rcon_client->listPlayers();
		$resp = 'There are 2/20 players online:cloakedninjas, poofacupcake'; //, a, b, v, a, das, asd, asd
		//$resp = 'There are 0/20 players online:';

		$players = substr($resp, strpos($resp, ':') + 1);
		$players = explode(', ', $players);

		if (count($players) == 1 && $players[0] == '') {
			return false;
		}
		return $players;
	}

	public function getGamemode() {
		switch ($this->properties['gamemode']) {
			case self::GAMEMODE_SURVIVAL :
				return 'Survival';

			case self::GAMEMODE_CREATIVE :
				return 'Creative';

			case self::GAMEMODE_ADVENTURE :
				return 'Adventure';
		}
	}

	public function getProperty($field, $pretty=true) {
		if (isset($this->properties[$field])) {
			if ($this->properties[$field] == 'true') {
				if ($pretty) {
					return 'Yes';
				}
				else {
					return true;
				}
			}
			if ($this->properties[$field] == 'false') {
				if ($pretty) {
					return 'No';
				}
				else {
					return false;
				}
			}

			return $this->properties[$field];
		}
		else {
			throw new Exception('Unknown server property: ' . $field);
		}
	}

	protected function readServerProperties() {
		$file = $this->store . '/server.properties';

		if (is_readable($file)) {
			$text = file_get_contents($file);

			$lines = explode("\n", $text);

			foreach ($lines as $line) {
				if (substr($line, 0, 1) == '#' || $line == '') {
					continue;
				}

				list($key, $value) = explode('=', $line);

				$this->properties[$key] = trim($value);

			}
		}
		else {
			throw new Exception('Unable to read server.properties file at: ' . $file);
		}
	}

	protected function createServerProperties($new_props) {
		$prop_file = "#Minecraft server properties\n#" . date("D M d H:i:s e Y") . "\n";

		foreach ($this->properties as $field=>$value) {
			switch ($field) {
				case 'allow-nether':
				case 'enable-query':
				case 'allow-flight':
				case 'enable-rcon':
				case 'white-list':
				case 'spawn-npcs':
				case 'spawn-animals':
				case 'spawn-monsters':
				case 'generate-structures':
				case 'snooper-enabled':
				case 'hardcore':
				case 'online-mode':
				case 'pvp':
					if (isset($new_props[$field])) {
						// only true values can be sent through
						$this->properties[$field] = 'true';
					}
					else {
						$this->properties[$field] = 'false';
					}
					break;

				default:
					if (isset($new_props[$field])) {
						$this->properties[$field] = $new_props[$field];
					}
					break;
			}

			$prop_file .= $field . '=' . $this->properties[$field] . "\n";
		}

		return $prop_file;
	}

	protected function writeTempPropertiesFile($props) {
		$file = $this->store . '/new.server.properties';

		touch($file);

		if (is_writable($file)) {
			file_put_contents($file, $props);
		}
		else {
			throw new Exception('Unable to write to temporary server.properties file at: ' . $file);
		}
	}

	protected function writePropertiesFile($props) {
		$file = $this->store . '/server.properties';

		if (is_writable($file)) {
			// copy prev properties file as backup
			copy($file, $file . '.backup');

			file_put_contents($file, $props);
		}
		else {
			throw new Exception('Unable to write to server.properties file at: ' . $file);
		}
	}

	protected function applyConfigChanges() {
		$file = $this->store . '/new.server.properties';
		$props = file_get_contents($file);

		$this->writePropertiesFile($props);
		unlink($file);
	}

	protected function getJarName() {
		switch ($this->type) {
			case self::TYPE_MINECRAFT:
				return self::JAR_MINECRAFT;

			case self::TYPE_BUKKIT:
				return self::JAR_BUKKIT;

			default:
				throw new Exception('Unknown server type');
				break;
		}
	}

	public function generatePassword($len=16) {
		$pass = '';

		for($i = 0; $i < $len; $i++) {
			$pass .= chr(rand(97, 122));
		}

		return $pass;
	}

	public function start() {
		if (PHP_OS == 'Linux') {
			// TODO
			$cmd = "ls -l";
			$foo = exec($cmd, $output, $return);
			var_dump($foo);
		}
		else {
			$min_mem = "1024M";
			$max_mem = "2048M";
			$path_to_jar = realpath($this->store . DIRECTORY_SEPARATOR . $this->getJarName());

			chdir($this->store);

			$cmd = "java -Xms$min_mem -Xmx$max_mem -jar $path_to_jar";

			// fire + forget Java
			pclose(popen("start /B ". $cmd, "r"));
		}
	}

	public function stop($force) {
		if (!$force) {
			$configManager = new Application_Model_ConfigManager();
			$warning_time = intval($configManager->getValue('warning_time'));

			if ($warning_time > 0) {
				$counter = 0;

				$this->issueInitialShutdownMessage();

				while ($counter < $warning_time) {
					$this->issueShutdownMessage($warning_time - $counter);

					sleep(1);
					$counter++;
				}
			}
		}

		$this->rcon_client->stop();

		if ($this->has_changes) {
			$this->applyConfigChanges();

			$this->has_changes = 0;
			$this->save();
		}
	}

	protected function issueInitialShutdownMessage() {
		$this->rcon_client->say(self::CHAT_COLOR_YELLOW . 'An admin has requested to shutdown this server!');
	}

	protected function issueShutdownMessage($time) {
		$msg = self::CHAT_COLOR_RED . 'Server going offline in : ' . self::CHAT_COLOR_YELLOW;

		// only show message:
		// every 10 secs if > 30
		// every 5 if > 10
		// every 1 if <= 10

		$display = ($time > 30 && $time % 10 == 0) || ($time > 10 && $time % 5 == 0) || ($time <= 10);

		if ($display) {
			$this->rcon_client->say($msg . $time);
		}
	}

	public function validatePost($data) {
		$errors = array();

		if (!isset($data['name']) || $data['name'] == '') {
			$errors['name'] = 'Server name cannot be blank';
		}

		if (!isset($data['host']) || $data['host'] == '') {
			$errors['host'] = 'Server host cannot be blank';
		}

		if (!isset($data['port']) || $data['port'] == '') {
			$errors['port'] = 'Server port cannot be blank';
		}

		if (!isset($data['rconport']) || $data['rconport'] == '') {
			$errors['rconport'] = 'Server RCON port cannot be blank';
		}

		if (count($errors) == 0) {
			return true;
		}

		return $errors;
	}

	public function saveChanges($data) {
		if (isset($data['prop']) && is_array($data['prop'])) {
			foreach ($data['prop'] as $field=>$value) {
				if (!isset($this->properties[$field])) {
					throw new Exception('Attempting to set new / unknown sever setting: ' . $field);
				}
			}

			$new_prop = $this->createServerProperties($data['prop']);

			// if server is running, create temp properties file, else go ahead and edit file
			if ($this->isRunning()) {
				if (isset($data['save_apply'])) {
					$this->stop(true);
					// shutdown server and save file
					$this->writePropertiesFile($new_prop);
				}
				else {
					$this->has_changes = 1;
					$this->save();
					$this->writeTempPropertiesFile($new_prop);
				}
			}
			else {
				$this->writePropertiesFile($new_prop);
			}
		}

		return true;
	}

	public function issueWebCommand($params) {
		$return = array();

		if (!isset($params['cmd'])) {
			$return['error'] = 'Missing command';
		}
		else {
			if (!isset($params['player'])) {
				$return['error'] = 'Missing player';
			}
			else {
				switch ($params['cmd']) {
					case 'tell' :
						if (!isset($params['message'])) {
							$return['error'] = 'Missing message';
							break;
						}

						$resp = $this->rcon_client->tell($params['player'], $params['message']);
						break;

					case 'give':
						$amount = (isset($params['amount'])) ? $params['amount'] : 1;
						$dmg = (isset($params['dmg'])) ? $params['dmg'] : null;

						$resp = $this->rcon_client->give($params['player'], $params['item'], $amount, $dmg);
						break;

					case 'tp':
						if (isset($params['to_player'])) {
							$resp = $this->rcon_client->tp($params['player'], $params['to_player']);
						}
						else {
							$resp = $this->rcon_client->tp($params['player'], $params['x'], $params['y'], $params['z']);
						}
						break;

					case 'xp':
						$resp = $this->rcon_client->xp($params['xp'], $params['player']);
						break;

					case 'kill':
						$resp = $this->rcon_client->kill($params['player']);
						break;

					case 'op':
						$resp = $this->rcon_client->op($params['player']);
						break;

					case 'deop':
						$resp = $this->rcon_client->deOp($params['player']);
						if ($resp !== false) {
							$this->rcon_client->tell($params['player'], 'You have been de-opped. Sorry :(');
						}
						break;

					case 'whitelist':
						$resp = $this->rcon_client->whiteList($params['method'], $params['player']);
						break;

					case 'kick':
						$resp = $this->rcon_client->kick($params['player']);
						break;

					case 'ban':
						$resp = $this->rcon_client->ban($params['player']);
						break;
				}
			}
		}


		if ($resp !== false) {
			$return = true;
		}
		else {
			$return['error'] = 'An error occured trying to issue the ' . $params['cmd'] . ' command';
		}

		return $return;
	}
}

