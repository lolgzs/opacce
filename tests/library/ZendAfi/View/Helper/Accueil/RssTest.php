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

class OneRssViewHelperTest extends ViewHelperTestCase {
	/** @var string */
	protected $html;

	public function setUp() {
		parent::setUp();

		Class_Rss::getLoader()
			->newInstanceWithId(15)
			->setIdCat(11)
			->setIdNotice(86546)
			->setTitre('Internet Movie Data Base')
			->setDescription('Suivez toutes les actus ciné d\'IMDB, le site de référence mondial.')
			->setUrl('http://rss.imdb.com/news/')
			->setDateMaj('2010-04-01 10:47:58');


		$helper = new ZendAfi_View_Helper_Accueil_Rss(12, array(
			'division' => '1',
			'type_module' => 'RSS',
			'preferences' => array(
				'boite'					=> '',
				'titre'					=> 'Fils Rss',
				'type_aff'			=> '1',
				'id_categorie'	=> '',
				'id_items'			=> '15',
				'nb_aff'				=> '2',
			)
		));

		$this->html = $helper->getBoite();
	}


	/** @test */
	public function titleShouldBeFilsRss() {
		$this->assertXPathContentContains($this->html, '//div[@class="titre"]//h1', 'Fils Rss');
	}


	/** @test */
	public function rssTitleCountShouldBeOne() {
		$this->assertQueryCount($this->html, '//div[@class="contenu"]/h2', 1);
	}


	/** @test */
	public function htmlShouldContainScriptDeclaration() {
		$this->assertXpath($this->html, '//script[contains(@src, "rss.js")]');
	}


	/** @test */
	public function htmlShouldContainDomReadyDeclaration() {
		$this->assertXPathContentContains($this->html, 
																			'//script[@type="text/javascript"]', 
																			'$(document).ready(loadRssByContentName(\'div.rss_content_15\', 2, 12))');
	}


	/** @test */
	public function htmlShouldContainAjaxContainer() {
		$this->assertXpath($this->html, '//div[@class="rss_content_15"]//img[contains(@src, "patience.gif")]');
	}
}


class OneRssNullViewHelperTest extends ViewHelperTestCase {
	/** @var string */
	protected $html;

	public function setUp() {
		parent::setUp();

		$helper = new ZendAfi_View_Helper_Accueil_Rss(12, array(
			'division' => '1',
			'type_module' => 'RSS',
			'preferences' => array(
				'boite'					=> '',
				'titre'					=> 'Fils Rss',
				'type_aff'			=> '1',
				'id_categorie'	=> '',
				'id_items'			=> '12',
				'nb_aff'				=> '2',
			)
		));

		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Rss')
			->whenCalled('getFluxFromIdsAndCategories')
			->answers(array(null));

		$this->html = $helper->getBoite();
	}

	/** @test */
	public function rssTitleCountShouldBeZero() {
		$this->assertQueryCount($this->html, '//div[@class="contenu"]/h2', 0);
	}
}


abstract class RssHelperWithManyRssTestCase extends ViewHelperTestCase{
	public function setUp() {
		parent::setUp();

		$this->imdb_feed = Class_Rss::getLoader()
			->newInstanceWithId(15)
			->setIdCat(11)
			->setIdNotice(86546)
			->setTitre('Internet Movie Data Base')
			->setDescription('Suivez toutes les actus ciné d\'IMDB, le site de référence mondial.')
			->setUrl('http://rss.imdb.com/news/')
			->setDateMaj('2010-04-01 10:47:58');

		$this->lemonde_feed = Class_Rss::getLoader()
				->newInstanceWithId(16)
				->setIdCat(11)
				->setIdNotice(86547)
				->setTitre('Le monde.fr')
				->setDescription('Ze french quotidien, gauche oriented')
				->setUrl('http://rss.lemonde.fr/c/205/f/3052/index.rss')
				->setDateMaj('2010-12-25 10:47:58')
				->setTags('Journaux, international');


		$this->linuxfr = Class_Rss::getLoader()
				->newInstanceWithId(25)
				->setIdCat(11)
				->setIdNotice(12345)
				->setTitre('Linux FR')
				->setDescription('Da French Linux Page')
				->setUrl('http://linuxfr.org/feed.rss')
				->setDateMaj('2011-09-23 11:11:11')
				->setTags('Geeks');



		$this->cat_favoris = Class_RssCategorie::getLoader()
			->newInstanceWithId(11)
			->setLibelle('Flux préférés');
	}
}



class RssViewHelperGetCategoryTest extends RssHelperWithManyRssTestCase {
	protected $html;

	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Rss')
			->whenCalled('findAllBy')
			->with(array('role' => 'categorie', 'model' => $this->cat_favoris))
			->answers(array($this->imdb_feed, $this->lemonde_feed));



		$helper = new ZendAfi_View_Helper_Accueil_Rss(12, array(
			'division' => '1',
			'type_module' => 'RSS',
			'preferences' => array(
				'boite'					=> '',
				'titre'					=> 'Many Fils Rss',
				'type_aff'			=> '1',
				'id_categorie'	=> '11',
				'id_items'			=> '',
				'nb_aff'				=> '5',
			)
		));

		$this->html = $helper->getBoite();

	}

	/** @test */
	public function titleShouldBeManyFilsRss() {
		$this->assertXPathContentContains($this->html, '//div[@class="titre"]//h1', 'Many Fils Rss');
	}

	/** @test */
	public function rssTitleCountShouldBeTwo() {
		$this->assertQueryCount($this->html, '//div[@class="contenu"]/h2', 2);
	}
}



class RssViewHelperGetLastTest extends RssHelperWithManyRssTestCase {
	protected $html;

	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Rss')
			->whenCalled('getLastRss')
			->with(50)
			->answers(array($this->imdb_feed, $this->lemonde_feed));



		$helper = new ZendAfi_View_Helper_Accueil_Rss(12, array(
			'division' => '1',
			'type_module' => 'RSS',
			'preferences' => array(
				'boite'					=> '',
				'titre'					=> 'Derniers Fils Rss',
				'type_aff'			=> '2',
				'id_categorie'	=> '',
				'id_items'			=> '',
				'nb_aff'				=> '5',
			)
		));

		$this->html = $helper->getBoite();

	}

	/** @test */
	public function titleShouldBeManyFilsRss() {
		$this->assertXPathContentContains($this->html, '//div[@class="titre"]//h1', 'Derniers Fils Rss');
	}

	/** @test */
	public function rssTitleCountShouldBeTwo() {
		$this->assertQueryCount($this->html, '//div[@class="contenu"]/h2', 2);
	}
}



class RssViewHelperGetTwoLastTest extends RssHelperWithManyRssTestCase {
	protected $html;

	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Rss')
			->whenCalled('getLastRss')
			->with(50)
			->answers(array($this->imdb_feed, $this->lemonde_feed, $this->linuxfr));



		$helper = new ZendAfi_View_Helper_Accueil_Rss(12, array(
			'division' => '1',
			'type_module' => 'RSS',
			'preferences' => array(
				'boite'					=> '',
				'titre'					=> 'Derniers Fils Rss',
				'type_aff'			=> '2',
				'id_categorie'	=> '',
				'id_items'			=> '',
				'nb_aff'				=> '2',
			)
		));

		$this->html = $helper->getBoite();
	}


	/** @test */
	public function rssTitleCountShouldBeTwo() {
		$this->assertQueryCount($this->html, '//div[@class="contenu"]/h2', 2);
	}
}

?>