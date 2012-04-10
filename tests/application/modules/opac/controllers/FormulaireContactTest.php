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

class FormulaireContactNewTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/index/formulairecontact');
	}

	/** @test */
	public function formShouldHaveClassZendForm() {
		$this->assertXPath('//form[@class="zend_form form"]');
	}

	/** @test */
	public function formShouldContainsInputForNom() {
		$this->assertXPath('//form//input[@name="nom"]');
	}


	/** @test */
	public function formShouldContainsInputForPrenom() {
		$this->assertXPath('//form//input[@name="prenom"]');
	}


	/** @test */
	public function formShouldContainsInputForAdresse() {
		$this->assertXPath('//form//input[@name="adresse"]');
	}

	/** @test */
	public function formShouldContainsInputForCodePostal() {
		$this->assertXPath('//form//input[@name="code_postal"]');
	}

	/** @test */
	public function formShouldContainsInputForVille() {
		$this->assertXPath('//form//input[@name="ville"]');
	}

	/** @test */
	public function formShouldContainsInputForEmail() {
		$this->assertXPath('//form//input[@name="mail"]');
	}

	/** @test */
	public function formShouldContainsInputForSujet() {
		$this->assertXPath('//form//input[@name="sujet"]');
	}

	/** @test */
	public function formShouldContainsTextAreaForMessage() {
		$this->assertXPath('//form//textarea[@name="message"]');
	}
}


class FormulaireContactInvalidPostTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/opac/index/formulairecontact',
												array('ville' => 'Annecy',
															'mail' => 'blabla'));
	}

	/** @test */
	public function inputVilleShouldContainsAnnecy() {
		$this->assertXPath('//input[@name="ville"]', 'Annecy');
	}

	/** @test */
	public function errorsShouldDisplayEmailInvalide() {
		$this->assertXPathContentContains('//ul[@class="errors"]', 'blabla');
	}
}


abstract class FormulaireContactValidPostTestCase extends AbstractControllerTestCase {
	protected $_mail;

	public function sendValidMail() {
		$mock_transport = new MockMailTransport();
		Zend_Mail::setDefaultTransport($mock_transport);

		$this->postDispatch('/opac/index/formulairecontact',
												array('nom' => 'Laffont',
															'prenom' => 'Manon',
															'adresse' => 'rue zork',
															'code_postal' => '74000',
															'ville' => 'Annecy',
															'sujet' => 'carambars',
															'message' => 'il en faut plus',
															'mail' => 'manon@laffont.com'));
		$this->_mail = $mock_transport->sent_mail;
	}
}


class FormulaireContactValidPostTest extends FormulaireContactValidPostTestCase {
	public function setUp() {
		parent::setUp();
		defineConstant('NOCAPTCHA', true);
		Class_Profil::getCurrentProfil()->setMailSite('laurent@afi-sa.fr');
		$this->sendValidMail();
	}


	/** @test */
	public function subjectShouldBeCarambarsWithPrefixOpac() {
		$this->assertEquals('[AFI-OPAC] carambars', $this->_mail->getSubject());
	}


	/** @test */
	public function fromShouldBeNoReplyAtLocalhost() {
		$this->assertEquals('no-reply@localhost', $this->_mail->getFrom());
	}

	
	/** @test */
	public function toShouldContainsLaurentAtAfiDotFr() {
		$this->assertContains('laurent@afi-sa.fr', $this->_mail->getRecipients());
	}


	protected function assertBodyContains($text) {
		$this->assertContains($text, 
													quoted_printable_decode($this->_mail->getBodyText()->getContent()));
	}

	/** @test */
	public function bodyShouldContainsIlEnFautPlus() {
		$this->assertBodyContains('il en faut plus');
	}


	/** @test */
	public function bodyShouldContainsLaffont() {
		$this->assertBodyContains('Nom: Laffont');
	}


	/** @test */
	public function bodyShouldContainsManon() {
		$this->assertBodyContains('Prénom: Manon');
	}


	/** @test */
	public function bodyShouldContainsAdresse() {
		$this->assertBodyContains('Adresse: rue zork');
	}


	/** @test */
	public function bodyShouldContainsVille() {
		$this->assertBodyContains('Ville: Annecy');
	}


	/** @test */
	public function bodyShouldContainsCodePostal() {
		$this->assertBodyContains('Code postal: 74000');
	}


	/** @test */
	public function bodyShouldContainsEMail() {
		$this->assertBodyContains('E-mail: manon@laffont.com');
	}

	/** @test */
	public function answerShouldRedirectToFormulaireContactSent() {
		$this->assertRedirectTo('/index/formulairecontactsent');
	}
}


class FormulaireContactValidPostWithoutMailCurrentProfilTest extends FormulaireContactValidPostTestCase {
	/** @test */
	public function toShouldContainsMailPortailIfExists() {
		parent::setUp();

		Class_Profil::getCurrentProfil()->setMailSite('');
		Class_Profil::getLoader()
			->newInstanceWithId(1)
			->setMailSite('jp@afi-sa.fr');

		$this->sendValidMail();
		$this->assertNotEmpty($this->_mail);
		$this->assertContains('jp@afi-sa.fr', $this->_mail->getRecipients());
	}

	/** @test */
	public function mailShouldNotBeSentIfMailPortailDoesNotExists() {
		Class_Profil::getCurrentProfil()->setMailSite('');
		Class_Profil::getLoader()
			->newInstanceWithId(1)
			->setMailSite('');
		$this->sendValidMail();

		$this->assertNull($this->_mail);
		$this->assertRedirectTo('/index/formulairecontacterror');
	}
}




class FormulaireContactRelatedActionsTest extends AbstractControllerTestCase {
	/** @test */
	public function formulaireContectSentShouldDisplaySuccess() {
		$this->dispatch('/index/formulairecontactsent');
		$this->assertXPathContentContains('//p', 'Le message a bien été envoyé');
	}


	/** @test */
	public function formulaireContectErrorShouldDisplayError() {
		$this->dispatch('/index/formulairecontacterror');
		$this->assertXPathContentContains('//p', "Erreur d'envoi: problème de configuration");
	}
}


?>