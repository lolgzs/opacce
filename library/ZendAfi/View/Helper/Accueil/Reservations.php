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
class ZendAfi_View_Helper_Accueil_Reservations extends ZendAfi_View_Helper_Accueil_AbonneAbstract  {
	protected $_titre_action = 'reservations';
	protected $_boite_id = 'reservations';

	public function getModels() {
		return $this->_abonne->getReservations();
		
	}


	public function renderModel($reservation) {

		$start_li='<li>';
		$etat='['.$reservation->getEtat().'] ';
		$tag_anchor = $this->view->tagAnchor($this->view->urlNotice($reservation->getNoticeOPAC()),
																				 $reservation->getTitre());
		return $start_li.$etat.$tag_anchor.'</li>'; 
	}
}

?>