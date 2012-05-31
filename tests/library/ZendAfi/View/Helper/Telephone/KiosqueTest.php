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

class Telephone_KiosqueTest extends ViewHelperTestCase {
	public function setUp() {
		parent::setUp();
		
		$module = new Class_Systeme_ModulesAccueil_Kiosque();
		$options = $module->getDefaultValues();
		$options['titre'] = 'Ze Kiosque';

		$params = array('division' => '2',
										'type_module' => 'KIOSQUE',
										'preferences' => $options);
		$this->helper = new ZendAfi_View_Helper_Telephone_Kiosque(2, $params);
		$this->html = $this->helper->getBoite();
		$this->scriptLoader = Class_ScriptLoader::getInstance();
	}


	/** @test */
	public function titreShouldBeZeKiosque() {
		$this->assertXPathContentContains($this->html,
																			'//div[@class="titre"]',
																			'Ze Kiosque');
	}


	/** @test */
	public function javascriptShouldBeLoaded() {
		$this->assertXPath($this->scriptLoader->html(), 
											 '//script[contains(@src, "kiosque-slideshow")]');
	}


	/** @test */
	public function slideshowShouldBeActivated() {
			$this->assertXPathContentContains($this->scriptLoader->html(),
																				'//script',
																				'$(\'#slideshow\').kiosqueSlideshow()');
	}

}