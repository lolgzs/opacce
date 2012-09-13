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
	public function osmPlayer($album) {
		$loader = Class_ScriptLoader::getInstance();
		$div_id = 'osmplayer'.$album->getId();

		$loader
			->addAdminScript('osmplayer/minplayer/bin/minplayer.js')
			->addAdminScript('osmplayer/src/iscroll/src/iscroll.js')
			->addAdminScript('osmplayer/src/osmplayer.js')
			->addInlineStyle('.minplayer-default-logo {display: none}');

		foreach(['parser.default', 'parser.youtube', 'parser.rss', 'parser.asx', 'parser.xspf', 'playlist', 'pager', 'teaser'] as $js)
			$loader->addAdminScript('osmplayer/src/osmplayer.'.$js);

		$loader->addAdminScript('osmplayer/templates/default/js/osmplayer.default.js');

		foreach(['controller', 'pager', 'playLoader', 'playlist', 'teaser'] as $template)
			$loader->addAdminScript('osmplayer/templates/default/js/osmplayer.'.$template.'.default.js');

		$loader
			->addStyleSheet(URL_ADMIN_JS.'osmplayer/templates/default/css/osmplayer_default.css')
			->addJQueryReady(sprintf('$("#%s").osmplayer({playlist: \'%s.xml\', height: \'500px\'})',
															 $div_id,
															 $this->view->url(['module' => 'opac', 
																								 'controller' => 'bib-numerique',
																								 'action' => 'album-xspf-playlist', 
																								 'id' => $album->getId()])));
		return '<div id="'.$div_id.'"></div>';
	}
}

?>