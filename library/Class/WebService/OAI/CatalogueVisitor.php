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

class Class_WebService_OAI_CatalogueVisitor {
	protected $_xml;
	protected $_xmlBuilder;

	public function __construct($builder) {
		$this->_xmlBuilder = $builder;
	}


	public function xml() {
		return $this->_xml;
	}


	public function visitCatalogue($catalogue) {
		$this->_xml = '';
		$catalogue->acceptVisitor($this);
		return $this;
	}


	public function visitOaiSpec($oai_spec) {
		$this->_xml .= $this->_xmlBuilder->setSpec($oai_spec);
	}


	public function visitLibelle($libelle) {
		$this->_xml .= $this->_xmlBuilder->setName($libelle);
	}


	public function visitDescription($description) {
		$dcBuilder = new Class_Xml_Oai_DublinCoreBuilder();
		$dcDescription = $dcBuilder->oai_dc($dcBuilder->description($description));
		$this->_xml .= $this->_xmlBuilder->setDescription($dcDescription);
	}
}

?>