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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Class_Module_News -> CMS
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Accueil_News extends ZendAfi_View_Helper_Accueil_Base {
	//---------------------------------------------------------------------
	// Construction du Html
	//---------------------------------------------------------------------
	protected function _renderHeadScriptsOn($script_loader) {
		if ($this->isDisplayDiaporama())
			$this->renderSlideShowScriptsOn($script_loader,
																			sprintf('.news-%d',	$this->id_module),
																			array('width' => $this->preferences['op_largeur_img']));
	}

	public function getHtml()	{
		$this->read_speaker_helper = new ZendAfi_View_Helper_ReadSpeaker();

		// Sélection de catégories ou d'articles
		if (Class_AdminVar::isTranslationEnabled())
			$this->preferences['langue'] = $this->getLocale();

		if (Class_AdminVar::isWorkflowEnabled())
			$this->preferences['status'] = Class_Article::STATUS_VALIDATED;

		$articles = Class_Article::getLoader()->getArticlesByPreferences($this->preferences);
		$this->contenu = sprintf('<div class="news-%d news">%s</div>',
														 $this->id_module,
														 $this->getArticles(Class_Article::filterByLocaleAndWorkflow($articles)));

		$this->titre = $this->getHtmlTitre();

		if ($this->preferences['rss_avis'])
			$this->rss_interne = $this->_getRSSurl('cms', 'rss');

		return $this->getHtmlArray();
	}


	protected function getHtmlTitre(){
		if (strlen(trim($this->preferences['titre']))==0)
			return '';

	  $titre = sprintf('<a href="%s/cms/articleviewselection/id_module/%d">%s</a>',
										 BASE_URL,
										 $this->id_module,
										 $this->preferences['titre']);

		return $titre;
	}

	//---------------------------------------------------------------------
	// Html pour 1 ou plusieurs articles
	//---------------------------------------------------------------------
	protected function getArticles($articles)	{
		if (!$articles) return '';

		$locale = $this->getLocale();

		$htmls = array();
		foreach($articles as $article)
			$htmls []= $this->buildHTMLForArticle($article);


		$html_separator = '';
		if ($this->isDisplayListe()) {
			$sep_class = $this->preferences["display_titles_only"] ? "article_only_title_separator" : "article_full_separator";
			$html_separator = sprintf('<div class="%s">&nbsp;</div>', $sep_class);
		}

		return implode($html_separator, $htmls);
	}


	public function buildHTMLForArticle($article) {
		$html = sprintf('<div class="auto_resize %s">',
										$this->preferences["display_titles_only"] ? "article_title_only" : "article_full");
		$html .= $this->view->tagEditArticle($article);
		$footer = '';

		$title = $article->getTitre();
		if ($article->hasSummary()) {
			$content = $article->getSummary();
			$footer = '<br />'. $this->view->tagAnchor($this->view->url($article->getUrl()),
																								 $this->translate()->_("Lire l'article complet"));
		} else {
			$content = $article->getFullContent();
		}

		if ($this->division==1)
			$title = $this->fixLibelleBoiteGauche($title);

		$html .= '<h2>';
		if (!$article->getCacherTitre() or $this->preferences["display_titles_only"])
			$html .= $this->view->tagAnchor($this->view->url($article->getUrl()), $title);

		$html .= $this->view->readSpeaker('cms',
																			'articleread',
																			array("id" => $article->getId()));
		$html .= '</h2>';
		$html .= $this->view->tagArticleEvent($article);

		if (!$this->preferences["display_titles_only"]) {
			$html.=$content;
			$html.=$footer;
		}

		$html.='</div>';
		return $html;
	}

}

?>