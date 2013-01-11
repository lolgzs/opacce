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
require_once 'library/ZendAfi/View/Helper/ViewHelperTestCase.php';

class TagsTest extends ViewHelperTestCase {
	protected $_old_sql;

	public function setUp() {
		parent::setUp();
		$this->_old_sql = Zend_Registry::get('sql');
		
		$notice_enreg = array('type_doc' => 1,
													'facettes' => 'A777',
													'id_notice' => 34,
													'editeur' => '',
													'annee' => '',
													'date_creation' => '',
													'clef_oeuvre' => '');

		Class_Notice::newInstanceWithId(34, $notice_enreg);

		Zend_Registry::set('sql', 
											 Storm_Test_ObjectWrapper::mock()
											 ->whenCalled('fetchAll')
											 ->answers(array($notice_enreg))
											 
											 ->whenCalled('fetchOne')
											 ->answers('Terry Pratchett'));

		$helper = new ZendAfi_View_Helper_Accueil_Tags(3, array('type_module' => 'TAGS',
																														'division' => 2,
																														'preferences' => array('type_tags' => 'AMDPZ',
																																									 'nombre' => 1)));
		$this->_html = $helper->getBoite();
	}


	/** @test */
	public function terryPratchetShouldBePresent() {
		$this->assertXPathContentContains($this->_html, 
																			'//a[contains(@href, "recherche/rebond")]', 
																			'Terry Pratchett');
	}


	public function tearDown() {
		Zend_Registry::set('sql', $this->_old_sql);
		parent::tearDown();
	}

}