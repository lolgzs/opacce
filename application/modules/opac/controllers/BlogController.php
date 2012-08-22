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
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  OPAC3: Blog
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class BlogController extends Zend_Controller_Action
{
	private $_user = null;								// Le user connecté
	var $modo_blog;
	var $_today;
    
	function init()
	{
		//Verify that the user has successfully authenticated.  If not, we redirect to the login.
		$user = ZendAfi_Auth::getInstance();
		if (!$user->hasIdentity())$this->_user=null;
		else $this->_user = ZendAfi_Auth::getInstance()->getIdentity();
		$this->modo_blog = getVar('MODO_BLOG');
		$class_date = new Class_Date();
		$this->_today = $class_date->DateTimeDuJour();
	}
    
	function indexAction()
	{
		$this->_redirect('opac/blog/lastcritique/nb/10');
	}
	
	//------------------------------------------------------------------------------------------------------  
	// Donner son avis
	//------------------------------------------------------------------------------------------------------  
	function viewauteurAction()	{
		$id_user = (int)$this->_request->getParam('id', $this->_user->ID_USER);
		if ($auteur = Class_Users::getLoader()->find($id_user)) {
			$this->view->liste_avis = Class_AvisNotice::filterVisibleForUser($this->_user, $auteur->getAvis());
			$this->view->name = $auteur->getNomAff();
		} else {
			$this->view->liste_avis = '';
			$this->view->name = 'Auteur introuvable';
		}
		
		$this->view->title = "Avis";
		$this->view->id_user = $id_user;
		if ($id_user == $this->_user->ID_USER)
			$this->view->getHelper('avis')->setActions(array('del'));
	}


	function delavisnoticeAction() {
		$id = $this->_request->getParam('id');
		Class_AvisNotice::getLoader()->find($id)->delete();
		$this->_redirect('blog/viewauteur');
	}

    
	function lastcritiqueAction()	{
		$nb_avis = (int)$this->_request->getParam('nb', 20);
		$liste_avis = Class_AvisNotice::getLoader()->findAllBy(array('order' => 'date_avis desc',
																																 'limit' => $nb_avis));
		
		$this->view->nb_aff = $nb_avis;
		$this->view->liste_avis = Class_AvisNotice::filterVisibleForUser($this->_user, $liste_avis);
		$this->view->title = $this->view->_("Dernières critiques");
		$this->renderScript('blog/viewcritiques.phtml');
	}

	/* Affiche les critiques à partir des préférences d'un module
		 Appelé lorsqu'on clique sur le titre du module critiques.
		 Paramètre: id_module
	 */
	function viewcritiquesAction() {
		$id_module = (int)$this->_request->getParam('id_module');
		$preferences = Class_Profil::getCurrentProfil()->getModuleAccueilPreferences($id_module);
		$avis = Class_AvisNotice::getLoader()->getAvisFromPreferences($preferences);
		
		$this->view->nb_aff = 50;
		$this->view->liste_avis = $avis;
		$this->view->title = 'Dernières critiques';
		if (array_key_exists('titre', $preferences))
			$this->view->title = $preferences['titre'];
	}


	/*
	 * Affiche l'avis avec l'id donné pour pouvoir être lu par Read Speaker.
	 */
	function readavisAction() {
		$this
			->getHelper('ViewRenderer')
			->setLayoutScript('readspeaker.phtml');

		$id_avis = $this->_request->getParam('id'); 
		$this->view->avis = Class_AvisNotice::getLoader()->find($id_avis);
	}

    
	function viewavisAction()	{
		$id_avis = $this->_request->getParam('id'); 
		$avis = Class_AvisNotice::getLoader()->find($id_avis);

		/* $class_blog = new Class_Blog(); */
		/* $cmt = $class_blog->getAllCmtByIdAvis($id_avis); */
		//$_SESSION["abonne_redirect"] = BASE_URL.'/blog/viewavis/id/'.$id_avis;
        
		$this->view->avis = $avis;
		$this->view->commentaires = array();
		$this->view->modo_blog = $this->modo_blog;
		$this->view->user_co = ($this->_user->ID_USER != '');
		$this->view->user = $this->_user;
	}
    

	function addcmtAction()	{
		$filter = new Zend_Filter_StripTags();
		$contenu = $filter->filter($this->_request->getPost('cmt'));
		$id_avi = $this->_request->getPost('id_avis'); $id_avis = explode('-',$id_avi);
		$pseudo = $this->_request->getPost('pseudo');
		$type = $this->_request->getPost('type');

		if(trim($pseudo)=="") {$this->_redirect('blog/viewavis/id/'.$id_avi);}
        
        
		if($type == "notice") $id_notice = $id_avis[0]; else $id_notice = 0;
		if($type == "cms") $id_cms = $id_avis[0]; else $id_cms = 0;
		if($this->modo_blog == 1) $statut =0; else $statut=1;

		if($this->_user->LOGIN)	{
				if($this->_user->ROLE_LEVEL >=3) $abon_ou_bib = 1;
				else $abon_ou_bib = 0;

				$cls_user= new Class_Users();
				$cls_user->updatePseudo($this->_user, $pseudo);
		}	else 	{
			$abon_ou_bib = 0;
		}
        
		$data = array(
									'ID_CMT' => '',
									'ID_USER' => $this->_user->ID_USER,
									'ID_NOTICE' => $id_notice,
									'ID_CMS' => $id_cms,
									'DATE_CMT' => $this->_today,
									'DATE_MOD' => null,
									'CMT' => $contenu,
									'STATUT' => $statut,
									'ABON_OU_BIB' => $abon_ou_bib,
									'SIGNATURE' => $pseudo,
									);
        
		$class_blog = new Class_Blog();
		$class_blog->addCmt($data);
		$this->_redirect('blog/viewavis/id/'.$id_avi);
	}
    
	function alertAction()
	{
		$class_blog = new Class_Blog();
		$type = $this->_request->getParam('type');
		$id = $this->_request->getParam('id_avis');
		$class_blog->alertThis($id,$type);
		$this->_redirect($_SERVER['HTTP_REFERER']);
	}
}