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

class MatiereTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->mock_sql = $this
												->getMockBuilder('Class_Systeme_Sql')
												->disableOriginalConstructor()
												->getMock();
		Zend_Registry::set('sql', $this->mock_sql);
	}


	/** @test */
	function sqlQueriesShouldBeEscaped() {
		$this->mock_sql
			->expects($this->once())
			->method('fetchOne')
			->with("select libelle from codif_matiere where id_matiere=4")
			->will($this->returnValue("L'art du 20ème"));

		$this->mock_sql
			->expects($this->once())
			->method('fetchAll')
			->with("select id_matiere from codif_matiere where libelle like 'L''art du 20ème : %'")
			->will($this->returnValue(array(array('id_matiere' => 12), 
																			array('id_matiere' => 24))));

		$matiere = new Class_Matiere();
		$this->assertEquals('12 24 ',	$matiere->getSousVedettes(4));
	}
}


?>
