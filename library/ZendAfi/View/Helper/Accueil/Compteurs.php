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
// OPAC3 - Class_Module_Tags
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_Compteurs extends ZendAfi_View_Helper_Accueil_Base
{

//---------------------------------------------------------------------
// Construction HTML 
//---------------------------------------------------------------------
	public function getHtml()
	{
		$html = '';
		// Compteurs
		if($this->preferences["nb_notices"]) {
			$nb_notices = str_replace(' ', '&nbsp;', $_SESSION["selection_bib"]["nb_notices"]);
			$html.=$this->translate()->_("Le catalogue contient %s notices.", $nb_notices);
		}
	
		// Valorisation du html accessible
		$this->titre = $this->preferences["titre"];
		$this->contenu=$html;
		return $this->getHtmlArray();
	}
}