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
abstract class OpacIndexControllerTranslationTestCase extends AbstractControllerTestCase {
	/**
	 * @param Class_Profil $profil
	 */
	protected function _initProfilHook($profil) {
		$translatorMock = $this->getMockBuilder('Class_I18nTranslator')
												->disableOriginalConstructor()
												->getMock()
												;

		$translatorMock->expects($this->atLeastOnce())
										->method('translate')
										->will($this->returnCallback(array('OpacIndexControllerTranslationFixtures', 'simulateEnglishTranslate')))
										;

		$profil->setTranslator(new Class_Profil_I18nTranslator($translatorMock));

	}
}

class OpacIndexControllerMenuTranslationTest extends OpacIndexControllerTranslationTestCase {
	/**
	 * @param Class_Profil $profil
	 */
	protected function _initProfilHook($profil) {
		parent::_initProfilHook($profil);

		$profil->setMenuHautOn(1)
						->setCfgMenus(OpacIndexControllerTranslationFixtures::createChatenayMenusConfiguration());

	}


	/** @test */
	public function accueilShouldBeHomeWithEnglishTranslator() {
		$this->dispatch('/');
		$this->assertQueryContentContains('div#menu_horizontal li a', 'Home');
	}

	/** @test */
	public function animationsShouldBeEventsWithEnglishTranslator() {
		$this->dispatch('/');
		$this->assertQueryContentContains('div#menu_horizontal li a', 'Events');
	}

}

class OpacIndexControllerAccueilTranslationTest extends OpacIndexControllerTranslationTestCase {
	/**
	 * @param Class_Profil $profil
	 */
	protected function _initProfilHook($profil) {
		parent::_initProfilHook($profil);

		$profil->setCfgAccueil(OpacIndexControllerTranslationFixtures::createChatenayAcceuilConfiguration());

	}

	/** @test */
	public function rechercherShouldBeSearchWithEnglishTranslator() {
		$this->dispatch('/');
		$this->assertQueryContentContains('div', 'Search');
	}

	/** @test */
	public function trouverDesResultatsShouldBeToFindResultWithEnglishTranslator() {
		$this->dispatch('/');
		$this->assertQueryContentContains('label', 'To find results enter search terms');
	}

	/** @test */
	public function parExempleShouldBeForExampleWithEnglishTranslator() {
		$this->dispatch('/');
		$this->assertQueryContentContains('div', 'for example : toto at the beach, apple, harry potter');
	}
}


class OpacIndexControllerTranslationFixtures {
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
						'preferences' => array('clef_profil' => '46'),
						'sous_menus' => ''
					),
					1 => array(
						'type_menu' => 'PROFIL',
						'libelle' => 'Animations',
						'picto' => 'vide.gif',
						'preferences' => array('clef_profil' => '46'),
						'sous_menus' => ''
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
								'type_module' => 'RECH_SIMPLE',
								'preferences' => array(
										'boite' => 'boite_neutre',
										'titre' => 'Rechercher',
										'message' => 'Pour trouver des résultats, saisissez des termes',
										'exemple' => 'par exemple : toto à la plage, pomme, harry potter',
										'largeur' => 200,
										'select_doc' => null,
										'select_bib' => null,
										'recherche_avancee' => null,
								)
						),
				)
		);
	}

	/**
	 * @param string $value
	 * @return string
	 */
	public static function simulateEnglishTranslate($value) {
		$dictionary = array(
			'Accueil' => 'Home',
			'Animations' => 'Events',
			'Rechercher' => 'Search',
			'Pour trouver des résultats, saisissez des termes' => 'To find results enter search terms',
			'par exemple : toto à la plage, pomme, harry potter' => 'for example : toto at the beach, apple, harry potter',
		);

		if (array_key_exists((string)$value, $dictionary)) {
			return $dictionary[$value];
		}

		return $value;
	}
}
?>