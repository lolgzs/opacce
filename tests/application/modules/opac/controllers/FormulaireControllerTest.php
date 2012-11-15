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

class FormulaireControllerPostActionTest extends Admin_AbstractControllerTestCase {
  public function setUp() {
    parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Formulaire')
		->whenCalled('save')
		->willDo(function ($formulaire) {
				$formulaire->setId(2)->cache();
				return true;
			});


		$timesource = new TimeSourceForTest();
		$timesource->setTime(strtotime('2012-10-23 12:32:00'));
		Class_Formulaire::setTimeSource($timesource);

    $this->postDispatch('/formulaire/add', 
												['nom' => 'Tinguette' ,
												 'prenom' => 'Quentin' ]
												,true);

		$this->new_formulaire = Class_Formulaire::find(2);
	}

	
	/** @test */
	public function saveFormulaireShouldHaveNomTinguette() {
		$this->assertEquals('Tinguette', $this->new_formulaire->getNom());
	}

	
	/** @test */
	public function dateCreationShouldBeNow() {
		$this->assertEquals('2012-10-23 12:32:00', $this->new_formulaire->getDateCreation());
	}


	/** @test */
	public function getDataShouldAnswerSerializedNomPrenom() {
		$this->assertEquals(serialize(['nom' => 'Tinguette' ,
																	 'prenom' => 'Quentin']),
																	$this->new_formulaire->getData());
	}
}

?>