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


class Class_WebService_OAI_ResumptionToken {
	protected static $_cache = null;
	protected $_params;
	protected $_list_size;
	protected $_cursor = 0;
	protected $_page_number = 1;

	public static function defaultCache($cache) {
		self::$_cache = $cache;
	}


	public static function getCache() {
		if (!isset(self::$_cache))
			return Zend_Registry::get('cache');
		return self::$_cache;
	}


	public static function newWithParamsAndListSize($params, $list_size, $cursor = 0, $page_number = 1) {
		return new self($params, $list_size, $cursor, $page_number);
	}


	public static function find($key) {
		return unserialize(self::getCache()->load($key));
	}


	public function __construct($params, $list_size, $cursor, $page_number) {
		$this->_params = $params;
		$this->_list_size = $list_size;
		$this->_cursor = $cursor;
		$this->_page_number = $page_number;
	}

	public function save() {
		$data = serialize($this);
		return $this->getCache()->save($data, md5($data));
	}


	public function next($size) {
		return self::newWithParamsAndListSize($this->_params, 
																					$this->_list_size, 
																					$this->_cursor + $size,
																					$this->_page_number + 1);
	}


	public function renderOn($builder) {
		return $builder->resumptionToken(array('completeListSize' => $this->_list_size,
																					 'cursor' => $this->_cursor),
																		 md5(serialize($this)));
	}


	public function getPageNumber() {
		return $this->_page_number;
	}
}
?>