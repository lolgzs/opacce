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
// OPAC3 :	Bouton image
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_BoutonIco extends ZendAfi_View_Helper_BaseHelper
{
	//----------------------------------------------------------------------------------
	// Main (arguments variables)
	//----------------------------------------------------------------------------------
	function boutonIco()
	{
		for( $i=0; $i < func_num_args(); $i++) $args[] = func_get_arg($i);

		// Recup des paramètres
		for($i=0; $i< count($args); $i++)
		{
			$attrib = $this->splitArg($args[$i]);
			switch($attrib[0])
			{
				case "picto" : $picto=URL_ADMIN_IMG.$attrib[1]; break;
				case "url" : $url=BASE_URL. "/". $attrib[1];
				case "bulle" : $bulle=$this->traduire($attrib[1]);
				case "type" : // Types prédéterminés
				$type=strtoupper($attrib[1]);

				if($type=="ADD")
				{
					$picto=URL_ADMIN_IMG."ico/add.gif";
					$bulle=$this->traduire("Ajouter");
				}
				elseif($type=="EDIT")
				{
					$picto=URL_ADMIN_IMG."ico/edit.gif";
					$bulle=$this->traduire("Modifier");
				}
				elseif($type=="DEL")
				{
					$picto=URL_ADMIN_IMG."ico/del.gif";
					$bulle=$this->traduire("Supprimer");
				}
				elseif($type=="CONFIRM")
				{
					$picto=URL_ADMIN_IMG."ico/coche_verte.gif";
					$bulle=$this->traduire("Confirmer");
				}
				elseif($type=="VALIDATE")
				{
					$picto=URL_ADMIN_IMG."ico/coche_verte.gif";
					$bulle=$this->traduire("Valider");
				}
				elseif($type=="TEST")
				{
					$picto=URL_ADMIN_IMG."ico/tester.gif";
					$bulle=$this->traduire("Tester");
				}
				elseif($type=="SHOW")
				{
					$picto=URL_ADMIN_IMG."ico/show.gif";
					$bulle=$this->traduire("Visualiser");
				}
				elseif($type=="MAIL")
				{
					$picto=URL_ADMIN_IMG."ico/mail.png";
					$bulle=$this->traduire("Envoyer par mail");
				}
			}
		}
		
		// Html du bouton
		if(isset($url)) $html[]='<a href="'. $url .'">';
		$html[]='<img class="ico" src="'. $picto .'"';
		if(isSet($bulle)) $html[]=' alt="'.$bulle.'" title="'.$bulle.'"';
		if($type=="DEL") $html[]=' onclick="javascript:if(!confirm(\''.$this->traduire('Êtes vous sûr de vouloir supprimer cet élément ?').'\')) return false;"';
		$html[]=' />';
		if(isSet($url)) $html[]='</a>';

		return implode("", $html);
	}
}