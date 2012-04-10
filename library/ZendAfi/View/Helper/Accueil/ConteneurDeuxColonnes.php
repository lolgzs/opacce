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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Conteneur sur 2 colonnes pour poser d'autres boîtes
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_ConteneurDeuxColonnes extends ZendAfi_View_Helper_Accueil_Base {
	/* Désactive le cache car si on modifie une sous-boîte le contenu doit être mis à jour*/
	public function shouldCacheContent() { return false; }

	public function getHtml() {
		$this->titre = $this->preferences["titre"];
		$this->contenu = $this->getContenu();
		return $this->getHtmlArray();
	}

	protected function getContenu() {
		$contenu = '';

		foreach(array('gauche', 'droite') as $colonne) {
			$id_key = 'col_'.$colonne.'_module_id';

			if (!array_isset($id_key, $this->preferences)) 
				$module_id = $this->_createModuleForCol($colonne);
			else {
				$module_id = $this->preferences[$id_key];
				if (!ZendAfi_View_Helper_Accueil_Base::getModuleHelper($module_id))
					$module_id = $this->_createModuleForCol($colonne);
			};

			$helper =  ZendAfi_View_Helper_Accueil_Base::getModuleHelper($module_id);

			if ($helper) {
				$helper->setView($this->view);
				$html = $helper->getBoite();
			}

			$contenu .= '<div class="col_'.$colonne.'"><div>'.$html.'</div></div>';
    }

		return '<div class="conteneur2colonnes">'.$contenu.'</div>';
	}


	protected function _createModuleForCol($colonne) {
		$id_key = 'col_'.$colonne.'_module_id';
		$type_key = 'col_'.$colonne.'_type';
		$type_module = $this->preferences[$type_key];
		$profil = Class_Profil::getCurrentProfil();

		$id_module = $profil->createNewModuleAccueilId();

		$modules_accueil = new Class_Systeme_ModulesAccueil();
		$preferences = $modules_accueil->getValeursParDefaut($type_module);

		$config = array("preferences" => $preferences,
										"type_module" => $type_module);

		


		$my_config = $profil->getModuleAccueilConfig($this->id_module);
		$my_config["preferences"][$id_key] = $id_module;

		$profil
			->updateModuleConfigAccueil($id_module, $config);

		$profil
			->updateModuleConfigAccueil($this->id_module, $my_config)
			->save();

		return $id_module;
	}
}

?>