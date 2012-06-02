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

class MenuImageTest extends ViewHelperTestCase {
	/** @var string */
	protected $_html;

	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('VODECLIC_KEY')
			->setValeur(1234);

		Class_Profil::getCurrentProfil()->setCfgMenus(
			array(
				'H' => array(
					'libelle' => 'Decouverte',
					'menus' => array(
						array(
							'libelle' => 'services',
							'sous_menus' => array(
																		array(
																					'type_menu' => 'VODECLIC',
																					'libelle' => 'vodeclic',
																					'picto' => 'vide.gif'
																					)
																		)
							),
						),
					)
				)
		);

		$this->helper = new ZendAfi_View_Helper_Java_MenuImage();
		$this->_html = $this->helper->MenuImage();
	}


	/** @test */
	public function divImageMenuShouldBePresent() {
		$this->assertXPath($this->_html, '//div[@id="imageMenu"]');
	}


	/** @test */
	public function menuVodeclicShouldBePresent() {
		$this->assertXPathContentContains($this->_html, '//li', 'vodeclic', $this->_html);
	}
}
