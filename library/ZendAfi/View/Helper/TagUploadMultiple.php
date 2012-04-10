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
class ZendAfi_View_Helper_TagUploadMultiple extends ZendAfi_View_Helper_BaseHelper {
	/**
	 * @param string $name
	 * @param string $buttonLabel
	 * @param array $args
	 * @return string
	 */
	public function tagUploadMultiple($name, $buttonLabel, Array $args) {
		return $this->getJavaScript($name, $args)
			. '<input type="button" class="bouton" value="'
			. $buttonLabel
			. '" onclick="load_' . $name . '();">'
			. '<div id="' . $name . '_conteneur"></div>';
	}


	/**
	 * @param string $name
	 * @param array $args
	 * @return string
	 */
	private function getJavaScript($name, Array $args) {
		// dimensions
		$hauteur				= 500;
		$hauteur_dialog = $hauteur + 120;
		$largeur_dialog = 800;

		// html iframe
		$iframeUrl = $this->view->url(array(
			'module'			=> 'admin',
			'controller'	=> 'upload',
			'action'			=> 'multiple',
			'modelClass'	=> $args['modelClass'],
			'modelId'			=> $args['modelId']
		));

		$iframe = '<iframe id="'
						. $name . '_iframe" height="'
						. $hauteur . '" frameborder="0" width="100%" scrolling="auto" src="'
						. $iframeUrl . '">&nbsp;</iframe>';

		// java script pour dialog
		$closingUrl = $this->view->url(array(
			'module'			=> 'admin',
			'controller'	=> 'album',
			'action'			=> 'edit_images',
			'id'					=> $args['modelId']
		));

		$js ='<script>';
		$js .= "
			function load_" . $name . "() {
				$('#" . $name . "_conteneur').dialog({
						modal: true,
						height:" . $hauteur_dialog . ",
						width:" . $largeur_dialog . ",
						title:'" . $this->view->traduire('Télécharger plusieurs fichiers') . "',
						buttons:{'" . $this->view->traduire('Fermer') . "': function() { $(this).dialog('close'); document.location='" . $closingUrl . "';}},
						zIndex:100000
				}).html('" . $iframe . "');
			}
		";
		$js.='</script>';

		return trim($js);
	}

}