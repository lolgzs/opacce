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

class BibNumeriqueAllCollectionsTest extends ViewHelperTestCase {
	/** @var string */
	protected $_html;


	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('getCollections')
			->answers(array(BibNumeriqueFixture::createBibleDeSouvigny()))
			->getWrapper()->beStrict();

		$helper = new ZendAfi_View_Helper_Accueil_BibNumerique(9, array(
		  'division' => 1,
			'type_module' => 'BIB_NUMERIQUE',
			'preferences' => array(
				 'boite' => '',
				 'titre' => 'Toutes nos collections',
				 'id_categories' => '',
				 'type_aff' => Class_Systeme_ModulesAccueil_BibliothequeNumerique::DISPLAY_TREE,
      ),
		));

		$this->_html = $helper->getBoite();
	}


	/** @test */
	public function titleShouldBeToutesNosCollections() {
		$this->assertXPathContentContains($this->_html, 
																			'//div[@class="titre"]//h1', 
																			'Toutes nos collections');
	}


	/** @test */
	public function itemCountShouldBeOne() {
		$this->assertQueryCount($this->_html, '//div[@class="contenu"]/div/ul/li', 1);
	}


	/** @test */
	public function itemTitleShouldBeBibleDeSouvigny() {
		$this->assertXPathContentContains($this->_html,
																			'//div[@class="contenu"]/div/ul/li', 
																			'Bible de Souvigny');
	}


	/** @test */
	public function categoriesCountShouldBeTwo() {
		$this->assertQueryCount($this->_html, '//div[@class="contenu"]/div/ul/li/ul/li', 2);
	}


	/** @test */
	public function firstCategorieTitleShouldBeLesEnluminures() {
		$this->assertXPathContentContains($this->_html, 
																			'//div[@class="contenu"]/div/ul/li/ul/li[1]/a', 
																			'Les enluminures');
	}


	/** @test */
	public function secondCategorieTitleShouldBeLesPlusBellesCalligraphies() {
		$this->assertXPathContentContains($this->_html, 
																			'//div[@class="contenu"]/div/ul/li/ul/li[2]/a', 
																			'Les plus belles calligraphies');
	}


	/** @test */
	public function firstCategorieShouldHaveOneAlbum() {
		$this->assertQueryCount($this->_html, '//div[@class="contenu"]/div/ul/li/ul/li[1]/ul/li', 1);
	}


	/** @test */
	public function firstCategorieAlbumTitleShouldBePremierVolume() {
		$this->assertXPathContentContains($this->_html,
																			'//div[@class="contenu"]/div/ul/li/ul/li[1]/ul/li/a',
																			'Premier volume');
	}


	/** @test */
	public function secondCategorieShouldHaveNoAlbum() {
		$this->assertNotXPath($this->_html, '//div[@class="contenu"]/div/ul/li/ul/li[2]/ul');
	}
}





class BibNumeriqueBibleDeSouvignyCollectionTest extends ViewHelperTestCase {
	/** @var string */
	protected $_html;

	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('find')->with(1)
			->answers(BibNumeriqueFixture::createBibleDeSouvigny())
			->getWrapper()->beStrict();

		$helper = new ZendAfi_View_Helper_Accueil_BibNumerique(9, array(
		  'division' => 1,
			'type_module' => 'BIB_NUMERIQUE',
			'preferences' => BibNumeriqueFixture::createOneCategorieTreePreference(),
		));

		$this->_html = $helper->getBoite();
	}


	/** @test */
	public function titleShouldBeToutesNotrePlusBelleBible() {
		$this->assertXPathContentContains($this->_html, 
																			'//div[@class="titre"]//h1', 
																			'Notre plus belle bible');
	}


	/** @test */
	public function selectedCollectionShouldNotBePresent() {
		$this->assertNotXPathContentContains($this->_html,
																				 '//div[@class="contenu"]/div/ul/li', 
																				 'Bible de Souvigny');
	}


	/** @test */
	public function itemCountShouldBeTwo() {
		$this->assertQueryCount($this->_html, '//div[@class="contenu"]/div/ul/li', 2);
	}


	/** @test */
	public function firstItemShouldBeLesEnluminures() {
		$this->assertXPathContentContains($this->_html, 
																			'//div[@class="contenu"]/div/ul/li[1]', 
																			'Les enluminures');
	}


	/** @test */
	public function secondItemShouldBeLesPlusBellesCalligraphies() {
		$this->assertXPathContentContains($this->_html, 
																			'//div[@class="contenu"]/div/ul/li[2]', 
																			'Les plus belles calligraphies');
	}


	/** @test */
	public function withPreferencesDiaporamaShouldNotFail() {
		$prefs =  BibNumeriqueFixture::createOneCategorieTreePreference();
		$prefs['style_liste'] = 'diaporama';
		$prefs['id_albums'] = '999';
		$helper = new ZendAfi_View_Helper_Accueil_BibNumerique(9, array(
																																		'division' => 1,
																																		'type_module' => 'BIB_NUMERIQUE',
																																		'preferences' => $prefs,
																																		));
		$this->_html = $helper->getBoite();
	}
}




abstract class BibNumeriqueViewCollectionTestCase extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_Accueil_BibNumerique */
	protected $_helper;

	public function setUp() {
		parent::setUp();

		if ($request = Zend_Controller_Front::getInstance())
			$request->setParam('id', null);

		BibNumeriqueFixture::createBibleDeSouvigny();
	}
}




class BibNumeriqueBibleDeSouvignyCollectionCurrentDetectionTest extends BibNumeriqueViewCollectionTestCase {
	/** @var ZendAfi_View_Helper_Accueil_BibNumerique */
	protected $_helper;

	public function setUp() {
		parent::setUp();

		$this->_helper = new ZendAfi_View_Helper_Accueil_BibNumerique(9, array(
		  'division' => 1,
			'type_module' => 'BIB_NUMERIQUE',
			'preferences' => BibNumeriqueFixture::createOneCategorieTreePreference(),
		));
	}


	/** @test */
	public function withoutAnyContexShouldNotHaveSelectLink() {
		$this->assertNotXPath($this->_helper->getBoite(), '//a[@class="selected"]');
	}


	/** @test */
	public function whenRequestingLesEnluminuresItsLinkShouldBeSelected() {
		Zend_Controller_Front::getInstance()
			->getRequest()->setParam('id', 2)
			->setActionName('view-categorie')
			->setControllerName('bib-numerique');

		$this->assertXpathContentContains($this->_helper->getBoite(), 
																			'//a[@class="selected"]', 
																			'Les enluminures');
	}


	/** @test */
	public function whenRequestingLesPlusBellesCalligraphiesItsLinkShouldBeSelected() {
		Zend_Controller_Front::getInstance()
			->getRequest()->setParam('id', 3)
			->setActionName('view-categorie')
			->setControllerName('bib-numerique');

		$this->assertXpathContentContains($this->_helper->getBoite(), 
																			'//a[@class="selected"]', 
																			'Les plus belles calligraphies');
	}


	/** @test */
	public function whenRequestingPremierVolumeItsLinkShouldBeSelected() {
		Zend_Controller_Front::getInstance()
			->getRequest()->setParam('id', 1)
			->setActionName('view-album')
			->setControllerName('bib-numerique');

		Class_Album::getLoader()->find(1)
			->setCategorie(Class_AlbumCategorie::getLoader()->find(2));

		$this->assertXpathContentContains($this->_helper->getBoite(), 
																			'//a[@class="selected"]', 
																			'Premier volume');
	}

	
	/** @test */
	public function whenRequestingPremierVolumeItsParentLinkShouldBeSelected() {
		Zend_Controller_Front::getInstance()
			->getRequest()->setParam('id', 1)
			->setActionName('view-album')
			->setControllerName('bib-numerique');

		Class_Album::getLoader()->find(1)
			->setCategorie(Class_AlbumCategorie::getLoader()->find(2));

		$this->assertXpathContentContains($this->_helper->getBoite(), 
																			'//a[@class="selected"]', 
																			'Les enluminures');
	}
}



class BibNumeriqueCollectionEnluminuresTest extends BibNumeriqueViewCollectionTestCase {
	public function setUp() {
		parent::setUp();

		$this->_helper = new ZendAfi_View_Helper_Accueil_BibNumerique(9, array(
		  'division' => 1,
			'type_module' => 'BIB_NUMERIQUE',
			'preferences' => BibNumeriqueFixture::createCategorieTwoTreePreference(),
		));
	}


	/** @test */
	public function linksShouldContainsAlbumPremierVolume() {
		$this->assertXpathContentContains($this->_helper->getBoite(), 
																			'//a', 'Premier volume');
	}
}



abstract class AbstractBibNumeriqueViewHelperWithAlbumHarlockTestCase extends ViewHelperTestCase {
	public function setUp() {
		parent::setUp();

		$harlock = Class_Album::getLoader()
			->newInstanceWithId(99)
			->setTitre('Harlock');

		$harlock->setRessources(array(Class_AlbumRessource::getLoader()
																	->newInstanceWithId(1)
																	->setLinkTo('cms/articleview/id/2')
																	->setFichier('1.gif')
																	->setTitre('Le capitaine')
																	->setDescription("Justicier de l'espace")
																	->setAlbum($harlock),
																	Class_AlbumRessource::getLoader()
																	->newInstanceWithId(2)
																	->setTitre('Le capitaine en slip')
																	->setFichier('2.gif')
																	->setAlbum($harlock),
																	Class_AlbumRessource::getLoader()
																	->newInstanceWithId(666)
																	->setFichier('8.doc')
																	->setAlbum($harlock),
																	Class_AlbumRessource::getLoader()
																	->newInstanceWithId(3)
																	->setFichier('3.gif')
																	->setAlbum($harlock),
																	Class_AlbumRessource::getLoader()
																	->newInstanceWithId(4)
																	->setFichier('4.gif')
																	->setAlbum($harlock)));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('find')
			->with(999)
			->answers($harlock);
	}
}




class BibNumeriqueAlbumTeaserTest extends AbstractBibNumeriqueViewHelperWithAlbumHarlockTestCase {
	/** @var ZendAfi_View_Helper_Accueil_BibNumerique */
	protected $_helper;


	/** @test */
	public function titleShouldBeToutesNotrePlusBelleBible() {
		$this->_buildHelperLimitedTo(2);
		$this->assertXPathContentContains($this->_helper->getBoite(), 
																			'//div[@class="titre"]//h1', 
																			'Notre plus belle bible');
	}

	/** @test */
	public function withFourImagesAndTwoLimitInPrefShouldRenderTwoImages() {
		$this->_buildHelperLimitedTo(2);
		$this->assertQueryCount($this->_helper->getBoite(), '//img', 2);
	}


	/** @test */
	public function withFourImagesAndGreaterLimitInPrefShouldRenderFourImages() {
		$this->_buildHelperLimitedTo(999);
		$this->assertQueryCount($this->_helper->getBoite(), '//img', 4);
	}


	/** @test */
	public function withNoLimitShouldRenderFourImages() {
		$this->_buildHelperLimitedTo('');
		$this->assertQueryCount($this->_helper->getBoite(), '//img', 4);
	}


	/** @test */
	public function withoutOrderInPrefShouldRespectInitialOrder() {
		$this->_buildHelperLimitedToFourOrderedBy('');
		$html = $this->_helper->getBoite();
		$this->_assertRespectOrder($html);
		return $html;
	}


	/**
	 * @depends withoutOrderInPrefShouldRespectInitialOrder
	 * @test 
	 */
	public function firstImageShouldLinkToArticleView($html) {
		$this->assertXPath($html,
											 '//a[@href="cms/articleview/id/2"]//img[contains(@src, "bib-numerique/thumbnail/width/400/id/1")]');
	}


	/**
	 * @depends withoutOrderInPrefShouldRespectInitialOrder
	 * @test 
	 */
	function firstImageTitleShouldContainsLeCapitaine($html) {
		$this->assertXPath($html,
											 '//a[@href="cms/articleview/id/2"]//img[@title="Le capitaine"]');
	}


	/**
	 * @depends withoutOrderInPrefShouldRespectInitialOrder
	 * @test 
	 */
	function firstImageAltShouldContainsJusticierDeL_Espace($html) {
		$this->assertXPath($html,
											 '//div[@class="contenu"]//div[1]//img[@alt="Justicier de l\'espace"]');
	}


	/**
	 * @depends withoutOrderInPrefShouldRespectInitialOrder
	 * @test 
	 */
	function secondImageShouldLinkToFullImagePrettyPhoto($html) {
		$this->assertXPath($html,
											 '//a[@href="/bib-numerique/get-resource/id/2"][@rel="prettyphoto[Harlock]"][@title="Le capitaine en slip"]',
											 $html);
	}


	/** @test */
	function pageShouldContainsCodeToLoadPrettyPhoto() {
		$this->assertXPath(Class_ScriptLoader::getInstance()->html(),
											 '//script[contains(@src,"prettyPhoto")]');
	}



	/** @test */
	public function withOrderRespectInPrefShouldRespectInitialOrder() {
		$this->_buildHelperLimitedToFourOrderedBy(Class_Systeme_ModulesAccueil_BibliothequeNumerique::ORDER_RESPECT);
		$this->_assertRespectOrder($this->_helper->getBoite());
	}

	
	/** @test */
	public function withOrderRandomInPrefShouldNotRespectInitialOrder() {
		$this->_buildHelperLimitedToFourOrderedBy(Class_Systeme_ModulesAccueil_BibliothequeNumerique::ORDER_RANDOM);
		$this->assertAnyXpath($this->_helper->getBoite(), 
													array('//div[@class="contenu"]//div[1]//img[not(contains(@src, "1.gif"))]',
																'//div[@class="contenu"]//div[2]//img[not(contains(@src, "2.gif"))]',
																'//div[@class="contenu"]//div[3]//img[not(contains(@src, "3.gif"))]',
																'//div[@class="contenu"]//div[4]//img[not(contains(@src, "4.gif"))]'));
	}


	/** @param int $limitCount */
	private function _buildHelperLimitedTo($limitCount) {
		$preferences = BibNumeriqueFixture::createOneAlbumTeaserPreferenceLimitedTo($limitCount);
		$this->_buildHelperWithPreferences($preferences);
	}

	
	/** @param string $orderMode */
	private function _buildHelperLimitedToFourOrderedBy($orderMode) {
		$preferences = BibNumeriqueFixture::createOneAlbumTeaserPreferenceLimitedTo(4);
		$preferences['order'] = $orderMode;
		$this->_buildHelperWithPreferences($preferences);
	}


	/** @param array $preferences*/
	private function _buildHelperWithPreferences(array $preferences) {
		$this->_helper = new ZendAfi_View_Helper_Accueil_BibNumerique(9, array(
		  'division' => 1,
			'type_module' => 'BIB_NUMERIQUE',
			'preferences' => $preferences,
		));
	}


	/** @param string $html */
	private function _assertRespectOrder($html) {
		$this->assertXPath($html, '//div[@class="contenu"]//div[1]//a//img[contains(@src, "bib-numerique/thumbnail/width/400/id/1")]');
		$this->assertXPath($html, '//div[@class="contenu"]//div[2]//img[contains(@src, "bib-numerique/thumbnail/width/400/id/2")]');
		$this->assertXPath($html, '//div[@class="contenu"]//div[3]//img[contains(@src, "bib-numerique/thumbnail/width/400/id/3")]');
		$this->assertXPath($html, '//div[@class="contenu"]//div[4]//img[contains(@src, "bib-numerique/thumbnail/width/400/id/4")]');
	}
}





class BibNumeriqueAlbumHarlockAsBookletTest extends AbstractBibNumeriqueViewHelperWithAlbumHarlockTestCase {
	public function setUp() {
		parent::setUp();
		$preferences = array('boite' => '',
												 'titre' => 'Harlock',
												 'id_categories' => '',
												 'id_albums' => '99',
												 'type_aff' => Class_Systeme_ModulesAccueil_BibliothequeNumerique::DISPLAY_ALBUM_TEASER,
												 'style_liste' => 'booklet');
		$helper = new ZendAfi_View_Helper_Accueil_BibNumerique(12, 		  
																													 array('division' => 1,
																																 'type_module' => 'BIB_NUMERIQUE',
																																 'preferences' => $preferences));
		Class_ScriptLoader::resetInstance();
		$this->html = $helper->getBoite();
	}


	/** @test */
	public function titleShouldBeHarlock() {
		$this->assertXPathContentContains($this->html,
																			'//div[@class="titre"]//h1', 
																			'Harlock');
	}


	/** @test */
	function titleShouldLinkToBookletId99() {
		$this->assertXPath($this->html, '//div[@class="titre"]//h1//a[@href="/bib-numerique/booklet/id/99"]', $this->html);
	}


	/** @test */
	public function bibNumAlbumContainerShouldHaveIdBooklet12() {
		$this->assertXPath($this->html, '//div[@class="bib-num-album"][@id="booklet_12"]');
	}


	/** @test */
	function pageShouldContainsCodeToOpenBooklet() {
		$this->assertContains("smalltalk.BibNumAlbum._load_in_scriptsRoot_('" . BASE_URL . "/bib-numerique/album/id/99.json', '#booklet_12', '" . BASE_URL . "/amber/afi/souvigny/')",
													Class_ScriptLoader::getInstance()->html());
	}


	/** @test */
	function loadInScriptsRootShouldBeIncludedOnce() {
		$this->assertEquals(1, substr_count(Class_ScriptLoader::getInstance()->html(),
																				'smalltalk.BibNumAlbum._load_in_scriptsRoot_'));
	}
}


class BibNumeriqueAlbumHarlockAsListeTest extends AbstractBibNumeriqueViewHelperWithAlbumHarlockTestCase {
	public function setUp() {
		parent::setUp();
		$preferences = array('boite' => '',
												 'titre' => 'Harlock',
												 'id_categories' => '',
												 'id_albums' => '99',
												 'nb_aff' => 2,
												 'op_largeur_img' => 150,
												 'op_hauteur_img' => 150,
												 'type_aff' => Class_Systeme_ModulesAccueil_BibliothequeNumerique::DISPLAY_ALBUM_TEASER,
												 'style_liste' => 'none');
		$helper = new ZendAfi_View_Helper_Accueil_BibNumerique(12, 		  
																													 array('division' => 1,
																																 'type_module' => 'BIB_NUMERIQUE',
																																 'preferences' => $preferences));
		$this->html = $helper->getBoite();
	}


	/** @test */
	function imageTitleShouldContainsLeCapitaine() {
		$this->assertXPath($this->html,
											 '//a[@href="cms/articleview/id/2"]//img[@title="Le capitaine"]',
											 $this->html);
	}
}



class BibNumeriqueFixture {
	/** @return Class_AlbumCategorie */
	public static function createBibleDeSouvigny() {
		return Class_AlbumCategorie::getLoader()
			->newInstanceWithId(1)
			->setLibelle('Bible de Souvigny')
			->setSousCategories(array(Class_AlbumCategorie::getLoader()
																->newInstanceWithId(2)
																->setLibelle('Les enluminures')
																->setAlbums(array(Class_Album::getLoader()
																									->newInstanceWithId(1)
																									->setTitre('Premier volume'))),
																Class_AlbumCategorie::getLoader()
																->newInstanceWithId(3)
																->setLibelle('Les plus belles calligraphies')
																->setAlbums(array())))
			->setAlbums(array());
	}

	
	/** @return array */
	public static function createOneCategorieTreePreference() {
		return array('boite' => '',
								 'titre' => 'Notre plus belle bible',
								 'id_categories' => '1',
								 'type_aff' => Class_Systeme_ModulesAccueil_BibliothequeNumerique::DISPLAY_TREE,
								 );
	}


	/** @return array */
	public static function createCategorieTwoTreePreference() {
		return array('boite' => '',
								 'titre' => 'Nos enluminures',
								 'id_categories' => '2',
								 'type_aff' => Class_Systeme_ModulesAccueil_BibliothequeNumerique::DISPLAY_TREE,
								 );
	}


	/** @return array */
	public static function createOneAlbumTeaserPreference() {
		return array('boite' => '',
								 'titre' => 'Notre plus belle bible',
								 'id_categories' => '',
								 'id_albums' => '999',
								 'nb_aff' => '2',
								 'type_aff' => Class_Systeme_ModulesAccueil_BibliothequeNumerique::DISPLAY_ALBUM_TEASER,
								 'order' => Class_Systeme_ModulesAccueil_BibliothequeNumerique::ORDER_RESPECT,
								 'style_liste' => 'diaporama',
								 'op_transition' => '',
								 'op_largeur_img' => '',
								 'op_hauteur_boite' => '',
								 'op_timeout' => '');
	} 


	/** 
	 * @param int $limitCount
	 * @return array 
	 */
	public static function createOneAlbumTeaserPreferenceLimitedTo($limitCount) {
		$prefs = self::createOneAlbumTeaserPreference();
		$prefs['nb_aff'] = $limitCount;
		return $prefs;
	}
}