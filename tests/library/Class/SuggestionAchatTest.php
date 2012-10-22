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


abstract class SuggestionAchatTestCase extends Storm_Test_ModelTestCase {
	protected $_mock_transport;
	protected $_suggestion;

	public function setUp() {
		parent::setUp();

		$this->_mock_transport = new MockMailTransport();
		Zend_Mail::setDefaultTransport($this->_mock_transport);

		$profil = new Class_Profil();
		$profil
			->setId(1)
			->setLibelle('PHP Unit')
			->setTitreSite('PHP Unit')
			->setMailSuggestionAchat('laurent@afi-sa.fr, patrick@afi-sa.fr ; estelle@afi-sa.fr');
		Class_Profil::setCurrentProfil($profil);

		$this->_suggestion = Class_SuggestionAchat::newInstanceWithId(2)
			->setDateCreation('2012-03-01')
			->setTitre('Harry Potter')
			->setAuteur('J.K.Rowling')
			->setIsbn('1234567890')
			->setDescriptionUrl('http://harrypotter.fr')
			->setCommentaire('Je veux le lire')
			->setUser(Class_Users::newInstanceWithId(4)
								->setNom('Belle')
								->setPrenom('Sébastien')
								->setIdabon('98765')
								->setMail('sbelle@gmail.com'));
	}
}




class SuggestionAchatMailTest extends SuggestionAchatTestCase {
	protected $_sent_mail;

	public function setUp() {
		parent::setUp();

		$this->_suggestion->sendMail('noreply@astromelun.fr');
		$this->_sent_mail = $this->_mock_transport->sent_mail;
	}


	protected function assertBodyContains($text) {
		$this->assertContains($text, 
													quoted_printable_decode($this->_sent_mail->getBodyText()->getContent()));
	}


	/** @test */
	public function mailSubjectShouldBeSuggestionAchatHarryPotter() {
		$this->assertEquals('Suggestion d\'achat: Harry Potter', $this->_sent_mail->getSubject());
	}


	/** @test */
	public function mailBodyTextShouldContainsAuteurJKRowling() {
		$this->assertBodyContains('Auteur: J.K.Rowling');
	}


	/** @test */
	public function mailBodyTextShouldContainsISBN1234567890() {
		$this->assertBodyContains('ISBN: 1234567890');
	}


	/** @test */
	public function mailBodyTextShouldContainsURLHarryPotterDotFr() {
		$this->assertBodyContains('Lien: http://harrypotter.fr');
	}


	/** @test */
	public function mailBodyTextShouldContainsCommentaire() {
		$this->assertBodyContains("Commentaire: Je veux le lire");
	}


	/** @test */
	public function mailBodyTextShouldContainsDemandeurSebastienBelle() {
		$this->assertBodyContains("Demandeur: Sébastien Belle");
	}


	/** @test */
	public function mailBodyTextShouldContainsIdAbon98765() {
		$this->assertBodyContains("N° carte abonné: 98765");
	}


	/** @test */
	public function fromShouldBeNoReplyAtAstroMelunDotFr() {
		$this->assertEquals('noreply@astromelun.fr', $this->_sent_mail->getFrom());
	}


	/** @test */
	public function toShouldContainsSBelleAtGmailDotCom() {
		$this->assertContains('sbelle@gmail.com', $this->_sent_mail->getRecipients());
	}


	/** @test */
	public function toShouldContainsLaurentAtAfiDotFr() {
		$this->assertContains('laurent@afi-sa.fr', $this->_sent_mail->getRecipients());
	}


	/** @test */
	public function toShouldContainsPatrickAtAfiDotFr() {
		$this->assertContains('patrick@afi-sa.fr', $this->_sent_mail->getRecipients());
	}

	/** @test */
	public function toShouldContainsEstelleAtAfiDoFr() {
		$this->assertContains('estelle@afi-sa.fr', $this->_sent_mail->getRecipients());
	}
}



class SuggestionAchatMailErrorsTest extends SuggestionAchatTestCase {
	/** @test */
	public function withoutMailSiteShouldSendMailOnlyToUser() {
		Class_Profil::getCurrentProfil()->setMailSuggestionAchat('');
		$this->_suggestion->sendMail('noreply@astromelun.fr');
		$this->assertEquals(['sbelle@gmail.com'], 
												$this->_mock_transport->sent_mail->getRecipients());
	}


	/** @test */
	public function withoutMailUserShouldSendMailOnlyToMailProfil() {
		$this->_suggestion->getUser()->setMail('');
		$this->_suggestion->sendMail('noreply@astromelun.fr');
		$this->assertEquals(['laurent@afi-sa.fr', 'patrick@afi-sa.fr', 'estelle@afi-sa.fr'], 
												$this->_mock_transport->sent_mail->getRecipients());
	}
}

?>
