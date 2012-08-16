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
class ZendAfi_View_Helper_Abonne_Paniers extends ZendAfi_View_Helper_Abonne_Abstract {
	public function abonne_paniers($user) {
		// Paniers
		$nb_paniers=count($user->getPaniers());
		$str_paniers = $this->view->_plural($nb_paniers,
																			 "Vous n'avez aucun panier de notices.",
																			 "Vous avez %d panier de notices",
																			 "Vous avez %d paniers de notices",
																			 $nb_paniers);

		return $this->tagFicheAbonne(sprintf('<a href=\'%s\'>%s</a>', 
																				 $this->view->url(['controller' => 'panier',
																													 'action' => 'index']), 
																				 $str_paniers));
	}
}

?>