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


if (!function_exists('xdebug_break')) {
	function xdebug_break(){};
}


function setupOpac() {
	Zend_Loader::registerAutoload();

	$cfg = loadConfig();
	setupConstants($cfg);
	setupSession($cfg);
	setupLanguage();
	setupDatabase($cfg);
	setupDevOptions($cfg);
	setupControllerActionHelper();
	setupHTTPClient($cfg);
	setupCache($cfg);
	setupMail($cfg);
	return setupFrontController();
}



function defineConstant($name, $value) {
	if (!defined($name))
		define($name, $value);
}



function setupConstants($cfg) {
	defineConstant('VERSION_PERGAME','V-05.20 [r'.array_at(1, explode(' ', '$Revision$ ')).']');
	defineConstant('ROOT_PATH',  './');

	defineConstant('MODULEDIRECTORY','./application/modules');
	defineConstant('LANG_DIR', './library/translation/');
	defineConstant('SQLDOSSIER', './library/sql/');

	defineConstant('XCHANGEFILEPATH', '..' . BASE_URL . '/xchange');
	defineConstant('USERFILESPATH', '..' . BASE_URL . '/userfiles');
	defineConstant('USERFILESURL', BASE_URL . '/userfiles/');
	defineConstant('PATH_TEMP',  getcwd().'/temp/');

	defineConstant('FCKBASEPATH',  'fckeditor/');
	defineConstant('FCKBASEURL',  BASE_URL . '/fckeditor/');

	defineConstant('CKBASEPATH',  'ckeditor/');
	defineConstant('CKBASEURL',  BASE_URL . '/ckeditor/');

	defineConstant('AMBERURL',  BASE_URL . '/amber/');

	defineConstant('ERROR_LOG', '..' . BASE_URL . '/errorLog.txt');
	defineConstant('URL_ADMIN_HTML', BASE_URL . '/application/modules/admin/views/scripts/');
	defineConstant('URL_ADMIN_CSS', BASE_URL . '/public/admin/css/');
	defineConstant('URL_ADMIN_IMG', BASE_URL . '/public/admin/images/');
	defineConstant('URL_ADMIN_JS', BASE_URL . '/public/admin/js/');
	defineConstant('JQUERY', URL_ADMIN_JS . 'jquery-1.7.2.min.js');
	defineConstant('JQUERYMOBILE_VERSION',  '1.1.0');
	defineConstant('JQUERYUI', URL_ADMIN_JS . 'jquery_ui/jquery-ui-1.8.16.full.js');
	
	// il y a des autre define URL dans ZendAfi_Controller_Plugin_DefineURLs
	// par exemple URL_IMG, URL_CSS, URL_HTML et URL_JS va chercher dans 'URL_SKIN . 'nom de le module' . /html' etc.
	defineConstant('URL_PERGAME_SERVICE','http://www.pergame.net/pergame_services/main.php');
	defineConstant('URL_AMAZON','http://images-eu.amazon.com/images/P/@VIGNETTE@.08.MZZZZZZZ.jpg');
	defineConstant('DEFAULT_TITLE_ADMIN', 'Mon portail web 2.0');
	defineConstant('BR','<br />');
	defineConstant('NL',"\n");
	defineConstant('CRLF', chr(13).chr(10));


	defineConstant('URL_FLASH', BASE_URL . '/public/opac/flash/');
	defineConstant('PATH_FLASH', './public/opac/flash/');
	defineConstant('URL_JAVA', BASE_URL . '/public/opac/java/');
	defineConstant('PATH_JAVA', './public/opac/java/');
	defineConstant('PATH_ADMIN_SUPPORTS', './public/admin/images/supports/');

	defineConstant('PATH_FONTS', './public/opac/fonts/');
	defineConstant('URL_CAPTCHA', BASE_URL . '/public/captcha/');
	defineConstant('PATH_CAPTCHA', './public/captcha/');
}




function loadConfig() {
	// load configuration (local ou production)
	if(array_isset('REMOTE_ADDR', $_SERVER) and $_SERVER['REMOTE_ADDR'] == '127.0.0.1')
		$serveur='local';
	else
		$serveur='production';
	$cfg = new Zend_Config_Ini('./config.ini', $serveur);
	Zend_Registry::set('cfg', $cfg);


	setlocale(LC_TIME, 'fr_FR.UTF-8');
	date_default_timezone_set($cfg->timeZone);

	return $cfg;
}




function setupCache($cfg) {
	$frontendOptions = array(
													 'lifetime' => 900, // durée du cache: 15mn
													 'automatic_serialization' => false,
													 'caching' => true);
													 //													 'caching' => $cfg->get('caching'));

	$backendOptions = array(
													'cache_dir' => PATH_TEMP // Directory where to put the cache files
													);

	// getting a Zend_Cache_Core object
	$cache = Zend_Cache::factory('Core',
															 'File',
															 $frontendOptions,
															 $backendOptions);
	//	$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
	Zend_Registry::set('cache', $cache);
}




function setupSession($cfg) {
	// Start Session
	$session = new Zend_Session_Namespace('afiopac');
	if (!isset($session->initialized))
		{
			Zend_Session::regenerateId();
			$session->initialized = true;
		}
	if (!isset($session->baseUrl)) $session->baseUrl = BASE_URL;
	Zend_Registry::set('session', $session);
}



function setupLanguage() {
	Zend_Locale::setDefault(Class_AdminVar::getDefaultLanguage());
	Zend_Registry::set('locale', new Zend_Locale());

	$translate = new ZendAfi_Translate('gettext', LANG_DIR.'fr.mo', 'fr');
	foreach (array('en', 'ro') as $language)
		$translate->addTranslation(LANG_DIR.$language.'.mo', $language);
	Zend_Registry::set('translate', $translate);

	Zend_Validate_Abstract::setDefaultTranslator($translate);
}




function setupDatabase($cfg) {
	// setup database
	$sql = Zend_Db::factory($cfg->sgbd->adapter, $cfg->sgbd->config->toArray());

	Zend_Db_Table::setDefaultAdapter($sql);

	$afi_sql = new Class_Systeme_Sql(
																	 $cfg->sgbd->config->host,
																	 $cfg->sgbd->config->username,
																	 $cfg->sgbd->config->password,
																	 $cfg->sgbd->config->dbname);
	Zend_Registry::set('sql', $afi_sql);


	Zend_Db_Table::getDefaultAdapter()->query('set names "UTF8"');
}



function setupDevOptions($cfg) {
	//permet d'activer les fonctions en développement
	if (null !== $experimental_dev = $cfg->get('experimental_dev'))
		defineConstant('DEVELOPMENT', $experimental_dev);
}




function setupControllerActionHelper() {
	Zend_Controller_Action_HelperBroker::addHelper(new ZendAfi_Controller_Action_Helper_ViewRenderer());
	Zend_Controller_Action_HelperBroker::addPrefix('ZendAfi_Controller_Action_Helper');
}




function setupHTTPClient($cfg) {
	//set up HTTP Client to use proxy settings
	$httpClient = new Zend_Http_Client();
	if ( (isset ($cfg->proxy->host) ) || ($cfg->proxy->host != '') ){
		$proxyConfig = array(
												 'adapter'    => 'Zend_Http_Client_Adapter_Proxy',
												 'proxy_host' => $cfg->proxy->host,
												 'proxy_port' => $cfg->proxy->port,
												 'proxy_user' => $cfg->proxy->user,
												 'proxy_pass' => $cfg->proxy->pass
												 );
		Zend_Registry::set('http_proxy',$proxyConfig);

		$proxy_adapter = new Zend_Http_Client_Adapter_Proxy();
		$proxy_adapter->setConfig($proxyConfig);
		$httpClient->setAdapter($proxy_adapter);
	}else{
		$proxyConfig = null;
	}

	$httpClient->setConfig(array('timeout' => 2));
	Zend_Registry::set('httpClient',$httpClient);
}


function setupMail($cfg) {
	if (defined('SMTP_HOST')) {
		Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp(SMTP_HOST));
		return;
	}
		
	if (!$cfg->mail->transport->smtp)
		return;

	Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp($cfg->mail->transport->smtp->host,
																															$cfg->mail->transport->smtp->toArray()));
}


function setupFrontController() {
	$front_controller = Zend_Controller_Front::getInstance()
		->addModuleDirectory(MODULEDIRECTORY)
		->addControllerDirectory(ROOT_PATH.'afi/application/modules/opacpriv/controllers','opacpriv')	
		->setDefaultModule('opac')
		->setBaseUrl(BASE_URL)
		->registerPlugin(new ZendAfi_Controller_Plugin_AdminAuth())
		->registerPlugin(new ZendAfi_Controller_Plugin_SetupLocale())
		->registerPlugin(new ZendAfi_Controller_Plugin_DefineURLs())
		->registerPlugin(new ZendAfi_Controller_Plugin_InitModule())
		->registerPlugin(new ZendAfi_Controller_Plugin_SelectionBib())
		->setParam('useDefaultControllerAlways', true);

	return setupRoutes($front_controller);
}


function setupRoutes($front_controller) {
	$front_controller
		->getRouter()
		->addRoute('embed', 
							 new Zend_Controller_Router_Route(
																								'embed/:controller/:action/*', 
																								array('module' => 'telephone',
																											'controller' => 'index',
																											'action' => 'index')))
		->addRoute('flash', 
							 new Zend_Controller_Router_Route(
																								'flash/:action/*', 
																								array('module' => 'opacpriv',
																											'controller' => 'flash',
																											'action' => 'index')));

	return $front_controller;
}

?>