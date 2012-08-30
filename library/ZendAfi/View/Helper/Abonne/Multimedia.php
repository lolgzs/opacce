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
class ZendAfi_View_Helper_Abonne_Multimedia extends ZendAfi_View_Helper_Abonne_Abstract {
	public function abonne_multimedia($user) {
		if (!Class_AdminVar::isMultimediaEnabled()) 
			return '';

		$action_url = $this->view->url(['controller' => 'abonne',
																		'action' => 'multimedia-hold-location'], 
																	 null, true);

		$html =  $this->view->tagAnchor($action_url,
																		$this->view->_("Réserver un poste multimédia"));
		$html .= '<ul>';
		foreach ($user->getFutureMultimediaHolds() as $hold) {
			$device = $hold->getDevice();
			$location = $device->getGroup()->getLocation();
			$html .= sprintf('<li><a href="%s">%s, %s %s, %s</li>', 
											 $this->view->url(['controller' => 'abonne',
																				 'action' => 'multimedia-hold-view',
																				 'id' => $hold->getId()],
																				null,	true),
											 $device->getLibelle() . ' - ' . $device->getOs(),
											 strftime('le %d %B %Y à %Hh%M', $hold->getStart()),
											 sprintf('pour %smn', (($hold->getEnd() - $hold->getStart()) / 60)),
											 $location->getLibelleBib());
		}

		$html .= '</ul>';

		return $this->tagFicheAbonne($html, 'multimedia', $action_url);
	}
}

?>