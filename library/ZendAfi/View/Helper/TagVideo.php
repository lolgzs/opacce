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

class ZendAfi_View_Helper_TagVideo extends Zend_View_Helper_HtmlElement {
	public function tagVideo($album) {
		Class_ScriptLoader::getInstance()
			->addScript('http://vjs.zencdn.net/c/video.js')
			->addStylesheet('http://vjs.zencdn.net/c/video-js.css')
			->addInlineStyle('.video-js {margin: 5px auto}');


		$html = '';
		$trailers = $album->getTrailers();
		foreach($trailers as $trailer) {
			$html .= sprintf('<source src="%s" type="%s">',
											 $trailer->getUrl(),
											 $trailer->getMimeType());
		}

		return sprintf('<video id="my_vid" class="video-js vjs-default-skin" poster="%s" controls preload="auto" data-setup="{}" width="640" height="400">%s</video>',
									 $album->getPoster(),
									 $html);
	}
}

?>