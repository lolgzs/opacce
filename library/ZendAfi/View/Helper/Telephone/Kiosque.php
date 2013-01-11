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
class ZendAfi_View_Helper_Telephone_Kiosque extends ZendAfi_View_Helper_Accueil_Base {
	protected function _renderHeadScriptsOn($script_loader) {
		$script_loader
			->addScript(BASE_URL . '/public/telephone/js/kiosque-slideshow')
			->addJQueryReady('$(\'#slideshow\').kiosqueSlideshow()')
			->addInlineStyle('
#slideshow {
  width: 100%;
  overflow: hidden;
  position: relative; /* or absolute, but not static */
}
#slideshow ul {
  list-style:none;
  width:9999%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  padding:0;
}
#slideshow li {
  height: 100%;
  float: left;
  margin: 0 2px; /* spacing between items */
}
#slideshow li a {
  display:block;
  overflow:hidden;
}

');
	}


	public function getHtml() {
		$this->titre = $this->preferences['titre'];

		$notices = $this->_getNotices();

		if (0 == count($notices))
			return $this->getHtmlArray();

		$_SESSION["recherche"]["retour_liste"] = $_SERVER["REQUEST_URI"];

		$this->contenu .= '<div id="slideshow" style="height:' . $this->preferences['op_hauteur_img'] . 'px"><ul>';
		foreach ($notices as $notice)	{
			$this->contenu .= sprintf('<li><a href="%s"><img src="%s" width="%s" title="%s"></a></li>',
																$this->view->url(array('controller' => 'recherche',
																											 'action' => 'viewnotice',
																											 'id' => $notice['id_notice'],
																											 'type_doc' => $notice['type_doc']), 
																								 null, true),
																$notice['vignette'],
																$this->preferences["op_largeur_img"],
																$notice['titre']);
		}

		$this->contenu .= '</ul></div>';
		return $this->getHtmlArray();
	}


	protected function _getNotices() {
		$catalogue = new Class_Catalogue();
		return $catalogue->getNoticesByPreferences($this->preferences, 'url');
	}
}