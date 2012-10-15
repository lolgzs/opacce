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
	function reseauxSociaux($type,$id_notice,$type_doc=0)	{
		$id_profil = Class_Profil::getCurrentProfil()->getId();

		switch($type)	{
		case "notice" : 
			$notice = Class_Notice::find($id_notice);
			$url_portail="/recherche/viewnotice/clef/".$notice->getClefAlpha()."?id_profil=".$id_profil."&amp;type_doc=".$notice->getTypeDoc(); 
			$message = $notice->getTitrePrincipal();
      break;
		case "article" : 
			$url_portail="/cms/articleview/id/".$id_notice."?id_profil=".$id_profil; 
			$message = '';
			break;
		}


		return $this->links($url_portail, $message);
	}


	public function links($url_portail, $message = '') {
		// Get reseaux
		$cls=new Class_WebService_ReseauxSociaux();
		$reseaux=$cls->getReseau();

		// Html
		$html='<div style="text-align:left;margin-top:7px">';
		foreach($reseaux as $clef => $reseau) {
			$url = $this->view->url(['controller' => 'social-network',
															 'action' => 'share',
															 'on' => $clef], null, true)
				.'?url='.urlencode($url_portail)
				.'&amp;message='.urlencode($message);
			$html.=sprintf('<img src="%s" style="margin-right:3px;cursor:pointer" alt="%s" title="%s" onclick="$.getScript(\'%s\'); return false" />',
										 URL_ADMIN_IMG.'reseaux/'.$clef.'.gif',
										 sprintf("%s ".$clef, $this->translate()->_('icone')),
										 sprintf("%s ".$clef, $this->translate()->_('partager sur')),
										 $url);
		}

		$url = $this->view->absoluteUrl($url_portail);
		$html .= $this->view->permalink($url);
		$html.='</div>';
		return $html;
	}
}