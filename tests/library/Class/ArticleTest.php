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

class ArticleWithTraductionsTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		$this->concert = Class_Article::getLoader()
			->newInstanceWithId(4)
			->setTitre('Erik Truffaz en concert')
			->setDescription('Venez nombreux')
			->setContenu('à Bonlieu')
			->setEventsDebut('2011-03-27')
			->setEventsFin('2011-03-27')
			->setLangue('fr');

		$this->concert_anglais = Class_Article::getLoader()
			->newInstanceWithId(41)
			->setParentId(4)
			->setTitre('Erik Truffaz live')
			->setDescription('Waiting for you !')
			->setContenu('at Bonlieu')
			->setLangue('en');


		$this->concert_roumain = Class_Article::getLoader()
			->newInstanceWithId(42)
			->setParentId(4)
			->setTitre('Erik în concert')
			->setDescription('Mulţi vin')
			->setContenu('la Bonlieu')
			->setLangue('ro');


		$art_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article');

		$art_wrapper
			->whenCalled('findAllBy')
			->with(array('role' => 'article_original', 'model' => $this->concert))
			->answers(array($this->concert_anglais, $this->concert_roumain));

		$art_wrapper
			->whenCalled('findAllBy')
			->with(array('role' => 'article_original', 'model' => $this->concert_roumain))
			->answers(array());

		$art_wrapper
			->whenCalled('findAllBy')
			->with(array('role' => 'article_original', 'model' => $this->concert_anglais))
			->answers(array());

		$art_wrapper
			->whenCalled('delete')
			->answers(true);

		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
	}


	/** @test */
	function concertToJSON() {
		$this->assertEquals('{"id":4,"label":"Erik Truffaz en concert"}',
												$this->concert->toJSON());
	}


	/** @test */
	function concertRoumainToJSON() {
		$this->assertEquals(json_encode(array('id' => 42, 'label' => "Erik în concert")),
												$this->concert_roumain->toJSON());
	}



	/** @test */
	function withWorkflowStatusConcertAnglaisShouldBeSameAsParent() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur(1);
		$this->concert_anglais->setStatus(1);
		$this->concert->setStatus(4);
		$this->assertEquals(4, $this->concert_anglais->getStatus());
	}


	/** @test */
	function loaderIdFieldShouldBeLowercaseID_ARTICLE() {
		$this->assertEquals('id_article', Class_Article::getLoader()->getIdField());
	}


	/** @test */
	function concertGetTraductionsShouldReturnConcertAnglaisAndRoumain() {
		$this->assertEquals(array($this->concert_anglais, $this->concert_roumain),
												$this->concert->getTraductions());
	}


	/** @test */
	function concertAnglaisGetArticleOriginalShouldReturnConcert() {
		$this->assertEquals($this->concert, $this->concert_anglais->getArticleOriginal());
	}


	/** @test */
	public function concertFirstImageURLShouldReturnEmptyString() {
		$this->assertEquals('', $this->concert->getFirstImageURL());
	}


	/** @test */
	public function concertStatusShouldBeValidated() {
		$this->assertEquals(3, $this->concert->getStatus());
	}

	/** @test */
	function concertAnglaisDescriptionShouldReturnWaitingForYou() {
		$this->assertEquals('Waiting for you !', $this->concert_anglais->getDescription());
	}


	/** @test */
	function concertAnglasContenuShouldReturnAtBonlieu() {
		$this->assertEquals('at Bonlieu', $this->concert_anglais->getContenu());
	}


	/** @test */
	function concertAnglaisShouldReturnErikTruffazLive() {
		$this->assertEquals('Erik Truffaz live', $this->concert_anglais->getTitre());
	}


	/** @test */
	function concertAnglaisSetEventDebutShouldReturn2011_03_27() {
		$this->assertEquals('2011-03-27', $this->concert_anglais->getEventsDebut());
	}


	/** @test */
	function concertGetTraductionLangueENShouldReturnConcertAnglais() {
		$this->assertEquals($this->concert_anglais,
												$this->concert->getTraductionLangue('en'));
	}


	/** @test */
	function concertRoGetTraductionLangueENShouldReturnConcertAnglais() {
		$this->assertEquals($this->concert_anglais,
												$this->concert_roumain->getTraductionLangue('en'));
	}


	/** @test */
	function concertRoGetOrCreateTraductionLangueCNShouldReturnArticleWithLangueCN() {
		$this->assertEquals('cn',
												$this->concert_roumain->getOrCreateTraductionLangue('cn')->getLangue());
	}


	/** @test */
	function concertGetOrCreateTraductionLangueCNShouldReturnNewArticle () {
		$concert_cn = $this->concert->getOrCreateTraductionLangue('cn');
		$this->assertTrue($concert_cn->isNew());
		return $concert_cn;
	}


	/**
	 * @test
	 * @depends concertGetOrCreateTraductionLangueCNShouldReturnNewArticle
	 */
	function newArticleLangueShouldBeCN($concert_cn) {
		$this->assertEquals('cn', $concert_cn->getLangue());
	}


	/**
	 * @test
	 * @depends concertGetOrCreateTraductionLangueCNShouldReturnNewArticle
	 */
	function newArticleOriginalShouldBeFR($concert_cn) {
		$this->assertEquals('Erik Truffaz en concert',
												$concert_cn->getArticleOriginal()->getTitre());
	}



	/** @test */
	function concertGetTraductionLangueFRShouldReturnItself() {
		$this->assertEquals($this->concert, $this->concert->getTraductionLangue('fr'));
	}


	/** @test */
	function concertAnglaisGetTraductionLangueROShouldReturnConcertRoumain() {
		$this->assertEquals($this->concert_roumain,
												$this->concert_anglais->getTraductionLangue('ro'));
	}


	/** @test */
	function concertRoumainGetTraductionFRShouldReturnConcertOriginal() {
		$this->assertEquals($this->concert, $this->concert_roumain->getTraductionLangue('FR'));
	}


	/** @test */
	function setContenuRoumainShouldDoIt() {
		$this->concert_roumain->setContenu('Vizitaţi Bonlieu');
		$this->assertEquals('Vizitaţi Bonlieu',
												$this->concert_roumain->getContenu());
		return $this->concert_roumain;
	}


	/**
	 * @test
	 * @depends setContenuRoumainShouldDoIt
	 */
	function concertOriginalContenuShouldBeKept($concert_roumain) {
		$this->assertEquals('à Bonlieu', $concert_roumain->getArticleOriginal()->getContenu());
	}


	/** @test */
	function setEventsDebutToConcertRoumainShouldDoIt() {
		$this->concert_roumain->setEventsDebut('2011-07-21');
		$this->assertEquals('2011-07-21', $this->concert_roumain->getEventsDebut());
		return $this->concert_roumain;
	}


	/**
	 * @test
	 * @depends setEventsDebutToConcertRoumainShouldDoIt
	 */
	function concertOriginalEventsDebutShouldBe2011_07_21($concert_roumain) {
		$this->assertEquals('2011-07-21',
												$concert_roumain->getArticleOriginal()->getEventsDebut());
	}


	/** @test */
	function newArticleTitreShouldBeEmpty() {
		$new_article = new Class_Article();
		$this->assertEquals('', $new_article->getTitre());
	}


	/** @test */
	function withEmptyTitreShouldNotBeValid() {
		$this->assertContains("Vous devez compléter le champ 'Titre'",
													$this->concert->setTitre('')->validate()->getErrors());
	}


	/** @test */
	function withHundredCharactersTitreShouldNotBeValid() {
		$this->assertContains("Le champ 'Titre' doit être inférieur à 200 caractères",
													$this->concert
													->setTitre('Ac turpis quis! Pulvinar a! Pid adipiscing, '.
																		 'natoque ultrices! Lacus purus vel montes cum? '.
																		 'Augue parturient porta placerat dapibus! Magna '.
																		 'Ac turpis quis! Pulvinar a! Pid adipiscing, '.
																		 'natoque ultrices! Lacus purus vel montes cum? '.
																		 'Augue parturient porta placerat dapibus! Magna '.
																		 'porttitor aliquet ')->validate()->getErrors());
	}


	/** @test */
	function withEmptyContenuShouldNotBeValid() {
		$this->assertContains("Vous devez compléter le champ 'Contenu'",
													$this->concert->setContenu('')->validate()->getErrors());
	}


	/** @test */
	function withDateDebutAfterDateFinShouldNotBeValid() {
		$this->assertContains("La date de début de publication doit être plus récente que la date de fin",
													$this->concert->setDebut('03/10/2010')->setFin('02/10/2010')->validate()->getErrors());
	}


	/** @test */
	function withEventDebutAfterEventFinShouldNotBeValid() {
		$this->assertContains("La date de début d'évènement doit être plus récente que la date de fin",
													$this->concert->setEventsDebut('03/10/2010')->setEventsFin('02/10/2010')->validate()->getErrors());
	}


	/** @test */
	function withDateDebutInvalidShouldNotBeValid() {
		$this->assertContains("La date de 'Début' n'est pas valide",
													$this->concert->setDebut('32/18/2010')->validate()->getErrors());
	}


	/** @test */
	function withDateFinInvalidShouldNotBeValid() {
		$this->assertContains("La date de 'Fin' n'est pas valide",
													$this->concert->setFin('32/18/2010')->validate()->getErrors());
	}


	/** @test */
	function withEventDebutInvalidShouldNotBeValid() {
		$this->assertContains("La date de 'Début évènement' n'est pas valide",
													$this->concert->setEventsDebut('32/18/2010')->validate()->getErrors());
	}

	/** @test */
	function withEventFinInvalidShouldNotBeValid() {
		$this->assertContains("La date de 'Fin évènement' n'est pas valide",
													$this->concert->setEventsFin('32/18/2010')->validate()->getErrors());
	}


	/** @test */
	function newArticleLangueShouldBeFR() {
		$new_article = new Class_Article();
		$this->assertEquals('fr', $new_article->getLangue());
	}


	/** @test */
	function deleteConcertShouldCascadeDelete() {
		$this->concert->delete();

		$wrapper = Class_Article::getLoader();
		foreach(array($this->concert, $this->concert_anglais, $this->concert_roumain) as $concert)
			$this->assertTrue($wrapper->methodHasBeenCalledWithParams('delete', array($concert)));
	}
}


class ArticleTestSmallArticle extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('0');
		$this->_article = ArticleTestFixtures::smallArticle();
	}

	/** @test */
	public function summaryShouldBeContenu() {
		$this->assertEquals($this->_article->getContenu(),
												$this->_article->getSummary());
	}

	/** @test */
	public function fullContentShouldBeContenu() {
		$this->assertEquals($this->_article->getContenu(),
												$this->_article->getFullContent());
	}

	/** @test */
	public function hasSummaryShouldReturnFalse(){
		$this->assertFalse($this->_article->hasSummary());
	}

}

class ArticleTestSummaryAndFullContentForArticleWithEndTag extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::endtagArticle();
	}

	/** @test */
	public function summaryShouldBeContenuBeforeEndTag() {
		$this->assertEquals('à Boussy',
												$this->_article->getSummary());
	}

	/** @test */
	public function fullContentShouldBeContenu(){
		$this->assertEquals('à Boussy  venez nombreux!',
												$this->_article->getFullContent());
	}

	/** @test */
	public function hasSummaryShouldReturnTrue(){
		$this->assertTrue($this->_article->hasSummary());
	}
}


class ArticleTestSummaryAndFullContentForArticleWithDescription extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::descriptionArticle();
	}

	/** @test */
	public function summaryShouldBeDescription() {
		$this->assertEquals('à Boussy',
												$this->_article->getSummary());
	}

	/** @test */
	public function fullContentShouldBeContenu() {
		$this->assertEquals('venez nombreux!',
												$this->_article->getFullContent());
	}

	/** @test */
	public function hasSummaryShouldReturnTrue(){
		$this->assertTrue($this->_article->hasSummary());
	}


}

class ArticleTestSummaryAndFullContentForArticleWithEmptyDescription extends Storm_Test_ModelTestCase {
	/** @var Class_Article  */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::emptyDescriptionArticle();
	}

	/** @test */
	public function summaryShouldBeContenu() {
		$this->assertEquals('venez nombreux!',
												$this->_article->getSummary());
	}

	/** @test */
	public function fullContentShouldBeContenu() {
		$this->assertEquals('venez nombreux!',
												$this->_article->getFullContent());
	}

	/** @test */
	public function hasSummaryShouldReturnFalse(){
		$this->assertFalse($this->_article->hasSummary());
	}
}

class ArticleTestEmptyDates extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::emptyDatesArticle();
	}

	/** @test */
	public function withoutWorkflowIsVisibleShouldReturnTrue() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertTrue($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusNonValidatedIsVisibleShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beDraft();
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusValidatedIsVisibleShouldReturnTrue() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beValidated();
		$this->assertTrue($this->_article->isVisible());
	}
}

class ArticleTestFutureStartDate extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::futureStartDateArticle();
	}

	/** @test */
	public function withoutWorkflowIsVisibleShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusNonValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beDraft();
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beValidated();
		$this->assertFalse($this->_article->isVisible());
	}
}

class ArticleTestPastStartDate extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::pastStartDateArticle();
	}

	/** @test */
	public function withoutWorkflowIsVisibleShouldReturnTrue() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertTrue($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusNonValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beDraft();
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusValidatedShouldReturnTrue() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beValidated();
		$this->assertTrue($this->_article->isVisible());
	}

}

class ArticleTestFutureEndDate extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::futureEndDateArticle();
	}

	/** @test */
	public function withoutWorkflowIsVisibleShouldReturnTrue() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertTrue($this->_article->isVisible());
	}

	/** @test */
	public function withoutWorkflowStatusShouldBeValidated() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertEquals(3, $this->_article->getStatus());
	}

	/** @test */
	public function withWorkflowAndStatusNonValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beDraft();
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusValidatedShouldReturnTrue() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beValidated();
		$this->assertTrue($this->_article->isVisible());
	}
}

class ArticleTestPastEndDate extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::pastEndDateArticle();
	}

	/** @test */
	public function isVisibleShouldReturnFalse() {
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withoutWorkflowIsVisibleShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusNonValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beDraft();
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beValidated();
		$this->assertFalse($this->_article->isVisible());
	}
}

class ArticleTestAllPastDates extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::allPastDatesArticle();
	}

	/** @test */
	public function isVisibleShouldReturnFalse() {
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withoutWorkflowIsVisibleShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusNonValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beDraft();
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beValidated();
		$this->assertFalse($this->_article->isVisible());
	}
}

class ArticleTestAllFutureDates extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::allFutureDatesArticle();
	}

	/** @test */
	public function isVisibleShouldReturnFalse() {
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withoutWorkflowIsVisibleShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusNonValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beDraft();
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beValidated();
		$this->assertFalse($this->_article->isVisible());
	}
}

class ArticleTestDatesIncludesNow extends Storm_Test_ModelTestCase {
	/** @var Class_Article */
	protected $_article;

	protected function setUp() {
		parent::setUp();
		$this->_article = ArticleTestFixtures::datesIncludesNowArticle();
	}

	/** @test */
	public function withoutWorkflowIsVisibleShouldReturnTrue() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertTrue($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusNonValidatedShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beDraft();
		$this->assertFalse($this->_article->isVisible());
	}

	/** @test */
	public function withWorkflowAndStatusValidatedShouldReturnTrue() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->_article->beValidated();
		$this->assertTrue($this->_article->isVisible());
	}
}

class ArticleTestFixtures {
	/**
	 * @return Class_Article
	 */
	public static function datesIncludesNowArticle() {
		$date = new DateTime();
		$date->modify('-1 month');

		$end = clone $date;
		$end->modify('+2 month');

		return Class_Article::getLoader()->newInstance()
																		->setDebut($date->format(DateTime::ISO8601))
																		->setFin($end->format(DateTime::ISO8601));
	}

	/**
	 * @return Class_Article
	 */
	public static function allFutureDatesArticle() {
		$date = new DateTime();
		$date->modify('+1 month');

		$end = clone $date;
		$end->modify('+1 day');

		return Class_Article::getLoader()->newInstance()
																		->setDebut($date->format(DateTime::ISO8601))
																		->setFin($end->format(DateTime::ISO8601));
	}

	/**
	 * @return Class_Article
	 */
	public static function allPastDatesArticle() {
		$date = new DateTime();
		$date->modify('-1 month');

		$end = clone $date;
		$end->modify('+1 day');

		return Class_Article::getLoader()->newInstance()
																		->setDebut($date->format(DateTime::ISO8601))
																		->setFin($end->format(DateTime::ISO8601));
	}

	/**
	 * @return Class_Article
	 */
	public static function pastEndDateArticle() {
		$date = new DateTime();
		$date->modify('-1 month');

		return Class_Article::getLoader()->newInstance()
																		->setDebut('')
																		->setFin($date->format(DateTime::ISO8601));
	}

	/**
	 * @return Class_Article
	 */
	public static function futureEndDateArticle() {
		$date = new DateTime();
		$date->modify('+1 month');

		return Class_Article::getLoader()->newInstance()
																		->setDebut('')
																		->setFin($date->format(DateTime::ISO8601));
	}


	/**
	 * @return Class_Article
	 */
	public static function pastStartDateArticle() {
		$date = new DateTime();
		$date->modify('-1 month');

		return Class_Article::getLoader()->newInstance()
																		->setDebut($date->format(DateTime::ISO8601))
																		->setFin('');
	}

	/**
	 * @return Class_Article
	 */
	public static function futureStartDateArticle() {
		$date = new DateTime();
		$date->modify('+1 month');

		return Class_Article::getLoader()->newInstance()
																		->setDebut($date->format(DateTime::ISO8601))
																		->setFin('');
	}

	/**
	 * @return Class_Article
	 */
	public static function emptyDatesArticle() {
		return Class_Article::getLoader()->newInstance()
																		->setDebut('')
																		->setFin('');
	}

	/**
	 * @return Class_Article
	 */
	public static function smallArticle() {
		return Class_Article::getLoader()->newFromRow(array(
																			'ID_ARTICLE' => 0,
																			'TITRE' => 'Fête de la pomme',
																			'CONTENU' => 'à Boussy',
																			'DEBUT' => '2010-10-02T00:00:00+02:00',
																			'FIN' => '2010-10-03T00:00:00+02:00',
																			'EVENTS_DEBUT' => null,
																			'EVENTS_FIN' => '20/12/2010',
																			'STATUS' => null,
																		));
	}

	/**
	 * @return Class_Article
	 */
	public static function endtagArticle() {
		return Class_Article::getLoader()->newFromRow(array(
																			'ID_ARTICLE' => 0,
																			'TITRE' => 'Fête de la pomme',
																			'CONTENU' => 'à Boussy {FIN} venez nombreux!',
																			'DEBUT' => '2010-10-02T00:00:00+02:00',
																			'FIN' => '2010-10-03T00:00:00+02:00',
																			'EVENTS_DEBUT' => null,
																			'EVENTS_FIN' => '20/12/2010'
																		));
	}

	/**
	 * @return Class_Article
	 */
	public static function descriptionArticle() {
		return Class_Article::getLoader()->newFromRow(array(
																			'ID_ARTICLE' => 0,
																			'TITRE' => 'Fête de la pomme',
																			'DESCRIPTION' => 'à Boussy',
																			'CONTENU' => 'venez nombreux!',
																			'DEBUT' => '2010-10-02T00:00:00+02:00',
																			'FIN' => '2010-10-03T00:00:00+02:00',
																			'EVENTS_DEBUT' => null,
																			'EVENTS_FIN' => '20/12/2010'
																		));
	}

	/**
	 * @return Class_Article
	 */
	public static function emptyDescriptionArticle() {
		return Class_Article::getLoader()->newFromRow(array(
																			'ID_ARTICLE' => 0,
																			'TITRE' => 'Fête de la pomme',
																			'DESCRIPTION' => '',
																			'CONTENU' => 'venez nombreux!',
																			'DEBUT' => '2010-10-02T00:00:00+02:00',
																			'FIN' => '2010-10-03T00:00:00+02:00',
																			'EVENTS_DEBUT' => null,
																			'EVENTS_FIN' => '20/12/2010'
																		));
	}

}