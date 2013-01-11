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

class ZendAfi_View_Helper_RenderLieu extends Zend_View_Helper_HtmlElement {
	public function renderLieu($lieu, $map_options = null) {
		if (!$lieu)
			return '';

		$adresse = nl2br($lieu->getAdresse()).'<br/>'.$lieu->getCodePostal().' '.$lieu->getVille();
		
		return sprintf('<div class="lieu">%s %s <p>%s</p></div>',
									 $this->view->mapForLieu($lieu, $map_options),
									 $lieu->getLibelle(),
									 $adresse);
	}
}

?>