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
////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Controller SITOTHEQUE
////////////////////////////////////////////////////////////////////////////////

class Admin_SitoController extends Zend_Controller_Action
{
	private $id_zone;
	private $id_bib;
	private $_today;

	//-----------------------------------------------------------------------------------------------
	// Initialisation
	//-----------------------------------------------------------------------------------------------
	function init()
	{
		// Zone et bib du filtre (initialisé dans le plugin DefineUrls)
		$this->id_zone=$_SESSION["admin"]["filtre_localisation"]["id_zone"];
		$this->id_bib=$_SESSION["admin"]["filtre_localisation"]["id_bib"];

		// On force une bib s'il ny en a pas
		if(!intval($this->id_bib))
		{
			if(intval($this->id_zone)) $this->id_bib=fetchOne("select ID_SITE from bib_c_site where ID_ZONE=".$this->id_zone." order by LIBELLE");
			else {$this->id_bib="PORTAIL"; $this->id_zone="PORTAIL";}
		}

		// Objets de vue
		$this->view->id_zone=$this->id_zone;
		$this->view->id_bib=$this->id_bib;
		
		/// OLD A MODIFIER ///////
		$class_date = new Class_Date();
		$this->_today = $class_date->DateTimeDuJour();
	}
	
	//----------------------------------------------------------------------------------
	// Liste des sites
	//----------------------------------------------------------------------------------
	function indexAction()
	{
		$class_sito = new Class_Sitotheque();
		$sites=$class_sito->rendArray($this->id_bib);
		$this->view->sites=$class_sito->rendHTML();
		$this->view->titre = 'Gestion de la sitothèque';
	}

	function cataddAction()
	{
		$this->view->titre = "Ajouter une catégorie de sites";
		$class_sito = new Class_Sitotheque();

		if ($this->_request->isPost())
		{
			$filter = new Zend_Filter_StripTags();
			$libelle = trim($filter->filter($this->_request->getPost('libelle')));
			$id_cat_mere = (int)$this->_request->getPost('id_cat_mere');

			$data = array(
			'ID_CAT' => '',
			'ID_CAT_MERE' => $id_cat_mere,
			'LIBELLE' => $libelle,
			'ID_SITE' => $this->id_bib,
			);
			$menu_deploy = $this->saveTreeMenu($id_cat_mere);

			$errorMessage = $class_sito->addCategorie($data);
			if ($errorMessage == ''){$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
			else
			{
				$this->view->message = $errorMessage;
				$this->view->cat = new stdClass();
				$this->view->cat->ID_CAT = null;
				$this->view->cat->ID_CAT_MERE = $id_cat_mere;
				$this->view->cat->LIBELLE = $libelle;
			}
		}
		else
		{
			$id_cat = (int)$this->_request->getParam('id', 0); if(!$id_cat){ $id_cat_mere =0;} else {$id_cat_mere = $id_cat;}
			$menu_deploy = $this->saveTreeMenu($id_cat_mere);
			// Cat _blank
			$this->view->cat = new stdClass();
			$this->view->cat->ID_CAT = null;
			$this->view->cat->LIBELLE = '';
			$this->view->cat->ID_CAT_MERE = $id_cat_mere;
		}
		$this->view->action = 'add';
		$this->view->sess_id_zone = $this->id_zone;
		$this->view->sess_id_site = $this->id_bib;
	}

	function cateditAction()
	{

		$this->view->titre = "Modifier une cat&eacute;gorie de sites";
		$class_sito = new Class_Sitotheque();

		if ($this->_request->isPost())
		{
			$filter = new Zend_Filter_StripTags();
			$id_cat_mere = (int)$this->_request->getPost('id_cat_mere');
			$id_cat = (int)$this->_request->getParam('id_cat', 0);
			$libelle = trim($filter->filter($this->_request->getPost('libelle')));
			if ($id !== false) {

				$data = array(
				'ID_CAT_MERE' => $id_cat_mere,
				'LIBELLE' => $libelle,
				);

				$errorMessage = $class_sito->editCategorie($data, $id_cat);
				if ($errorMessage == ''){$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
				else
				{
					$sito_categorie = $class_sito->getCategorieByIdCat($id_cat);
					$this->view->combo_cat = $this->rendComboCat($this->id_bib,$sito_categorie->ID_CAT,$sito_categorie->ID_CAT_MERE);
					$this->view->cat = $sito_categorie;
					$this->view->message = $errorMessage;
				}
			}
		}
		else
		{
			$id = (int)$this->_request->getParam('id', 0);
			$menu_deploy = $this->saveTreeMenu($id);
			if ( $id > 0 )
			{
				$sito_categorie = $class_sito->getCategorieByIdCat($id);
				$this->view->combo_cat = $this->rendComboCat($this->id_bib,$sito_categorie->ID_CAT,$sito_categorie->ID_CAT_MERE);

				if($sito_categorie == null){$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
				else{$this->view->cat = $sito_categorie;}
			}
			else {$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
		}
		// Action
		$this->view->action = 'edit';
		$this->view->sess_id_zone = $this->id_zone;
		$this->view->sess_id_site = $this->id_bib;
	}

	function catdelAction()
	{
		$class_sito = new Class_Sitotheque();

		$id = (int)$this->_request->getParam('id');
		if ($id > 0) {
			$menu_deploy = $this->saveTreeMenu($id);
			$errorMessage = $class_sito->deleteCategorie($id);
			if ( $errorMessage == ''){$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
			else{$this->_redirect('admin/error/database');}
		}

		$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);
	}

	function sitoaddAction()
	{
		$this->view->titre = "Ajouter un site";
		$class_sito = new Class_Sitotheque();

		if ($this->_request->isPost())
		{
			$filter = new Zend_Filter_StripTags();
			$titre = trim($filter->filter($this->_request->getPost('titre')));
			$url = trim($filter->filter($this->_request->getPost('url')));
			$categorie = trim($filter->filter($this->_request->getPost('categorie')));
			$commentaire = trim($filter->filter($this->_request->getPost('commentaire')));
			$id_cat = (int)$this->_request->getPost('id_cat');
			$tags = trim($filter->filter($this->_request->getPost('tags')));

			$menu_deploy = $this->saveTreeMenu($id_cat);
			$data = array(
			'ID_SITO' => '',
			'ID_CAT' => $id_cat,
			'ID_NOTICE' => 0,
			'TITRE' => $titre,
			'DESCRIPTION' => $commentaire,
			'URL' => $url,
			'DATE_MAJ' => $this->_today,
			'TAGS' => $tags,
			);

			$errorMessage = $class_sito->addSito($data);
			if ($errorMessage == ''){$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
			else
			{
				$combo_cat = $class_sito->rendComboCategorie($this->id_bib,$id_cat);
				$menu_deploy = $this->saveTreeMenu($id_cat);

				$this->view->sito = new stdClass();
				$this->view->sito->ID_CAT = $id_cat;
				$this->view->sito->TITRE = $titre;
				$this->view->sito->DESCRIPTION = $commentaire;
				$this->view->sito->URL = $url;
				$this->view->sito->TAGS = $tags;
				$this->view->combo_cat = $combo_cat;
				$this->view->message = $errorMessage;
			}

		}
		else
		{
			$id_cat = (int)$this->_request->getParam('id', 0);
			$combo_cat = $class_sito->rendComboCategorie($this->id_bib,$id_cat);
			$menu_deploy = $this->saveTreeMenu($id_cat);
			$this->view->combo_cat = $combo_cat;
		}

		// Action
		$this->view->action = 'add';
		$this->view->sess_id_zone = $this->id_zone;
		$this->view->sess_id_site = $this->id_bib;
	}

	function sitoeditAction()
	{
		$this->view->titre = "Modifier un site";
		$class_sito = new Class_Sitotheque();

		if ($this->_request->isPost())
		{
			$filter = new Zend_Filter_StripTags();
			$id_sito = (int)$this->_request->getPost('id_sito');
			$id_cat = (int)$this->_request->getPost('id_cat');
			$titre = trim($filter->filter($this->_request->getPost('titre')));
			$url = trim($filter->filter($this->_request->getPost('url')));
			$categorie = trim($filter->filter($this->_request->getPost('categorie')));
			$commentaire = trim($filter->filter($this->_request->getPost('commentaire')));
			$tags = trim($filter->filter($this->_request->getPost('tags')));

			if ($id_sito !== false)
			{

				$data = array(
				'ID_CAT' => $id_cat,
				'TITRE' => $titre,
				'DESCRIPTION' => $commentaire,
				'URL' => $url,
				'DATE_MAJ' => $this->_today,
				'TAGS' => $tags,
				);

				$menu_deploy = $this->saveTreeMenu($id_cat);
				$errorMessage = $class_sito->editSito($data, $id_sito);
				if ($errorMessage == ''){$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
				else
				{
					$sito = $class_sito->getSitoById($id_sito);

					$combo_cat = $class_sito->rendComboCategorie($this->id_bib,$sito->ID_CAT);
					$this->view->sito = $sito;
					$this->view->combo_cat = $combo_cat;
					$this->view->message = $errorMessage;

				}
			}
		}
		else
		{
			$id_sito = (int)$this->_request->getParam('id', 0);
			if ( $id_sito > 0 )
			{
				$sito = $class_sito->getSitoById($id_sito);
				if($sito == null){$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
				else
				{
					$combo_cat = $class_sito->rendComboCategorie($this->id_bib,$sito->ID_CAT);
					$menu_deploy = $this->saveTreeMenu($sito->ID_CAT);

					$this->view->sito = $sito;
					$this->view->combo_cat = $combo_cat;
					$this->view->message = $errorMessage;
				}
			}
			else {$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
		}

		// Action
		$this->view->action = 'edit';
		$this->view->sess_id_zone = $this->id_zone;
		$this->view->sess_id_site = $this->id_bib;
	}

	function sitodelAction()
	{
		$class_sito = new Class_Sitotheque();

		$id = (int)$this->_request->getParam('id');
		if ($id > 0) {
			$sito = $class_sito->getSitoById($id);
			$menu_deploy = $this->saveTreeMenu($sito->ID_CAT);
			$errorMessage = $class_sito->deleteSito($id);
			if ( $errorMessage == ''){$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);}
			else{$this->_redirect('admin/error/database');}
		}
		$this->_redirect('admin/sito?z='.$this->id_zone.'&b='.$this->id_bib);
	}

	function viewsitoAction()
	{
		$this->view->titre = "Afficher un site";
		$id_sito = (int)$this->_request->getParam('id', 0);
		$class_sito = new Class_Sitotheque();
		$sito = $class_sito->getSitoById($id_sito);

		$menu_deploy = $this->saveTreeMenu($sito->ID_CAT);
		$this->view->sito = $sito;
		$this->view->sess_id_zone = $this->id_zone;
		$this->view->sess_id_site = $this->id_bib;
	}

	function rendComboCat($id_bib,$id_cat,$id_cat_mere)
	{
		$class_sito = new Class_Sitotheque();
		$cat_array = $class_sito->getAllCategorie($id_bib);
		$html[]='<select name="id_cat_mere" id="id_cat_mere" style="width:100%">';
		$html[]='<option value="'.$id_cat_mere.'" selected="selected">Aucune</option>';
		foreach ($cat_array as $cat)
		{
			if(($id_cat !=$cat["ID_CAT"]) && ($cat["ID_CAT_MERE"] != $id_cat))$html[]='<option value="'.$cat["ID_CAT"].'">'.$cat["LIBELLE"].'</option>';
		}
		$html[]='</select>';
		return implode('',$html);
	}

	function saveTreeMenu($id_cat)
	{
		$class_sito = New Class_Sitotheque();
		$id_menu[]=$id_cat;
		while(true)
		{
			$id_cat = $class_sito->getIdCatMereByIdCat($id_cat);
			if(!$id_cat || $id_cat== 0){break;}
			else{$id_menu[]=$id_cat;}
		}
		$_SESSION["MENU_DEPLOY"]["SITO"] = $id_menu;
	}
}