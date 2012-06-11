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
class Class_Systeme_ModulesAccueil_Null {
	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_INFO;
	
	/** @var string */
	protected $_libelle = '';

	/** @var string */
	protected $_action = '';

	/** @var int */
	protected $_popupWidth = 0;

	/** @var int */
	protected $_popupHeight = 0;

	/** @var bool */
	protected $_isPhone = false;

	/** @var bool */
	protected $_isPackMobile = true;

	/** @var array */
	protected $_defaultValues = array();


	/** @return boolean */
	public function isVisibleForProfil($profil) {
		if (!$profil->isTelephone())
			return true;

		return $this->_isPhone && (!$this->_isPackMobile || Class_AdminVar::isPackMobileEnabled());
	}

	
	/** @return array */
	public function getDefaultValues() {
		if (!isset($this->_defaultValues['boite']))
			$this->_defaultValues['boite'] = null;

		if (!isset($this->_defaultValues['titre']))
			$this->_defaultValues['titre'] = '';

		return $this->_defaultValues;
	}


	/** @var string */
	public function getGroup() {
		return $this->_group;
	}


	/** @return string */
	public function getLibelle() {
		return $this->_libelle;
	}


	/** @return string */
	public function getAction() {
		return $this->_action;
	}


	/** @return bool */
	public function isPhone() {
		return $this->_isPhone;
	}


	/** @return int */
	public function getPopupWidth() {
		return $this->_popupWidth;
	}


	/** @return int */
	public function getPopupHeight() {
		return $this->_popupHeight;
	}


	/** @return array */
	public function getProperties() {
		return array('libelle' => $this->getLibelle(),
								 'groupe' => $this->getGroup(),
								 'action' => $this->getAction(),
								 'popup_width' => $this->getPopupWidth(),
								 'popup_height' => $this->getPopupHeight(),
								 'phone' => $this->isPhone());
	}
}

?>