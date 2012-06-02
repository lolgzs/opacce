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

/* Regénère un nouveau front controller pour chaque test,
 * sinon les paramètres de requête sont gardés.
 */

$_REQUEST["id_profil"] = $_SESSION["id_profil"] = 2;
$_SERVER['HTTP_HOST'] = 'localhost';

$controller = Zend_Controller_Front::getInstance()
	->throwExceptions(true)
	->addModuleDirectory('application/modules')
	->setDefaultModule('opac')
	->registerPlugin(new ZendAfi_Controller_Plugin_AdminAuth())
	->registerPlugin(new ZendAfi_Controller_Plugin_SetupLocale())
	->registerPlugin(new ZendAfi_Controller_Plugin_DefineURLs())
	->registerPlugin(new ZendAfi_Controller_Plugin_InitModule())
	->registerPlugin(new ZendAfi_Controller_Plugin_SelectionBib());

$_SESSION["selection_bib"]=array("message" => 'selection bib sucks',
																 "nb_notices" => 12345,
																 "html" => "<madmode>yes, really</madmode>",
																 "id_bibs" => '');

$viewRenderer = new ZendAfi_Controller_Action_Helper_ViewRenderer();
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

setupRoutes($controller);
?>