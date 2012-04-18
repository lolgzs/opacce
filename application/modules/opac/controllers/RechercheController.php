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
		if($tri = $this->_request->getParam("tri"))
		{
			unset($_SESSION["recherche"]["resultat"]);
			$_SESSION["recherche"]["selection"]["tri"] = $this->_request->getParam("tri");
		}

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
		if($this->_getParam("facette"))
		{
			if($_REQUEST["facette"]=="reset") unset($_SESSION["recherche"]["selection"]["facette"]);
			else
			{
				$facette="[".$_REQUEST["facette"]."]";
				if(strpos($_SESSION["recherche"]["selection"]["facette"],$facette) === false) $_SESSION["recherche"]["selection"]["facette"].=" ".$facette;
			}
			unset($_SESSION["recherche"]["resultat"]);
		}

		// Selection de bibs
		if ($this->_getParam("bib_select")) unset($_SESSION["recherche"]["resultat"]);
		unset($_SESSION["recherche"]["selection"]["selection_bib"]);

		if (array_key_exists("id_bibs", $_SESSION["selection_bib"]))
		{
			$bibs=explode(",",$_SESSION["selection_bib"]["id_bibs"]);
			if (!array_key_exists("recherche", $_SESSION))
				$_SESSION["recherche"] = array("selection" => array("selection_bib" => array()),
																			 "mode" => "",
																			 "retour_notice" => "");

			foreach($bibs as $bib) $_SESSION["recherche"]["selection"]["selection_bib"].=" B".$bib;
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
		if (!array_key_exists("recherche", $_SESSION)) $_SESSION["recherche"] = array("mode" => null, "retour_notice" => null);

		// Urls retour
		if(!$_SESSION["recherche"]["mode"])
		$_SESSION["recherche"]["mode"]=$this->_request->action;
		if($_SESSION["recherche"]["mode"]=="rebond") $_SESSION["recherche"]["mode"]="simple";
		$this->view->url_retour=BASE_URL."/opac/recherche/".$_SESSION["recherche"]["mode"];
		$this->view->url_facette=$this->view->url_retour;
		if($_SESSION["recherche"]["retour_notice"]) $this->view->url_retour_notice=$_SESSION["recherche"]["retour_notice"];
		$_SESSION["recherche"]["retour_notice"] = null;

		// Préférences
		$current_module=$this->_getParam("current_module");
		$this->preferences=$current_module["preferences"];
	}


	public function _getListNotices($req) {
		if (!isset($this->liste))
			$this->liste=new Class_ListeNotices($this->preferences["liste_nb_par_page"],
																					$this->preferences["liste_codes"]);

		return $this->liste->getListe($req);
	}

//------------------------------------------------------------------------------------------------------
// INDEX
//------------------------------------------------------------------------------------------------------
	function indexAction()
	{
		$this->_redirect('opac/recherche/simple?statut=reset');
	}

//------------------------------------------------------------------------------------------------------
// RECHERCHE SIMPLE
//------------------------------------------------------------------------------------------------------
	function simpleAction()
	{
    // Dernier mode de recherche
		$_SESSION["recherche"]["mode"]="simple";
		$_SESSION["recherche"]["retour_liste"]=$this->_request->REQUEST_URI;
		if($this->view->statut == "saisie") return;

		// Lancer la recherche
		$this->view->texte_selection=$this->getTexteSelection();
		if($_REQUEST["pertinence"] == 1) $_SESSION["recherche"]["selection"]["pertinence"]=true;
 		if(!$_SESSION["recherche"]["resultat"])
 		{
			if (!$criteres = $_SESSION["recherche"]["selection"])
			$criteres = array();
 			$ret=$this->moteur->lancerRechercheSimple($criteres);
			if($ret["statut"]=="erreur")
			{
				$ret["nombre"]=0;
				$this->view->liste=$ret;
				return false;
			}
			// Histo recherche
			$this->addHistoRecherche(1,$_SESSION["recherche"]["selection"]);
			// Facettes et tags
			$facettes=$this->moteur->getFacettes($ret["req_facettes"],$this->preferences);
			// Mettre les elements dans la session
			$_SESSION["recherche"]["resultat"]=array_merge($facettes,$ret);
		}

		// Get de la liste
		$this->view->liste=$this->_getListNotices($_SESSION["recherche"]["resultat"]["req_liste"]);

		// Variables viewer
		$this->view->resultat=$_SESSION["recherche"]["resultat"];
		$this->view->resultat["page_cours"]=$_REQUEST["page"];
		$this->view->url_tri=BASE_URL."/recherche/simple";
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
 		if(!$_SESSION["recherche"]["resultat"])
 		{
 			$ret=$this->moteur->lancerRechercheAvancee($_SESSION["recherche"]["selection"]);
			if($ret["statut"]=="erreur")
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
		$this->view->resultat["page_cours"]=$_REQUEST["page"];
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
	function viewnoticeAction()
	{
		unset($_SESSION["recherche"]["rebond"]);
		$_SESSION["recherche"]["retour_notice"]=$this->_request->REQUEST_URI;

		if (array_isset('retour_liste', $_SESSION["recherche"]))
			$this->view->url_retour=$_SESSION["recherche"]["retour_liste"];

		// Lire la notice
		$id_notice=intVal($this->_request->id);
		$oNotice=new Class_Notice();
		$this->view->notice=$oNotice->getNoticeDetail($id_notice,$this->preferences);
		if(!$this->view->notice) $this->_redirect('opac/recherche/simple');
		$this->view->url_img=Class_WebService_Vignette::getUrl($this->view->notice["id_notice"],false);

		// Pour les reseaux sociaux
		$this->view->titreAdd(strip_tags($this->view->notice["titre_principal"]));
		if($this->view->notice["auteur_principal"] > "") $this->view->nomSite.=" / " . $this->view->notice["auteur_principal"];

		// Picto du genre
		$genre=$oNotice->getChampNotice("G", $this->view->notice["facettes"]);
		if($genre)
		{
			$picto_genre=fetchOne("select picto from codif_genre where id_genre=".$genre[0]["id"]);
			if($picto_genre == "_vide.gif") $picto_genre="";
			$this->view->picto_genre=$picto_genre;
		}

		// Url panier
		$this->view->url_panier=BASE_URL."/opac/panier?id_notice=".$id_notice;

		// Stats visualisation
		$stat=new  Class_StatsNotices();
		$stat->addStatVisu($id_notice);
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
	function rebondAction()
	{
		// Lancer la recherche
 		$code=$_REQUEST["code_rebond"];
 		$_SESSION["recherche"]["selection"]["code_rebond"]=$code;
 		$_SESSION["recherche"]["retour_liste"]=$this->_request->REQUEST_URI;

 		$this->view->texte_selection=$this->getTexteSelection();
 		$ret=$this->moteur->lancerRechercheRebond($_SESSION["recherche"]["selection"]);
		if($ret["statut"]=="erreur")
		{
			$ret["nombre"]=0;
			$this->view->liste=$ret;
			return false;
		}

		// Facettes et tags
		$facettes=$this->moteur->getFacettes($ret["req_facettes"],$this->preferences);

		// Mettre les elements dans la session
		$_SESSION["recherche"]["resultat"]=array_merge($facettes,$ret);

		// Get de la liste
		$this->view->liste=$this->_getListNotices($ret["req_liste"]);

		// Variables viewer
		$this->view->resultat=$_SESSION["recherche"]["resultat"];
		$this->view->resultat["url_retour"].="?code_rebond=".$code;
		$this->view->url_facette=BASE_URL."/opac/recherche/rebond?code_rebond=".$code;
		$this->view->resultat["page_cours"]=$_REQUEST["page"];
	}

//------------------------------------------------------------------------------------------------------
// CALCUL DU TEXTE DES CRITERES DE SELECTION
//------------------------------------------------------------------------------------------------------
	private function getTexteSelection()
	{
		$mode=$_SESSION["recherche"]["mode"];
		$rech=$_SESSION["recherche"]["selection"];

		// facettes
		if($rech["facette"])
		{
			$items=explode(" ",$rech["facette"]);
			foreach($items as $item)
			{
				$item=str_replace("[","",$item);
				$item=str_replace("]","",$item);
				if(!trim($item)) continue;
				if($facette) $facette.=", ";
				if($item[0]=="T") $item[0]="t";
				$facette.=Class_Codification::getNomChamp($item). ' = ';
				$facette.= Class_Codification::getLibelleFacette($item);
			}
			$facette=BR.$this->view->_("Facettes : %s", $facette);
		}

		// Rebond
		if($rech["code_rebond"])
		{
			$texte=Class_Codification::getNomChamp($rech["code_rebond"])." : ";
			$texte.=Class_Codification::getLibelleFacette($rech["code_rebond"]);
			if($rech["type_doc"]) $texte.=BR.$this->view->_("Type de document : %s", Class_Codification::getLibelleFacette("T".$rech["type_doc"]));
		}

		// Recherche simple
		elseif($mode == "simple")
		{
			$texte="Recherche : ". $rech["expressionRecherche"];
			if($rech["type_doc"]) $texte.=$this->view->_(", type de document: %s", Class_Codification::getLibelleFacette("T".$rech["type_doc"]));
			if($rech["annexe"])
			{
				if(!$texte)$texte="  "; else $texte.=BR;
				$texte.=$this->view->_("Site : %s", Class_Codification::getLibelleFacette("Y".$rech["annexe"]));
				$texte.='&nbsp;&raquo;&nbsp;<a href="'.BASE_URL.'/recherche/simple?annexe=reset">Elargir la recherche à tous les sites</a>';
			}
		}
		// Recherche avancee
		elseif($mode=="avancee")
		{
			$operateur=array("and" => $this->view->_(" et "), "or" => $this->view->_(" ou "),"and not" => $this->view->_(" sauf "));
			if($rech["type_recherche"]=="commence") $signe =$this->view->_(" commence par :"); else $signe =$this->view->_(" contient :");
			if($rech["rech_titres"]) $texte=$this->view->_(", Titre").$signe.$rech["rech_titres"];
			if($rech["rech_auteurs"]) $texte.= ", ".$operateur[$rech["operateur_auteurs"]]."Auteur".$signe.$rech["rech_auteurs"];
			if($rech["rech_matieres"]) $texte.= ", ".$operateur[$rech["operateur_matieres"]]."Sujet".$signe.$rech["rech_matieres"];
			if($rech["rech_dewey"]) $texte.= ", ".$operateur[$rech["operateur_dewey"]]."Dewey /pcdm4".$signe.$rech["rech_dewey"];
			if($rech["rech_editeur"]) $texte.= ", ".$operateur[$rech["operateur_editeur"]]."Editeur".$signe.$rech["rech_editeur"];
			if($rech["rech_collection"]) $texte.= ", ".$operateur[$rech["operateur_collection"]]."Collection".$signe.$rech["rech_collection"];
			if($rech["type_doc"]) $texte.=BR.$this->view->_("Type de document : %s", Class_Codification::getLibelleFacette("T".$rech["type_doc"]));
			if($rech["annexe"])
			{
				if(!$texte)$texte="  "; else $texte.=BR;
				$texte.=$this->view->_("Site : %s", Class_Codification::getLibelleFacette("Y".$rech["annexe"]));
				$texte.='&nbsp;&raquo;&nbsp;<a href="'.BASE_URL.'/recherche/avancee?annexe=reset">Elargir la recherche à tous les sites</a>';
			}
			if($rech["annee_debut"] and $rech["annee_fin"])
			{
				$texte.=BR.$this->view->_("Documents parus ");
				if($rech["annee_debut"] == $rech["annee_fin"])$texte.="en ".$rech["annee_debut"];
				else $texte.=$this->view->_("entre %s et %s", $rech["annee_debut"], $rech["annee_fin"]);
			}
			$texte=substr($texte,2);
		}
		$texte.=$facette;
		return $texte;
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

			if($errorMessage=="")
			{
				// Stats réservation
				$stat=new Class_StatsNotices();
				$stat->addStatReservation($id_notice);

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

				ini_set('sendmail_from', 'nobody@afi-sa.net');
				// Pour la bib
				$header = "From: ". utf8_decode($user_name) . " <" . $user_mail . ">\r\n";
				mail($mail_bib, utf8_decode($this->view->_("Demande de réservation de document")), $message, $header);
				// Pour le user
				$header_user = "From: Calice68 <nobody@calice68.fr>\r\n";
				mail($user_mail, utf8_decode($this->view->_("Demande de réservation de document")), $message_user.$message, $header_user);
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
