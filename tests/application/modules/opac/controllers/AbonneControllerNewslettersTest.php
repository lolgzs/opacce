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


abstract class AbstractAbonneControllerNewslettersTestCase extends AbstractControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "abonne_sigb";
		$account->ROLE_LEVEL = 2;
		$account->ID_USER = $this->marcus->getId();
		$account->PSEUDO = "Marcus";
	}

	public function setUp() {
		$this->marcus = Class_Users::getLoader()->newInstanceWithId(10)
			->setPrenom('Marcus')
			->setNom('Miller')
			->setLogin('mmiller')
			->setMail('marcus@gmail.com')
			->setPseudo('mimi')
			->setDateDebut(null)
			->setPassword('mysecret')
			->setFicheSIGB(array('type_comm' => 0, 'nom_aff' => 'Marcus'))
			->setRole('abonne_sigb')
			->setRoleLevel(3)
			->setIdSite(2)
			->setIdabon('00123');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('save')
			->answers(true);

		$this->newsletter_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Newsletter');
		
		parent::setUp();
	}
}


abstract class AbonneControllerWithTwoNewslettersTestCase extends AbstractAbonneControllerNewslettersTestCase {
	public function setUp() {
		parent::setUp();

		$this->concerts = Class_Newsletter::newInstanceWithId(12, ['titre' =>'Concerts',
																															 'contenu' => 'Festival jazz']);

		$this->visites = Class_Newsletter::newInstanceWithId(14, [ 'titre' =>'Visites',
																															 'contenu' => 'du patrimoine']);

		$this->newsletter_loader->whenCalled('findAll')
													 ->answers(array($this->concerts, $this->visites));

		$this->marcus
			->setNewsletters(array($this->concerts));	//Marcus is subscribed to concerts newsletter
	}
}


class AbonneControllerNewslettersFicheActionWithNoExistingNewslettersTest extends AbstractAbonneControllerNewslettersTestCase {
	public function setUp() {
		parent::setUp();

		$this->marcus->setNewsletters(array());
		$this->newsletter_loader->whenCalled('findAll')
													 ->answers(array());

		$this->dispatch('/opac/abonne');
	}

	public function testInfoAbonnementNewsletterConcerts () {
		$this->assertNotQueryContentContains('div.abonneFiche',
																				 "Vous n'êtes abonné à aucune lettre d'information");
	}

	public function testLinkToEditInformationsInfoAbonnements() {
		$this->assertNotXPathContentContains("//div[@class='abonneFiche']//a[@href='/abonne/edit']",
																				 'Modifier mes abonnements');

	}

	public function testEditFormDoNotShowSubscriptions() {
		$this->dispatch('/opac/abonne/edit');
		$this->assertAction('edit');
		$this->assertNotXPath("//form//input[@type='checkbox'][@name='subscriptions[]']");
		$this->assertNotXPath("//form//label[@for='subscriptions']");
	}
}


class AbonneControllerNewslettersFicheActionWithOneSubscriptionTest extends AbonneControllerWithTwoNewslettersTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne', true);
	}

	public function testForwardedToFiche() {
		$this->assertController('abonne');
		$this->assertAction('fiche');
	}

	public function testLinkToEditInformationsInTitle() {
		$this->assertXPathContentContains("//div[@class='abonneTitre']//a[@href='/abonne/edit']",
																			'Modifier ma fiche');

	}

	public function testInfoAbonnementNewsletterConcerts () {
		$this->assertQueryContentContains('div.abonneFiche',
																			"Vous êtes abonné à la lettre d'information: Concerts");
	}


	public function testIconeNewsletterDisplayed () {
		$this->assertXPath('//div[@class="abonneFiche"]//a[contains(@href, "/abonne/edit")]//img[contains(@src, "abonnes/newsletter.png")]');
	}


	public function testLinkToEditInformationsInfoAbonnements() {
		$this->assertXPathContentContains("//div[@class='abonneFiche']//a[@href='/abonne/edit']",
																			'Modifier mes abonnements');

	}
}


class AbonneControllerNewslettersFicheActionWithTwoSubscriptionsTest extends AbonneControllerWithTwoNewslettersTestCase {
	public function setUp() {
		parent::setUp();

		$this->marcus->addNewsletter($this->visites);

		$this->dispatch('/opac/abonne/fiche');
		$this->assertController('abonne');
		$this->assertAction('fiche');
	}

	public function testInfoAbonnementNewsletterConcertsEtVisites () {
		$this->assertQueryContentContains('div.abonneFiche',
																			"Vous êtes abonné aux lettres d'information: Concerts, Visites");
	}
}


class AbonneControllerNewslettersFicheActionWithNoSubscriptionTest extends AbonneControllerWithTwoNewslettersTestCase {
	public function setUp() {
		parent::setUp();

		$this->marcus->setNewsletters(array());
		$this->dispatch('/opac/abonne/fiche');
		$this->assertController('abonne');
		$this->assertAction('fiche');
	}

	public function testInfoAbonnementNewsletterConcerts () {
		$this->assertQueryContentContains('div.abonneFiche',
																			"Vous n'êtes abonné à aucune lettre d'information");
	}
}


class AbonneControllerNewslettersEditActionTest extends AbonneControllerWithTwoNewslettersTestCase {
	public function setUp() {
		parent::setUp();

		$this->aide_fiche_abonne = new Class_AdminVar();
		$this->aide_fiche_abonne
			->setId('AIDE_FICHE_ABONNE')
			->setValeur("Saisissez\nvos données");
		Class_AdminVar::getLoader()
			->cacheInstance($this->aide_fiche_abonne);


		$this->dispatch('/opac/abonne/edit');
	}

	public function testNewsletterActionFound() {
		$this->assertController('abonne');
		$this->assertAction('edit');
	}

	public function testAideIsDisplayed() {
		$this->assertContains("<div class='help'>Saisissez<br />\nvos données</div>", $this->_response->getBody());
	}

	public function testUserForm() {
		$this->assertXPath("//form[@id='user'][@method='post'][@action='/abonne/edit/id/10']");
	}

	public function testConcertsInCheckbox() {
		$this->assertXPathContentContains("//form//input[@type='checkbox'][@name='subscriptions[]'][@value=12]/..",
																			'Concerts');
	}

	public function testConcertsIsChecked() {
		$this->assertXPath("//form//input[@type='checkbox'][@name='subscriptions[]'][@value=12][@checked]");
	}

	public function testVisitesInCheckbox() {
		$this->assertXPathContentContains("//form//input[@type='checkbox'][@name='subscriptions[]'][@value=14]/..",
																			'Visites');
	}

	public function testVisitesIsNotChecked() {
		$this->assertNotXPath("//form//input[@type='checkbox'][@name='subscriptions[]'][@value=14][@checked]");
	}


	public function testEmailField() {
		$this->assertXPath("//form//input[@id='mail'][@value='marcus@gmail.com']");
	}

	public function testNomField() {
		$this->assertXPath("//form//input[@id='nom'][@value='Miller']");
	}

	public function testPrenomField() {
		$this->assertXPath("//form//input[@id='prenom'][@value='Marcus']");
	}

	public function testPseudoField() {
		$this->assertXPath("//form//input[@id='pseudo'][@value='mimi']");
	}

	public function testSubmitButton() {
		$this->assertXPath(utf8_encode("//form//input[@type='submit']"));
	}
}



class AbonneControllerNewslettersSaveActionTest extends AbonneControllerWithTwoNewslettersTestCase {
	public function setUp() {
		parent::setUp();

		$data = array('nom' => 'MILLER',
									'prenom' => 'MARCUS',
									'mail' => 'marcus@free.fr',
									'pseudo' => 'M2',
									'subscriptions' => array(14),
									'password' => 'amstramgram',
									'confirm_password' => 'amstramgram');

		$this->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('/opac/abonne/edit');
	}

	/** @test */
	public function marcusShouldHaveBeenSaved() {
		$this->assertEquals($this->marcus, Class_Users::getLoader()->getFirstAttributeForLastCallOn('save'));
	}

	public function testNomSetToMILLER() {
		$this->assertEquals('MILLER', $this->marcus->getNom());
	}

	public function testPrenomSetToMARCUS() {
		$this->assertEquals('MARCUS', $this->marcus->getPrenom());
	}

	public function testMailSetToMarcusAtFreeDotFr() {
		$this->assertEquals('marcus@free.fr', $this->marcus->getMail());
	}

	public function testPseudoSetToM2() {
		$this->assertEquals('M2', $this->marcus->getPseudo());
	}

	public function testPasswordSetToAmstramgram() {
		$this->assertEquals('amstramgram', $this->marcus->getPassword());
	}

	public function testNewslettersContainsVisites() {
		$this->assertEquals(array($this->visites), $this->marcus->getNewsletters());
	}

	public function testRedirectedToFiche() {
		$this->assertRedirectTo('/abonne/fiche');
	}
}


class AbonneControllerNewslettersSaveWithEmptyDataTest extends AbonneControllerWithTwoNewslettersTestCase {
	public function setUp() {
		parent::setUp();

		$this->getRequest()->setMethod('POST');

		$this->dispatch('/opac/abonne/edit');
	}

	/** @test */
	public function marcusShouldHaveBeenSaved() {
		$this->assertEquals($this->marcus, Class_Users::getLoader()->getFirstAttributeForLastCallOn('save'));
	}

	public function testNoNewsletters() {
		$this->assertEquals(array(), $this->marcus->getNewsletters());
	}

	public function testPasswordNotChanged() {
		$this->assertEquals('mysecret', $this->marcus->getPassword());
	}
}




class AbonneControllerNewslettersOpsysCommunicationTest extends AbonneControllerWithTwoNewslettersTestCase {
	public function setUp() {
		parent::setUp();

		$this->opsys_service = $this->getMock('MockOpsysService', array('saveEmprunteur'));
		$this->emprunteur = new Class_WebService_SIGB_Emprunteur('00123', 'Marcus');
		$this->emprunteur->setService($this->opsys_service);

		$this->marcus->setFicheSIGB(array('type_comm' => Class_IntBib::COM_OPSYS,
																			'fiche' => $this->emprunteur,
																			'nom_aff' => 'Marcus'));

	}


	protected function _postData() {
		$this->getRequest()
			->setMethod('POST')
			->setPost(array('nom' => 'Duchamp',
											'prenom' => 'Michel',
											'mail' => 'mduchamp@orange.fr',
											'password' => 'carambar',
											'confirm_password' => 'carambar'));
		$this->dispatch('/opac/abonne/edit');
	}


	public function testWithNoSIGBErrorRedirectToFiche() {
		$this->opsys_service
			->expects($this->once())
			->method('saveEmprunteur')
			->with($this->emprunteur)
			->will($this->returnValue($this->opsys_service));

		$this->_postData();

		$this->assertRedirectTo('/abonne/fiche');
	}


	public function testWithSIGBErrorDisplayErrorMessage() {
		$this->opsys_service
			->expects($this->once())
			->method('saveEmprunteur')
			->with($this->emprunteur)
			->will($this->throwException(new Exception('(1023) Le SIGB ne réponds pas')));

		$this->_postData();
		$this->assertAction('edit');

		$this->assertQueryContentContains("ul.errors li",
																			'(1023) Le SIGB ne réponds pas');
	}
}



class AbonneControllerNewslettersSubscriptionTest extends AbonneControllerWithTwoNewslettersTestCase {

	public function setUp() {
		parent::setUp();
		$_SERVER['HTTP_REFERER'] = '/opac/index';
		$this->dispatch('/opac/abonne/subscribe-newsletter/id/14',true);
		
	}	


	/** @test **/
	public function subscribeActionShouldCallSubscribenewsletterAction() {
		$this->assertAction('subscribe-newsletter');
	}


	/** @test **/
	public function subscribeActionShouldRedirectToCurrentPage() {
		$this->assertRedirectTo('/opac/index');
	}


	/** @test **/
	public function subscribeActionShouldAddNewsletterToUser() {
		$this->assertContains( $this->visites,$this->marcus->getNewsletters());
	}


	/** @test **/
	public function userShouldHaveBeenSaved() {
		$this->assertTrue(Class_Users::methodHasBeenCalled('save'));
	}

}


class AbonneControllerNewslettersUnsubscriptionTest extends AbonneControllerWithTwoNewslettersTestCase {

	public function setUp() {
		parent::setUp();
		$_SERVER['REQUEST_URI'] = '/';
		$this->dispatch('/opac/abonne/unsubscribe-newsletter/id/14',true);
		
	}	

	/** @test **/
	public function unsubscribeActionShouldCallSubscribenewsletterAction() {
		$this->assertAction('unsubscribe-newsletter');
	}


	/** @test **/
	public function unsubscribeActionShouldAddNewsletterToUser() {
		$this->assertNotContains( $this->visites,$this->marcus->getNewsletters());
	}


	/** @test **/
	public function userShouldHaveBeenSaved() {
		$this->assertTrue(Class_Users::methodHasBeenCalled('save'));
	}



}


class AbonneControllerNewslettersValidationsTest extends AbonneControllerWithTwoNewslettersTestCase {
	protected function _postData($data) {
		$this->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('/opac/abonne/edit');
		$this->assertAction('edit');
	}

	public function testWrongMail() {
		$this->_postData(array('mail' => 'marc'));
		$this->assertQueryContentContains("form ul.errors li",
																			"'marc' n'est pas un email valide dans le format local-part@hostname");
	}


	public function testOnlyPasswordFilled() {
		$this->_postData(array('password' => 'picetpic'));
		$this->assertQueryContentContains('form ul.errors li',
																			'Vous devez confirmer le mot de passe');
	}

	public function testOnlyConfirmPasswordFilled() {
		$this->_postData(array('confirm_password' => 'ratatam'));
		$this->assertQueryContentContains('form ul.errors li',
																			'Vous devez saisir un mot de passe');
	}

	public function testPasswordAndConfirmationDoesNotMatch() {
		$this->_postData(array('password' => 'picetpic',
													 'confirm_password' => 'ratatam'));
		$this->assertQueryContentContains('form ul.errors li',
																			'Les mots de passe ne correspondent pas');
	}

	public function testPasswordTooShort() {
		$this->_postData(array('password' => 'pic',
													 'confirm_password' => 'pic'));
		$this->assertQueryContentContains('form ul.errors li',
																			"'***' contient moins de 4 caractères");
	}

	public function testPasswordTooLong() {
		$this->_postData(array('password' => '26 caracteres *************',
													 'confirm_password' => '26 caracteres **************'));
		$this->assertQueryContentContains('form ul.errors li',
																			"'***************************' contient plus de 24 caractères");
	}

	/** @test */
	public function withEmptyPasswordMarcusShouldBeSaved() {
		$this->_postData(array('password' => ''));
		$this->assertEquals($this->marcus, Class_Users::getLoader()->getFirstAttributeForLastCallOn('save'));
	}
}

?>