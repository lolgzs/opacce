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
// OPAC3 - Controleur pour les recherches OAI
//////////////////////////////////////////////////////////////////////////////////////////

class RechercheoaiController extends Zend_Controller_Action
{
  								
//------------------------------------------------------------------------------------------------------
// Initialisation du controler
//------------------------------------------------------------------------------------------------------
	function init()
	{
		
	}

//------------------------------------------------------------------------------------------------------
// INDEX
//------------------------------------------------------------------------------------------------------
	function indexAction()
	{
		$cls_oai=new Class_NoticeOAI();
		$this->view->statut=$_REQUEST["statut"];
		$this->view->id_entrepot=$_REQUEST["id_entrepot"];
		$this->view->entrepots =$cls_oai->getEntrepots();
	}

//------------------------------------------------------------------------------------------------------
// RESULTAT DE LA RECHERCHE
//------------------------------------------------------------------------------------------------------
	function resultatAction()
	{
    $cls_oai=new Class_NoticeOAI();
		$this->view->entrepots =$cls_oai->getEntrepots();
		$this->view->expressionRecherche = $_REQUEST["expressionRecherche"];
		$this->view->id_entrepot=$_REQUEST["id_entrepot"];
		$resultat=$cls_oai->recherche($_REQUEST);
		if($resultat["statut"]=="erreur") $this->view->erreur=$resultat["erreur"];
		else
		{
			$this->view->recherche=$_REQUEST["expressionRecherche"];
			$this->view->notices=$cls_oai->getPageResultat($resultat["req_liste"], $this->_getParam('page'));
			$this->view->nombre=$resultat["nombre"];
			$this->view->page = $this->_getParam('page');
			$this->view->url_retour=BASE_URL."/rechercheoai/resultat?expressionRecherche=".$_REQUEST["expressionRecherche"]."&id_entrepot=".$_REQUEST["id_entrepot"];
		}
	}


	function viewnoticeAction() {
		$this->view->notice = Class_NoticeOAI::getLoader()->find($this->_getParam('id'));
	}

}
