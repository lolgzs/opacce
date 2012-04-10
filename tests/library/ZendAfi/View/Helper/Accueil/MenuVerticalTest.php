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

abstract class MenuVerticalTestCase extends ViewHelperTestCase {
	/** @var string */
	protected $_html;

	public function setUp() {
		parent::setUp();

		$this->_menuConfigTrigger();

		$this->helper = new ZendAfi_View_Helper_Accueil_MenuVertical(12, array(
			'division' => '1',
			'type_module' => 'MENU_VERTICAL',
			'preferences' => array(
				'boite'					=> '',
				'afficher_titre'=> '1',
				'menu'					=> '7',
				'menu_deplie' => 0
			)
		));

		$this->_html = $this->helper->getBoite();
	}


	protected function _menuConfigTrigger() {}
}




abstract class MenuVerticalWithOnlyOneItemTestCase extends MenuVerticalTestCase {
	/** @var string */
	protected $_itemLabel;


	/** @test */
	public function renderShouldContainOnlyOneItem() {
		$this->assertQueryCount($this->_html, '//ul/li', 1, $this->_html);
	}


	/** @test */
	public function renderShouldContainDirectLinkToRss() {
		$this->assertXpathContentContains($this->_html, '//ul/li/a', $this->_itemLabel);
	}
}




class MenuVerticalRssWithOnlyOneItemTest extends MenuVerticalWithOnlyOneItemTestCase {
	protected function _menuConfigTrigger() {
		Class_Profil::getCurrentProfil()->setCfgMenus(
			array(
				7 => array(
					'libelle' => 'Sciences et Technologies',
					'menus' => array(
						array(
							'type_menu' => 'RSS',
							'libelle' => 'Bulletin electronique',
							'picto' => 'vide.gif',
							'preferences' => array(
								'id_categorie' => '',
								'id_items' => '46',
								'nb' => '10'
							),
							'sous_menus' => ''
						),
					)
				)
			)
		);

		Class_Rss::getLoader()
			->newInstanceWithId(46)
			->setTitre('Bulletin electronique');

		$this->_itemLabel = 'Bulletin electronique';
	}
}




class MenuVerticalSitothequeWithOnlyOneItemTest extends MenuVerticalWithOnlyOneItemTestCase {
	protected function _menuConfigTrigger() {
		Class_Profil::getCurrentProfil()->setCfgMenus(
			array(
				7 => array(
					'libelle' => 'Sciences et Technologies',
					'menus' => array(
						array(
							'type_menu' => 'SITO',
							'libelle' => 'Tous Nos Sites',
							'picto' => 'vide.gif',
							'preferences' => array(
								'id_categorie' => '',
								'id_items' => '46',
								'nb' => '10'
							),
							'sous_menus' => ''
						),
					)
				)
			)
		);

		Class_Sitotheque::getLoader()
			->newInstanceWithId(46)
			->setTitre('Tous les sites');

		$this->_itemLabel = 'Tous Nos Sites';
	}
}




abstract class MenuVerticalWithManyItemsTestCase extends MenuVerticalTestCase {
	/** @var string */
	protected $_parentLabel;

	/** @var string */
	protected $_firstItemLabel;

	/** @var string */
	protected $_secondItemLabel;


	/** @test */
	function parentItemShouldHaveDisplayNone() {
		$this->assertXPath($this->_html, '//ul/li[contains(@style, "display:none")]');
	}


	/** @test */
	public function renderShouldContainMainLink() {
		$this->assertXpathContentContains($this->_html, '//ul/li/a', $this->_parentLabel);
	}


	/** @test */
	public function renderShouldContainFirstItemlLink() {
		$this->assertXpathContentContains($this->_html, '//ul/li/a', $this->_firstItemLabel);
	}


	/** @test */
	public function renderShouldContainSecondItemlLink() {
		$this->assertXpathContentContains($this->_html, '//ul/li/a', $this->_secondItemLabel);
	}
}




class MenuVerticalRssWithManyItemsTest extends MenuVerticalWithManyItemsTestCase {
	protected function _menuConfigTrigger() {
		Class_Profil::getCurrentProfil()->setCfgMenus(
			array(
				7 => array(
					'libelle' => 'Sciences et Technologies',
					'menus' => array(
						array(
							'type_menu' => 'RSS',
							'libelle' => 'Bulletin electronique',
							'picto' => 'vide.gif',
							'preferences' => array(
								'id_categorie' => '',
								'id_items' => '46-47',
								'nb' => '10'
							),
							'sous_menus' => array(array('type_menu' => 'ACCUEIL',
																					'libelle' => 'Accueil',
																					'picto' => 'vide.gif',
																					'preferences' => array()))
						),
					)
				)
			)
		);

		Class_Rss::getLoader()
			->newInstanceWithId(46)
			->setTitre('Premier Bulletin electronique');

		Class_Rss::getLoader()
			->newInstanceWithId(47)
			->setTitre('Autre Bulletin electronique');

		$this->_parentLabel			= 'Bulletin electronique';
		$this->_firstItemLabel	= 'Premier Bulletin electronique';
		$this->_secondItemLabel	= 'Autre Bulletin electronique';
	}


	/** @test */
	function withMenuDeplieOnShouldNotHaveDisplayNone() {
		$this->helper->setPreference('menu_deplie', 1);
		$this->_html = $this->helper->getBoite();
		$this->assertNotXPath($this->_html, '//ul/li[contains(@style, "display:none")]');
	}

	/** @test */
	public function renderShouldContainThreeSubItems() {
		$this->assertQueryCount($this->_html, '//ul/li/ul/li', 3);
	}
}




class MenuVerticalSitothequeWithManyItemsTest extends MenuVerticalWithManyItemsTestCase {
	protected function _menuConfigTrigger() {
		Class_Profil::getCurrentProfil()->setCfgMenus(
			array(
				7 => array(
					'libelle' => 'Nos sites',
					'menus' => array(
						array(
							'type_menu' => 'RSS',
							'libelle' => 'Nos Sites',
							'picto' => 'vide.gif',
							'preferences' => array(
								'id_categorie' => '',
								'id_items' => '46-47',
								'nb' => '10'
							),
							'sous_menus' => ''
						),
					)
				)
			)
		);

		Class_Rss::getLoader()
			->newInstanceWithId(46)
			->setTitre('Sites Culturels');

		Class_Rss::getLoader()
			->newInstanceWithId(47)
			->setTitre('Sites professionels');

		$this->_parentLabel			= 'Nos Sites';
		$this->_firstItemLabel	= 'Sites Culturels';
		$this->_secondItemLabel	= 'Sites professionels';
	}

	/** @test */
	public function renderShouldContainTwoSubItems() {
		$this->assertQueryCount($this->_html, '//ul/li/ul/li', 2);
	}
}




class MenuVerticalAlbumTest extends MenuVerticalTestCase {
	protected function _menuConfigTrigger() {
		Class_Profil::getCurrentProfil()->setCfgMenus(
			array(
				7 => array(
					'libelle' => 'Albums',
					'menus' => array(
						array(
							'type_menu' => 'BIBNUM',
							'libelle' => 'Bible de souvigny',
							'picto' => 'vide.gif',
							'preferences' => array(
								'album_id' => '41'
							),
							'sous_menus' => ''
						),
					)
				)
			)
		);

		Class_Album::getLoader()
			->newInstanceWithId(41)
			->setTitre('Bible de souvigny')
			->setRessources(array(
														Class_AlbumRessource::getLoader()
														->newInstanceWithId(411)
														->setOrdre(0),

														Class_AlbumRessource::getLoader()
														->newInstanceWithId(412)
														->setOrdre(1),

														Class_AlbumRessource::getLoader()
														->newInstanceWithId(413)
														->setOrdre(2)
														->setTitre('Troisieme page')));

		$this->_itemLabel = 'Bible de souvigny';
	}


	/** @test */
	function albumHRefShouldLinkToBooklet() {
		$this->assertXPath($this->_html,'//li//a[contains(@href, "/bib-numerique/booklet/id/41")]', $this->_html);
	}


	/** @test */
	function menuShouldContainsEntryToThirdPage() {
		$this->assertXPath($this->_html,'//ul/li/ul//li//a[contains(@href, "/bib-numerique/booklet/id/41/#/page/3")]', $this->_html);
	}


	/** @test */
	function menuShouldNotContainsEntryToSecondPage() {
		$this->assertNotXPath($this->_html,'//ul/li/ul//li//a[contains(@href, "/bib-numerique/booklet/id/41/#/page/2")]', $this->_html);
	}
}