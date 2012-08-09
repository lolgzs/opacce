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
class ZendAfi_View_Helper_Accueil_MenuVertical extends ZendAfi_View_Helper_Accueil_Base {
	/**
	 * @var Class_Systeme_ModulesMenu
	 */
	private $_cls_menu;

	/**
	 * CSS des li
	 * @var string
	 */
	private $_li_style;


	/**
	 * Dossier pour les pictos
	 * @var Class_Systeme_ModulesMenu
	 */
	private $path_ico;						


	/**
	 * @return array
	 */
	public function getHtml()	{
		extract($this->preferences);
		$profil = Class_profil::getCurrentProfil();
		$this->_cls_menu = new Class_Systeme_ModulesMenu();
		$this->_li_style = sprintf('style="margin-left:20px;%s"',
															 $menu_deplie ? "" : "display:none");

		// Lire la config du menu
		$id_profil = $profil->getId();
		$menus = $profil->getCfgMenusAsArray();
		if(!$menus)
			return $this->retourErreur($this->translate()->_('Aucun menu n\'est paramétré pour ce profil.'));

		foreach ($menus as $id_menu => $cfg)
			if ($id_menu == $menu) {
				$config = $cfg;
				break;
			}

		if (!count($config['menus']))
			return $this->retourErreur($this->translate()->_('Ce menu ne contient aucune entrée.'));

		$this->path_ico = BASE_URL . $profil->getPathTheme() . 'images/menus/';

		if ('1' == $afficher_titre)
			$titre = array_last(explode(':: ', $config['libelle'])); //le :: c'est pour les menus qui viennent d'un autre profil (Châtenay)

		$contenu = '<div class="menuGauche"><ul class="menuGauche">';
		foreach ($config['menus'] as $entree) {
			$contenu .= $this->_getLigne($entree);
			if (array_isset('sous_menus', $entree)) {
				$contenu .= sprintf('<li class="menuGauche" %s><ul>', $this->_li_style);
				foreach ($entree["sous_menus"] as $sous_menu)
					$contenu .= $this->_getLigne($sous_menu);
				$contenu .= '</ul></li>';
			}
		}
		$contenu .= '</ul></div>';

		$this->titre = $titre;
		$this->contenu = $contenu;
		return $this->getHtmlArray();
	}


	/**
	 * @param array $entree
	 * @return string
	 */
	private function _getLigne($entree) {
		$builder = $this->_getBuilderMethod($entree);
		return $this->$builder($entree);
	}


	/**
	 * @param array $menuitem
	 * @return string
	 */
	private function _getBuilderMethod($menuitem) {
		$builder_method = '_menu' . $menuitem['type_menu'] . 'Builder';
		if (method_exists($this, $builder_method))
			return $builder_method;
		return '_defaultBuilder';
	}


	/**
	 * @param string $controller
	 * @param string $action
	 * @param array $menu
	 * @param array $items
	 * @return string
	 */
	private function _renderItems($controller, $action, $menu, $items) {
		if (1 == count($items)) {
			$item = reset($items);
			$id = $item->getId();
			return $this->_renderListItem($menu, $this->view->url(array(
																																	'controller' => $controller,
																																	'action' => $action,
																																	'id' => $id)), null, true);
		}

		$content = $this->_renderListItem($menu);
		$content .= sprintf('<li class="menuGauche" %s><ul>', $this->_li_style);

		foreach($items as $item) {
			$id = $item->getId();
			$titre = $item->getTitre();
			$url = $this->view->url(array('controller' => $controller,
																		'action' => $action,
																		'id' => $id));
			$content .= $this->_renderLIAnchor($url, $titre);
		}

		$content .= "</ul></li>";
		return $content;
	}


	public function _renderLIAnchor($url, $content) {
		return sprintf('<li class="menuGauche"><a href="%s">%s</a></li>',
									 htmlspecialchars($url),
									 htmlspecialchars($content));
	}


	/**
	 * @param array $menuitem
	 * @return string
	 */
	private function _menuRSSBuilder($menuitem) {
		$id_items			= $menuitem['preferences']['id_items'];
		$id_categorie	= $menuitem['preferences']['id_categorie'];
		$nb						= $menuitem['preferences']['nb'];

		$feeds = Class_Rss::getLoader()->getFluxFromIdsAndCategories(
																																 explode('-', $id_items),
																																 explode('-', $id_categorie));

		shuffle($feeds);
		$feeds = array_slice($feeds, 0, $nb);

		return $this->_renderItems('rss', 'main', $menuitem, $feeds);
	}


	/**
	 * @param array $menuitem
	 * @return string
	 */
	private function _menuSITOBuilder($menuitem) {
		$id_items			= $menuitem['preferences']['id_items'];
		$id_categorie	= $menuitem['preferences']["id_categorie"];
		$nb						= $menuitem['preferences']["nb"];

		$sitos = Class_Sitotheque::getLoader()->getSitesFromIdsAndCategories(
																																				 explode('-', $id_items),
																																				 explode('-', $id_categorie));
		shuffle($sitos);
		$sitos = array_slice($sitos, 0, $nb);
		return $this->_renderItems('sito', 'sitoview', $menuitem, $sitos);
	}


	/**
	 * @param array $menuitem
	 * @return string
	 */
	private function _menuNEWSBuilder($menuitem) {
		if (!array_key_exists('preferences', $menuitem))
			return $this->_renderListItem($menuitem);

		$preferences = $menuitem['preferences'];

		if ($preferences['display_mode'] == 'Submenu') {
			unset($preferences["display_mode"]);
			$articles = Class_Article::getLoader()->getArticlesByPreferences($preferences);
			$articles = Class_Article::getLoader()->filterByLocaleAndWorkflow($articles);
			return $this->_renderItems('cms', 'articleview', $menuitem, $articles);
		}

		foreach ($preferences as $k => $v) {
			if ('' == $v) {
				unset($preferences[$k]);
			}
		}

		$view_selection_url = $this->view->url(
																					 array_merge($preferences, array('controller' => 'cms', 'action' => 'viewsummary')),
																					 null,
																					 true
																					 );

		return $this->_renderListItem($menuitem, $view_selection_url);
	}


	/**
	 * @param array $menuitem
	 * @return string
	 */
	protected function _menuBIBNUMBuilder($menuitem) {
		$content = '';

		if (!$album = Class_Album::getLoader()->find($menuitem["preferences"]['album_id']))
			return '';

		$url_booklet = $this->view->url(array('controller' => 'bib-numerique',
																					'action' => 'booklet',
																					'id' => $album->getId()));

		$pageno = 1;
		foreach($album->getRessourcesWithTitre() as $ressource) {
			$content .=  $this->_renderLIAnchor(sprintf('%s/#/page/%d', 
																									$url_booklet, 
																									$ressource->getOrdre()+1),
																					$ressource->getTitre());
			$pageno = $pageno + 1;
		}

		return sprintf('%s<li class="menuGauche" %s><ul>%s</ul></li>', 
									 $this->_renderListItem($menuitem),
									 $this->_li_style, 
									 $content);

	}


	/**
	 * @param array $menuitem
	 * @param string $url
	 * @param string $target
	 * @return string
	 */
	private function _renderListItem($menuitem, $url=null, $target=null) {
		$a_href = $url ? "href='".htmlspecialchars($url)."'" : "";
		$a_target = $target ? "target='$target'" : "";

		$content ='<li class="menuGauche">';
		if ($menuitem["picto"] > '' and $menuitem["picto"] != "vide.gif")
			$content.= sprintf('<img alt="%s" src="%s" />',
												 $this->translate()->_('pictogramme pour %s',
																							 $menuitem["libelle"]),
												 $this->path_ico.$menuitem["picto"]);

		$content .=
			'<a '.$a_href.' '.$a_target.' onclick="afficher_sous_menu(this)">'.
			htmlspecialchars($menuitem["libelle"]).
			'</a>';

		$content .= '</li>';

		return $content;
	}


	/**
	 * @param array $menuitem
	 * @return string
	 */
	private function _defaultBuilder($menuitem) {
		$param_url = $this->_cls_menu->getUrl($menuitem["type_menu"], $menuitem["preferences"]);
		$url = $param_url["url"];
		$target = ($param_url["target"] > "") ? $param_url["target"] : null;

		return $this->_renderListItem($menuitem, $param_url["url"],	$target);
	}
}