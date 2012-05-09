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
// OPAC3 - News
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Telephone_News extends ZendAfi_View_Helper_Accueil_News
{
		
//---------------------------------------------------------------------
// Main routine
//---------------------------------------------------------------------  

	protected function getArticles($articles) {
		if (!$articles) return "";
  	$html = "<div class='liste'><ul class='articles'>";
		
		foreach($articles as $article) {
			$fallback_img = URL_ADMIN_IMG.'supports/vignette_vide.gif';
			$vignette_url = $article->getFirstImageURL();
			$html.= sprintf("<li class='lien'><a href='%s'><img src='%s' onerror='this.src=\"%s\"' alt='%s'/><span>%s</span></a></li>",
											$this->view->url($article->getUrl()),
											$vignette_url ? $vignette_url : $fallback_img,
											$fallback_img,
											htmlentities($article->getTitre(), ENT_QUOTES),
											$article->getTitre());
		}

		return $html."</ul></div>";
	}

	protected function getHtmlTitre(){
		return $this->preferences['titre'];
	}
}

?>