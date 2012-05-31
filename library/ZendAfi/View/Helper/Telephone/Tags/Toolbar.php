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
		$html='<div data-theme="c" data-role="header" data-id="main-toolbar" class="toolbar">';
		  if(is_array($url_retour))
			  $url_retour = $this->view->url($url_retour);

		  if($url_retour) 
			  $html.=sprintf('<a href="%s" data-rel="back" data-icon="back">%s</a>',
											 $url_retour,
											 $this->view->_('Retour'));  

		$html.='<h1>'.$titre.'</h1>';

		if($accueil) 
			$html.=sprintf('<a href="%s" data-icon="home">%s</a>',
										 $this->view->url(array(), null, true),
										 $this->view->_('Accueil')); 
		$html.='</div>';
		return $html;
	}
}