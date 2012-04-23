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
// OPAC3 - Crée un lien vers la page d'aide Ardans
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Admin_HelpLink extends ZendAfi_View_Helper_BaseHelper {
	protected $_mapping =
		array('profil' => array( 'index' => 3612,
														 'menusindex' => 3618,
														 'accueil' => 3614,
														 'proprietes' => 3894),

					'catalogue' => array( 'index' => 3613 ),

					'formation' => array( 'index' => 4477 ),

					'accueil' => array('kiosque' => 3651),

					'cms' => array('index' => 3611),

					'index' => array('index' => 4037,
													 'changelog' => 4488),

					'modules' => array('recherche_viewnotice' => 3647,
														 'recherche_resultat' => 3643),

					'modo' => array('index' => 3648,
													'membreview' => 3890),

					'bib' => array('index' => 3928)
				);


	public function helpLink($action = null) {
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$controller = strtolower($request->getControllerName());
		if ($action === null)
			$action = strtolower($request->getActionName());

		if (!$ardans_url = $this->_getArdansURL($controller, $action))
			return '';

		return
			"<a class='ardans_help' href='$ardans_url' target='_blank'>".
				"<img src='".URL_ADMIN_IMG."ico/help.png' alt='Aide' />".
			"</a>";
	}


	protected function _getArdansURL($controller, $action) {
		if (!array_key_exists($controller, $this->_mapping))
			return '';

		$controller_mapping = $this->_mapping[$controller];

		if (!array_key_exists($action, $controller_mapping))
			$action = 'index';

		if (!array_key_exists($action, $controller_mapping))
			return '';

		$ardans_id = $controller_mapping[$action];
		return "https://akm.ardans.fr/AFI2/invite/listerFiche.do?idFiche=$ardans_id";
	}
}