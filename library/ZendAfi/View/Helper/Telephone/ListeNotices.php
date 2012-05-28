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
// OPAC3 :	Liste de notices
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Telephone_ListeNotices extends ZendAfi_View_Helper_BaseHelper {
	public function urlNotice($id, $type_doc) {
		return $this->view->url(array('controller' => 'recherche', 
																	 'action' => 'viewnotice', 
																	 'id' => $id, 
																	 'type_doc' => $type_doc));
	}


	public function listeNotices($notices, $nombre_resultats, $page, $preferences) 	{
		// Message d'erreur
		if (array_key_exists('statut', $notices) && ($notices["statut"]=="erreur")) {
			$html='<h2>'.$notices["erreur"].'</h2>';
			if($notices["nb_mots"] > 1) {
				$html.=sprintf('<a href="%s?pertinence=1">&raquo;&nbsp;%s</a>',
											 $this->view->url(array('controller' => 'recherche', 
																							'action' => 'simple')),
											 $this->translate()->_('Elargir la recherche sur tous les mots'));
			}
			return $html;
		}
		
		// Nombre de resultats et n° de page
		$html='<table><tr><td align="left" width="100%">';
		if(!intval($page)) $page=1;
    if(!$nombre_resultats) $html.= $this->translate()->_('Aucune notice trouvée');
    if($nombre_resultats == 1) $html.=$this->translate()->_('1 notice trouvée');
    if($nombre_resultats > 1) $html.=$this->translate()->_('%s notices trouvées', $nombre_resultats).'</td><td align="right">page&nbsp;'.$page;
    $html.='</td></tr></table>';
    if(!$nombre_resultats) return $html;
    
    // Liste en fonction du format
		$html.=$this->listeVignette($notices,$preferences["liste_codes"]);
    return ($html);
	}
//------------------------------------------------------------------------------------------------------
// Format 3 : VIGNETTE
//------------------------------------------------------------------------------------------------------
	public function listeVignette($data,$champs) {	
		$html='<div class="liste">';
		$html.='<ul>';
		foreach($data as $notice)	{
			$notice = array_merge(array('auteur_principal' => ''), 
														$notice);

			if (!$titre = $notice['T']) $titre = $notice['titre_principal'];
			if (!$auteur = $notice['A']) $auteur = $notice['auteur_principal'];

			$html.='<li class="lien">';
			$html.= sprintf('<a href="%s">',
											$this->urlNotice($notice["id_notice"], $notice["type_doc"]));
			$html.='<table cellspacing="0" cellpadding="0">';

			// Image
			$img=Class_WebService_Vignette::getUrl($notice["id_notice"]);
			$html.='<tr><td width="70px" valign="top"><img src="'.$img["vignette"].'" width="60px" style="cursor:pointer;"></td>';

			// Titre / auteur principal
			$html.='<td valign="top">'.$titre.BR.$auteur;

			// Données variables
			for($i=0; $i < strlen($champs); $i++)
			{
				$champ=$champs[$i];
				if($champ=="T" or $champ=="A") continue;
				if(trim($notice[$champ]) == '') continue;
				$html.=BR.Class_Codification::getNomChamp($champ)." : ";
				$html.=$notice[$champ];
				$html.='</td>';
			}
			$html.='</tr></table>';
			$html.='</a></li>';
		}
		$html.='</ul></div>';
		return $html;
	}	
}