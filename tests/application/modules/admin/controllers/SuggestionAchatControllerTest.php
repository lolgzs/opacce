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
require_once 'AdminAbstractControllerTestCase.php';

abstract class Admin_SuggestionAchatControllerTestCase extends Admin_AbstractControllerTestCase {
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




class Admin_SuggestionAchatControllerIndexTest extends Admin_SuggestionAchatControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/suggestion-achat', true);
	}


	/** @test */
	public function pageTitleShouldBeSuggestionAchat() {
		$this->assertXPathContentContains('//h1', 'Suggestions d\'achat');
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
		$this->assertXPath('//tr[1]//a[contains(@href, "suggestion-achat/edit/id/2")]');
	}


	/** @test */
	function firstRowTDShouldHaveLinkToDelete() {
		$this->assertXPath('//tr[1]//a[contains(@href, "suggestion-achat/delete/id/2")]');
	}


	/** @test */
	public function secondRowTDShouldContainsMillenium() {
		$this->assertXPathContentContains('//tr[2]//td', 'Millenium');		
	}
}




class Admin_SuggestionAchatControllerEditHarryPotterTest extends Admin_SuggestionAchatControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/suggestion-achat/edit/id/2', true);
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




class Admin_SuggestionAchatControllerEditHarryPotterPostTest extends Admin_SuggestionAchatControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SuggestionAchat')
			->whenCalled('save')
			->answers(true);
	}


	/** @test */
	public function errorForEmptyTitreAndCommentaireShouldBeTitreOuCommentaireRequis() {
		$this->postDispatch('/admin/suggestion-achat/edit/id/2', 
			                  ['titre' => '', 'commentaire' => ''],
												true);	

		$this->assertXPathContentContains('//ul[@class="errors"]//li', 'Titre ou commentaire requis');
	}


	/** @test */
	public function withValidDataShouldHaveNoError() {
		$this->postDispatch('/admin/suggestion-achat/edit/id/2', 
												['titre' => 'Star Wars', 'auteur' => 'G.Lucas', 'isbn' => ''],
												true);
		$this->assertRedirect();
	}
}



?>