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
// OPAC3 :	Tag de choix de pictogramme pour les menus

//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_ChoixPictogramme extends ZendAfi_View_Helper_BaseHelper
{
	private $hauteur_div="95px";		// Hauteur de la liste
	private $largeur_div="180px";		// Largeur de la liste
	private $nb_par_ligne=5;    		// Nombre de pictos par ligne
	
//---------------------------------------------------------------------
// Main routine
//---------------------------------------------------------------------
	public function ChoixPictogramme($type,$name,$id_profil,$picto_selected,$scroll=false)
	{
		// Liste des pictos
		$class_profil=Class_Profil::getCurrentProfil();
		if($type=="menu")	{
			if ($class_profil->isTelephone()) {
				$this->largeur_div="230px"; 
				$this->hauteur_div="150px";
			}
		}
		if($type=="plan_animation")
		{
			$path_ico='/public/admin/images/animation_plan/';
			$this->largeur_div="500px";
			$this->hauteur_div="500px";
		}
		else 
			$path_ico=$class_profil->getPathTheme()."images/".$type."/";
		$dir=opendir(".".$path_ico);
    while(false !== ($file = readdir($dir))) 
    {
    	$extension=substr($file,-4);
    	if($extension != ".gif" and $extension !=".png" and $extension !=".jpg") continue;
    	$pictos[]=$file;
    }
    closedir($dir);
		sort($pictos);
    $path_ico=BASE_URL.$path_ico;
		
		$html='<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.$picto_selected.'"/>';
		if($scroll==true) $scroll='window.scrollTo(0,2000);';
		$onclick="oListe=getId('liste_picto_".$name."'); if(oListe.style.display=='block') oListe.style.display='none'; else oListe.style.display='block';".$scroll;
		$onmouseout="getId('liste_picto_".$name."').style.display='none'";
		$html.='<img id="select_'.$name.'" src="'.$path_ico.$picto_selected.'" style="cursor:pointer;padding-right:'.$this->largeur_div.'" onclick="'.$onclick.'" onmouseout="'.$onmouseout.'" alt="'.$name.'"/>';
		$html.='<div id="liste_picto_'.$name.'" style="display:none;position:absolute;" onmouseover="this.style.display=\'block\'" >';
		$html.='<div style="width:'.$this->largeur_div.';height:'.$this->hauteur_div.';border:1px solid #C8C8C8;background-color:#FFFFFF;overflow:auto;margin-bottom:10px">';
		$html.='<table cellspacing="10"><tr>';
	
		$nb = 0;
		foreach($pictos as $picto)
		{
			if($nb >= $this->nb_par_ligne) {$html.='</tr><tr>'; $nb=0;}
			$onclick="getId('".$name."').value=this.id;getId('select_".$name."').src=this.src;getId('liste_picto_".$name."').style.display='none';";
			$html.='<td align="center"><img id="'.$picto.'" src="'.$path_ico.$picto.'" onclick="'.$onclick.'" style="cursor:pointer" alt="'.$name.'"/></td>';
			$nb++;
		}
		$html.='</tr></table></div></div>';
		return $html;
	}
		
}