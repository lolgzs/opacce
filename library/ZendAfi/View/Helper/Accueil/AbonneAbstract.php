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
abstract class ZendAfi_View_Helper_Accueil_AbonneAbstract extends ZendAfi_View_Helper_Accueil_Base {
	protected $_titre_action;
	protected $_abonne;
	protected $_boite_id;

	public function getHTML() {
		$this->titre = $this->getTitre();

		if (!$this->_abonne = Class_Users::getIdentity())
			return $this->getHtmlArray() ;

		$this->contenu = sprintf('<div class="boite_%s"><ul>%s</ul></div>',$this->_boite_id,$this->getContenu());
		return $this->getHtmlArray();
	}


	public function getTitre(){
		  return $this->view->tagAnchor([ 'controller' => 'abonne',
																			'action' => $this->_titre_action],
																		$this->preferences['titre']);
	}


	public function getContenu() {
		return implode('',
									 array_map(
										 [$this, 'renderModel'],
										 $this->getModels()));
	}


	public function isBoiteVisible() {
		return Class_Users::hasIdentity();
	}


	public function shouldCacheContent() {
		return false;
	}

}

?>