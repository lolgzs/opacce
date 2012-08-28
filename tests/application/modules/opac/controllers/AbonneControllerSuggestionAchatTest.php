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
require_once 'AbstractControllerTestCase.php';

class AbonneControllerSuggestionAchatFormTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/suggestion-achat', true);
	}

	/** @test */
	public function pageTitleShouldBeSuggestionAchat() {
		$this->assertXPathContentContains('//title', 'Suggestion d\'achat');
	}


	/** @test */
	public function boiteShouldHaveTitleSuggestionAchat() {
		$this->assertXPathContentContains('//div[@class="boiteMilieu"]//h1', 'Suggestion d\'achat');
	}


	/** @test */
	public function formShouldContainsInputForTitre() {
		$this->assertXPath('//form//input[@name="titre"][@placeholder="ex: Harry Potter à l\'école des sorciers"]');
	}


	/** @test */
	public function formShouldContainsInputForAuteur() {
		$this->assertXPath('//form//input[@name="auteur"][@placeholder="ex: Joanne Kathleen Rowling"]');
	}


	/** @test */
	public function formShouldContainsInputForDescriptionUrl() {
		$this->assertXPath('//form//input[@type="url"][@name="description_url"][@placeholder="ex: http://fr.wikipedia.org/wiki/Harry_Potter_à_l\'école_des_sorciers"]');
	}


	/** @test */
	public function formShouldContainsInputForISBN() {
		$this->assertXPath('//form//input[@name="isbn"][@placeholder="ex: 2-07-054127-4"]');	
	}

	
	/** @test */
	public function formShouldContainsTextAreaForCommentaire() {
		$this->assertXPath('//form//textarea[@name="commentaire"]');
	}


	/** @test */
	public function formShouldContainsSubmitButtonEnvoyer() {
		$this->assertXPath('//form//input[@type="submit"][@value="Envoyer"]');
	}
}




class AbonneControllerSuggestionAchatPostValidDataTest extends AbstractControllerTestCase {
	protected $_suggestion;
	protected $_mail;

	protected function _loginHook($account) {
		$account->username     = 'Arnaud';
		$account->ID_USER      = 666;
	}


	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SuggestionAchat')
			->whenCalled('save')
			->willDo(
							 function($suggestion){
								 $this->_suggestion = $suggestion->setId(66);
							 });

		$mock_transport = new MockMailTransport();
		Zend_Mail::setDefaultTransport($mock_transport);

		$this->postDispatch('/opac/abonne/suggestion-achat', 
												['titre' => 'Harry Potter',
												 'auteur' => 'J.K.Rowling',
												 'description_url' => 'http://harrypotter.fr',
												 'isbn' => '2-07-0541 27_4',
												 'commentaire' => 'Je veux le lire',
												 'submit' => 'Envoyer']);	

		$this->_mail = $mock_transport->sent_mail;
	}


	/** @test */
	public function newSuggestionShouldHaveTitreHarryPotter() {
		$this->assertEquals('Harry Potter', $this->_suggestion->getTitre());
	}


	/** @test */
	public function newSuggestionShouldHaveAuteurJKRowling() {
		$this->assertEquals('J.K.Rowling', $this->_suggestion->getAuteur());
	}


	/** @test */
	public function newSuggetionShouldHaveDescriptionUrlHarryPotterDotFr() {
		$this->assertEquals('http://harrypotter.fr', $this->_suggestion->getDescriptionUrl());
	}


	/** @test */
	public function newSuggestionsShouldHaveIsbn2070541274() {
		$this->assertEquals('2070541274', $this->_suggestion->getIsbn());
	}


	/** @test */
	public function shouldNotHaveSubmitAttribute() {
		$this->assertNotContains('submit', array_keys($this->_suggestion->toArray()));
	}

	
	/** @test */
	public function newSuggestionShouldHaveCommentaireJeVeuxLeLire() {
		$this->assertEquals('Je veux le lire', $this->_suggestion->getCommentaire());
	}


	/** @test */
	public function newSuggestionShouldBelongsToArnaud() {
		$this->assertEquals('Arnaud', $this->_suggestion->getUser()->getLogin());
	}

	
	/** @test */
	public function newSuggetionShouldDateCreationShouldBeToday() {
		$this->assertEquals(date('Y-m-d'), $this->_suggestion->getDateCreation());
	}


	/** @test */
	public function responseShouldRedirectToSuggestionAchatId66() {
		$this->assertRedirectTo('/opac/abonne/suggestion-achat/id/66');
	}


	/** @test */
	public function sentMailSubjectShouldBeSuggestionAchatHarryPotter() {
		$this->assertEquals('Suggestion d\'achat: Harry Potter', $this->_mail->getSubject());
	}


	/** @test */
	public function fromShouldBeNoReplyAtLocalhost() {
		$this->assertEquals('noreply@localhost', $this->_mail->getFrom());
	}
}




class AbonneControllerSuggestionAchatPostWrongDataTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SuggestionAchat')
			->whenCalled('save')
			->answers(true);


		$this->postDispatch('/opac/abonne/suggestion-achat', 
												['titre' => '',
												 'auteur' => '',
												 'description_url' => 'h p',
												 'isbn' => '207',
												 'commentaire' => '']);	
	}


	/** @test */
	public function errorForTitreShouldBeUnTitreEstRequis() {
		$this->assertXPathContentContains('//li', 'Un titre est requis');
	}


	/** @test */
	public function errorForAuteurShouldBeUnAuteurEstRequis() {
		$this->assertXPathContentContains('//li', 'Un auteur est requis');
	}


	/** @test */
	public function errorForDescriptionUrlShouldBeURLInvalide() {
		$this->assertXPathContentContains('//li', '\'h p\' n\'est pas une URL valide');
	}


	/** @test */
	public function errorForIsbnShouldBeFormatIncorrect() {
		$this->assertXPathContentContains('//li', '\'207\' n\'est pas un ISBN valide');
	}
}




class AbonneControllerSuggestionAchatDataWithEmptyFieldsNotRequiredTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SuggestionAchat')
			->whenCalled('save')
			->answers(true);

		$this->postDispatch('/opac/abonne/suggestion-achat', 
												['titre' => 'Millenium',
												 'auteur' => 'Stieg Larsson',
												 'description_url' => '',
												 'isbn' => '',
												 'commentaire' => ''],
												true);	
	}


	/** @test */
	public function responseShouldBeARedirect() {
		$this->assertRedirect();
	}
}




class AbonneControllerSuggestionAchatWithIdTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
    Class_SuggestionAchat::newInstanceWithId(66);
		$this->dispatch('/opac/abonne/suggestion-achat/id/66', true);
	}


  /** @test */
	public function pageShouldDisplaySuggestionPriseEnCompte() {
		$this->assertXPathContentContains('//p', 'Votre suggestion d\'achat');
	}
}

?>