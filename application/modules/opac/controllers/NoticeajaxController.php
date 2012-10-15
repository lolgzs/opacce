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

class NoticeAjaxController extends Zend_Controller_Action {
	private $notice;								// Instance de la classe notice
	private $notice_html;						// Instance de la classe html notice
	private $service_afi;						// Web service afi actif ou pas


	public function init() {
		// Recup parametres
		$this->id_notice=str_replace("N","", $this->_request->getParam("id_notice"));
		if (!$this->notice = Class_Notice::getLoader()->find($this->id_notice))
			$this->notice = new Class_Notice();

		$this->notice_html=new Class_NoticeHtml();
		
		// Desactiver le view renderer normal pour tous les modes sauf notice
		if($this->_request->getParam("action") != "notice") {
			$viewRenderer = $this->getHelper('ViewRenderer');
			$viewRenderer->setNoRender();
			$onglet = $this->_getParam("onglet");
			if (strPos($onglet,"_onglet_") !== false) 
				$this->notice_html->initHautOnglet($onglet);
		}

		// Test services afi
		$this->service_afi = Class_CosmoVar::get('url_services');
	}


	public function noticeAction() {
		// Preferences d'affichage
		$current_module = $this->_getParam("current_module");
		$preferences = $current_module["preferences"];
		
		// Lire la notice
		if (!$this->view->notice = Class_Notice::find($this->id_notice))
			$this->_redirect('opac/recherche/simple');

		// Url panier
		$user = ZendAfi_Auth::getInstance()->getIdentity();
		$this->view->url_panier = "fonction_abonne('".$user->ID_USER."','/opac/abonne/panier?id=".$this->id_notice."')";
		
		// View
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('noticeajax/notice.phtml');
		
		// Stats visualisation
		$stat = new  Class_StatsNotices();
		$stat->addStatVisu($this->id_notice);
		$this->view->preferences = $preferences;
	}
	

	public function tagsAction() {
		$tags=$this->notice->getTags($this->id_notice);
		$html=$this->notice_html->getTags($tags, $this->id_notice);

		$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
		$this->getResponse()->setBody($html . Class_ScriptLoader::getInstance()->html());
	}
	

	public function exemplairesAction()	{
		session_write_close();

		$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
	
		if (!$this->id_notice) {
			$this->getResponse()->setBody('');
			return;
		}

		// Condition oeuvre ou id_notice
		$clef_oeuvre=fetchOne("select CLEF_OEUVRE from notices where id_notice=" . $this->id_notice);
		if (array_key_exists('data', $_REQUEST) 
				&& $_REQUEST["data"]=="OEUVRE") {
			$aff = "oeuvre";
			$nb_notices_oeuvre = 0;
			$notices = fetchAll("select id_notice from notices where clef_oeuvre='$clef_oeuvre' and id_notice!=" . $this->id_notice);
			if ($notices) {
				foreach ($notices as $notice) {
					if ($insql)
						$insql .= ',';
					$insql .= $notice["id_notice"];
				}
			}
			$cond[] = "id_notice in(" . $insql . ")";
		} else {
			$aff = "normal";
			$nb_notices_oeuvre = fetchOne("select count(*) from notices where clef_oeuvre='$clef_oeuvre' and id_notice!=" . $this->id_notice);
			$cond[] = "id_notice=" . $this->id_notice;
		}

		// Conditions liees au profil
		$sel_section = Class_Profil::getCurrentProfil()->getSelSection();

		if ($sel_section)
			$cond[] = "section in(" . implode(',', explode(';', $sel_section)) . ")";

		if (array_key_exists('selection_bib', $_SESSION)
				&& array_key_exists('id_bibs', $_SESSION['selection_bib'])) {
			$sel_bib = $_SESSION["selection_bib"]["id_bibs"];
			if ($sel_bib)
				$cond[] = "id_bib in(" . $sel_bib . ")";
		}
		$where = getWhereSql($cond);

		$data = $this->_loadExemplaireWhere($where);
		if (!$data) {
			$where = " where " . $cond[0];
			$data = $this->_loadExemplaireWhere($where);
		}

		// Tableau
		$html=$this->notice_html->getExemplaires($data,$nb_notices_oeuvre,$aff);

		$this->getResponse()->setBody($html);
	}


	protected function _loadExemplaireWhere($where) {
		// Lire les notices groupees ou pas
		if (0 == $this->notice_html->preferences["exemplaires"]["grouper"])
			return fetchAll("Select id_notice,id_bib,cote,count(*) from exemplaires " . $where . " group by 1,2,3" );
		
		return fetchAll("Select * from exemplaires " . $where);
	}


	public function localisationAction() {
		$id_bib=$this->_request->getParam("id_bib");
		$cote=$this->_request->getParam("cote");
		$code_barres=$this->_request->getParam("code_barres");

		// Recup des donnees
		$cls_loc=new Class_Localisation();
		$data=$cls_loc->getLocFromExemplaire($id_bib,$cote,$code_barres);

		// Retour
		$ret =json_encode($data);
		$this->_sendResponse($ret);
	}


	public function detailAction() {
		$notice=$this->notice->getTousChamps($this->id_notice);
		if($notice["type_doc"]==2) {
			$notice=$this->notice->getArticlesPeriodique($this->id_notice);
			$html=$this->notice_html->getArticlesPeriodique($notice);
		}
		else $html=$this->notice_html->getNoticeDetaillee($notice,$_REQUEST["onglet"]);

		$this->_sendResponse($html);
	}


	public function similairesAction()	{
		$notices=$this->notice->getNoticesSimilaires($this->id_notice);
		$html = $this->notice_html->getListeNotices($notices, $this->view);
		$this->_sendResponse($html);
	}
	

	public function resumeAction()	{
		session_write_close();
		$avis = $this->notice->findAllResumes();
		$html=$this->notice_html->getResume($avis);
		$this->_sendResponse($html);
	}


	public function vignetteAction() {
		session_write_close();
		$img=new Class_WebService_Vignette();
		$img->getFluxImage($_REQUEST["clef"],$_REQUEST["id_notice"]);
	}


	public function biographieAction() {
		session_write_close();
		$this->_sendResponse($this->view->biographie($this->notice));
	}
	

	public function bandeannonceAction() {
		$notice=$this->notice->getNotice($this->id_notice,"TA");
		if($this->service_afi > "")
		{
			$args=array("titre" => $notice["T"],"auteur" => $notice["A"]);
			$data=Class_WebService_AllServices::runServiceAfi(6,$args);
			$source=$data["source"];
			$bo=$data["player"];
			$html=$this->notice_html->getBandeAnnonce($source,$bo);
		}
		else $html= $html=$this->notice_html->getNonTrouve($this->view->_("Service non disponible"),true);
		$this->_sendResponse($html);
	}
	

	public function photosAction() {
		$notice=$this->notice->getNotice($this->id_notice,"TA");
		
		// Docs sonores : lastFm
		if($notice["type_doc"]==3) {
			$lastfm=new Class_WebService_Lastfm();
			$photos=$lastfm->getPhotos($notice["A"]);
		}
		
		$html=$this->notice_html->getPhotos($photos);	
		$this->_sendResponse($html);
	}
	

	public function bibliographieAction() {
		$notice=$this->notice->getNotice($this->id_notice,"TA");
		
		// Docs sonores : lastFm
		if ($notice["type_doc"]==3) {
			$lastfm=new Class_WebService_Lastfm();
			$biblio=$lastfm->getDiscographie($notice["A"]);
		}
		
		$html=$this->notice_html->getBibliographie($biblio,$notice["A"]);	
		$this->_sendResponse($html);
	}


	public function resnumeriquesAction() {
		$html = sprintf('<p>%s</p>', $this->view->_('Aucune ressource correspondante'));
		if (null !== $exemplaire = Class_Exemplaire::getLoader()->findFirstBy(array('id_notice' => $this->id_notice)))
			$html = $this->view->renderAlbum($exemplaire->getAlbum());
		$this->_sendResponse($html.Class_ScriptLoader::getInstance()->html());
	}
	

	public function morceauxAction() {
		$notice=$this->notice->getNotice($this->id_notice,"TA");

		// Chez amazon
		$source = "Amazon";
		$amazon = new Class_WebService_AmazonSonores();
		$morceaux = $amazon->rend_notice_ean($notice["ean"]);

		// Chez LastFm
		if (!$morceaux["nb_resultats"]) 
		{
			$source="Last.fm";
			$last_fm=new Class_WebService_Lastfm();
			$morceaux=$last_fm->getMorceaux($notice["T"],$notice["A"]);
			$morceaux["id_notice"]=$notice["id_notice"];
		}
		$morceaux["auteur"]=$notice["A"];
		$html=$this->notice_html->getMorceaux($morceaux,$source);
		$this->_sendResponse($html);
	}


	public function videomorceauAction() {
		if($this->service_afi > "")
		{
			$args=array("titre" => $_REQUEST["titre"], "auteur" => $_REQUEST["auteur"]);
			$data=Class_WebService_AllServices::runServiceAfi(9,$args);
			$source=$data["source"];
			$video=$data["video"];
			if(!$video) $html=$this->notice_html->getNonTrouve();
			else $html=$video;
		}
		else 
			$html= $html=$this->notice_html->getNonTrouve($this->view->_("Service non disponible"),true);
		$this->_sendResponse($html);
	}


	public function playerlastfmAction() {
		$lastfm=new Class_WebService_Lastfm();
		$html=$lastfm->getPlayer($_REQUEST["url"]);
		$this->_sendResponse($html);
	}


	public function videosAction() {
		if ($num_video = $this->_getParam("num_video", 0))	{
			$num_video = $num_video-1;
			$html.=$_SESSION["video_interview"][$num_video]["player"];
		}
		else
		{
			unset($_SESSION["video_interview"]);
			$notice=$this->notice->getNotice($this->id_notice,"TA");
			if(!trim($notice["A"])) 
				$html=$this->notice_html->getNonTrouve($this->view->_("Cette notice n'a pas d'auteur."),true);
			else if($this->service_afi > "")
			{
				$args=array("titre"=>$notice["T"],"auteur" => $notice["A"]);
				$data=Class_WebService_AllServices::runServiceAfiInterviews($args);
				$source=$data["source"];
				$videos=$data["videos"];
				$_SESSION["video_interview"]=$videos;
				$html=$this->notice_html->getInterviews($source,$videos);
			}
			else $html= $html=$this->notice_html->getNonTrouve($this->view->_("Service non disponible"),true);
		}
		$this->_sendResponse($html);
	}


	public function avisAction() {
		session_write_close();
		// Lire la notice
		$notice = Class_Notice::getLoader()->find($this->id_notice);

		$all_avis = $notice->getAllAvisPerSource($_REQUEST["page"]);
		$html=$this->notice_html->getAvis($notice,$all_avis);
		$this->_sendResponse($html);
	}


	public function babelthequeAction() {
		if (!$script = Class_AdminVar::get('BABELTHEQUE_JS')) {
			$this->_sendResponse('');
			return;
		}

		$html = sprintf('<script type="text/javascript" src="%s"></script>', $script);
		$html .= sprintf('<input type="hidden" id="BW_id_isbn" value="%s"\>', 
										 $this->notice->getIsbn());
		
		$blocs = array('notes', 'critiques', 'critiques_pro', 'citations', 'videos', 'etiquettes', 'suggestions');
		foreach($blocs as $bloc)
			$html .= sprintf('<div id="BW_%s"></div>', $bloc);

		$this->_sendResponse($html);
	}


	public function frbrAction() {		
	  $id = str_replace('N', '', $this->_getParam('id_notice'));

	  if (!$model = Class_Notice::find((int)$id)) {
	    $this->_sendResponse(ZendAfi_View_Helper_Frbr::NO_RESULT_MESSAGE);
	    return;
	  }
 
	  $this->_sendResponse($this->view->frbr($model));
	}


	protected function _sendResponse($html) {
		$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
		$this->getResponse()->setBody($html);
	}
} 
