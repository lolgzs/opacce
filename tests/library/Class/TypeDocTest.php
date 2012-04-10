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

class TypeDocTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->cosmo_types = Class_CosmoVar::getLoader()
			->newInstanceWithId('types_docs')
			->setListe("0:non identifié\r\n1:livres\r\n2:périodiques");

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_CosmoVar')
			->whenCalled('save')
			->answers(true);

		$this->types_docs = Class_TypeDoc::getLoader()->findAll();
	}


	/** @test */
	function instancesCountShouldBeThree() {
		$this->assertEquals(3, count($this->types_docs));
	}


	/** @test */
	function firstInstanceIdShouldEqualsZero() {
		$this->assertEquals(0, array_first($this->types_docs)->getId());
	}


	/** @test */
	function firstInstanceLabelShouldBeNonIdentifie() {
		$this->assertEquals('non identifié', array_first($this->types_docs)->getLabel());
	}


	/** @test */
	function thirdInstanceIdShouldBeTwo() {
		$this->assertEquals(2, array_at(2, $this->types_docs)->getId());
	}


	/** @test */
	function thirdInstanceLabelShouldBePeriodiques() {
		$this->assertEquals('périodiques', array_at(2, $this->types_docs)->getLabel());
	}


	/** @test */
	function saveNewInstanceVideoShouldUpdateTypesDocsVars() {
		Class_TypeDoc::newWithLabel('videos')->save();

		$this->assertEquals("0:non identifié\r\n1:livres\r\n2:périodiques\r\n3:videos",
												$this->cosmo_types->getListe());
	}


	/** @test */
	function saveModifiedPeriodiquesShouldUpdateTypesDocsVars() {
		array_at(2, $this->types_docs)
			->setLabel('journaux')
			->save();

		$this->assertEquals("0:non identifié\r\n1:livres\r\n2:journaux",
												$this->cosmo_types->getListe(),
												'Current value: '.$this->cosmo_types->getListe());		
	}


	/** @test */
	function saveNewInstancesVideoAndCDShouldUpdateTypesDocsVar() {
		Class_TypeDoc::getLoader()
			->newInstance()
			->setLabel('videos')
			->save();

		Class_TypeDoc::getLoader()
			->newInstance()
			->setLabel('CD')
			->save();

		$this->assertEquals("0:non identifié\r\n1:livres\r\n2:périodiques\r\n3:videos\r\n4:CD",
												$this->cosmo_types->getListe(),
												'Current value: '.$this->cosmo_types->getListe());
	}


	/** @test */
	function deleteLivresShouldUpdateTypesDocsVar() {
		array_at(1, $this->types_docs)->delete();

		$this->assertEquals("0:non identifié\r\n2:périodiques",
												$this->cosmo_types->getListe());
	}


	/** @test */
	function typeDocToIdLabelArrayShouldAnswerAssociativeArray() {
		$array = Class_TypeDoc::toIdLabelArray(Class_TypeDoc::getLoader()->findAll());
		$this->assertEquals($array, array(0 => 'non identifié', 1 => 'livres', 2 => 'périodiques'));
	}


	/** @test */
	function loaderFindTwoShouldReturnPeriodiques() {
		$this->assertEquals('périodiques', Class_TypeDoc::getLoader()->find(2)->getLabel());
	}


	/** @test */
	function loaderFindFourShouldReturnNull() {
		$this->assertEquals(null, Class_TypeDoc::getLoader()->find(4));
	}
}

?>