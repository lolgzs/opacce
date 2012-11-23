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

class ProfilLoader extends Storm_Model_Loader {
	public function findAllByZoneAndBib($id_zone=0,$id_bib=0) {
		$conditions = array('order' => 'libelle');

		$where = '';
		if($id_zone and $id_zone !="ALL") {
			if($id_zone=="PORTAIL")
				$conditions["ID_SITE"] = 0;
			else {
				$conditions["ID_SITE"] = array("select ID_SITE from bib_c_site where ID_ZONE=$id_zone");
			}
		}


		if($id_bib and $id_bib!="ALL") {
			if($id_bib=="PORTAIL")
				$id_bib=0;
			$conditions["ID_SITE"] = $id_bib;
		}

		return	$this->findAllBy($conditions);
	}


	public function getPortail() {
		return $this->find(1);
	}
}




class Class_Profil extends Storm_Model_Abstract {
	use Trait_StaticFileWriter;

  const DIV_BANNIERE = 4;
	protected static $_default_translator = null;

  protected $_loader_class = 'ProfilLoader';
  protected $_table_name = 'bib_admin_profil';
  protected $_table_primary = 'ID_PROFIL';

  protected $_belongs_to = ['bib' => ['model' => 'Class_Bib',
																			'referenced_in' => 'id_site'],

														'parent_profil' => ['model' => 'Class_Profil',
																								'referenced_in' => 'parent_id']];

  protected $_has_many  = ['sub_profils' => ['model' => 'Class_Profil',
																						 'role' => 'parent_profil',
																						 'dependents' => 'delete']];

  protected static $_current_profil;
  protected static $DEFAULT_VALUES, $CFG_SITE_KEYS, $FORWARDED_ATTRIBUTES;

	/**
	 * liste des bannieres
	 * @var array
	 */
	protected $_all_header_img;

	/**
	 * @var Class_Profil_I18nTranslator
	 */
	protected $_translator;


	/**
	 * @return Class_Profil
	 */
	public static function getCurrentProfil() {
		if (!isset(self::$_current_profil)) {
			if (!$id_profil = Zend_Registry::get('session')->id_profil)
				$id_profil = 1;
			self::$_current_profil = self::getLoader()->find($id_profil);
		}
		return self::$_current_profil;
	}



	public static function goBackToPreviousProfil() {
		$session = Zend_Registry::get('session');
		$session->id_profil = $session->previous_id_profil;
	}


	/**
	 * @param Class_Profil $profil
	 */
	public static function setCurrentProfil($profil) {
		return self::$_current_profil = $profil;
	}


	/**
	 * @return array
	 */
	public static function getCfgSiteKeys() {
		if (!isset(self::$CFG_SITE_KEYS))
			self::$CFG_SITE_KEYS = array( 'accessibilite_on',
																		'barre_nav_on',
																		'header_css',
																		'header_js',
																		'header_img',
																		'header_img_cycle',
																		'largeur_division2',
																		'largeur_division3',
																		'liens_sortants_off',
																		'marge_division1',
																		'marge_division2',
																		'marge_division3',
																		'menu_haut_on',
																		'largeur_division1',
																		'largeur_site',
																		'nb_divisions',
																		'hauteur_banniere',
																		'couleur_lien_bandeau',
																		'couleur_texte_bandeau',
																		'access_level',
																		'favicon',
																		'logo_gauche_img',
																		'logo_gauche_link',
																		'logo_droite_img',
																		'logo_droite_link',
																		'header_social_network',
																		'mail_suggestion_achat');
	  return self::$CFG_SITE_KEYS;

	}


	/**
	 * @return array
	 */
	public static function getDefaultValues() {
		if (!isset(self::$DEFAULT_VALUES))
			self::$DEFAULT_VALUES =
				['cfg_site' => '',
				 'cfg_accueil' => ZendAfi_Filters_Serialize::serialize(['modules' => []]),
				 'cfg_menus' => ZendAfi_Filters_Serialize::serialize(['H' => ['libelle' => 'Menu horizontal',
																																			'picto' => 'vide.gif',
																																			'menus' => []],
																															'V' => ['libelle' => 'Menu vertical',
																																			'picto' => 'vide.gif',
																																			'menus' => []]]),
				 'cfg_modules' => '',
				 'cfg_notice' => ZendAfi_Filters_Serialize::serialize(['exemplaires' => ['grouper' => 0,
																																								 'annexe' => 0,
																																								 'bib' => 1,
																																								 'section' => 0,
																																								 'emplacement' => 0,
																																								 'localisation' => 1,
																																								 'plan' => 1,
																																								 'resa' => 1,
																																								 'dispo' => 1,
																																								 'date_retour' => 0],
																															 'en_pret' => 'En prêt']),
				 'hauteur_banniere' => 100,
				 'mail_site' => '',
				 'mail_suggestion_achat' => '',
				 'skin' => 'original',
				 'largeur_site' => 1000,
				 'nb_divisions' => 3,
				 'largeur_division1' => 250,
				 'marge_division1' => 10,
				 'largeur_division2' => 550,
				 'marge_division2' => 10,
				 'largeur_division3' => 200,
				 'marge_division3' => 10,
				 'menu_haut_on' => true,
				 'barre_nav_on' => true,
				 'ref_description' => '',
				 'ref_tags' => '',
				 'id_site' => 0,
				 'sel_section' => '',
				 'sel_annexe' => '',
				 'sel_type_doc' => '',
				 'header_css' => null,
				 'header_js' => null,
				 'header_img' => null,
				 'header_img_cycle' => false,
				 'liens_sortants_off' => false,
				 'titre_site' => '',
				 'libelle' => '** nouveau profil **',
				 'commentaire' => '',
				 'browser' => 'opac',
				 'accessibilite_on' => true,
				 'couleur_lien_bandeau' => '',
				 'couleur_texte_bandeau' => '',
				 'access_level' => -1,
				 'parent_id' => 0, 
				 'favicon' => '',
				 'logo_gauche_img' => '',
				 'logo_gauche_link' => '',
				 'logo_droite_img' => '',
				 'logo_droite_link' => '',
				 'header_social_network' => false];
	  return self::$DEFAULT_VALUES;
  }


	/**
	 * @param translator Class_Profil_I18nTranslator
	 */
	public static function setDefaultTranslator($translator) {
		self::$_default_translator = $translator;
	}


	/**
	 * @return Class_Profil_I18nTranslator
	 */
	public static function getDefaultTranslator() {
		return self::$_default_translator;
	}


	/**
	 * @return array
	 */
	public static function getAttributesForwardedToParent() {
		if (isset(self::$FORWARDED_ATTRIBUTES))
			return self::$FORWARDED_ATTRIBUTES;

		$all_attributes = array_keys(self::getDefaultValues());
		$not_forwarded_attributes = array('id',
																			'parent_id',
																			'parent_profil',
																			'libelle',
																			'cfg_accueil',
																			'sub_profils');
		self::$FORWARDED_ATTRIBUTES = array_diff($all_attributes,
																						 $not_forwarded_attributes);
		return self::$FORWARDED_ATTRIBUTES;
	}


	/**
	 * @param string $field
	 * @return bool
	 */
	public function shouldForwardAttributeToParent($field) {
		return
			in_array($field, self::getAttributesForwardedToParent())
			and $this->hasParentProfil();
	}

	/**
	 * @param Class_I18nTranslator $translator
	 * @return Class_Profil
	 */
	public function setTranslator($translator) {
		$this->_translator = $translator;
		return $this;
	}


	/**
	 * @return String
	 */
	public function getLocale() {
		return Zend_Registry::getInstance()->get('locale')->getLanguage();
	}


	/**
	 * @return Class_Profil_I18nTranslator
	 */
	public function getTranslator() {
		if ((null === $this->_translator) and (null === $this->_translator=self::getDefaultTranslator())){
			$locale = $this->getLocale();
			$this->_translator = Class_Profil_I18nTranslator::newForLocale($locale);
		}

		return $this->_translator;
	}


	/**
	 * @return string
	 */
	public function getPathTheme()	{
		$path='/public/'.$this->getBrowser().'/skins/'.$this->getSkin().'/';
		if(!file_exists('.'.$path))
			$path='/public/'.$this->getBrowser().'/skins/original/';
		return $path;
	}


	/**
	 * @return string
	 */
	public function getPathTemplates() {
		return '.'.$this->getPathTheme().'templates/boites/';
	}


	/**
	 * @param int $id_module
	 * @param string $type_module
	 * @return array
	 */
	public function getOrCreateConfigAccueil($id_module, $type_module) {
		$cfg_accueil = $this->getCfgAccueilAsArray();

		if (array_isset($id_module, $cfg_accueil['modules']))
			$module = $cfg_accueil['modules'][$id_module];
		else
			$module = array('preferences' => array());

		$data = array();
		if (array_isset('preferences', $module))
			$data = $module['preferences'];

		$default_values = Class_Systeme_ModulesAccueil::getInstance()->getValeursParDefaut($type_module);
		return array_merge($default_values, $data);
	}


	/**
	 * @param int $id_module
	 * @return string css path
	 */
	public function getHeaderCssIE($ieversion) {
		$parts = pathinfo($this->getHeaderCss());
		if (!isset($parts['dirname']))
			return '';

		return 'ie'.$ieversion.'_'.$parts['basename'];
	}


	/**
	 * @param int $id_module
	 * @param string $module_config
	 * @return Class_Profil
	 */
	public function updateModuleConfigAccueil($id_module, $module_config) {
		$cfg_accueil=$this->getCfgAccueilAsArray();
		$cfg_accueil['modules'][$id_module] = $module_config;
		$this->setCfgAccueil($cfg_accueil);
		return $this;
	}


	/**
	 * @param string $controller
	 * @param string $action
	 * @param int $id_module
	 * @return string
	 */
	public function urlForModule($controller, $action, $id_module) {
		$url = 'http://' . $_SERVER['SERVER_NAME'] . BASE_URL .
			     '/'.$controller.'/'.$action.'?'.
						http_build_query(
												array(
															'id_module' => $id_module,
															'id_profil' => $this->getId(),
															'language' => $this->getLocale()));
		return htmlspecialchars($url);
	}


	/**
	 * @param int $start_id
	 * @return int
	 */
	public function createNewModuleAccueilId($start_id = 1){
		$new_id = $start_id;
		while ($this->getModuleAccueilConfig($new_id) != null) $new_id++;

		// réserve l'id pour ne pas redonner 2 fois le même
		$cfg_accueil = $this->getCfgAccueilAsArray();
		$cfg_accueil['modules'][$new_id] = array('preferences' => array(
																								 'id_module' => $new_id));
		$this->setCfgAccueil($cfg_accueil);

		return $new_id;
	}


	/**
	 * @param int $id_module
	 * @return array | null
	 */
	public function getModuleAccueilConfig($id_module){
		$cls_module = Class_Systeme_ModulesAccueil::getInstance();

		$cfg_accueil = $this->getCfgAccueilAsArray();
		$modules_config = $cfg_accueil['modules'];

		if (array_key_exists($id_module, $modules_config)) {
			if (!is_array($modules_config[$id_module]))
				$modules_config[$id_module] = array();

			$module = array_merge(array('type_module' => null,
																	'preferences' => array()),
														$modules_config[$id_module]);

			$default_prefs = $cls_module->getValeursParDefaut($module['type_module']);

			if (!array_isset('preferences', $module))
				$module['preferences'] = $default_prefs;
			else
				$module['preferences'] = array_merge($default_prefs, $module['preferences']);
		
			return $module;
		}
		return null;
	}


	/**
	 * @param int $id_module
	 * @return array
	 */
	public function getModuleAccueilPreferences($id_module){
		$config = $this->getModuleAccueilConfig($id_module);
		if ($config)
			return $config['preferences'];
		return array();
	}


	/**
	 * @return string
	 */
	public function getHeaderImg() {
		$path = $this->_get('header_img');
		if (!$path) {
			if (file_exists(PATH_SKIN.'/images/site/banniere.png'))
				$path = URL_IMG.'site/banniere.png';
			else
				$path = URL_IMG.'site/banniere.jpg';
		}
		return $path;
	}



	/** 
	 * @param images array
	 * @return Class_Profil
	 */
	function setAllHeaderImg($images) {
		$this->_all_header_img = $images;
		return $this;
	}


	/** 
	 * @return array
	 */
	public function getAllHeaderImg() {
		if (!$this->getHeaderImgCycle())
			return array($this->getHeaderImg());

		if (isset($this->_all_header_img))
			return $this->_all_header_img;

		$images = array();
		$handle = opendir(USERFILESPATH.'/bannieres');
		while (false !== ($file = readdir($handle))) { 
			if (false != strpos(strtolower($file), '.jpg')) {
				$images []= USERFILESURL.'bannieres/'.$file;
			}
		}
		closedir($handle);
		return $this->_all_header_img = $images;
	}


	/**
	 * @param string $data
	 * @return array
	 */
	protected function _unserialize($data) {
		try {
			$unserialized = ZendAfi_Filters_Serialize::unserialize($data);
		} catch (Exception $e) {
			$unserialized = array();
		}

		if (!$unserialized) return array();
		return $unserialized;
	}


	/**
	 * @return array
	 */
	public function getDefaultCfgMenus() {
		return $this->_unserialize(self::getDefaultValue('cfg_menus'));
	}


	/**
	 * @param string $name
	 * @return array
	 */
	public function _getRawCfgAsArrayNamed($name) {
		$name = (string)$name;
		$cfg = $this->_unserialize($this->{'getCfg' . $name}());

		if (empty($cfg) && method_exists($this, 'getDefaultCfg' . $name)) {
			$cfg = $this->{'getDefaultCfg' . $name}();
		}

		if (('Menus' == $name)&& (!array_key_exists('H', $cfg))) {
			$cfg['H'] = array_at('H', $this->{'getDefaultCfg' . $name}());
		}

		return $cfg;
	}


	/**
	 * @param string $name
	 * @return array
	 */
	protected function _getCfgAsArrayNamed($name) {
		$cfg = $this->_getRawCfgAsArrayNamed($name);
		return $this->getTranslator()->translate($cfg, $name);
	}

	
	/**
	 * @return array
	 */
	public function getCfgMenusAsArray() {
		return $this->_getCfgAsArrayNamed('Menus');
	}


	/**
	 * @return array
	 */
	public function getCfgSiteAsArray() {
		return $this->_getCfgAsArrayNamed('Site');
	}


	/**
	 * @return array
	 */
	public function getCfgAccueilAsArray() {
		return $this->_getCfgAsArrayNamed('Accueil');
	}


	/**
	 * @return array
	 */
	public function getCfgNoticeAsArray() {
		return $this->_getCfgAsArrayNamed('Notice');
	}


	/**
	 * @return array
	 */
	public function getCfgModulesAsArray() {
		return $this->_getCfgAsArrayNamed('Modules');
	}


	/**
	 * @param string $controller
	 * @param string $action
	 * @return array
	 */
	public function getCfgModulesPreferences($controller, $action, $subaction = '') {
		$cfg_modules = $this->getCfgModulesAsArray();
		
		$cls_module = new Class_Systeme_ModulesAppli();
		$preferences_defaut = $cls_module->getValeursParDefaut($controller,$action);

		if (!array_key_exists($controller, $cfg_modules) || 
			  !array_key_exists($action.$subaction, $cfg_modules[$controller]))
			return $preferences_defaut;
		else
			return array_merge($preferences_defaut,
												 $cfg_modules[$controller][$action.$subaction]);
	}


	/**
	 * @param string $controller
	 * @param string $action
	 * @param string $pref
	 * @return mixed
	 */
	public function getModulePreference($controller, $action, $pref) {
		$preferences = $this->getCfgModulesPreferences($controller, $action);
		return $preferences[$pref];
	}
	

	/**
	 *
	 * @param string $cfg_name
	 * @param mixed $string_or_array
	 * @return Class_Profil
	 */
	private function _setCfgNamed($cfg_name, $string_or_array) {
		if (is_array($string_or_array))
			$cfg = ZendAfi_Filters_Serialize::serialize($string_or_array);
		else
			$cfg = $string_or_array;
		return $this->_set($cfg_name, $cfg);
	}


	/**
	 * @param mixed $string_or_array
	 * @return Class_Profil
	 */
	public function setCfgAccueil($string_or_array) {
		return $this->_setCfgNamed('cfg_accueil', $string_or_array);
	}


	/**
	 * @param mixed $string_or_array
	 * @return Class_Profil
	 */
	public function setCfgMenus($string_or_array) {
		return $this->_setCfgNamed('cfg_menus', $string_or_array);
	}


	/**
	 * @param mixed $string_or_array
	 * @return Class_Profil
	 */
	public function setCfgNotice($string_or_array) {
		return $this->_setCfgNamed('cfg_notice', $string_or_array);
	}


	/**
	 * @param mixed $string_or_array
	 * @return Class_Profil
	 */
	public function setCfgModules($string_or_array) {
		return $this->_setCfgNamed('cfg_modules', $string_or_array);
	}


	/**
	 * @param mixed $string_or_array
	 * @return Class_Profil
	 */
	public function setCfgSite($string_or_array) {
		return $this->_setCfgNamed('cfg_site', $string_or_array);
	}


	/**
	 * @return string
	 */
	public function getTitreSite() {
		if ($this->hasParentProfil())
			return $this->getParentProfil()->getTitreSite().' - '.$this->getLibelle();

		return $this->getLibelle('libelle');
	}


	/**
	 * @return array
	 */
	public function toArray() {
		$attributes = parent::toArray();
		// Parce que toArray appelle getTitreSite qui prends
    // getLibelle s'il y en a un et donc risque d'écraser
		$attributes['titre_site'] = $this->_get('titre_site');
		return $attributes;
	}


	/**
	 * @param string $param
	 * @param mixed $value
	 * @return Class_Profil
	 */
	public function setCfgSiteParam($param, $value) {
		$cfg_site = $this->getCfgSiteAsArray();
		$cfg_site[$param] = $value;
		return $this->setCfgSite(ZendAfi_Filters_Serialize::serialize($cfg_site));
	}


	/**
	 * @param string $param
	 * @return bool
	 */
	public function hasDefaultValue($param) {
		return array_key_exists($param, self::getDefaultValues());
	}


	/**
	 * @param string $param
	 * @return mixed
	 */
	public function getDefaultValue($param) {
		$default_values = self::getDefaultValues();
		return $default_values[$param];
	}


	/**
	 * @param string $param
	 * @return mixed
	 */
	public function getCfgSiteParam($param) {
		$cfg_site = $this->getCfgSiteAsArray();
		if (!array_key_exists($param, $cfg_site))
			return $this->getDefaultValue($param);
		return $cfg_site[$param];
	}


	/**
	 * @return bool
	 */
	public function isTelephone() {
		return 'telephone' == $this->getBrowser();
	}


	/**
	 * @return Class_Profil
	 */
	public function beTelephone() {
		return $this->setBrowser('telephone');
	}


	/**
	 * @return array
	 */
	function getAvailableSkins()	{
		// Parcourir le dossier des skins opac
		$scanlisting = scandir('./public/opac/skins');
		$availableSkins = array();

		foreach($scanlisting as $key => $value)
			if (is_dir("./public/opac/skins/$value") and $value[0] != '.')
				$availableSkins[$value] = $value;

		return $availableSkins;

	}


	/**
	 * Si un attribut n'est pas trouvé, regarde s'il n'est pas dans cfgSite.
	 * Permet de faire abstraction de CfgSite quand on va chercher les parametres
	 * d'un profil.
	 * ex: $profil->getLargeurDivision1();
	 * au lieu de
	 * $cfg = $profil->getCfgSite();
	 * $cfg['largeur_division1']
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function _get($field) {
		// Prends l'attribut parent s'il y en a un
		if ($this->shouldForwardAttributeToParent($field))
			return $this->getParentProfil()->_get($field);

		if ($this->isAttributeExists($field))
				return parent::_get($field);

		if ($field !== 'cfg_site' and
				array_key_exists($field, $this->_getRawCfgAsArrayNamed('Site')))
			return $this->getCfgSiteParam($field);

		if ($this->hasDefaultValue($field))
			return $this->getDefaultValue($field);
		throw new Exception('Tried to call unknown method Profil::get' . $field);
	}


	/**
	 * @param string $field
	 * @param mixed $value
	 * @return Class_Profil
	 * @see _get
	 */
	public function _set($field, $value) {
		if ($this->shouldForwardAttributeToParent($field))
			return $this->getParentProfil()->_set($field, $value);


		if (in_array($field, self::getCfgSiteKeys())) {
			return $this->setCfgSiteParam($field, $value);
		}
		else
			return parent::_set($field, $value);
	}


	/**
	 * @return Class_Profil
	 */
	public function validate() {
		if ($this->getNbDivisions() < 3)	$this->setLargeurDivision3(0);
		if ($this->getNbDivisions() < 2)	$this->setLargeurDivision2(0);

		$this->check($this->getLibelle(), 'Le libellé est obligatoire.');
		$this->check($this->getLargeurSite() >= 800 and $this->getLargeurSite() <= 2000,
								 'La largeur du site doit être comprise entre 800 et 2000 pixels.');
		$this->check($this->getLargeurDivision1() +
								 $this->getLargeurDivision2() +
								 $this->getLargeurDivision3() <= $this->getLargeurSite(),
								 'La somme des largeurs des divisions ne doit pas excéder la largeur du site.');

		$this->check($this->getLargeurDivision1(), 'Il manque la largeur de la division 1.');
		$this->check($this->getLargeurDivision2() or $this->getNbDivisions() < 2,
								 'Il manque la largeur de la division 2.');
		$this->check($this->getLargeurDivision3() or $this->getNbDivisions() < 3,
								 'Il manque la largeur de la division 3.');
		$this->check($this->getMargeDivision1() < 20
								 and $this->getMargeDivision2() < 20
								 and $this->getMargeDivision3() < 20,
								 'Une marge interne de division ne peut pas excéder 20 pixels.');


		$this->check($this->_isCSSColorValid($this->getCouleurTexteBandeau()),
								 'La couleur du texte bandeau doit être au format #001122');

		$this->check($this->_isCSSColorValid($this->getCouleurLienBandeau()),
								 'La couleur des liens du bandeau doit être au format #001122');

		$url_validate = new ZendAfi_Validate_Url();
		$this->check(!$this->getLogoGaucheLink() or $url_validate->isValid($this->getLogoGaucheLink()),
								 'Le lien pour le logo gauche n\'est pas valide');

		$this->check(!$this->getLogoDroiteLink() or $url_validate->isValid($this->getLogoDroiteLink()),
								 'Le lien pour le logo droite n\'est pas valide');
    return $this;
	}


	/**
	 * @param string $color
	 * @return bool
	 */
	protected function _isCSSColorValid($color) {
		return !$color or preg_match('/^#([A-F0-9]{3}|[A-F0-9]{6})$/i', $color);
	}


	/**
	 * @return string
	 */
	public function getStyleCss() {
			$css = '<style id="profil_stylesheet" type="text/css">';

			if ($hauteur_banniere = $this->getHauteurBanniere())
				$css .= 'div#banniere, div#header{height:'.$hauteur_banniere.'px}';

			if ($couleur_texte = $this->getCouleurTexteBandeau())
				$css .= 'div#header * {color:'.$couleur_texte.'} div#header form input {color: #000}';

			if ($couleur_lien = $this->getCouleurLienBandeau())
				$css .= 'div#header a, div#header a:visited {color:'.$couleur_lien.'}';

			$css .= '</style>';

			return $css;
	}


	/**
	 * @return array
	 */
	public function getAllAccessLevels() {
		$acl = new ZendAfi_Acl_AdminControllerRoles();
		$roles = $acl->getListeRoles();
		array_pop($roles);
		$roles['-1'] = 'public';
		ksort($roles);
		return $roles;
	}


	/**
	 * @return bool
	 */
	public function isPublic() {
		return (int)$this->getAccessLevel() === -1;
	}


	/**
	 * @param string $type_module
	 * @return array
	 */
	public function getDefautBoite($type_module) {
		$modules_accueil = new Class_Systeme_ModulesAccueil();
		return $modules_accueil->getValeursParDefaut($type_module);
	}


	/**
	 * @param string $type_module
	 * @return bool
	 */
	public function isTypeBoiteInBanniere($type_module) {
		$modules_banniere = $this->getBoitesDivision(self::DIV_BANNIERE);
		foreach ($modules_banniere as $module)  {
			if ($module['type_module'] == $type_module)
				return true;
		}
		return false;
	}


	/**
	 * @param int $division
	 * @return array
	 */
	public function getBoitesDivision($division) {
		if ($division == self::DIV_BANNIERE and $this->hasParentProfil())
			$cfg_accueil = $this->getParentProfil()->getCfgAccueilAsArray();
		else
			$cfg_accueil = $this->getCfgAccueilAsArray();

		$boites = array();
		foreach ($cfg_accueil['modules'] as $id => $module) {
			if (!$module) $module = array();
			$module = array_merge(array('type_module' => null,
																	'preferences' => array()),
														$module);

			$module_accueil = Class_Systeme_ModulesAccueil::moduleBycode($module['type_module']);
			if (!$module_accueil->isVisibleForProfil($this))
				continue;

			$module['preferences'] = array_merge($module_accueil->getDefaultValues(),	
																					 $module['preferences']);

			if (array_key_exists('division', $module) and $module['division'] == $division)
				$boites [$id]= $module;
		}

		return $boites;
	}


	/**
	 * @param bool $is_present
	 * @param string $type_module
	 * @return Class_Profil
	 */
	public function setBoiteOfTypeInBanniere($is_present, $type_module) {
		$cfg_accueil = $this->getCfgAccueilAsArray();

		if ($is_present) {
			$id = $this->createNewModuleAccueilId();

			$module = array('division' => self::DIV_BANNIERE,
											'type_module' => $type_module,
											'preferences' => $this->getDefautBoite($type_module));
			$module['preferences']['id_module'] = $id;
			$module['preferences']['boite'] = $type_module == 'LOGIN' ? 'boite_banniere_droite' : 'boite_banniere_gauche';
			$cfg_accueil['modules'][$id] = $module;

		} else {
			foreach ($cfg_accueil['modules'] as $index => $module)  {
				if ($module['division'] == self::DIV_BANNIERE and
						$module['type_module'] == $type_module)
					unset($cfg_accueil['modules'][$index]);
			}
		}

		return $this->setCfgAccueil($cfg_accueil);
	}


	/**
	 * @return bool
	 */
	public function getBoiteLoginInBanniere() {
		return $this->isTypeBoiteInBanniere('LOGIN');
	}


	/**
	 * @return bool
	 */
	public function getBoiteRechercheSimpleInBanniere() {
		return $this->isTypeBoiteInBanniere('RECH_SIMPLE');
	}


	/**
	 * @param bool $is_present
	 * @return Class_Profil
	 */
	public function setBoiteLoginInBanniere($is_present) {
		if ($is_present and $this->getBoiteLoginInBanniere())
			return $this;

		return $this->setBoiteOfTypeInBanniere($is_present, 'LOGIN');
	}


	/**
	 * @param bool $is_present
	 * @return Class_Profil
	 */
	public function setBoiteRechercheSimpleInBanniere($is_present) {
		if ($is_present and $this->getBoiteRechercheSimpleInBanniere())
			return $this;


		return $this->setBoiteOfTypeInBanniere($is_present, 'RECH_SIMPLE');
	}


	/**
	 * @param int $id_menu
	 * @return array
	 */
	public function getMenu($id_menu) {
		return array_at($id_menu, $this->getCfgMenusAsArray());
	}


	/**
	 * @param array $menu
	 * @return int
	 */
	public function addMenu($menu) {
		$cfg_menus = $this->getCfgMenusAsArray();
		$cfg_menus []= $menu;
		$last_index = array_last(array_keys($cfg_menus));

		$this->setCfgMenus($cfg_menus);

		return $last_index;
	}


	/**
	 * @return array
	 */
	public function getBoitesMenuVertical() {
		$boites_menu = array();
		$all_boites = array_at('modules', $this->getCfgAccueilAsArray());
		foreach ($all_boites as $id => $boite) {
			if ($boite['type_module'] == 'MENU_VERTICAL')
				$boites_menu[$id] = $boite;
		}
		return $boites_menu;
	}


	/**
	 * @return array
	 */
	public function getMenusInBoitesMenuVertical() {
		$menus = array();
		$boites_menu = $this->getBoitesMenuVertical();
		foreach ($boites_menu as $boite) {
			$id_menu = $boite['preferences']['menu'];
			$menus[$id_menu] = $this->getMenu($id_menu);
		}
		return $menus;
	}


	/**
	 * @return string
	 */
	public function getBibLibelle() {
		if ($bib = $this->getBib())
			return $bib->getLibelle();
		return '';
	}


	/**
	 * @return bool
	 */
	public function isInPortail() {
		return null === $this->getBib();
	}


	/**
	 * @return bool
	 */
	public function isPortail() {
		return 1 === $this->getId();
	}


	/**
	 * Créé une copie profil (non récursif)
	 * @return Class_Profil
	 */
	public function copy() {
		$copy = new Class_Profil();
		$attributes = $this->_attributes;
		unset($attributes['id']);
		unset($attributes['id_profil']);
		
		return $copy
			->updateAttributes($attributes)
			->setLibelle('** Nouveau Profil **');
	}


	/**
	 * Créé une copie du profil et de ses sous-pages
	 * @return Class_Profil
	 */
	public function deepCopy() {
		$clone = $this->copy()->setSubProfils([]);
		$pages = $this->getSubProfils();
		foreach($pages as $page)
			$clone->addSubProfil($page->copy()->setLibelle($page->getLibelle()));
			
		return $clone;
	}


	/**
	 * @param Class_Profil $new_parent
	 * @return Class_Profil
	 */
	public function _copyMenuForMigrationTo($new_parent) {
		if (!$this->hasParentProfil()) return $this;

		$cfg_accueil = $this->getCfgAccueilAsArray();

		$boites_menu = $this->getBoitesMenuVertical();
		$my_menus = $this->getMenusInBoitesMenuVertical();

		foreach($my_menus as $id_menu => $menu) {
			$menu['libelle'] = $this->getParentProfil()->getLibelle().':: '.$menu['libelle'];
			$new_id_menu = $new_parent->addMenu($menu);

			foreach ($boites_menu as $id_boite => $boite) {
				if ($boite['preferences']['menu'] == $id_menu) {
					$cfg_accueil['modules'][$id_boite]['preferences']['menu'] = $new_id_menu;
				}
			}
		}

		return $this->setCfgAccueil($cfg_accueil);
	}


	/**
	 * @param Class_Profil $new_parent
	 * @return Class_Profil
	 */
	public function setParentProfil($new_parent) {
		if ($new_parent == $this->getParentProfil())
			return $this;

		$this->_copyMenuForMigrationTo($new_parent);
		parent::_set('parent_profil', $new_parent);

		if ($this->hasParentProfil())
			$this->getParentProfil()->save();

		return $this;
	}


	/**
	 * @return array
	 */
	public function getSubProfils() {
		$sub_profils = [];
		foreach(parent::_get('sub_profils') as $profil) {
			$libelle = $profil->getLibelle();

			if (isset($sub_profils[$libelle])) {
				$i = 1;
				while (isset($sub_profils[$libelle.' ('.$i.')']))	$i++;
				$libelle = $libelle.' ('.$i.')';
			}

			$sub_profils[$libelle] = $profil;
		}

		ksort($sub_profils);
		return $sub_profils;
	}


	public function delete() {
		// on ne doit pas pouvoir supprimer le profil portail
		if (1 != $this->getId()) {
			parent::delete();
		}
	}


	public function getMailSiteOrPortail() {
		if ($this->isPortail())
			return $this->getMailSite();

		if ($this->hasMailSite())
			return $this->getMailSite();

		return $this->getLoader()->getPortail()->getMailSite();
	}


	public function getMailSuggestionAchatOrPortail() {
		if ($this->isPortail()) {
			if ($mail =$this->getMailSuggestionAchat())
				return $mail;
			return $this->getMailSite();
		}

		if ($this->hasMailSuggestionAchat())
			return $this->getMailSuggestionAchat();

		if ($mail = $this->getLoader()->getPortail()->getMailSuggestionAchat())
			return $mail;

		return $this->getMailSiteOrPortail();
	}


	protected function _getIdModuleAtDivPosInCfg($div, $pos, $cfg_accueil) {
		$id_module = 0;
		$i = 0;
		foreach($cfg_accueil['modules'] as $module_id => $module) {
			if ($module['division'] == $div) {
				if ($pos == $i) {
					$id_module = $module_id;
					break;
				}
				$i++;
			}
		}

		return $id_module;
	}


	public function moveModuleOldDivPosNewDivPos($old_div, $old_pos, $new_div, $new_pos) {
		$cfg_accueil = $this->getCfgAccueilAsArray();
		$id = $this->_getIdModuleAtDivPosInCfg($old_div, $old_pos, $cfg_accueil);
		$moved_module = $cfg_accueil['modules'][$id];
		$moved_module['division'] = $new_div;
		unset($cfg_accueil['modules'][$id]);

		$new_modules = array();
		$i = 0;
		foreach($cfg_accueil['modules'] as $module_id => $module) {
			$in_new_div = $module['division'] == $new_div;

			if (($i == $new_pos) && $in_new_div) 
				$new_modules[$id] = $moved_module;

			if ($in_new_div)
				$i++;

			$new_modules[$module_id] = $module;
		}

		if (!isset($new_modules[$id]))
			$new_modules[$id] = $moved_module;
			
		$cfg_accueil['modules'] = $new_modules;

		$this->setCfgAccueil($cfg_accueil);
	}


	public function writeHeaderCss($data) {
		if (!$header_css = $this->getHeaderCssPath()) 
			$header_css = USERFILESPATH.'/css/profil_'.$this->getId().'.css';

		$this->getFileWriter()->putContents($header_css, $data);		
		return $this->setHeaderCss(str_replace(USERFILESPATH.'/', USERFILESURL, $header_css));
	}

	
	public function getHeaderCssPath() {
		if ($header_css = $this->_get('header_css'))
			return USERFILESPATH.str_replace(USERFILESURL, '/', $this->_get('header_css'));
		return '';
	}


	public function getHeaderCss() {
		if ($this->getFileWriter()->fileExists($this->getHeaderCssPath()))
			return $this->_get('header_css');
		return '';
	}


	/** @return array la liste des zones titre a afficher dans le resultat de recherche */
	public function getZonesTitre() {
		$cfg = $this->getCfgModulesAsArray();
		if (!isset($cfg['recherche']['resultatsimple']['zones_titre']))
			return [];

		return explode(';', $cfg['recherche']['resultatsimple']['zones_titre']);
	}
}