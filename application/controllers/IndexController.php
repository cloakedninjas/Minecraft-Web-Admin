<?php

class IndexController extends Zend_Controller_Action {

    public function init() {

    	$this->_helper->validUser();

    	Zend_Layout::getMvcInstance()->assign('show_menu', true);
    }

    public function indexAction() {
/*
        $rcon = new Application_Model_MinecraftRconClient('localhost', 25575, 'cockmuncher');

        if ($rcon->auth()) {
        	var_dump($rcon->whiteList('list'));
        }
        else {
        	echo 'nope';
        }
*/
    }


}

