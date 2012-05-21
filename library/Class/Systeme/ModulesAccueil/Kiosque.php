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
class Class_Systeme_ModulesAccueil_Kiosque extends Class_Systeme_ModulesAccueil_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_RECH;
	
	/** @var string */
	protected $_libelle = 'Kiosque de notices';

	/** @var string */
	protected $_action = 'kiosque';

	/** @var int */
	protected $_popupWidth = 570;

	/** @var int */
	protected $_popupHeight = 520;

	/** @var bool */
	protected $_isPhone = false;

	/** @var array */
	protected $_defaultValues = array(
		'titre' => "Kiosque",			 // Titre de la boite
		'style_liste' => "slide_show",	 // Style de representation (objets flash ou js)
		'nb_notices' => 20,				// Nombre de notices a afficher
		'only_img' => 1,					// Notices avec vignettes
		'aleatoire' => 1,				// 0=non,1=oui
		'tri' => 1,					 // 0=alpha,1=par date de creation,2=les plus consultées
		'nb_analyse' => 50,			 // nbre a analyser pour le mode aleatoire
		'op_hauteur_img' => 0, // hauteur des vignettes
		'op_transition' => '', //type de transition pour le diaporama
		'op_largeur_img' => 0, //largeur des vignettes 
		'rss_avis' => 1,    // afficher les RSS
		'id_catalogue' => 0
	);
}
?>