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
class Class_Systeme_ModulesMenu_Rss extends Class_Systeme_ModulesMenu_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesMenu::GROUP_MENU_INFORMATIONS;
	
	/** @var int */
	protected $_popupWidth = 800;

	/** @var int */
	protected $_popupHeight = 550;

	/** @var string */
	protected $_libelle = 'Fils Rss';

	/** @var string */
	protected $_action = 'rss';

	protected $_defaultValues = array(
			'id_categorie' => '', // Liste d'id_categorie séparés par des tirets
			'id_items' => '', // Liste d'id_sito séparés par des tirets
			'nb' => '10', // Nombres de flux à afficher
		);


	public function getUrl($preferences =[]) {
		if ($preferences["id_items"]) {
			$items = explode("-", $preferences["id_items"]);
			$url = BASE_URL . "/rss/main/id_flux/" . $items[0];
		}
		return $this->_url;
	}


}
?>