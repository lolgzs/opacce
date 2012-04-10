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
require_once 'ModelTestCase.php';

abstract class AdminVarTestCase extends ModelTestCase {
	protected function _onLoaderFindReturns($name, $value) {
		$this
			->_generateLoaderFor('Class_AdminVar', array('find'))
			->expects($this->once())
			->method('find')
			->with($name)
			->will($this->returnValue($value));
	}
}

class AdminVarTestGet extends AdminVarTestCase {
	public function testGetReturnsValeur() {
		$my_var = Class_AdminVar::getLoader()
			->newInstance()
			->setClef('MY_VAR')
			->setValeur(2);

		$this->_onLoaderFindReturns('MY_VAR', $my_var);
		$this->assertEquals(2, Class_AdminVar::get('MY_VAR'));
	}

	public function testGetReturnsNullIfUnknownVar() {
		$this->_onLoaderFindReturns('INEXISTANT', null);
		$this->assertEquals(null, Class_AdminVar::get('INEXISTANT'));
	}


	public function testSQLForUpdate() {
		$tbl_var = $this->_buildTableMock('Class_AdminVar',
																			 array('update'));

		$tbl_var
			->expects($this->once())
			->method('update')
			->with(array('clef' => 'MODO_AVIS',
									 'valeur' => 2),
						 "clef='MODO_AVIS'");

		$my_var = new Class_AdminVar();
		$my_var->initializeAttributes(array('id' => 'MODO_AVIS',
																				'valeur' => 2));
		$my_var->save();
	}



	/** @test */
	public function withoutLanguesVarsGetLanguesShouldReturnEmptyArray() {
		$this->_onLoaderFindReturns('LANGUES', null);
		$this->assertEquals(array(), Class_AdminVar::getLangues());
	}


	/** @test */
	public function withLanguesEN_semicolon_RO_ShouldReturnArrayWithEN_RO() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('en ; RO');
		$this->assertEquals(array('fr', 'en', 'ro'), Class_AdminVar::getLangues());
	}


	/** @test */
	public function withDuplicatedLanguesShouldReturnArrayWithEachElementOnce() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('en ; RO;fr ;ro;');
		$this->assertEquals(array('fr', 'en', 'ro'), Class_AdminVar::getLangues());
	}

	/** @test */
	public function withUndefinedWorkflowIsWorkflowEnabledShouldReturnFalse() {
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->assertFalse(Class_AdminVar::isWorkflowEnabled());
	}

	/** @test */
	public function withWorkflowDefinedToOneIsWorkflowEnabledShouldReturnTrue() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('WORKFLOW')
			->setValeur('1');
		$this->assertTrue(Class_AdminVar::isWorkflowEnabled());
	}

	/** @test */
	public function withWorkflowDefinedToNonOneValueIsWorklowEnabledShouldReturnFalse() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('WORKFLOW')
			->setValeur('A sample non 1 value');
		$this->assertFalse(Class_AdminVar::isWorkflowEnabled());
	}

}

class AdminVarTestSet extends AdminVarTestCase {
	/** @test */
	public function setShouldCreateInstanceIfNotExist() {
		$wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AdminVar')
								->whenCalled('find')->answers(null)->getWrapper()
								->whenCalled('save')->answers(true)->getWrapper();

		Class_AdminVar::set('DUMMY_KEY', '1');

		$this->assertTrue($wrapper->methodHasBeenCalled('newInstance'));
	}

	/** @test */
	public function setShouldChangeExistingValue() {
		Class_AdminVar::getLoader()->newInstanceWithId('DUMMY_ADMIN_VAR')
															->setClef('DUMMY_ADMIN_VAR')
															->setValeur('dummy value');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AdminVar')
								->whenCalled('save')->answers(true);

		Class_AdminVar::set('DUMMY_ADMIN_VAR', 'another value');

		$this->assertEquals('another value', Class_AdminVar::get('DUMMY_ADMIN_VAR'));

	}
}

?>