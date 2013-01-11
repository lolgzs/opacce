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
// OPAC3 - Controleur UTILISATEURS
//
//@TODO@ :- faire tri sur les entetes de colonnes
//				-	Faire une recherche par noms
//////////////////////////////////////////////////////////////////////////////////////////

class Admin_UsersController extends Zend_Controller_Action
{
	private $id_zone;
	private $id_bib;
	private $user;

	//------------------------------------------------------------------------------------------------------
	// Initialisation du controller
	//------------------------------------------------------------------------------------------------------
	function init()
	{
		// User connecté
		$this->user = ZendAfi_Auth::getInstance()->getIdentity();
		
		// Zone et bib du filtre (initialisé dans le plugin DefineUrls)
		$this->id_zone=$_SESSION["admin"]["filtre_localisation"]["id_zone"];
		$this->id_bib=$_SESSION["admin"]["filtre_localisation"]["id_bib"];
		
		// Objets de vue
		$this->view->id_zone=$this->id_zone;
		$this->view->id_bib=$this->id_bib;
	}

	//------------------------------------------------------------------------------------------------------
	// Liste des users
	//------------------------------------------------------------------------------------------------------
	function indexAction()
	{
		$this->view->titre = 'Gestion des utilisateurs';

		$cls_user = new Class_Users();
		$page=$this->_getParam('page');
		
		// Recherche
		$rech_user = array();
		if (($this->_getParam('recherche') == 1)	and $this->_request->isPost()){
			$rech_user=ZendAfi_Filters_Post::filterStatic($this->_request->getPost());
			$_SESSION["admin"]["rech_user"] = $rech_user;
		}
		
		// Lire les users
		$ret = $cls_user->getUsers($this->id_zone,
															 $this->id_bib,
															 $this->user->ROLE_LEVEL,
															 isset($_SESSION["admin"]["rech_user"]) ? $_SESSION["admin"]["rech_user"] : $rech_user,
															 $page);
		
		// Variables de vue
		$this->view->users = $ret["users"];
		$this->view->nb_par_page = $ret["nb_par_page"];
		$this->view->nombre = $ret["nombre"];
		$this->view->rech_user = $rech_user;
		$this->view->url=$this->_request->REQUEST_URI;
		$this->view->page=$page;
	}

	//------------------------------------------------------------------------------------------------------
	// Création utilisateur
	//------------------------------------------------------------------------------------------------------
	function addAction()
	{
		$this->view->titre = 'Ajouter un utilisateur';

		$user = new Class_Users();
		$user
			->setLogin('')
			->setPassword('')
			->setNom('')
			->setPrenom('')
			->setMail('')
			->setRoleLevel(0)
			->setRole('')
			->setIdabon(null)
			->setOrdreabon(1)
			->setDateDebut('')
			->setDateFin('');
		
		if ($this->id_bib)
			$user->setIdSite($this->id_bib);

		$this->_addOrEditUser($user);

		$this->view->id_user=0;
		$this->view->action = 'add';
	}


	protected function _addOrEditUser($user) {
		$class_role = new ZendAfi_Acl_AdminControllerRoles();

		// Retour du formulaire
		if ($this->_request->isPost()) {
			// Filtrage et controle des parametres
			$data=ZendAfi_Filters_Post::filterStatic($this->_request->getPost());
			$data['ordre']=intval($data['ordre']);
			if($data['role'] > 4) $data['bib']=0;
			if($data['role'] < 2) {$data['id_abon']=0; $data['ordre']=0;}

			$user
				->setLogin($data['username'])
				->setPassword($data['password'])
				->setNom($data['nom'])
				->setPrenom($data['prenom'])
				->setMail($data['mail'])
				->setRoleLevel($data['role'])
				->setRole($class_role->rendNomRole($data['role']))
				->setIdSite($data['bib'])
				->setIdabon($data['id_abon'])
				->setOrdreabon($data['ordre'])
				->setTelephone($data['telephone'])
				->setAdresse($data['adresse'])
				->setVille($data['ville'])
				->setCodePostal($data['code_postal']);
	
			try {
				if ($user->save())
					$this->_redirect("admin/users");
				else
					$this->view->erreurs = implode(BR, $user->getErrors());
			} catch (Exception $e) {
				$this->view->erreurs = $e->getMessage();
			}
		}

		// Variables de vue
		$this->view->user = $user;
	}
	

	//------------------------------------------------------------------------------------------------------
	// Modification utilisateur
	//------------------------------------------------------------------------------------------------------
	function editAction()	{
		$id_user = $this->_request->getParam('id',0);
		$user = Class_Users::getLoader()->find($id_user);

		$this->view->titre = "Modifier l'utilisateur: ".$user->getLogin();

		if ($this->_request->isPost()) 
			$user->updateSIGBOnSave();

		$this->_addOrEditUser($user);
		$this->view->id_user = $id_user;
		$this->view->action = 'edit';	
		if ($bib = $user->getBib()) {
			$this->view->id_bib = $bib->getId();
			$this->view->id_zone = $bib->getIdZone();
		}
	}

	//------------------------------------------------------------------------------------------------------
	// Suppression utilisateur
	//------------------------------------------------------------------------------------------------------						
	function deleteAction()
	{	
		$id_user=$this->_request->getParam('id');
		$user = new Class_Users();
		
		// Vérifications
		if($id_user == $this->user->ID_USER) $erreur="Vous ne pouvez pas vous supprimer vous-même.";
		if($id_user == 1) $erreur="Il est interdit de détruire le super administrateur.";
		$enreg=$user->getUser($id_user);
		if($enreg['ROLE_LEVEL']==6)
		{
			$nb_admin=fetchOne("select count(*) from bib_admin_users where ROLE_LEVEL=6");
			if($nb_admin == 1) $erreur="On ne peut pas supprimer le seul administrateur du portail.";
		}
	
		// On peut supprimer
		if(!$erreur)
		{
			$user->deleteUser($id_user);
			$this->_redirect('admin/users');
		}
		// Erreur dans la vue
		$this->view->erreur=$erreur;
	}
}