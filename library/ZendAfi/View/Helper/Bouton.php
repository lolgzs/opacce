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
// OPAC3 :	Bouton avec picto et texte
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Bouton extends ZendAfi_View_Helper_BaseHelper
{

//------------------------------------------------------------------------------------------------------
// Main
//------------------------------------------------------------------------------------------------------
	function bouton( )
	{
		for( $i=0; $i < func_num_args(); $i++) $args[] = func_get_arg($i);
		
		$id = '';
		$picto='';
		$texte='';
		$url='';
		$largeur='';
		$align ='';
		$id_toolbar = '';
		$onclick = '';
		$idForm = 'form';
		$javascript = '';

		// Recup des paramètres
		for($i=0; $i< count($args); $i++)
		{
			$attrib = $this->splitArg($args[$i]);
			switch($attrib[0])
			{
				case"id":$id=$attrib[1]; break;
				case "picto" : $picto=URL_ADMIN_IMG.'ico/'.$attrib[1]; break;
				case "texte"  : $texte=$attrib[1]; break;
				case "url" : $url=$attrib[1]; if(strToUpper(strLeft($url,4))!="JAVA") $onclick="window.location.replace('".$url."')"; else $onclick=$url; break;
				case "largeur" : $largeur=$attrib[1]; break;
			  case "form" : $idForm=$attrib[1];break;
				case "align" : $align=' align="' . $attrib[1] .'"'; break;
				case "javascript" : $javascript="javascript:".$attrib[1]; break;
				case "id_toolbar" : $id_toolbar=$attrib[1]; break; // Pour toolbars uniquement
				case "validation" :  // Pour forms : submit ou reset
					$attrib[1]=strtoupper($attrib[1]);
					if( $attrib[1] == "V") $onclick="document.forms['@ID_FORM@'].submit(); return false;";
					if( $attrib[1] == "R") $onclick="document.forms['@ID_FORM@'].reset(); return false;";
					break;			
				case "type" : // Boutons standards
					$attrib[1]=strtoupper($attrib[1]);
					if( $attrib[1]=="V")
					{
						if(!$id) $id="975";
						$picto=URL_ADMIN_IMG . '/ico/coche_verte.gif';
						$texte=$this->translate()->_("Valider");
						if(!$largeur) $largeur="120px";	
						$onclick="document.forms['@ID_FORM@'].submit(); return false;";	
					}
					elseif($attrib[1]=="RETOUR")
					{
						$picto=URL_ADMIN_IMG . '/ico/retour.gif';
						$texte=$this->translate()->_("Retour");
					}
			}
		}
		$texte=$this->traduire($texte);
		$texte = htmlentities($texte, null , 'UTF-8');
		// Html du bouton
		$rootName = "menu" . $id_toolbar . "_item" . $id;
		$onclick=$javascript.str_replace("@ID_FORM@",$idForm,$onclick);
		if($id_toolbar > 0 ) {
		$html[]='<td class="toolbar" style="width:'.$largeur.';">';}
		$html[]='<div id="' . $rootName . '" style="margin: 0 auto; width:'.$largeur.';" class="bouton"';
		$html[]= 			' onmouseover="javascript:PicToolbarOver( this, \'' . $rootName . '\')"';
		$html[]= 			' onmouseout="javascript:PicToolbarNormal(this, \'' . $rootName . '\')"';
		$html[]=			' onmousedown="javascript:PicToolbarDown(this, \'' . $rootName . '\')"';
		$html[]=			' onmouseup="javascript:PicToolbarOver( this,  \'' . $rootName . '\')"';
		//$html[]=			' >';
		$html[]=			' onclick="'.$onclick.'">';
		$html[]= '<a href="#">';
		$html[]= '<table cellspacing="0" cellpadding="0"  '.$align.'>';
		$html[]= 		'<tr>';
		$html[]=			'<td>';
		$html[]=				'<img name="' . $rootName . '_gauche" src="' .  URL_ADMIN_IMG . '/bouton/bouton_gauche.gif" border="0" alt="" />';
		$html[]=			'</td>';
		$html[]=			'<td id="' . $rootName . '_milieu" style="background-image:url('.URL_ADMIN_IMG .'/bouton/bouton_milieu.gif); text-align:center" >';
		$html[]=				'<img src="' . $picto . '" border="0" alt="valider" />';
		$html[]=			'</td>';
		$html[]=				'<td width="100%" align="center" id="' . $rootName . '_texte" style="background-image:url('.URL_ADMIN_IMG .'/bouton/bouton_milieu.gif); text-align:center;">';
		$html[]=					$texte;
		$html[]=				'</td>';
		$html[]=			'<td>';
		$html[]=				'<img name="' . $rootName . '_droite" src="' . URL_ADMIN_IMG . '/bouton/bouton_droite.gif" border="0" alt="" />';
		$html[]=			'</td>';
		$html[]=		'</tr>';
		$html[]=	'</table>';
		$html[]=	'</a>';
		

		if($id_toolbar > 0 ) $html[]='</td>';
		$html[]='</div>';
		
		return implode("", $html);
	}
}