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
// OPAC3 - Class_Module_Sito -> Sitothèque
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Accueil_Sito extends ZendAfi_View_Helper_Accueil_Base {
	protected function _renderHeadScriptsOn($script_loader) {
		if ($this->isGroupByCategorie())
			Class_ScriptLoader::getInstance()
				->addJQueryReady('$("ul.sitotheque>li>h2>a").click(
																	 function(event){
																				 event.preventDefault();
																				 $(this).closest("li").find("ul").slideToggle();
																		});');
	}

	//---------------------------------------------------------------------
	// CONSTRUCTION du Html
	//--------------------------------------------------------------------- 
	public function getHtml()	{
		extract($this->preferences);
		$contenu = '';
		// Sélection de catégories ou d'articles
		if ($this->isTypeAffichageSelection())	{
			$sites = Class_Sitotheque::getLoader()->getSitesFromIdsAndCategories(
																										 explode('-', $id_items),
																										 explode('-', $id_categorie));

			shuffle($sites);
			$contenu.=$this->renderSitesSlice($sites,$nb_aff);			
			$titre= sprintf('<a href="%s" title="%s">%s</a>',
											htmlspecialchars(BASE_URL.'/opac/sito/viewselection/id_module/'.$this->id_module),
											$this->translate()->_('Sélection de sites'),
											$titre);
		}

		// Les plus recents
		if ($this->isTypeAffichagePlusRecents() && $nb_aff > 0) {
			$last_sito = Class_Sitotheque::getLoader()->findAllBy(array('limit' => 50));
			shuffle($last_sito);

			if(!$titre) 
				$titre = $this->translate()->_("Derniers sites ajoutés");

			$titre= sprintf('<a href="%s" title="%s">%s</a>',
											htmlspecialchars(BASE_URL.'/opac/sito/viewrecent/nb/50'),
											$this->translate()->_('Liste des derniers sites ajoutés'),
											$titre);
			$contenu.=$this->renderSitesSlice($last_sito,$nb_aff);	
		}
		$this->titre=$titre;
		$this->contenu = $contenu;
		return $this->getHtmlArray();
	}


	public function isTypeAffichageSelection() {
		return $this->preferences['type_aff'] == 1;
	}


	public function isTypeAffichagePlusRecents() {
		return $this->preferences['type_aff'] == 2;
	}


	public function isGroupByCategorie() {
		if  (!array_isset('group_by_categorie', $this->preferences))
			return false;

		return $this->preferences['group_by_categorie'] == true;
	}


	public function groupSitesByCategorie($sites) {
		$categories = array();
		foreach ($sites as $site) {
			$categorie = $site->getCategorieLibelle();
			if (!array_isset($categorie, $categories))
				$categories[$categorie] = array();
			$categories[$categorie] []= $site;
		}
		return $categories;
	}


	//---------------------------------------------------------------------
	// Html pour 1 ou plusieurs sites
	//---------------------------------------------------------------------
	protected function renderSitesSlice($sites,$nb_aff)	{
		if(!$sites) return "";

		$sites = array_slice($sites, 0, $nb_aff);

		if (!$this->isGroupByCategorie()) 
			return $this->renderSites($sites, 
																'<div style="clear:both;width:100%;background:transparent url('.URL_IMG.'box/menu/separ.gif) repeat-x scroll center bottom;margin-bottom:5px">&nbsp;</div>');

		$categories = $this->groupSitesByCategorie($sites);
		$htmls = array();
		foreach ($categories as $libelle_categorie => $sites)
			$htmls []= sprintf('<li><h2><a href="#">%s</a></h2><ul><li>%s</li></ul></li>', 
												 $libelle_categorie,
												 $this->renderSites($sites));

		return sprintf('<ul class="sitotheque">%s<ul>',
									 implode('', $htmls));
	}


	protected function renderSites($sites, $separator = '') {
		$htmls = array();
		foreach($sites as $site)
			$htmls []= $this->renderSite($site);

		return implode($separator, $htmls);
	}


	protected function renderSite($site) {
		if($this->division == 1)	{
			$site->setTitre($this->fixLibelleBoiteGauche($site->getTitre()));
			$site->setDescription($this->extractHeader($site->getDescription()));
		}

		$html = sprintf('<h2><a href="%s" title="%s">',
										$site->getUrl(),
										$this->translate()->_('Aller sur le site'));

		if ($img_url = $this->getThumbnail($site->getUrl()))
			$html.= sprintf('<img src="%s" alt="%s" />',$img_url,	$this->translate()->_('vignette du site %s', $site->getTitre()));

		$html .= '&raquo;&nbsp;'.$site->getTitre().'</a></h2>';
		$html .= $site->getDescription();

		return '<div class="sitotheque">'.$html.'</div>';
	}


	public function getThumbnail($url) {
		if (!isset($this->thumbnails_helper))
			$this->thumbnails_helper = new ZendAfi_View_Helper_WebThumbnail();
		return $this->thumbnails_helper->webThumbnail($url);
	}
}