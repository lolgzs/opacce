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
//  OPAC3: PANIERS 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class PanierController extends Zend_Controller_Action
{
	private $_user = null;								// User connecté (auth)
	private $panier;											// Instance classe panier

//------------------------------------------------------------------------------------------------------  
// Initialisation controller
//------------------------------------------------------------------------------------------------------  
	function init()	{
		if (!array_isset("panier", $_SESSION))
			$_SESSION["panier"] = array(
																	"panier_courant" => 0,
																	"url_retour" => '',
																	"erreur" => '');

		// Url retour
		$retour = $_SERVER["HTTP_REFERER"];
		if (strPos($retour,"viewnotice") > 0) 
			$_SESSION["panier"]["url_retour"]=$retour;
		
		//Verify that the user has successfully authenticated.  If not, we redirect to the login.
		$user = ZendAfi_Auth::getInstance();
		if (!$user->hasIdentity())	{
			$_SESSION["abonne_redirect"]=$this->_request->REQUEST_URI;
			$this->_redirect('opac/auth/login');
		}
		else 
			$this->_user = ZendAfi_Auth::getInstance()->getIdentity();
		
		// Instanciation classe panier
		$this->panier=new Class_PanierNotice();
	}

//------------------------------------------------------------------------------------------------------  
// Entrée dans le système
//------------------------------------------------------------------------------------------------------  
function indexAction()	{
	if ($id_panier = $this->_getParam("id_panier", null)) 
		$_SESSION["panier"]["panier_courant"]=$id_panier;
	
	// Liste des paniers
	$liste_panier = $this->panier->getListeAbonne($this->_user->ID_USER);
	$this->view->listePanier = $liste_panier;
	$this->view->title = $this->view->_("Vos paniers");
	$this->view->message = false;

	if(!count($liste_panier)) 
		$this->view->message = $this->view->_("Vous n'avez aucun panier.");
	else 
		$this->view->message = $this->view->_("Vous avez %d panier(s)", count($liste_panier));
	
	// Si ajout notice : choix du panier
	if ($id_notice = $this->_getParam("id_notice")) {
		$this->view->url_creer_panier=BASE_URL."/opac/panier/panierajouternotice?id_notice=".$id_notice;
		//if(!count($liste_panier)) $this->_redirect($this->view->url_creer_panier);
		$combo='<select name="id_panier" style="width:auto">';
		$combo.='<option value="">'.$this->view->_('nouveau panier').'</option>';
		for($i=0; $i<count($liste_panier); $i++)
			{
				if($liste_panier[$i]["ID_PANIER"] == $_SESSION["panier"]["panier_courant"]) $selected=" selected"; else $selected="";
				$combo.='<option value="'.$liste_panier[$i]["ID_PANIER"].'"'.$selected.'>'.stripSlashes($liste_panier[$i]["LIBELLE"]).'</option>';
			}
		$combo.='</select>';
		$this->view->combo=$combo;
	}
	$this->view->id_notice = $id_notice;
	
	// Si panier courant on affiche le panier
	if($id_panier)	{
		$panier=$this->panier->getPanier($this->_user->ID_USER, $id_panier);
		if (array_isset("ID_PANIER", $panier)) {
			$this->view->panier_courant=$panier;
			// comme ça quand on clique est sur une notice on peut revenir au panier
			$_SESSION["recherche"]["retour_liste"] = BASE_URL.'/opac/panier?id_panier='.$id_panier;
		}
	}
	
	$this->view->url_retour=$_SESSION["panier"]["url_retour"];
	
	// Message d'erreur
	if($_SESSION["panier"]["erreur"]) 
		$this->view->erreur=$_SESSION["panier"]["erreur"];
	$_SESSION["panier"]["erreur"]="";
}

//------------------------------------------------------------------------------------------------------  
// Création nouveau panier
//------------------------------------------------------------------------------------------------------  
	function creerpanierAction()
	{
		$id_panier=$this->panier->creerPanier($this->_user->ID_USER);
		$this->_redirect('opac/panier?id_panier='.$id_panier);
	}
	
//------------------------------------------------------------------------------------------------------  
// Suppression d'un panier
//------------------------------------------------------------------------------------------------------  
	function supprimerpanierAction()	{
		$id_panier=$this->panier->supprimerPanier($this->_user->ID_USER,$_REQUEST["id_panier"]);
		$this->_redirect('opac/panier');
	}

//------------------------------------------------------------------------------------------------------  
// Ajout d'un document dans un panier
//------------------------------------------------------------------------------------------------------  
	function panierajouternoticeAction()
	{
		$id_notice=$_REQUEST["id_notice"];
		$id_panier=$_REQUEST["id_panier"];
		if(!$id_panier) $id_panier = $this->panier->creerPanier($this->_user->ID_USER);
		$ret=$this->panier->ajouterNotice($this->_user->ID_USER,$id_panier, $id_notice);
		if($ret==false) 
		{
			$_SESSION["panier"]["erreur"]=$this->view->_("Cette notice figure déjà dans le panier sélectionné.");
			$this->_redirect('opac/panier?id_notice='.$id_notice.'&id_panier='.$id_panier);
		}
		else 
		{
			$_SESSION["panier"]["erreur"]="";
			$this->_redirect('opac/panier?id_panier='.$id_panier);
		}
	}

//------------------------------------------------------------------------------------------------------  
// Suppression d'un document dans un panier
//------------------------------------------------------------------------------------------------------  
	function paniersupprimernoticeAction()
	{
		$id_notice=$_REQUEST["id_notice"];
		$id_panier=$_SESSION["panier"]["panier_courant"];
		$this->panier->supprimerNotice($this->_user->ID_USER,$id_panier, $id_notice);
		$this->_redirect('opac/panier?id_panier='.$id_panier);
	}

//------------------------------------------------------------------------------------------------------  
// Mise a jour libelle panier
//------------------------------------------------------------------------------------------------------  
	function majtitrepanierAction()
	{
		$id_panier=$_SESSION["panier"]["panier_courant"];
		$this->panier->majTitre($this->_user->ID_USER,$id_panier, $_POST["new_libelle"]);
		$this->_redirect('opac/panier?id_panier='.$id_panier);
	}
	
//------------------------------------------------------------------------------------------------------  
// Export du panier aux 2 formats
//------------------------------------------------------------------------------------------------------  
	function exportAction()
	{
		$id_panier=$_SESSION["panier"]["panier_courant"];
		$this->view->panier=$this->panier->exportPaniers($this->_user->ID_USER,$id_panier);
		$this->view->url_retour=BASE_URL."/opac/panier?id_panier=".$id_panier;
	}
}