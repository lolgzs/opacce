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

class ArteVodLinkTest extends Storm_Test_ModelTestCase {
	protected $_james_bond;
	protected $_arte_vod_link;

	public function setUp() {
		parent::setUp();
		$entre_les_murs = Class_Album::newInstanceWithId(5)
			->beArteVOD()
			->setExternalUri('http://www.mediatheque-numerique.com/films/entre-les-murs')
			->setTitle('Entre les murs');

		$this->_james_bond = Class_Users::newInstanceWithId(45)
			->setIdabon(45)
			->setPrenom('James')
			->setNom('Bond')
			->setMail('jbond@007.fr')
			->setDateFin('2023-09-12');

		$this->_arte_vod_link = Class_ArteVodLink::forAlbumAndUser($entre_les_murs, $this->_james_bond);
	}


	/** @test */
	public function baseUrlForBondShouldBeMediathequeNumeriqueDotCom() {
		$this->assertEquals('http://www.mediatheque-numerique.com/films/entre-les-murs', $this->_arte_vod_link->baseUrl());
	}


	/** @test */
	public function withKeySECRETUrlShouldContainsEncryptedDate() {
		Class_AdminVar::newInstanceWithId('ARTEVOD_SSO_KEY')->setValeur('GOGO');

		$this->assertEquals('http://www.mediatheque-numerique.com/films/entre-les-murs'
												.'?sso_id=afi'
												.'&id=45'
												.'&d='. hash('sha256', date('dmY').'GOGO')
												.'&prenom=James'
												.'&nom=Bond'
												.'&email='.urlencode('jbond@007.fr'),
												
												$this->_arte_vod_link->url());
	}

	/** @test */
	public function userWithoutNomEmailAndPrenomUrlSSOShouldNotContainsNomEmailPrenom() {
		Class_AdminVar::newInstanceWithId('ARTEVOD_SSO_KEY')->setValeur('secret');

		$this->_james_bond
			->setPrenom('')
			->setNom('')
			->setMail('');

		$this->assertEquals('http://www.mediatheque-numerique.com/films/entre-les-murs'
												.'?sso_id=afi'
												.'&id=45'
												.'&d='. hash('sha256', date('dmY').'secret'),
												
												$this->_arte_vod_link->url());
	}
}


?>