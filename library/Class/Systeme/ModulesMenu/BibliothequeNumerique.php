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
class Class_Systeme_ModulesMenu_BibliothequeNumerique extends Class_Systeme_ModulesMenu_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesMenu::GROUP_MENU_INFORMATIONS;
	
	/** @var int */
	protected $_popupWidth = 550;

	protected $_libelle = "Lien vers un album";

	protected $_action = "album";

	/** @var int */
	protected $_popupHeight = 290;

	protected $_isPhone = false;



	/** @return boolean */
	public function isVisibleForProfil($profil) {
		return Class_AdminVar::isBibNumEnabled();
	}


	public function getUrl($preferences=[]) {
		return BASE_URL . "/bib-numerique/booklet/id/" . $preferences['album_id'];
	}
}
?>