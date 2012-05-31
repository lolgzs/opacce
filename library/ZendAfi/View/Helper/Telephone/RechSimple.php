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
// OPAC3 - Recherche
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Telephone_RechSimple extends ZendAfi_View_Helper_Accueil_Base {
		
//---------------------------------------------------------------------
// Main routine
//---------------------------------------------------------------------  
	public function getHtml()	{
		$html = sprintf('<form method="post" action="%s" data-mini="true" class="ui-grid-a">', $this->view->url(array('controller' => 'recherche', 
																																							 'action' => 'simple')));

		$html .= '<div class="ui-block-a">';
		$html .= sprintf('<input data-mini="true" type="text" placeholder="%s" name="expressionRecherche" x-webkit-speech="x-webkit-speech">',
										 $this->translate()->_('Recherche'));
		$html .= '</div>';

		$html .= '<div class="ui-block-b">';
		$html .= '<button data-mini="true" type="submit" value="ok" >';
		$html .= '</div>';

		$html .= '</form>';

		$this->contenu = $html;
		return $this->getHtmlArray();

	}
}