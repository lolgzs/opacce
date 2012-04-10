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
// OPAC3 : Debut multilingue
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Controller_Plugin_SetupLocale extends Zend_Controller_Plugin_Abstract {
	function preDispatch(Zend_Controller_Request_Abstract $request) {
		$session = Zend_Registry::get('session');
		$translate = Zend_Registry::get('translate');
		$locale = Zend_Registry::get('locale');

		if ($lang = $request->getParam('language'))
			$session->language = $lang;

		if (isset($session->language))
			$requestedLanguage = $session->language;
		else {
			$locale->setLocale(Zend_Locale::BROWSER);
			$requestedLanguage = key($locale->getBrowser());
		}

		if(in_array($requestedLanguage, $translate->getList())){
			$language = $requestedLanguage;
		} else {
			$language = Class_AdminVar::getDefaultLanguage();
		}

		$locale->setLocale($language);
		$translate->setLocale($language);

		$locale_params = array(LC_TIME);
		$this->_appendLocaleCodes($language, $locale_params);
		call_user_func_array('setlocale', $locale_params);

		if ('admin' ==  $request->getModuleName())
			Class_Profil::setDefaultTranslator(new Class_Profil_NullTranslator(null));
	}


	protected function _appendLocaleCodes($language, &$an_array) {
		switch ($language) {
		case 'fr':
			array_push($an_array, 'fr_FR.UTF-8', 'fr_FR');
			break;
		case 'en':
			array_push($an_array, 'en_US', 'en_US.UTF-8');
			break;
		case 'ro':
			array_push($an_array, 'ro_RO.', 'ro_RO.UTF-8');
			break;
		}
	}
}