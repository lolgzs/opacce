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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Variables globales admin
//////////////////////////////////////////////////////////////////////////////////////////

class Class_AdminVar extends Storm_Model_Abstract {
	protected $_table_name = 'bib_admin_var';
	protected $_table_primary = 'CLEF';

	/** @var array */
	protected static $_knownVars = array(
		'AVIS_MAX_SAISIE',
		'AVIS_MIN_SAISIE',
		'BLOG_MAX_NB_CARAC',
		'NB_AFFICH_AVIS_PAR_AUTEUR',
		'CLEF_GOOGLE_MAP',
		'MODO_AVIS',
		'MODO_AVIS_BIBLIO',
		'AVIS_BIB_SEULEMENT',
		'MODO_BLOG',
		'REGISTER_OK',
		'RESA_CONDITION',
		'SITE_OK',
		'ID_BIBLIOSURF',
		'GOOGLE_ANALYTICS',
		'ID_READ_SPEAKER',
		'BLUGA_API_KEY',
		'AIDE_FICHE_ABONNE',
		'INTERDIRE_ENREG_UTIL',
		'LANGUES',
		'CACHE_ACTIF',
		'WORKFLOW',
		'BIBNUM',
		'FORMATIONS',
		'PCDM4_LIB',
		'DEWEY_LIB',
		'VODECLIC_KEY',
		'VODECLIC_ID',
		'OAI_SERVER'
	);


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public static function get($name) {
		$var = self::getLoader()->find($name);
		if ($var == null)
			return null;
		return $var->getValeur();
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public static function set($name, $value) {
		if (null === ($instance = self::getLoader()->find($name))) {
			$instance = self::getLoader()->newInstance()->setClef($name);
		}

		$instance
			->setValeur($value)
			->save();

		// très particulier à cette table qui n'a pas de primary autoincrément
		// si on vient de la créer, last insertid est vide
		// donc ici on reprend le champ clef
		$instance->setId($instance->getClef());

		return $instance;
	}

	public static function getDefaultLanguage() {
		return 'fr';
	}

	public static function getLangues() {
		if (!$langues_value = self::get('LANGUES'))
			return array();

		$langues = array(self::getDefaultLanguage());

		foreach(explode(';', strtolower($langues_value)) as $langue)
			$langues []= trim($langue);
		return array_unique(array_filter($langues));
	}


	public static function getLanguesWithoutDefault() {
		return array_diff(self::getLangues(), array(self::getDefaultLanguage()));
	}

	/**
	 * @return bool
	 */
	public static function isTranslationEnabled() {
		return count(self::getLangues()) > 1;
	}


	/**
	 * @return bool
	 */
	public static function isModuleEnabled($name) {
		if (!$value = self::get($name)) {
			return false;
		}

		return (1 == (int)$value);
	}


	/**
	 * @return bool
	 */
	public static function isWorkflowEnabled() {
		return self::isModuleEnabled('WORKFLOW');
	}


	/**
	 * @return bool
	 */
	public static function isBibNumEnabled() {
		return self::isModuleEnabled('BIBNUM');
	}


	/**
	 * @return bool
	 */
	public static function isFormationEnabled() {
		return self::isModuleEnabled('FORMATIONS');
	}


	/**
	 * @return bool
	 */
	public static function isVodeclicEnabled() {
		return ('' != self::get('VODECLIC_KEY'));
	}


	/**
	 * @return bool
	 */
	public static function isOAIServerEnabled() {
		return self::isModuleEnabled('OAI_SERVER');
	}


	/**
	 * @return array
	 */
	public static function getKnownVars() {
		return self::$_knownVars;
	}


	/** @return bool */
	public static function isCacheEnabled() {
		return self::isModuleEnabled('CACHE_ACTIF');
	}
}