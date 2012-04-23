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
require_once 'Class/Newsletter.php';

abstract class Admin_NewsletterControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setup();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_PanierNotice')
			->whenCalled('findAllBelongsToAdmin')
			->answers(array());
	}
}


class Admin_NewsletterControllerIndexActionTest extends Admin_NewsletterControllerTestCase {
	public function setUp() {
		parent::setUp();

		$fixtures = array(
											array('id' => 1,
														'titre' => 'Nouveautés classique',
														'contenu' => 'Notre sélection du mois',
														'last_distribution_date' => '2005-03-27 12:30:00'),

											array('id' => 2,
														'titre' => 'Animations',
														'contenu' => 'Pour les jeunes',
														'last_distribution_date' => null));

		$mock_results = new Zend_Db_Table_Rowset(array('data' => $fixtures));
		$tbl_newsletters = $this->getMock('MockTableNewsletters',
																			array('fetchAll'));

		$tbl_newsletters
			->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue($mock_results));

		Class_Newsletter::getLoader()->setTable($tbl_newsletters);
		$this->dispatch('/admin/newsletter');
	}


	public function testIndexActionFound() {
		$this->assertController('newsletter');
		$this->assertAction('index');
	}

	public function testListNouveautesClassique() {
		$this->assertXPathContentContains("//tr[1]//td", 'Nouveautés classique');
	}

	public function testLastDistributionDateForNouveautesClassique() {
		$this->assertXPathContentContains("//tr[1]//td", '27/03/2005 12:30');
	}

	public function testEditNouveautesClassiqueLink() {
		$this->assertXPath("//tr[1]//td//a[@href='/admin/newsletter/edit/id/1']");
	}

	public function testDeleteNouveautesClassiqueLink() {
		$this->assertXPath("//tr[1]//td//a[@href='/admin/newsletter/delete/id/1']");
	}

	public function testPreviewNouveautesClassiqueLink() {
		$this->assertXPath("//tr[1]//td//a[@href='/admin/newsletter/preview/id/1']");
	}

	public function testTestNouveautesClassiqueLink() {
		$this->assertXPath("//tr[1]//td//a[@href='/admin/newsletter/sendtest/id/1']");
	}

	public function testSendNouveautesClassiqueLink() {
		$this->assertXPath("//tr[1]//td//a[@href='/admin/newsletter/send/id/1']");
	}

	public function testListAnimations() {
		$this->assertXPathContentContains("//tr[2]//td", 'Animations');
	}

	public function testLastDistributionDateForAnimationsIsNone() {
		$this->assertXPathContentContains("//tr[2]//td", 'Aucune');
	}

	public function testListAnimationsEditLink() {
		$this->assertXPath("//tr[2]//td//a[@href='/admin/newsletter/edit/id/2']");
	}

	public function testDeleteAnimationsLink() {
		$this->assertXPath("//tr[2]//td//a[@href='/admin/newsletter/delete/id/2']");
	}

	public function testPreviewAnimationsLink() {
		$this->assertXPath("//tr[2]//td//a[@href='/admin/newsletter/preview/id/2']");
	}

	public function testTestAnimationsLink() {
		$this->assertXPath("//tr[2]//td//a[@href='/admin/newsletter/sendtest/id/2']");
	}

	public function testSendAnimationsLink() {
		$this->assertXPath("//tr[2]//td//a[@href='/admin/newsletter/send/id/2']");
	}

	public function testAddLink() {
		$this->assertXPath("//div[contains(@onclick, '/admin/newsletter/add')]");
	}
}



class Admin_NewsletterControllerAddActionTest extends Admin_NewsletterControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->mock_sql = $this->getSqlMock();
		$this->mock_sql
			->expects($this->any())
			->method('fetchAll')
			->with("select * from catalogue order by libelle")
			->will($this->returnValue(false));


		$this->dispatch('/admin/newsletter/add');		
	}


	/** @test */
	function catalogueComboShouldBeEmpty() {
		$this->assertNotXPath("//select[@id='id_catalogue']/option");
	}

	public function testAddActionFound() {
		$this->assertController('newsletter');
		$this->assertAction('add');
	}

	public function testFormTitreIsEmpty() {
			$this->assertXPath("//form//input[@id='titre'][@value='']");
	}

	public function testFormContenu() {
		$this->assertQuery("form textarea[@name='contenu']");
	}

	public function testTitle() {
		$this->assertQueryContentContains("h1", "Créer une lettre d'information");
	}

	public function testSubmitButton() {
			$this->assertXPath(utf8_encode("//div[contains(@onclick, \"document.forms['newsletter'].submit()\")]"));
	}
}



class Admin_NewsletterControllerAddActionWithCataloguesTest extends Admin_NewsletterControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->mock_sql = $this->getSqlMock();

		$this->mock_sql
			->expects($this->any())
			->method('fetchAll')
			->with("select * from catalogue order by libelle")
			->will($this->returnValue(array(array('ID_CATALOGUE' => 2,
																						'LIBELLE' => 'Jazz'), 
																			array('ID_CATALOGUE' => 5,
																						'LIBELLE' => 'BD'))));

		$this->dispatch('/admin/newsletter/add');		
	}


	/** @test */
	function catalogueJazzShouldBeAnOption() {
		$this->assertXPathContentContains("//select[@id='id_catalogue']/option[@value='2']", 'Jazz');
	}


	/** @test */
	function catalogueBDShouldBeAnOption() {
		$this->assertXPathContentContains("//select[@id='id_catalogue']/option[@value='5']", 'BD');
	}
}



class Admin_NewsletterControllerEditActionTest extends Admin_NewsletterControllerTestCase {
	public function setUp() {
		parent::setUp();

		$fixtures = array('id' => 53,
											'titre' => 'Nouveautés',
											'contenu' => 'Notre sélection du mois',
											'expediteur' => 'laurent@free.fr');

		$mock_results = new Zend_Db_Table_Rowset(array('data' => array($fixtures)));
		$this->tbl_newsletters = $this->getMock('MockTableNewsletters',
																						array('find', 'update', 'fetchall'));

		$this->tbl_newsletters
			->expects($this->any())
			->method('find')
			->with(53)
			->will($this->returnValue($mock_results));

		$this->tbl_newsletters
			->expects($this->any())
			->method('fetchall')
			->will($this->returnValue(array()));

		Class_Newsletter::getLoader()->setTable($this->tbl_newsletters);
		$this->dispatch('/admin/newsletter/edit/id/53');
	}


	public function testEditForwardedToIndexAction() {
		$this->assertController('newsletter');
		$this->assertAction('index');
	}

	public function testTitle() {
		$this->assertQueryContentContains("h1", "Modifier la lettre: Nouveautés");
	}

	public function testFormAction() {
		$this->assertXPath("//form[@id='newsletter'][@method='post'][@action='/admin/newsletter/edit/id/53']");
	}

	public function testFormTitre() {
		$this->assertXPath("//form//input[@id='titre'][@value='Nouveautés']");
	}

	public function testFormExpediteur() {
		$this->assertXPath("//form//input[@id='expediteur'][@value='laurent@free.fr']");
	}

	public function testFormContenuHTML() {
		$this->assertQueryContentContains("form textarea[@name='contenu'][following-sibling::script]",
																			'Notre sélection du mois');
	}

	public function testSubmitButton() {
		$this->assertXPath(utf8_encode("//div[contains(@onclick, \"document.forms['newsletter'].submit()\")]"));
	}


	public function testSubmitPostsTheForm() {
		$data = array('id' => 53,
									'titre' => utf8_encode('Archives'),
									'expediteur' => 'laurent@free.fr',
									'contenu' => utf8_encode('Sélection du mois dernier'),
									'id_catalogue' => '',
									'id_panier' => '',
									'nb_notices' => 2);

		$this->tbl_newsletters
			->expects($this->once())
			->method('update')
			->with($data);
		
		$request = $this->getRequest();
		$request
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('/admin/newsletter/edit');
		$this->assertRedirectTo('/admin/newsletter/preview/id/53');
	}
}


class Admin_NewsletterControllerSaveActionTest extends Admin_NewsletterControllerTestCase {

	public function testSubmitAddActionCreateNewInstance() {
		$data = array('titre' => utf8_encode('Fêtes du lac'),
									'contenu' => utf8_encode('Plein les yeux'),
									'id_catalogue' => '',
									'id_panier' => '',
									'nb_notices' => 2);

		$tbl_newsletters = $this->getMock('MockTableNewsletters',
																			array('insert', 'fetchall', 'find'));
		Class_Newsletter::getLoader()->setTable($tbl_newsletters);
		$tbl_newsletters
			->expects($this->once())
			->method('insert')
			->with($data);

		$this->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('/admin/newsletter/add');
		$this->assertRedirectTo('/admin/newsletter/index');
	}
}



class Admin_NewsletterControllerValidationsTest extends Admin_NewsletterControllerTestCase {

	public function testTitleShouldNotBeEmpty() {
		$data = array('titre' => '',
									'contenu' => utf8_encode('Plein les yeux'));

		$this->_assertActionEditOnPost($data);
		$this->assertQueryContentContains("form ul.errors li", "Une valeur est requise");
	}

	public function testExpediteurShouldBeValidEmail() {
		$data = array('titre' => 'Ce soir',
									'expediteur' => 'zork',
									'contenu' => utf8_encode('Plein les yeux'));

		$this->_assertActionEditOnPost($data);
		$this->assertQueryContentContains("form ul.errors li", 
																			"'zork' n'est pas un email valide dans le format local-part@hostname");
	}


	protected function _assertActionEditOnPost($data) {
		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('/admin/newsletter/edit');
		$this->assertAction('index');
	}
}



class Admin_NewsletterControllerDeleteActionTest extends Admin_NewsletterControllerTestCase {
	public function testDeleteCalledOnInstance() {
		$newsletter = $this->getMock('Class_Newsletter');

		$nl_loader = $this->getMock('Storm_Model_Loader', array(), array(), '', FALSE, FALSE);
		$nl_loader
			->expects($this->once())
			->method('find')
			->with(4)
			->will($this->returnValue($newsletter));

		$newsletter
			->expects($this->once())
			->method('delete');
		
		Storm_Model_Abstract::setLoaderFor('Class_Newsletter', $nl_loader);

		$this->dispatch('/admin/newsletter/delete/id/4');
		$this->assertRedirectTo('/admin/newsletter/index');
	}
}


class Admin_NewsletterControllerSendActionTest extends Admin_NewsletterControllerTestCase {
	public function setUp() {
		$this->newsletter = $this->getMock('Class_Newsletter', array('send'));

		$nl_loader = $this->getMock('Storm_Model_Loader', array(), array(), '', FALSE, FALSE);
		$nl_loader
			->expects($this->once())
			->method('find')
			->with(4)
			->will($this->returnValue($this->newsletter));
		Storm_Model_Abstract::setLoaderFor('Class_Newsletter', $nl_loader);

		parent::setUp();
	}

	public function testSendCalled() {
		$this->newsletter
			->expects($this->once())
			->method('send');

		$this->dispatch('/admin/newsletter/send/id/4');

		$this->assertResponseCode(200);
		$this->assertEquals($this->_response->getBody(), 
												'Lettre envoyée');
	}


	public function testSendRaisesException() {
		$this->newsletter
			->expects($this->once())
			->method('send')
			->will($this->throwException(new Zend_Mail_Protocol_Exception('Connection timed out')));

		$this->dispatch('/admin/newsletter/send/id/4');

		$this->assertResponseCode(500);
		$this->assertEquals($this->_response->getBody(), 
												"Erreur à l'envoi de la lettre: Connection timed out");
	}
}


class Admin_NewsletterControllerPreviewActionTest extends Admin_NewsletterControllerTestCase {
	public function setUp() {
		parent::setUp();

		$marcus = new Class_Users();
		$marcus
			->setPrenom('Marcus')
			->setNom('Miller')
			->setLogin('mmiller')
			->setMail('marcus@gmail.com');

		$miles = new Class_Users();
		$miles
			->setPrenom('Miles')
			->setNom('Davis')
			->setLogin('mdavis')
			->setMail('mdavis@free.fr');


		$nouveautes = new Class_Newsletter();
		$nouveautes
			->setId(3)
			->setTitre('Nouveautés')
			->setContenu('Notre sélection du mois<img src="zork.jpg"/> <b>Hoho</b>')			
			->setIdCatalogue(null)
			->setNbNotices(0)
			->setIdPanier(null)
			->setUsers(array($miles, $marcus));

		Class_Newsletter::getLoader()->cacheInstance($nouveautes);


		$profil_portail = new Class_Profil();
		$profil_portail
			->setId(1)
			->setLibelle('Portail')
			->setMailSite('laurent@afi-sa.net');
		Class_Profil::getLoader()->cacheInstance($profil_portail);

		$this->dispatch('/admin/newsletter/preview/id/3');
	}

	public function testFrom() {
		$this->assertQueryContentContains('p', 'laurent@afi-sa.net');
	}


	public function testSubject() {
		$this->assertQueryContentContains('p', 'Nouveautés');
	}

	public function testBodyText() {
		$this->assertQueryContentContains('p', 'Notre sélection du mois Hoho');
	}

	public function testBodyHtml() {
		$this->assertXPath('//div//img[@src="zork.jpg"]');
	}

	public function testTableAbonnes() {
		foreach(array('Nom', 'Prénom', 'Identifiant', 'E-mail') as $head_field)
			$this->assertQueryContentContains("table#abonnes thead tr th", $head_field);
	}

	public function testMarcusHere() {
		foreach(array('Miller', 'Marcus', 'mmiller', 'marcus@gmail.com') as $content)
			$this->assertQueryContentContains('table#abonnes tbody tr td', $content);
	}

	public function testMilesHere() {
		foreach(array('Miles', 'Davis', 'mdavis', 'mdavis@free.fr') as $content)
			$this->assertQueryContentContains('table#abonnes tbody tr td', $content);
	}
}



class Admin_NewsletterControllerSendTestActionTest extends Admin_NewsletterControllerTestCase {
	public function setUp() {
		parent::setUp();

		$nouveautes = new Class_Newsletter();
		$nouveautes
			->setId(3)
			->setTitre('Nouveautés')
			->setContenu('Notre sélection du mois')
			->setExpediteur('lolo@afi.fr');

		Class_Newsletter::getLoader()->cacheInstance($nouveautes);
		$this->dispatch('/admin/newsletter/sendtest/id/3');
	}

	public function testFormActionIsSendTest() {
		$this->assertXPath("//form[@id='sendparams'][@method='post'][@action='/admin/newsletter/sendtest/id/3']");
	}

	public function testFormInputRecipientContainsLoloAtAFIDotFR() {
		$this->assertXPath('//form//input[@id="destinataire"][@value="lolo@afi.fr"]');
	}

	public function testSubmitButton() {
		$this->assertXPath("//input[@type='submit'][@value='Envoyer à cette adresse']");
	}

	public function testTitle() {
		$this->assertQueryContentContains("h1", "Tester l'envoi de la lettre: Nouveautés");
	}
}


class Admin_NewsletterControllerWithoutExpediteurSendTestActionTest extends Admin_NewsletterControllerTestCase {
	public function setUp() {
		parent::setUp();

		$nouveautes = new Class_Newsletter();
		$nouveautes
			->setId(3)
			->setTitre('Nouveautés')
			->setContenu('Notre sélection du mois')
			->setExpediteur(null);

		Class_Profil::getLoader()->find(1)->setMailSite(null);

		Class_Newsletter::getLoader()->cacheInstance($nouveautes);
		$this->dispatch('/admin/newsletter/sendtest/id/3');
	}

	public function testFormInputRecipientIsEmpty() {
		$this->assertXPath('//form//input[@id="destinataire"][@value=""]');
	}
}


class Admin_NewsletterControllerPostSendTestActionTest extends Admin_NewsletterControllerTestCase {
	public function setUp() {
		parent::setUp();
		
		$this->nouveautes = $this->getMock('Mock_Newsletter', 
																 array('getId', 
																			 'sendTo', 
																			 'getExpediteur',
																			 'getTitre')); 
		$this->nouveautes
			->expects($this->atLeastOnce())
			->method('getId')
			->will($this->returnValue(4));

		$this->nouveautes
			->expects($this->any())
			->method('getTitre')
			->will($this->returnValue('nouveautés'));

		$this->nouveautes
			->expects($this->any())
			->method('getExpediteur')
			->will($this->returnValue('flo@astrolabe.fr'));

		Class_Newsletter::getLoader()->cacheInstance($this->nouveautes);

	}


	protected function _post($data) {
		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('/admin/newsletter/sendtest/id/4');
		$this->assertAction('index');
	}


	public function testInvalidEmailShowsError() {
		$this->_post(array('destinataire' => 'zork'));
		$this->assertQueryContentContains("form ul.errors li", 
																			"'zork' n'est pas un email valide dans le format local-part@hostname");	
	}

	public function testSuccessfullSend() {
		$this->nouveautes
			->expects($this->once())
			->method('sendTo')
			->with('marcel@free.fr')
			->will($this->returnValue(true));

		$this->_post(array('destinataire' => 'marcel@free.fr'));

		$this->assertQueryContentContains('div.subview', 
																			'Lettre "nouveautés" envoyée à marcel@free.fr');
	}

	public function testErrorDisplayedWhenSendRaiseException() {
		$this->nouveautes
			->expects($this->once())
			->method('sendTo')
			->with('marcel@free.fr')
			->will($this->throwException(new Zend_Mail_Protocol_Exception('Connection timed out')));

		$this->_post(array('destinataire' => 'marcel@free.fr'));
		$this->assertQueryContentContains("ul.errors li", 
																			"Echec de l'envoi: Connection timed out");	
	}
}

?>