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

abstract class ZendAfi_View_Helper_SocialShareTestCase extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_RenderForm */
	protected $_helper;

	public function setUp() {
		parent::setUp();
		$view = new ZendAfi_Controller_Action_Helper_View();
		$this->_helper = new ZendAfi_View_Helper_SocialShare();
		$this->_helper->setView($view);
	}
}




class ZendAfi_View_Helper_SocialShareTwitterTest extends ZendAfi_View_Helper_SocialShareTestCase {
	public function setUp() {
		parent::setUp();
		$profil = Class_Profil::getLoader()->newInstanceWithId(23)->setLibelle('Info pratiques');
		$this->html = $this->_helper->socialShare($profil, array('twitter'));
	}


	/** @test */
	public function imgTwitterShouldBeVisible() {
		$this->assertXPath($this->html, '//div[@class="share"]//img[@class="twitter"][contains(@onclick, "socialShare(\'twitter\')")]');
	}


	/** @test */
	public function imgFacebookShouldNotBeVisible() {
		$this->assertNotXPath($this->html, '//img[@class="facebook"]');
	}


	/** @test */
	public function scriptLoaderShouldContainsSocialShareMethod() {
		$this->assertXPathContentContains(Class_ScriptLoader::getInstance()->html(),
																			'//script',
																			'socialShare(network)');
	}
}




class ZendAfi_View_Helper_SocialShareFacebookTwitterAndMailTest extends ZendAfi_View_Helper_SocialShareTestCase {
	public function setUp() {
		parent::setUp();
		$profil = Class_Profil::getLoader()->newInstanceWithId(26)->setLibelle('Section adultes');
		$this->html = $this->_helper->socialShare($profil, array('facebook', 'twitter', 'mail'));
	}


	/** @test */
	public function imgTwitterShouldBeVisible() {
		$this->assertXPath($this->html, '//div[@class="share"]//img[@class="twitter"][contains(@src, "twitter")]');
	}


	/** @test */
	public function imgFacebookShouldBeVisible() {
		$this->assertXPath($this->html, '//div[@class="share"]//img[@class="facebook"][contains(@onclick, "socialShare(\'facebook\')")][contains(@src, "facebook")]');
	}


	/** @test */
	public function imgMailShouldLinkToFormulaireContact() {
		$this->assertXPath($this->html, '//div[@class="share"]//img[@class="mail"][contains(@onclick, "index/formulairecontact")][contains(@src, "mail")]');	
	}
}

?>