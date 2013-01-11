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
class Class_Systeme_ModulesAccueil_News extends Class_Systeme_ModulesAccueil_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_INFO;
	
	/** @var string */
	protected $_libelle = 'Articles';

	/** @var string */
	protected $_action = 'news';

	/** @var int */
	protected $_popupWidth = 800;

	/** @var int */
	protected $_popupHeight = 750;

	/** @var bool */
	protected $_isPhone = true;

	/** @var bool */
	protected $_isPackMobile = false;

	/** @var array */
	protected $_defaultValues = array(
		'titre' => 'Articles', // Titre de la boite
		'type_aff' => 1, // Type a afficher : 1=sélection libre, 2=les + récents
		'id_categorie' => '', // Liste d'id_categorie séparés par des tirets
		'id_items' => '', // Liste d'id_news séparés par des tirets
		'nb_aff' => '1', // Nombre à afficher
		'nb_analyse' => '5', // Nombre à analyser
		'display_order' => 'Selection', // Ordre d'affichage des articles
		'display_titles_only' => false, // Afficher seulement les titres
		'rss_avis' => true, // Afficher les rss
		'op_largeur_img' => 200, // Largeur de la boite pour l'affichage diaporama
		'op_hauteur_boite' => 400 // Hauteur de la boite pour l'affichage diaporama
	);
}
?>