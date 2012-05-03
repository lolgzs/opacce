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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Objets java
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

class JavaController extends ZendAfi_Controller_IFrameAction {

//-------------------------------------------------------------------------------
// Met le layout
//-------------------------------------------------------------------------------
	function init()	{
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('iframe.phtml');
	}


//-------------------------------------------------------------------------------
// Menu images avec panneaux horizontaux
//-------------------------------------------------------------------------------
	function menuimageAction()
	{
		// rien ici -> tout est fait par le view-helper
	}

//-------------------------------------------------------------------------------
// Kiosque de notices
//-------------------------------------------------------------------------------
	function kiosqueAction()	{
		// Preferences du module
		$id_module=$this->_getParam("id_module");
		$preferences=$this->view->profil->getModuleAccueilPreferences($id_module);
		$this->view->preferences=$preferences;
		
		// Lire les notices
		$catalogue=new Class_Catalogue();
		$this->view->notices=$catalogue->getNoticesByPreferences($preferences,"url");

		// Redirection vers la bonne vue
		$vue="/java/".$this->_getParam("vue").".phtml";
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript($vue);
	}


	function kiosquedescAction() {
		$this->getHelper('ViewRenderer')->setNoRender();
		echo $this->view->_('Kiosque de notices');
	}
}