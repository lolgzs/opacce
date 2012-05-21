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
class CmsController extends Zend_Controller_Action {
	public function init() {
		parent::init();

		$this->_helper->getHelper('AjaxContext')
										->addActionContext('reseau', 'html')
										->initContext();
	}


	public function indexAction() {
		$this->_redirect('opac/cms/articleviewrecent/nb/10');
	}

	/**
	 * Paramètres attendus:
	 *		'd': date sélectionnée
	 *		'b': identifiant de la bibliothèque
	 *		'cat': identifiant de la catégorie
	 */
	public function articleviewbydateAction() {
		$id_profil = (int)$this->_getParam('id_profil');
		$id_module = (int)$this->_getParam('id_module');
		if (!$profil = Class_Profil::getLoader()->find($id_profil))
			$profil = Class_Profil::getCurrentProfil();
		$preferences	= $profil->getModuleAccueilPreferences($id_module);

		$preferences['event_date']		= $this->_getParam('d');
		$preferences['id_bib']				= $this->_getParam('b');
		$preferences['display_order'] = 'EventDebut';
		$preferences['events_only']		= true;
		$preferences['published'] = false;

		if ($id_cat = (int)$this->_getParam('select_id_categorie'))
			$preferences['id_categorie'] = $id_cat;

		$articles = Class_Article::getLoader()->getArticlesByPreferences($preferences);

		$articles = Class_Article::getLoader()->filterByLocaleAndWorkflow($articles);

		$this->view->articles	= Class_Article::getLoader()->groupByBib($articles);

	}


	public function calendarrssAction() {
		$id_profil = (int)$this->_getParam('id_profil');
		$id_module = (int)$this->_getParam('id_module');

		$profil				= Class_Profil::getLoader()->find($id_profil);
		$preferences	= $profil->getModuleAccueilPreferences($id_module);

		if (
			array_key_exists('id_categorie', $preferences)
			&& ('' != $preferences['id_categorie'])
		) {
			if ('Random' == $preferences['display_order']) {
				$preferences['display_order'] = 'DateCreation';
				$preferences['nb_aff'] = $preferences['nb_analyse'];
			}
		}	else {
			$preferences['event_date'] = strftime('%Y-%m');
		}

		$preferences['events_only'] = true;
		$preferences['published'] = false;

		$articles = Class_Article::getLoader()->getArticlesByPreferences($preferences);
		$articles = Class_Article::getLoader()->filterByLocaleAndWorkflow($articles);

		$data_rss = array(
			'title' 	=> $preferences['titre'],
			'link'  	=> $profil->urlForModule('cms', 'articleviewbydate', $id_module),
			'charset'	  => 'utf-8',
			'description' => 'Agenda: ' . $preferences['titre'],
			'lastUpdate'  => time()
		);

		$this->_renderRSS($articles, $data_rss);

	}

	public function articleviewAction() {
		if (
			null === (
				$article = Class_Article::getLoader()->find((int)$this->_getParam('id'))
			)
		) {
			$this->_redirect('opac/index', array('exit' => true));
		}

		$langue = Zend_Registry::get('translate')->getLocale();
		$article = $article->getTraductionLangue($langue);

		$this->view->article = $article;
		$this->view->title = $article->getTitre();
	}


	public function articlereadAction(){
		$this
			->getHelper('ViewRenderer')
			->setLayoutScript('readspeaker.phtml');

		$article = Class_Article::getLoader()
			->find((int)$this->_getParam('id'));

		$langue = Zend_Registry::get('translate')->getLocale();
		$article = $article->getTraductionLangue($langue);

		$this->view->article = $article;
		$this->renderScript('cms/article_partial.phtml');

	}


	public function articleviewrecentAction() {
		$limit = (int)$this->_request->getParam('nb');

		if (0 == $limit) {
			$limit = 10;
		}

		$preferences = array(
			'nb_aff'				=> $limit,
			'display_order' => 'DateCreationDesc',
		);

		$articles = Class_Article::getLoader()->getArticlesByPreferences($preferences);
		$articles = Class_Article::getLoader()->filterByLocaleAndWorkflow($articles);

		$this->view->articles = $articles;

		$this->view->title = $this->view->_('Derniers Articles');
		$this->renderScript('cms/articlesview.phtml');

	}

	public function reseauAction() {
		$this->view->article = Class_Article::getLoader()
														->find((int)$this->_getParam('id_article'));
		$this->_helper->getHelper('viewRenderer')->setLayoutScript('empty.phtml');
	}

	public function rssAction(){
		$id_profil = (int)$this->_getParam('id_profil');
		$id_module = (int)$this->_getParam('id_module');

		$articles = array();

		$data_rss = array(
				'title' 	=> 'Flux indisponible',
				'link'  	=> $this->_request->getScheme() . '://'
												. $this->_request->getServer('HTTP_HOST'),
				'charset'	  => 'utf-8',
				'description' => '',
				'lastUpdate'  => time()
		);

		if (null != ($profil = Class_Profil::getLoader()->find($id_profil))) {
			$preferences = $profil->getModuleAccueilPreferences($id_module);

			$data_rss = array_merge(
					$data_rss,
					array(
						'title' 	=> $preferences['titre'],
						'link'  	=> $profil->urlForModule('cms', 'viewselection', $id_module),
						'description' => 'Articles: '.$preferences['titre']
					)
			);

			$articles = Class_Article::getLoader()
										->getArticlesByPreferences($preferences);

			$articles = Class_Article::getLoader()->filterByLocaleAndWorkflow($articles);

		}

		$this->_renderRSS($articles, $data_rss);

	}

	/**
	 * @see ZendAfi_View_Helper_Accueil_MenuVertical
	 */
	public function viewsummaryAction() {
		$this->_viewArticlesByPreferences($this->_getAllParams());
	}

	/**
	 * @see ZendAfi_View_Helper_Accueil_News
	 */
	public function articleviewselectionAction() {
		$preferences = $this->_modulesPreferences(
																				(int)$this->_getParam('id_module'),
																				(int)$this->_getParam('id_profil'));
		$preferences['nb_aff'] = 30;
		if (!array_isset('display_order', $preferences) || ('Random' == $preferences['display_order']))
			$preferences['display_order'] = 'DateCreationDesc';
		$this->_viewArticlesByPreferences($preferences);
	}


	public function categorieviewAction() {
		$articles = Class_Article::getLoader()->getArticlesByPreferences(array(
				'id_categorie' => (int)$this->_request->getParam('id')
		));

		$articles = Class_Article::getLoader()->filterByLocaleAndWorkflow($articles);

		$this->view->articles = $articles;
		$this->renderScript('cms/articlesview.phtml');
	}


	public function calendarAction() {
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();

		$date = $this->_getParam("date");
		$id_module = $this->_getParam("id_module");
		$preferences = $this->_modulesPreferences($id_module);

		if (!preg_match('/[0-9]{4}-[0-9]{2}/', $date)) {
			$date = date('Y-m-d');
		}

		// param pour l'affichage du calendar
		$param["DATE"]=$date;
		$param["URL"]="";
		$param["ID_BIB"]=Class_Profil::getCurrentProfil()->getIdSite();
		$param["AFFICH_MOIS"]=1;
		$param["NEWS"]["AFFICH_NEWS"]=1;
		$param["NB_NEWS"]= (int)$preferences["nb_events"];
		$param["ALEATOIRE"]=1;
		$param["ID_MODULE"] = $id_module;
		$param["ID_CAT"] = $preferences["id_categorie"];
		$param["SELECT_ID_CAT"] = $preferences["display_cat_select"]
																? $this->_getParam("select_id_categorie")
																: "all";
		$param["DISPLAY_CAT_SELECT"] = $preferences["display_cat_select"];
		$param["DISPLAY_NEXT_EVENT"] = array_key_exists('display_next_event', $preferences) ? $preferences["display_next_event"] : '1';
		$param["EVENT_INFO"] = $preferences["display_event_info"];
		$class_calendar = new Class_Calendar($param);
		$this->getResponse()->setBody($class_calendar->rendHTML());
	}

	/**
	 * @param array $articles
	 * @param array $rss_array
	 */
	private function _renderRSS($articles, $rss_array) {
		$entries = array();
		foreach ($articles as $article) {
			$entries[] = array(
			 'title'       => $article->getTitre(),
			 'link'        => $this->_request->getScheme() . '://'
													. $this->_request->getServer('HTTP_HOST')
			                    . $this->view->url($article->getUrl()),
			 'description' => $article->getFullContent(),
			 'lastUpdate'	 => strtotime($article->getDateMaj())
			);
		}

		$rss_array['entries'] = $entries;

		$feed = Zend_Feed::importArray($rss_array, 'rss');

		$this->getHelper('ViewRenderer')->setNoRender();
		$this->_response->setHeader('Content-Type', 'application/rss+xml;charset=utf-8') ;
		$this->_response->setBody($feed->saveXML());
	}

	/**
	 * @param int $id_module
	 * @param int $id_profil
	 * @return array
	 */
	private function _modulesPreferences($id_module, $id_profil = null) {
		$profil = ($id_profil)
								? Class_Profil::getLoader()->find($id_profil)
								: Class_Profil::getCurrentProfil();

		return $profil->getModuleAccueilPreferences($id_module);
	}

	/**
	 * @param array $preferences
	 */
	private function _viewArticlesByPreferences($preferences) {
		$articles = Class_Article::getLoader()
															->getArticlesByPreferences($preferences);

		$articles = Class_Article::getLoader()->filterByLocaleAndWorkflow($articles);

		$this->view->articles = $articles;

		if (array_key_exists('titre', $preferences)) {
			$this->view->title = $preferences['titre'];
		}

		if (array_key_exists('summary_content', $preferences)) {
			$this->view->show_content = $preferences['summary_content'];
		} else {
			$this->view->show_content = 'FullContent';
		}

		$this->renderScript('cms/articlesview.phtml');

	}
}