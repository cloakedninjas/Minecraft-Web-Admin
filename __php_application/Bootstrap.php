<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	protected function _initActionHelpers() {
		Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH .'/controllers/helpers');
	}

	protected function _initConfig() {
		$config = new Zend_Config($this->getOptions());
		Zend_Registry::set('config',$config);
	}

	protected function _initRouter() {
		$front = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();

		$route = new Zend_Controller_Router_Route(
				'/servers/edit/:server_id',
				array(
					'controller'	=> 'servers',
					'action'		=> 'detail',
				)
		);

		$router->addRoute('server-detail', $route);
	}

	protected function _initDbAdapter() {
		$this->bootstrap('db');
		$db = $this->getResource('db');
		Zend_Registry::set('db' , $db);
	}
}

