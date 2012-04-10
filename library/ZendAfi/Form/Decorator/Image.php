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
class ZendAfi_Form_Decorator_Image extends Zend_Form_Decorator_Abstract {
	/**
	 * @param  string $content
	 * @return string
	 */
	public function render($content) {
		if ('' == $this->_element->getValue()) {
			return $content;
		}

		if ('' == $this->_element->getBaseUrl()) {
			return $content;
		}

		$parts = explode('.', $this->_element->getValue());
		$ext = end($parts);

		if (!in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif'))) {
			return $content;
		}

		return $content . $this->_element->getView()->tagImg(
			$this->_element->getThumbnailUrl(),
			array('style' => 'width:100px;')
		);
	}
}
?>