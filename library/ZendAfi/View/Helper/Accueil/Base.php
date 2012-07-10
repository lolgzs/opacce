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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC 3 : classe de base pour le gestion des modules de la page D'ACCUEIL !
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Accueil_Base extends ZendAfi_View_Helper_BaseHelper {
	protected static $modules_config;
 	protected $id_module;									// Index du module pour modifier la config
 	protected $type_module;								// Type de module
 	protected $division;									// Division dans laquelle se trouve le module
	protected $preferences;								// Réglages initialises au constructeur
	protected $titre;											// Titre du module
	protected $contenu;										// Contenu du module
	protected $message;										// Message au dessus de la boite du module
	protected $rss_interne;								// Lien sur fil rss interne


//---------------------------------------------------------------------
// Constructeur (initialise les preferences)
//---------------------------------------------------------------------
	public function __construct($id_module,$params)	{
		parent::__construct();
		$this->id_module = $id_module;
		$this->type_module = $params["type_module"];

		$this->division = null;
		if (array_isset('division', $params))
			$this->division = $params["division"];

		$modules_accueil = new Class_Systeme_ModulesAccueil();
	
		$this->preferences = $params["preferences"];
		if (!$this->preferences) 
			$this->preferences = $modules_accueil->getValeursParDefaut($this->type_module);

		$this->preferences = array_merge($modules_accueil->getValeursParDefaut($this->type_module), 
																		 $this->preferences);
	}


	public function setPreference($name, $value) {
		$this->preferences[$name] = $value;
		return $this;
	}

	// Par défaut le contenu de la boîte n'est pas en cache
	public function shouldCacheContent() {
		if (Class_Users::getLoader()->isCurrentUserAdmin())
			return false;

		return Class_AdminVar::isCacheEnabled();
	}


	public function getLocale() {
		return $this->view->_translate()->getLocale();
	}

	// Calcul la clé qui référence le cache()
	public function getCacheKey() {
		return md5(serialize(array(BASE_URL,
															 $this->id_module,
															 Class_Profil::getCurrentProfil()->getId(),
															 $this->getLocale(),
															 $this->preferences)));
	}


	public function getPreferences() {
		return $this->preferences;
	}


	//------------------------------------------------------------------------------------------------------
	// Rend le combo des templates de boites
	//------------------------------------------------------------------------------------------------------
	public static function getComboTemplates($valeur_select, $path_templates=null)	{
		if (null === $path_templates)
			$path_templates = Zend_Registry::get("path_templates")."boites/";

		$dir = opendir($path_templates);

		$template = array();
		// Recup des templates
    while (false !== ($file = readdir($dir))) {
    	if(substr($file,-5) != ".html") continue;
    	$clef=str_replace(".html","",$file);
    	$template[$clef] = str_replace("_"," ",$clef);
    }
    closedir($dir);
		asort($template);

		$template = array_merge(array("" => self::translate()->_("Boite par défaut de la division")),
														$template);

		// Composer le html
		$combo='<select name="boite">';
		foreach($template as $clef => $libelle)
		{
			if($valeur_select==$clef) $selected=" selected"; else $selected="";
			$combo.='<option value="'.$clef.'"'.$selected.'>'.$libelle.'</option>';
		}
		$combo.='</select>';
		return $combo;
	}


	/*
	 * Génère l'url du RSS pour ce module
	 */
	protected function _getRSSurl($controller, $action) {
		return Class_Profil::getCurrentProfil()->urlForModule($controller,
																													$action,
																													$this->id_module);
	}

//---------------------------------------------------------------------
// Rend le html_array à mettre dans une boite
//---------------------------------------------------------------------
	protected function getHtmlArray()
	{
		return array("TITRE" => $this->titre,
								 "CONTENU" => $this->getFonctionAdmin().$this->contenu,
								 "MESSAGE" => $this->message,
								 "RSS" => $this->rss_interne);
	}

//---------------------------------------------------------------------
// Retour en erreur
//---------------------------------------------------------------------
	protected function retourErreur($erreur)
	{
		return array("TITRE" => $this->translate()->_("Erreur de configuration"),
								 "CONTENU" => $this->getFonctionAdmin().'<br/><p class="erreur">'.$erreur.'</p><br/>');
	}


	/**
	 * @param string $contenu
	 * @return string
	 */
	protected function extractHeader($contenu) {
		$contenu_sep = explode('{FIN}', $contenu);

		if (1 < count($contenu_sep)) {
			$contenu_sep[0] = $contenu_sep[0] . ' [...]';
		}

		$content = html_entity_decode($contenu_sep[0], ENT_QUOTES, 'UTF-8');
		$contenu_fix = wordwrap($content, 30, "\n", 1);

		return $contenu_fix;

	}

//---------------------------------------------------------------------
// Réduit la largeur du titre pour une boite à gauche
//---------------------------------------------------------------------
	protected function fixLibelleBoiteGauche($libelle)
	{
		if(strlen($libelle) > 35) $titre_fix = substr($libelle,0,35).'...';
		else $titre_fix = $libelle;
		return ($titre_fix);
	}

//---------------------------------------------------------------------
// Rend l'objet d'acces aux proprietes
//---------------------------------------------------------------------
	protected function getFonctionAdmin()
	{
		$cls=new ZendAfi_View_Helper_FonctionsAdmin();
		$fonctions_admin=$cls->fonctionsAdmin("module_accueil",$this->id_module,$this->type_module);
		return $fonctions_admin;
	}


	protected function getTemplate() {
		$style_boite = array("boite_vide",
												 "boite_de_la_division_gauche",
												 "boite_de_la_division_du_milieu",
												 "boite_de_la_division_droite",
												 "boite_banniere_gauche");
		if (!array_key_exists('boite', $this->preferences) || !$this->preferences['boite'])
			$this->preferences['boite'] = $style_boite[$this->getDivision()];

		$template = $this->preferences['boite'];
		return Zend_Registry::get("path_templates")."boites/".$template.".html";
	}


	public function getDivision() {
		if (!$this->division)
			return 0;
		return $this->division;
	}


	/** 
	 * Tout le temps exécuté dans le rendu, cache actif ou non
	 * ce qui permet d'inclure notamment les actions javascripts
	 */
	protected function _beforeCache() {
		$this->_renderHeadScriptsOn(Class_ScriptLoader::getInstance());
	}


	protected function _renderHeadScriptsOn($script_loader) {
	}


	public function getBoite() {
		$this->_beforeCache();

		$key = $this->getCacheKey();
		$cache = Zend_Registry::get('cache');

		if ($this->shouldCacheContent() and ($boite = $cache->load($key)))
			return array_first(ZendAfi_Filters_Serialize::unserialize($boite));


		$template = $this->getTemplate();
		$html_array = $this->getHtml();
		$boite = $this->getBoiteFromTemplate($template, $html_array);


		if ($this->shouldCacheContent())
			$cache->save(ZendAfi_Filters_Serialize::serialize(array($boite)), $key);
		return $boite;
	}

	//------------------------------------------------------------------------------------------------------
	// Rendu de boite a partir d'un template
	//------------------------------------------------------------------------------------------------------
	public function getBoiteFromTemplate($template, $html_array)
	{
		// Fil rss interne
		if (array_isset('RSS', $html_array))
			$html_array["RSS"]= sprintf('<a href="%s" target="_blank"><img src="%s" style="border:0px" alt="%s"/></a>',
																	$html_array["RSS"],
																	URL_IMG.'rss.gif',
																	$this->translate()->_('flux RSS de la boite %s',
																												$this->preferences['titre']));
		else
			$html_array["RSS"]="";


		// Lire le template
		if(file_exists($template))
		{
			$html=file_get_contents($template);
			$pos_fin = 0;
			$blocs = array();

			// Interpretation des IF-xxx
			while(true)	{
				$pos=strPos($html,"{IF-",$pos_fin);
				if($pos===false) break;
				$pos_fin=strpos($html,"{ENDIF}",$pos);
				if($pos_fin===false) break;
				$blocs[]=substr($html,$pos,($pos_fin+7-$pos));
			}

			if($blocs) {
				foreach($blocs as $bloc)	{
					$pos=strpos($bloc,"}");
					$var=substr($bloc,4,$pos-4);
					if(!trim($html_array[$var])) {$html=str_replace($bloc,"",$html); continue;}
					$suppr[]=substr($bloc,0,($pos+1));
				}
				$suppr[]="{ENDIF}";
				foreach($suppr as $var) $html=str_replace($var,"",$html);
			}

			// Fusion des variables
			$html=str_replace("{URL_IMG}",URL_IMG,$html);
			foreach($html_array as $clef => $valeur) $html=str_replace("{".$clef."}",$valeur,$html);
		}
		else $html=$html_array["TITRE"].BR.$html_array["CONTENU"];
		return ($html);
	}


	/*
	 * Renvoie l'instance ZendAfi_View_Helper_* selon le type du module dont l'id
	 * est donnée
	 */
	public static function getModuleHelper($id_module) {
		$module_params = Class_Profil::getCurrentProfil()->getModuleAccueilConfig($id_module);
		return self::getModuleHelperFromParams($id_module, $module_params);
	}


	public static function getModuleHelperFromParams($id_module, $module_params) {
		$type_module=$module_params["type_module"];
		if (!$type_module) return null;

		$classname = self::getModuleHelperClass($type_module);
		if (class_exists($classname))
			return new $classname($id_module, $module_params);

		return null;
	}


	/*
	 * Renvoie le nom de classe Helper du type de module donné, en fonction
	 * du type de profil (téléphone ou portail)
	 * ex:
	 *   ZendAfi_View_Helper_Accueil_Base::moduleHelperClass('RECH_SIMPLE')
	 * renvoie ZendAfi_View_Helper_Accueil_RechSimple (si portail)
	 *         ZendAfi_View_Helper_Telephone_RechSimple (si téléphone)
	 */
	private static function getModuleHelperClass($type_module) {
		$suffix = str_replace(' ', '',                                    //joint le tout: "Rech Guidee" => "RechGuidee"
									ucwords(                                            //met la première lettre en majuscule: "rech guidee" => "Rech Guidee"
				  				strtolower(str_replace('_', ' ', $type_module)))); //sépare les mots et met en minuscule: "RECH_GUIDEE" => "rech guidee"

		if (Class_Profil::getCurrentProfil()->isTelephone())
			$prefix = 'Telephone';
		else
			$prefix = 'Accueil';

		return 'ZendAfi_View_Helper_'.$prefix.'_'.$suffix;
	}



	/** Paramètres commun pour les affichages diaporame */
	public function getDisplayType() {
		if (!array_key_exists("style_liste", $this->preferences) 
				|| "none" == $this->preferences["style_liste"])
			return "none";
		return $this->preferences["style_liste"];
	}


	public function isDisplayDiaporama() {
		return "diaporama" == $this->getDisplayType();
	}


	public function isDisplayBooklet() {
		return "booklet" == $this->getDisplayType();
	}


	public function isDisplayListe() {
		return "none" == $this->getDisplayType();
	}


	/**
	 * @param Class_Album $album
	 * @return ZendAfi_View_Helper_Accueil_BibNumerique
	 */
	public function renderSlideShowScriptsOn($script_loader, $selector, $options=null) {
		$this->view->getHelper('TagSlideshow')
			->setPreferences($this->preferences)
			->renderSlideShowScriptsOn($script_loader, $selector, $options);
	}
}
