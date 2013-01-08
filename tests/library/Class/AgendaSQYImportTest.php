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

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
		->whenCalled('save')->answers(true);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_ArticleCategorie')
		->whenCalled('save')->answers(true);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Lieu')
		->whenCalled('save')->answers(true);

		$xml = file_get_contents(realpath(dirname(__FILE__)). '/../../fixtures/agenda-sqy.xml');
		$agenda = (new Class_Agenda_SQY())->importFromXML($xml);
		$this->_categories = $agenda->getCategories();
		$this->_events = $agenda->getEvents();
		$this->_locations = $agenda->getLocations();
	}


	/** @test */
	public function categoriesCountShouldBeFourtySix() {
		$this->assertEquals(46, count($this->_categories));
	}


	/** @test */
	public function eventsCountShouldBeSeventyThree() {
		$this->assertEquals(73, count($this->_events));
	}


	/** @test */
	public function locationCountShouldBeFourtyFive() {
		$this->assertEquals(45, count($this->_locations));
	}


	/** 
	 * @test 
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"46"][catégorie animation/loisirs]]
	 */
	public function firstCategoryLibelleShouldBeAnimationLoisirs() {
		$animation = $this->_categories[46];
		$this->assertEquals('Animation / loisirs', $animation->getLibelle());
	}


	/** 
	 * @test 
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"69"][lieu Musée de la ville]]
	 */
	public function firstLocationLibelleShouldBeMuseeDeLaVille() {
		$musee = $this->_locations[69];
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


	/** @test */
	public function firstEventTitreShouldBeRevonsLaVille() {
		$event_revons = $this->_events[3421];
		$this->assertEquals('"Rêvons la ville"', $event_revons->getTitre());
		return $event_revons;
	}


	/** 
	 * @test 
	 * @depends firstEventTitreShouldBeRevonsLaVille
	 */
	public function firstEventDescriptionShouldContainsLaNouvelleExposition($event_revons) {
		$this->assertContains('la nouvelle exposition du Musée', $event_revons->getDescription());
	}


	/** 
	 * @test 
	 * @depends firstEventTitreShouldBeRevonsLaVille
	 */
	public function firstEventContenuShouldContainsDesActeursDeSaintQuentin($event_revons) {
		$this->assertContains('<p>Des acteurs de Saint-Quentin-en-Yvelines', 
													$event_revons->getContenu());
	}


	/** 
	 * @test 
	 * @depends firstEventTitreShouldBeRevonsLaVille
	 */
	public function firstEventDebutShouldBe2012Dash05Dash16($event_revons) {
		$this->assertEquals('2012-05-16', 
												$event_revons->getEventsDebut());
	}


	/** 
	 * @test 
	 * @depends firstEventTitreShouldBeRevonsLaVille
	 */
	public function firstEventFinShouldBe2013Dash03Dash16($event_revons) {
		$this->assertEquals('2013-03-16', 
												$event_revons->getEventsFin());
	}


	/** 
	 * @test 
	 * @depends firstEventTitreShouldBeRevonsLaVille
	 */
	public function firstEventShouldHaveBeenSaved($event_revons) {
		$this->assertEquals('"Rêvons la ville"',
												Class_Article::getFirstAttributeForMethodCallAt('save', 0)->getTitre());
	}


	/** @test */
	public function categoryVisiteWithNoArticlesShouldNotHaveBeenSaved() {
		$visite = Class_Agenda_SQY_CategoryWrapper::getWrappedInstance(41);
		$this->assertFalse(Class_ArticleCategorie::methodHasBeenCalledWithParams('save', [$visite]));
	}


	/** @test */
	public function lieuMuseeDeLaVilleShouldHaveBeenSaved() {
		$this->assertEquals('Musée de la ville',
												Class_Lieu::getFirstAttributeForMethodCallAt('save', 0)->getLibelle());
	}


	/** @test */
	public function lieuGuyancourtWithNoEventsShouldNotHaveBeenSaved() {
		$guyancourt = Class_Agenda_SQY_LocationWrapper::getWrappedInstance(121);
		$this->assertFalse(Class_Lieu::methodHasBeenCalledWithParams('save', [$guyancourt]));
	}


	/** @test */
	public function fourthEventLocationShouldBeMuseeNational() {
		$event_infinis = $this->_events[3992];
		$this->assertContains('Musée national des Granges',
													$event_infinis->getLieu()->getLibelle());
	}


	/** @test */
	public function eventJazzClubShouldBeInCategoryJazz() {
		$event_jazz = $this->_events[3486];
		$this->assertEquals('Jazz', $event_jazz->getCategorie()->getLibelle());		
	}


	/**
	 * @test 
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"3654"%20location%3D"45"%20organizer%3D""%20category%3D"10,11"%20category2%3D"68"%20category3%3D""%20city2%3D"5"][event histoire soldat]]
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"10"][catégorie musique]]
	 */
	public function eventHistoireDuSoldatShouldBeInCategoryMusique() {
		$event_soldat = $this->_events[3654];
		$this->assertEquals('Musique', $event_soldat->getCategorie()->getLibelle());		
		return $event_soldat;
	}


	/** 
	 * @test 
	 * @depends eventHistoireDuSoldatShouldBeInCategoryMusique
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"68"][catégorie spectacles]]
	 */
	public function evenHistoireDuSoldatTagsShouldContainsSpectacle($event_soldat) {
		$this->assertEquals('Musique,Théâtre,Spectacles', $event_soldat->getTags());
	}


	/** 
	 * @test 
	 */
	public function eventJeuxDuMercrediShouldBeInCategoryPortail() {
		$event_jeux = $this->_events[4006];
		$this->assertEquals('Portail', $event_jeux->getCategorie()->getLibelle());		
	}


	/** 
	 * @test 
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"4037"%20location%3D"15"%20organizer%3D""%20category%3D"11"%20category2%3D"70,68"%20category3%3D""%20city2%3D"1"][évenement "d'un retournement à l'autre"]]
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"70"][catégorie Eco-emploi]]
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"68"][catégorie spectacles]]
	 */
	public function eventRetournementShouldHaveTagsEcoEmploiAndSpectacles() {
		$event_retournement = $this->_events[4037];
		$this->assertEquals('Théâtre,Eco - emploi,Spectacles', $event_retournement->getTags());
	}


	/**
	 * @test 
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"3786"%20location%3D"54"%20organizer%3D""%20category%3D"18,14"%20category2%3D"68"%20category3%3D"59"%20city2%3D"3"][évènement Pascal Péroteau]]
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"14"][catégorie Jeune public]] 
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"68"][catégorie spectacles]]
	 * [[file:../../fixtures/agenda-sqy.xml::<item%20index%3D"59"][catégorie Représentation scolaire]]
	 */
	public function eventPascalPeroteauShouldHaveTagsJeunePublicAndSpectaclesAndRepresentationScolaire() {
		$event_peroteau = $this->_events[3786];
		$this->assertEquals('Chanson,Jeune public,Spectacles,Représentation scolaire',
												$event_peroteau->getTags());
	}
}


?>