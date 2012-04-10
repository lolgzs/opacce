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
// OPAC3 :	Tag pour upload d'images
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_TagUpload extends ZendAfi_View_Helper_BaseHelper
{

	//---------------------------------------------------------------------
	// Main routine
	//---------------------------------------------------------------------
	public function TagUpload($name,$type,$filename)
	{
		// Arguments en fonction du type
		switch($type)
		{
			case "territoire":
				$args["extensions"]=".jpg,.jpeg,.gif,.png";
				$args["largeur_vignette"]="300";
				$args["hauteur_vignette"]="320";
				$args["poids"]="500";
				$args["largeur_conseil"]="";
				$args["hauteur_conseil"]="";
				$args["path"]="photobib";
				break;
			case "plan_bib":
				$args["extensions"]=".jpg,.jpeg,.gif,.png";
				$args["largeur_vignette"]="300";
				$args["hauteur_vignette"]="250";
				$args["poids"]="500";
				$args["largeur_conseil"]="";
				$args["hauteur_conseil"]="";
				$args["path"]="photobib/plans";
				break;
			case "localisation_bib":
				$args["extensions"]=".jpg,.jpeg,.gif,.png";
				$args["largeur_vignette"]="200";
				$args["hauteur_vignette"]="200";
				$args["poids"]="500";
				$args["largeur_conseil"]="200";
				$args["hauteur_conseil"]="200";
				$args["path"]="photobib/localisations";
				break;
		}
		$hauteur=$args["hauteur_vignette"]+10;
		$args["filename"]=$filename;
		$args["input_name"]=$name;

		// Verif du path et creation s'il le faut
		$path=getcwd()."/userfiles/".$args["path"];
		if(file_exists($path) == false) mkdir($path);

		// html avec iframe
		$html='<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.$filename.'">';
		$iframe=new ZendAfi_View_Helper_IframeContainer();
		$html.=$iframe->iframeContainer("100%",
																		$hauteur,
																		array('module' => 'opac',
																					'controller' => "upload",
																					'action' => "form"),
																		$args);
		return $html;
	}

}