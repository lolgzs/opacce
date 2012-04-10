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

/**
 * Un item d'un flux RSS
 */
class Class_RssItem {
	protected $_wrapped_item;

	public function __construct($item) {
		$this->_wrapped_item = $item;
	}

	public function __call($method, $args) {
		return call_user_func_array(array($this->_wrapped_item, $method),
																$args);
  }


	public function getTitle() {
		return $this->_wrapped_item->title();
	}


	public function getLink() {
		return $this->_wrapped_item->link();
	}


	public function getDate() {
		$date = '';
		if ($item_date = trim($this->_wrapped_item->pubDate())) {
			$locale = Zend_Registry::get('locale');
			$dateFormat = ($locale == 'en_US') ? 'MM-dd-yyyy' : 'dd-MM-yyyy';

			try {
				$zendDate =  new Zend_Date($item_date, Zend_Date::RSS);
				$date = $zendDate->toString($dateFormat);
			} catch (Exception $e) {
				$date = '';
			}
		}

		return $date;
	}


	public function getDescription() {
		$description = $this->_wrapped_item->description();
		if (is_array($description)) {
			$description = reset($description);
		}

		if (is_object($description) && $description instanceof DOMElement) {
			$description = $description->nodeValue;
		}

		return strip_tags($description);
	}
}

?>