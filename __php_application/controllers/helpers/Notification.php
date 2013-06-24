<?php

class Zend_Controller_Action_Helper_Notification extends Zend_Controller_Action_Helper_Abstract {

	public function setMessage($msg, $type='notice') {
		$layout = Zend_Layout::getMvcInstance()->getView();
		$layout->flash_messages = array('type' => $type, 'msg' => $msg);
	}

}