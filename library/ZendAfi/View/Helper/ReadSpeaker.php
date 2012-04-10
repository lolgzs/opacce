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
/*
 * Affiche le widget de lecture Read Speaker
 */

class ZendAfi_View_Helper_ReadSpeaker extends ZendAfi_View_Helper_BaseHelper {
	protected static $_wr_id = 0;

	protected static function _new_wr_id() {
		self::$_wr_id = self::$_wr_id + 1;
		return self::$_wr_id.rand();
	}

	public function readSpeaker($controller, $action, $params) {
		$id_read_speaker = Class_AdminVar::get('ID_READ_SPEAKER');
		if (!$id_read_speaker) return '';
		$url_to_read = 'http://'.$_SERVER['SERVER_NAME'].BASE_URL."/$controller/$action?".http_build_query($params);
		$wr_id = self::_new_wr_id();
		$icon_url = URL_ADMIN_IMG.'ico/read_speaker_listen.gif';

		$html = <<<HTML
       <a class="readspeaker" href="http://wr.readspeaker.com/webreader/webreader.php?cid=$id_read_speaker&amp;t=web_free&amp;title=readspeaker&amp;url=" 
          onclick="readpage(this.href + escape('$url_to_read'),$wr_id); return false;">
       <img src="$icon_url" style="border-style: none;"  title="" alt="écoutez" /></a>
       <span id="WR_$wr_id"></span>
HTML;

		return $html;
	}
}

?>
