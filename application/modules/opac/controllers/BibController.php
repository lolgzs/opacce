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
// OPAC3 - Controleur localisations
//////////////////////////////////////////////////////////////////////////////////////////
class BibController extends Zend_Controller_Action {
	private $_pathImg = "./public/admin/images/";

	function indexAction() {
		if (array_isset("id_bibs", $_SESSION["selection_bib"]))
			$this->_redirect('opac/bib?geo_zone=reset');

		$zones = Class_Zone::getLoader()->findAll();
		$bibs = array();
		$news = array();
		foreach($zones as $zone)
			$bibs = array_merge($bibs, $zone->getVisibleBibs());

		$this->view->articles = $this->_getArticlesForBibs($bibs);
		$this->view->global_map_path = sprintf("%s/photobib/global.jpg", USERFILESURL);
		$this->view->zones = $zones;
	}


	protected function _getArticlesForBibs($bibs) {
		if (Class_Profil::getCurrentProfil()->getModulePreference('bib', 
																															$this->getRequest()->getActionName(), 
																															'hide_news'))
			return array();

		$news = array();
		$art_loader = Class_Article::getLoader();

		foreach($bibs as $bib)
			$news = array_merge($news, 
													$art_loader->getArticlesByPreferences(array('id_bib' => $bib->getId())));
		return $art_loader->filterByLocaleAndWorkflow($news);
	}


	//---------------------------------------------------------------------
	// Affichage d'un territoire
	//---------------------------------------------------------------------
	protected function _prepareZoneMap() {
		// Parametres
		$id_zone = (int)$this->_request->getParam('id');
		if($id_zone < 1) $this->_redirect('opac/bib/index');

		$zone = Class_Zone::getLoader()->find($id_zone);
		if (!$zone->getCouleurTexte()) $zone->setCouleurTexte("#ffffff");
		if (!$zone->getCouleurOmbre()) $zone->setCouleurOmbre("#000000");
		if (!$zone->getTailleFonte()) $zone->setTailleFonte("12");

		$this->view->id_zone = $id_zone;
		$this->view->zone=$zone;
		$this->view->liste_bib = $zone->getVisibleBibs();
		return $this->view->liste_bib;
	}


	function zoneviewAction()	{
		$bibs = $this->_prepareZoneMap();		
		$this->view->articles = $this->_getArticlesForBibs($bibs);
	}


	function mapzoneviewAction() {
		$this->_prepareZoneMap();
	}


	function selectionAction() {
		$this->view->title = "Selection de bibliothèque";
		$class_zone = new Class_Zone();
		$class_bib = new Class_Bib();

		$zone_array = $class_zone->getAllZone();
		$this->view->territoire = $zone_array;
		$this->view->bib = $class_bib;

		// Url de retour
		$url=$_SERVER["HTTP_REFERER"];
		$pos=strPos($_SERVER["HTTP_REFERER"],"bib_select");
		if($pos) $url=substr($url,0,$pos-1);
		if(strPos($url,"?") === false) $url.="?"; else $url.="&";
		$this->view->url_retour=$url."bib_select=";

		// Selection active
		$id_bibs=$_SESSION["selection_bib"]["id_bibs"];
		if($id_bibs) {
			$this->view->sel_bib=array();
			$id_bibs=explode(",",$id_bibs);
			foreach($id_bibs as $bib) $this->view->sel_bib[$bib]=true;
		}
		else $this->view->sel_bib="all";
	}

//------------------------------------------------------------------------------------------------------
// Google maps
//------------------------------------------------------------------------------------------------------
	function mapviewAction() {
		$id_bib = (int)$this->_request->getParam('id_bib');
		if($id_bib<=0) $this->_redirect('opac/bib/index');
		$retour = $this->_request->getParam('retour');

		// Url de retour
		if($retour == "notice")	
			$this->view->url_retour = $_SESSION["recherche"]["retour_notice"];
		if(substr($retour,0,5)=="http:") 
			$this->view->url_retour=$retour;
		else {
			$this->view->url_retour = sprintf('%s/bib/bibview/id/%d', BASE_URL, $id_bib);
		}

		$class_bib = new Class_Bib();
		$bib = $class_bib->getBibById($id_bib);
		$data = ZendAfi_Filters_Serialize::unserialize($bib->GOOGLE_MAP);

		// Pas de carte
		if(!$data) 
			$this->_redirect($this->view->url_retour);

		// Création du javascript
		$init="";
		$id_couche = 1;
		foreach($data["COUCHE"] as $couche)
			{
				$root="oCouches[".$couche["ID_COUCHE"]."]";
				$init.=$root." = new Object();".NL;
				$init.=$root.".titre='".addslashes($couche["TITRE"])."';".NL;
				$init.=$root.".longitude=".$couche["LONGITUDE"].";".NL;
				$init.=$root.".latitude=".$couche["LATITUDE"].";".NL;
				$init.=$root.".echelle=".$couche["ECHELLE"].";".NL;
				$init.=$root.".points= new Array();".NL;

				$id_point = 1;
				foreach($data["COUCHE"][$id_couche]["POINT"] as $point)
					{
						$root1=$root.".points[".$id_point."]";
						$init.=$root1." = new Object();".NL;
						$init.=$root1.".titre='".addslashes($point["TITRE"])."';".NL;
						$init.=$root1.".longitude=".$point["LONGITUDE"].";".NL;
						$init.=$root1.".latitude=".$point["LATITUDE"].";".NL;
						$init.=$root1.".icone=".$point["ICONE"].";".NL;
						$init.=$root1.".adresse='".addslashes($point["ADRESSE"])."';".NL;
						$init.=$root1.".ville='".addslashes($point["VILLE"])."';".NL;
						$init.=$root1.".pays='".addslashes($point["PAYS"])."';".NL;
						$init.=$root1.".photo='".$point["PHOTO"]."';".NL;
						$init.=$root1.".infos= new Array();".NL;

						$id_info = 1;
						foreach($data["COUCHE"][$id_couche]["POINT"][$id_point]["INFO"] as $info)
							{
								$texte= urldecode($info["TEXTE"]);
								$texte = addslashes($texte);
								$root2=$root1.".infos[".$id_info."]";
								$init.=$root2." = new Object();".NL;
								$init.=$root2.".titre='".addslashes($info["TITRE"])."';".NL;
								$init.=$root2.".texte='".nl2br($texte)."';".NL;
								$id_info++;
							}
						$id_point++;
					}
				$id_couche++;
			}

		// Lire les fichiers icones
		require_once("fonctions/file_system.php");
		$icones=parse_dossier($this->_pathImg."plan_acces");
		$hIcone = '';
		if($icones)
			foreach($icones as $ico) {
				if(strRight($ico[1],4)==".png" and strRight($ico[1],5) !="s.png") {
					$index=str_replace("icon","",$ico[1]);
					$index=str_replace(".png","",$index);
					$hIcone.="hIcone[".$index."]= creer_icone('".URL_ADMIN_IMG."plan_acces/".$ico[1]."');".NL;
				}
			}
		$this->view->googleKey = getVar('CLEF_GOOGLE_MAP');
		$this->view->map = $data;
		$this->view->oCouches = $init;
		$this->view->hIcone = $hIcone;
		$this->view->id_bib = $id_bib;
	}

	function bibviewAction() {
		$id_bib = (int)$this->_request->getParam('id');
		if(!$id_bib || $id_bib==0) {
			$this->_redirect('opac/bib/index');
			return;
		}

		$bib = Class_Bib::getLoader()->find($id_bib);
		$zone = $bib->getZone();

		$this->view->url_retour = $this->view->url(array('action' => 'zoneview',
																										 'id' => $zone->getId()));
		if (3 == count($specs = explode(' ', $this->_getParam('retour')))) {
			$this->view->url_retour = $this->view->url(array('controller' => $specs[0],
																											 'action' => $specs[1],
																											 'id' => $specs[2]));
		}

		$this->view->bib = $bib;
		$this->view->zone_name = $zone->getLibelle();
		$this->view->id_bib = $bib->getId();
		$this->view->id_zone = $zone->getId();
	}


	function photobibAction() {
		$id_bib = (int)$this->_request->getParam('id_bib');
		$img="/userfiles/photobib/photoBib".$id_bib.".jpg";
		$adresse_img=getcwd().$img;

		if(! file_exists($adresse_img)) $adresse_img=getcwd()."/userfiles/photobib/photoVide.jpg";
		$handle=fopen($adresse_img,"rb");
		$data=fread ($handle, filesize($adresse_img));
		fclose($handle);

		header("Content-type: image/jpg");
		header("pragma: no-cache");
		print($data);
		exit;
	}


	function giveInfoBulle($id_bib) {
		$class_bib = new Class_Bib();
		$bib = $class_bib->getBibById($id_bib);
		return(addslashes($bib->LIBELLE));
	}
}