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


class ZendAfi_View_Helper_AudioJsPlayer extends Zend_View_Helper_HtmlElement {
	/**
	 * Exemple:
	 * $this->audioJsPlayer();
	 * Voir: http://kolber.github.com/audiojs/
	 */
	public function audioJsPlayer($src) {
		$options = ['swfLocation' => URL_ADMIN_JS.'audiojs/audiojs/audiojs.swf',
								'imageLocation' => URL_ADMIN_JS.'audiojs/audiojs/player-graphics.gif'];
		Class_ScriptLoader::getInstance()
			->loadJQuery()
			->addStyleSheet(URL_ADMIN_JS.'audiojs/audiojs/style-light.css')
			->addAdminScript('audiojs/audiojs/audio.min.js')
			->addInlineScript(sprintf('audiojs.events.ready(function() {audiojs.createAll(%s);})', 
																json_encode($options)));

		return '<audio controls="controls"><source src="'.$src.'"></audio>';
	}
}

?>