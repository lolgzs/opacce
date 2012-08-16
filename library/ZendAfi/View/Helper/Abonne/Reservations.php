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
class ZendAfi_View_Helper_Abonne_Reservations extends ZendAfi_View_Helper_Abonne_Abstract {
	public function abonne_reservations($user) {
		$fiche_sigb = $user->getFicheSigb();
		if (!isset($fiche_sigb['fiche']))
			return '';

		$nb_resas = $fiche_sigb["fiche"]->getNbReservations();
		$str_resas = $this->view->_plural($nb_resas,
																		 "Vous n'avez aucune réservation en cours.",
																		 "Vous avez %d réservation en cours",
																		 "Vous avez %d réservations en cours",
																		 $nb_resas);
		
		return $this->tagFicheAbonne(sprintf("<a href='%s'>%s</a>", 
																				 $this->view->url(['controller' => 'abonne',
																													 'action' => 'reservations']),
																				 $str_resas),
																 'reservations');
	}
}

?>
