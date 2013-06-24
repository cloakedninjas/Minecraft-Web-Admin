<?php

class Application_Model_ConfigManager {

	protected $is_setup = false;
	protected $path = null;

	/**
	 *
	 * @var Zend_Db
	 */
	protected $db;


	public function __construct() {
		$config = Zend_Registry::get('config');

		$this->db = Zend_Registry::get('db');
		$this->path = realpath($config->mwa->db->path) . DIRECTORY_SEPARATOR . $config->mwa->db->name;

		if (!file_exists($this->path)) {
			$this->is_setup = false;
			return;
		}

		try {
			$result = $this->db->fetchAll("SELECT setup_run FROM config");

			if (count($result) == 1) {
				$this->is_setup = (bool) $result[0]->setup_run;
			}
		}
		catch (Exception $e) {
			throw new Exception('Failed to read database at ' . $this->path, Application_Model_ErrorManager::DB_EMPTY);
		}

		if ($this->is_setup) {
			$this->values = $this->db->fetchRow('SELECT * FROM config');
		}
	}

	public function isSetup() {
		return $this->is_setup;
	}

	public function setupConfig() {
		$setup_vars = new Zend_Session_Namespace('setup_vars');

		// check if file is writeable
		$success = file_put_contents($this->path, '');

		if ($success !== false) {
			$config = Zend_Registry::get('config');

			echo "Creating database\n";

			$queries = explode(";", file_get_contents($config->mwa->db->init_sql));
			foreach ($queries as $query) {
				$this->db->query($query);
			}

			$this->createUser($setup_vars->username, $setup_vars->password, $setup_vars->password_salt);

			$this->setJarPath($setup_vars->jar_storage);


			return true;
		}
		else {
			throw new Exception('Unable to create database, please check file permissions for: ' . $this->path, Application_Model_ErrorManager::DB_CREATE);
		}
	}

	public function createUser($username, $password, $salt) {
		$this->db->query('INSERT INTO users ("username", "password", "salt", "last_login") VALUES (?, ?, ?, 0)', array(
			$username,
			$password,
			$salt
		));
	}

	public function setJarPath($path) {
		$this->db->query('UPDATE config SET jar_storage = ?', array(
			$path
		));
	}

	public function flagSetupRun() {
		$this->db->query('UPDATE config SET setup_run = 1');
	}

	public function getValue($field) {
		if (isset($this->values->$field)) {
			return $this->values->$field;
		}
		else {
			return false;
		}
	}
}

