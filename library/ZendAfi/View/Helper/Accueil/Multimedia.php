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
class ZendAfi_View_Helper_Accueil_Multimedia extends ZendAfi_View_Helper_Accueil_AbonneAbstract  {
	protected $_titre_action = 'multimedia-hold-location';
	protected $_boite_id = 'multimedia';

	public function getModels() {
    return $this->_abonne->getFutureMultimediaHolds();
	}


	public function getContenu() {
		return parent::getContenu().sprintf(
			'<div><a href="%s">%s</a></div>',
			$this->view->url(['controller' => 'abonne',
												'action' => $this->_titre_action],
											 null, true),
			$this->view->_('Réserver un poste multimedia'));
	}


	public function renderModel($reservation) {
		return sprintf('<li><a href="%s">[%s] %s</a></li>',
									 $this->view->url(['controller' => 'abonne',
																		 'action' => 'multimedia-hold-view',
																		 'id' => $reservation->getId()],
																		null,true),
									 $reservation->getLibelleBib(),
									 strftime('%d/%m/%Y %Hh%M', $reservation->getStart()));

	}


	public function isBoiteVisible() {
		return (parent::isBoiteVisible() && Class_AdminVar::isMultimediaEnabled());
	}


}

?>