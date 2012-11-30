<?php

class ApiController extends Zend_Controller_Action {

    public function init() {
    	$this->_helper->validUser();

		$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender();
	}

	public function isServerRunningAction() {
		$server = new Application_Model_MinecraftServer($this->_getParam('server_id'));

    	if ($server->id) {

    		$response = array('running' => false);

    		$socket = @fsockopen($server->host, $server->port, $errstr, $errno, 1);

    		if ($socket !== false) {
    			fclose($socket);
    			$response['running'] = true;
    		}

    		echo json_encode($response);
    	}
    	else {
			$this->error('Unknown server');
    	}
	}

    public function startServerAction() {
    	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    		$this->error('Invalid request method ');
    		return;
    	}

    	$server = new Application_Model_MinecraftServer($this->_getParam('server_id'));

    	if ($server->id) {
    		if ($server->isRunning()) {
    			$this->error('Server already running');
    		}
    		else {
				$server->start();
				$this->success();
    		}
    	}
    	else {
			$this->error('Unknown server');
    	}
    }

    public function stopServerAction() {
    	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    		$this->error('Invalid request method ');
    		return;
    	}

    	$server = new Application_Model_MinecraftServer($this->_getParam('server_id'));

    	if ($server->id) {
    		if (!$server->isRunning()) {
    			$this->error('Server not running');
    		}
    		else {
    			$server->stop($this->_getParam('force'));
    			$this->success();
    		}
    	}
    	else {
    		$this->error('Unknown server');
    	}
    }

    public function issueCommandAction() {
    	$server = new Application_Model_MinecraftServer($this->_getParam('server_id'));

    	if ($server->id) {
    		if (!$server->isRunning()) {
    			$this->error('Server is not running');
    		}
    		else {
    			$resp = $server->issueWebCommand($this->_getAllParams());

    			var_dump($resp);

    			$this->success();
    		}
    	}
    	else {
    		$this->error('Unknown server');
    	}
    }

    protected function error($message) {
    	echo json_encode(array('error' => $message));
    }

    protected function success($message=null) {
    	echo json_encode(array('status' => 1));
    }


}

