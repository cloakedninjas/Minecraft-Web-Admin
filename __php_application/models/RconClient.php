<?php

/**
 * Based on the code from <a href="http://fremnet.net/article/199/source-rcon-class">fremnet.net/article/199/source-rcon-class</a><br />
 * Tweaked to suit Minecraft RCON
 *
 * @author Daniel Jackson
 * @link www.cloakedninjas.co.uk
 */

abstract class Application_Model_RconClient {

	const SERVERDATA_EXECCOMMAND = 2;
	const SERVERDATA_AUTH = 3;

	const ERR_UNABLE_TO_AUTH = 1;

	protected $config = array(
		'host' => '',
		'port' => 0,
		'password' => ''
	);

	protected $socket = null;
	protected $id = 0;
	protected $last_error = array(
		'msg' => '',
		'num' => 0
	);


	public function __construct($host, $port, $password='', $timeout=2) {
		$this->config['host'] = $host;
		$this->config['port'] = $port;
		$this->config['password'] = $password;

		$this->socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

		if ($this->socket === false) {
			$this->logError($errstr, $errno);
		}
	}

	public function isRunning() {
		return $this->socket !== false;
	}

	public function auth() {
		if ($this->socket === false) {
			return false;
		}

		$this->write(self::SERVERDATA_AUTH, $this->config['password']);


		$response = $this->read();

		if (isset($response['ID']) && $response['ID'] == -1) {
			$this->logError('Password authentican failed', self::ERR_UNABLE_TO_AUTH);
			return false;
		}

		return true;
	}

	public function sendCommand($command) {
		if ($this->socket === false) {
			return false;
		}

		$this->write(self::SERVERDATA_EXECCOMMAND, $command);

		$response = $this->read();

		return $response['s1'];
	}

	protected function write($cmd, $s1='', $s2='') {
		// Get and increment the packet id
		$this->id++;

		// Put our packet together
		$data = pack("VV", $this->id, $cmd) . $s1 . chr(0) . $s2 . chr(0);

		$data = pack("VV",1,$cmd).$s1.chr(0).$s2.chr(0);

		// Prefix the packet size
		$data = pack("V", strlen($data)) . $data;

		// Send packet
		fwrite($this->socket, $data ,strlen($data));

		return true;
	}

	protected function read() {

		//Fetch the packet size
		$size = @fread($this->socket, 4);

		// Decode size to byte length
		$response = unpack('Vsize', $size);

		// Read the rest of the stream
		$packet = fread($this->socket, $response['size']);

		// Decode the response (black magic happens here)
		$return = unpack("V1ID/V1response/a*s1/a*s2",$packet);

		return $return;
	}

	public function hasErrors() {
		return $this->last_error['msg'] != '';
	}

	protected function logError($str, $no=0) {
		$this->last_error['msg'] = $str;
		$this->last_error['num'] = $no;
	}
}
