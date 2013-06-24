<?php

class LoginController extends Zend_Controller_Action {

    public function init() {
    	$this->_helper->validUser(true, false);

    	$this->auth = new Application_Model_AuthManager();

    	if ($this->auth->isAuthed()) {
    		$this->_redirect('/');
    	}

    	Zend_Layout::getMvcInstance()->assign('show_menu', false);

    }

    public function indexAction() {

    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    		if ($this->auth->authenticate($this->_getParam('username'), $this->_getParam('password'))) {
    			$this->_redirect('/');
    		}
    		else {
    			$this->view->prev_username = $this->_getParam('username');
    			$this->view->error = 'Incorrect username and / or password';
    		}
    	}

    }


}

