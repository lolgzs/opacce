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
// OPAC3 : Controller fils RSS
////////////////////////////////////////////////////////////////////////////////

class Admin_RssController extends Zend_Controller_Action
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
		$date = new Zend_Date();
		$currentYear = $date->toString('yyyy');
		$this->view->currentYear = $currentYear - 1;
		$this->view->limitYear = $currentYear + 10;

		$class_date = new Class_Date();
		$this->_today = $class_date->DateTimeDuJour();
	}
	
	//----------------------------------------------------------------------------------
	// Liste des fils rss
	//----------------------------------------------------------------------------------
	function indexAction()
	{
		$class_rss = new Class_Rss();
		$rss=$class_rss->rendArray($this->id_bib);
		$this->view->rss=$class_rss->rendHTML();
		$this->view->titre = 'Mise à jour des flux RSS';
	}


	function cataddAction()
	{
		$this->view->titre = "Ajouter une cat&eacute;gorie de flux RSS";
		$class_rss = new Class_Rss();

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
			$class_rss = new Class_Rss();
			$errorMessage = $class_rss->addCategorie($data);
			if ($errorMessage == ''){$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
			else
			{
				$this->view->cat = new stdClass();
				$this->view->cat->ID_CAT = null;
				$this->view->cat->ID_CAT_MERE = $id_cat_mere;
				$this->view->cat->LIBELLE = $libelle;
				$this->view->id_cat_mere = $id_cat_mere;
				$this->view->message = $errorMessage;
			}
		}
		else
		{
			$id = (int)$this->_request->getParam('id', 0); if(!$id || $id ==0) $id_cat_mere = 0; else $id_cat_mere = $id;
			$menu_deploy = $this->saveTreeMenu($id_cat_mere);
			// Cat _blank
			$this->view->cat = new stdClass();
			$this->view->cat->ID_CAT = null;
			$this->view->cat->LIBELLE = '';
			$this->view->cat->ID_CAT_MERE = $id_cat_mere;
		}
		// Action
		$this->view->action = 'add';
		$this->view->sess_id_zone = $this->id_zone;
		$this->view->sess_id_site = $this->id_bib;
	}

	function cateditAction()
	{
		$this->view->titre = "Modifier une cat&eacute;gorie de flux RSS";
		$class_rss = new Class_Rss();

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

				$errorMessage = $class_rss->editCategorie($data, $id_cat);
				if ($errorMessage == ''){$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
				else
				{
					$rssCategorie = $class_rss->getCategorieByIdCat($id_cat);
					if (is_string($categorie) ){$this->_redirect('admin/error/database');}
					else
					{
						$this->view->combo_cat = $this->rendComboCat($this->id_bib,$rssCategorie->ID_CAT,$rssCategorie->ID_CAT_MERE);
						$this->view->rss = $rssCategorie;
						$this->view->id_cat_mere = $id_cat_mere;
						$this->view->message = $errorMessage;
					}
				}
			}
		}
		else
		{
			$id = (int)$this->_request->getParam('id', 0);
			$menu_deploy = $this->saveTreeMenu($id);
			if ( $id > 0 )
			{
				$rssCategorie = $class_rss->getCategorieByIdCat($id);
				$this->view->combo_cat = $this->rendComboCat($this->id_bib,$rssCategorie->ID_CAT,$rssCategorie->ID_CAT_MERE);
				if ( is_string($rssCategorie) ){$this->_redirect('admin/error/database');}
				elseif($rssCategorie == null){$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
				else{$this->view->rss = $rssCategorie;	}
			}
			else {$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
		}
		// Action
		$this->view->action = 'edit';
		$this->view->sess_id_zone = $this->id_zone;
		$this->view->sess_id_site = $this->id_bib;
	}
	function catdelAction()
	{
		$class_rss = new Class_Rss();
		$id = (int)$this->_request->getParam('id');
		$menu_deploy = $this->saveTreeMenu($id);
		if ($id > 0)
		{
			$errorMessage = $class_rss->delCategorie($id);
			if ($errorMessage == ''){$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
			else{$this->_redirect('admin/error/rss');}
		}
		$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);
	}


	function rssaddAction()
	{
		$this->view->titre = "Ajouter un flux RSS";
		$class_rss = new Class_Rss();

		if ($this->_request->isPost())
		{
			$filter = new Zend_Filter_StripTags();
			$titre = trim($filter->filter($this->_request->getPost('titre')));
			$url = trim($filter->filter($this->_request->getPost('xml')));
			$commentaire = trim($filter->filter($this->_request->getPost('commentaire')));
			$id_cat = trim($filter->filter($this->_request->getPost('id_cat')));
			$tags = trim($filter->filter($this->_request->getPost('tags')));

			$menu_deploy = $this->saveTreeMenu($id_cat);
			$data = array(
			'ID_RSS' => '',
			'ID_CAT' => $id_cat,
			'ID_NOTICE' => 0,
			'TITRE' => $titre,
			'DESCRIPTION' => $commentaire,
			'URL' => $url,
			'DATE_MAJ' => $this->_today,
			'TAGS' => $tags,
			);

			$errorMessage = $class_rss->addRss($data);
			if ($errorMessage == ''){$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
			else
			{
				$combo_cat = $class_rss->rendComboCategorie($this->id_bib,$id_cat);
				$menu_deploy = $this->saveTreeMenu($id_cat);

				$this->view->rss = new stdClass();
				$this->view->rss->ID_CAT = $id_cat;
				$this->view->rss->TITRE = $titre;
				$this->view->rss->DESCRIPTION = $commentaire;
				$this->view->rss->URL = $url;
				$this->view->rss->TAGS = $tags;
				$this->view->combo_cat = $combo_cat;
				$this->view->message = $errorMessage;
			}

		}
		else
		{
			$id_cat = (int)$this->_request->getParam('id', 0);
			// set up an "empty" Rss
			$combo_cat = $class_rss->rendComboCategorie($this->id_bib,$id_cat);
			$menu_deploy = $this->saveTreeMenu($id_cat);
			$this->view->combo_cat = $combo_cat;
		}

		// Action
		$this->view->action = 'add';
		$this->view->sess_id_zone = $this->id_zone;
		$this->view->sess_id_site = $this->id_bib;
	}

	function rsseditAction()
	{
		$this->view->titre = "Modifier un flux RSS";
		$class_rss = new Class_Rss();

		if ($this->_request->isPost())
		{
			$filter = new Zend_Filter_StripTags();
			$id_rss = (int)$this->_request->getPost('id_rss');
			$titre = trim($filter->filter($this->_request->getPost('titre')));
			$url = trim($filter->filter($this->_request->getPost('xml')));
			$commentaire = trim($filter->filter($this->_request->getPost('commentaire')));
			$categorie = trim($filter->filter($this->_request->getPost('categorie')));
			$tags = trim($filter->filter($this->_request->getPost('tags')));
			$id_cat = (int)$this->_request->getPost('id_cat');
			if ($id_rss !== false)
			{

				$data = array(
				'ID_RSS' => $id_rss,
				'ID_CAT' => $id_cat,
				'ID_NOTICE' => 0,
				'TITRE' => $titre,
				'DESCRIPTION' => $commentaire,
				'URL' => $url,
				'DATE_MAJ' => $this->_today,
				'TAGS' => $tags,
				);

				$menu_deploy = $this->saveTreeMenu($id_cat);
				$errorMessage = $class_rss->editRss($data, $id_rss);
				if ($errorMessage == ''){$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
				else
				{
					$rss = $class_rss->getRssById($id_rss);
					if ( is_string($rss[0]) ){$this->_redirect('admin/error/database');}
					else
					{
						$combo_cat = $class_rss->rendComboCategorie($this->id_bib,$rss[0]["ID_CAT"]);
						$this->view->rss = new stdClass();
						$this->view->rss->ID_CAT = $id_cat;
						$this->view->rss->ID_RSS = $id_rss;
						$this->view->rss->TITRE = $titre;
						$this->view->rss->DESCRIPTION = $commentaire;
						$this->view->rss->URL = $url;
						$this->view->rss->TAGS = $tags;
						$this->view->combo_cat = $combo_cat;
						$this->view->message = $errorMessage;
					}
				}
			}
		}
		else
		{
			$id_rss = (int)$this->_request->getParam('id', 0);
			if ( $id_rss > 0 )
			{
				$rss = $class_rss->getRssById($id_rss);
				if (is_string($rss[0])){$this->_redirect('admin/error/database');}
				elseif($rss[0] == null){$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
				else
				{
					$combo_cat = $class_rss->rendComboCategorie($this->id_bib,$rss[0]["ID_CAT"]);
					$menu_deploy = $this->saveTreeMenu($rss[0]["ID_CAT"]);

					$this->view->rss = new stdClass();
					$this->view->rss->ID_RSS = $id_rss;
					$this->view->rss->ID_CAT = $rss[0]["ID_CAT"];
					$this->view->rss->TITRE = $rss[0]["TITRE"];
					$this->view->rss->DESCRIPTION = $rss[0]["DESCRIPTION"];
					$this->view->rss->URL = $rss[0]["URL"];
					$this->view->rss->TAGS = $rss[0]["TAGS"];
					$this->view->combo_cat = $combo_cat;
					$this->view->message = $errorMessage;
				}
			}
			else {$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
		}

		// Action
		$this->view->action = 'edit';
		$this->view->sess_id_zone = $this->id_zone;
		$this->view->sess_id_site = $this->id_bib;
	}

	function rssdelAction()
	{
		$class_rss = new Class_Rss();
		$id = (int)$this->_request->getParam('id');
		$rss = $class_rss->getRssById($id);
		$menu_deploy = $this->saveTreeMenu($rss[0]["ID_CAT"]);
		if ($id > 0) {
			$errorMessage = $class_rss->deleteRss($id);
			if ( $errorMessage == ''){$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
			else{$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);}
		}
		$this->_redirect('admin/rss?z='.$this->id_zone.'&b='.$this->id_bib);
	}

	function rendComboCat($id_bib,$id_cat,$id_cat_mere)
	{
		$class_rss = new Class_Rss();
		$cat_array = $class_rss->getAllCategorieByIdBib($id_bib);
		$html[]='<select name="id_cat_mere" id="id_cat_mere" style="width:100%">';
		$html[]='<option value="'.$id_cat_mere.'" selected="selected">Aucune</option>';
		foreach ($cat_array as $cat)
		{
			if ($id_bib ==0)
			{
				if ($id_cat !=$cat->ID_CAT && $cat->ID_CAT_MERE != $id_cat) $html[]='<option value="'.$cat->ID_CAT.'">'.$cat->LIBELLE.'</option>';
			}
			else
			{
				if($id_cat !=$cat["ID_CAT"] && $cat["ID_CAT_MERE"] != $id_cat)$html[]='<option value="'.$cat["ID_CAT"].'">'.$cat["LIBELLE"].'</option>';
			}
		}
		$html[]='</select>';
		return implode('',$html);
	}

	function saveTreeMenu($id_cat)
	{
		$class_rss = New Class_Rss();
		$id_menu[]=$id_cat;
		while(true)
		{
			$id_cat = $class_rss->getIdCatMereByIdCat($id_cat);
			if(!$id_cat || $id_cat== 0){break;}
			else{$id_menu[]=$id_cat;}
		}
		$_SESSION["MENU_DEPLOY"]["RSS"] = $id_menu;
	}
}