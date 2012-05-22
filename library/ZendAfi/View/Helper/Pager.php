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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Pager
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Pager extends ZendAfi_View_Helper_BaseHelper
{

	function Pager($nombre,$nb_par_page,$page,$url)	{
		if(!$nombre) return;
		// Nombre de pages
		if(!$page) $page=1;
		$nb_pages=intval($nombre / $nb_par_page);
		if($nombre % $nb_par_page) $nb_pages++;
		if($nb_pages == 1) return;
		
		// Url
		if(substr($url,0,4) == "java") $javascript=true;
		else
		{
			if(strPos($url,"?") === false) $url.="?"; else $url.="&amp;";
			$pos=strPos($url,"page="); if($pos >0) $url=substr($url,0,$pos);
			$url.="page=";
		}
		
		// Bornes
		if($nb_pages < 11){$deb=1; $fin=$nb_pages;}
		else
		{
			$deb=$page-5;
			if($deb < 1) $deb=1;
			$fin=$deb+9;
			if($fin > $nb_pages) $fin=$nb_pages;
		}
		
		// Html
		if($page > 1) 
			$html=$this->getLigne($url,($page-1),"&laquo;");
		else
			$html = '';

		for($i=$deb; $i <= $fin; $i++)
		{
			if($i == $page) $href="#"; else $href=$url; 
			$html.=$this->getLigne($href,$i,$i);
		}
		if($page < $nb_pages) $html.=$this->getLigne($url,($page+1),"&raquo;");
		return $html;
	}
	
	private function getLigne($href, $page, $libelle) {
		$style = '';
		if ($href == "#") { 
			$style = ' style="font-size:larger;color:#808080"';
			$libelle = '<big>' . $libelle . '</big>';
		}

		if (substr($href, 0, 4) == "java") 
			$href = str_replace("@PAGE@", $page, $href);
		else 
			$href .= $page;

		return '<span style="padding-left:5px;padding-right:5px"><a href="'.$href.'"'.$style.'><b>'.$libelle.'</b></a></span>';
	}
}