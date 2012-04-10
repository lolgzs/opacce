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
class Class_Systeme_ModulesAccueil_Catalogue extends Class_Systeme_ModulesAccueil_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_RECH;
	
	/** @var string */
	protected $_libelle = 'Catalogue de notices';

	/** @var string */
	protected $_action = 'catalogue';

	/** @var int */
	protected $_popupWidth = 570;

	/** @var int */
	protected $_popupHeight = 545;

	/** @var bool */
	protected $_isPhone = false;

	/** @var array */
	protected $_defaultValues = array(
		'message' => "message du dessus",	// Message place au dessus de la boite
		'notices' => 2,							 // Notices a afficher 0=toutes, 1=les plus consultees, 2=les nouveautés
		'format' => 4,						// Format affichage (4=bookflip)
		'ordre' => "",						// Affichage (0=par ordre, 1=aléatoire)
		'type_doc' => "",					// Types de docs (codes)
		'section' => "",					 // Sections (codes)
		'genre' => "",						// Genres (codes)
		'dewey' => "",						// Dewey (commence par)
		'pcdm4' => "",						// Pcdm4 (commence par)
		'matiere' => "",					 // Matieres (codes)
		'nb_requete' => 200,				 // Nbre de notices à analyser
		'nb_aff' => 10,					 // Nombre de notices a afficher
	);
}
?>