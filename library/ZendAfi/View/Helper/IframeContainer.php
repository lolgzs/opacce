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
// OPAC3 - Containeur Iframe pour objets flash et javascript
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_IframeContainer extends ZendAfi_View_Helper_BaseHelper {
	protected $_params = array();
	protected $_src_args = array();
	protected $_url;

	//------------------------------------------------------------------------------------------------------
	// Main routine
	//------------------------------------------------------------------------------------------------------
	public function iframeContainer($largeur,$hauteur,$url_array,$preferences=false)	{
		if($preferences) {
			foreach($preferences as $clef => $valeur)
				$this->_src_args[$clef] = urlencode($valeur);
		}

		$this->_url = $this->view->url($url_array);


		$this->_params = array('height' => $hauteur,
													 'style' => 'border: 0px; overflow:hidden',
													 'width' => $largeur,
													 'scrolling' => 'no');
		return $this->getHtml();
	}


	public function setCacheKey($key) {
		$this->_src_args['cachekey'] = $key;
	}


	public function getHtml() {
		$absolute_url = sprintf("http://%s%s",
														$_SERVER['HTTP_HOST'],
														$this->_url);
		$this->_params['src'] = sprintf("%s?%s",
																		$absolute_url,
																		http_build_query($this->_src_args, '', '&amp;'));


		$iframe_attributes = '';
		foreach($this->_params as $name => $value)
			$iframe_attributes .= " $name='$value' ";

		return sprintf("<iframe %s>&nbsp;</iframe>", $iframe_attributes);
	}
}