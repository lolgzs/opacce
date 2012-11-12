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
class ZendAfi_View_Helper_Accueil_Reservations extends ZendAfi_View_Helper_Accueil_Base {

	public function getHTML() {
    $this->titre = $this->view->tagAnchor($this->view->url([ 'controller' => 'abonne',
																														 'action' => 'reservations'
		]),$this->preferences['titre']);
		$user = Class_Users::getIdentity();
		if (!isset($user))
			return $this->getHtmlArray() ;

		$liste_reservations= sprintf('<ul>%s</ul>',
												 implode('',
																 array_map(
																	 function($reservation) {
																		 $start_li='<li>';
																		 $etat='['.$reservation->getEtat().'] ';
																		 $tag_anchor = $this->view->tagAnchor($this->view->urlNotice($reservation->getNoticeOPAC()),
																																					$reservation->getTitre());
																		 return $start_li.$etat.$tag_anchor.'</li>'; 
																	 },
																	 $user->getReservations())));


		$this->contenu = sprintf('<div class="boite_reservations">%s</div>',
														 $liste_reservations);

		return $this->getHtmlArray();
	}


	public function isBoiteVisible() {
		return null !=  Class_Users::getIdentity();
	}


	public function shouldCacheContent() {
		return false;
	}
	
}

?>