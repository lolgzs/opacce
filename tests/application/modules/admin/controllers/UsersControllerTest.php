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

abstract class UsersControllerWithMarcusTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->marcus = Class_Users::getLoader()
			->newInstanceWithId(10)
			->setPrenom('Marcus')
			->setNom('Miller')
			->setLogin('mmiller')
			->setMail('marcus@gmail.com')
			->setPseudo('mimi')
			->setPassword('mysecret')
			->setFicheSIGB(array('type_comm' => 0, 'nom_aff' => 'Marcus'))
			->setRole('abonne_sigb')
			->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB)
			->setBib(Class_Bib::getLoader()->newInstanceWithId(1)->setIdZone(null))
			->setIdabon('00123')
			->setOrdreabon(1)
			->setDateDebut('19-07-2009')
			->setDateFin('19-07-2010')
			->setTelephone('01 23 45 67 89')
			->setAdresse('34 avenue Funk')
			->setCodePostal('99000')
			->setVille('Jazz City');
		
		$this->assertTrue($this->marcus->isValid());

		$this->user_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users');
	}

	protected function _postEditData($data) {
		$this->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('/admin/users/edit/id/10');
	}
}



class UsersControllerEditMarcusTest extends UsersControllerWithMarcusTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/users/edit/id/10');
	}


	public function testIdentifiantIsMMiller() {
		$this->assertXPath("//input[@name='username'][@value='mmiller']");
	}

	
	public function testPasswordIsMysecret() {
		$this->assertXPath("//input[@name='password'][@value='mysecret']");
	}

	public function testNomIsMiller() {
		$this->assertXPath("//input[@name='nom'][@value='Miller']");
	}

	public function testPrenomIsMarcus() {
		$this->assertXPath("//input[@name='prenom'][@value='Marcus']");
	}

	public function testSelectedRoleIsAbonneSIGB() {
		$this->assertXPathContentContains("//select[@name='role']/option[@value='2'][@selected='selected']",
																			'abonné identifié SIGB');
	}

	public function testSelectedBibIsIdOne() {
		$this->assertXPath("//input[@name='bib'][@value='1']", $this->_response->getBody());
	}

	public function testMailIsMarcusAtGmailDotCom() {
		$this->assertXPath("//input[@name='mail'][@value='marcus@gmail.com']");
	}

	public function testTelephoneIs0123456789() {
		$this->assertXPath("//input[@name='telephone'][@value='01 23 45 67 89']");
	}

	public function testAdresseIs34avenueFunk() {
		$this->assertXPathContentContains("//textarea[@name='adresse']", '34 avenue Funk');
	}

	public function testCodePostalIs99000() {
		$this->assertXPath("//input[@name='code_postal'][@value='99000']");
	}

	public function testVilleIsJazzCity() {
		$this->assertXPath("//input[@name='ville'][@value='Jazz City']");
	}

	public function testNumeroCarteIs00123() {
		$this->assertXPath("//input[@name='id_abon'][@value='00123']");
	}

	public function testOrdreIsOne() {
		$this->assertXPath("//input[@name='ordre'][@value='1']");
	}

	public function testDateDebut() {
		$this->assertQueryContentContains("td", '19-07-2009');
	}

	public function testDateFin() {
		$this->assertQueryContentContains("td", '19-07-2010');
	}
}


class UsersControllerEditMarcusAsAdminPortailTest extends UsersControllerWithMarcusTestCase {
	public function setUp() {
		parent::setUp();
		$this->marcus->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL);
	}


	/** @test */
	function comboBibShouldBeVisible() {
		$this->dispatch('/admin/users/edit/id/10');
		$this->assertXPath('//select[@name="bib"]');
	}


	/** @test */
	function withoutBibShouldDisplayComboBib() {
		$this->marcus->setBib(null);
		$this->dispatch('/admin/users/edit/id/10');
		$this->assertXPath('//select[@name="bib"]');
	}
}


class UsersControllerPostMarcusDataTest extends UsersControllerWithMarcusTestCase {
	public function setUp() {
		parent::setUp();

		$this->user_loader
			->whenCalled('save')
			->with($this->marcus)
			->answers(true)
			->getWrapper();

		$this->_postEditData(array('username' => 'mdavis',
															 'password' => 'tutu',
															 'nom' => 'Davis',
															 'prenom' => 'Miles',
															 'mail' => 'mdavis@free.fr',
															 'role' => '4',
															 'bib' => '1',
															 'id_abon' => '2341',
															 'ordre' => '2',
															 'telephone' => '09 87 76 54 32 12',
															 'adresse' => '12 rue miles',
															 'code_postal' => '75000',
															 'ville' => 'Paris'));
		$this->assertRedirectTo('/admin/users');
	}

	/** @test */
	function marcusShouldHaveBeenSaved() {
		$this->assertEquals($this->marcus,
												$this->user_loader->getFirstAttributeForLastCallOn('save'));
	}

	public function testLoginIsMDavis() {
		$this->assertEquals('mdavis', $this->marcus->getLogin());
	}

	public function testPasswordIsTutu() {
		$this->assertEquals('tutu', $this->marcus->getPassword());
	}

	public function testNomIsDavis() {
		$this->assertEquals('Davis', $this->marcus->getNom());
	}

	public function testPrenomIsMiles() {
		$this->assertEquals('Miles', $this->marcus->getPrenom());
	}

	public function testTelephoneIs09_87_76_54_32_12() {
		$this->assertEquals('09 87 76 54 32 12', $this->marcus->getTelephone());
	}

	public function testAdresseIs12RueMiles() {
		$this->assertEquals('12 rue miles', $this->marcus->getAdresse());
	}

	public function testCodePostalIs75000() {
		$this->assertEquals('75000', $this->marcus->getCodePostal());
	}

	public function testVilleIsParis() {
		$this->assertEquals('Paris', $this->marcus->getVille());
	}

	public function testMailIsMDavisAtFreeDotFr() {
		$this->assertEquals('mdavis@free.fr', $this->marcus->getMail());
	}

	public function testRoleLevelIsFour() {
		$this->assertEquals(4, $this->marcus->getRoleLevel());
	}

	public function testIdSiteIsOne() {
		$this->assertEquals(1, $this->marcus->getIdSite());
	}

	public function testRoleIsAdministrateur() {
		$this->assertEquals('admin_bib', $this->marcus->getRole());
	}

	public function testOrdreabonIsTwo() {
		$this->assertEquals(2, $this->marcus->getOrdreabon());
	}
}


class UsersControllerPostMarcusInvalidDataTest extends UsersControllerWithMarcusTestCase {
	public function testNoUsernamePasswordAndRole() {
		$this->_postEditData(array('username' => '',
															 'password' => '',
															 'nom' => 'Davis',
															 'prenom' => 'Miles',
															 'mail' => 'mdavis@free.fr',
															 'role' => '4',
															 'bib' => '0',
															 'id_abon' => '2341',
															 'ordre' => '2',
															 'telephone' => '',
															 'adresse' => '',
															 'code_postal' => '',
															 'ville' => ''));
		$this->assertAction('edit');
		$this->assertQueryContentContains('span#abonne_erreur', "Vous devez compléter le champ 'Identifiant'");
		$this->assertQueryContentContains('span#abonne_erreur', "Vous devez compléter le champ 'Mot de passe'");
		$this->assertQueryContentContains('span#abonne_erreur', 
																			"La bibliothèque est obligatoire pour le rôle : administrateur bibliothèque");
	}


	public function testNoCardTooLongUserNameAndPassword() {
		$this->_postEditData(array('username' => 'username with more than 50 characters ******************',
															 'password' => 'password with more than 50 characters ******************',
															 'nom' => 'Davis',
															 'prenom' => 'Miles',
															 'mail' => 'mdavis@free.fr',
															 'role' => '2',
															 'bib' => '1',
															 'id_abon' => '',
															 'ordre' => '2',
															 'telephone' => '04 50 12 34',
															 'adresse' => '',
															 'code_postal' => '',
															 'ville' => ''));
		$this->assertAction('edit');
		$this->assertQueryContentContains('span#abonne_erreur',
																			"Le champ 'Identifiant' doit être inférieur à 50 caractères");
		$this->assertQueryContentContains('span#abonne_erreur',
																			"Le champ 'Mot de passe' doit être inférieur à 50 caractères");
		$this->assertQueryContentContains('span#abonne_erreur',
																			"Le numéro de carte est obligatoire pour les abonnés identifiés dans un sigb.");
	}
}


class UsersControllerPostValidDataWithCommOpsysTest extends UsersControllerWithMarcusTestCase {
	public function setUp() {
		parent::setUp();

		$this->opsys_service = $this->getMock('MockOpsysService', array('saveEmprunteur'));
		$this->emprunteur = new Class_WebService_SIGB_Emprunteur('2341', 'Marcus');
		$this->emprunteur->setService($this->opsys_service);

		$this->marcus->setFicheSIGB(array('type_comm' => Class_CommSigb::COM_OPSYS,
																			'fiche' => $this->emprunteur,
																			'nom_aff' => 'Marcus'));

		$this->user_loader
			->whenCalled('save')
			->with($this->marcus)
			->answers(true);
	}

	protected function _postData() {
		$this->_postEditData(array('username' => 'mdavis',
															 'password' => 'tutu',
															 'nom' => 'Davis',
															 'prenom' => 'Miles',
															 'mail' => 'mdavis@free.fr',
															 'role' => '4',
															 'bib' => '1',
															 'id_abon' => '2341',
															 'ordre' => '2',
															 'telephone' => '04 12 34 56 78',
															 'adresse' => '',
															 'code_postal' => '',
															 'ville' => ''));
	}


	public function testWithNoSIGBErrorRedirectToUsers() {
		$this->opsys_service
			->expects($this->once())
			->method('saveEmprunteur')
			->with($this->emprunteur)
			->will($this->returnValue($this->opsys_service));

		$this->_postData();
		$this->assertRedirectTo('/admin/users');

		$this->assertEquals($this->marcus, 
												$this->user_loader->getFirstAttributeForLastCallOn('save'));
	}


	public function testWithSIGBErrorDisplayErrorMessage() {
		$this->opsys_service
			->expects($this->once())
			->method('saveEmprunteur')
			->with($this->emprunteur)
			->will($this->throwException(new Exception("(234) L'abonné n'existe pas")));

		$this->_postData();
		$this->assertAction('edit');
		$this->assertQueryContentContains('span#abonne_erreur',
																			"(234) L'abonné n'existe pas");
	}
}


class UsersControllerAddViewTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/users/add');
	}

	public function testActionAdd() {
		$this->assertAction('add');
	}

	public function testIdentifiantIsEmpty() {
		$this->assertXPath("//input[@name='username'][@value='']");
	}

	public function testPasswordIsEmpty() {
		$this->assertXPath("//input[@name='password'][@value='']");
	}

	public function testTelephoneIsEmpty() {
		$this->assertXPath("//input[@name='telephone'][@value='']");
	}

}


class UsersControllerAddPostTest extends UsersControllerWithMarcusTestCase {
	public function setUp() {
		parent::setUp();
		$this->user_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users');		
	}



	protected function _postData() {
		$this->getRequest()
			->setMethod('POST')
			->setPost((array('username' => 'mdavis',
											 'password' => 'tutu',
											 'nom' => 'Davis',
											 'prenom' => 'Miles',
											 'mail' => 'mdavis@free.fr',
											 'role' => '4',
											 'bib' => '1',
											 'id_abon' => '2341',
											 'ordre' => '2',
											 'telephone' => '',
											 'adresse' => '',
											 'code_postal' => '',
											 'ville' => '')));
		$this->dispatch('/admin/users/add');
	}


	public function testValidDataRedirectedToUsers() {
		$this->user_loader
			->whenCalled('save')->answers(true)
			->whenCalled('findFirstBy')->answers(null);

		$this->_postData();

		$this->assertRedirectTo('/admin/users');
	}


	public function testExistingLoginError() {
		$this->user_loader
			->whenCalled('save')
			->with($this->marcus)
			->never()
			->getWrapper()

			->whenCalled('findFirstBy')
			->answers(new Class_Users())
			->getWrapper();

		$this->_postData();

		$this->assertAction('add');
		$this->assertQueryContentContains('span#abonne_erreur',
																			"L'identifiant que vous avez choisi existe déjà.");
	}


	public function testSaveFailedRenderAdd() {
		$this->user_loader
			->whenCalled('save')
			->answers(false)
			->getWrapper()

			->whenCalled('findFirstBy')
			->answers(null)
			->getWrapper();

		$this->_postData();

		$this->assertAction('add');
	}
}

?>