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
require_once 'TelephoneAbstractControllerTestCase.php';

abstract class AbstractCmsControllerTelephoneTestCase extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Article::getLoader()
			->newInstanceWithId(4)
			->setTitre('Fete de la patate')
			->setDescription('A Annecy !');
	}
}




class CmsControllerTelephoneTest extends AbstractCmsControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('cms/articleview/id/4');
	}

	/** @test */
	function titleShouldDisplayFeteDeLaPatate() {
		$response = $this->_response;
		$this->assertXPathContentContains('//h1', 'Fete de la patate');
	}


	/** @test */
	function contentShouldBeAAnnecy() {
		$this->assertXPathContentContains('//div[@class="article pave"]', 'A Annecy !');
	}
}




class CmsControllerTelephoneEmbeddedTest extends AbstractCmsControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('embed/cms/articleview/id/4');
	}


	/** @test */
	function urlHomeShouldContainsEmbed() {
		$this->assertXPath('//a[contains(@href,"/embed")][@data-icon="home"]');
	}
}





class CmsControllerCalendarActionTest extends AbstractCmsControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('/telephone/cms/calendar?date=2011-10', true);
	}


	/** @test */
	public function pageShouldRenderOctober() {
		$this->assertXPathContentContains('//td[@class="calendar_title_month"]/a', 
																			"octobre"); 
	}
}




class CmsControllerArticleViewByDateActionTest extends AbstractCmsControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->answers(array(
											Class_Article::getLoader()
											->newInstanceWithId(1)
											->setTitre('La fête de la banane')
											->setContenu('Une fête qui glisse !')
											->setEventsDebut('2011-09-03')
											->setEventsFin('2011-10-03')
											->setCategorie(
																		 Class_ArticleCategorie::getLoader()->newInstanceWithId(1)
																		 ->setLibelle('Alimentaire')
																		 ->setBib(Class_Bib::getLoader()
																							->newInstanceWithId(1)
																							->setLibelle('Bonlieu'))
																		 ),
											Class_Article::getLoader()
											->newInstanceWithId(1)
											->setTitre('La fête de la frite')
											->setContenu('')
											->setEventsDebut('2011-09-03')
											->setEventsFin('2011-09-03')
											->setCategorie(
																		 Class_ArticleCategorie::getLoader()->newInstanceWithId(1)
																		 ->setLibelle('Alimentaire')
																		 ),
											));

		$this->dispatch('/telephone/cms/articleviewbydate?d=2011-10-01', true);
	}


	/** @test */
	public function feteDeLaBananeShouldBePresent() {
		$this->assertXpathContentContains('//ul//li//a', 'La fête de la banane');
	}


	/** @test */
	public function feteDeLaBananeAnchorShouldLinkToActionViewArticleOne() {
		$this->assertXpathContentContains('//ul//li//a[contains(@href, "cms/articleview/id/1")]', 
																			'La fête de la banane');
	}


	/** @test */
	public function toolbarUrlRetourShouldBeRoot() {
		$this->assertXPath('//div[@class="toolbar"]//a[@href="/"]');
	}
}

?>