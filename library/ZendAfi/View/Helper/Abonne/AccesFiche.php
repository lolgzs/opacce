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
class ZendAfi_View_Helper_Abonne_AccesFiche extends Zend_View_Helper_HtmlElement {
	public function abonne_accesfiche($user) {
		$fiche_sigb = $user->getFicheSigb();
		if (array_key_exists("fiche", $fiche_sigb)) {
			try {
				if ($popup_url = $fiche_sigb["fiche"]->getUserInformationsPopupUrl($user)) 
					return $this->divAbonneTitre(sprintf('<a onclick="openIFrameDialog(\'%s\');">%s</a>',
																							 $popup_url,
																							 $this->view->boutonIco("type=edit").$this->view->_(' Modifier ma fiche')),
																			 $user);

			} catch (Exception $e) {
				return $this->divAbonneTitre(sprintf('<div class="error">Erreur VSmart: %s</div>', $e->getMessage()),
																		 $user);
			}
		}


		return $this->divAbonneTitre($this->view->tagAnchor(['controller' => 'abonne', 'action' => 'edit'],
																												$this->view->boutonIco("type=edit").$this->view->_(' Modifier ma fiche')),
																 $user);
	}


	public function divAbonneTitre($html, $user) {
		return sprintf('<div class="abonneTitre">%s<span>%s</span></div>', 				
									 $user->getNomAff(),
									 $html);
	}
}

?>
