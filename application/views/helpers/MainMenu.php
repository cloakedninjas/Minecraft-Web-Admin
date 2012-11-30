<?php

class Zend_View_Helper_MainMenu extends Zend_View_Helper_Abstract {
	public function mainMenu() {
		$html = '<ul class="navigation">';
		$html .= '<li' . $this->isActive('index') . '><a href="/">Home</a></li>';
		$html .= '<li' . $this->isActive('servers') . '><a href="/servers">Servers</a></li>';
		$html .= '<li' . $this->isActive('settings') . '><a href="/settings">Settings</a></li>';

		return $html;
	}

	protected function isActive($controller) {
		if (Zend_Controller_Front::getInstance()->getRequest()->getControllerName() == $controller) {
			return ' class="active"';
		}

	}


}