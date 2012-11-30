<?php

class Zend_Controller_Action_Helper_ValidUser extends Zend_Controller_Action_Helper_Abstract {

	public function direct($check_config=true, $check_auth=true) {
		if ($check_config) {
			$this->config = new Application_Model_ConfigManager();

	    	if (!$this->config->isSetup()) {

	    		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
	    		$redirector->gotoUrlAndExit('/setup');
	    	}
		}

		if ($check_auth) {
	    	$this->auth = new Application_Model_AuthManager();

	    	if (!$this->auth->isAuthed()) {
	    		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
	    		$redirector->gotoUrlAndExit('/login');
	    	}
		}
	}

}