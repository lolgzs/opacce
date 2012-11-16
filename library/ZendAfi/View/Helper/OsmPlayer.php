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

class ZendAfi_View_Helper_OsmPlayer extends Zend_View_Helper_HtmlElement {
	/**
	 * Exemple:
	 * $this->osmPlayer("audio.mytag", ['width' => '100%', 'height' => '200px']);
	 * Voir: http://mediafront.org/osmplayer/index.html
	 */
	public function osmPlayer($selector, $options) {
		$options = array_merge($options,
													 ['swfplayer' => URL_ADMIN_JS.'osmplayer/minplayer/flash/minplayer.swf',
														'logo' => URL_ADMIN_JS.'osmplayer/logo.png']);

		$this->loadJS()->addJQueryReady(sprintf('$("%s").osmplayer(%s)',
																						$selector,
																						json_encode($options)));
	}


	public function loadJS() {
		$loader = Class_ScriptLoader::getInstance();
		foreach(['compatibility', 'flags', 'async', 'plugin', 'display'] as $js)
			$loader->addAdminScript('osmplayer/minplayer/src/minplayer.'.$js);

		$loader->addAdminScript('osmplayer/minplayer/src/minplayer.js');

		foreach(['image', 'file', 'playLoader', 'players.base', 'players.html5', 'players.flash', 'players.minplayer',
						 'players.youtube', 'players.vimeo', 'controller'] as $js)
			$loader->addAdminScript('osmplayer/minplayer/src/minplayer.'.$js);

		$loader
			->addAdminScript('osmplayer/src/iscroll/src/iscroll.js')
			->addAdminScript('osmplayer/src/osmplayer.js');

		foreach(['parser.default', 'parser.youtube', 'parser.rss', 'parser.asx', 'parser.xspf', 'playlist', 'pager', 'teaser'] as $js)
			$loader->addAdminScript('osmplayer/src/osmplayer.'.$js);

		$loader->addAdminScript('osmplayer/templates/default/js/osmplayer.default.js');

		foreach(['controller', 'pager', 'playLoader', 'playlist', 'teaser'] as $template)
			$loader->addAdminScript('osmplayer/templates/default/js/osmplayer.'.$template.'.default.js');

		return $loader->addStyleSheet(URL_ADMIN_JS.'osmplayer/templates/default/css/osmplayer_default.css');
	}
}