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
class Class_WebService_OAI_Response_Null {
	const PROLOG = '<?xml version="1.0" encoding="UTF-8"?>';
	protected $_baseUrl;

	public function __construct($baseUrl) {
		$this->_baseUrl = $baseUrl;
	}


	public function xml() {
		$builder = new Class_Xml_Builder();
		
		return self::PROLOG . "\n"
			. $builder->_tag(array('OAI-PMH' =>  array('xmlns' => 'http://www.openarchives.org/OAI/2.0/',
																								 'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
																								 'xsi:schemaLocation' => 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd')),
											 $builder->responseDate(date('c')).
											 $this->buildXmlOn($builder));
	}


	public function buildXmlOn($builder) {
		return $builder->error(array('code' => 'badVerb'), 'Illegal OAI verb');
	}
}

?>