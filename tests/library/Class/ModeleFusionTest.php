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

class ModeleFusionWithSessionFormationAndBibTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		$this->modele = Class_ModeleFusion::getLoader()
			->newInstance()
			->setDataSource(array('session_formation' => Class_SessionFormation::getLoader()
																										 ->newInstance()
																										 ->setDateDebut('2012-03-23')
																										 ->setFormation(Class_Formation::getLoader()
																																		->newInstance()
																																		->setLibelle('Learn PHP'))
																										 ->setStagiaires(array(Class_Users::getLoader()
																																								->newInstance()
																																								->setNom('Luke')
																																								->setPrenom('Lucky'),

																																						Class_Users::getLoader()
																																								->newInstance()
																																								->setNom('Dalton')
																																								->setPrenom('Joe'))),
														'bib' => Class_Bib::getLoader()
																								->newInstance()
																								->setLibelle('Annecy')));
	}

	
	/** @test */
	public function withTemplateSessionFormationLibelleShouldReplace() {
		$this->assertEquals('Learn PHP', $this->modele
																						->setContenu('{session_formation.formation.libelle}')
																						->getContenuFusionne());
	}


	/** @test **/
	public function withTemplateSessionFormationDateDebutShouldReplace() {
		$this->assertEquals('2012-03-23', $this->modele
																						->setContenu('{session_formation.date_debut}')
																						->getContenuFusionne());
	}


	/** @test **/
	public function withTemplateSessionFormationDateDebutAndLibelleShouldReplaceBoth() {
		$this->assertEquals('2012-03-23, Learn PHP', 

												$this->modele
												  ->setContenu('{session_formation.date_debut}, {session_formation.formation.libelle}')
												  ->getContenuFusionne());
	}


	/** @test **/
	public function withTemplateSessionFormationAndBibShouldReplaceBoth() {
		$this->assertEquals('Le 23 mars 2012 à Annecy', 

												$this->modele
												  ->setContenu('Le {session_formation.date_debut_texte} à {bib.libelle}')
												  ->getContenuFusionne());
	}


	/** @test */
	public function withIntervenantsAndColsDefsShouldRepeatDataInTable() {
		$this->assertEquals('Stagiaires:<table>'.
												'<tr><td>Nom</td><td>Prenom</td><td>Signature</td></tr>'.
												'<tr><td>Luke</td><td>Lucky</td><td></td></tr>'.
												'<tr><td>Dalton</td><td>Joe</td><td></td></tr>'.
												'</table>', 

												$this->modele
												  ->setContenu('Stagiaires:{session_formation.stagiaires["Nom":nom, "Prenom":prenom, "Signature":]}')
												  ->getContenuFusionne());
	}


	/** @test */
	public function withModeleContenuWithHTMLEntitiesShouldWork() {
		$this->assertEquals('&eacute;leves:<table>'.
												'<tr><td>Nom</td><td>Pr&eacute;nom</td><td>Signature</td></tr>'.
												'<tr><td>Luke</td><td>Lucky</td><td></td></tr>'.
												'<tr><td>Dalton</td><td>Joe</td><td></td></tr>'.
												'</table>', 

												$this->modele
												->setContenu(htmlentities(utf8_decode('éleves:{session_formation.stagiaires["Nom":nom, "Prénom":prenom, "Signature":]}'), 
																									ENT_QUOTES))
												->getContenuFusionne());
	}


	/** @test */
	public function withInexistantAttributesLibelleShouldNotReplaceTheData() {
		$this->assertEquals('{session_formation.zork.libelle}', 
												
												$this->modele
												->setContenu('{session_formation.zork.libelle}')
												->getContenuFusionne());
	}


	/** @test */
	public function withInexistantAttributesForTablesLibelleShouldNotReplaceTheData() {
		$this->assertEquals('{session_formation.stagiaires["Nom":nom, "Prénom":zork, "Signature":]}', 
												
												$this->modele
												->setContenu('{session_formation.stagiaires["Nom":nom, "Prénom":zork, "Signature":]}')
												->getContenuFusionne());
	}

}

?>