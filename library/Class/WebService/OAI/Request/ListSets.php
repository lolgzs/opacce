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
class Class_WebService_OAI_Request_ListSets {
	const SETS_BY_PAGE = 100;
	protected $_baseUrl;
	protected $_catalogs;

	public function __construct($params, $baseUrl) {
		$this->_baseUrl = $baseUrl;
		$this->_catalogs = Class_Catalogue::getLoader()->findAllBy(array('where' => 'oai_spec is not null',
																																		 'where' => 'oai_spec !=\'\'',
																																		 'order' => 'oai_spec'));
	}


	public function getCatalogs() {
		return $this->_catalogs;
	}


	public function renderOn($builder) {
		$attributes = array('verb' => 'ListSets');
		return $builder->request($attributes, $this->_baseUrl);
	}
}
?>