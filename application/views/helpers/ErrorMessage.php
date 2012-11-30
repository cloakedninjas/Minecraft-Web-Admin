<?php

class Zend_View_Helper_ErrorMessage extends Zend_View_Helper_Abstract {
	public function errorMessage($msg) {
		if (is_array($msg)) {
			$html = '<ul class="error">';

			foreach ($msg as $m) {
				$html .= '<li>' . $m . '</li>';
			}
			$html .= '</ul>';

			return $html;
		}
		else {
			return '<div class="error">' . $msg . '</div>';
		}
	}


}