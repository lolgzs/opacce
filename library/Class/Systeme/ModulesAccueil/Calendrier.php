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
class Class_Systeme_ModulesAccueil_Calendrier extends Class_Systeme_ModulesAccueil_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_INFO;
	
	/** @var string */
	protected $_libelle = 'Calendrier';

	/** @var string */
	protected $_action = 'calendrier';

	/** @var int */
	protected $_popupWidth = 800;

	/** @var int */
	protected $_popupHeight = 700;

	/** @var bool */
	protected $_isPhone = false;

	/** @var array */
	protected $_defaultValues = array(
		'titre' => 'Calendrier des animations',	 // Titre de la boite
		'id_categorie' => '',										// Catégories d'articles à afficher. ex: 2-3-4
		'display_cat_select' => false,					 // Afficher le sélecteur de catégories
		// Information à afficher en préfixed du titre de l'article: bib, cat ou none.
		'display_event_info'=> false,
		'rss_avis' => false,                     //RSS
		'display_next_event' => '1' //Afficher Prochains rendez-vous
	);


	/** @return array */
	public function getProperties() {
		$properties = parent::getProperties();
		$properties['display_event_info'] = 'bib';
		return $properties;
	}
}
?>