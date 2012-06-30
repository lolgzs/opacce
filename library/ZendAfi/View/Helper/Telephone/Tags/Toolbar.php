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


class ZendAfi_View_Helper_Telephone_Tags_ToolBar extends ZendAfi_View_Helper_BaseHelper {
	public function ToolBar($titre,$url_retour=false,$accueil=true)	{
		$html = '<div data-role="footer" data-theme="c">';
		$html .= '<div data-role="navbar"><ul>';
		$html .= sprintf('<li><a href="%s"  data-icon="home" rel="external" data-ajax="false" data-iconpos="notext">%s</a></li>',
										 $this->view->url(array(), null, true),
										 $this->view->_('Accueil'));

		$html .= sprintf('<li><a href="#" onclick="$(\'.search-bar\').slideToggle();$(\'.navbar-search-input\').focus();return false;" data-icon="search"  data-iconpos="notext">%s</a></li>',
										 $this->view->_('Recherche'));


		if (Class_AdminVar::isPackMobileEnabled()) 
			$html .= sprintf('<li><a href="%s" data-icon="star" data-iconpos="notext" data-ajax="false">%s</a></li>',
											 $this->view->url(array('controller' => 'abonne'), null, true),
											 $this->view->_('Compte'));  

		$html .= sprintf('<li><a href="%s"  data-icon="grid" rel="external" data-ajax="false" data-iconpos="notext">%s</a></li>',
										 $this->view->url(array('id_profil' => 1), null, true),
										 $this->view->_('Complet'));


		$html .= '</ul></div>';

		$html .= '</div>';
		
		$html .= '<div class="ui-bar ui-bar-c search-bar" style="display:none;padding:0;">';
		$html .= sprintf('<form method="post" action="%s">', $this->view->url(array('controller' => 'recherche', 
																																								'action' => 'simple')));
		$html .= sprintf('<input class="navbar-search-input" data-mini="true" type="search" name="expressionRecherche" x-webkit-speech="x-webkit-speech">');

		$html .= '</form>';
		$html .= '</div>';

		return $html;
	}
}