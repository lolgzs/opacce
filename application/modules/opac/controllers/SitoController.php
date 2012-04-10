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

class SitoController extends Zend_Controller_Action
{

	private $_session;
	private $_idProfil;

	function init()
	{
		$this->_session = Zend_Registry::get('session');
		$this->_idProfil = $this->_session->idProfil;
	}

	function indexAction()
	{
		$this->_redirect('opac/');
	}

	// Lire une url
	function sitoviewAction()
	{
		$class_sito = new Class_Sitotheque();
		$id_sito = (int)$this->_request->getParam('id');

		if ($id_sito > 0)
		{
			$sito = $class_sito->getSitoById($id_sito);
			if ($sito == null) {$this->_redirect('opac/index');}
			else
			{
				$this->view->sito = $sito;
				$this->view->title = $sito->TITRE;
			}
		}
	}

    // Lire les sito les plus recentes
	function viewrecentAction()
	{
		$class_sito = new Class_Sitotheque();
		$nb_sito = (int)$this->_request->getParam('nb');

        if ($nb_sito < 1)	$limit = 10; else $limit = $nb_sito;
            $sitos = $class_sito->getLastSito($limit);

			$this->view->sitos = $sitos;
			$this->view->title = $this->view->_("Derniers Sites");
			$this->renderScript('sito/viewsitos.phtml');
	}


	function viewselectionAction()
	{
		$class_sito = new Class_Sitotheque();
		$id_module = $this->_request->getParam('id_module');

		$preferences = Class_Profil::getCurrentProfil()->getModuleAccueilPreferences($id_module);
		
		$sitos = $class_sito->getSitesFromIdsAndCategories(
																				 explode('-',$preferences['id_items']),
																				 explode("-",$preferences['id_categorie']));
		$this->view->sitos = $sitos;
		$this->view->title = $this->view->_("Sélection de sites");
		$this->renderScript('sito/viewsitos.phtml');
	}

}

?>