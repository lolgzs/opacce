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
require_once 'Class/EntrepotOAI.php';


class EntrepotOAITestFindById extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$entrepot_row = array('id' => 5,
													'libelle' => 'BNF gallica',
													'handler' => 'http://oai.bnf.fr/oai2/OAIHandler');

		$tbl_entrepot_oai = $this->getMock('MockTableEntrepotOAI',
																			 array('fetchRow'));
		$tbl_entrepot_oai
			->expects($this->once())
			->method('fetchRow')
			->with('id=5')
			->will($this->returnValue($entrepot_row));

		Class_EntrepotOAI::setTableEntrepotOAI($tbl_entrepot_oai);
	}


	public function testFirstEntrepotIsBNF() {
		$entrepot = Class_EntrepotOAI::findById(5);

		$this->assertEquals('BNF gallica',
												$entrepot->getLibelle());
		$this->assertEquals('http://oai.bnf.fr/oai2/OAIHandler',
												$entrepot->getHandler());
		$this->assertEquals(5,
												$entrepot->getId());
	}

}


class EntrepotOAITestFindAll extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$entrepot_rows = array(
													 array('id' => 1,
																 'libelle' => 'BNF gallica',
																 'handler' => 'http://oai.bnf.fr/oai2/OAIHandler'),
													 array('id' => 2,
																 'libelle' => 'ifremer',
																 'handler' => 'http://www.ifremer.fr/docelec/oai/OAIHandler'));


		$tbl_entrepot_oai = $this->getMock('MockTableEntrepotOAI',
																			 array('fetchAll'));
		$tbl_entrepot_oai
			->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue($entrepot_rows));

		Class_EntrepotOAI::setTableEntrepotOAI($tbl_entrepot_oai);
		$this->entrepots = Class_EntrepotOAI::findAll();
	}


	public function testFirstEntrepotIsBNF() {
		$entrepot = $this->entrepots[0];

		$this->assertEquals('BNF gallica',
												$entrepot->getLibelle());
		$this->assertEquals('http://oai.bnf.fr/oai2/OAIHandler',
												$entrepot->getHandler());
		$this->assertEquals(1,
												$entrepot->getId());
												
		
	}

	public function testSecondEntrepotIsIfremer() {		
		$entrepot = $this->entrepots[1];

		$this->assertEquals('ifremer',
												$entrepot->getLibelle());
		$this->assertEquals('http://www.ifremer.fr/docelec/oai/OAIHandler',
												$entrepot->getHandler());
		$this->assertEquals(2,
												$entrepot->getId());
		
	}
}

class EntrepotOAITestTable extends PHPUnit_Framework_TestCase {
	public function testTableDefaultsToTableEntrepotOAI() {
		Class_EntrepotOAI::setTableEntrepotOAI(null);
		$this->assertTrue(Class_EntrepotOAI::getTableEntrepotOAI() instanceof TableEntrepotOAI);
	}
}

?>
