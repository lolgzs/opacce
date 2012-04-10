<?php
/*
STORM is under the MIT License (MIT)

Copyright (c) 2010-2011 Agence Française Informatique http://www.afi-sa.fr

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

class MockTable {
	protected $_fetchAllRequest;

	public function getAdapter() {
		return $this;
	}

	public function fetchAll($request) {
		$this->_fetchAllRequest = $request;
		return array();
	}

	public function getFetchAllRequest() {
		return $this->_fetchAllRequest;
	}
}


class Storm_Test_LoaderTest extends Storm_Test_ModelTestCase {
	/** @test */
	function findAllShouldAcceptASQLQuery() {
		$loader = Storm_Test_Mock_User::getLoader();
		$table = new MockTable();
		$loader->setTable($table);

		$this->assertEquals(array(), $loader->findAll('SELECT * FROM USERS'));
		$this->assertEquals('SELECT * FROM USERS', $table->getFetchAllRequest());
	}


	/** @test */
	public function countByWithWhereShouldBuildRightSQL() {
		$loader = Storm_Test_Mock_User::getLoader();
		$loader->setTable($table = Storm_Test_ObjectWrapper::mock());
		
		$select = Storm_Test_ObjectWrapper::mock()
			->whenCalled('from')
			->with($table, array('count(id) as numberof'))
			->answers(null)

			->whenCalled('where')
			->with('nom like "%zork%"')
			->answers(null)

			->beStrict();
		
		$table
			->whenCalled('select')
			->answers($select)
			
			->whenCalled('fetchAll')
			->with($select)
			->answers(new Zend_Db_Table_Rowset(array('data' => array(array('numberof' => 3)))));


		$this->assertEquals(3, $loader->countBy(array('where' => 'nom like "%zork%"')));
	}
}

?>