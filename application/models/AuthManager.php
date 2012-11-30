<?php

class Application_Model_AuthManager {

	protected $session = false;
	protected $session_namespace = 'MWA_Auth';

	public function __construct() {
		$this->session = new Zend_Session_Namespace($this->session_namespace);
	}

	public function isAuthed() {
		return $this->session->authed;
	}

	public function authenticate($username, $password) {
		$db = Zend_Registry::get('db');
		$row = $db->fetchRow('SELECT * FROM users WHERE username = ?', $username);

		if ($row !== false) {
			if ($row->password == $this->encryptPassword($password, $row->salt)) {
				$this->setAuthed(true, $row);
				return true;
			}
		}
		return false;
	}

	public function setAuthed($authed=true, $user=null) {
		$this->session->authed = $authed;
	}

	public function createSalt() {
		$len = 8;
		$salt = '';

		for($i = 0; $i < $len; $i++) {
			$salt .= chr(rand(97, 122));
		}

		return $salt;
	}

	public function encryptPassword($password, $salt) {
		return hash('sha256', $password . $salt);
	}


}

