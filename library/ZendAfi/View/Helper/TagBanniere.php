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
class ZendAfi_View_Helper_TagBanniere extends Zend_View_Helper_HtmlElement {
	public function tagBanniere() {
		$this->_profil = $this->view->profil;
		if ($this->_profil->getHeaderImgCycle()) {
			Class_ScriptLoader::getInstance()
				->addScript(URL_JAVA . 'diaporama/jquery.cycle.all.min')
				->addJQueryReady(sprintf('$("#banniere a.home").cycle(%s)', 
																 json_encode(array('fx' => 'fade',
																									 'width' => $this->_profil->getLargeurSite(),
																									 'height' => $this->_profil->getHauteurBanniere()))));
		}

		return sprintf('<div id="banniere">%s<a class="home" href="%s" style="display:block">%s</a></div>',
									 $this->_getLogos(),
									 BASE_URL,
									 $this->_getImages());
	}



	protected function _getLogos() {
		return $this->_getLogo('gauche').$this->_getLogo('droite');
	}

	
	protected function _getLogo($position) {
		if ($this->_profil->_has('logo_'.$position.'_img'))
			return sprintf("<div class='logo_%s'><a href='%s'><img src='%s' alt=''/></a></div>",
										 $position,
										 $this->_profil->_get('logo_'.$position.'_link'),
										 $this->_profil->_get('logo_'.$position.'_img'));
		return '';
	}


	protected function _getImages() {
		$images = '';
		$largeur_site = $this->_profil->getLargeurSite();

		foreach ($this->_profil->getAllHeaderImg() as $img)
			$images .= sprintf('<img alt="%s" src="%s" style="width:%dpx" />',
												 $this->view->_("banniere du site"),
												 $img,
												 $largeur_site);

		return $images;
	}
}
?>