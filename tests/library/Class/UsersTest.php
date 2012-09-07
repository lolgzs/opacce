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

class UserFixtures {
	public static function miles() {
		return array('ID_USER' => 1,
				'LOGIN' => 'mdavis',
				'ROLE' => 'invite',
				'ROLE_LEVEL' => 0,
				'PASSWORD' => 'nifniff',
				'ID_SITE' => 1,
				'NOM' => 'Davis',
				'PRENOM' => 'Miles',
				'DATE_FIN' => '2025-04-26');
	}

	public static function truffaz() {
		return array('ID_USER' => 34,
				'LOGIN' => 'etruffaz',
				'ROLE' => 'invite',
				'ROLE_LEVEL' => 0,
				'PASSWORD' => 'nafnaf',
				'ID_SITE' => 1,
				'NOM' => 'Truffaz',
				'PRENOM' => 'Erik',
				'DATE_FIN' => '2001-10-23');
	}

	public static function all() {
		return array(self::miles(),
				self::truffaz());
	}

}




class UsersTestLoader extends PHPUnit_Framework_TestCase {

	public function testIdFieldIsID_USER() {
		$loader = Class_Users::getLoader();
		$this->assertEquals('id_user', $loader->getIdField());
	}

}




class UsersMilesDavisAttributesTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->miles = Class_Users::getLoader()->newFromRow(UserFixtures::miles());
	}

	public function testID_USER() {
		$this->assertEquals(1, $this->miles->ID_USER);
	}

	public function testNOM() {
		$this->assertEquals('Davis', $this->miles->NOM);
	}

	public function testPRENOM() {
		$this->assertEquals('Miles', $this->miles->PRENOM);
	}


	/** @test */
	public function idSigbShouldDefaultToEmpty() {
		$this->assertEquals('', $this->miles->getIdSigb());
	}

	public function testSetPRENOM() {
		$this->miles->PRENOM = 'Miles Dewey';
		$this->assertEquals('Miles Dewey', $this->miles->PRENOM);
		$this->assertEquals('Miles Dewey', $this->miles->getPrenom());
	}

	public function testSetNOM() {
		$this->miles->NOM = 'DAVIS';
		$this->assertEquals('DAVIS', $this->miles->NOM);
		$this->assertEquals('DAVIS', $this->miles->getNom());
	}

	public function testSetID() {
		$this->miles->ID = 25;
		$this->assertEquals(25, $this->miles->ID_USER);
		$this->assertEquals(25, $this->miles->getId());
	}

	public function testGetUnknownFieldReturnsNull() {
		$this->assertEquals(null, $this->miles->INEXISTANT);
	}

	public function testSetUnknownFieldCreatesIt() {
		$this->miles->INEXISTANT = 12;
		$this->assertEquals(12, $this->miles->INEXISTANT);
		$this->assertEquals(12, $this->miles->getInexistant());
	}

	/** @test */
	function abonnementShouldBeValid() {
		$this->assertTrue($this->miles->isAbonnementValid());
	}

}




class UsersErikTruffazAttributesTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->truffaz = Class_Users::getLoader()->newFromRow(UserFixtures::truffaz());
	}


	/** @test */
	function abonnementShouldNotBeValid() {
		$this->assertFalse($this->truffaz->isAbonnementValid());
	}
}




class UsersTestFindAll extends ModelTestCase {
	public function setUp() {
		$this->_setFindAllExpectation('Class_Users', UserFixtures::all());
		$this->users = Class_Users::getLoader()->findAll();
	}

	public function testFirstIsDavis() {
		$davis = $this->users[0];
		$this->assertEquals(1, $davis->getId());
		$this->assertEquals('Davis', $davis->getNom());
		$this->assertEquals('Miles', $davis->getPrenom());
	}

	public function testSecondIsTruffaz() {
		$truffaz = $this->users[1];
		$this->assertEquals(34, $truffaz->getId());
		$this->assertEquals('Truffaz', $truffaz->getNom());
		$this->assertEquals('Erik', $truffaz->getPrenom());
	}
}




class UsersTestFindById extends ModelTestCase {
	public function testFindByIdOneReturnsMiles() {
		$this->_setFindExpectation('Class_Users', UserFixtures::miles(), 1);
		$miles = Class_Users::getLoader()->find(1);
		$this->assertEquals(1, $miles->getId());
		$this->assertEquals('Davis', $miles->getNom());
	}

	public function testFindByIdThirtyFourReturnsTruffaz() {
		$this->_setFindExpectation('Class_Users', UserFixtures::truffaz(), 34);
		$truffaz = Class_Users::getLoader()->find(34);
		$this->assertEquals(34, $truffaz->getId());
		$this->assertEquals('Truffaz', $truffaz->getNom());
	}

}




class UsersTestSave extends ModelTestCase {
	public function setUp() {
		$this->tbl_users = $this->_buildTableMock('Class_Users', array('insert', 'update', 'select', 'fetchAll'));
		$this->tbl_users
						->expects($this->any())
						->method('select')
						->will($this->returnValue(new Zend_Db_Table_Select(new Storm_Model_Table(array('name' => 'bib_admin_users')))));

		$this->tbl_users
						->expects($this->any())
						->method('fetchAll')
						->will($this->returnValue(new Zend_Db_Table_Rowset(array('data' => array()))));
	}

	public function testSaveNewUser() {
		$this->tbl_users
						->expects($this->once())
						->method('insert')
						->with(array('nom' => 'Coltrane',
												 'prenom' => 'John',
												 'login' => 'jcoltrane',
												 'password' => 'giantsteps',
												 'role_level' => 2,
												 'id_site' => 1,
												 'role' => 'abonne_sigb',
												 'idabon' => '1234',
												 'date_fin' => '',
												 'naissance' => '',
												 'date_debut' => 0,
												 'telephone' => '',
												 'mail' => '',
												 'adresse' => '',
												 'code_postal' => '',
												 'ville' => ''));

		$coltrane = new Class_Users();
		$coltrane
						->setNom('Coltrane')
						->setPrenom('John')
						->setLogin('jcoltrane')
						->setPassword('giantsteps')
						->setRoleLevel(2)
						->setIdSite(1)
						->setRole('abonne_sigb')
						->setIdabon('1234')
						->save();
	}

	public function testSaveExistingUser() {
		$this->tbl_users
			->expects($this->once())
			->method('update')
			->with(array('nom' => 'Truffaz',
									 'prenom' => 'Erik',
									 'login' => 'etruffaz',
									 'role' => 'invite',
									 'role_level' => 0,
									 'password' => 'nafnaf',
									 'id_site' => 1,
									 'mail' => 'erik@truffaz.com',
									 'id_user' => 34,
									 'date_fin' => '2001-10-23',
									 'idabon' => '',
									 'naissance' => '',
									 'date_debut' => 0,
									 'telephone' => '',
									 'adresse' => '',
									 'code_postal' => '',
									 'ville' => ''), 
						 'id_user=\'34\'');

		Class_Users::getLoader()
						->newFromRow(UserFixtures::truffaz())
						->setMail('erik@truffaz.com')
						->save();
	}
}




class UsersTestDelete extends ModelTestCase {

	protected function _expectDelete($id, $fixture) {
		$this
						->_buildTableMock('Class_Users', array('delete'))
						->expects($this->once())
						->method('delete')
						->with('id_user=' . $id);

		Class_Users::getLoader()
						->newFromRow($fixture)
						->delete();
	}

	public function testDeleteMilesCallsDeleteWithIdOne() {
		$this->_expectDelete(1, UserFixtures::miles());
	}

	public function testDeleteTruffazCallsDeleteWithId34() {
		$this->_expectDelete(34, UserFixtures::truffaz());
	}

}




class UsersTestAssociations extends ModelTestCase {

	public function setUp() {
		$this->haute_savoie = Class_Zone::getLoader()
						->newInstanceWithId(74)
						->setLibelle('Haute-Savoie');

		$this->annecy = Class_Bib::getLoader()
						->newInstanceWithId(23)
						->setIdZone(74)
						->setLibelle('Annecy');

		$this->cran = Class_Bib::getLoader()
						->newInstanceWithId(74960)
						->setIdZone(74)
						->setLibelle('Cran');

		$this->fanfoue = Class_Users::getLoader()
						->newInstanceWithId(98)
						->setIdSite(23)
						->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB)
						->setPseudo('Fanfoue');

		$this->robert = Class_Users::getLoader()
						->newInstanceWithId(43)
						->setIdSite(74960)
						->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB)
						->setPseudo('Robert');

		$this->marcel = Class_Users::getLoader()
						->newInstanceWithId(47)
						->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL)
						->setPseudo('Marcel');

		$this->calimero = Class_Users::getLoader()
						->newInstanceWithId(97)
						->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB)
						->setPseudo('Calimero');

		$this->article_concert = Class_Article::getLoader()
						->newInstanceWithId(29)
						->setCategorie(Class_ArticleCategorie::getLoader()
										->newInstanceWithId(70)
										->setIdSite(23));
	}

	/** @test */
	function userBibShouldBeAnnecy() {
		$this->assertEquals('Annecy', $this->fanfoue->getBib()->getLibelle());
	}

	/** @test */
	function annecyZoneShouldBeHauteSavoie() {
		$this->assertEquals('Haute-Savoie', $this->annecy->getZone()->getLibelle());
	}

	/** @test */
	function userZoneShouldBeHauteSavoie() {
		$this->assertEquals('Haute-Savoie', $this->fanfoue->getZone()->getLibelle());
	}

	/** @test */
	function fanfoueCanEditArticleConcertShouldBeTrue() {
		$this->assertTrue($this->fanfoue->canEditArticle($this->article_concert));
	}

	/** @test */
	function marcelCanEditArticleConcertShouldBeTrue() {
		$this->assertTrue($this->marcel->canEditArticle($this->article_concert));
	}

	/** @test */
	function robertCanEditArticleConcertShouldBeFalse() {
		$this->assertFalse($this->robert->canEditArticle($this->article_concert));
	}

	/** @test */
	function calimeroCanEditArticleConcertShouldBeFalse() {
		$this->assertFalse($this->calimero->canEditArticle($this->article_concert));
	}

}




class UsersTestAge extends ModelTestCase {
	public function setUp() {
		$this->user = new Class_Users();
		$this->user->setNaissance('1980-03-19');
		
	}

	/** @test */
	public function AgeShouldBeFiveOnYear1985() {
		$this->user->setToday('1985/10/19');
		$this->assertEquals(5,$this->user->getAge());
	}

	
	/** @test */
	public function AgeShouldBeFiveOn18March1986() {
		$this->user->setToday('1986/03/18');
		$this->assertEquals(5,$this->user->getAge());
	}
	
	
		/** @test */
	public function AgeShouldBeFiveOn19March1986() {
		$this->user->setToday('1986/03/19');
		$this->assertEquals(6,$this->user->getAge());
	}
	
	
	/** @test */
	public function AgeShouldBeNullWithoutDateNaissance() {
		$this->user->setNaissance('');
		$this->assertNull($this->user->getAge());
	}
	
	
	/** @test */
	public function getTodayShouldBeToday(){
		$this->assertEquals(date('Y/m/d'),$this->user->getToday());
	}
}




abstract class UsersMailingActionTestCase extends Storm_Test_ModelTestCase {
	protected $mock_transport, $user ,$mock_sql;

	public function setUp() {
		parent::setUp();
		$this->mock_transport = new MockMailTransport();
		Zend_Mail::setDefaultTransport($this->mock_transport);


		$this->user = new Class_Users();

		$this->mock_sql = Storm_Test_ObjectWrapper::on(Zend_Registry::get('sql'));
		Zend_Registry::set('sql', $this->mock_sql);


		Class_CosmoVar::getLoader()
			->newInstanceWithId('mail_admin')
			->setValeur('admin@afi-sa.fr');
	}


	public function tearDown() {
		Zend_Registry::set('sql', $this->mock_sql->getWrappedObject());
		parent::tearDown();
	}
}




class UsersLostPassTest extends UsersMailingActionTestCase {
	protected $ret, $mail;

	public function setUp() {
		parent::setUp();

		$this->mock_sql
			->whenCalled('fetchEnreg')
			->with("Select * from bib_admin_users where LOGIN='zork'", false)
			->answers(array('LOGIN' => 'zork',
											'MAIL' => 'zork@afi.fr',
											'PASSWORD' => '123'))
			->beStrict();

		$this->ret = $this->user->lostPass('zork');
		$this->mail = $this->mock_transport->sent_mail;
	}

	
	/** @test */
	public function retShouldContainsUnMailViensDetreEnvoye() {
		$this->assertContains('Un mail vient de vous', $this->ret['message_mail']);
	}


	/** @test */
	public function mailShouldHaveBeenSentToZork() {
		$this->assertContains('zork@afi.fr', $this->mail->getRecipients());
	}


	/** @test */
	public function mailShouldContainsLoginAndPassword() {
		$body = quoted_printable_decode($this->mail->getBodyText()->getContent());
		$this->assertContains('Votre identifiant : zork', $body);
		$this->assertContains('Votre mot de passe : 123', $body);
	}
}




class UsersRegistrationTest extends UsersMailingActionTestCase {
	protected $user;

	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('REGISTER_OK')
			->setValeur('');

		$this->mock_sql
			->whenCalled('fetchOne')
			->answers(0)

			->whenCalled('insert')
			->answers(true)
			
			->whenCalled('fetchOne')
			->with("select count(*) from bib_admin_users_non_valid Where MAIL='zork@afi-sa.fr'")
			->answers(1)


			->whenCalled('fetchOne')
			->with("select count(*) from bib_admin_users Where MAIL='glub@afi-sa.fr'")
			->answers(1);


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('findFirstBy')
			->answers(false)

			->whenCalled('findFirstBy')
			->with(array('login' => 'laurent'))
			->answers(true);
	}


	/** @test */
	public function verifMailWithMalformedAdressShouldReturnFalse() {
		$this->assertFalse($this->user->verifMail('wrong'));
	}


	/** @test */
	public function verifMailWithZorkAtAfiSaDotFrShouldReturnFalse() {
		$this->assertFalse($this->user->verifMail('zork@afi-sa.fr'));
	}


	/** @test */
	public function verifMailWithGlubAtAfiSaDotFrShouldReturnFalse() {
		$this->assertFalse($this->user->verifMail('glub@afi-sa.fr'));
	}


	/** @test */
	public function verifMailWithLlaffontAtAfiSaDotFrShouldReturnTrue() {
		$this->assertTrue($this->user->verifMail('llaffont@afi-sa.fr'));
	}


	/** @test */
	public function registerUserWithLoginLaurentShouldReturnErrors() {
		$_SESSION['captcha_code'] = '1234';
		$ret = $this->user->registerUser(array('login' => 'laurent',
																					 'mail' => '',
																					 'test_mail' => '',
																					 'mdp' => 'bug',
																					 'mdp2' => 'hoho',
																					 'captcha' => '--'));
		$this->assertContains('Cet identifiant existe déjà', $ret['error']);
		$this->assertContains('Vous n\'avez pas saisi les mêmes mots de passe', $ret['error']);
		$this->assertContains('L\'adresse e-mail est invalide ou est déjà utilisée.', $ret['error']);
	}


	/** @test */
	public function registerUserWithLoginMarioShouldSendMail() {
		$_SESSION['captcha_code'] = '1234';
		$ret = $this->user->registerUser(array('login' => 'mario',
																					 'mail' => 'mario@afi-sa.fr',
																					 'test_mail' => 'mario@afi-sa.fr',
																					 'mdp' => 'secret',
																					 'mdp2' => 'secret',
																					 'captcha' => '1234',
																					 'cle' => 'xxx'));
		$mail = $this->mock_transport->sent_mail;

		$this->assertContains('mario@afi-sa.fr', $mail->getRecipients());
		$this->assertContains('admin@afi-sa.fr', $mail->getFrom());

		$this->assertContains('Vous avez fait une demande d\'inscription', 
													quoted_printable_decode($mail->getBodyText()->getContent()));

		$this->assertContains('Un mail viens de vous être envoyé pour confirmer votre inscription', 
													$ret['message_mail']);
	}
}



class UsersFicheAbonneTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_astro = Class_IntBib::getLoader()
			->newInstanceWithId(5)
			->setCommParams(array("url_serveur" => 'http://astrolabe.com/opsys.wsdl'))
			->setCommSigb(2);

		$this->webservice = Storm_Test_ObjectWrapper::mock()->whenCalled('isConnected')->answers(true);

		Class_WebService_SIGB_Opsys::setService($this->webservice);

		$this->patrick = Class_Users::getLoader()
			->newInstanceWithId(666)
			->setLogin('patrick')
			->setIdSite(5);

		$this->amadou = Class_Users::getLoader()
			->newInstanceWithId(123)
			->setLogin('amadou')
			->setIdSite(5)
			->setIdabon(123);
		Class_WebService_SIGB_EmprunteurCache::newInstance()->remove($this->amadou);


		$this->pret_potter = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
			->setTitre('Harry Potter')
			->setDateRetour('23/05/2022');
		$this->pret_alice = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
			->setTitre('Alice pays merveille')
			->setDateRetour('23/05/2001');
		$this->pret_alien = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
			->setTitre('Alien')
			->setDateRetour('23/05/2002');

		$this->reservation_alien = Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire()->setTitre('Alien');

		$this->webservice
			->whenCalled('getEmprunteur')
			->with($this->amadou)
			->answers(Class_WebService_SIGB_Emprunteur::newInstance()
								->empruntsAddAll(array($this->pret_potter, $this->pret_alice, $this->pret_alien))
								->reservationsAddAll(array($this->reservation_alien)));

	}


	/** @test */
	public function withoutIdAbonShouldReturnErrorVousDevezVousConnecter() {
		$this->assertContains("Vous devez vous connecter", 
													array_at('message', $this->patrick->getFicheSigb()));
	}


	/** @test */
	public function withoutIdAbonGetEmpruntsShouldReturnEmptyArray() {
		$this->assertEquals(array(),$this->patrick->getEmprunts());
	}


	/** @test */
	public function withoutIdAbonGetReservationssShouldReturnEmptyArray() {
		$this->assertEquals(array(),$this->patrick->getReservations());
	}


	/** @test */
	public function getEmpruntsShouldConnectToWebService() {
		$this->assertEquals(array($this->pret_alice, $this->pret_alien, $this->pret_potter), 
												$this->amadou->getEmprunts());
	}


	/** @test */
	public function nbEmpruntsShouldReturnThree() {
		$this->assertEquals(3, $this->amadou->getNbEmprunts());
	}


	/** @test */
	public function nbEmpruntsRetardShouldReturnTwo() {
		$this->assertEquals(2, $this->amadou->getNbEmpruntsRetard());
	}


	/** @test */
	public function getReservationsShouldConnectToWebService() {
		$this->assertEquals(array($this->reservation_alien), $this->amadou->getReservations());
	}


	/** @test */
	public function nbReservationsShouldReturnOne() {
		$this->assertEquals(1, $this->amadou->getNbReservations());
	}
}


?>