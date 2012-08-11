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
// OPAC3 : Reseaux sociaux (facebook, twitter,etc...)
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_ReseauxSociaux extends ZendAfi_View_Helper_BaseHelper
{
	
	//------------------------------------------------------------------------------------------------------
	// Main routine
	//------------------------------------------------------------------------------------------------------
	function reseauxSociaux($type,$id_notice,$type_doc=0)
	{
		// Url en fonction du type
		$notice = Class_Notice::find($id_notice);
		$id_profil = Class_Profil::getCurrentProfil()->getId();
		switch($type)	{
		case "notice" : $url_portail="/recherche/viewnotice/clef/".$notice->getClefAlpha()."?id_profil=".$id_profil."&amp;type_doc=".$notice->getTypeDoc(); break;
		case "article" : $url_portail="/cms/articleview/id/".$notice->getId()."?id_profil=".$id_profil; break;
		}


		return $this->links($url_portail);
	}


	public function links($url_portail) {
		// Get reseaux
		$cls=new Class_WebService_ReseauxSociaux();
		$reseaux=$cls->getReseau();

		// Html
		$html='<div style="text-align:left;margin-top:7px">';
		foreach($reseaux as $clef => $reseau)
		{
			try {
				$url=$cls->getUrl($clef,$url_portail);
				$html.=sprintf('<img src="%s" style="margin-right:3px;cursor:pointer" alt="%s" title="%s" onclick="window.open(\'%s\',\'_blank\',\'location=yes, width=800, height=410\');" />',
											 URL_ADMIN_IMG.'reseaux/'.$clef.'.gif',
											 sprintf("%s ".$clef, $this->translate()->_('icone')),
											 sprintf("%s ".$clef, $this->translate()->_('partager sur')),
											 $url);
			} catch (Exception $e) {

			}
		}

		$url="http://".$_SERVER["HTTP_HOST"].BASE_URL.$url_portail;
		$html .= $this->view->permalink($url);
		$html.='</div>';
		return $html;
	}
}