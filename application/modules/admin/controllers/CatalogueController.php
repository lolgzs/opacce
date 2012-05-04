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
// OPAC3 - Controleur CATALOGUES DYNAMIQUES
//////////////////////////////////////////////////////////////////////////////////////////

class Admin_CatalogueController extends Zend_Controller_Action
{
		
 //----------------------------------------------------------------------------------
 // Init du controleur
 //----------------------------------------------------------------------------------
	function init()
	{
		
	}
   
	//----------------------------------------------------------------------------------
	// Liste des catalogues
	//----------------------------------------------------------------------------------
	function indexAction()
	{
		$class_catalogue = new Class_Catalogue();
		$catalogues=$class_catalogue->getCatalogue(0);
		$this->view->catalogues = $catalogues;
		if(!$catalogues) $this->view->message="Aucun catalogue n'a été trouvé";

		$this->view->titre = "Définition des catalogues dynamiques";
	}
	
	//----------------------------------------------------------------------------------
	// Tester un catalogue
	//----------------------------------------------------------------------------------
	function testerAction()
	{
		$id_catalogue=(int)$this->_getParam("id_catalogue");
		if(!$id_catalogue) $this->_redirect("admin/catalogue/index");
		
		// Lire les notices
		$catalogue = Class_Catalogue::getLoader()->find($id_catalogue);
		$ret=$catalogue->getTestCatalogue();
		
		// Variables de vue
		$this->view->requete=$ret["requete"];
		$this->view->temps_execution=$ret["temps_execution"]." secs.";
		$this->view->nb_notices=$ret["nb_notices"];
		$this->view->avec_vignettes=$ret["avec_vignettes"];
		$this->view->notices=$ret["notices"];
		$this->view->id_catalogue=$id_catalogue;
		$this->view->titre = 'Test du catalogue: '.fetchOne("select LIBELLE from catalogue where id_catalogue=$id_catalogue");
	}
	
	//----------------------------------------------------------------------------------
	// Ajout d'un catalogue
	//----------------------------------------------------------------------------------
	function addAction() {
		$catalogue = Class_Catalogue::getLoader()
			->newInstance()
			->setLibelle("** nouveau catalogue **");

		if ($this->isSaved($catalogue)) {
			$this->_redirect("admin/catalogue/index");
			return;
		}

		$this->view->catalogue = $catalogue;
		$this->view->titre = "Ajout de catalogue";
		$this->getHelper('ViewRenderer')->renderScript('catalogue/form.phtml');
	}
	

	//----------------------------------------------------------------------------------
	// modification d'un catalogue
	//----------------------------------------------------------------------------------
	function editAction()	{
		$id_catalogue=(int)$this->_getParam("id_catalogue");
		if (!$catalogue = Class_Catalogue::getLoader()->find($id_catalogue)) {
			$this->_redirect("admin/catalogue/index");
			return;
		}

		if ($this->isSaved($catalogue)) {
			$this->_redirect("admin/catalogue/index");
			return;
		}

		$this->view->titre="Modification de catalogue";
		$this->view->catalogue=$catalogue;
		$this->getHelper('ViewRenderer')->renderScript('catalogue/form.phtml');
	}


	public function isSaved($catalogue) {
		if (!$this->_request->isPost()) 
			return false;

		return $catalogue
			->updateAttributes($this->_request->getPost())
			->save();
	}

	//----------------------------------------------------------------------------------
	// Suppression d'un catalogue
	//----------------------------------------------------------------------------------
	function deleteAction()	{
		$id_catalogue=(int)$this->_getParam("id_catalogue");
		if ($catalogue = Class_Catalogue::getLoader()->find($id_catalogue))
			$catalogue->delete();

		$this->_redirect("admin/catalogue/index");
	}
}