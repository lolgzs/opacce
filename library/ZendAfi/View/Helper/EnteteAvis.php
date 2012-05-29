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

class ZendAfi_View_Helper_EnteteAvis extends ZendAfi_View_Helper_BaseHelper {
	public function enteteAvis($avis) {
		$auteur = $avis->getUserName();
		$date_avis = $avis->getReadableDateAvis();
		$url_avis = $this->view->url(array("controller" => "blog","action" => "viewavis", "id" => $avis->getId()));
		$entete = $avis->getEntete();

		return sprintf('<a class="entete_critique" href="%s">%s %s<div class="auteur_critique">%s - %s</div></a>',
									 $url_avis,
									 $this->view->noteImg($avis->getNote()),
									 $entete,
									 $auteur,
									 $date_avis);
	}
}

?>