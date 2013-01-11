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

class ZendAfi_View_Helper_SocialShare extends Zend_View_Helper_HtmlElement {
	public function socialShare($profil, $networks) {
		$this->addHeadScript();

		$html = '';
		foreach ($networks as $network) 
			$html .= $this->htmlForNetwork($profil, $network);

		return '<div class="share">'.$html.'</div>';
	}


	public function htmlForNetwork($profil, $network) {
		$onclick_attr = array('facebook' => 'socialShare(\'facebook\')',
													'twitter' => 'socialShare(\'twitter\')',
													'mail' => sprintf('window.location=\'%s\'', 
																						$this->view->url(array('controller' => 'index',
																																	 'action' => 'formulairecontact'))));

		return  sprintf('<img class="%s" src="%s"  onclick="%s; return false" alt="%s"/>',
										$network,
										$this->networkImgUrl($network),										
										$onclick_attr[$network],
										'partager sur '.$network);
	}


	public function addHeadScript() {
		Class_ScriptLoader::getInstance()
			->addInlineScript(sprintf("function socialShare(network) {"
																."jQuery.getScript('%s/index/share/on/' + network + '/titre/' + $(\"title\").text() + '?url=' + encodeURIComponent(window.location));"
																."}",
																BASE_URL));
	}


	public function networkImgUrl($network) {
		$file = $network.'.png';
		return file_exists(PATH_SKIN.'/images/reseaux/'.$file) ? URL_IMG.'reseaux/'.$file : URL_SHARED_IMG.'reseaux/'.$file;
	}
}

?>