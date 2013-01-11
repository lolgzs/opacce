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
class Class_Systeme_ModulesMenu_Catalogue extends Class_Systeme_ModulesMenu_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesMenu::GROUP_MENU_CATALOGUES;
	
	/** @var int */
	protected $_popupWidth = 550;

	protected $_libelle = "Catalogue";
	/** @var int */
	protected $_popupHeight = 470;

	protected $_isPhone = false;

	protected $_action = 'catalogue';

	protected $_defaultValues = array(
			'titre' => 'Catalogue', // Titre de la boite
			'nb_notices' => 20, // Nombre de notices a afficher
			'aleatoire' => 1,	// 1=tirage aleatoire
			'tri' => 1, // 0=alpha,1=par date de creation,2=les plus consultées
			'nb_analyse' => 50, // nbre a analyser pour le mode aleatoire
	);


	public function getUrl($preferences = []) {
		return BASE_URL . "/catalogue/appelmenu?" . http_build_query($preferences) . "&reset=true";
	}
}
?>