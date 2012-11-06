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
// OPAC3 - Controleur pour toutes les recherches
//////////////////////////////////////////////////////////////////////////////////////////

class RechercheController extends Zend_Controller_Action
{
	private $moteur;															// Instance du moteur de recherche
	private $liste;																// Instance de la classe de liste de notices
	private $preferences;													// Préférences pour la liste du résultat

//------------------------------------------------------------------------------------------------------
// Initialisation du controler
//------------------------------------------------------------------------------------------------------
	function init()
	{
		// Instanciations
		$profil = Class_Profil::getCurrentProfil();
		$this->moteur=new Class_MoteurRecherche();
		$this->view->resultat=array();

		// Formulaire de recherche
		if ($this->_request->isPost())
		{
			unset($_SESSION["recherche"]);
			$filter = new Zend_Filter_StripTags();
			foreach($_POST as $critere => $valeur) $_SESSION["recherche"]["selection"][$critere] = trim($filter->filter($this->_request->getPost($critere, false)));
		}

		// tri du résultat
		if ($tri = $this->_getParam("tri")) {
			unset($_SESSION["recherche"]["resultat"]);
			$_SESSION["recherche"]["selection"]["tri"] = $tri;
		}

		if (!isset($_SESSION["recherche"]["selection"]["tri"]))
			$_SESSION["recherche"]["selection"]["tri"] = '*';

		// elargir recherche par annexe
		if($this->_getParam("annexe")=="reset") {
			unset($_SESSION["recherche"]["selection"]["annexe"]);
			unset($_SESSION["recherche"]["resultat"]);
		}

		// Statut de la recherche
		$this->view->statut = $this->_getParam("statut");
		if($this->view->statut == "saisie")
		{
			unset($_SESSION["recherche"]["resultat"]);
			if($_SESSION["recherche"]["selection"]) foreach($_SESSION["recherche"]["selection"] as $critere => $valeur) eval('$this->view->'.$critere.'=$_SESSION["recherche"]["selection"][$critere];');
		}
		if($this->view->statut == "reset")
		{
			unset($_SESSION["recherche"]);
			$this->view->statut ="saisie";
		}

		// Facettes
		if ($this->_getParam("facette")) {
			if ($this->_getParam("facette") =="reset") 
				unset($_SESSION["recherche"]["selection"]["facette"]);
			else
			{
				$facette="[".$this->_getParam("facette")."]";
				if(strpos($_SESSION["recherche"]["selection"]["facette"],$facette) === false) 
					$_SESSION["recherche"]["selection"]["facette"].=" ".$facette;
			}
			unset($_SESSION["recherche"]["resultat"]);
		}

		// Selection de bibs
		if ($this->_getParam("bib_select")) unset($_SESSION["recherche"]["resultat"]);
		unset($_SESSION["recherche"]["selection"]["selection_bib"]);

		if (!isset($_SESSION['selection_bib']))
			$_SESSION['selection_bib'] = array();

		if (array_key_exists("id_bibs", $_SESSION["selection_bib"])) {
			$bibs=explode(",",$_SESSION["selection_bib"]["id_bibs"]);
			if (!array_key_exists("recherche", $_SESSION))
				$_SESSION["recherche"] = array("selection" => array("selection_bib" => array()),
																			 "mode" => "",
																			 "retour_notice" => "");

			$_SESSION["recherche"]["selection"]["selection_bib"] = '';
			foreach($bibs as $bib) 
				$_SESSION["recherche"]["selection"]["selection_bib"] .=" B".$bib;
		}

		// Selection de types de docs lies au profil
		if($profil->getSelTypeDoc() and !$_SESSION["recherche"]["selection"]["type_doc"]) $_SESSION["recherche"]["selection"]["type_doc"]=str_replace(";",",",$profil->getSelTypeDoc());

		// Selection de annexes liees au profil
		unset($_SESSION["recherche"]["selection"]["selection_annexe"]);
		if($profil->getSelAnnexe()) $_SESSION["recherche"]["selection"]["selection_annexe"]=str_replace(";",",",$profil->getSelAnnexe());

		// Selection de sections liees au profil
		unset($_SESSION["recherche"]["selection"]["selection_sections"]);
		if($profil->getSelSection())	{
			$sections=explode(",",$profil->getSelSection());
			foreach($sections as $section)
				$_SESSION["recherche"]["selection"]["selection_sections"].=" S".$section;
		}
		if (!array_key_exists("recherche", $_SESSION)) 
			$_SESSION["recherche"] = array("mode" => null, "retour_notice" => null);

		// Urls retour
		if (!isset($_SESSION["recherche"]['mode']))
			$_SESSION["recherche"]["mode"] = $this->_request->getActionName();

		if ($_SESSION["recherche"]["mode"]=="rebond") 
			$_SESSION["recherche"]["mode"]="simple";

		$this->view->url_retour=BASE_URL."/opac/recherche/" . $_SESSION["recherche"]["mode"];
		$this->view->url_facette=$this->view->url_retour;
		if (isset($_SESSION["recherche"]["retour_notice"])) 
			$this->view->url_retour_notice=$_SESSION["recherche"]["retour_notice"];
		$_SESSION["recherche"]["retour_notice"] = null;

		// Préférences
		$current_module=$this->_getParam("current_module");
		$this->preferences=$current_module["preferences"];

		$simple_ou_avancee = $this->_request->getActionName() == 'avancee' ? 'avancee' : 'simple';
		$this->view->url_retour_recherche_initiale = $this->view->url(['action' => $simple_ou_avancee]).'?statut=saisie';
		$this->view->url_nouvelle_recherche = $this->view->url(['action' => $simple_ou_avancee]).'?statut=reset';
	}


	public function _getListNotices($req) {
		if (!isset($this->liste))
			$this->liste=new Class_ListeNotices($this->preferences["liste_nb_par_page"],
																					$this->preferences["liste_codes"]);

		return $this->liste->getListe($req, $this->_getParam('page', 1));
	}

//------------------------------------------------------------------------------------------------------
// INDEX
//------------------------------------------------------------------------------------------------------
	function indexAction()	{
		if (!$expression = $this->_getParam('q')) {
			$this->_redirect('opac/recherche/simple?statut=reset');
			return;
		}

		$ret = $this->moteur->lancerRechercheSimple(['expressionRecherche' => $expression]);

		if ($ret["statut"]=="erreur") {
			$ret['nombre'] = 0;
			$ret['page_cours'] = 0;
			$this->view->liste = $ret;
			$this->view->resultat = $ret;
			$this->_redirect('opac/recherche/simple?statut=reset');
			return;
		}

		$facettes = $this->moteur->getFacettes($ret["req_facettes"], $this->preferences);
		$this->view->liste = $this->_getListNotices($ret["req_liste"]);
		$this->view->resultat = array_merge($facettes, $ret, ['page_cours' => 1]);
		$this->view->url_tri = BASE_URL."/recherche/simple";
		$this->view->texte_selection = $expression;
		$this->view->current_module['preferences'] = array_merge($this->view->current_module['preferences'],
																														 ['liste_format' => 3, 'liste_nb_par_page' => 10]);
		$this->renderScript('recherche/resultatRecherche.phtml');
	}


	public function simpleAction() {
    // Dernier mode de recherche
		$_SESSION["recherche"]["mode"] = 'simple';
		$_SESSION["recherche"]["retour_liste"] = $this->view->url(['controller' => 'recherche', 'action' => 'simple'], null, true);

		if ($this->view->statut == "saisie") return;

		// Lancer la recherche
		$this->view->texte_selection = $this->getTexteSelection();

		$_SESSION["recherche"]["selection"]["pertinence"] = (1 == $this->_getParam('pertinence', 0));

 		if (!isset($_SESSION["recherche"]["resultat"])) {
			$criteres = (isset($_SESSION['recherche']['selection'])) ?
				$_SESSION['recherche']['selection'] :
				array();

			$ret = $this->moteur->lancerRechercheSimple($criteres);

			if ($ret["statut"]=="erreur") {
				$ret['nombre'] = 0;
				$ret['page_cours'] = 0;
				$this->view->liste = $ret;
				$this->view->resultat = $ret;
				return;
			}

			$this->addHistoRecherche(1,$_SESSION["recherche"]["selection"]);
			$facettes = $this->moteur->getFacettes($ret["req_facettes"], $this->preferences);
			$_SESSION["recherche"]["resultat"] = array_merge($facettes, $ret);
			$this->_redirect('/recherche/simple');
			return;
		}

		// Get de la liste
		$this->view->liste = $this->_getListNotices($_SESSION["recherche"]["resultat"]["req_liste"]);

		// Variables viewer
		$this->view->resultat = $_SESSION["recherche"]["resultat"];
		$this->view->resultat["page_cours"] = $this->_getParam('page');
		$this->view->url_tri = BASE_URL."/recherche/simple";
		$this->view->is_pertinence = $_SESSION["recherche"]["selection"]["pertinence"];
		$this->view->tri = $_SESSION["recherche"]["selection"]["tri"];
	}


//------------------------------------------------------------------------------------------------------
// RECHERCHE AVANCEE
//------------------------------------------------------------------------------------------------------
	function avanceeAction()
	{
		// pour combo des annexes
		$annexes=fetchAll("select code,libelle from codif_annexe where invisible=0 order by libelle");
		if($annexes)
		{
			$this->view->annexes=array(""=>"tous");
			foreach($annexes as $annexe)
			{
				$this->view->annexes[$annexe["code"]]=$annexe["libelle"];
			}
		}

		// Dernier mode de recherche
		$_SESSION["recherche"]["mode"]="avancee";
		$_SESSION["recherche"]["retour_liste"]=$this->_request->REQUEST_URI;
		if($this->view->statut == "saisie") return;

		// Lancer la recherche
		$this->view->texte_selection=$this->getTexteSelection();
 		if(!isset($_SESSION["recherche"]["resultat"]))
 		{
 			$ret=$this->moteur->lancerRechercheAvancee($_SESSION["recherche"]["selection"]);
			if (isset($ret['statut']) && ($ret['statut']=='erreur'))
			{
				$ret["nombre"]=0;
				$this->view->liste=$ret;
				return false;
			}
			// Histo recherche
			$this->addHistoRecherche(2,$_SESSION["recherche"]["selection"]);
			// Facettes et tags
			$facettes=$this->moteur->getFacettes($ret["req_facettes"],$this->preferences);

			// Mettre les elements dans la session
			$_SESSION["recherche"]["resultat"]=array_merge($facettes,$ret);
		}

		// Get de la liste
		$this->view->liste=$this->_getListNotices($_SESSION["recherche"]["resultat"]["req_liste"]);

		// Variables viewer
		$this->view->resultat=$_SESSION["recherche"]["resultat"];
		$this->view->resultat["page_cours"] = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 0;
	}

//------------------------------------------------------------------------------------------------------
// RECHERCHE GUIDEE
//------------------------------------------------------------------------------------------------------
	function guideeAction()
	{
    // Dernier mode de recherche
		$_SESSION["recherche"]["mode"]="guidee";
		$_SESSION["recherche"]["retour_liste"]=$this->_request->REQUEST_URI;

		// Test des parametres
		$indice=$_REQUEST["rubrique"];
		if(!$indice) unset($_SESSION["recherche"]);
		$fil_ariane=$_SESSION["recherche"]["resultat"]["fil_ariane"]["fil"];

		// Rubriques
		$ret=$this->moteur->lancerRechercheGuidee($indice,$fil_ariane,$_SESSION["recherche"]["selection"]["selection_bib"]);
 		$_SESSION["recherche"]["resultat"]=$ret;

		// Notices
		if(strlen($ret["fil_ariane"]["fil"]) > 4)
		{
			$this->view->liste=$this->_getListNotices($ret["req_liste"]);
		}

		// Variables viewer
		$this->view->resultat=$_SESSION["recherche"]["resultat"];
		$this->view->resultat["page_cours"]=$_REQUEST["page"];
	}

//------------------------------------------------------------------------------------------------------
// AFFICHAGE NOTICE
//------------------------------------------------------------------------------------------------------
	public function viewnoticeAction() {
		unset($_SESSION["recherche"]["rebond"]);
		if ((new Zend_Controller_Request_Http($this->view->absoluteUrl($this->_request->REQUEST_URI)))->getModuleName() !== 'admin')
				$_SESSION["recherche"]["retour_notice"] = $this->_request->REQUEST_URI;

		$id_notice = (int)$this->_getParam('id');
		$clef_alpha = $this->_getParam('clef');

		if ($clef_alpha && ($notices = Class_Notice::getLoader()->getAllNoticesByClefAlpha($clef_alpha))) {
			if (!$id_notice && (count($notices) > 1)) {
				$query = implode(' ', array_filter(array_slice(explode('-', $clef_alpha), 0, 3)));
				$this->_redirect('/opac/recherche?'.http_build_query(['q' => $query]));
				return;
			}
			$id_notice = $notices[0]->getId();
		}

		if (!$notice = Class_Notice::find($id_notice)) {
			$this->_redirect('opac/recherche/simple');
			return;
		}

		$current_module = $this->_getParam('current_module');
		$current_module['preferences'] = $this->preferences = Class_Profil::getCurrentProfil()->getCfgModulesPreferences('recherche', 
																																																										 'viewnotice', 
																																																										 $notice->getTypeDoc());
		$current_module['action2'] = $notice->getTypeDoc();
		$this->view->current_module = $current_module;
		$this->_request->setParam('current_module', $current_module);

		$this->view->notice = $notice;
		$this->view->preferences = $this->preferences;

		if (array_isset('retour_liste', $_SESSION["recherche"]))
			$this->view->url_retour = $_SESSION["recherche"]["retour_liste"];

		$this->view->display_modifier_vignette_link = Class_Users::isCurrentUserCanAccesBackend() && $notice->isVignetteUpdatableToCacheServer();

		// Pour les reseaux sociaux
		$this->view->titreAdd(strip_tags($this->view->notice->getTitrePrincipal()));
		if ($auteur = $this->view->notice->getAuteurPrincipal()) 
			$this->view->nomSite .= " / " . $auteur;

		// Picto du genre
		if (($genres = $notice->getChampNotice("G", $notice->getFacettes()))
				&& ($genre = Class_CodifGenre::find($genres[0]['id']))
				&& ('_vide.gif' != $genre->getPicto()))	{
				$this->view->picto_genre = $genre->getPicto();
		}

		// Url panier
		$this->view->url_panier=BASE_URL."/opac/panier?id_notice=".$id_notice;

		// Stats visualisation
		$stat=new  Class_StatsNotices();
		$stat->addStatVisu($id_notice);

		Class_ScriptLoader::getInstance()->loadBabeltheque();
	}


//------------------------------------------------------------------------------------------------------
// Lecture de la notice pour Read Speaker
//------------------------------------------------------------------------------------------------------
	function readnoticeAction()	{
		$this
			->getHelper('ViewRenderer')
			->setLayoutScript('readspeaker.phtml');

		$id_notice=intVal($this->_request->id);
		$class_notice=new Class_Notice();
		$this->view->notice=$class_notice->getNoticeDetail($id_notice,$this->preferences);
	}

//------------------------------------------------------------------------------------------------------
// LIEN REBONDISSANT
//------------------------------------------------------------------------------------------------------
	function rebondAction()	{
		// Lancer la recherche
 		$code = $this->_getParam('code_rebond');
		$_SESSION["recherche"]["mode"]="rebond";
 		$_SESSION["recherche"]["selection"]["code_rebond"]=$code;
 		$_SESSION["recherche"]["retour_liste"]=$this->_request->REQUEST_URI;

 		$ret=$this->moteur->lancerRechercheRebond($_SESSION["recherche"]["selection"]);
		if($ret["statut"]=="erreur") {
			$this->view->liste = $ret;
			$this->view->resultat = $ret;
		} else {
			// Facettes et tags
			$facettes=$this->moteur->getFacettes($ret["req_facettes"],$this->preferences);

			// Mettre les elements dans la session
			$resultat = array_merge($facettes,$ret);
			$_SESSION["recherche"]["resultat"] = $resultat;
			$this->view->resultat = $resultat;
			$this->view->liste=$this->_getListNotices($ret["req_liste"]);
		}

		$this->view->resultat["url_retour"] = isset($this->view->resultat["url_retour"]) 
			? $this->view->resultat["url_retour"]."?code_rebond=".$code
			: $this->view->url(['action' => 'simple']);

		$this->view->resultat["page_cours"] = $this->_getParam('page');
 		$this->view->texte_selection = $this->getTexteSelection();
		$this->view->url_facette = BASE_URL."/opac/recherche/rebond?code_rebond=".$code;

		$params = $this->_request->getParams();
    $this->view->tri = isset($params['tri']) ? $params['tri'] : '*';
		unset($params['tri']);
		unset($params['current_module']);
		$this->view->url_retour = $this->view->url($params);
	}


	private function getTexteSelection() {
		$mode = $_SESSION["recherche"]["mode"];
		$rech = $_SESSION["recherche"]["selection"];

		// facettes
		$facette = null;
		if (isset($rech['facette'])) {
			$items = explode(" ",$rech["facette"]);
			foreach ($items as $item) {
				$item = str_replace("[", "", $item);
				$item = str_replace("]", "", $item);
				if (!trim($item)) continue;
				if ($facette) 
					$facette .= ', ';
				if ($item[0] == "T")
					$item[0] = "t";

				$facette .= Class_Codification::getNomChamp($item). ' = ';
				$facette .= Class_Codification::getLibelleFacette($item);
			}

			$facette = BR . $this->view->_("Facettes : %s", $facette);
		}

		// Rebond
		if(isset($rech["code_rebond"])) {
			$texte = Class_Codification::getNomChamp($rech["code_rebond"]) . " : ";
			$texte .= Class_Codification::getLibelleFacette($rech["code_rebond"]);
			if (isset($rech["type_doc"])) 
				$texte .= BR . $this->view->_("Type de document : %s", Class_Codification::getLibelleFacette("T" . $rech["type_doc"]));
			return $texte . $facette;
		}

		// Recherche simple
		if ('simple' == $mode) {
			$texte = "Recherche : ". $rech["expressionRecherche"];

			if (isset($rech["type_doc"]) && $rech['type_doc']) 
				$texte .= $this->view->_(", type de document: %s", Class_Codification::getLibelleFacette("T".$rech["type_doc"]));

			if (isset($rech["annexe"]) && $rech['annexe'])	{
				$texte .= BR;
				$texte .= $this->view->_("Site : %s", Class_Codification::getLibelleFacette("Y".$rech["annexe"]));
				$texte .= '&nbsp;&raquo;&nbsp;<a href="' . $this->view->url(). '?annexe=reset">Elargir la recherche à tous les sites</a>';
			}
			return $texte . $facette;
		}

		// Recherche avancee
		$texte = '';
		if ('avancee' == $mode) {
			$operateur = array("and" => $this->view->_(" et "), 
												 "or" => $this->view->_(" ou "),
												 "and not" => $this->view->_(" sauf "));

			$signe = (isset($rech["type_recherche"]) && ($rech["type_recherche"]=="commence")) ? 
				$this->view->_(" commence par :"):
				$this->view->_(" contient :");

			if (isset($rech["rech_titres"])) 
				$texte .= $this->view->_(", Titre") . $signe . $rech["rech_titres"];
			if (isset($rech["rech_auteurs"])) 
				$texte .= ", " . $operateur[$rech["operateur_auteurs"]] . "Auteur" . $signe . $rech["rech_auteurs"];
			if (isset($rech["rech_matieres"])) 
				$texte .= ", " . $operateur[$rech["operateur_matieres"]] . "Sujet" . $signe . $rech["rech_matieres"];
			if (isset($rech["rech_dewey"]))
				$texte .= ", " . $operateur[$rech["operateur_dewey"]] . "Dewey /pcdm4" . $signe . $rech["rech_dewey"];
			if (isset($rech["rech_editeur"]))
				$texte.= ", ". $operateur[$rech["operateur_editeur"]] . "Editeur" . $signe . $rech["rech_editeur"];
			if (isset($rech["rech_collection"]))
				$texte.= ", " . $operateur[$rech["operateur_collection"]] . "Collection" . $signe . $rech["rech_collection"];
			if (isset($rech["type_doc"]))
				$texte .= '  ' . BR . $this->view->_("Type de document : %s", Class_Codification::getLibelleFacette("T".$rech["type_doc"]));

			if (isset($rech["annexe"])) {
				if ($texte) 
					$texte .= BR;
				$texte .= $this->view->_("Site : %s", Class_Codification::getLibelleFacette("Y".$rech["annexe"]));
				$texte .= '&nbsp;&raquo;&nbsp;<a href="'. $this->view->url() . '?annexe=reset">Elargir la recherche à tous les sites</a>';
			}

			if (isset($rech["annee_debut"]) && isset($rech["annee_fin"])) {
				$texte .= BR . $this->view->_("Documents parus ");
				$texte .= ($rech["annee_debut"] == $rech["annee_fin"]) ?
					"en " . $rech["annee_debut"] :
					$this->view->_("entre %s et %s", $rech["annee_debut"], $rech["annee_fin"]);
			}
			$texte = substr($texte,2);
		}
		return $texte . $facette;
	}

//------------------------------------------------------------------------------------------------------
// Relancer une recherche en historique
//------------------------------------------------------------------------------------------------------
	function histoAction()
	{
		$ligne=$_SESSION["histo_recherche"][$_REQUEST["id_histo"]];
		$_SESSION["recherche"]["selection"]=$ligne["selection"];
		switch($ligne["type"])
		{
			case 1: { $_SESSION["recherche"]["mode"]="simple"; $this->_redirect('opac/recherche/simple'); }
			case 2: { $_SESSION["recherche"]["mode"]="avancee";$this->_redirect('opac/recherche/avancee'); }
		}
	}

//------------------------------------------------------------------------------------------------------
// Memo historique de recherche dans la session
//------------------------------------------------------------------------------------------------------
	private function addHistoRecherche($type,$criteres)
	{
		$ligne["type"]=$type;
		$ligne["selection"]=$criteres;
		// controle si existe deja
		if(isset($_SESSION["histo_recherche"]))
		{
			for($i=0; $i < count($_SESSION["histo_recherche"]); $i++) if($_SESSION["histo_recherche"][$i] == $ligne) return;
		}
		$_SESSION["histo_recherche"][]=$ligne;
	}


	function reseauAction() {
		// Désactiver le renderer
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();

		$id_notice = $this->_getParam('id_notice');
		$type_doc = $this->_getParam('type_doc');
		$helper = new ZendAfi_View_Helper_ReseauxSociaux();
		echo $helper->reseauxSociaux('notice', $id_notice, $type_doc);
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
		$img->getFluxImage($_REQUEST["clef"],$_REQUEST["id_notice"]);
	}

//------------------------------------------------------------------------------------------------------
// Reservation en ligne (mode mail)
//------------------------------------------------------------------------------------------------------
	public function reservationAction()
	{
		$class_bib = new Class_Bib();
		if ($this->_request->isPost())
		{
			$id_notice = (int)$this->_request->getPost('id_notice');
			$id_bib = (int)$this->_request->getPost('id_bib');
			$mail_bib = $this->_request->getPost('mail_bib');
			$bib_name = $this->_request->getPost('bib_name');
			$user_name = $this->_request->getPost('user_name');
			$demande = $this->_request->getPost('demande');
			$user_mail = $this->_request->getPost('user_mail');
			$code_saisi = $this->_request->getPost('code_saisi');
			$cote = $this->_request->getPost('cote');

			// Test field
			$Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
			$messages = array();
			if(trim($user_name) =="")
				$messages []= $this->view->_("Vous n'avez pas saisi vos Nom et Prénom :");
			if(strlen($demande) <=1)
				$messages []= $this->view->_("Vous n'avez pas saisi de demande :");
			if(!preg_match($Syntaxe,$user_mail))
				$messages []= $this->view->_("Votre adresse e-mail est incorrecte.");
			if($code_saisi != $_SESSION["captcha_code"])
				$messages []= $this->view->_("Le code anti-spam est incorrect.");
			$errorMessage = implode(',', $messages);

			if($errorMessage=="")	{
				// Stats réservation
				//$stat=new Class_StatsNotices();
				//$stat->addStatReservation($id_notice);

				$class_notice = new Class_Notice();
				$notice = $class_notice->getNotice($id_notice,"TAE");
				$texte_mail_resa = str_replace('%0D%0A',chr(13).chr(10),getVar('TEXTE_MAIL_RESA'));
				$texte_mail_resa_ok = urldecode($texte_mail_resa);

				// Envoie de mail
				$message_user = sprintf("%s\r\n\r\n", utf8_decode($texte_mail_resa_ok));
				$messages = array();
				$messages []= $this->view->_("Nom et prénom : %s", $user_name);
				$messages []= '';
				$messages []= utf8_decode($this->view->_("Notice réservée : "));
				$messages []= $this->view->_("Titre : %s", utf8_decode($notice["T"]));
				$messages []= $this->view->_("Auteur : %s", utf8_decode($notice["A"]));
				$messages []= $this->view->_("Editeur : %s", utf8_decode($notice["E"]));
				$messages []= $this->view->_("Cote : %s", utf8_decode($cote));
				$messages []= '';
				$messages []= utf8_decode($this->view->_("Message du demandeur :"));
				$messages []= utf8_decode($demande);
				$message = implode("\r\n", $messages);

				//pour la bibliothèque
				$mail = new ZendAfi_Mail('utf8');
				$mail
					->setSubject(utf8_decode($this->view->_("Demande de réservation de document")))
					->setBodyText($message)
					->setFrom($user_mail,
										$user_name)
					->addTo($mail_bib)
					->send();

				//pour l'utilisateur
				$mail = new ZendAfi_Mail('utf8');
				$mail
					->setSubject(utf8_decode($this->view->_("Demande de réservation de document")))
					->setBodyText($message_user.$message)
					->setFrom('nobody@noreply.fr',
										Class_Bib::getLoader()->find($id_bib)->getLibelle())
					->addTo($user_mail)
					->send();

				$this->_redirect('opac/recherche/viewnotice/id/'.$id_notice."?type_doc=".$notice["type_doc"]);
			}
			else
			{
				$bib = $class_bib->getBibById($id_bib);
				$this->view->id_bib = $id_bib;
				$this->view->nom_bib = $bib->LIBELLE;
				$this->view->mail_bib = $bib->MAIL;
				$this->view->id_notice = $id_notice;
				$this->view->errorMessage = $errorMessage;
				$this->view->user_name = $user_name;
				$this->view->demande = $demande;
				$this->view->id_notice = $id_notice;
				$this->view->user_mail = $user_mail;

				$resa = getVar("RESA_CONDITION");
				$resa_condition = str_replace('%0D%0A','<br />',$resa);
				$this->view->condition_resa = urldecode($resa_condition);
			}
		}
		// Entree dans le formulaire
		else
		{
			// Parametres
			$id_bib = (int)$this->_request->getParam('b');
			$id_notice = (int)$this->_request->getParam('n');
			$id_origine = $this->_request->getParam('id_origine');
			$cote = $this->_request->getParam('cote');

			// Mode mail
			$bib = $class_bib->getBibById($id_bib);
			$this->view->id_bib = $id_bib;
			$this->view->nom_bib = $bib->LIBELLE;
			$this->view->mail_bib = $bib->MAIL;
			$this->view->id_notice = $id_notice;
			$this->view->cote = $cote;

			$resa = getVar("RESA_CONDITION");
			$resa_condition = str_replace('%0D%0A','<br />',$resa);
			$this->view->condition_resa = urldecode($resa_condition);
		}
	}
//------------------------------------------------------------------------------------------------------
// Reservation en ligne (mode sigb)
//------------------------------------------------------------------------------------------------------
	function reservationajaxAction() {
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender(); 
		if (!Class_Users::getLoader()->getIdentity()) {
			print(json_encode('http://' . $_SERVER['SERVER_NAME'] . BASE_URL.'/opac/auth/ajaxlogin'));
			return;
		}
			
		
		// Parametres
		$id_bib = (int)$this->_request->getParam('id_bib');
		$id_origine = $this->_request->getParam('id_origine');
		$code_annexe = $this->_request->getParam('code_annexe');

		// appel de la classe de communication avec le sigb
		$comm = new Class_CommSigb();
		$ret = $comm->ReserverExemplaire($id_bib,$id_origine, $code_annexe);

		// Modes sigb
		if ($ret["erreur"]) $message=$ret["erreur"];
		else if ($ret["popup"])	$message=$ret["popup"];
		else $message=$this->view->_("Votre réservation est enregistrée.");

		// Retour

		print(json_encode($message));
		exit;
	}


	public function reservationPickupAjaxAction() {
		$this->getHelper('ViewRenderer')->setLayoutScript('iframe.phtml');
		Class_ScriptLoader::getInstance()
			->loadJQuery()
			->addAdminScript('onload_utils')
			->addOPACStyleSheet('global')
			->addSkinStyleSheet('global');

		$annexes = Class_CodifAnnexe::findAllByPickup();

		$form = $this->view->newForm(array('id' => 'pickup',
																			 'class' => 'zend_form'))
			->setMethod(Zend_Form::METHOD_POST)
			->addElement('Radio', 'code_annexe', array('required' => true,
																								 'allowEmpty' => false))
			->addDisplayGroup(array('code_annexe'), 'group', array('legend' => 'Site de retrait'))
			->addElement('Submit', 'Valider', array('onclick' => 'javascript:parent.reservationPickupAjaxConfirm(this.form);return false;'))
			->addElement('Submit', 'Annuler', array('onclick' => 'javascript:parent.reservationPickupAjaxCancel();return false;'));
			
		$radio = $form->getElement('code_annexe');
		foreach ($annexes as $annexe)
			$radio->addMultiOption($annexe->getCode(), $annexe->getLibelle());
		$radio->setValue($this->_getParam('code_annexe'));

		$this->view->form = $form;
	}
}
