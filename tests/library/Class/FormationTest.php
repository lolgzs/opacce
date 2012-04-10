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

class FormationJavaWithNoSessionTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		$this->_learning_java = Class_Formation::getLoader()
			->newInstanceWithId(99999)
			->setLibelle('Learning Java');
	}

	/** @test */
	function libelleShouldAnswerLearningJava() {
		$this->assertEquals('Learning Java', $this->_learning_java->getLibelle());
	}


	/** @test */
	function getSessionsShouldReturnEmptyArray() {
		$this->assertSame(array(), $this->_learning_java->getSessions());
	}

	/** @test */
	function getAnneeShouldReturnCurrentYear() {
		$this->assertEquals(date('Y'), $this->_learning_java->getAnnee());
	}
}


class FormationSmalltalkWithTwoSessionsTest extends Storm_Test_ModelTestCase {
	protected $_patrick_inscription;
	protected $_patrick;
	protected $_laurent;
	protected $_laurent_intervention;

	public function setUp() {
		$this->_learn_st = Class_Formation::getLoader()
																					->newInstanceWithId(3)
																					->setLibelle('Learning Smalltalk')
																					->setSessions(array( 
																															$this->_session_janvier = Class_SessionFormation::getLoader()
																															->newInstanceWithId(1)
																															->setFormationId(3)
																															->setDateDebut('2009-01-05')
																															->setDateLimiteInscription('0000-00-00')
																															->setEffectifMin(1)
																															->setEffectifMax(3),

																															$this->_session_fevrier = Class_SessionFormation::getLoader()
																															->newInstanceWithId(2)
																															->setFormationId(3)
																															->setDateDebut('2009-02-05')));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SessionFormationInscription')
			->whenCalled('findAllBy')
			->with(array('role' => 'session_formation',
									 'model' => $this->_session_janvier))
			->answers(array(
											$this->_patrick_inscription = Class_SessionFormationInscription::getLoader()
																													 ->newInstanceWithId(1)
																													 ->setSessionFormationId(1)
																													 ->setStagiaireId(5)))

			->whenCalled('findAllBy')
			->with(array('role' => 'session_formation',
									 'model' => $this->_session_fevrier))
			->answers(array())

			->whenCalled('findAllBy')
			->with(array('role' => 'stagiaire',
									 'model' => $this->_patrick = Class_Users::getLoader()
																			 ->newInstanceWithId(5)
																			 ->setPrenom('Patrick')
																			 ->setLogin('pat')
									                     ->setUserGroups(array(Class_UserGroup::getLoader()
																														 ->newInstanceWithId(76)
																														 ->addRightSuivreFormation()))))
			->answers(array($this->_patrick_inscription));



		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_SessionFormationIntervention')
			->whenCalled('findAllBy')
			->with(array('role' => 'session_intervention',
									 'model' => $this->_session_janvier))
			->answers(array(
											$this->_laurent_intervention = Class_SessionFormationIntervention::getLoader()
																													 ->newInstanceWithId(1)
																													 ->setSessionInterventionId(1)
																													 ->setIntervenantId(6)))

			->whenCalled('findAllBy')
			->with(array('role' => 'session_intervention',
									 'model' => $this->_session_fevrier))
			->answers(array())

			->whenCalled('findAllBy')
			->with(array('role' => 'intervenant',
									 'model' => $this->_laurent = Class_Users::getLoader()
																			 ->newInstanceWithId(6)
																			 ->setPrenom('Laurent')))
			->answers(array($this->_laurent_intervention));
	}


	/** @test */
	function getSessionsShouldReturnSessionJanvierAndFevrier() {
		$this->assertEquals(array($this->_session_janvier, $this->_session_fevrier),
												$this->_learn_st->getSessions());
	}


	/** @test */
	public function sessionJanvierDateLimiteInscriptionShouldReturnNull() {
		$this->assertEquals(null, $this->_session_janvier->getDateLimiteInscription());
	}


	/** @test */
	public function sessionJanvierHasDateLimiteInscriptionShouldReturnFalse() {
		$this->assertFalse($this->_session_janvier->hasDateLimiteInscription());
	}


	/** @test */
	function getAnneeShouldReturn2009() {
		$this->assertEquals('2009', $this->_learn_st->getAnnee());
	}


	/** @test */
	function patrickInscriptionFormationShouldBeSessionJanvier() {
		$this->assertSame($this->_session_janvier, $this->_patrick_inscription->getSessionFormation());
	}


	/** @test */
	function patrickInscriptionStagiaireShouldBePatrick() {
		$this->assertSame($this->_patrick, $this->_patrick_inscription->getStagiaire());
	}


	/** @test */
	function patrickSessionFormationsShouldReturnArrayWithSessionJanvier() {
		$this->assertEquals(array($this->_session_janvier), $this->_patrick->getSessionFormations());
	}


	/** @test */
	public function laurentSessionInterventionsShouldReturnArrayWithSessionJanvier() {
		$this->assertEquals(array($this->_session_janvier), $this->_laurent->getSessionInterventions());
	}


	/** @test */
	public function patrickAddSessionFevrierShouldUpdateSessionFormationList() {
		$this->_patrick->addSessionFormation($this->_session_fevrier);
		$this->assertEquals(array($this->_session_janvier, $this->_session_fevrier), 
												$this->_patrick->getSessionFormations());
		return $this->_patrick;
	}


	/** @test */
	public function sessionJanvierShouldBeValid() {
		$this->assertTrue($this->_session_janvier->isValid(),
											implode(',', $this->_session_janvier->getErrors()));
	}


	/** @test */
	public function withTooManyInscritsShouldNotBeValid() {
		$this
			->_session_janvier
			->setEffectifMax(2)
			->addStagiaire(Class_Users::getLoader()
										 ->newInstanceWithId(94)
										 ->setLogin('riri'))
			->addStagiaire(Class_Users::getLoader()
										 ->newInstanceWithId(95)
										 ->setLogin('fifi'));
		$this->assertFalse($this->_session_janvier->isValid());
	}


	/** 
	 * @test 
	 * @depends patrickAddSessionFevrierShouldUpdateSessionFormationList
	 */
	public function patrickRemoveSessionFevrireShouldUpdateSessionFormatioListe($patrick) {
		$patrick->removeSessionFormation($this->_session_fevrier);
		$this->assertEquals(array($this->_session_janvier), $patrick->getSessionFormations());
	}


	/** @test */
	function sessionJanvierStagiairesShouldReturnAnArrayWithPatrick() {
		$this->assertEquals(array($this->_patrick), $this->_session_janvier->getStagiaires());
	}


	/** @test */
	function sessionJanvierIntervenantsShouldReturnAnArrayWithLaurent() {
		$this->assertEquals(array($this->_laurent), $this->_session_janvier->getIntervenants());
	}


	/** @test */
	function formationLearnSmalltalkStagiairesShouldReturnAnArrayWithPatrick() {
		$stagiaires = $this->_learn_st->getStagiaires();

		$this->assertEquals(array($this->_patrick), 
												$stagiaires);
	}


	/** @test */
	function patrickFormationsShouldReturnAnArrayWithLearnSt() {
		$this->assertEquals(array($this->_learn_st), $this->_patrick->getFormations());
	}
}




class FormationHaskellWithTwoSessionsAccrossYearTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		$this->_learn_haskell = Class_Formation::getLoader()
			->newInstanceWithId(5)
			->setLibelle('Learning Haskell')
			->setSessions(array( 
													$this->_session_decembre = Class_SessionFormation::getLoader()
													->newInstanceWithId(51)
													->setDateDebut('2025-12-23'),
													
													$this->_session_janvier = Class_SessionFormation::getLoader()
													->newInstanceWithId(52)
													->setDateDebut('2026-01-05')
													 )
										);
	}


	/** @test */
	function getAnneeShouldReturn2025() {
		$this->assertEquals('2025', $this->_learn_haskell->getAnnee());
	}
}

?>