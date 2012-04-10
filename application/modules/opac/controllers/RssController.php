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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - FLUX RSS
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
class RssController extends Zend_Controller_Action
{

	private $_session;
	private $_idProfil;

	//---------------------------------------------------------------------------
	// Init controller
	//---------------------------------------------------------------------------
	function init()
	{
		$this->_session = Zend_Registry::get('session');
		$this->_idProfil = $this->_session->idProfil;

		$this->_helper->getHelper('AjaxContext')
										->addActionContext('view-raw-rss', 'html')
										->initContext()
										;
	}

	//---------------------------------------------------------------------------
	// Redirection
	//---------------------------------------------------------------------------
	function indexAction()
	{
		$this->_redirect('opac/');
	}


	private function _renderRSS($rss)
	{
		if(!count($rss) )
			{
				$message['type'] = "erreur";
				$message['titre'] = $this->view->_("Impossible de lire le flux rss");
				$message['message'] = "";
				$message['cause'] = $this->view->_("Le ou les flux demandés ne sont plus valides");
				$message['remede'] = $this->view->_("Merci de le signaler à un responsable de la bibliothèque.");

				$this->view->message = $message;
				$this->view->title = $this->view->_("Erreur");
				$this->render('error');
			}
		else
			$this->view->rss = $rss;

		$this->renderScript('rss/main.phtml');
	}

	//---------------------------------------------------------------------------
	// Affichage Catégorie
	//---------------------------------------------------------------------------
	function mainAction()
	{
		$id_cat = (int)$this->_request->getParam('id_cat', 0);
		$id_flux = (int)$this->_request->getParam('id_flux', 0);
		if ($id_flux == 0) $id_flux = (int)$this->_request->getParam('id', 0);
		$liste_flux=$this->_request->getParam('liste_flux');

		$rssClass = new Class_Rss();
		if($id_flux) $ids=array($id_flux);
		elseif($id_cat) $ids = $rssClass->fetchRowRssCategorieById($id_cat);
		elseif($liste_flux) $ids=explode(";",$liste_flux);

		for($i=0; $i < count($ids); $i++)
			{
				$ret=$rssClass->getRssById($ids[$i]);
				if(!$ret) continue;
				$rss[]=$ret[0];
			}
		$this->_renderRSS($rss);
	}


	/*
	 * Affiche la sélection des RSS d'un module / boîte donné
	 */
	function viewselectionAction() {
		$id_module = $this->_request->getParam('id');
		$preferences = Class_Profil::getCurrentProfil()->getModuleAccueilPreferences($id_module);


		$rssClass = new Class_Rss();
		$feeds = $rssClass->getFluxFromIdsAndCategories(explode('-', $preferences['id_items']),
																										explode('-', $preferences['id_categorie']));
		$this->_renderRSS($feeds);
	}


	//---------------------------------------------------------------------------
	// Affichage des dernier RSS
	//---------------------------------------------------------------------------
	function viewrecentAction()
	{
		$this->view->title = $this->view->_("Derniers Fils RSS");
		$nb_aff = (int)$this->_request->getParam('nb');
		$rssClass = new Class_Rss();

		if(!$nb_aff) $nb_aff=200;
		$liste_rss = $rssClass->getLastRss($nb_aff);
		$this->_renderRSS($liste_rss);
	}

	public function viewRawRssAction () {
		$rssId = (int)$this->_request->getParam('id_rss', 0);
		$profil = Class_Profil::getLoader()->find((int)$this->_request->getParam('id_profil', 1));
		$preferences = $profil->getModuleAccueilPreferences((int)$this->_request->getParam('id_module'));

		try {
			$rss = Class_Rss::getLoader()->find($rssId);
			$this->view->feed_items = array_slice($rss->getFeedItems(), 0, $preferences['nb_aff']);
		} catch (Exception $e) {
			$this->view->invalidRss = true;
			$this->view->feed_items = array();
		}

		$this->_helper->getHelper('viewRenderer')->setLayoutScript('empty.phtml');
	}


	//---------------------------------------------------------------------------
	// Affichage d'1 flux en AJAX
	//---------------------------------------------------------------------------
	function afficherrssAction()
	{
		$rssId = (int)$this->_request->getParam('id_rss', 0);
		$rssClass = new Class_Rss();

		$ret = $rssClass->getRssById($rssId);
		$rss=$ret[0];

		$html[]='<div class="rss_popup">';
		$html[]='<div class="header">';
		$html[]=$rss["TITRE"];
		$html[]='<img src="'.URL_ADMIN_IMG.'fermer.gif" style="cursor:pointer" onclick="closeRSSDiv(\''.$rssId.'\')"/>';
		$html[]='</div>';

		$html[]='<div class="content">';

		if(!$rss)
			{
				$html[] = $this->view->_("L'adresse du flux RSS n'est plus valide.");
			}
		else
			{
				$httpClient = Zend_Registry::get('httpClient');
				try
					{
						Zend_Feed::setHttpClient($httpClient);
						$link = $rss["URL"];
						$rssFeed = ZendAfi_Feed::import($link);

						$i = 0;
						$locale = Zend_Registry::get('locale');
						foreach ($rssFeed as $item)
							{
								$i++;
								$titleRss = $item->title();
								$urlRss = $item->link();
								$descriptionRss = $item->description();

								if ($item->pubDate())
									{
										$date = $item->pubDate();
										if ($locale == 'en_US') $dateFormat = 'MM-dd-yyyy';
										else $dateFormat = 'dd-MM-yyyy';
										try
											{
												$zendDate =  new Zend_Date($date, Zend_Date::RFC_2822);
												$dateRss = $zendDate->toString($dateFormat);
											}
										catch (Exception $e)
											{
												$dateRss = '';
											}
									}
								else $dateRss = '';
								$html[]='<table width="100%" class="popup" cellspacing="0" align="center">';
								$html[]='<tr>';
								$html[]='<td class="popup_titre" width="70%"><a href="'.$urlRss.'" target="_blank" class="popup" onclick="' . $onclick . '">'.$titleRss.'</a></td>';
								$html[]='<td  class="popup_titre" width="1%"></td>';
								$html[]='<td  class="popup_titre" width="30%"><a href="#" onclick="return(false);">'.$dateRss.'</a></td>';
								$html[]='</tr>';
								$html[]='<tr>';
								$html[]='<td  class="popup_ligne" colspan="3">'. $descriptionRss .'</td>';
								$html[]='</tr>';
								$html[]='</table>';
							}

					}
				catch(Exception $e)
					{
						$html[] = $this->view->_("Il y a un problème avec l'adresse du flux RSS");
					}

			}
		$html[]='</div>';

		$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
		$this->getResponse()->setBody(implode("",$html));

		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
	}

	//---------------------------------------------------------------------------
	// Flux RSS de la page des modérations
	//---------------------------------------------------------------------------
	function moderationsAction() {
		$this->getHelper('ViewRenderer')->setNoRender();
		$class_rss = new Class_Rss();
		$data_rss = array(
							'titre' => $this->view->_('Modérations'),
							'description' => $this->view->_('Données en attente de modération'),
							'lien' => 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
							'items' => array());

		$moderer = new Class_Moderer();
		$modstats = $moderer->getModerationStats();
		//on n'affiche pas les catégories où il n'y a aucune donnée à modérer
		foreach($modstats as $stat) {
			if ($stat['count']==0) continue;
			$data_rss["items"][]= array(
								'titre' => $stat['label'].': '.$stat['count'],
								'lien' => 'http://'.$_SERVER['SERVER_NAME'].$stat['url']);
		}

		if (count($data_rss['items']) == 0)
			$data_rss["items"][]= array('titre' => $this->view->_('Aucune donnée à modérer'));

		$flux = $class_rss->createFluxRss($data_rss);
		echo $flux;
	}


	//---------------------------------------------------------------------------
	// Flux RSS des dernieres critiques
	//---------------------------------------------------------------------------
	function critiquesAction() {
		$id_profil = $this->_request->getParam('id_profil');

		$profil = Class_Profil::getLoader()->find($id_profil);

		$id_module = $this->_request->getParam('id_module');

		$preferences = $profil->getModuleAccueilPreferences($id_module);

		$liste_avis = Class_AvisNotice::getLoader()->getAvisFromPreferences($preferences);

		//on ne prends que les 20 derniers avis
		$liste_avis = array_slice($liste_avis, 0, 20);

		$data_rss["titre"] = $preferences['titre'];
		$data_rss["description"] = $this->view->_("Critiques de la sélection: %s", $preferences['titre']);
		$data_rss["lien"] = urlencode(urlencode($profil->urlForModule('blog', 'viewcritiques', $id_module)));
		$data_rss["items"] = array();
		
		$avis_helper = new ZendAfi_View_Helper_Avis();
		foreach($liste_avis as $avis) {
			$desc = $avis_helper->avis($avis, 0, $vignette_link_to_avis = true);

			$entry = array("titre" => $avis->getEntete(),
										 "lien" => 'http://'.$_SERVER['SERVER_NAME'].BASE_URL.'/blog/viewavis/id/'.$avis->getFirstNotice()->getId().'-'.$avis->getIdUser(),
										 "desc" => '<![CDATA['.$this->filtreNews($desc).']]>');
			$data_rss["items"][] = $entry;
		}

		$class_rss = new Class_Rss();
		$flux = $class_rss->createFluxRss($data_rss);
		echo $flux;

		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
	}


	// Critiques d'un utitisateur
	function userAction() {
		$id = $this->_request->getParam('id');
		$user = Class_Users::getLoader()->find($id);
		$avis_helper = new ZendAfi_View_Helper_Avis();

		$entries = array();
		foreach($user->getLastAvis() as $avis)  {
			$desc = $avis_helper->avis($avis, 0, $vignette_link_to_avis = true);

			$entries [] = array('title' => $avis->getEntete(),
													'link' => sprintf('http://%s/opac/blog/viewavis/id/%s',
																						$_SERVER['SERVER_NAME'].BASE_URL,
																						$avis->getId()),
													'description' => $this->filtreNews($desc),
													'lastUpdate' => strtotime($avis->getDateAvis()));
		}
			
		$data_rss = array( 'title' => sprintf('Avis de %s', $user->getNomAff()),
											 'link' => sprintf('http://%s/blog/viewauteur/id/%d', 
																				 $_SERVER['SERVER_NAME'].BASE_URL,
																				 $user->getId()),
											 'charset' => 'utf-8',
											 'entries' => $entries);
		$feed = Zend_Feed::importArray($data_rss, 'rss');

		$this->getHelper('ViewRenderer')->setNoRender();
		$this->_response->setHeader('Content-Type', 'application/rss+xml;charset=utf-8') ;
		$this->_response->setBody($feed->saveXML());
	}



	function filtreNews($contenu_news)
	{
		$contenu = preg_replace('/ src="/',' src="http://'.$_SERVER['SERVER_NAME'].'/',$contenu_news);
		$contenu = preg_replace('/ src=\'\//',' src=\'http://'.$_SERVER['SERVER_NAME'].'/',$contenu_news);
		$contenu = preg_replace('^{FIN}^','',$contenu);
		return($contenu);
	}



	/*
	 * Flux RSS pour la boîte kiosque
	 */
	function kiosqueAction() {
		$id_profil = (int)$this->_request->getParam('id_profil');
		$id_module = (int)$this->_request->getParam('id_module');
		$profil = Class_Profil::getLoader()->find($id_profil);
		$preferences = $profil->getModuleAccueilPreferences($id_module);

		$catalogue=new Class_Catalogue();
		$preferences["aleatoire"] = 0; // les dernières seulement
		$notices=$catalogue->getNotices($preferences,"url");

		$entries = array();

		foreach($notices as $notice) {
			$entries []= array(
												 'title'       => $notice["titre"].', '.$notice["auteur"],
												 'link'        => 'http://' . $_SERVER['SERVER_NAME'].BASE_URL.'/recherche/viewnotice/id/'.$notice['id_notice'],
												 'description' =>  $this->_noticeRssDescription($notice),
												 'lastUpdate'	 => strtotime($notice['date_creation']));
		}
		$rss_array = array(
											'title' 	=> $preferences['titre'],
											'link'  	=> 'http://' . $_SERVER['SERVER_NAME'].BASE_URL,
											'charset'	  => 'utf-8',
											'description' => $preferences['titre'],
											'lastUpdate'  => time(),
											'entries' => $entries);

		$feed = Zend_Feed::importArray($rss_array, 'rss') ;

		$this->getHelper('ViewRenderer')->setNoRender();
		$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8') ;
		$this->getResponse()->setBody($feed->saveXML()) ;
	}


	protected function _noticeRssDescription($notice) {
		extract($notice);
		$desc =<<<DESCRIPTION
			<div>$titre ($auteur, $editeur, $annee)</div>
      <img src='$vignette'>
DESCRIPTION;
		return $desc;
	}
}