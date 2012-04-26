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

abstract class Admin_FormationControllerTestCase extends Admin_AbstractControllerTestCase {
	protected $_learn_java;
	protected $_learn_python;
	protected $_benoit;
	protected $_patrick;
	protected $_amadou;
	protected $_amandine;
	protected $_prof_laurent;
	protected $_prof_stl;
	protected $_session_java_fevrier;
	protected $_session_java_mars;
	protected $_session_python_juillet;
	protected $_groupe_stagiaires;

	public function setUp() {
		parent::setUp();

		$this->_activateModuleFormation();
		$this->_setupUsersAndGroups();
		$this->_setupFormations();
	}


	protected function _setupFormations() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SessionFormationIntervention')
			->whenCalled('save')->answers(true)
			->whenCalled('delete')->answers(null);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SessionFormation')
			->whenCalled('save')->answers(true)
			->whenCalled('delete')->answers(null);

		$this->_benoit = Class_Users::getLoader()
			->newInstanceWithId(12)
			->setLogin('Benoit')
			->setNom('Curzillat')
			->setPrenom('Benoit')
			->setUserGroups( array($this->_groupe_stagiaires));

		$this->_patrick = Class_Users::getLoader()
			->newInstanceWithId(5)
			->setLogin('Pat')
			->setNom('Barroca')
			->setPrenom('Patrick')
			->setUserGroups(array($this->_groupe_stagiaires));


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Formation')
			->whenCalled('save')->answers(true)
			->whenCalled('delete')->answers(null)
			->whenCalled('findAll')->answers(array(
							 $this->_learn_java = Class_Formation::getLoader()
							 ->newInstanceWithId(3)
							 ->setLibelle('Learn Java')
							 ->setDescription('Here you will learn some old and boring stuff')
							 ->setSessions(array( 
																	 $this->_session_java_mars = Class_SessionFormation::getLoader()
																	 ->newInstanceWithId(32)
																	 ->setFormationId(3)
																	 ->setDateDebut('2012-03-27')
																	 ->setDateFin('2012-03-29')
																	 ->setEffectifMin(5)
																	 ->setEffectifMax(25)
																	 ->setDuree(8)
																	 ->setContenu('Intro à la syntaxe')
																	 ->setHoraires('9h - 12h, 13h - 18h')
																	 ->setLieu($salle_reunion = Class_Lieu::getLoader()
																						                     ->newInstanceWithId(12)
																						                      ->setLibelle('Salle reunion AFI'))
																	 ->setDateLimiteInscription('2012-03-05')
																	 ->setIntervenants(array($this->_prof_laurent)),

																	 $this->_session_java_fevrier = Class_SessionFormation::getLoader()
																	 ->newInstanceWithId(31)
																	 ->setFormationId(3)
																	 ->setDateDebut('2012-02-17')
																	 ->setDateFin('')
																	 ->setEffectifMax(10)
																	 ->setStagiaires(array($this->_benoit)))),
											

							 $this->_learn_python = Class_Formation::getLoader()
							 ->newInstanceWithId(12)
							 ->setLibelle('Learn Python')
							 ->setSessions(array( 
																	 $this->_session_python_juillet = Class_SessionFormation::getLoader()
																	 ->newInstanceWithId(121)
																	 ->setFormationId(12)
																	 ->setDateDebut('2009-07-21')
																	 ->setStagiaires(array())
																	 ->beAnnule())) 
							 ));


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Lieu')
			->whenCalled('save')
			->answers(true)

			->whenCalled('findAllBy')
			->answers(array($salle_reunion,

											Class_Lieu::getLoader()
											->newInstanceWithId(28)
											->setLibelle('au marché'),

											Class_Lieu::getLoader()
											->newInstanceWithId(18)
											->setLibelle('Au café du coin')));


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SessionFormationInscription')
			->whenCalled('save')->answers(true)
			->whenCalled('delete')->answers(null)
			->whenCalled('findAllBy')
			->with(array('role' => 'session_formation',
									 'model' => $this->_session_java_mars))
			->answers(array($patrick_inscription = Class_SessionFormationInscription::getLoader()
											->newInstanceWithId(76)
											->setStagiaire($this->_patrick)
											->setSessionFormation($this->_session_java_mars)
											->bePresent(),

											$benoit_inscription = Class_SessionFormationInscription::getLoader()
											->newInstanceWithId(77)
											->setStagiaire($this->_benoit)
											->setSessionFormation($this->_session_java_mars)
											->beAbsent()))

			->whenCalled('findAllBy')
			->with(array('role' => 'stagiaire',
									 'model' => $this->_patrick))
			->answers(array($patrick_inscription))


			->whenCalled('findAllBy')
			->with(array('role' => 'stagiaire',
									 'model' => $this->_benoit))
			->answers(array($benoit_inscription))
			;
	}


	protected function _activateModuleFormation() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('FORMATIONS')
			->setValeur('1');
	}


	protected function _setupUsersAndGroups() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_UserGroup')
			->whenCalled('findAll')
			->answers(array(
											$this->_groupe_stagiaires = Class_UserGroup::getLoader()
											->newInstanceWithId(12)
											->setLibelle('Stagiaires')
											->setRights(array(Class_UserGroup::RIGHT_SUIVRE_FORMATION))
											->setUsers(array(
																			 $this->_amadou = Class_Users::getLoader()
																			 ->newInstanceWithId(8)
																			 ->setNom('Diouf')
																			 ->setPrenom('Amadou')
																			 ->setLogin('adiouf')
																			 ->setSessionsFormation(array())
																			 ->setUserGroups(array(Class_UserGroup::getLoader()->find(12))),

																			 $this->_amandine = Class_Users::getLoader()
																			 ->newInstanceWithId(10)
																			 ->setNom('Pistache')
																			 ->setPrenom('Amandine')
																			 ->setLogin('amd')
																			 ->setSessionsFormation(array())
																			 ->setUserGroups(array(Class_UserGroup::getLoader()->find(12))))),


											Class_UserGroup::getLoader()
											->newInstanceWithId(34)
											->setLibelle('Profs')
											->setRights(array(Class_UserGroup::RIGHT_DIRIGER_FORMATION))
											->setUsers(array(
																			 $this->_prof_laurent = Class_Users::getLoader()
																			 ->newInstanceWithId(34)
																			 ->setNom('Laffont')
																			 ->setPrenom('Laurent')
																			 ->setLogin('llaffont'),

																			 $this->_prof_stl = Class_Users::getLoader()
																			 ->newInstanceWithId(35)
																			 ->setNom('Falcy')
																			 ->setPrenom('Estelle')
																			 ->setLogin('stl')))
											));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('save')->answers(true);
	}
}




class Admin_FormationControllerAddTest extends Admin_FormationControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/add');
	}


	/** @test */
	function benoitShouldBeInSessionJava17Fevrier() {
		$this->assertContains($this->_benoit, $this->_session_java_fevrier->getStagiaires());
	}


	/** @test */
	function titleShouldBeAjouterUneFormation() {
		$this->assertXPathContentContains('//h1', 'Ajouter une formation');
	}


	/** @test */
	function formShouldContainsInputForLibelle() {
		$this->assertXPath('//form[@id="formationForm"][contains(@action,"formation/add")]//input[@name="libelle"]',
											 $this->_response->getBody());
	}


	/** @test */
	function menuGaucheAdminShouldContainsLinkToFormation() {
		$this->assertXPathContentContains('//div[@class="menuGaucheAdmin"]//a[contains(@href,"admin/formation")]',
																			"Formations");
	}

	/** @test */
	function headShouldIncludeControlMajScript() {
		$this->assertXPath('//script[contains(@src, "controle_maj")]',
											 $this->_response->getBody());
	}
}



class Admin_FormationControllerPostNewFormationTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/formation/add',
												array('libelle' => 'Learning Smalltalk'));
	}


	/** @test */
	function newFormationLibelleShouldBeLearningSmalltalk() {
		$formation = Class_Formation::getLoader()->getFirstAttributeForLastCallOn('save');
		$this->assertEquals('Learning Smalltalk', $formation->getLibelle());
	}


	/** @test */
	function answerShouldRedirectToAddSessionToFormation() {
		$this->assertRedirectTo('/admin/formation/session_add/formation_id/');
	}
}



class Admin_FormationControllerPostErrorsTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/formation/add', array('libelle' => ''));		
	}


	/** @test */
	function saveShouldNotBeCalledIfLibelleEmpty() {
		$this->assertFalse(Class_Formation::getLoader()->methodHasBeenCalled('save'));		
	}


	/** @test */
	function responseShouldNotRedirectToFormationIndex() {
		$this->assertNotRedirectTo('/admin/formation');
	}

	/** @test */
	function errorShouldDisplayUneValeurEstRequise() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', "Une valeur est requise");
	}
}




class Admin_FormationControllerEditLearningJavaTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/edit/id/3');
	}


	/** @test */
	function titleShouldBeModierLaFormationLearnJava() {
		$this->assertXPathContentContains('//h1', 'Modifier la formation: Learn Java');
	}


	/** @test */
	public function formShouldContainsTextAreaForDescription() {
		$this->assertXPathContentContains('//textarea[@name="description"]', 'Here you will learn some old and boring stuff');
	}


	/** @test */
	function formShouldContainsInputForLibelle() {
		$this->assertXPath('//form[contains(@action,"formation/edit/id/3")]//input[@name="libelle"][@value="Learn Java"]');
	}
}




class Admin_FormationControllerPostLearningJavaTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/formation/edit/id/3',
												array('libelle' => 'Learning J2EE',
															'description' => 'Now with fun'));
	}


	/** @test */
	function libelleShouldBeLearningJ2EE() {
		$this->assertEquals('Learning J2EE', $this->_learn_java->getLibelle());
	}


	/** @test */
	public function descriptionShouldBeNowWithFun() {
		$this->assertEquals('Now with fun', $this->_learn_java->getDescription());
	}


	/** @test */
	function saveShouldHaveBeenCalled() {
		$this->assertTrue(Class_Formation::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	function answerShouldRedirectToFormationIndex() {
		$this->assertRedirectTo('/admin/formation');
	}
}




class Admin_FormationControllerIndexTest extends Admin_FormationControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->_session_python_juillet
			->setEffectifMin(20)
			->setEffectifMax(2);
		$this->dispatch('/admin/formation');
	}


	/** @test */
	function aListItemShouldContainsLearnJava() {
		$this->assertXPathContentContains('//ul//li', 'Learn Java');
	}


	/** @test */
	function aListItemShouldContainsLearnPythonAnnule() {
		$this->assertXPathContentContains('//ul//li', 'Learn Python');
	}


	/** @test */
	function firstH2ShouldContains2009() {
		$this->assertXPathContentContains('//div[2]//h2', '2009', $this->_response->getBody());
	}


	/** @test */
	function secondH2ShouldContains2012() {
		$this->assertXPathContentContains('//div[3]//h2', '2012');
	}


	/** @test */
	function titleShouldBeMiseAJourDesFormations() {
		$this->assertXPathContentContains('//h1', 'Mise à jour des formations');
	}


	/** @test */
	function pageShouldContainsButtonToCreateFormation() {
		$this->assertXPathContentContains('//div[contains(@onclick, "formation/add")]//td', 'Ajouter une formation');
	}


	/** @test */
	function session_fevrier_17_ShouldBeDisplayedUnderLearnJavaInSecondPosition() {
		$this->assertXPathContentContains('//ul//li//ul//li[2]', 'février, 17', $this->_response->getBody());
	}


	/** @test */
	function session_fevier17_ShouldDisplayOneParticipants() {
		$this->assertXPathContentContains('//ul//li//ul//li[2]//div', '1 / 1-10 participants');
	}


	/** @test */
	function participants_session_fevrier17_ShouldLinkToActionInscriptions() {
		$this->assertXPathContentContains('//li[2]//div//a[contains(@href, "formation/inscriptions/session_id/31")]', 
																			'1 / 1-10 participants');
	}


	/** @test */
	function sessionFevrier17ShouldHaveALinkToExportInscriptionsCSV() {
		$this->assertXPath('//li[2]//div//a[contains(@href, "formation/export_inscriptions/id/31")]');
	}


	/** @test */
	function sessionFevrier17ShouldHaveALinkToPrint() {
		$this->assertXPath('//li[2]//div//a[contains(@href, "formation/session_impressions/id/31")]');
	}


	/** @test */
	function sessionFevrier17ShouldHaveALinkToActionPresences() {
		$this->assertXPath('//li[2]//div//a[contains(@href, "formation/presences/id/31")]');
	}


	/** @test */
	function session_mars_27_ShouldBeDisplayedUnderLearnJavaInFirstPosition() {
		$this->assertXPathContentContains('//ul//li//ul//li[1]', 'mars, 27', $this->_response->getBody());
	}


	/** @test */
	function session_mars_27_LearnJavaShouldDisplayTwoParticipants() {
		$this->assertXPathContentContains('//ul//li//ul//li[1]//div', '2 / 5-25 participants');
	}


	/** @test */
	function session_mars_27ShouldHaveLinkToEdit() {
		$this->assertXPath('//ul//li//ul//li[1]//a[contains(@href, "formation/session_edit/id/32")]');
	}


	/** @test */
	function session_mars_27ShouldHaveLinkToDelete() {
		$this->assertXPath('//ul//li//ul//li[1]//a[contains(@href, "formation/session_delete/id/32")]');
	}


	/** @test */
	function formationLearnJavaShouldHaveLinkToAddSession() {
		$this->assertXPath('//ul//li//a[contains(@href, "formation/session_add/formation_id/3")]');
	}


	/** @test */
	function formationLearnJavaShouldHaveLinkToEditItself() {
		$this->assertXPath('//ul//li//a[contains(@href, "formation/edit/id/3")]');
	}
				

	/** @test */
	function formationLearnJavaShouldHaveLinkToDeleteItself() {
		$this->assertXPath('//ul//li//a[contains(@href, "formation/delete/id/3")]');
	}

	/** @test */
	public function sessionPythonInvalidShouldDisplayErrorEffectif() {
		$this->assertXPathContentContains('//li[1]//div[@class="error"]', 'effectif maximum doit être supérieur');		
	}


	/** @test */
	function sessionPythonShouldHaveClassAnnule() {
		$this->assertXPathContentContains('//ul//li//ul//li[contains(@class, " annule")]', 'juillet, 21');
	}

}




class Admin_FormationControllerEditSessionLearningJavaFevrierTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/session_edit/id/31');
	}


	/** @test */
	function inputDateFinShouldBeEmpty() {
		$this->assertXPath('//form[@id="sessionForm"]//input[@name="date_fin"][@value=""]');
	}

}




class Admin_FormationControllerEditSessionLearningJavaMars27Test extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/session_edit/id/32');
	}


	/** @test */
	function titreShouldBeModifierLaSessionDu27Mars2012DeLaFormationLearnJava() {
		$this->assertXPathContentContains('//h1', 'Modifier la session du 27 mars 2012 de la formation "Learn Java"');
	}


	/** @test */
	function inputDateDebutShouldContains27_03_2012() {
		$this->assertXPath('//form[@id="sessionForm"][contains(@action,"formation/sessionedit")]//input[@name="date_debut"][@value="27/03/2012"]');
	}


	/** @test */
	function inputDateFinShouldContains28_03_2012() {
		$this->assertXPath('//form[@id="sessionForm"][contains(@action,"formation/sessionedit")]//input[@name="date_fin"][@value="29/03/2012"]');
	}


	/** @test */
	function inputDateLimiteInscriptionShouldContains05_03_2012() {
		$this->assertXPath('//form[@id="sessionForm"][contains(@action,"formation/sessionedit")]//input[@name="date_limite_inscription"][@value="05/03/2012"]');
	}

	
	/** @test */
	function inputEffectifMinValueShouldBe5() {
		$this->assertXPath('//input[@name="effectif_min"][@value="5"]');
	}


	/** @test */
	function inputEffectifMaxValueShouldBe25() {
		$this->assertXPath('//input[@name="effectif_max"][@value="25"]');
	}


	/** @test */
	public function contenuShouldBeTextAreaWithIntroSyntaxe() {
		$this->assertXPathContentContains('//td//textarea[@name="contenu"]', 'Intro à la syntaxe');
	}


	/** @test */
	public function checkboxIntervenantLaurentShouldBeChecked() {
		$this->assertXPath('//input[@name="intervenant_ids[]"][@value="34"][@checked="checked"]');	
	}


	/** @test */
	public function inputHorairesShouldContains9h18h() {
		$this->assertXPath('//input[@name="horaires"][@value="9h - 12h, 13h - 18h"]');
	}


	/** @test */
	public function inputLieuShouldContainsSalleReunionAFI() {
		$this->assertXPathContentContains('//select[@name="lieu_id"]//option[@value="12"]', "Salle reunion AFI");
	}
}




class Admin_FormationControllerDeleteSessionLearningJavaMars27Test extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/session_delete/id/32');
	}


	/** @test */
	function deleteShouldHaveBeenCalled() {
		$session = Class_SessionFormation::getLoader()->getFirstAttributeForLastCallOn('delete');
		$this->assertEquals(32, $session->getId());
	}


	/** @test */
	function responseShouldRedirectToFormationIndex() {
		$this->assertRedirectTo('/admin/formation');
	}
}




class Admin_FormationControllerDeleteFormationLearningJavaTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/delete/id/3');
	}


	/** @test */
	function deleteFormationShouldHaveBeenCalled() {
		$formation = Class_Formation::getLoader()->getFirstAttributeForLastCallOn('delete');
		$this->assertEquals(3, $formation->getId());
	}


	/** @test */
	function deleteSession17FebruaryShouldHaveBeenCalled() {
		$session = Class_SessionFormation::getLoader()->getFirstAttributeForLastCallOn('delete');
		$this->assertEquals(31, $session->getId());
	}


	/** @test */
	function responseShouldRedirectToFormationIndex() {
		$this->assertRedirectTo('/admin/formation');
	}
}




class Admin_FormationControllerPostSessionLearnJavaTest extends  Admin_FormationControllerTestCase  {
	protected $_session;

	public function setUp() {
		parent::setUp();

		$this->postDispatch('/admin/formation/session_edit/id/32',
												array('date_debut' => '29/05/2012',
															'date_limite_inscription' => '03/05/2012',
															'effectif_min' => '1',
															'effectif_max' => '8',
															'contenu' => 'Accompagné d un bon café',
															'horaires' => '9h - 18h',
															'lieu_id' => 18,
															'is_annule' => '1'));
		$this->_session = Class_SessionFormation::getLoader()->find(32);
	}


	/** @test */
	public function sessionShouldBeValid() {
		$this->assertTrue($this->_session->isValid(),
											implode(',', $this->_session->getErrors()));
	}


	/** @test */
	function saveShouldHaveBeenCalled() {
		$this->assertTrue(Class_SessionFormation::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	function dateDebutShouldBe2012_05_29() {
		$this->assertEquals('2012-05-29', $this->_session->getDateDebut());
	}


	/** @test */
	function dateLimiteInscriptionShouldBe2012_05_03() {
		$this->assertEquals('2012-05-03', $this->_session->getDateLimiteInscription());
	}


	/** @test */
	function responseShouldRedirectToSessionFormationEdit() {
		$this->assertRedirectTo('/admin/formation/session_edit/id/32');
	}

	/** @test */
	function effectifMaxShouldBeHeight() {
		$this->assertEquals(8, $this->_session->getEffectifMax());
	}


	/** @test */
	public function contenuShouldBeAccompagneDunBonCafe() {
		$this->assertEquals('Accompagné d un bon café', $this->_session->getContenu());
	}


	/** @test */
	public function horairesShouldBe9h18h() {
		$this->assertEquals('9h - 18h', $this->_session->getHoraires());
	}


	/** @test */
	public function lieuShouldEqualsAuCafeDuCoin() {
		$this->assertEquals('Au café du coin', $this->_session->getLieu()->getLibelle());
	}


	/** @test */
	public function sessionShouldBeAnnule() {
		$this->assertTrue($this->_session->isAnnule());
	}
}




class Admin_FormationControllerPostSessionLearnJavaWithInvalidDataTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/formation/session_edit/id/32',
												array('date_debut' => '',
															'effectif_min' => 20,
															'effectif_max' => 4,
															'date_limite_inscription' => '05/01/2099',
															'date_fin' => '05/01/1990'));
	}

	/** @test */
	function responseShouldNotRedirectToFormationIndex() {
		$this->assertNotRedirectTo('/admin/formation');
	}


	/** @test */
	function errorShouldDisplayUneValeurEstRequise() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', "Une valeur est requise");
	}


	/** @test */
	function errorsShouldContainsEffectifMaxLowerThanEffectifMin() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', "L'effectif maximum doit être supérieur ou égal à l'effectif minimum");
	}


	/** @test */
	public function errorsShouldContainsDateLimiteInscriptionAfterDateDebut() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', 
																			"La date limite d'inscription doit être inférieure ou égale à la date de début");
	}


	/** @test */
	public function errorsShouldContainsDateFinBeforeDateDebut() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', 
																			"La date de fin doit être supérieure ou égale à la date de début");
	}
}




class Admin_FormationControllerAddSessionToFormationLearningPythonTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/session_add/formation_id/12');
	}


	/** @test */
	function titreShouldBeNouvelleSessionDeLaFormationLearnPython() {
		$this->assertXPathContentContains('//h1', 'Nouvelle session de la formation "Learn Python"');
	}


	/** @test */
	function inputDateDebutShouldBeEmpty() {
		$this->assertXPath('//form[@id="sessionForm"][contains(@action,"formation/sessionadd")]//input[@name="date_debut"][@value=""]');
	}


	/** @test */
	public function formShouldContainsCheckBoxProfLaurent() {
		$this->assertXPath('//input[@name="intervenant_ids[]"][@value="34"]');	
	}


	/** @test */
	public function formShouldContainsCheckBoxProfStl() {
		$this->assertXPath('//input[@name="intervenant_ids[]"][@value="35"]');	
	}


	/** @test */
	public function formShouldNotContainsCheckBoxForAmadou() {
		$this->assertNotXPath('//input[@name="intervenant_ids[]"][@value="8"]');	
	}


	/** @test */
	public function formShouldContainsCheckboxAnnule() {
		$this->assertXPath('//input[@name="is_annule"][@value="0"][not(@checked)]');	
	}

}




class Admin_FormationControllerPostAddSessionToFormationLearningPythonTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();


		Class_SessionFormation::getLoader()
			->whenCalled('save')
			->willDo(function($session) {
					$session->setId(99); 
					return true;
				});

		$this->postDispatch('/admin/formation/session_add/formation_id/12',
												array('date_debut' => '17/02/2010',
															'date_fin' => '17/02/2010',
															'effectif_min' => '3',
															'effectif_max' => '12',
															'contenu' => 'On charme les serpents',
															'intervenant_ids' => array(34, 35),
															'horaires' => '9h - 18h',
															'lieu_id' => 28,
															'is_annule' => '0'));
		$this->session = Class_SessionFormation::getLoader()->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	function newSessionDateDebutShouldBe2010_02_17() {
		$this->assertEquals('2010-02-17', $this->session->getDateDebut());
	}


	/** @test */
	function newSessionDateFinShouldBe2010_02_17() {
		$this->assertEquals('2010-02-17', $this->session->getDateFin());
	}


	/** @test */
	function answerShouldRedirectToFormationIndex() {
		$this->assertRedirectTo('/admin/formation/session_edit/id/99');
	}


	/** @test */
	function getFormationShouldReturnFormationLearnPython() {
		$this->assertEquals($this->_learn_python, $this->session->getFormation());
	}


	/** @test */
	public function sessionIntervenantsShouldContainstLaurentAndSTL() {
		$this->assertEquals(array($this->_prof_laurent, $this->_prof_stl), 
												$this->session->getIntervenants());
	}


	/** @test */
	public function sessionShouldNotHaveAttributeIntervenantIds() {
		$this->assertNull($this->session->INTERVENANT_IDS);
	}


	/** @test */
	public function sessionShouldNotBeAnnule() {
		$this->assertFalse($this->session->isAnnule());
	}
}




class Admin_FormationControllerPostAddSessionToFormationLearningPythonWithWrongIntervenantsTest extends  Admin_FormationControllerTestCase  {
	/** @test */
	public function sessionSaveShoulNotHaveBeenCalled() {
		$this->postDispatch('/admin/formation/session_add/formation_id/12',
												array('date_debut' => '17/02/2010',
															'effectif_min' => '3',
															'effectif_max' => '12',
															'contenu' => 'On charme les serpents',
															'intervenant_ids' => array(10)));
		$this->assertFalse(Class_SessionFormation::getLoader()->methodHasBeenCalled('save'));
	}
}




class Admin_FormationControllerActionsWithInvalidIdsTest extends  Admin_FormationControllerTestCase  {
	/** @test */
	function editFormationShouldRedirectToIndex() {
		$this->dispatch('/admin/formation/edit/id/99999999');
		$this->assertRedirectTo('/admin/formation');
	}


	/** @test */
	function deleteFormationShouldRedirectToIndex() {
		$this->dispatch('/admin/formation/delete/id/99999999');
		$this->assertRedirectTo('/admin/formation');
	}


	/** @test */
	function editSessionShouldRedirectToIndex() {
		$this->dispatch('/admin/formation/session_edit/id/99999999');
		$this->assertRedirectTo('/admin/formation');
	}


	/** @test */
	function addSessionShouldRedirectToIndex() {
		$this->dispatch('/admin/formation/session_add/formation_id/99999999');
		$this->assertRedirectTo('/admin/formation');
	}


	/** @test */
	function deleteSessionShouldRedirectToIndex() {
		$this->dispatch('/admin/formation/session_delete/id/99999999');
		$this->assertRedirectTo('/admin/formation');
	}
}




class Admin_FormationControllerInscriptionsSessionMarsJavaTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/inscriptions/session_id/32');
	}


	/** @test */
	function titreShouldBeListeDesParticipants() {
		$this->assertXPathContentContains("//h1", 'Liste des participants à la session du 27 mars 2012 de la formation "Learn Java"');
	}


	/** @test */
	function firstRowTDShouldContainsPat() {
		$this->assertXPathContentContains('//table//tr[1]//td', 'Pat');
	}


	/** @test */
	function firstRowTDShouldContainsBarroca() {
		$this->assertXPathContentContains('//table//tr[1]//td', 'Barroca');
	}


	/** @test */
	function firstRowTDShouldContainsPatrick() {
		$this->assertXPathContentContains('//table//tr[1]//td', 'Patrick');
	}


	/** @test */
	function firstRowTDShouldContainsLinkToUnsubscribePatrick() {
		$this->assertXPath('//table//tr[1]//td//a[@href="/admin/formation/inscriptions/session_id/32/delete/5"]');
	}


	/** @test */
	function secondRowTDShouldContainsBenoit() {
		$this->assertXPathContentContains('//table//tr[2]//td', 'Benoit');
	}


	/** @test */
	public function pageShouldContainsFormToFindUsers() {
		$this->assertXPath('//form[@id="findusers"][@action="/admin/formation/inscriptions/session_id/32"][@method="get"]');
	}


	/** @test */
	public function formShouldContainsInputForSearch() {
		$this->assertXPath('//form//input[@name="search"]');
	}


	/** @test */
	public function formShouldContainsSubmitButton() {
		$this->assertXPath('//form//input[@type="submit"]');
	}


	/** @test */
	function panelFormationLearnJavaShouldBeVisible() {
		$this->assertXPathContentContains('//ul//li', 'Learn Java', $this->_response->getBody());
	}


	/** @test */
	public function libelleSearchFormShouldBeRechercherDesStagiaires() {
		$this->assertXPathContentContains('//label[@for="search"]', 'Rechercher des stagiaires');
	}


	/** @test */
	public function inputSubmitSearchFormShouldBeInscrireLesStagiairesSelectionnes() {
		$this->assertXPath('//form[@id="user_subscribe"]//input[@value="Inscrire les stagiaires sélectionnés"]');
	}
}




class Admin_FormationControllerExportInscriptionsSessionMarsJavaTest extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/export_inscriptions/id/32');
	}


	/** @test */
	public function responseHeaderShouldHaveContentTypeCSV() {
		$this->assertHeaderContains('Content-Type', 'text/csv;charset=utf-8');
	}


	/** @test */
	public function responseHeaderShouldHaveContentDispositionAttachment() {
		$this->assertHeaderContains('Content-Disposition', 
																'attachment; filename="session_32.csv"');
	}


	/** @test */
	public function contentShouldContainsPatAndBenoit() {
		$this->assertContains("Nom;Prénom;Identifiant\n".
													"Barroca;Patrick;Pat\n".
													"Curzillat;Benoit;Benoit\n", 
													$this->_response->getBody());
	}


	/** @test */
	public function contentShouldContainsDetailSession() {
		$this->assertContains("Formation;Learn Java;\n".
													"Session;27 mars 2012;\n".
													"Durée;8h;\n".
													"Effectif;5-25;\n",
													$this->_response->getBody());
	}
}




class Admin_FormationControllerPresencesSessionJavaMars27Test extends  Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/presences/id/32');
	}


	/** @test */
	public function titreShouldBePersonnesPresentes() {
		$this->assertXPathContentContains('//h1', 'Personnes présentes à la session du 27 mars 2012 de la formation "Learn Java"');
	}


	/** @test */
	function panelFormationLearnJavaShouldBeVisible() {
		$this->assertXPathContentContains('//ul//li', 'Learn Java');
	}


	/** @test */
	public function formShouldHaveCheckboxForBenoit() {
		$this->assertXPath('//input[@name="user_ids[]"][@value="12"][not(@checked)]');
	}


	/** @test */
	public function formShouldHaveCheckedCheckboxForPatrick() {
		$this->assertXPath('//input[@name="user_ids[]"][@value="5"][@checked="checked"]');	
	}
}




class Admin_FormationControllerPostPresencesSessionJavaMars27Test extends  Admin_FormationControllerTestCase  {
	protected $_inscription_patrick, $_inscription_benoit;

	public function setUp() {
		parent::setUp();

		$this->_inscription_patrick = Class_SessionFormationInscription::getLoader()->find(76);
		$this->_inscription_benoit = Class_SessionFormationInscription::getLoader()->find(77);

		$this->postDispatch('/admin/formation/presences/id/32',
												array('user_ids' => array(12)));
	}


	/** @test */
	public function inscriptionBenoitShouldBePresent() {
		$this->assertTrue($this->_inscription_benoit->isPresent());
	}


	/** @test */
	public function inscriptionPatrickShouldNoteBePresent() {
		$this->assertFalse($this->_inscription_patrick->isPresent());
	}


	/** @test */
	public function inscriptionPatrickShouldHaveBeenSaved() {
		$this->assertTrue(Class_SessionFormationInscription::getLoader()->methodHasBeenCalledWithParams('save', array($this->_inscription_patrick)));
	}


	/** @test */
	public function responseShouldRedirectToPresencesId32() {
		$this->assertRedirectTo('/admin/formation/presences/id/32');
	}
}




class Admin_FormationControllerSessionMarsJavaDeletePatrickTest extends Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch("/admin/formation/inscriptions/session_id/32/delete/5?dummmy=Zork");
	}

	/** @test */
	public function patrickShouldNotAppearInStagiaires() {
		$this->assertNotContains($this->_patrick, $this->_session_java_mars->getStagiaires());
	}

	/** @test */
	public function sessionShouldHaveBeenSaved() {
		$this->assertEquals($this->_session_java_mars,
												Class_SessionFormation::getLoader()->getFirstAttributeForLastCallOn('save'));		
	}

	/** @test */
	public function responseShouldRedirectToReferreWithoutDeleteParam() {
		$this->assertRedirectTo("/admin/formation/inscriptions/session_id/32?dummmy=Zork");
	}
}




class Admin_FormationControllerSessionMarsJavaWithOtherUsersNotStagiairesDeletePatrickTest extends Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->_benoit->setUserGroups(array());
		$this->_patrick->setUserGroups(array());

		$this->dispatch("/admin/formation/inscriptions/session_id/32/delete/5?dummmy=Zork");
	}


	/** @test */
	public function sessionShouldHaveBeenSaved() {
		$this->assertEquals($this->_session_java_mars,
												Class_SessionFormation::getLoader()->getFirstAttributeForLastCallOn('save'));		
	}
}




class Admin_FormationControllerInscriptionsSessionMarsJavaRechercheAmaTest extends Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();

		Class_Users::getLoader()
			->whenCalled('findAll')
			->with("select bib_admin_users.* ".
						 "from bib_admin_users ".
						 "inner join user_group_memberships on user_group_memberships.user_id = bib_admin_users.id_user ".
						 "inner join user_groups on user_group_memberships.user_group_id = user_groups.id  ".
						 "where (user_groups.rights_token & 1 = 1) and ".
						 "(nom like '%ama%' or prenom like '%ama%' or login like '%ama%') ".
						 "order by nom, prenom, login limit 500")
			->answers(array($this->_amadou, $this->_amandine, $this->_patrick));

		$this->dispatch('/admin/formation/inscriptions/session_id/32?search=Ama');
	}


	/** @test */
	public function formUserSearchShouldContainsAma() {
		$this->assertXPath('//form//input[@name="search"][@value="Ama"]');
	}

	/** @test */
	public function userSubcribeFormShouldContainsCheckBoxWithAmadou() {
		$this->assertXPath('//form[@id="user_subscribe"]//input[@name="users[]"][@value="8"]',
											 Class_Users::getLoader()->getFirstAttributeForLastCallOn('findAll'));	
		$this->assertXPathContentContains('//form[@id="user_subscribe"]//label', 'Diouf Amadou - adiouf');	
	}


	/** @test */
	public function userSubcribeFormShouldContainsCheckBoxWithAmandine() {
		$this->assertXPathContentContains('//form[@id="user_subscribe"]//label', 'Amandine');	
	}

	/** @test */
	public function formShouldIncludeSubmitButtonForSubscription() {
		$this->assertXPath('//form[@id="user_subscribe"]//input[@type="submit"]');
	}

	/** @test */
	function firstRowTDShouldContainsLinkToUnsubscribePatrickThatKeepsSearchParams() {
		$this->assertXPath('//tr[1]//a[@href="/admin/formation/inscriptions/session_id/32/delete/5?search=Ama"]');
	}
}




class Admin_FormationControllerInscriptionsSessionPythonInscritAmadouAndAmandineTest extends Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/formation/inscriptions/session_id/121',
												array('users' => array(8, 10)));
	}


	/** @test */
	public function amandineShouldHasRightSuivreFormation() {
		$this->assertTrue($this->_amandine->hasRightSuivreFormation());
	}


	/** @test */
	public function amadouShouldHasRightSuivreFormation() {
		$this->assertTrue($this->_amadou->hasRightSuivreFormation());
	}


	/** @test */
	public function sessionStagiairesShouldIncludeAmandineAndAmadou() {
		$this->assertEquals(array($this->_amadou, $this->_amandine),
												$this->_session_python_juillet->getStagiaires());
	}	


	/** @test */
	public function sessionPythonShouldBeValid() {
		$this->assertTrue($this->_session_python_juillet->isValid(),
											implode(',', $this->_session_python_juillet->getErrors()));
	}


	/** @test */
	public function sessionShouldHaveBeenSaved() {
		$this->assertEquals($this->_session_python_juillet,
												Class_SessionFormation::getLoader()->getFirstAttributeForLastCallOn('save'));
	}

	/** @test */
	public function subscribeTwiceShouldNotDublicateSubscriptions() {
		$this->postDispatch('/admin/formation/inscriptions/session_id/121',
												array('users' => array(8, 10)));
		$this->assertEquals(array($this->_amadou, $this->_amandine),
												$this->_session_python_juillet->getStagiaires());
	}
}




abstract class FormationControllerImpressionsTestCase extends Admin_FormationControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->_session_java_fevrier->setStagiaires(array($this->_benoit, $this->_patrick));
		$modele_lettre_emargement = Class_ModeleFusion::getLoader()
			->newInstanceWithId(2)
			->setNom('FORMATION_EMARGEMENT')
			->setContenu('<div>
										<h1>Lettre emargement</h1>
										<h2>{session_formation.formation.libelle}</h2>

										<p>
										{session_formation.date_debut}
										</p>

										<p>
										{session_formation.stagiaires["Nom":nom, "Prénom":prenom, "Signature"]}
										</p>
										</div>');

		$modele_lettre_convocation = Class_ModeleFusion::getLoader()
			->newInstanceWithId(5)
			->setNom('FORMATION_CONVOCATION')
			->setContenu('<div>
										<h1>Convocation pour {stagiaire.nom}, {stagiaire.prenom}</h1>
										<p>Le stage {session_formation.formation.libelle} débutera le {session_formation.date_debut}</p>
										</div>');


		$modele_lettre_stagiaires = Class_ModeleFusion::getLoader()
			->newInstanceWithId(19)
			->setNom('FORMATION_LISTE_STAGIAIRES')
			->setContenu('<h1>Liste des stagiaires pour la session du {session_formation.date_debut_texte}</h1>
			              <h2>{session_formation.formation.libelle}</h2>
				            {session_formation.stagiaires["Nom":nom, "Prénom":prenom, "Bibliothèque":bib.libelle, "Téléphone":telephone]}');


		$modele_lettre_attestation = Class_ModeleFusion::getLoader()
			->newInstanceWithId(25)
			->setNom('FORMATION_ATTESTATION')
			->setContenu('<h1>Je soussign&eacute; {stagiaire.nom}, {stagiaire.prenom} avoir particip&eacute; à la session du {session_formation.date_debut_texte}</h1>
			              <h2>{session_formation.formation.libelle}</h2>');

		$modele_lettre_refus = Class_ModeleFusion::getLoader()
			->newInstanceWithId(32)
			->setNom('FORMATION_REFUS')
			->setContenu('<p>A l\'attention de {stagiaire.nom}, {stagiaire.prenom}</p>
			              <p>Votre inscription au stage {session_formation.formation.libelle} n\'a pu &ecirc;tre retenue</p>');


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_ModeleFusion')
			->whenCalled('save')->answers(true)

			->whenCalled('findFirstBy')
			->with(array('nom' => 'FORMATION_EMARGEMENT'))
			->answers($modele_lettre_emargement)


			->whenCalled('findFirstBy')
			->with(array('nom' => 'FORMATION_LISTE_STAGIAIRES'))
			->answers($modele_lettre_stagiaires)


			->whenCalled('findFirstBy')
			->with(array('nom' => 'FORMATION_CONVOCATION'))
			->answers($modele_lettre_convocation)


			->whenCalled('findFirstBy')
			->with(array('nom' => 'FORMATION_ATTESTATION'))
			->answers($modele_lettre_attestation)

			->whenCalled('findFirstBy')
			->with(array('nom' => 'FORMATION_REFUS'))
			->answers($modele_lettre_refus);
	}
}




class FormationControllerSessionJavaFevrierImpressionsTest extends FormationControllerImpressionsTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/session_impressions/id/31');
	}


	/** @test */
	public function titleShouldBeImpressionsPourLaSessionFevrierJava() {
		$this->assertXPathContentContains('//h1', 'Impressions pour la session du 17 février 2012 de la formation "Learn Java"');
	}


	/** @test */
	function panelFormationLearnJavaShouldBeVisible() {
		$this->assertXPathContentContains('//ul//li', 'Learn Java');
	}


	/** @test */
	public function pageShouldContainsCKEditorForFicheEmargement() {
		$this->assertXPathContentContains('//form[@id="FORMATION_EMARGEMENT_FORM"]//textarea[@name="FORMATION_EMARGEMENT"]', 
																			'Lettre emargement',
																			$this->_response->getBody());
	}


	/** @test */
	public function pageShouldContainsCKEditorForFicheConvocation() {
		$this->assertXPathContentContains('//form[@id="FORMATION_CONVOCATION_FORM"]//textarea[@name="FORMATION_CONVOCATION"]', 
																			'Convocation pour',
																			$this->_response->getBody());
	}


	/** @test */
	public function formActionShouldBeModeleImpression() {
		$this->assertXPath('//form[contains(@action, "admin/formation/modele_impression/id/2/session_id/31")]');
	}


	/** @test */
	public function pageShouldContainsALinkToPrintFicheEmargement() {
		$this->assertXPath('//a[contains(@href, "formation/fiche_emargement/id/31")]');
	}


	/** @test */
	public function pageShouldContainsALinkToPrintConvocations() {
		$this->assertXPath('//a[contains(@href, "formation/convocations/id/31")]');
	}


	/** @test */
	public function pageShouldContainsALinkToPrintListeStagiaires() {
		$this->assertXPath('//a[contains(@href, "formation/liste_stagiaires/id/31")]');
	}


	/** @test */
	public function pageShouldContainsALinkToPrintAttestations() {
		$this->assertXPath('//a[contains(@href, "formation/attestations/id/31")]');
	}


	/** @test */
	public function pageShouldContainsALinkToPrintRefus() {
		$this->assertXPath('//a[contains(@href, "formation/refus/id/31")]');
	}
}




class FormationControllerSessionJavaFevrierPostImpressionsTest extends FormationControllerImpressionsTestCase  {
	public function setUp() {
		parent::setUp();

		$this->postDispatch('/admin/formation/modele_impression/id/2/session_id/31',
												array('FORMATION_EMARGEMENT' => 'A new template'));
	}


	/** @test */
	public function modeleContenuShouldBeANewTemplate() {
		$this->assertEquals('A new template', Class_ModeleFusion::getLoader()->find(2)->getContenu());
	}

	
	/** @test */
	public function answerShouldRedirectToSessionImpressionID31() {
		$this->assertRedirectTo("/admin/formation/session_impressions/id/31");
	}


	/** @test */
	public function modeleShouldHaveBeenSaved() {
		$this->assertTrue(Class_ModeleFusion::getLoader()->methodHasBeenCalled('save'));
	}
}



class FormationControllerFicheEmargementSessionJavaFevrierTest extends FormationControllerImpressionsTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/fiche_emargement/id/31');
	}


	/** @test */
	public function actionShouldBeFicheEmargement() {
		$this->assertAction('fiche_emargement');
	}


	/** @test */
	public function pageShouldContainsH1WithLettreEmargement() {
		$this->assertXPathContentContains('//div[@class="lettre_fusion"]//h1', 
																			'Lettre emargement',
																			$this->_response->getBody());
	}


	/** @test */
	public function pageShouldContainsH2WithLearnJava() {
		$this->assertXPathContentContains('//div[@class="lettre_fusion"]//h2', 'Learn Java');
	}

	
	/** @test */
	public function pageShouldContainsTableWithNomPrenom() {
		$this->assertXPathContentContains('//table//td', 'Curzillat');
		$this->assertXPathContentContains('//table//td', 'Benoit');
	}


	/** @test */
	public function pageShouldNotHaveLayout() {
		$this->assertNotXPath('//div[@id="banniere"]');
	}
}




class FormationControllerConvocationTestCase extends FormationControllerImpressionsTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/convocations/id/31');	
	}


	/** @test */
	public function actionShouldBeConvocations() {
		$this->assertAction('convocations');
	}


	/** @test */
	public function pageShouldContainssConvocationPourBenoit() {
		$this->assertXPathContentContains('//h1', 'Convocation pour Curzillat, Benoit');
	}


	/** @test */
	public function pageShouldContainssConvocationPourPatrick() {
		$this->assertXPathContentContains('//h1', 'Convocation pour Barroca, Patrick');
	}
}




class FormationControllerListeStagiairesSessionJavaFevrierTest extends FormationControllerImpressionsTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/liste_stagiaires/id/31');
	}


	/** @test */
	public function actionShouldBeFicheEmargement() {
		$this->assertAction('liste_stagiaires');
	}


	/** @test */
	public function pageShouldContainsH1WithLettreEmargement() {
		$this->assertXPathContentContains('//div[@class="lettre_fusion"]//h1', 
																			'Liste des stagiaires',
																			$this->_response->getBody());
	}


	/** @test */
	public function pageShouldContainsTableWithNomPrenom() {
		$this->assertXPathContentContains('//table//td', 'Curzillat');
		$this->assertXPathContentContains('//table//td', 'Benoit');
	}
}



class FormationControllerAttestationTestCase extends FormationControllerImpressionsTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/attestations/id/31');	
	}


	/** @test */
	public function actionShouldBeAttestations() {
		$this->assertAction('attestations');
	}


	/** @test */
	public function pageShouldContainsCurzillatBenoit() {
		$this->assertXPathContentContains('//h1', 'Curzillat, Benoit');
	}


	/** @test */
	public function pageShouldContainsBarrocaPatrick() {
		$this->assertXPathContentContains('//h1', 'Barroca, Patrick');
	}
}




class FormationControllerRefusTestCase extends FormationControllerImpressionsTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/formation/refus/id/31');	
	}


	/** @test */
	public function actionShouldBeAttestations() {
		$this->assertAction('refus');
	}


	/** @test */
	public function pageShouldContainsCurzillatBenoit() {
		$this->assertXPathContentContains('//p', 'Curzillat, Benoit');
	}


	/** @test */
	public function pageShouldContainsBarrocaPatrick() {
		$this->assertXPathContentContains('//p', 'Barroca, Patrick');
	}
}


?>