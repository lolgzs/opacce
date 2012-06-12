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

class Class_Systeme_ModulesAccueil extends Class_Systeme_ModulesAbstract {
	const MODULES_KEY = 'modules';
	const PREFERENCES_KEY = 'preferences';

	const GROUP_INFO = 'INFO';
	const GROUP_RECH = 'RECH';
	const GROUP_SITE = 'SITE';

	/**
	 * @var array
	 */
	protected $_groupes = array(
		self::GROUP_INFO => "Modules d'informations",
		self::GROUP_RECH => "Modules de recherches",
		self::GROUP_SITE => "Modules niveau site"
	);

	/**
	 * @var array
	 */
	private static $_modules;

	/**
	 * @var Class_Systeme_ModulesAccueil
	 */	
	private static $_instance;


	public static function getInstance() {
		if (!isset(self::$_instance))
			self::$_instance = new self();
		return self::$_instance;
	}


	/** 
	 * @param string $code
	 * @return Class_Systeme_ModulesAccueil_Null or subclass
	 */
	public static function moduleByCode($code) {
		return self::getInstance()->getModuleByCode($code);
	}


	/** 
	 * @param string $code
	 * @return Class_Systeme_ModulesAccueil_Null or subclass
	 */
	public function getModuleByCode($code) {
		$modules = self::getModules();
		if (array_key_exists((string)$code, $modules)) {
			return $modules[(string)$code];
		}

		return new Class_Systeme_ModulesAccueil_Null;
	}


	/**
	 * @return array
	 */
	public function getValeursParDefaut($type) {
		return $this->getModuleByCode($type)->getDefaultValues();
	}


	public function acceptVisitor($visitor) {
		if (!isset($this->_data[self::MODULES_KEY]))
			return;

		foreach ($this->_data[self::MODULES_KEY] as $module) {
			if (isset($module[self::PREFERENCES_KEY])) {
				$visitor->visitPreference($module[self::PREFERENCES_KEY]);
			}
		}
	}


	/** @return array */
	public static function getModules() {
		if (null === self::$_modules) {
			self::$_modules = array('NEWS' => new Class_Systeme_ModulesAccueil_News,
															'CRITIQUES' => new Class_Systeme_ModulesAccueil_Critiques,
															'CALENDAR' => new Class_Systeme_ModulesAccueil_Calendrier,
															'RSS' => new Class_Systeme_ModulesAccueil_Rss,
															'SITO' => new Class_Systeme_ModulesAccueil_Sitotheque,
															'RECH_SIMPLE' => new Class_Systeme_ModulesAccueil_RechercheSimple,
															'RECH_GUIDEE' => new Class_Systeme_ModulesAccueil_RechercheGuidee,
															'TAGS' => new Class_Systeme_ModulesAccueil_Tags,
															//'CATALOGUE' => new Class_Systeme_ModulesAccueil_Catalogue,
															'KIOSQUE' => new Class_Systeme_ModulesAccueil_Kiosque,
															'MENU_VERTICAL' => new Class_Systeme_ModulesAccueil_MenuVertical,
															'CARTE_ZONES' => new Class_Systeme_ModulesAccueil_CarteZones,
															'LOGIN' => new Class_Systeme_ModulesAccueil_Login,
															'CONTENEUR_DEUX_COLONNES' => new Class_Systeme_ModulesAccueil_ConteneurDeuxColonnes,
															'COMPTEURS' => new Class_Systeme_ModulesAccueil_Compteurs,
															'LANGUE' => new Class_Systeme_ModulesAccueil_Langue,
															'BIB_NUMERIQUE' => new Class_Systeme_ModulesAccueil_BibliothequeNumerique);
			}

		return self::$_modules;
	}
}