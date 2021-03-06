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
class ZendAfi_View_Helper_Abonne_Prets extends ZendAfi_View_Helper_Abonne_Abstract {
	public function abonne_prets($user) {
		$fiche_sigb = $user->getFicheSigb();
		if (!isset($fiche_sigb['fiche']))
			return '';

		$nb_prets = $fiche_sigb["fiche"]->getNbEmprunts();
		$str_prets = $this->view->_plural($nb_prets,
																			"Vous n'avez aucun prêt en cours.",
																			"Vous avez %d prêt en cours",
																			"Vous avez %d prêts en cours",
																			$nb_prets);

		$nb_retards = $fiche_sigb["fiche"]->getNbPretsEnRetard();
		$str_retards = $nb_retards ? $this->view->_('(%d en retard)', $nb_retards) : '';

		$action_url = $this->view->url(['controller' => 'abonne',
																		'action' => 'prets']);
		return $this->tagFicheAbonne(sprintf('<a href=\'%s\'>%s %s</a>', 
																				 $action_url, 
																				 $str_prets, 
																				 $str_retards),
																 'prets',
																 $action_url);
	}
}

?>
