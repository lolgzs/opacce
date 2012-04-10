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
abstract class I18nStringExtractorTestCase extends PHPUnit_Framework_TestCase {
	/** @var Class_Profil_I18nStringExtractor */
	protected $_extractor;

	/** @var PHPUnit_Framework_MockObject_MockObject */
	protected $_profil;

	protected function setUp() {
		$this->_extractor = new Class_Profil_I18nStringExtractor();

		$this->_profil = Class_Profil::getLoader()
			->newInstanceWithId(5)
			->setLibelle('Chatenay');
	}

}

class EmptyI18nStringExtractorTest extends I18nStringExtractorTestCase {
	/**
	 * @test
	 * @expectedException RuntimeException
	 */
	public function withNoModelShouldRaiseException() {
		$this->_extractor->extract();

	}

}


class ChatenayAccueilI18nStringExtractorTest extends I18nStringExtractorTestCase {
	protected function setUp() {
		parent::setUp();
		$this->_profil->setCfgAccueil(I18nStringExtractorTestFixtures::createChatenayAcceuilConfiguration());
		$this->_extractor->setModel($this->_profil);
	}

	/** @test */
	public function extractShouldContainsBoiteNewsTitrePourVenir() {
		$this->assertContains('Pour venir', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsBoiteSitoTitreSitotheque() {
		$this->assertContains('Sitothèque', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsBoiteSitoTitreSitothequeOnce() {
		$actual = $this->_extractor->extract();
		$this->assertEquals(1, count(array_keys($actual, 'Sitothèque')));
	}

	/** @test */
	public function extractShouldContainsBoiteRechercheTitreRechercher() {
		$this->assertContains('Rechercher', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsBoiteRechercheMessagePourTrouverEtc() {
		$this->assertContains('Pour trouver des résultats, saisissez des termes', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsBoiteRechercheExemple() {
		$this->assertContains('par exemple : toto à la plage, pomme, harry potter', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsBoiteCarteZonesTitreRechercherUneBib() {
		$this->assertContains('Rechercher une bibliothèque', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsBoiteCarteZoneMessageMessageSousLaCarte() {
		$this->assertContains('Message sous la carte', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsBoiteLoginTitreSeConnecter() {
		$this->assertContains('Se connecter', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsBoiteLoginIdentifiantVotreIdentifiant() {
		$this->assertContains('Votre identifiant', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsBoiteLoginMotDePasseVotreMotDePasse() {
		$this->assertContains('Votre mot de passe', $this->_extractor->extract());
	}

}

class ChatenayMenusI18nStringExtractorTest extends I18nStringExtractorTestCase {
	protected function setUp() {
		parent::setUp();
		$this->_profil->setCfgMenus(I18nStringExtractorTestFixtures::createChatenayMenusConfiguration());
		$this->_extractor->setModel($this->_profil);
	}




	protected function tearDown() {
		$this->_profil->setTranslator(null);
		parent::tearDown();
	}



	/** @test */
	public function withAnotherTranslatorExtractShouldGetOriginalText() {
		$englishTranslator = $this->getMock('MockTranslator', array('translate'));
		$englishTranslator
			->expects($this->never())
			->method('translate');

		$this->_profil->setTranslator($englishTranslator);
		$this->assertContains('Accueil', $this->_extractor->extract());
	}


	/** @test */
	public function extractShouldContainsHorizontalMenuLibelleAccueil() {
		$this->assertContains('Accueil', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsHorizontalMenuLibelleAnimations() {
		$this->assertContains('Animations', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsHorizontalMenuLibelleLienVersUnSite() {
		$this->assertContains('Lien vers un site', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsHorizontalMenuLibelleInfosPratiques() {
		$this->assertContains('Infos pratiques', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsHorizontalSubmenuLibelleDernieresCritiques() {
		$this->assertContains('Dernières critiques', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsHorizontalSubmenuLibelleDerniersArticles() {
		$this->assertContains('Derniers articles', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsHorizontalSubmenuLibelleAlbumPhoto() {
		$this->assertContains('Album photo', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsMenuLibelleQuoiDeNeuf() {
		$this->assertContains('Quoi de neuf ?', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsMenuLibelleLivresAdultes() {
		$this->assertContains('Livres adultes', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsSubmenuLibelleRomans() {
		$this->assertContains('Romans', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsSubmenuPreferenceTitreNosNouveautesRomansAdultes() {
		$this->assertContains('Nos nouveautés romans adultes', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsSubmenuLibellePolar() {
		$this->assertContains('Polar', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsSubmenuPreferenceTitreNosNouveautesEnPolar() {
		$this->assertContains('Nos nouveautés en polar', $this->_extractor->extract());
	}

}

class ChatenayModulesI18nStringExtractorTest extends I18nStringExtractorTestCase {
	protected function setUp() {
		parent::setUp();
		$this->_profil->setCfgModules(I18nStringExtractorTestFixtures::createChatenayModulesConfiguration());
		$this->_extractor->setModel($this->_profil);

	}

	/** @test */
	public function extractShouldNotContainsEmptyString() {
		$this->assertNotContains('', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsRechercheFacetteMessageAffiner() {
		$this->assertContains('Affiner le résultat', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsRechercheTagsMessageElargir() {
		$this->assertContains('Elargir la recherche', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsViewnoticeOngletTitreNoticeDetaillee() {
		$this->assertContains('Notice détaillée', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsViewnoticeOngletTitreAvis() {
		$this->assertContains('Avis', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsCmsFilArianeCalendrierAriane() {
		$this->assertContains('Calendrier ariane', $this->_extractor->extract());
	}

	/** @test */
	public function extractShouldContainsCmsFilArianeArticleRecentsAriane() {
		$this->assertContains('Articles récents ariane', $this->_extractor->extract());
	}
	
}

class I18nStringExtractorTestFixtures {
	public static function createChatenayModulesConfiguration() {
		return array(
			'recherche' => array(
				'resultatsimple' => array(
					'titre' => '',
					'boite' => '',
					'liste_format' => '3',
					'liste_nb_par_page' => '5',
					'liste_codes' => 'TAN',
					'facettes_actif' => '1',
					'facettes_nombre' => '3',
					'facettes_codes' => 'ADPML',
					'facettes_message' => 'Affiner le résultat',
					'tags_actif' => '1',
					'tags_message' => 'Elargir la recherche',
				),
				'viewnotice1' => array(
					'boite' => '',
					'entete' => 'ECNA',
					'onglets' => array(
						'detail' => array(
							'aff' => 2,
							'ordre' => 1,
							'titre' => 'Notice détaillée',
							'largeur' => 0,
						),
						'avis' => array(
							'aff' => 2,
							'ordre' => 2,
							'titre' => 'Avis',
							'largeur' => 0,
						),
						'exemplaires' => array(
							'aff' => 2,
							'ordre' => 3,
							'titre' => 'Exemplaires',
							'largeur' => 0,
						),
						'resume' => array(
							'aff' => 2,
							'ordre' => 4,
							'titre' => 'Résumés, analyses',
							'largeur' => 0,
						),
						'tags' => array(
							'aff' => 2,
							'ordre' => 5,
							'titre' => 'Tags',
							'largeur' => 0,
						),
						'biographie' => array(
							'aff' => 2,
							'ordre' => 6,
							'titre' => 'Biographies',
							'largeur' => 0,
						),
					),
				),
			),
			'cms' => array(
				'articleviewbydate' => array(
					'boite' => '',
					'barre_nav' => 'Calendrier ariane',
				),
				'articleviewrecent' => array(
					'boite' => '',
					'barre_nav' => 'Articles récents ariane'
				),
			),
		);
	}

	public static function createChatenayMenusConfiguration() {
		return array(
			'H' => array(
				'libelle' => 'Menu horizontal',
				'picto' => 'vide.gif',
				'menus' => array(
					0 => array(
						'type_menu' => 'ACCUEIL',
						'libelle' => 'Accueil',
						'picto' => 'vide.gif',
						'sous_menus' => ''
					),
					1 => array(
						'type_menu' => 'PROFIL',
						'libelle' => 'Animations',
						'picto' => 'vide.gif',
						'preferences' => array('clef_profil' => '46'),
						'sous_menus' => ''
					),
					2 => array(
						'type_menu' => 'URL',
						'libelle' => 'Lien vers un site',
						'picto' => 'vide.gif',
						'preferences' => array(
							'url' => 'http://google.fr',
							'target' => '0'
						),
						'sous_menus' => ''
					),
					3 => array(
						'type_menu' => 'MENU',
						'libelle' => 'Infos pratiques',
						'picto' => 'vide.gif',
						'sous_menus' => array(
							0 => array(
								'type_menu' => 'AVIS',
								'libelle' => 'Dernières critiques',
								'picto' => 'vide.gif'
							),
							1 => array(
								'type_menu' => 'LAST_NEWS',
								'libelle' => 'Derniers articles',
								'picto' => 'vide.gif'
							),
							2 => array(
								'type_menu' => 'ALBUM',
								'libelle' => 'Album photo',
								'picto' => 'vide.gif'
							),
						),
					),
				),
			),
			2 => array(
				'libelle' => 'Quoi de neuf ?',
				'picto' => 'vide.gif',
				'menus' => array(
					array(
						'type_menu' => 'MENU',
						'libelle' => 'Livres adultes',
						'picto' => 'vide.gif',
						'sous_menus' => array(
							array(
								'type_menu' => 'CATALOGUE',
								'libelle' => 'Romans',
								'picto' => 'vide.gif',
								'preferences' => array(
									'titre' => 'Nos nouveautés romans adultes',
									'aleatoire' => '0',
									'tri' => '1',
									'nb_notices' => '30',
									'nb_analyse' => '100',
									'id_catalogue' => '39',
									'id_panier' => '0',
									'id_user' => 0
								)
							),
							array(
								'type_menu' => 'CATALOGUE',
								'libelle' => 'Polar',
								'picto' => 'vide.gif',
								'preferences' => array(
									'titre' => 'Nos nouveautés en polar',
									'aleatoire' => '0',
									'tri' => '1',
									'nb_notices' => '30',
									'nb_analyse' => '50',
									'id_catalogue' => '6',
									'id_panier' => '0',
									'id_user' => 0
								),
							),
						),
					),
				),
			),
		);

	}

	public static function createChatenayAcceuilConfiguration() {
		return array(
				'modules' => array(
						1 => array(
								'division' => '1',
								'type_module' => 'MENU_VERTICAL',
								'preferences' => array(
										'boite' => 'boite_decouverte_puce',
										'afficher_titre' => '1',
										'menu' => '2',
										'Valider' => 'Valider',
								)
						),
						2 => array(
								'division' => '1',
								'type_module' => 'NEWS',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Pour venir',
										'Valider' => 'Valider',
								)
						),
						3 => array(
								'division' => '1',
								'type_module' => 'SITO',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Sitothèque',
										'Valider' => 'Valider',
								)
						),
						4 => array(
								'division' => '2',
								'type_module' => 'SITO',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Sitothèque',
										'Valider' => 'Valider',
								)
						),
						5 => array(
								'division' => '1',
								'type_module' => 'RECH_SIMPLE',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Rechercher',
										'message' => 'Pour trouver des résultats, saisissez des termes',
										'exemple' => 'par exemple : toto à la plage, pomme, harry potter',
										'Valider' => 'Valider',
								)
						),
						6 => array(
								'division' => '1',
								'type_module' => 'CARTE_ZONES',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Rechercher une bibliothèque',
										'message_carte' => 'Message sous la carte',
										'Valider' => 'Valider',
								)
						),
						7 => array(
								'division' => '1',
								'type_module' => 'LOGIN',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Se connecter',
										'identifiant' => 'Votre identifiant',
										'mot_de_passe' => 'Votre mot de passe',
										'Valider' => 'Valider',
								)
						),
				)
		);
	}

}

?>