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
class Class_Systeme_ModulesAccueil_BibliothequeNumerique extends Class_Systeme_ModulesAccueil_Null {
	const DISPLAY_TREE = 'displayTree';
	const DISPLAY_ALBUM_TEASER = 'displayAlbumTeaser';

	const ORDER_RESPECT = 'orderRespect';
	const ORDER_RANDOM = 'orderRandom';

	/** @var array */
	protected $_displayModes = array(self::DISPLAY_TREE => 'Arborescence',
																	 self::DISPLAY_ALBUM_TEASER => 'Mise en avant d\'un album');

	/** @var array */
	protected $_orderModes = array(self::ORDER_RESPECT => 'Respecter l\'ordre',
																	 self::ORDER_RANDOM => 'Aléatoire');

	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_INFO;
	
	/** @var string */
	protected $_libelle = 'Bibliothèque numérique';

	/** @var string */
	protected $_action = 'bibliotheque-numerique';

	/** @var int */
	protected $_popupWidth = 800;

	/** @var int */
	protected $_popupHeight = 700;

	/** @var bool */
	protected $_isPhone = true;

	/** @var array */
	protected $_defaultValues = array('titre' => 'Bibliothèque numérique',	 // Titre de la boite
																		'id_categories' => '', // séparés par des -
																		'type_aff' => self::DISPLAY_TREE, // mode d'affichage
																		'nb_aff' => '', // nb à afficher
																		'id_albums' => '', // séparés par des -
																		'order' => self::ORDER_RESPECT, // mode de tri,
																		);


	/** @return array */
	public function getDisplayModes() {
		return $this->_displayModes;
	}

	
	/** @return array */
	public function getOrderModes() {
		return $this->_orderModes;
	}


	/** @return boolean */
	public function isVisibleForProfil($profil) {
		return Class_AdminVar::isBibNumEnabled();
	}
}
?>