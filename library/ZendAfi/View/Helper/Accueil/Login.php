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
// OPAC3 - Class module Recherche Simple
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_Login extends ZendAfi_View_Helper_Accueil_Base {

	/* Désactive le cache: sinon la boîte n'est pas à jour si on se connecte / déconnecte */
	public function shouldCacheContent() { return false; }


//---------------------------------------------------------------------
// Construction du Html
//---------------------------------------------------------------------
	public function getHtml()	{
		$this->titre = $this->preferences['titre'];
		$this->contenu = "<div id='boite_login'>".
			                    $this->view->partial('auth/boitelogin.phtml',
																							 array("preferences" => $this->preferences,
																										 "boite_login_message" => $this->view->boite_login_message,
																										 "id_module" => $this->id_module)).
                     "</div>";

		return $this->getHtmlArray();
	}

}