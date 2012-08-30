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

class ModoControllerIndexActionTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/modo/', true);
	}


	/** @test */
	public function controllerShouldBeModo() {
		$this->assertController('modo');
	}


	/** @test */
	public function actionShouldBeIndex() {
		$this->assertAction('index');
	}


	/** @test */
	public function linkToModerateAvisCmsShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "/modo/aviscms")]');
	}


	/** @test */
	public function linkToModerateAvisNoticeShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "/modo/avisnotice")]');
	}


	/** @test */
	public function linkToModerateTagsShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "/modo/tagnotice")]');
	}


	/** @test */
	public function linkToModerateSuggestionAchatShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "/admin/modo/suggestion-achat")]');
	}
}



abstract class ModoControllerSuggestionAchatTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SuggestionAchat')
			->whenCalled('findAllBy')
			->with(['order' => 'date_creation'])
			->answers([
								 Class_SuggestionAchat::newInstanceWithId(2)
								 ->setDateCreation('2012-03-01')
								 ->setTitre('Harry Potter')
								 ->setAuteur('J.K.Rowling')
								 ->setIsbn('1234567890')
								 ->setDescriptionUrl('http://harrypotter.fr')
								 ->setCommentaire('Je veux le lire'),

								 Class_SuggestionAchat::newInstanceWithId(3)
								 ->setDateCreation('2012-03-02')
								 ->setTitre('Millenium')
								 ->setAuteur('Stieg Larsson')
								 ]);
	}
}



class ModoControllerSuggestionAchatActionTest extends ModoControllerSuggestionAchatTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/modo/suggestion-achat', true);
	}


	/** @test */
	public function titleShouldBeModerationDesSuggestionsAchat() {
		$this->assertXPathContentContains('//h1', 'Modération des suggestions d\'achat');
	}


	/** @test */
	public function firstRowTDShouldContainsHarryPotter() {
		$this->assertXPathContentContains('//tr[1]//td', 'Harry Potter');		
	}


	/** @test */
	public function firstRowTDShouldContainsJKRowling() {
		$this->assertXPathContentContains('//tr[1]//td', 'J.K.Rowling');		
	}


	/** @test */
	public function firstRowTDShouldContainsDateCreation2012_03_01() {
		$this->assertXPathContentContains('//tr[1]//td', '2012-03-01');		
	}


	/** @test */
	function firstRowTDShouldHaveLinkToEdit() {
		$this->assertXPath('//tr[1]//a[contains(@href, "suggestion-achat-edit/id/2")]');
	}


	/** @test */
	function firstRowTDShouldHaveLinkToDelete() {
		$this->assertXPath('//tr[1]//a[contains(@href, "suggestion-achat-delete/id/2")]');
	}


	/** @test */
	public function secondRowTDShouldContainsMillenium() {
		$this->assertXPathContentContains('//tr[2]//td', 'Millenium');		
	}
}


class ModoControllerSuggestionAchatEditHarryPotterTest extends ModoControllerSuggestionAchatTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/modo/suggestion-achat-edit/id/2', true);
	}


	/** @test */
	public function formShouldContainsInputForTitre() {
		$this->assertXPath('//form[@id="suggestion"]//input[@name="titre"]');
	}


	/** @test */
	public function formShouldNotHaveSubmitButton() {
		$this->assertNotXPath('//form//input[@type="submit"]');
	}
}



class ModoControllerSuggestionAchatEditUnknownTest extends ModoControllerSuggestionAchatTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SuggestionAchat')
				->whenCalled('find')
				->with(99)
				->answers(null);
		
		$this->dispatch('/admin/modo/suggestion-achat-edit/id/99', true);
	}


	/** @test */
	public function shouldRedirect() {
		$this->assertRedirect();
	}
}



class ModoControllerSuggestionAchatEditHarryPotterPostTest extends ModoControllerSuggestionAchatTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SuggestionAchat')
			->whenCalled('save')
			->answers(true);
	}


	/** @test */
	public function errorForEmptyTitreAndCommentaireShouldBeTitreOuCommentaireRequis() {
		$this->postDispatch('/admin/modo/suggestion-achat-edit/id/2', 
			                  ['titre' => '', 'commentaire' => ''],
												true);	

		$this->assertXPathContentContains('//ul[@class="errors"]//li', 'Titre ou commentaire requis');
	}


	/** @test */
	public function withValidDataShouldHaveNoError() {
		$this->postDispatch('/admin/modo/suggestion-achat-edit/id/2', 
												['titre' => 'Star Wars', 'auteur' => 'G.Lucas', 'isbn' => ''],
												true);
		$this->assertRedirect();
	}
}



class ModoControllerSuggestionAchatDeleteHarryPotterTest extends ModoControllerSuggestionAchatTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SuggestionAchat')
			->whenCalled('delete')
			->answers(null);

		$this->dispatch('admin/modo/suggestion-achat-delete/id/2', true);
	}


	/** @test */
	public function shouldCallDelete() {
		$this->assertTrue(Class_SuggestionAchat::getLoader()->methodHasBeenCalled('delete'));
	}


	/** @test */
	public function shouldRedirect() {
		$this->assertRedirect();
	}
}



class ModoControllerSuggestionAchatDeleteUnknownTest extends ModoControllerSuggestionAchatTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SuggestionAchat')
				->whenCalled('find')
				->with(99)
				->answers(null);

		$this->dispatch('admin/modo/suggestion-achat-delete/id/99', true);
	}


	/** @test */
	public function shouldRedirect() {
		$this->assertRedirect();
	}
}



class ModoControllerDeleteAvisCmsTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Avis::getLoader()
			->newInstanceWithId(34)
			->setAuteur(Class_Users::getLoader()
				          ->newInstanceWithId(98)
				          ->setPseudo('Mimi'))
			->setDateAvis('2012-02-05')
			->setNote(4)
			->setEntete('Hmmm')
			->setAvis('ça a l\'air bon')
			->beWrittenByAbonne()
      ->setIdCms(28);


	  Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Avis')
			->whenCalled('delete')
			->answers(true);

	
		$this->dispatch('admin/modo/delete-cms-avis/id/34', true);
	}


  /** @test */
	public function avisShouldHaveBeenDeleted() {
		$this->assertEquals(34, Class_Avis::getLoader()->getFirstAttributeForLastCallOn('delete')->getId());
	}


	/** @test */
	public function answersShouldRedirectToArticleId28() {
		$this->assertRedirectTo('/opac/cms/articleview/id/28');
	}
}

?>