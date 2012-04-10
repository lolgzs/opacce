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
class Class_Profil_I18n {
	const VISITABLE_CLASS_KEY = 'visitable';
	const CONFIG_GETTER_KEY = 'configGetter';

	/** @var Array */
	protected static $_knownStringKeys = array(
		'titre', 'message', 'exemple', 'libelle',
		'message_carte', 'identifiant', 'mot_de_passe',
		'facettes_message', 'tags_message', 'barre_nav',
	);

	/**
	 * @var array
	 */
	protected $_knownConfigurations = array(
		'Accueil' => array(
			self::CONFIG_GETTER_KEY => 'Accueil',
			self::VISITABLE_CLASS_KEY => 'Class_Systeme_ModulesAccueil',
		),
		'Menus' => array(
			self::CONFIG_GETTER_KEY => 'Menus',
			self::VISITABLE_CLASS_KEY => 'Class_Systeme_ModulesMenu',
		),
		'Modules' => array(
			self::CONFIG_GETTER_KEY => 'Modules',
			self::VISITABLE_CLASS_KEY => 'Class_Systeme_ModulesAppli',
		),
	);

	/** @var Class_Profil */
	protected $_model;

	public function __construct($model = null) {
		$this->_model = $model;
	}

	/**
	 * @param array $configurations
	 */
	public function setKnownConfigurations(array $configurations) {
		$this->_knownConfigurations = $configurations;
	}

	/**
	 * @return Class_Profil_I18nStringExtractor
	 */
	public function setModel($model) {
		$this->_model = $model;
		return $this;
	}

	/**
	 * @param array $data
	 */
	public function visitArray(array $data) {
		foreach ($data as $k => $v) {
			if (in_array($k, self::$_knownStringKeys))
				$this->_visitArrayAction($v);

		}

	}

	/**
	 * @param string $value
	 */
	protected function _visitArrayAction($value) {}

	/**
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, $arguments) {
		// si appelé pour visite, raccourci
		if ('visit' == substr($name, 0, 5)) {
			$this->visitArray($arguments[0]);

		}

	}
}
?>