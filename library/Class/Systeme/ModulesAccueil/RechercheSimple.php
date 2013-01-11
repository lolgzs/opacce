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
class Class_Systeme_ModulesAccueil_RechercheSimple extends Class_Systeme_ModulesAccueil_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_RECH;
	
	/** @var string */
	protected $_libelle = 'Recherche simple';

	/** @var string */
	protected $_action = 'rechsimple';

	/** @var int */
	protected $_popupWidth = 710;

	/** @var int */
	protected $_popupHeight = 520;

	/** @var bool */
	protected $_isPhone = false;

	/** @var array */
	protected $_defaultValues = array(
		'titre' => 'Rechercher', // Titre de la boite
		'message' => '', // Message au-dessus du champ de saisie
		'exemple' => '', // Exemple sous le champ de saisie
		'select_bib' => 0, // Sélection des bib
		'select_doc' => 0, // Sélection des types de docs
		'select_annexe' => 0, // Sélection annexes
		'largeur' => 140, // du champ de saisie
		'recherche_avancee' => 1,
	);
}
?>