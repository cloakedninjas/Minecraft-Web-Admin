<?php

class ServersController extends Zend_Controller_Action {

    public function init() {

    	$this->_helper->validUser();

    	Zend_Layout::getMvcInstance()->assign('show_menu', true);
	}

    public function indexAction() {
		$db = Zend_Registry::get('db');
		$servers = $db->fetchAll('SELECT * FROM SERVERS');

    	$this->view->servers = $servers;
    }

    public function detailAction() {
    	$server = new Application_Model_MinecraftServer($this->_getParam('server_id'));

    	if ($server->id) {
    		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    			$result = $server->saveChanges($this->_getAllParams());

    			if ($result !== true) {
    				$this->_helper->Notification->setMessage($result['error'], 'warn');
    			}
    			else {
    				$this->_helper->Notification->setMessage('Changes saved successfully.', 'warn');
    			}
    		}

    		$configManager = new Application_Model_ConfigManager();

    		$this->view->server = $server;
    		$this->view->warning_time = $configManager->getValue('warning_time');
    	}
    	else {
    		$this->view->error = 'No server found with that ID';
    	}

    }

    public function createAction() {
    	$config = Zend_Registry::get('config');

    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    		$server = new Application_Model_MinecraftServer();

    		$valid = $server->validatePost($this->_getAllParams());

    		if ($valid === true) {
    			$server->saveChanges($this->_getAllParams());
    		}
    		else {
    			$this->view->form_errors = $valid;
    		}
    	}

    	$this->view->defaults = $config->minecraft->default->toArray();

    }


}

