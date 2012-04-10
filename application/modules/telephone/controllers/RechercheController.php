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

class Telephone_RechercheController extends Zend_Controller_Action
{
	private $liste;																// Instance de la classe de liste de notices
	private $preferences;													// Préférences pour la liste du résultat
	private $id_notice;														// Id_notice pour les fonctions d'affichage des notices
	private $cls_notice;													// instance de la classe notice pour les fonctions d'affichage des notices
	private $service_afi;													// Web service afi actif ou pas
 
//------------------------------------------------------------------------------------------------------
// Initialisation du controler
//------------------------------------------------------------------------------------------------------
	function init()
	{
		// Formulaire de recherche
		if ($this->_request->isPost())
		{
			unset($_SESSION["recherche"]);
			$filter = new Zend_Filter_StripTags();
			foreach($_POST as $critere => $valeur) $_SESSION["recherche"]["selection"][$critere] = trim($filter->filter($this->_request->getPost($critere, false)));
		}

		// Pour les fonctions des notices
		$id_notice=$this->_getParam("id_notice");
		if(!$id_notice) $id_notice=$this->_getParam("id");
		if($id_notice)
		{
			$this->id_notice=$id_notice;
			$this->cls_notice=new Class_Notice();
			$this->view->url_retour=$_SESSION["recherche"]["retour_notice"];
		}

		// Préférences
		$current_module=$this->_getParam("current_module");
		$this->preferences=$current_module["preferences"];
		$this->liste=new Class_ListeNotices(
																				$this->preferences["liste_nb_par_page"], 
																				$this->preferences["liste_codes"]); 

		// Test services afi
		$this->service_afi=fetchOne("select valeur from variables where clef ='url_services'");
	}
	
//-------------------------------------------------------------------------------
// Index pour trapper les modules en construction
//-------------------------------------------------------------------------------
function indexAction()
{
	$this->view->url_retour=$_SESSION["recherche"]["retour_notice"];
}


public function baseUrl() {
	return $this->view->url(array(), null, true);
}


//-------------------------------------------------------------------------------
// Lancer une recherche
//-------------------------------------------------------------------------------
	function lancerAction()
	{
		$_SESSION["recherche"]["retour_liste"]=$this->_request->REQUEST_URI;
		if(!$_SESSION["recherche"]["resultat"])
		{
			$moteur=new Class_MoteurRecherche();
			$ret=$moteur->lancerRechercheSimple($_SESSION["recherche"]["selection"]);
			if($ret["statut"]=="erreur")
			{
				$ret["nombre"]=0;
				$this->view->liste=$ret;
				$this->view->url_retour=$this->baseUrl();
				return false;
			}
			$_SESSION["recherche"]["resultat"]=$ret;
		}

		$this->view->notices=$this->liste->getListe($_SESSION["recherche"]["resultat"]["req_liste"]);
		$this->view->page=$_REQUEST["page"];
		$this->view->url_retour=$this->view->url(array(), null, true);
		$this->view->url=$this->view->url(array('controller' => 'recherche',
																						'action' => 'lancer'));
		$this->view->nombre=$_SESSION["recherche"]["resultat"]["nombre"];
		$this->view->preferences=$this->preferences;
	}

	//-------------------------------------------------------------------------------
	// Lancer recherche rebonbissante
	//-------------------------------------------------------------------------------
	function rebondAction()
	{
		$_SESSION["recherche"]["retour_liste"]=$this->_request->REQUEST_URI;
		$code=$_REQUEST["code_rebond"];
		$_SESSION["recherche"]["selection"]["code_rebond"]=$code;

		$moteur=new Class_MoteurRecherche();
		$ret=$moteur->lancerRechercheRebond($_SESSION["recherche"]["selection"]);
		if($ret["statut"]=="erreur")
		{
			$ret["nombre"]=0;
			$this->view->liste=$ret;
			$this->view->url_retour=$this->baseUrl();
			$viewRenderer = $this->getHelper('ViewRenderer');
			$viewRenderer->renderScript('recherche/lancer.phtml');
			return false;
		}
		$_SESSION["recherche"]["resultat"]=$ret;

		$this->view->notices=$this->liste->getListe($_SESSION["recherche"]["resultat"]["req_liste"]);
		$this->view->page=$_REQUEST["page"];
		$this->view->url_retour=$this->baseUrl();
		$this->view->url=$this->view->url(array('controller' => 'recherche',
																						'action' => 'rebond'))."?code_rebond=".$_SESSION["recherche"]["selection"]["code_rebond"];
		$this->view->nombre=$_SESSION["recherche"]["resultat"]["nombre"];
		$this->view->preferences=$this->preferences;

		// Rendre la vue liste
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('recherche/lancer.phtml');
	}

//-------------------------------------------------------------------------------
// Afficher une notice
//-------------------------------------------------------------------------------
	function viewnoticeAction()
	{
		$_SESSION["recherche"]["retour_notice"]=$this->_request->REQUEST_URI;
		$this->view->notice=$this->cls_notice->getNoticeDetail($this->id_notice, $this->preferences);
		$this->view->url_image=Class_WebService_Vignette::getUrl($this->id_notice,false);
		$this->view->url_retour=$_SESSION["recherche"]["retour_liste"];
	}

//------------------------------------------------------------------------------------------------------
// Rend le flux de la vignette (ajax)
//------------------------------------------------------------------------------------------------------
	function vignetteAction()
	{
		// Désactiver le renderer
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();

		// Envoyer l'image
		$img=new Class_WebService_Vignette();
		$img->getFluxImage($_REQUEST["clef"],$this->id_notice);
	}
//------------------------------------------------------------------------------------------------------
// Notice : Grande image
//------------------------------------------------------------------------------------------------------
	function grandeimageAction()
	{
		$notice=$this->cls_notice->getNotice($this->id_notice,"T");
		$this->view->titre=$notice["T"];
		$this->view->url_image=$this->_getParam("url");
	}

//------------------------------------------------------------------------------------------------------
// Notice détaillée
//------------------------------------------------------------------------------------------------------
	function detailAction()
	{
		$notice=$this->cls_notice->getNotice($this->id_notice,"T");
		$this->view->titre=$notice["T"];
		$this->view->notice=$this->cls_notice->getTousChamps($this->id_notice);
	}

//------------------------------------------------------------------------------------------------------
// Notice : Avis
//------------------------------------------------------------------------------------------------------
	function avisAction()	{
		// Lire la notice
		$notice = Class_Notice::getLoader()->find($this->id_notice);
		$all_avis = $notice->getAllAvisPerSource();

		$source_visible = $this->_getParam("source");
		if (!array_key_exists($source_visible, $all_avis)) 
			foreach ($all_avis as $source => $avis)
				if ($avis["nombre"] > 0) {
					$source_visible = $source;
					break;
				}
		

		$this->view->notice = $notice;
		$this->view->avis = $notice->getAllAvisPerSource();
		$this->view->source_visible = $source_visible;
	}

//------------------------------------------------------------------------------------------------------
// Notice : Exemplaires
//------------------------------------------------------------------------------------------------------
	function exemplairesAction()
	{
		$notice=$this->cls_notice->getNotice($this->id_notice,"T");
		$this->view->titre=$notice["T"];
		$this->view->exemplaires = fetchAll("Select id_bib,cote,count(*) from exemplaires where id_notice=".$this->id_notice." group by 1,2" );
	}

//------------------------------------------------------------------------------------------------------
// Notice : résumés
//------------------------------------------------------------------------------------------------------
	function resumeAction()
	{
		// Lire la notice
		$notice=$this->cls_notice->getNotice($this->id_notice,"T");

		// Si isbn ou ean
		if($notice["id_service"])
		{
			// resume interne
			$resume=$this->cls_notice->getChampNotice("R");
			if($resume)
			{
				$lig["source"]="notice interne";
				$lig["texte"]=$resume;
				$avis[]=$lig;
			}

			// Amazon
			$amazon=new Class_WebService_Amazon();
			$ret=$amazon->rend_analyses($notice["id_service"]);
			if($ret) foreach($ret as $item) $avis[]=$item;

			// Fnac
			$fnac=new Class_WebService_Fnac();
			$resume = $fnac->getResume($notice["id_service"]);
			if($resume)
			{
				$lig["source"]="Editeur";
				$lig["texte"]=$resume;
				$avis[]=$lig;
			}
		}

		// Resumé premiere
		if($notice["type_doc"]==4)
		{
			$premiere=new Class_WebService_Premiere();
			$resume=$premiere->get_resume($notice["T"]);
			if($resume)
			{
				$lig["source"]="Premiere.fr";
				$lig["texte"]=$resume;
				$avis[]=$lig;
			}
		}
		$this->view->notice=$notice;
		$this->view->avis=$avis;
	}

	//------------------------------------------------------------------------------------------------------
	// Tags utilisateur
	//------------------------------------------------------------------------------------------------------
	function tagsAction()
	{
		$notice = $this->cls_notice->getNotice($this->id_notice,"T");

		$notice_html = new Class_NoticeHtml($this->notice);
		$notice_html->notice = $notice;

		$tags = $notice_html->getTags($this->cls_notice->getTags($this->id_notice),
																	$this->notice["id_notice"]);

		//si on est en mode embed / telephone
		$route_name = $this->getHelper('ViewRenderer')->getRouteName();
		$tags=str_replace("/opac/",
											sprintf("/%s/", $route_name),
											$tags);

		$this->view->notice=$notice;
		$this->view->tags=$tags;
	}

	//------------------------------------------------------------------------------------------------------
	// Biographie
	//------------------------------------------------------------------------------------------------------
	function biographieAction()
	{
		$notice_html=new Class_NoticeHtml($this->notice);
		$notice=$this->cls_notice->getNotice($this->id_notice,"TA");
		if(!$notice["A"]) $html.=$notice_html->getNonTrouve("Cette notice n'a pas d'auteur",true);
		else if($this->service_afi > "")
		{
			$args=array("auteur" => $notice["A"]);
			$data=Class_WebService_AllServices::runServiceAfi(8,$args);
			$html=$notice_html->getBiographie($data,$notice);
		}
		else $html= $html=$notice_html->getNonTrouve("Service non disponible");

		$this->view->notice=$notice;
		$this->view->html=$html;
	}

	//------------------------------------------------------------------------------------------------------
	// Notices similaires
	//------------------------------------------------------------------------------------------------------
	function similairesAction()	{
		$notice_html=new Class_NoticeHtml($this->notice);

		$notices=$this->cls_notice->getNoticesSimilaires($this->id_notice);
		$html=$notice_html->getListeNotices($notices, $this->view, $this->view->url(array(), null, true));
		
		$this->view->notice=$this->cls_notice->getNotice($this->id_notice,"T");
		$this->view->html=$html;
	}

}