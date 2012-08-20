<?php
/**
 * Copyright (c) 2012, Agence Française Informatique (AFI). All rights reserved.
 *
 * AFI-OPAC 2.0 is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation.
 *
 * There are special exceptions to the terms and conditions of the AGPL as it
 * is applied to this software (see README file).
 *
 * AFI-OPAC 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with AFI-OPAC 2.0; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301  USA 
 */
class ZendAfi_View_Helper_Timeline extends Zend_View_Helper_Abstract {
	const URL = 'url';
	const LABEL = 'label';
	const CURRENT = 'current';

	/** @var boolean */
	protected $_after_current;

	/**
	 * @param $actions array
	 * @return string
	 */
	public function timeline($actions) {
		$this->_after_current = false;
				
		$html = '<div class="timeline"><ul>';
		foreach ($actions as $action)
			$html .= $this->_renderAction($action);
		$html .= '</ul></div>';

		return $html;
	}


	protected function _renderAction($action) {
		$class = $this->_getClassForAction($action);
		$content = $this->view->_($action['label']);
		if ($class == 'passed')
			$content = '<a href="'.$action[self::URL].'">'.$content.'</a>';

		$html = sprintf('<li class="%s">%s</li>', $class, $content);

 		if ($action[self::CURRENT])
			$this->_after_current = true;

		return $html;
	}


	protected function _getClassForAction($action) {
		if ($action[self::CURRENT])
			return 'selected';
		if (!$this->_after_current)
			return 'passed';
		return '';
	}
}
?>