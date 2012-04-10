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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Sélecteur de la langue du site
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_Langue extends ZendAfi_View_Helper_Accueil_Base {
	public function getHTML() {
    $this->titre = $this->preferences['titre'];

		$this->contenu = '';

		$langues = Class_AdminVar::getLangues();
		foreach ($langues as $langue) {
			$url = $this->view->url(array('language' => $langue));
			$this->contenu .= sprintf('<a href="%s"><img src="%s" alt="%s" /></a>', 
																$url, 
																URL_ADMIN_IMG.'flags/'.$langue.'.png', 
																$langue);
		}


		$this->contenu = sprintf('<div class="country_flag">%s</div>', $this->contenu);
		return $this->getHtmlArray();
	}
}

?>