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

class ArticleLoaderGetArticlesByPreferencesTest extends ModelTestCase {
	const WHERE_VISIBLE_CLAUSE =
		'((DEBUT IS NULL) OR (DEBUT <= CURDATE())) AND ((FIN IS NULL) OR (FIN >= CURDATE()))';

	public function setUp() {
		$this->select = new Zend_Db_Table_Select(new Storm_Model_Table(array('name' => 'cms_article')));

		$this->tbl_articles = $this->_buildTableMock('Class_Article', array('select', 'fetchAll'));
		$this->tbl_articles
			->expects($this->any())
			->method('select')
			->will($this->returnValue($this->select));

		$this->tbl_articles
			->expects($this->any())
			->method('fetchAll')
			->will($this->returnValue($this->_buildRowset(array(array(
																													 'ID_ARTICLE' => 23,
																													 'ID_CAT' => 2,
																													 'TITRE' => 'Fête de la pomme',
																													 'DATE_CREATION' => '2011-04-02',
																													 'DEBUT' => '2011-10-02',
																													 'FIN' => '2011-10-22',
																													 'EVENTS_DEBUT' => '2011-10-20'),

																										 array('ID_ARTICLE' => 18,
																													 'ID_CAT' => 2,
																													 'TITRE' => 'Fête de la poire',
																													 'DATE_CREATION' => '2010-03-25',
																													 'DEBUT' => '2010-03-25',
																													 'EVENTS_DEBUT' => '2010-03-25'),

																										 array('ID_ARTICLE' => 55,
																													 'ID_CAT' => 4,
																													 'TITRE' => 'Fête de la banane',
																													 'DEBUT' => '2011-01-01',
																													 'DATE_CREATION' => '2011-05-01',
																													 'EVENTS_DEBUT' => '2011-03-25',
																													 'EVENTS_FIN' => '2011-03-27')))));

		Class_ArticleCategorie::getLoader()
			->newInstanceWithId(2)
			->setLibelle('Fêtes')
			->setSousCategories(array(Class_ArticleCategorie::getLoader()
																->newInstanceWithId(4)
																->setLibelle('Exotiques')
																->setSousCategories(array())));

	}


	public function assertSelect($expected) {
		$this->assertEquals("SELECT `cms_article`.* FROM `cms_article` ".$expected,
												str_replace("\n", "", $this->select->assemble()));
	}


	public function getArticles($prefs) {
		return Class_Article::getLoader()->getArticlesByPreferences($prefs);
	}


	/** @test */
	function withNoSelectionAnd5ArticlesDisplayedOrderedByDate() {
		$articles = $this->getArticles(array('display_order' => 'DateCreationDesc',
																				 'nb_aff' => 5));
		$this->assertSelect(sprintf("WHERE %s AND (PARENT_ID=0) ORDER BY `DATE_CREATION` DESC LIMIT 5",
																self::WHERE_VISIBLE_CLAUSE));
		return $articles;
	}


	/** @test */
	function withNoSelectionAnd5ArticlesDisplayedOrderedByDateAndLangueRO() {
		$articles = $this->getArticles(array('display_order' => 'DateCreationDesc',
																				 'nb_aff' => 5,
																				 'langue' => 'ro'));
		$this->assertSelect(sprintf("WHERE %s AND (LANGUE='ro') ORDER BY `DATE_CREATION` DESC LIMIT 5",
																self::WHERE_VISIBLE_CLAUSE));
		return $articles;
	}


	/** @test */
	function withNoSelectionAnd2ArticlesOn10DisplayedOrderedRandom() {
		$articles = $this->getArticles(array('display_order' => 'Random',
																				 'nb_analyse' => 10,
																				 'nb_aff' => 2));
		$this->assertSelect(sprintf("WHERE %s AND (PARENT_ID=0) ORDER BY `DATE_CREATION` DESC LIMIT 10",
																self::WHERE_VISIBLE_CLAUSE));
		return $articles;
	}



	/** @test */
	function withNoSelectionAndDisplayedOrderedByDateFirstShouldAlwaysBeFeteDeLaBanane() {
		for($i=0; $i<10; $i++) {
			$articles = $this->getArticles(array('display_order' => 'DateCreationDesc',
																					 'nb_aff' => 5));
			$this->assertEquals('Fête de la banane', array_first($articles)->getTitre());
		}
	}


	/** @test */
	function withDisplayOrderRandomArticlesShouldBeShuffled() {
		$articles = $this->getArticles(array('display_order' => 'Random',
																				 'nb_analyse' => 10,
																				 'nb_aff' => 2));
		for($i=0; $i<10; $i++) {
			$next = $this->getArticles(array('display_order' => 'Random',
																			 'nb_analyse' => 10,
																			 'nb_aff' => 2));
			if ($next[0] != $articles[0])
				return;
		}
		$this->assertFalse(true);
	}


	/**
	 * @test
	 * @depends withNoSelectionAnd2ArticlesOn10DisplayedOrderedRandom
	 */
	function withNoSelectionNbAffTwoShouldReturnTwoArticles($articles) {
		$this->assertEquals(2, count($articles));
	}


	/** @test */
	function withArticleSelectionAndNbAffOneFirstShouldBeDisplayedOrderedByEventDebut() {
		$articles = $this->getArticles(array('display_order' => 'EventDebut',
																				 'id_items' => '23-18-',
																				 'nb_aff' => 1));
		$this->assertSelect(sprintf("WHERE %s AND (id_article in (23,18)) ORDER BY FIELD(ID_ARTICLE, 23,18) ASC",
																self::WHERE_VISIBLE_CLAUSE));
		$this->assertEquals('Fête de la poire', array_first($articles)->getTitre());
		$this->assertEquals(1, count($articles));
	}



	/** @test */
	function withArticleSelectionAndNbAffOneOrderSelctionBothArticlesShouldBeVisible() {
		$articles = $this->getArticles(array('display_order' => 'Selection',
																				 'id_items' => '23-18-',
																				 'nb_aff' => 1));
		$this->assertSelect(sprintf("WHERE %s AND (id_article in (23,18)) ORDER BY FIELD(ID_ARTICLE, 23,18) ASC",
																self::WHERE_VISIBLE_CLAUSE));
		$this->assertEquals('Fête de la pomme', array_first($articles)->getTitre());
		$this->assertEquals('Fête de la banane', array_last($articles)->getTitre());
		$this->assertEquals(3, count($articles));
	}



	/** @test */
	function withArticleSelectionRandomAndNbAffOneShouldReturnOneArticle() {
		$articles = $this->getArticles(array('display_order' => 'Random',
																				 'nb_aff' => 1));
		$this->assertEquals(1, count($articles));
	}


	/** @test */
	function withCategorieSelectionDisplayedOrderedByDebutPublication() {
		$articles = $this->getArticles(array('display_order' => 'DebutPublicationDesc',
																				 'id_categorie' => '4-2',
																				 'nb_aff' => 2));
		$this->assertSelect(sprintf("WHERE %s AND (`cms_article`.ID_CAT in (4,2)) ORDER BY FIELD(`cms_article`.ID_CAT, 4,2) ASC",
												self::WHERE_VISIBLE_CLAUSE));
		$this->assertEquals('Fête de la pomme', array_first($articles)->getTitre());
		$this->assertEquals('Fête de la banane', array_at(1, $articles)->getTitre());
		$this->assertEquals(2, count($articles));
	}


	/** @test */
	function withSelectionCategoriesAndLangueFr() {
		$articles = $this->getArticles(array('id_categorie' => '4-2',
																				 'nb_aff' => 2,
																				 'langue' => 'ro'));
		$this->assertSelect(sprintf("WHERE %s AND (`cms_article`.ID_CAT in (4,2)) ORDER BY FIELD(`cms_article`.ID_CAT, 4,2) ASC",
																self::WHERE_VISIBLE_CLAUSE));
	}


	/** @test */
	function withObsoleteSelectionCategories() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('find')
			->with(99)
			->answers(null);

		$this->getArticles(array('id_categorie' => '9999-2',
														 'nb_aff' => 2));

		$this->assertSelect(sprintf("WHERE %s AND (`cms_article`.ID_CAT in (2,4)) ORDER BY FIELD(`cms_article`.ID_CAT, 2,4) ASC",
																self::WHERE_VISIBLE_CLAUSE));
	}


	/** @test */
	function withArticleAndCategorieSelectionSqlShouldBeOr() {
		$articles = $this->getArticles(array('display_order' => 'EventDebut',
																				 'id_items' => '23-18-',
																				 'id_categorie' => '4',
																				 'nb_aff' => 3));
		$this->assertSelect(sprintf("WHERE %s AND (id_article in (23,18) OR `cms_article`.ID_CAT in (4)) ORDER BY FIELD(`cms_article`.ID_CAT, 4) ASC, FIELD(ID_ARTICLE, 23,18) ASC",
																self::WHERE_VISIBLE_CLAUSE));
	}


	/** @test */
	public function withSelectionShouldBeOrderedByDateCreation() {
		$articles = $this->getArticles(array('display_order' => 'DateCreationDesc',
																				 'id_items' => '18-23-',
																				 'nb_aff' => 1));

		$this->assertSelect(sprintf("WHERE %s AND (id_article in (18,23)) ORDER BY FIELD(ID_ARTICLE, 18,23) ASC",
																self::WHERE_VISIBLE_CLAUSE));
		$this->assertEquals('Fête de la banane', array_first($articles)->getTitre());
		$this->assertEquals(1, count($articles));
	}


	/** @test */
	public function withMonthDateSqlShouldFilterEventsByMonth() {
		$articles = $this->getArticles(array('display_order' => 'EventDebut',
																				 'id_items' => '',
																				 'id_categorie' => '',
																				 'event_date' => '2011-03',
																				 'id_bib' => 0));
		$this->assertSelect(sprintf("WHERE %s AND (left(EVENTS_DEBUT,7) <= '2011-03') AND (left(EVENTS_FIN,7) >= '2011-03') AND (PARENT_ID=0) ORDER BY `DATE_CREATION` DESC",
																self::WHERE_VISIBLE_CLAUSE));
		$this->assertEquals(3, count($articles));
	}


	/** @test */
	public function withDayDateSqlShouldFilterEventsByDay() {
		$articles = $this->getArticles(array('display_order' => 'EventDebut',
																				 'id_items' => '',
																				 'id_categorie' => '',
																				 'event_date' => '2011-03-15',
																				 'id_bib' => 0));
		$this->assertSelect(sprintf("WHERE %s AND (EVENTS_DEBUT <= '2011-03-15') AND (EVENTS_FIN >= '2011-03-15') AND (PARENT_ID=0) ORDER BY `DATE_CREATION` DESC",
																self::WHERE_VISIBLE_CLAUSE));
	}


	/** @test */
	public function withIdBibGivenShouldFilterByIdSite() {
		$articles = $this->getArticles(array('display_order' => 'EventDebut',
																				 'id_items' => '',
																				 'id_categorie' => '',
																				 'event_date' => '2011-03',
																				 'id_bib' => 5 ));
		$this->assertSelect(sprintf("INNER JOIN `cms_categorie` ON cms_categorie.ID_CAT = cms_article.ID_CAT WHERE %s AND (cms_categorie.ID_SITE=5) AND (left(EVENTS_DEBUT,7) <= '2011-03') AND (left(EVENTS_FIN,7) >= '2011-03') AND (PARENT_ID=0) ORDER BY `DATE_CREATION` DESC",
																self::WHERE_VISIBLE_CLAUSE));
	}


	/** @test */
	public function withStatusShouldFilterByStatus() {
		$articles = $this->getArticles(array('status' => Class_Article::STATUS_ARCHIVED));
		$this->assertSelect(sprintf('WHERE %s AND (PARENT_ID=0) AND (STATUS = 4) ORDER BY `DATE_CREATION` DESC', self::WHERE_VISIBLE_CLAUSE));
	}


	/** @test */
	public function withEventsOnlyShouldFilterOnEventsDates() {
		$article = $this->getArticles(array('events_only' => true));
		$this->assertSelect(sprintf('WHERE %s AND (EVENTS_DEBUT IS NOT NULL) AND (EVENTS_FIN IS NOT NULL) AND (PARENT_ID=0) ORDER BY `DATE_CREATION` DESC', self::WHERE_VISIBLE_CLAUSE));
	}


	/** @test */
	public function withPublishedFalseShouldNotFilterByDebutAndFin() {
		$article = $this->getArticles(array('published' => false));
		$this->assertSelect('WHERE (PARENT_ID=0) ORDER BY `DATE_CREATION` DESC');
	}
}




abstract class ArticleLoaderGroupByBibTestCase extends ModelTestCase {
	/** @var array */
	protected $articles;

	protected function setUp() {
		parent::setUp();

		$this->articles = ArticleLoader::groupByBib($this->_getArticlesFixture());
	}

}




class ArticleLoaderGroupByBibWithoutBibTest extends ArticleLoaderGroupByBibTestCase {
	/** @test */
	public function sizeOfArrayShouldBeOne() {
		$this->assertEquals(1, count($this->articles));
	}

	/** @test */
	public function keyShouldBeEmptyString() {
		$this->assertEquals('', key($this->articles));
	}

	/** @test */
	public function valueShouldBeSourceArray() {
		$this->assertEquals($this->_getArticlesFixture(), current($this->articles));
	}

	/**
	 * @return array
	 */
	protected function _getArticlesFixture() {
		return array(
			Class_Article::getLoader()
				->newInstanceWithId(1)
				->setTitre('Un article merveilleux'),
			Class_Article::getLoader()
				->newInstanceWithId(2)
				->setTitre('Un article moins merveilleux que le précédent'),
		);
	}
}




class ArticleLoaderGroupByBibWithOneBibTest
extends ArticleLoaderGroupByBibTestCase {
	/** @test */
	public function sizeOfArrayShouldBeOne() {
		$this->assertEquals(1, count($this->articles));
	}


	/** @test */
	public function keyShouldBeBonlieu() {
		$this->assertEquals('Bonlieu', key($this->articles));
	}


	/** @test */
	public function valueShouldBeSourceArray() {
		$this->assertEquals($this->_getArticlesFixture(), current($this->articles));
	}


	/**
	 * @return array
	 */
	protected function _getArticlesFixture() {
		return array(
								 Class_Article::getLoader()
								 ->newInstanceWithId(1)
								 ->setTitre('Un article merveilleux')
								 ->setCategorie(
																Class_ArticleCategorie::getLoader()
																->newInstanceWithId(1)
																->setLibelle('Alimentation')
																->setBib(
																				 Class_Bib::getLoader()
																				 ->newInstanceWithId(1)
																				 ->setLibelle('Bonlieu')
																				 )
																),
								 Class_Article::getLoader()
								 ->newInstanceWithId(2)
								 ->setTitre('Un article moins merveilleux que le précédent')
								 ->setCategorie(
																Class_ArticleCategorie::getLoader()
																->newInstanceWithId(1)
																->setLibelle('Alimentation')
																->setBib(
																				 Class_Bib::getLoader()
																				 ->newInstanceWithId(1)
																				 ->setLibelle('Bonlieu')
																				 )
																),
								 );
	}
}




class ArticleLoaderGroupByBibWithTwoBibTest
extends ArticleLoaderGroupByBibTestCase {
	/** @test */
	public function sizeOfArrayShouldBeTwo() {
		$this->assertEquals(2, count($this->articles));
	}


	/** @test */
	public function keysShouldBeBonlieuAndLaTurbine() {
		$this->assertEquals(array('Bonlieu', 'La Turbine'), array_keys($this->articles));
	}


	/** @test */
	public function firstArticleShouldBeGroupedUnderBonlieu() {
		$fixtures = $this->_getArticlesFixture();
		$this->assertEquals($fixtures[0], $this->articles['Bonlieu'][0]);
	}


	/** @test */
	public function secondArticleShouldBeGroupedUnderLaTurbine() {
		$fixtures = $this->_getArticlesFixture();
		$this->assertEquals($fixtures[1], $this->articles['La Turbine'][0]);
	}


	/**
	 * @return array
	 */
	protected function _getArticlesFixture() {
		return array(
								 Class_Article::getLoader()
								 ->newInstanceWithId(1)
								 ->setTitre('Un article merveilleux')
								 ->setCategorie(
																Class_ArticleCategorie::getLoader()
																->newInstanceWithId(1)
																->setLibelle('Alimentation')
																->setBib(
																				 Class_Bib::getLoader()
																				 ->newInstanceWithId(1)
																				 ->setLibelle('Bonlieu')
																				 )
																),
								 Class_Article::getLoader()
								 ->newInstanceWithId(2)
								 ->setTitre('Un article moins merveilleux que le précédent')
								 ->setCategorie(
																Class_ArticleCategorie::getLoader()
																->newInstanceWithId(1)
																->setLibelle('Alimentation')
																->setBib(
																				 Class_Bib::getLoader()
																				 ->newInstanceWithId(2)
																				 ->setLibelle('La Turbine')
																				 )
																),
								 );
	}
}




class ArticleLoaderGroupByBibWithAndWithoutBibTest
extends ArticleLoaderGroupByBibTestCase {
	/** @test */
	public function sizeOfArrayShouldBeTwo() {
		$this->assertEquals(2, count($this->articles));
	}


	/** @test */
	public function keysShouldBePortailAndLaTurbine() {
		$this->assertEquals(array('Portail', 'La Turbine'), array_keys($this->articles));
	}


	/** @test */
	public function firstArticleShouldBeGroupedUnderPortail() {
		$fixtures = $this->_getArticlesFixture();
		$this->assertEquals($fixtures[0], $this->articles['Portail'][0]);
	}


	/** @test */
	public function secondArticleShouldBeGroupedUnderLaTurbine() {
		$fixtures = $this->_getArticlesFixture();
		$this->assertEquals($fixtures[1], $this->articles['La Turbine'][0]);
	}


	/**
	 * @return array
	 */
	protected function _getArticlesFixture() {
		return array(
								 Class_Article::getLoader()
								 ->newInstanceWithId(1)
								 ->setTitre('Un article merveilleux')
								 ->setCategorie(
																Class_ArticleCategorie::getLoader()
																->newInstanceWithId(1)
																->setLibelle('Alimentation')
																),
								 Class_Article::getLoader()
								 ->newInstanceWithId(2)
								 ->setTitre('Un article moins merveilleux que le précédent')
								 ->setCategorie(
																Class_ArticleCategorie::getLoader()
																->newInstanceWithId(2)
																->setLibelle('Électronique')
																->setBib(
																				 Class_Bib::getLoader()
																				 ->newInstanceWithId(2)
																				 ->setLibelle('La Turbine')
																				 )
																),
								 );
	}
}
?>