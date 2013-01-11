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

class VodeclicLinkTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();
		$this->_jean = Class_Users::getLoader()
			->newInstanceWithId(4)
			->setIdabon(34)
			->setPrenom('Jean')
			->setNom('Mardgay')
			->setMail('jean@golf.fr')
			->setDateFin('2023-09-02');

		Class_AdminVar::getLoader()
			->newInstanceWithId('VODECLIC_KEY')
			->setValeur('2m5js1dPpFNrtAJbsfX1');
		Class_AdminVar::getLoader()
			->newInstanceWithId('VODECLIC_ID')
			->setValeur('bonlieu');

		$this->encrypted_email = hash('sha256', 'jean@golf.fr2m5js1dPpFNrtAJbsfX1');
		$this->encrypted_date = hash('sha256', date('dmY').'2m5js1dPpFNrtAJbsfX1');
		$this->encrypted_id = hash('sha256', '34'.'2m5js1dPpFNrtAJbsfX1');

		$this->_vodeclic = Class_VodeclicLink::forUser($this->_jean);
	}


	/** @test */
	public function urlForJeanShouldBeBiblioSSO() {
		$this->assertEquals('https://biblio.vodeclic.com/auth/biblio/sso', 
												$this->_vodeclic->baseUrl());
	}


	/** @test */
	public function withKey234UrlForJeanShouldContainsEncryptedId_Date_EMail() {
		$this->assertEquals(sprintf('https://biblio.vodeclic.com/auth/biblio/sso?'.
																'email=jean%%40golf.fr&encrypted_email=%s&'.
																'id=34&encrypted_id=%s&'.
																'd=%s&'.
																'partenaire=bonlieu&'.
																'nom=Mardgay&'.
																'prenom=Jean',
																$this->encrypted_email, 
																$this->encrypted_id,
																$this->encrypted_date), 
												$this->_vodeclic->url());
	}


	/** @test */
	public function withoutMailUrlShouldContainsEncryptedMail() {
		$this->_jean->setMail('')->setNom('')->setPrenom('');

		$this->assertEquals(sprintf('https://biblio.vodeclic.com/auth/biblio/sso?'.
																'id=34&encrypted_id=%s&'.
																'd=%s&'.
																'partenaire=bonlieu',
																$this->encrypted_id,
																$this->encrypted_date), 
												$this->_vodeclic->url());
	}
}

?>