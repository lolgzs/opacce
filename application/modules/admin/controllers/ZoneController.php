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
class Admin_ZoneController extends Zend_Controller_Action
{

	//---------------------------------------------------------------------
	// Liste des zones
	//---------------------------------------------------------------------
	function indexAction()	{
		$this->view->titre = "Gestion des territoires";
		$this->view->zone_array = Class_Zone::getLoader()->findAll();
	}


	protected function _checkPost($zone) {
		$this->view->zone = $zone;

		if (!$this->_request->isPost()) 
			return;

		if ($zone->updateAttributes($_POST)->save())
				$this->_redirect('admin/zone/index');
	}

	//---------------------------------------------------------------------
	// Ajouter une zone
	//---------------------------------------------------------------------
	function addAction()	{
		$zone = new Class_Zone();
		$this->_checkPost($zone);
		$this->view->titre = 'Ajouter un territoire';
	}

	//---------------------------------------------------------------------
	// Modifier une zone
	//---------------------------------------------------------------------
	function editAction() {
		$id = (int)$this->_request->getParam('id', 0);
		if (!$zone = Class_Zone::getLoader()->find($id)) {
			$this->_redirect('admin/zone/index');
			return;
		}

		$this->_checkPost($zone);

		$this->view->titre = 'Modifier le territoire: '.$zone->getLibelle();
	}

	//---------------------------------------------------------------------
	// supprimer une zone
	//---------------------------------------------------------------------
	function deleteAction()
	{
		$id_zone=$this->_request->getParam("id_zone");
		$zoneClass = new Class_Zone();
		$zoneClass->deleteZone($id_zone);
		$this->_redirect('admin/zone/index');
	}

	//---------------------------------------------------------------------
	// placer les bibliotheques sur la carte
	//---------------------------------------------------------------------
	function placerbibsAction()
	{
		$this->view->titre = 'Placement des bibliothèques sur la carte';
		$id_zone=$this->_request->getParam("id_zone");

		$bibs = fetchAll("select * from bib_c_site where ID_ZONE=$id_zone and VISIBILITE > 0");
		// Validation
		if ($this->_request->isPost())
		{
			$i = 0;
			foreach($bibs as $bib)
			{
				// Positions des bibs
				$id_bib=$bib['ID_SITE'];
				$data = array();
				$data["libelle"]=$_POST["libelle_".$i];
				$data["posX"]=$_POST["posX_".$i];
				$data["posY"]=$_POST["posY_".$i];
				$data["posPoint"]=$_POST["posPoint_".$i];
				$data["profilID"]=$_POST["profilID_".$i];
				$props=addslashes(ZendAfi_Filters_Serialize::serialize($data));
				sqlExecute("update bib_c_site set AFF_ZONE='$props' where ID_SITE=$id_bib");

				// Proprietes du label
				$couleur_texte=$_POST["couleur_texte"];
				$couleur_ombre=$_POST["couleur_ombre"];
				$taille_fonte=$_POST["taille_fonte"];
				sqlExecute("update bib_c_zone set COULEUR_TEXTE='$couleur_texte', COULEUR_OMBRE='$couleur_ombre', TAILLE_FONTE='$taille_fonte' where ID_ZONE=$id_zone");
				$i++;
			}
			$this->_redirect('admin/zone/index');
		}

		// Entree formulaire
		$this->view->zone= Class_Zone::getLoader()->find($id_zone);
		if(!$this->view->zone->COULEUR_TEXTE) $this->view->zone->COULEUR_TEXTE="#ffffff";
		if(!$this->view->zone->COULEUR_OMBRE) $this->view->zone->COULEUR_OMBRE="#000000";
		if(!$this->view->zone->TAILLE_FONTE) $this->view->zone->TAILLE_FONTE="12";
		
		// Caracteristiques de l'image
		$this->view->image=$this->view->zone->getImageZone($this->view->zone->IMAGE,true);
		$this->view->bibs=$bibs;
	}
}