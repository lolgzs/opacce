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
require_once 'Class/Newsletter.php';
require_once 'ModelTestCase.php';


class NewsletterMailingAnimationsTestSendMail extends ModelTestCase {
	public function setUp() {

		// Les mails envoyés reprennent l'adresse de la configuration du profil Portail
		$profil_portail = new Class_Profil();
		$profil_loader = $this->getMock('Mock_Storm_Model_Loader', array('find'));
		$profil_loader
			->expects($this->atLeastOnce())
			->method('find')
			->with(1)
			->will($this->returnValue($profil_portail));
		Storm_Model_Abstract::setLoaderFor('Class_Profil', $profil_loader);
		$profil_portail->setMailSite('flo@astrolabe.fr');


		//A l'envoi du mail, la newsletter doit être sauvegardée (pour mémoriser au moins la date d'envoi)
		$this
			->_buildTableMock('Class_Newsletter', array('insert'))
			->expects($this->once())
			->method('insert');

		$this
			->_buildTableMock('Class_NewsletterSubscription', array('insert'))
			->expects($this->atLeastOnce())
			->method('insert');


		$this->rdubois = new Class_Users();
		$this->rdubois
			->setPrenom('Rémy')
			->setNom('Dubois')
			->setMail('rdubois@free.fr');

		$this->mduchamp = new Class_Users();
		$this->mduchamp
			->setPrenom('Marcel')
			->setNom('Duchamp')
			->setMail('mduchamp@hotmail.com');


		$this->animations = new Class_Newsletter();
		$this->animations
			->setTitre('Animations du mois')
		  ->setContenu('Découverte des cuisines du monde')
			->setIdCatalogue(null)
			->setNbNotices(0)
			->setIdPanier(null)
			->addUser($this->rdubois)
			->addUser($this->mduchamp)
			->setExpediteur(null);

		$this->mock_transport = new MockMailTransport();
		Zend_Mail::setDefaultTransport($this->mock_transport);
		$this->animations->send();
		$this->mail = $this->mock_transport->sent_mail;
	}

	public function testToArrayContainsAdminPortailAsExpediteur() {
		$this->assertContains('flo@astrolabe.fr',
													$this->animations->toArray());
	}

	public function testSubjectIsAnimationsDuMois() {
		$this->assertEquals('Animations du mois', $this->mail->getSubject());
	}

	public function testBodyTextIsDecouverteCuisineDuMonde() {
		$this->assertContains('Découverte des cuisines du monde',
													quoted_printable_decode($this->mail->getBodyText()->getContent()));
	}

	public function testBccIncludesRduboisAtFreeDotFr() {
		$this->assertContains('rdubois@free.fr',
													$this->mail->getRecipients());
	}

	public function testBccIncludesMduchampAtHotmailDotCom() {
		$this->assertContains('mduchamp@hotmail.com',
													$this->mail->getRecipients());
	}

	public function testToIsAdminPortail() {
		$this->assertContains('flo@astrolabe.fr',
													$this->mail->getRecipients());
	}


	public function testRecipientsSizeIsThree() {
		$this->assertEquals(3, count($this->mail->getRecipients()));
	}

	public function testSenderIsAdminPortail() {
		$this->assertEquals('flo@astrolabe.fr', $this->mail->getFrom());
	}

	public function testGenerateMailIsSameAsSentMail() {
		$generated = $this->animations->generateMail();
		$generated->setDate($this->mail->getDate());
		$this->assertEquals($this->mail, $generated);
	}

	public function testNewsletterLastDistributionDateIsNow() {
		$this->assertEquals(strftime('%Y-%m-%d'),
												strftime('%Y-%m-%d',
																 strtotime($this->animations->getLastDistributionDate()) ));
	}


	public function testSettingExpediteurUseIt() {
		$this->animations->setExpediteur('cyberlab@astrolabe.fr');
		$mail = $this->animations->generateMail();

		$this->assertEquals('cyberlab@astrolabe.fr',
												$mail->getFrom());

		$this->assertContains('cyberlab@astrolabe.fr',
													$mail->getRecipients());

		$this->assertNotContains('flo@astrolabe.fr',
														 $mail->getRecipients());

		$this->assertEquals(3, count($this->mail->getRecipients()));
	}


	public function testSendToOnlySendToGivenRecipient() {
		$this->animations->sendTo('marcel@free.fr');
		$mail = $this->mock_transport->sent_mail;

		$this->assertEquals(1, count($mail->getRecipients()));

		$this->assertContains('marcel@free.fr',
													$mail->getRecipients());
	}
}




class NewsletterConcertsTestMailRecipients extends ModelTestCase {
	public function setUp() {
		$jpasse = new Class_Users();
		$jpasse
			->setPrenom('Jean')
			->setNom('Passe')
			->setMail('jpasse@hotmail.com');

		$user_without_mail = new Class_Users();
		$user_without_mail
			->setPrenom('Mata')
			->setNom('Hari')
			->setMail(null);


		$concerts = new Class_Newsletter();
		$concerts
			->setTitre('Concerts')
			->setContenu("Marcus Miller<br />au Jazz Festival")
			->setIdCatalogue(null)
			->setNbNotices(0)
			->setIdPanier(null);

		$concerts
			->addUser($jpasse)
			->addUser($user_without_mail);

		$this->mail = $concerts->generateMail();
	}

	public function testSubjectIsConcerts() {
		$this->assertEquals('Concerts', $this->mail->getSubject());
	}


	public function testBodyTextIsMarcusAuJazz() {
		$this->assertContains("Marcus Miller\nau Jazz Festival",
												quoted_printable_decode($this->mail->getBodyText()->getContent()));
	}


	public function testBodyHTMLBreaksLines() {
		$this->assertContains("Marcus Miller<br />au Jazz Festival",
													quoted_printable_decode($this->mail->getBodyHTML()->getContent()));
	}


	public function testBccIncludesJean() {
		$this->assertContains('jpasse@hotmail.com',
													$this->mail->getRecipients());
	}

	public function testRecipientsSizeIsOne() {
		$this->assertEquals(1, count($this->mail->getRecipients()));
	}
}




class NewsletterConcertsTestPanier extends ModelTestCase {
	public function setUp() {
		$this->millenium = new Class_Notice();
		$this->millenium
			->setId(345)
			->setTitrePrincipal("Les hommes qui n'aimaient pas les femmes")
			->setResume('Polard du nord')
			->setAuteurPrincipal("Stieg Larsson")
			->setAnnee(2005)
			->setUrlVignette('http://amazon.fr/millenium.png');

		$this->potter = new Class_Notice();
		$this->potter
			->setId(987)
			->setTitrePrincipal("Harry Potter à l'école des sorciers")
			->setAuteurPrincipal("J.K. Rowling")
			->setResume("L'histoire d'un sorcier...")
			->setAnnee(1998)
			->setUrlVignette("http://amazon.fr/potter.gif");

		$this->_generateLoaderFor('Class_Notice', array('getNoticesFromPreferences'))
			->expects($this->once())
			->method('getNoticesFromPreferences')
			->with(array('id_catalogue' => null,
									 'id_panier' => 23,
									 'nb_notices' => 2,
									 'only_img' => false,
									 'aleatoire' => 0,
									 'tri' => 1))
			->will($this->returnValue(array($this->millenium, $this->potter)));


		$selection = new Class_Newsletter();
		$selection
			->setTitre('Selection')
			->setContenu('Notre sélection du mois')
			->setIdCatalogue(null)
			->setNbNotices(2)
			->setIdPanier(23);

		$this->mail = $selection->generateMail();
	}


	public function assertMIMEPartContains($needle, $text) {
		$decoded_text = quoted_printable_decode($text->getContent());
		parent::assertContains($needle, $decoded_text,
													 $needle.' not found in '.$decoded_text);
	}

	public function assertBodyHTMLContains($needle) {
		$this->assertMIMEPartContains($needle,
																	$this->mail->getBodyHTML());
	}

	public function assertBodyTextContains($needle) {
		$this->assertMIMEPartContains($needle,
																	$this->mail->getBodyText());
	}

	public function testBodyTextContainsMillenium() {
		$this->assertBodyTextContains("Les hommes qui n'aimaient pas les femmes (Stieg Larsson, 2005)");
	}

	public function testBodyTextContainsResumeMillenium() {
		$this->assertBodyTextContains("Polard du nord");
	}

	public function testBodyTextContainsURLMillenium() {
		$this->assertBodyTextContains("http://localhost" . BASE_URL . "/recherche/viewnotice/id/345");
	}

	public function testBodyTextContainsPotter() {
		$this->assertBodyTextContains("Harry Potter à l'école des sorciers (J.K. Rowling, 1998)");
	}

	public function testBodyTextContainsResumePotter() {
		$this->assertBodyTextContains("L'histoire d'un sorcier");
	}

	public function testBodyTextContainsURLPotter() {
		$this->assertBodyTextContains("http://localhost" . BASE_URL . "/recherche/viewnotice/id/987");
	}

	public function testVignetteMilleniumInHTML() {
		$this->assertBodyHTMLContains('<img src="http://amazon.fr/millenium.png"');
	}

	public function testLinkMillenium() {
		$this->assertBodyHTMLContains('<a href="http://localhost' . BASE_URL . '/recherche/viewnotice?id=345">');
	}

	public function testBodyHTMLContainsPotter() {
		$this->assertBodyHTMLContains("Harry Potter à l'école des sorciers (J.K. Rowling, 1998)");
	}

	public function testBodyHTMLContainsResumePotter() {
		$this->assertBodyHTMLContains("L'histoire d'un sorcier...");
	}

	public function testVignettePotterInHTML() {
		$this->assertBodyHTMLContains('<img src="http://amazon.fr/potter.gif"');
	}

	public function testLinkPotter() {
		$this->assertBodyHTMLContains('<a href="http://localhost' . BASE_URL . '/recherche/viewnotice?id=987">');
	}
}

?>