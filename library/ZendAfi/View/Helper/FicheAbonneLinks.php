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
class ZendAfi_View_Helper_FicheAbonneLinks extends Zend_View_Helper_HtmlElement {
	public function ficheAbonneLinks($nb_prets, $nb_retards, $nb_reservations) {
		$html = '';
		if ($nb_prets)
			$html .= $this->view->tagAnchor(array('controller' => 'abonne', 'action' => 'prets'),
																			'&raquo;&nbsp;'.
																			$this->view->_plural($nb_prets,
																													 "",
																													 "%d prêt en cours",
																													 "%d prêts en cours",
																													 $nb_prets).
																			$this->view->_plural($nb_retards,
																													 "",
																													 ", %d en retard",
																													 ", %d en retard",
																													 $nb_retards));

		if ($nb_retards)
			$html = '<span class="pret_en_retard">'.$html.'</span>';

		if ($nb_reservations)
			$html .= $this->view->tagAnchor(array('controller' => 'abonne', 'action' => 'reservations'),
																			'&raquo;&nbsp;'.
																			$this->view->_plural($nb_reservations,
																													 "",
																													 "%d réservation",
																													 "%d réservations",
																													 $nb_reservations));
		return $html;
	}
}

?>