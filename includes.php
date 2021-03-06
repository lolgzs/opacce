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

set_include_path('.' . PATH_SEPARATOR . './library'
								 . PATH_SEPARATOR . '../ZendFramework-1.6.2/library'
								 . PATH_SEPARATOR . get_include_path());

// Includes de base
include_once "local.php";
$site= substr($_SERVER['SCRIPT_NAME'], 1, strpos($_SERVER['SCRIPT_NAME'], "index.php") -2);
$parts=explode('/', $site);
if(!file_exists("../" . end($parts))) {
	echo "Erreur de vhost !" ;
	exit ;
}

define("BASE_URL", "/" . $site) ;

include_once "fonctions/fonctions.php";
require_once "Zend/Loader.php";
require_once "library/startup.php";
?>