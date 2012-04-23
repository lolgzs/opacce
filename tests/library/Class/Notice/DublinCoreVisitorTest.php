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

abstract class DublinCoreVisitorTestCase extends Storm_Test_ModelTestCase { 
	protected $_xpath;
	protected $_dublin_core_visitor;

	public function setUp() {
		parent::setUp();
		$this->_xpath = new Storm_Test_XPath();
		$this->_dublin_core_visitor = new Class_Notice_DublinCoreVisitor();
	}
}


class DublinCoreVisitorPotterTest extends DublinCoreVisitorTestCase { 
	public function setUp() {
		parent::setUp();
		$potter = Class_Notice::getLoader()
			->newInstanceWithId(4)
			->setClefAlpha('harrypotter-sorciers')
			->setTitrePrincipal('Harry Potter a l\'ecole des sorciers')
			->setDateMaj('2012-04-23');
		$this->_dublin_core_visitor->visit($potter);
	}


	/** @test */
	public function identifierShouldBeHarryPotterSorciers() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//dc//identifier',
																							sprintf('http://localhost%s/recherche/notice/harrypotter-sorciers',
																											BASE_URL));
	}


	/** @test */
	public function titleShouldBeHarryPotterEcoleSorciers() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//dc//title',
																							'Harry Potter a l\'ecole des sorciers');
	}


	/** @test */
	public function namespaceShouldBeOAIDC() {
		$this->_xpath->assertXpath($this->_dublin_core_visitor->xml(),
															 '//dc');
	}

	
}


class DublinCoreVisitorSouvignyTest extends DublinCoreVisitorTestCase { 
	public function setUp() {
		parent::setUp();
		$souvigny = Class_Notice::getLoader()
			->newInstanceWithId(5)
			->setClefAlpha('souvigny-bible-11eme')
			->setDateMaj('2012-04-23');
		$oldServerName = $_SERVER['SERVER_NAME'];
		$_SERVER['SERVER_NAME'] = 'moulins.fr';
		$this->_dublin_core_visitor->visit($souvigny);
		$_SERVER['SERVER_NAME'] = $oldServerName;
	}


	/** @test */
	public function identifierShouldBeSouvignyBible11eme() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//dc//identifier',
																							sprintf('http://moulins.fr%s/recherche/notice/souvigny-bible-11eme',
																											BASE_URL));
	}
	
}

?>