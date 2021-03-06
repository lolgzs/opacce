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


class SystemeControllerMailTestActionTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getLoader()->getPortail()->setMailSite('pollux@afi-sa.fr');
			
		$this->dispatch('admin/systeme/mailtest');
	}
	

	/** @test */
	public function titleShouldBeTestEnvoiMail() {
		$this->assertXPathContentContains('//h1', 'Test de l\'envoi des mails');
	}


	/** @test */
	public function menuGaucheAdminShouldContainsLinkToMailTest() {
		$this->assertXPathContentContains('//div[@class="menuGaucheAdmin"]//a[contains(@href, "systeme/mailtest")]', 
																			'Test envoi mails',
																			$this->_response->getBody());
	}


	/** @test */
	public function formShouldContainsInputForSenderWithPortailEmail() {
		$this->assertXPath('//form//input[@name="sender"][@value="pollux@afi-sa.fr"]');
	}


	/** @test */
	public function formShouldContainsInputForRecipient() {
		$this->assertXPath('//form//input[@name="recipient"][@value=""]');
	}


	/** @test */
	public function formShouldContainsSubmitButton() {
		$this->assertXPath('//form//input[@type="submit"]');
	}
}




class SystemeControllerPostMailActionTest extends Admin_AbstractControllerTestCase {
	protected $_mail;

	public function setUp() {
		parent::setUp();
		$mock_transport = new MockMailTransport();
		Zend_Mail::setDefaultTransport($mock_transport);

		$this->postDispatch('admin/systeme/mailtest',
												array('sender' => 'pollux@afi-sa.fr',
															'recipient' => 'castor@afi-sa.fr'));

		$this->_mail = $mock_transport->sent_mail;
	}

	
	/** @test */
	public function mailShouldHaveBeenSentFromPollux() {
		$this->assertEquals('pollux@afi-sa.fr', 
												$this->_mail->getFrom());
	}


	/** @test */
	public function mailShouldHaveBeenSentToCastor() {
		$this->assertEquals('castor@afi-sa.fr', 
												array_first($this->_mail->getRecipients()));
	}


	/** @test */
	public function subjectShouldBeTestMailDepuisOPAC() {
		$this->assertEquals('[AFI-OPAC2.0] test envoi mails', $this->_mail->getSubject());
	}


	/** @test */
	public function bodyTextShouldContainsEnvoyeDepuisBASE_URL() {
		$this->assertEquals('Envoyé depuis '.BASE_URL, 
												quoted_printable_decode($this->_mail->getBodyText()->getContent()));
	}


	/** @test */
	public function pageShouldDisplayEMailCorrectementEnvoye() {
		$this->assertXPathContentContains('//p', 'Le mail a bien été envoyé');
	}
}




class SystemeControllerPostInvalidPostTest extends Admin_AbstractControllerTestCase {
	protected $_mail;

	public function setUp() {
		parent::setUp();
		Zend_Mail::setDefaultTransport(new MockMailTransport());

		$this->postDispatch('admin/systeme/mailtest',
												array('sender' => 'zork', 'recipient' => ''));
	}


	/** @test */
	public function pageShouldDisplayErrorUneValeurEstRequise() {
		$this->assertXPathContentContains('//ul[@class="errors"]', 'Une valeur est requise', $this->_response->getBody());
	}


	/** @test */
	public function pageShouldDisplayErrorZorkNotMailValide() {
		$this->assertXPathContentContains('//ul[@class="errors"]', "'zork' n'est pas un email valide");		
	}
}



class SystemeControllerPHPInfoActionTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/systeme/phpinfo', true);
	}


	/** @test */
	public function pageShouldDisplayPHPInfo() {
		$this->assertXPathContentContains('//div', 'PHP Version');
	}


	/** @test */
	public function menuGaucheAdminShouldContainsLinkToPHPInfoTest() {
		$this->assertXPathContentContains('//div[@class="menuGaucheAdmin"]//a[contains(@href, "systeme/phpinfo")]', 
																			'Informations système');
	}

}

?>