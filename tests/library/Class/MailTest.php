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

class Class_MailTesting extends Class_Mail {
	protected 
		$_destinataire, 
		$_sujet, 
		$_body, 
		$_headers, 
		$_mail_return_value;

	public function mail($destinataire, $sujet, $body, $headers) {
		$this->_destinataire = $destinataire;
		$this->_sujet = $sujet;
		$this->_body = $body;
		$this->_headers = $headers;
		return $this->_mail_return_value;
	}

	public function mailReturns($value) {
		$this->_mail_return_value = $value;
	}

	public function getDestinataire() {
		return $this->_destinataire;
	}

	public function getSujet() {
		return $this->_sujet;
	}

	public function getBody() {
		return $this->_body;
	}

	public function getMailHeaders() {
		return $this->_headers;
	}
}



class MailToZorkFromFlorenceTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		Class_CosmoVar::getLoader()
			->newInstanceWithId('mail_admin')
			->setValeur('florence@astrolabe-melun.fr');

		$this->_mail_testing = new Class_MailTesting();
		$this->_mail_testing->mailReturns(true);

		$this->_status = $this->_mail_testing->sendMail('Bienvenue !', 
																										'Vous êtes inscrit',
																										'zork@gmail.com');
	}


	/** @test */
	function statusShouldBeEmptyString() {
		$this->assertEquals('', $this->_status);
	}


	/** @test */
	function destinataireShouldBeZork() {
		$this->assertEquals('zork@gmail.com', $this->_mail_testing->getDestinataire());
	}


	/** @test */
	function sujetShouldBeBienvenue() {
		$this->assertEquals('Bienvenue !', $this->_mail_testing->getSujet());
	}


	/** @test */
	function bodyShouldBeVousEtesInscrit() {
		$this->assertEquals('Vous êtes inscrit', $this->_mail_testing->getBody());
	}


	/** @test */
	function headersShouldContainSenderFlorence() {
		$this->assertContains('From: florence@astrolabe-melun.fr', 
													$this->_mail_testing->getMailHeaders());
	}
}




class MailErrorsTest extends PHPUnit_Framework_TestCase {
	/** @test */
	function withoutMailAdminShouldReturnErrorMessage() {
		Class_CosmoVar::getLoader()
			->newInstanceWithId('mail_admin')
			->setValeur('');
		$mail_testing = new Class_MailTesting();
		$mail_testing->mailReturns(true);

		$status = $mail_testing->sendMail('Bienvenue !', 
																			'Vous êtes inscrit',
																			'zork@gmail.com');
		$this->assertContains("Les paramètres d'envoi de mails du portail sont incomplets.",
													$status);
	}


	/** @test */
	function withWrongToShouldReturnErrorMessage() {
		Class_CosmoVar::getLoader()
			->newInstanceWithId('mail_admin')
			->setValeur('laurent@gmail.com');
		$mail_testing = new Class_MailTesting();
		$mail_testing->mailReturns(true);

		$status = $mail_testing->sendMail('Bienvenue !', 'Ici',
																			'zork@g*mail.com');
		$this->assertContains("L'adresse e-mail du destinataire est incorrecte.",
													$status);
	}


	/** @test */
	function withMailErrorShouldReturnErrorMessage() {
		Class_CosmoVar::getLoader()
			->newInstanceWithId('mail_admin')
			->setValeur('laurent@gmail.com');
		$mail_testing = new Class_MailTesting();
		$mail_testing->mailReturns(false);

		$status = $mail_testing->sendMail('Bienvenue !', 
																			'Vous êtes inscrit',
																			'zork@gmail.com');
		$this->assertContains("Les paramètres d'envoi de mails du portail sont incomplets.",
													$status);
	}
}


?>