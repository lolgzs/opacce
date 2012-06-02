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
chdir('..');

error_reporting(E_ALL^E_DEPRECATED);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('memory_limit', '2048M');
date_default_timezone_set('Europe/Paris');

set_include_path( realpath(dirname(__FILE__)).'/../library'
. PATH_SEPARATOR . realpath(dirname(__FILE__)).'/library/Class'
. PATH_SEPARATOR . realpath(dirname(__FILE__)).'/../library/Class'
. PATH_SEPARATOR . realpath(dirname(__FILE__)).'/../library/ZendAfi'
. PATH_SEPARATOR . '../ZendFramework-1.6.2/library'
. PATH_SEPARATOR . realpath(dirname(__FILE__)).'/../application/modules'
. PATH_SEPARATOR . realpath(dirname(__FILE__)).'/application/modules'
. PATH_SEPARATOR . realpath(dirname(__FILE__))
. PATH_SEPARATOR . get_include_path());

// Includes de base
include_once( "fonctions/fonctions.php");
require_once "Zend/Loader.php";
require_once "startup.php";

$path = dirname(__FILE__);
$parts = explode(DIRECTORY_SEPARATOR, $path);
$parts = array_reverse($parts);

define("BASE_URL", "/" . $parts[1]);
define("URL_IMG", BASE_URL . "/public/opac/skins/original/images/");
define("URL_SHARED_IMG", BASE_URL . "/public/opac/images");

setupOpac();

Zend_Registry::get('cache')->setOption('caching', true);
$cfg = new Zend_Config(Zend_Registry::get('cfg')->toArray(), true);
$cfg->amber = new Zend_Config(array('deploy' => false));
Zend_Registry::set('cfg', $cfg);

$_SERVER['SERVER_NAME'] = 'localhost';

?>
