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
// OPAC3 : Facettes liste de notices
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Facettes extends ZendAfi_View_Helper_BaseHelper
{

	function facettes($facettes,$preferences,$url)
	{
		// Parametres
		if(!$facettes) return;
		extract($preferences);
		$facettes_codes="T".$facettes_codes;
		$html='<div class="facette">';
		if(strPos($url,"?") === false) $url.="?"; else $url.="&amp;";
		$url.="facette=";
		
		// Html
		foreach($facettes as $type => $valeurs)
		{
			if($facettes_codes and strpos($facettes_codes,$type) === false) continue;
			$html.='<table width="100%">';
			$html.='<tr><td colspan="2" class="facette_titre"><b>'.$valeurs["titre"].'</b></td></tr>';
			$fin_div="";
			for($i=1; $i< count($valeurs); $i++)
			{
				$html.='<tr><td style="vertical-align:top;padding-top:3px"><img border="0" src="'.URL_IMG."puce_facette.gif".'" alt="puce" /></td>';
				$html.='<td style="vertical-align:top;width:100%">';
				$html.='<a class="facette" href="'.$url.$valeurs[$i]["id"].'">'.$valeurs[$i]["libelle"].' <font color="black" size="1pt">('.$valeurs[$i]["nombre"].')</font></a>';
				$html.='</td></tr>';
				if($i == $facettes_nombre and count($valeurs) > ($facettes_nombre+1))
				{
					$fin_div='</div>';
					$onclick="document.getElementById('".$type."').style.display='block';document.getElementById('".$type."_msg').style.display='none';";
					$html.='<tr id="'.$type.'_msg"><td colspan="2" onclick="'.$onclick.'" style="cursor:pointer">Afficher plus de facettes...</td></tr>';
					$html.='</table><div id="'.$type.'" style="display:none"><table width="100%">';
				}
			}
			$html.='</table>'.$fin_div;
		}
		$html.='</div>';
		return $html;
	}
}