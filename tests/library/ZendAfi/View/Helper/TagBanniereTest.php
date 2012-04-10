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

class ZendAfi_View_Helper_TagBanniereCycleTest extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_TreeView */
	protected $_helper;

	/** @var string */
	protected $_html;

	public function setUp() {
		parent::setUp();
		defineConstant("PATH_SKIN","");

		$this->_helper = new ZendAfi_View_Helper_TagBanniere();
		$this->_helper->setView(new ZendAfi_Controller_Action_Helper_View());
		$this->_helper->view->profil = Class_Profil::getLoader()
			->newInstanceWithId(4)
			->setHeaderImgCycle(true)
			->setAllHeaderImg(array('ban1.jpg', 'ban2.jpg'));

		$this->_html = $this->_helper->tagBanniere();
	}

	/** @test */
	function divBanniereShouldBePresent() {
		$this->assertXPath($this->_html, '//div[@id="banniere"]');
	}


	/** @test */
	function ban1ShouldBeVisible() {
		$this->assertXPath($this->_html, "//img[@src='ban1.jpg']");
	}

	/** @test */
	function ban2ShouldBeVisible() {
		$this->assertXPath($this->_html, "//img[@src='ban2.jpg']");
	}	
}

?>