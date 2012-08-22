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
class Admin_ProfilController extends Zend_Controller_Action {
	/** @var Class_Profil */
	private $_profil;

	private $id_profil;				// Id profil sélectionné (pour les combos de sélection)
	private $id_zone;					// Id zone sélectionnée (pour les combos de sélection)
	private $id_bib;					// Id bib sélectionnée (pour les combos de sélection)


	public function preDispatch() {
		$session = $_SESSION['admin'];
		// Zone et bib du filtre (initialisé dans le plugin DefineUrls)
		$this->id_zone	= $session['filtre_localisation']['id_zone'];
		$this->id_bib		= $session['filtre_localisation']['id_bib'];

		// Init du profil en cours de traitement
		$id_profil_param = (int)$this->_request->getParam('id_profil', 0);

		if (!$this->_profil = Class_Profil::getLoader()->find($id_profil_param)) {
			if (!in_array(
							$this->_request->getActionName(),
							array('index', 'redirect-to-index', 'add', 'genres', 'module-sort'))
			) {
				$this->_forward('redirect-to-index');
				return;

			} else {
				$this->_profil = Class_Profil::getCurrentProfil();
			}

		} else {
			Class_Profil::setCurrentProfil($this->_profil);
		}

		$this->id_profil = $this->_profil->getId();

		$session['id_profil']	= $this->id_profil;
		$_SESSION['admin']		= $session;


		$this->view->id_zone = $this->id_zone;
		$this->view->id_bib = $this->id_bib;
		$this->view->profil = $this->_profil;
	}


	public function redirectToIndexAction() {
		$this->_redirect('/admin/profil');
	}


	public function indexAction()	{
		$user = ZendAfi_Auth::getInstance()->getIdentity();

		$profils = Class_Profil::getLoader()->findAllByZoneAndBib($this->id_zone,
																															$this->id_bib);

		$profils_by_bib = array();
		foreach ($profils as $profil) {

			if ($profil->hasParentProfil() or
					($user->ROLE_LEVEL <= 5 and
					 $user->ID_SITE != $profil->getIdSite())) continue;

			if ($profil->isInPortail()) {
				$libelle = 'Portail';
			} else {
				$libelle = $profil->getBibLibelle();
			}

			if (!array_key_exists($libelle, $profils_by_bib))
				$profils_by_bib[$libelle] = array();

			$profils_by_bib[$libelle] []= $profil;
		}

		ksort($profils_by_bib);

		$this->view->titre = 'Gestion des profils';
		$this->view->profils_by_bib = $profils_by_bib;
		$this->view->can_add_profil = ($user->ROLE_LEVEL > 5);
	}


	public function proprietesAction() {
		$this->view->titre = "Propriétés des modules: ".$this->_profil->getLibelle();

		// Définition des modules
		$cls=new Class_Systeme_ModulesNotice();
		$this->view->modules=$cls->getModules();
	}


	public function menusindexAction()	{
		$this->view->titre = "Configuration des menus du profil: ".$this->_profil->getLibelle();
		$this->view->menus = $this->_profil->getCfgMenusAsArray();
		$this->view->path_img = BASE_URL.$this->_profil->getPathTheme()."images/menus/";
	}


	public function menusmajAction() {
		$id_menu=$this->_getParam('id_menu');
		$profil = Class_Profil::getLoader()->find($this->id_profil);
		$menus = $profil->getCfgMenusAsArray();
		$this->view->path_img = BASE_URL.$profil->getPathTheme()."images/menus/";
		$this->view->browser = $profil->getBrowser();

		switch($this->_getParam("mode")) {
			case "edit":
				$this->view->id_profil=$this->id_profil;
				$this->view->id_menu=$id_menu;
				$this->view->action="Modifier";
				$this->view->menu=$menus[$id_menu];
				$this->view->titre = '['.$this->_profil->getLibelle().'] Modifier le menu: '.$menus[$id_menu]['libelle'];
				break;
			case "delete":
				unset($menus[$id_menu]);
				$profil->setCfgMenus($menus)->save();
				$this->_redirect('admin/profil/menusindex/id_profil/'.$this->id_profil);
				break;
			case "add":
				$this->view->id_menu=0;
				$this->view->action="Ajouter";
				$this->view->menu=array("libelle" => "** nouveau menu **","picto" => "vide.gif");
				$this->view->titre = '['.$this->_profil->getLibelle().'] Ajouter un menu';
				break;
			case "valider":
				$menu=$this->_getPostMenu();
				// En creation on attribue un id
				if(!$id_menu)
				{
					$id_max=0;
					foreach($menus as $id_menu => $data)
					{
						$id=intval($id_menu);
						if($id > $id_max) $id_max=$id;
					}
					$id_menu=$id_max +1;
				}
				$menus[$id_menu]=$menu;
				$profil->setCfgMenus($menus)->save();
				$this->_redirect('admin/profil/menusindex/id_profil/'.$this->id_profil);
				break;
		}

	}


	private function _getPostMenu() {
		extract($_POST);
		// Généralités
		$enreg["libelle"]=$libelle;
		$enreg["picto"]=$picto;

		// Lignes de menu
		if(!count($menu_properties)) return $enreg;
		foreach($menu_properties as $id_property => $item_post)
		{
			$menu=$this->_getItemPostMenu($item_post);
			unset($sous_menu);

			// Sous-menus
			if(count($sous_menu_properties[$id_property]))
			{
				foreach($sous_menu_properties[$id_property] as $item_post)
				$sous_menu[]=$this->_getItemPostMenu($item_post);
			}
			$menu["sous_menus"]=$sous_menu;
			$enreg["menus"][]=$menu;
		}
		return $enreg;
	}


	private function _getItemPostMenu($item_post) {
		$elems=explode(";",$item_post);
		foreach($elems as $elem)
		{
			// Decouper les preferences
			if(substr($elem,0,11)=="preferences")
			{
				$preferences=explode("|",substr($elem,12));
				foreach($preferences as $preference)
				{
					if(!$preference) continue;
					$attrib=$this->_splitArg($preference);
					$menu["preferences"][$attrib[0]]=$attrib[1];
				}
			}
			// Decouper attribut standards
			else
			{
				$attrib=$this->_splitArg($elem);
				$menu[$attrib[0]]=$attrib[1];
			}
		}
		$p=explode("/",$menu["picto"]);
		$menu["picto"]=array_pop($p);
		return $menu;
	}


	private function _parseSaveContentString($cfg_module) {
		$enreg = array("modules" => array());
		$profil = Class_Profil::getLoader()->find($this->id_profil);

		// Decoupage des modules
		$modules = explode(';',$cfg_module);
		foreach ($modules as $module) {
			$elem=explode('|',$module);
			$division=substr($elem[0],-1);

			$id_module=$elem[1];

			// permet de ne pas écraser un module existant,
			// on réaffecte un id si c'est un nouveau module
			if ($id_module == 'new') {
				$id_module = $profil->createNewModuleAccueilId();
			}

			$type_module=$elem[2];
			$str_preferences=trim($elem[3]);

			$preferences = array();
			if(strlen($str_preferences) > 0) {
				$props=explode("/",$str_preferences);
				foreach($props as $prop) {
					$names_vals=$this->_splitArg($prop);
					$preferences[$names_vals[0]]=$names_vals[1];
				}
			}
			$enreg["modules"][$id_module]=compact("division","type_module","preferences");
		}
		return $enreg;
	}


	public function newpageAction() {
		$newpage = Class_Profil::getLoader()
			->newInstance()
			->setParentProfil($this->_profil)
			->setLibelle('** nouvelle page **');
		$newpage->save();
		$this->_redirect('admin/profil/accueil/id_profil/'.$newpage->getId());
	}


	public function copyAction() {
		$copy = $this->_profil->copy();

		if (!$copy->hasParentProfil()) {
			$copy
				->setParentProfil($this->_profil)
				->setLibelle($this->_profil->getLibelle());
		} else {
			$copy->setLibelle($this->_profil->getLibelle().' - copie');
		}

		$copy->save();

		if (preg_match('/admin\/profil$/', $_SERVER['HTTP_REFERER']))
			$this->_redirect('admin/profil');

		$this->_redirect('admin/profil/accueil/id_profil/'.$copy->getId());
	}


	public function accueilAction()	{
		// Instanciations et initialisations
		$this->view->titre = "Configuration de la page: ".$this->_profil->getLibelle();

		$profil = Class_Profil::getLoader()->find($this->id_profil);
		$class_module = new Class_Systeme_ModulesAccueil();
		$liste_module = $class_module->getModules();

		// Retour du formulaire
		if ($this->_request->isPost())	{
			// Recup du POST
			$profil->setLibelle($this->_getParam('libelle',
																					 $profil->getLibelle()));
			$cfg_module = $this->_getParam('saveContent');
			$enreg = $this->_parseSaveContentString($cfg_module);

			foreach($enreg["modules"] as $id_module => $module_config) {
				if ($id_module == 0) {
					$id_module = $profil->createNewModuleAccueilId();
					$enreg["modules"][$id_module] = $enreg["modules"][0];
					unset($enreg["modules"][0]);
				}

				/* Si aucune préférences (nouveau module), on mets les préférences par défaut */
				if (count($module_config['preferences']) == 0) {
					$type_module = $module_config['type_module'];
					$enreg["modules"][$id_module]['preferences'] = $class_module->getValeursParDefaut($type_module);
				}

				/* Permet de ne pas perdre la configuration des modules contenus
				 * dans une boîte 2 colonnes */
				if ($module_config['type_module'] == "CONTENEUR_DEUX_COLONNES") {
					if (array_key_exists('col_gauche_module_id', $module_config['preferences']) and
						  $id_col_gauche = $module_config['preferences']['col_gauche_module_id'])
						$enreg["modules"][$id_col_gauche] = $profil->getModuleAccueilConfig($id_col_gauche);


					if (array_key_exists('col_droite_module_id', $module_config['preferences']) and
							$id_col_droite = $module_config['preferences']['col_droite_module_id'])
						$enreg["modules"][$id_col_droite] = $profil->getModuleAccueilConfig($id_col_droite);
				}
			}

			/* Récupère les boite de la banniere pour ne pas les perdre */
			if (!$profil->hasParentProfil()) {
				foreach($profil->getBoitesDivision(Class_Profil::DIV_BANNIERE) as $id => $module)
					$enreg["modules"][$id] = $module;
			}

			$profil->setCfgAccueil($enreg)->save();
			$this->_redirect('admin/profil/accueil/id_profil/'.$profil->getId());
		}

		// Entree en maj
		else {
			// Html des modules sélectionnés triés par divisions
			$box = array(1 => '', 2 => '', 3 => '', 4 => '');
			foreach($box as $division => $content) 
				$box[$division] = $this->_getHTMLForProfilModulesDivision($profil, $division);

			// Html des objets disponibles
			$groupes=$class_module->getGroupes();
			$box_dispo = array();
			foreach($groupes as $groupe => $libelle) {
				if (!array_key_exists($groupe, $box_dispo))
					$box_dispo[$groupe] = '';
				$box_dispo[$groupe].='<div><p>'.$libelle.'</p><ul id="allItems" style="height:185px;">';
			}

			foreach($liste_module as $type_module => $module) {
				if (!$module->isVisibleForProfil($profil)) continue;
				$box_dispo[$module->getGroup()].=$this->_getItemModule($type_module,$module);
			}

			foreach($groupes as $groupe => $libelle) 
				$box_dispo[$groupe].='</ul></div>';

			// Get le nombre de divisions dans le profil
			$this->view->nb_divisions = $profil->getNbDivisions();

			// Variables de vue
			$this->view->module_info = $box_dispo["INFO"];
			$this->view->module_rech = $box_dispo["RECH"];
			$this->view->module_site = $box_dispo["SITE"];
			$this->view->box1 = $box[1];
			$this->view->box2 = $box[2];
			$this->view->box3 = $box[3];
		}
	}


	protected function _getHTMLForProfilModulesDivision($profil, $division) {
		$html = '';
		$modules = $profil->getBoitesDivision($division);

		foreach($modules as $id_module => $module)
			$html .= $this->_getItemModule($module['type_module'], 
																		 Class_Systeme_ModulesAccueil::moduleByCode($module['type_module']), 
																		 $module['preferences'],
																		 $id_module);
		return $html;
	}


	protected function _getItemModule($type_module, $module, $preferences = '', $id_module = 0) {
		$properties = '';
		if ($preferences)	{
			foreach($preferences as $clef => $valeur)
				$properties.=$clef."=".$valeur."/";
		}

		if($id_module) $display="block"; else $display="none";
		$onclick="majProprietes(this,'".BASE_URL."/admin/accueil/".$module->getAction()."?config=admin&amp;id_profil=".$this->id_profil."',".$module->getPopupWidth().",".$module->getPopupHeight().");";

		$item='<li id="'.$type_module.'" id_module="'.$id_module.'" proprietes="'.$properties.'"><table width="97%"><tr>';
		$item.='<td align="left" class="cfg_accueil">'.$module->getLibelle().'</td>';
		$item.='<td align="right"><img src="'.URL_ADMIN_IMG.'ico/fonctions_admin.png" onclick="'.$onclick.'" title="propriétés" style="display:'.$display.'" alt="Propriétés"/></td>';
		$item.='</tr></table></li>';
		return $item;
	}


	public function genresAction() {
		if ($this->_request->isPost())
		{
			if(count($_POST)>0)
			{
				foreach($_POST as $id => $picto)
				{
					$elems=explode("_",$id);
					$id_genre=$elems[1];
					sqlExecute("update codif_genre set picto='$picto' where id_genre=$id_genre");
				}
			}
		}
		// Entree en maj
		$this->view->genres=fetchAll("select * from codif_genre");
		$this->view->titre = "Pictogrammes pour les genres";
	}


	public function addAction()	{
		$profil = Class_Profil::getLoader()->find(1)->copy();
		if ($this->_postProfil($profil))
			$this->_redirect('admin/profil/edit/id_profil/'.$profil->getId());

		$this->view->profil = $profil;
		$this->view->action = 'add';
		$this->view->titre = 'Ajouter un profil';
	}


	private function _postProfil($profil) {
		if (!$this->_request->isPost())
			return false;

		$post = ZendAfi_Filters_Post::filterStatic($this->_request->getPost());
		return $profil
			->updateAttributes($post)
			->save();
	}


	public function editAction() {
		$profil = Class_Profil::getLoader()->find($this->id_profil);
		$this->_postProfil($profil);

		// Action
		$this->view->profil = $profil;
		$this->view->action = 'edit';
		$this->view->titre = 'Modifier le profil: '.$profil->getLibelle();
	}


	public function moveAction() {
		$profil = Class_Profil::getLoader()->find($this->id_profil);

		$target_id = $this->_getParam('to');
		$target = Class_Profil::getLoader()->find($target_id);

		$profil
			->setParentProfil($target)
			->save();

		$this->getHelper('ViewRenderer')->setNoRender();
	}


	public function moduleSortAction() {
		$this->getHelper('ViewRenderer')->setNoRender();

		if (!$profil = Class_Profil::getLoader()->find($this->_getParam('profil')))
			return;

		$profil->moveModuleOldDivPosNewDivPos($this->_getParam('fromDivision'), 
																					$this->_getParam('fromPosition'), 
																					$this->_getParam('toDivision'), 
																					$this->_getParam('toPosition'));
		$profil->save();
	}


	public function deleteAction()	{
		$profil = Class_Profil::getLoader()->find($this->id_profil);
		$profil->delete();

		// si on travaille sur un profil on reste dans le contexte
		if ($profil->hasParentProfil()
				and strpos($_SERVER['HTTP_REFERER'], 'id_profil'))
			$this->_redirect('admin/profil/edit/id_profil/'.$this->_profil->getParentProfil()->getId());

		$this->_redirect('admin/profil');
	}


	private function _splitArg($item) {
		$pos = strpos($item, '=');
		if ($pos === false)
			return false;

		$clef = substr($item, 0, $pos);
		$valeur = substr($item, ($pos+1));
		return array($clef, $valeur);
	}
}