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

abstract class TestFixtures {
	protected $_fixtures = array();

	public function getFixtures() {
		return 	$this->_fixtures;
	}

	public function get($name) {
		return $this->_fixtures[$name];
	}

	public function all(){
		return array_values($this->_fixtures);
	}
}


abstract class ModelTestCase extends PHPUnit_Framework_TestCase {
	protected function _buildTableMock($model, $methods) {
		$table = $this->getMock('Storm_Model_Table'.$model,$methods);
		$loader = call_user_func(array($model, 'getLoader'));
		$loader->setTable($table);
		return $table;
	}

	protected function _buildRowset($data) {
		return new Zend_Db_Table_Rowset(array('data' => $data));
	}


	protected function _setFindExpectation($model, $fixture, $id) {
		$mock_results = $this->_buildRowset(array($fixture));

		$this->_buildTableMock($model, array('find'))
			->expects($this->once())
			->method('find')
			->with($id)
			->will($this->returnValue($mock_results));
	}

	protected function tearDown() {
		Storm_Model_Abstract::unsetLoaders();
	}

	protected function _setFindAllExpectation($model, $fixtures) {
		if (!is_array($fixtures)) {
			$finst = new $fixtures;
			$fixtures = $finst->all();
		}
		$mock_results = $this->_buildRowset($fixtures);
		$tbl_newsletters = $this->_buildTableMock($model,
																							array('fetchAll'));

		$tbl_newsletters
			->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue($mock_results));
		return $tbl_newsletters;
	}

	protected function _generateLoaderFor($model, $methods) {
		$loader = $this->getMock('Mock'.$model, $methods);
		Storm_Model_Abstract::setLoaderFor($model, $loader);
		return $loader;
	}
}

?>