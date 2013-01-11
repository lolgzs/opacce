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
class ZendAfi_View_Helper_Telephone_Pager extends ZendAfi_View_Helper_BaseHelper {

	//---------------------------------------------------------------------
	// Main routine
	//---------------------------------------------------------------------
	public function pager($nombre,$nb_par_page,$page,$url)
	{
		if(!$nombre) return;
		if(!$nb_par_page) $nb_par_page = 10;
		// Nombre de pages
		if(!$page) $page=1;
		$nb_pages=intval($nombre / $nb_par_page);
		if($nombre % $nb_par_page) $nb_pages++;
		if($nb_pages == 1) return;

		// Url
		if(strPos($url,"?") === false) $url.="?"; else $url.="&";
		$pos=strPos($url,"page="); if($pos >0) $url=substr($url,0,$pos);
		$url.="page=";
		
		// Pager
		$html='<div class="ui-bar" data-theme="c" data-role="footer" data-id="pager" data-position="fixed">';
		$html.='<div data-role="controlgroup" data-type="horizontal">';

		$html.=sprintf('<a href="%s" data-role="button" data-icon="arrow-l" %s>%s</a>',
									 $url.($page-1),
									 $page > 1 ? '' : 'class="ui-disabled"',
									 $this->translate()->_('Page précédente'));

		$html.=sprintf('<a href="%s" data-role="button" data-icon="arrow-r" data-iconpos="right" %s>%s</a>',
									 $url.($page+1),
									 ($page != $nb_pages) ? '' : 'class="ui-disabled"',
									 $this->translate()->_('Page suivante'));

		$html.='</div></div>';
		return $html;
	}
}