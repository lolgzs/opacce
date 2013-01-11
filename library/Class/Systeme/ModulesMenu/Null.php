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
class Class_Systeme_ModulesMenu_Null extends Class_Systeme_ModulesAccueil_Null {
	/** @var string */
	protected $_url = '';

	protected $_action = 'index';

	protected $_group='';
	/** @var string */
	protected $_open_in_new_window = false;

	protected $_popupWidth='550';
	protected $_popupHeight='215';

	public function getUrl($preferences=[]) {
		return BASE_URL.$this->_url;
	}


	public function shouldOpenInNewWindow() {
		return $this->_open_in_new_window;
	}


	public function isVisibleForProfil($profil) {
		return true;
	}


	public function getProperties() {

		$properties = [ 'libelle' => $this->_libelle,
										'groupe' => $this->getGroup(),
										'phone' => $this->isPhone(),
										'popup_width' => $this->_popupWidth,
										'popup_height' => $this->_popupHeight,
										
		];
		if ($this->_action)
			$properties['action'] = $this->_action;
		return $properties;
						 
					 
	}
}


?>