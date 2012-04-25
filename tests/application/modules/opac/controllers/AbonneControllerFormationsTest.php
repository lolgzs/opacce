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

abstract class AbstractAbonneControllerFormationsTestCase extends AbstractControllerTestCase {
	protected $_amadou;
	protected $_learn_java;
	protected $_learn_python;
	protected $_learn_smalltalk;
	protected $_session_smalltalk_janvier;
	protected $_session_smalltalk_juillet;
	protected $_session_java_mars;
	protected $_session_java_fevrier;
	protected $_session_java_septembre;
	protected $_session_python_juillet;
	protected $_gallice_cafe;
	protected $_bib_romains;
	protected $_bonlieu;

	protected function _loginHook($account) {
		$account->ROLE = "abonne_sigb";
		$account->ROLE_LEVEL = 2;
		$account->ID_USER = '435';
		$account->PSEUDO = "Amadou";
	}

	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('FORMATIONS')
			->setValeur('1');

		$this->_gallice_cafe = Class_Lieu::getLoader()
													->newInstanceWithId(98)
													->setLibelle('Gallice');

		$this->_bib_romains = Class_Lieu::getLoader()
													->newInstanceWithId(99)
													->setLibelle('Bibliothèque des romains');

		$this->_bonlieu = Class_Lieu::getLoader()
													->newInstanceWithId(100)
													->setLibelle('Bonlieu')
													->setAdresse("1, rue Jean-Jaures\nBP 294")
													->setCodePostal(74007)
													->setVille('Annecy');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('save')->answers(true);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SessionFormation')
			->whenCalled('save')->answers(true);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Formation')
			->whenCalled('save')->answers(true)
			->whenCalled('findAll')->answers(array(
																						 $this->_learn_smalltalk = Class_Formation::getLoader()
																						 ->newInstanceWithId(1)
																						 ->setLibelle('Learn Smalltalk')
																						 ->setSessions(array( 
																																	$this->_session_smalltalk_janvier = Class_SessionFormation::getLoader()
																																	->newInstanceWithId(11)
																																	->setFormationId(1)
																																	->setEffectifMin(1)
																																	->setEffectifMax(10)
																																	->setStagiaires(array())
																																	->setLieu($this->_gallice_cafe)
																																	->setDateDebut('2009-01-17'),


																																	$this->_session_smalltalk_juillet = Class_SessionFormation::getLoader()
																																	->newInstanceWithId(12)
																																	->setFormationId(1)
																																	->setEffectifMin(1)
																																	->setEffectifMax(10)
																																	->setStagiaires(array())
																																	->setLieu($this->_gallice_cafe)
																																	->setDateDebut('2023-07-12')
																																	)),

																						 $this->_learn_java = Class_Formation::getLoader()
																						 ->newInstanceWithId(3)
																						 ->setLibelle('Learn Java')
																						 ->setDescription('If you want to')
																						 ->setSessions(array( $this->_session_java_mars = Class_SessionFormation::getLoader()
																																	->newInstanceWithId(32)
																																	->setFormationId(3)
																																	->setDateDebut('2022-03-27') //Pour toi, cher développeur de 2022 :)
																																	->setStagiaires(array())
																																	->setDateLimiteInscription('2009-02-15'),

																																	$this->_session_java_fevrier = Class_SessionFormation::getLoader()
																																	->newInstanceWithId(31)
																																	->setFormationId(3)
																																	->setEffectifMin(2)
																																	->setEffectifMax(5)
																																	->setStagiaires(array())
																																	->setLieu($this->_bonlieu)
																																	->setDateDebut('2022-02-17') 
																																	->setDateLimiteInscription('2022-02-15'),


																																	$this->_session_java_septembre = Class_SessionFormation::getLoader()
																																	->newInstanceWithId(30)
																																	->setFormationId(3)
																																	->setDateDebut('2022-09-27') 
																																	->setStagiaires(array())
																																	->setDateLimiteInscription('2022-02-15')
																																	->beAnnule()
																																	)),

																						 $this->_learn_python = Class_Formation::getLoader()
																						 ->newInstanceWithId(12)
																						 ->setLibelle('Learn Python')
																						 ->setSessions(array( $this->_session_python_juillet = Class_SessionFormation::getLoader()
																																	->newInstanceWithId(121)
																																	->setFormationId(12)
																																	->setDateDebut('2023-07-21')
																																	->setContenu('Introduction a la syntaxe')
																																	->setObjectif('Ecrire un premier programme')
																																	->setEffectifMin(5)
																																	->setEffectifMax(22)
																																	->setDuree(8)
																																	->setHoraires('8h-12h, 14h-18h')
																																	->setLieu($this->_bib_romains)
																																	->setIntervenants( array(Class_Users::getLoader()
																																													 ->newInstanceWithId(76)
																																													 ->setLogin('jpp')
																																													 ->setPrenom('Jean-Paul')
																																													 ->setNom('Pirant'),

																																													 Class_Users::getLoader()
																																													 ->newInstanceWithId(77)
																																													 ->setLogin('cc')
																																													 ->setPrenom('Christophe')
																																													 ->setNom('Cerisier')) ) ))
																						 ));

		$this->_amadou = Class_Users::getLoader()
			->newInstanceWithId('435')
			->setLogin('Amadou')
			->setPassword('123')
			->setRole('abonne_sigb')
			->setIdabon('435')
			->setSessionFormations(array($this->_session_python_juillet))
			->setUserGroups(array(Class_UserGroup::getLoader()
														->newInstanceWithId(23)
														->addRightSuivreFormation()));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SessionFormationInscription')
			->whenCalled('save')->answers(true)
			->whenCalled('delete')->answers(true)

			->whenCalled('findAllBy')
			->with(array('role' => 'stagiaire',
									 'model' => $this->_amadou))
			->answers(array($inscription_amadou_python = Class_SessionFormationInscription::getLoader()
																						 ->newInstanceWithId(1)
																						 ->setStagiaire($this->_amadou)
																						 ->setSessionFormation($this->_session_python_juillet)))
			->whenCalled('findAllBy')
			->with(array('role' => 'session_formation',
									 'model' => $this->_session_python_juillet))
			->answers(array($inscription_amadou_python));
	}
}


class AbonneControllerFormationsListTest extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/formations');
	}	

	/** @test */
	function aH2ShouldContainsLearnJava() {
		$this->assertXPathContentContains('//h2', 'Learn Java');
	}


	/** @test */
	function aDivForDescriptionShouldContainsIfYouWantTo() {
		$this->assertXPathContentContains('//div', 'If you want to');
	}


	/** @test */
	function aH2ShouldContainsLearnPython() {
		$this->assertXPathContentContains('//h2', 'Learn Python');
	}


	/** @test */
	function session_java_mars_ShouldNotHaveLinkForInscrireAsInscriptionClosed() {
		$this->assertNotXPath('//a[contains(@href, "abonne/inscrire_session/id/32")]');
	}


	/** @test */
	function session_java_septembre_ShouldNotHaveLinkForInscrireAsSessionAnnule() {
		$this->assertNotXPath('//a[contains(@href, "abonne/inscrire_session/id/30")]');
	}


	/** @test */
	function session_janvier_smalltalk_ShouldNotHaveLinkForInscrireAsFinished() {
		$this->assertNotXPath('//a[contains(@href, "abonne/inscrire_session/id/11")]');
	}


	/** @test */
	function session_juillet_smalltalk_ShouldNotDisplayLimiteAsNotSet() {
		$this->assertNotXPathContentContains('//td[@class="session_12"]', 'Limite:');
	}



	/** @test */
	function session_fevrier_17_ShouldBeDisplayedUnderLearnJavaInSecondPosition() {
		$this->assertXPathContentContains('//tr[2]//td', '17 février 2022');
	}


	/** @test */
	function session_fevrier_17_lieuBonlieuShouldBeDisplayed() {
		$this->assertXPathContentContains('//tr[2]//td', 'Bonlieu');
	}


	/** @test */
	function session_fevrier_17_ShouldHaveLinkForInscrire() {
		$this->assertXPathContentContains('//tr[2]//a[contains(@href, "abonne/inscrire_session/id/31")]',
																			"S'inscrire");
	}


	/** @test */
	function session_fevrier_17_ShouldDisplayDateLimite15Fevrier() {
		$this->assertXPathContentContains('//tr[2]',
																			"Limite: 15 février 2022");
	}


	/** @test */
	function session_fevrier_17_ShouldHaveLinkForDetailSessionFormation() {
		$this->assertXPathContentContains('//tr[2]//a[contains(@href, "abonne/detail_session/id/31")]', 
																			'Détails de la session');
	}


	/** @test */
	function session_mars_27_ShouldBeDisplayedUnderLearnJavaInFirstPosition() {
		$this->assertXPathContentContains('//tr[1]', '27 mars 2022');
	}


	/** @test */
	function session_septembre_java_ShouldBeAnnule() {
		$this->assertXPathContentContains('//tr', '27 septembre 2022 (Annul');
	}



	/** @test */
	function session_python_juillet_ShouldHaveLinkForDesinscrire() {
		$this->assertXPathContentContains('//tr//a[contains(@href, "abonne/desinscrire_session/id/121")]',
																			"Se désinscrire");
	}



	/** @test */
	function boiteTitleShouldBeFormations() {
		$this->assertXPathContentContains('//h1', 'Formations');
	}
}




class AbonneControllerFormationsFicheAbonneTest extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/fiche/retour/fiche');
	}

	/** @test */
	public function pageShouldContainsLinkToFormations() {
		$this->assertXPathContentContains('//a[contains(@href, "abonne/formations")]', 'S\'inscrire à une formation');
	}


	/** @test */
	public function pageShouldContainsVousEtesInscritFormationPython() {
		$this->assertXPathContentContains('//ul//li', 'Learn Python, 21 juillet 2023');
	}


	/** @test */
	public function pageShouldContainsLinkToDetailSessionPyhton() {
		$this->assertXPath('//li//a[contains(@href, "abonne/detail_session/id/121/retour/fiche")]');
	}
}




Class AbonneControllerFormationsFicheAbonneWithoutSufficientRigthsTest extends AbstractAbonneControllerFormationsTestCase {
	/** @test */
	public function whenFormationsDisabledPageShouldNotContainsLinkToFormations() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('FORMATIONS')
			->setValeur('0');
		$this->dispatch('/opac/abonne/fiche');
		$this->assertNotXPath('//a[contains(@href, "abonne/formations")]');
	}


	/** @test */
	public function whenUserNotStagiairePageShouldNotContainsLinkToFormations() {
		$this->_amadou->setUserGroups(array());
		$this->dispatch('/opac/abonne/fiche');
		$this->assertNotXPath('//a[contains(@href, "abonne/formations")]');
	}
}




class AbonneControllerFormationsListWithoutRightSuivreFormationTest extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this->_amadou->setUserGroups(array());
		$this->dispatch('/opac/abonne/formations');
	}


	/** @test */
	function linkForInscrireShouldNotExists() {
		$this->assertNotXPath('//a[contains(@href, "abonne/inscrire_session")]');
	}


	/** @test */
	function linkForDesinscrireShouldNotExists() {
		$this->assertNotXPath('//a[contains(@href, "abonne/desinscrire_session")]');
	}

	
	/** @test */
	public function messageVousNavezPasLesDroitsSuffisantsShouldBeVisible() {
		$this->assertXPathContentContains('//p', "Vous n'avez pas les droits");
	}
}


class AbonneControllerFormationsAmadouInscritSessionMarsJavaClosedTest extends AbstractAbonneControllerFormationsTestCase {
	/** @test */
	public function inscrireSessionShouldNotCallSave() {
		$this->dispatch('/opac/abonne/inscrire_session/id/32');
		$this->assertFalse(Class_SessionFormationInscription::getLoader()->methodHasBeenCalled('save'));
	}	
}


class AbonneControllerFormationsAmadouInscritSessionMarsJavaOpenTest extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this->_session_java_mars->setDateLimiteInscription('2022-03-05');
		$this->dispatch('/opac/abonne/inscrire_session/id/32');
	}	


	/** @test */
	public function sessionJavaMarsShouldBeValid() {
		$this->assertTrue($this->_session_java_mars->isValid(),
											implode(',', $this->_session_java_mars->getErrors()));
	}


	/** @test */
	function amadouShouldBeInsessionMarsJavaStagiaires() {
		$this->assertContains($this->_amadou, $this->_session_java_mars->getStagiaires());
	}


	/** @test */
	function answerShouldRedirectToFormationList() {
		$this->assertRedirectTo('/abonne/formations');
	}


	/** @test */
	function aNewInscriptionShouldHaveBeenCreated() {
		$inscription = Class_SessionFormationInscription::getLoader()->getFirstAttributeForLastCallOn('save');
		$this->assertEquals(32, $inscription->getSessionFormationId());
		$this->assertEquals(435, $inscription->getStagiaireId());
	}
}



abstract class AbonneControllerFormationsSessionJavaFevrierFullTestCase extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this
			->_session_java_fevrier
			->setEffectifMax(2)
			->setStagiaires(Class_UserGroup::getLoader()
											->newInstanceWithId(93)
											->addRightSuivreFormation()
											->setUsers(array(Class_Users::getLoader()->newInstanceWithId(94)->setLogin('titi'),
																			 Class_Users::getLoader()->newInstanceWithId(95)->setLogin('toto')))
											->getUsers());
	}
}



class AbonneControllerFormationsSessionJavaFevrierFullListTest extends AbonneControllerFormationsSessionJavaFevrierFullTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/formations');
	}


	/** @test */
	function session_fevrier_17_ShouldNotHaveLinkForInscrire() {
		$this->assertNotXPath('//a[contains(@href, "abonne/inscrire_session/id/31")]');
	}


	/** @test */
	public function sessionShouldDisplayFull() {
		$this->assertXPathContentContains('//tr[2]//span', 'Effectif maximum atteint');
	}
}




class AbonneControllerFormationsSessionJavaFevrierFullAndInscritListTest extends AbonneControllerFormationsSessionJavaFevrierFullTestCase {
	/** @test */
	public function pageShouldHaveLinkToDesinscrire() {
		$this->_session_java_fevrier->addStagiaire($this->_amadou);
		$this->_amadou->setSessionFormations(array($this->_session_java_fevrier));
		$this->dispatch('/opac/abonne/formations');
		$this->assertXPath('//a[contains(@href, "abonne/desinscrire_session/id/31")]', $this->_response->getBody());
	}
}



class AbonneControllerFormationsAmadouInscritSessionJavaFevrierFullTest extends AbonneControllerFormationsSessionJavaFevrierFullTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/inscrire_session/id/31');
	}

	/** @test */
	public function sessionShouldNotHaveBeenSaved() {
		$this->assertFalse(Class_SessionFormation::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	public function userShouldNotHaveBeenSaved() {
		$this->assertFalse(Class_Users::getLoader()->methodHasBeenCalled('save'));
	}
}




class AbonneControllerFormationsInscritSessionWithoutRightSuivreFormationTest extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this->_amadou->setUserGroups(array());
		$this->dispatch('/opac/abonne/inscrire_session/id/32');

	}

	/** @test */
	public function noInscriptionShouldBeSaved() {
		$this->assertFalse(Class_SessionFormationInscription::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	public function amadouShouldNotBeValid() {
		$this->assertFalse($this->_amadou->isValid());
	}
}




class AbonneControllerFormationsAmadouDesinscritSessionJuilletPythonTest extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/desinscrire_session/id/121');
	}	

	/** @test */
	function answerShouldRedirectToFormationList() {
		$this->assertRedirectTo('/abonne/formations');
	}


	/** @test */
	function amadouSessionsShouldNotContainsSessionJuilletPython() {
		$this->assertNotContains($this->_session_python_juillet, $this->_amadou->getSessionFormations());
	}


	/** @test */
	function inscriptionObjectShouldHaveBeenDeleted() {
		$inscription = Class_SessionFormationInscription::getLoader()->getFirstAttributeForLastCallOn('delete');
		$this->assertEquals(121, $inscription->getSessionFormationId());
		$this->assertEquals(435, $inscription->getStagiaireId());
	}
}




class AbonneControllerFormationsSessionFevrierJavaTest extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/detail_session/id/31');
	}	


	/** @test */
	public function pageShouldContainsLinkToInscrire() {
		$this->assertXPathContentContains('//a[contains(@href, "abonne/inscrire_session/id/31")]', 
																			'S\'inscrire');	
	}


	/** @test */
	public function ddShouldContainsNombreDeParticipants() {
		$this->assertXPathContentContains('//dl/dd', 'minimum: 2, maximum: 5, actuel: 0');
	}


	/** @test */
	function ddShouldContainsAdresseBonlieu() {
		$this->assertXPathContentContains('//dd', 'Bonlieu');
		$this->assertXPathContentContains('//dd', '1, rue Jean-Jaures');
		$this->assertXPathContentContains('//dd', '74007 Annecy');
	}


	/** @test */
	function ddShouldContainsGoogleMap() {
		$this->assertXPath('//dd//img[@src="http://maps.googleapis.com/maps/api/staticmap?sensor=false&zoom=15&size=300x300&center=1%2C+rue+Jean-Jaures%0ABP+294%2C74007%2CAnnecy%2CFRANCE&markers=1%2C+rue+Jean-Jaures%0ABP+294%2C74007%2CAnnecy%2CFRANCE"]',
											 $this->_response->getBody());
	}

}




class AbonneControllerFormationsSessionJuilletPythonDetailTest extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/detail_session/id/121');
	}	

	/** @test */
	public function actionShouldBeDetailSession() {
		$this->assertAction('detail_session');
	}


	/** @test */
	public function titleShouldBeFormationLearnPython_SessionDu21Juillet2023() {
		$this->assertXPathContentContains('//h1', 'Formation Learn Python: session du 21 juillet 2023');
	}


	/** @test */
	public function pageShouldContainsAButtontoGoBackToFormations() {
		$this->assertXPathContentContains('//a[contains(@href, "abonne/formations")]', 'Retour');
	}


	/** @test */
	public function pageShouldContainsLinkToDesinscrire() {
		$this->assertXPathContentContains('//a[contains(@href, "abonne/desinscrire_session/id/121")]', 
																			'Se désinscrire');	
	}


	/** @test */
	public function ddShouldContainsContenu() {
		$this->assertXPathContentContains('//dl[@class="session_formation"]//dd', 'Introduction a la syntaxe');
	}


	/** @test */
	public function ddShouldContainsNombreDeParticipants() {
		$this->assertXPathContentContains('//dl/dd', 'minimum: 5, maximum: 22, actuel: 1');
	}

	/** @test */
	public function ddShouldContainsDuree() {
		$this->assertXPathContentContains('//dl/dd', '8 h');
	}


	/** @test */
	public function ddShouldContainsLieu() {
		$this->assertXPathContentContains('//dl/dd', 'Bibliothèque des romains');
	}


	/** @test */
	public function ddShouldContainsHoraires() {
		$this->assertXPathContentContains('//dl/dd', '8h-12h, 14h-18h');
	}


	/** @test */
	public function ddIntervenantsShouldContainsJpp() {
		$this->assertXPathContentContains('//dd//li', 'Pirant, Jean-Paul');
	}


	/** @test */
	public function ddIntervenantsShouldContainsCc() {
		$this->assertXPathContentContains('//dd//li', 'Cerisier, Christophe');
	}
}




class AbonneControllerFormationsSessionJuilletPythonDetailRetourFicheTest extends AbstractAbonneControllerFormationsTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/detail_session/id/121/retour/fiche');
	}	

	/** @test */
	public function pageShouldContainsAButtontoGoBackToFicheAbonne() {
		$this->assertXPathContentContains('//a[contains(@href, "abonne/fiche")]', 'Retour');
	}

}




class AbonneControllerFormationsWrongIdsTest extends AbstractAbonneControllerFormationsTestCase {
	/** @test */
	public function onDetailSessionShouldRedirectToFormations() {
		$this->dispatch('/opac/abonne/detail_session/id/9999');
		$this->assertRedirectTo('/abonne/formations');
	}	


	/** @test */
	public function onInscrireSessionShouldRedirectToFormations() {
		$this->dispatch('/opac/abonne/inscrire_session/id/9999');
		$this->assertRedirectTo('/abonne/formations');
	}	


	/** @test */
	public function ondesinscrireSessionShouldRedirectToFormations() {
		$this->dispatch('/opac/abonne/desinscrire_session/id/9999');
		$this->assertRedirectTo('/abonne/formations');
	}	
	
}