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


class ZendAfi_View_Helper_IconeSupportForTesting extends ZendAfi_View_Helper_IconeSupport {
	public function getExistingFiles() {
		return array(
								 PATH_SKIN.'/images/supports/support_1.gif',
								 PATH_SKIN.'/images/supports/son_s.png',
								 PATH_ADMIN_SUPPORTS.'famille_periodique.png'
								 );
	}

	public function fileExists($path) {
		return in_array($path, $this->getExistingFiles());
	}
}


class ZendAfi_View_Helper_IconeSupportTest extends ViewHelperTestCase {
	public function setUp() {
		parent::setUp();
		defineConstant("PATH_SKIN", "./public/opac/skins/original/");
		$this->_helper = new ZendAfi_View_Helper_IconeSupportForTesting();
		$this->_helper->setView(new ZendAfi_Controller_Action_Helper_View());
	}


	/** @test */
	function iconeSupportForOneShouldReturnSupportOneGifInSkinDir() {
		$this->assertXPath($this->_helper->iconeSupport(1),
											 '//img[@src="'.URL_IMG.'supports/support_1.gif"]');
	}


	/** @test */
	function imageForSupportTwoShouldReturnSupportPeriodiqueInAdminDir() {
		$this->assertEquals(URL_ADMIN_IMG.'supports/famille_periodique_small.png',
												$this->_helper->imageForSupport(2));
	}


	/** @test */
	function imageForSupportThreeShouldReturnFamilleMultimediaInSkinDir() {
		$this->assertEquals(URL_IMG.'supports/son_s.png',
												$this->_helper->imageForSupport(3));
	}


	/** @test */
	function imageForSupport38ShouldReturnSupportAutInAdminDir() {
		$this->assertEquals(URL_ADMIN_IMG.'supports/aut_s.png',
												$this->_helper->imageForSupport(38));
	}


	/** @test */
	function imageForSupportGreaterThan100ShouldReturnSupportMLSInAdminDir() {
		$this->assertEquals(URL_ADMIN_IMG.'supports/mls_s.png',
												$this->_helper->imageForSupport(Class_TypeDoc::LIVRE_NUM));
	}
}
?>