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
class ZendAfi_View_Helper_Accueil_Prets extends ZendAfi_View_Helper_Accueil_Base {

	public function getHTML() {
    $this->titre = $this->view->tagAnchor($this->view->url([ 'controller' => 'abonne',
																														 'action' => 'prets'
		]),$this->preferences['titre']);
		$user = Class_Users::getIdentity();
		if (!isset($user))
			return $this->getHtmlArray() ;


	

		$liste_prets= sprintf('<ul>%s</ul>',
												 implode('',
																 array_map(
																	 function($emprunt) {
																		 $start_li='<li>';
																		 if ($emprunt->enRetard())
																			 $start_li='<li class="pret_en_retard">';
																		 $date_retour='<span class="date_retour"> ['.$emprunt->getDateRetour().']</span> ';
																		 $tag_anchor = $this->view->tagAnchor($this->view->urlNotice($emprunt->getNoticeOPAC()),
																																					$emprunt->getTitre());
																		 return $start_li.$date_retour.$tag_anchor.'</li>'; 
																	 },
																	 $user->getEmprunts())));


		$this->contenu = sprintf('<div class="boite_prets">%s</div>',
														 $liste_prets);

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