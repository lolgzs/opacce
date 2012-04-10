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

class ZendAfi_Controller_IFrameAction extends Zend_Controller_Action {
	function rendercacheAction() {
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('iframe.phtml');
		$this->renderScript('rendercache.phtml');
	}


	protected function _getCacheKey() {
		if (!$key = $this->_getParam('cachekey', false))
			return false;

		return $key.'iframe';
	}


	function preDispatch() {
		// si on est déjà passé par le preDispatch (à cause du _forward) et le 
		// le html est déjà généré, on sort => sinon boucle infinie
		if ($this->view->cached_html) return; 
		
		if (!$key = $this->_getCacheKey()) return;

		if (!$content = Zend_Registry::get('cache')->load($key))	return;
		
		if (!$html = array_first(ZendAfi_Filters_Serialize::unserialize($content))) return;

		$this->view->cached_html = $html;
		$this->_forward('rendercache');
	}


	function postDispatch() {
		if ($this->view->cached_html or !$key = $this->_getCacheKey())
			return;

		$content_to_cache = ZendAfi_Filters_Serialize::serialize(array($this->_response->getBody())); 
		Zend_Registry::get('cache')->save($content_to_cache, $key);
	}
}

?>