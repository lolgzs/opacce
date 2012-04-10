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

class ModererTest extends PHPUnit_Framework_TestCase {
	protected $_original_sql;
	protected $moderer;

	public function setUp() {
		$this->_original_sql = Zend_Registry::get('sql');

		$this->mock_sql = $this->getMockBuilder('Class_Systeme_Sql')
			->disableOriginalConstructor()
			->getMock();
		Zend_Registry::set('sql', $this->mock_sql);		

		$this->moderer = new Class_Moderer();
	}


	public function tearDown() {
		Zend_Registry::set('sql', $this->_original_sql);		
	} 


	/** @test */
	function actionOneShouldUpdate() {
		$this->mock_sql
			->expects($this->once())
			->method('execute')
			->with("update cms_avis set STATUT=1 where ID_USER=2 and ID_CMS=5");


		$this->mock_sql
			->expects($this->once())
			->method('fetchAll')
			->with("select * from cms_avis where ID_USER=2 and ID_CMS=5")
			->will($this->returnValue(array(array("STATUT" => 0))));

		$this->moderer->modererAvisCms(1, 2, 5);
	}


	/** @test */
	function actionTwoShouldDelete() {
		$this->mock_sql
			->expects($this->once())
			->method('execute')
			->with("delete from cms_avis where ID_USER=10 and ID_CMS=23");


		$this->mock_sql
			->expects($this->once())
			->method('fetchAll')
			->with("select * from cms_avis where ID_USER=10 and ID_CMS=23")
			->will($this->returnValue(array(array("STATUT" => 0))));

		$this->moderer->modererAvisCms(2, 10, 23);
	}
}

?>