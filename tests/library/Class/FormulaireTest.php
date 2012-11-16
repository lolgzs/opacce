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

class FormulaireTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();
		$this->_fantomas = Class_Users::newInstanceWithId(23, ['login' => 'fantomas']);
		$this->_formulaire = Class_Formulaire::newInstanceWithId(2, ['id_user' => 23]);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Formulaire')
		->whenCalled('findAllBy')
		->answers([$this->_formulaire]);

	}


	/** @test */
	public function formulaireUserShouldBeFantomas() {
		$this->assertEquals('fantomas', $this->_formulaire->getUser()->getLogin());
	}


	/** @test */
	public function userFantomasShouldHaveOneFormulaire() {
		$this->assertEquals([$this->_formulaire], 
												$this->_fantomas->getFormulaires());
	}
}
