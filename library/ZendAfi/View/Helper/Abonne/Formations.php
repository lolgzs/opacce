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
class ZendAfi_View_Helper_Abonne_Formations extends ZendAfi_View_Helper_Abonne_Abstract {
	public function abonne_formations($user) {
		if (!Class_AdminVar::isFormationEnabled() || !$user->hasRightSuivreFormation())
			return '';

		$html = $this->view->tagAnchor($this->view->url(['controller' => 'abonne',
																										 'action' => 'formations'], 
																										null, 
																										true),
																	 $this->view->_("S'inscrire à une formation"));

		$html .= '<ul>';
	
		foreach ($user->getSessionFormations() as $session) {
			$html .= sprintf('<li><a href="%s">%s, %s</li>', 
											 $this->view->url(['controller' => 'abonne',
																				 'action' => 'detail_session',
																				 'id' => $session->getId(),
																				 'retour' => 'fiche'],
																				null,
																				true),
											 $session->getLibelleFormation(),
											 $this->view->humanDate($session->getDateDebut(), 'd MMMM YYYY'));
		}

		$html .= '</ul>';

		return $this->tagFicheAbonne($html);
	}
}

?>