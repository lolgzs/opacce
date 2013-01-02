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


class AgendaSQYImportTest extends Storm_Test_ModelTestCase {
	protected $_categories;
	protected $_events;
	protected $_locations;

	public function setUp() {
		parent::setUp();
		$xml = file_get_contents(realpath(dirname(__FILE__)). '/../../fixtures/agenda-sqy.xml');
		$agenda = (new Class_Agenda_SQY())->importFromXML($xml);
		$this->_categories = $agenda->getCategories();
		$this->_events = $agenda->getEvents();
		$this->_locations = $agenda->getLocations();
	}


	/** @test */
	public function categoriesCountShouldBeFourtyFive() {
		$this->assertEquals(45, count($this->_categories));
	}


	/** @test */
	public function eventsCountShouldBeHeightyFive() {
		$this->assertEquals(85, count($this->_events));
	}


	/** @test */
	public function locationCountShouldBeFourtyFive() {
		$this->assertEquals(45, count($this->_locations));
	}


	/** @test */
	public function firstCategoryLibelleShouldBeAnimationLoisirs() {
		$animation = $this->_categories[0];
		$this->assertEquals('Animation / loisirs', $animation->getLibelle());
	}


	/** @test */
	public function firstLocationLibelleShouldBeMuseeDeLaVille() {
		$musee = $this->_locations[0];
		$this->assertEquals('Musée de la ville', $musee->getLibelle());
		return $musee;
	}


	/** 
	 * @test 
	 * @depends firstLocationLibelleShouldBeMuseeDeLaVille
	 */
	public function firstLocationCodePostalShouldBe78180($musee) {
		$this->assertEquals('78180', $musee->getCodePostal());
	}


	/** 
	 * @test 
	 * @depends firstLocationLibelleShouldBeMuseeDeLaVille
	 */
	public function firstLocationVilleShouldBeMontigny($musee) {
		$this->assertEquals('Montigny-le-Bretonneux', $musee->getVille());
	}


	/** 
	 * @test 
	 * @depends firstLocationLibelleShouldBeMuseeDeLaVille
	 */
	public function firstLocationPaysShouldBeFrance($musee) {
		$this->assertEquals('France', $musee->getPays());
	}
}


?>