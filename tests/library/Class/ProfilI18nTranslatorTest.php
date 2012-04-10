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
abstract class Profil18nTranslatorTestCase extends PHPUnit_Framework_TestCase {
	/** @var Class_Profil_I18nTranslator */
	protected $_profilTranslator;

	/** @var PHPUnit_Framework_MockObject_MockObject */
	protected $_translator;

	protected function setUp() {
		$this->_translator = $this->getMockBuilder('Class_I18nTranslator')
													->disableOriginalConstructor()
													->getMock();

		$this->_profilTranslator = new Class_Profil_I18nTranslator($this->_translator);

	}

	public function simulateEnTranslate($value) {
		$value = (string)$value;
		$key = md5($value);
		$dictionary = I18nProfilTranslatorTestFixtures::createChatenayEnTranslation();

		if (array_key_exists($key, $dictionary))
			return $dictionary[$key];

		return $value;
	}

	public function simulateFrTranslate($value) {
		return $value;
	}

}

class ChatenayUnknownI18nTranslatorTest extends Profil18nTranslatorTestCase {
	/** @test */
	public function translateOfUnknownTypeShouldChangeNothing() {
		$this->assertEquals(
			array('exemple' => 'of unknown'),
			$this->_profilTranslator->translate(array('exemple' => 'of unknown'),	'Completely unknown type of configuration')
		);
	}
}

class ChatenayMenusI18nTranslatorTest extends Profil18nTranslatorTestCase {
	/** @test */
	public function translateOfEmptyDatasShouldChangeNothing() {
		$this->assertEquals(array(), $this->_profilTranslator->translate(array(), 'Menus'));
	}

	/** @test */
	public function translateInFrShouldChangeNothing() {
		$this->_translator->expects($this->atLeastOnce())
											->method('translate')
											->will($this->returnCallback(array($this, 'simulateFrTranslate')))
											;

		$this->assertEquals(
			I18nProfilTranslatorTestFixtures::createChatenayMenusConfiguration(),
			$this->_profilTranslator->translate(I18nProfilTranslatorTestFixtures::createChatenayMenusConfiguration(), 'Menus')
		);
	}

	/** @test */
	public function translateInEnShouldTranslateDatas() {
		$this->_translator->expects($this->atLeastOnce())
											->method('translate')
											->will($this->returnCallback(array($this, 'simulateEnTranslate')))
											;

		$this->assertEquals(
			I18nProfilTranslatorTestFixtures::createChatenayMenusTranslatedConfiguration(),
			$this->_profilTranslator->translate(I18nProfilTranslatorTestFixtures::createChatenayMenusConfiguration(), 'Menus')
		);

	}

}

class ChatenayAccueilI18nTranslatorTest extends Profil18nTranslatorTestCase {
	/** @test */
	public function translateOfEmptyDatasShouldChangeNothing() {
		$this->assertEquals(array(), $this->_profilTranslator->translate(array(), 'Accueil'));
	}

	/** @test */
	public function translateInFrShouldChangeNothing() {
		$this->_translator->expects($this->atLeastOnce())
											->method('translate')
											->will($this->returnCallback(array($this, 'simulateFrTranslate')))
											;

		$this->assertEquals(
			I18nProfilTranslatorTestFixtures::createChatenayAcceuilConfiguration(),
			$this->_profilTranslator->translate(I18nProfilTranslatorTestFixtures::createChatenayAcceuilConfiguration(), 'Accueil')
		);
	}

	/** @test */
	public function translateInEnShouldTranslateDatas() {
		$this->_translator->expects($this->atLeastOnce())
											->method('translate')
											->will($this->returnCallback(array($this, 'simulateEnTranslate')))
											;

		$this->assertEquals(
			I18nProfilTranslatorTestFixtures::createChatenayAcceuilTranslatedConfiguration(),
			$this->_profilTranslator->translate(I18nProfilTranslatorTestFixtures::createChatenayAcceuilConfiguration(), 'Accueil')
		);

	}
}

class ChatenayModulesI18nTranslatorTest extends Profil18nTranslatorTestCase {
	/** @test */
	public function translateOfEmptyDatasShouldChangeNothing() {
		$this->assertEquals(array(), $this->_profilTranslator->translate(array(), 'Modules'));
	}


	/** @test */
	public function translateInFrShouldChangeNothing() {
		$this->_translator->expects($this->atLeastOnce())
											->method('translate')
											->will($this->returnCallback(array($this, 'simulateFrTranslate')))
											;

		$this->assertEquals(
			I18nProfilTranslatorTestFixtures::createChatenayModulesConfiguration(),
			$this->_profilTranslator->translate(I18nProfilTranslatorTestFixtures::createChatenayModulesConfiguration(), 'Modules')
		);
	}

	/** @test */
	public function translateInEnShouldTranslateDatas() {
		$this->_translator->expects($this->atLeastOnce())
											->method('translate')
											->will($this->returnCallback(array($this, 'simulateEnTranslate')))
											;

		$this->assertEquals(
			I18nProfilTranslatorTestFixtures::createChatenayModulesTranslatedConfiguration(),
			$this->_profilTranslator->translate(I18nProfilTranslatorTestFixtures::createChatenayModulesConfiguration(), 'Modules')
		);

	}

}


class I18nProfilTranslatorTestFixtures {
	public static function createChatenayModulesTranslatedConfiguration() {
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
					'facettes_message' => 'Refine your search',
					'tags_actif' => '1',
					'tags_message' => 'Enlarge your search',
				),
				'viewnotice1' => array(
					'boite' => '',
					'entete' => 'ECNA',
					'onglets' => array(
						'detail' => array(
							'aff' => 2,
							'ordre' => 1,
							'titre' => 'Detailed notice',
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
							'titre' => 'Digests, analytics',
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
					'barre_nav' => 'Calendar in breadcrumb',
				),
				'articleviewrecent' => array(
					'boite' => '',
					'barre_nav' => 'Latest news in breadcrumb'
				),
			),
		);
	}

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

	public static function createChatenayEnTranslation() {
		return array(
			md5('Menu horizontal') => 'Horizontal menu',
			md5('Accueil') => 'Home',
			md5('Animations') => 'Events',
			md5('Lien vers un site') => 'Link to a site',
			md5('Infos pratiques') => 'Utilities',
			md5('Dernières critiques') => 'Latest critics',
			md5('Derniers articles') => 'Latest news',
			md5('Album photo') => 'Photo album',
			md5('Quoi de neuf ?') => 'What\'s up?',
			md5('Livres adultes') => 'Adults books',
			md5('Romans') => 'Fictions',
			md5('Nos nouveautés romans adultes') => 'Ours latest adults fictions',
			md5('Nos nouveautés en polar') => 'Our latests polars',
			md5('Pour venir') => 'How to come',
			md5('Sitothèque') => 'Sites library',
			md5('Rechercher') => 'Search',
			md5('Pour trouver des résultats, saisissez des termes') => 'To search please enter search terms',
			md5('par exemple : toto à la plage, pomme, harry potter') => 'For example : toto at the beach, apple, harry potter',
			md5('Message sous la carte') => 'Under the map message',
			md5('Votre identifiant') => 'Login',
			md5('Votre mot de passe') => 'Password',
			md5('Affiner le résultat') => 'Refine your search',
			md5('Elargir la recherche') => 'Enlarge your search',
			md5('Notice détaillée') => 'Detailed notice',
			md5('Résumés, analyses') => 'Digests, analytics',
			md5('Calendrier ariane') => 'Calendar in breadcrumb',
			md5('Articles récents ariane') => 'Latest news in breadcrumb',
		);
	}


	public static function createChatenayMenusTranslatedConfiguration() {
		return array(
			'H' => array(
				'libelle' => 'Horizontal menu',
				'picto' => 'vide.gif',
				'menus' => array(
					0 => array(
						'type_menu' => 'ACCUEIL',
						'libelle' => 'Home',
						'picto' => 'vide.gif',
						'sous_menus' => ''
					),
					1 => array(
						'type_menu' => 'PROFIL',
						'libelle' => 'Events',
						'picto' => 'vide.gif',
						'preferences' => array('clef_profil' => '46'),
						'sous_menus' => ''
					),
					2 => array(
						'type_menu' => 'URL',
						'libelle' => 'Link to a site',
						'picto' => 'vide.gif',
						'preferences' => array(
							'url' => 'http://google.fr',
							'target' => '0'
						),
						'sous_menus' => ''
					),
					3 => array(
						'type_menu' => 'MENU',
						'libelle' => 'Utilities',
						'picto' => 'vide.gif',
						'sous_menus' => array(
							0 => array(
								'type_menu' => 'AVIS',
								'libelle' => 'Latest critics',
								'picto' => 'vide.gif'
							),
							1 => array(
								'type_menu' => 'LAST_NEWS',
								'libelle' => 'Latest news',
								'picto' => 'vide.gif'
							),
							2 => array(
								'type_menu' => 'ALBUM',
								'libelle' => 'Photo album',
								'picto' => 'vide.gif'
							),
						),
					),
				),
			),
			2 => array(
				'libelle' => 'What\'s up?',
				'picto' => 'vide.gif',
				'menus' => array(
					array(
						'type_menu' => 'MENU',
						'libelle' => 'Adults books',
						'picto' => 'vide.gif',
						'sous_menus' => array(
							array(
								'type_menu' => 'CATALOGUE',
								'libelle' => 'Fictions',
								'picto' => 'vide.gif',
								'preferences' => array(
									'titre' => 'Ours latest adults fictions',
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
									'titre' => 'Our latests polars',
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

	public static function createChatenayAcceuilTranslatedConfiguration() {
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
										'titre' => 'How to come',
										'Valider' => 'Valider',
								)
						),
						3 => array(
								'division' => '1',
								'type_module' => 'SITO',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Sites library',
										'Valider' => 'Valider',
								)
						),
						4 => array(
								'division' => '2',
								'type_module' => 'SITO',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Sites library',
										'Valider' => 'Valider',
								)
						),
						5 => array(
								'division' => '1',
								'type_module' => 'RECH_SIMPLE',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Search',
										'message' => 'To search please enter search terms',
										'exemple' => 'For example : toto at the beach, apple, harry potter',
										'Valider' => 'Valider',
								)
						),
						6 => array(
								'division' => '1',
								'type_module' => 'CARTE_ZONES',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Rechercher une bibliothèque',
										'message_carte' => 'Under the map message',
										'Valider' => 'Valider',
								)
						),
						7 => array(
								'division' => '1',
								'type_module' => 'LOGIN',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Se connecter',
										'identifiant' => 'Login',
										'mot_de_passe' => 'Password',
										'Valider' => 'Valider',
								)
						),
				)
		);
	}

}

?>