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
class ZendAfi_View_Helper_TagAnchor extends Zend_View_Helper_HtmlElement {
	/**
	 * @param string $url
	 * @param string $text
	 * @param array $attribs
	 * @return string
	 */
	public function tagAnchor($url, $text, array $attribs = array()) {
		if (is_array($url))
			$url = $this->view->url($url);
		return '<a href="'. $url .'"'. $this->_htmlAttribs($attribs) . '>' . $text . '</a>';

	}

	public function baseURL($text, $controller, $action, $params) {
		$url = 'http://' . $_SERVER['SERVER_NAME'] . BASE_URL . '/' . $controller . '/' . $action . '?' . http_build_query($params);
		return $this->tagAnchor($url, $text);
	}

}
?>