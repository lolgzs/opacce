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
class Class_Systeme_ModulesAccueil_Sitotheque extends Class_Systeme_ModulesAccueil_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_INFO;
	
	/** @var string */
	protected $_libelle = 'Sitothèque';

	/** @var string */
	protected $_action = 'sitotheque';

	/** @var int */
	protected $_popupWidth = 800;

	/** @var int */
	protected $_popupHeight = 600;

	/** @var bool */
	protected $_isPhone = false;

	/** @var array */
	protected $_defaultValues = array(
		'titre' => 'Sitothèque', // Titre de la boite
		'type_aff' => 1, // Type a afficher : 1=sélection libre, 2=les + récents
		'id_categorie' => '', // Liste d'id_categorie séparés par des tirets
		'id_items' => '', // Liste d'id_site séparés par des tirets
		'nb_aff' => '2', // Nombre à afficher
		'group_by_categorie' => false //grouper les sites par categorie sous forme de menu
	);
}
?>