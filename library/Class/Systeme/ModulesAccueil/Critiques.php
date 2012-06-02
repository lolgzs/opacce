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
class Class_Systeme_ModulesAccueil_Critiques extends Class_Systeme_ModulesAccueil_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_INFO;
	
	/** @var string */
	protected $_libelle = 'Critiques';

	/** @var string */
	protected $_action = 'critiques';

	/** @var int */
	protected $_popupWidth = 570;

	/** @var int */
	protected $_popupHeight = 520;

	/** @var bool */
	protected $_isPhone = true;

	/** @var array */
	protected $_defaultValues = array(
		'titre' => "Dernières critiques", // Titre du bloc critiques
		'nb_aff_avis' => 2, // Nombre de critiques à afficher
		'nb_words' => 30, // Couper les critiques à X mots
		'display_order' => "Random", // Affichage par ordre aléatoire
		'rss_avis' => "1", // Proposer le flux RSS
		'only_img' => '1',
		'id_panier' => 0,
		'id_catalogue' => 0,
		'abon_ou_bib' => 0,
	);
}
?>