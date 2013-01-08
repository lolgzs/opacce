<?PHP
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
// Constantes
define("VERSION_COSMOGRAMME","6.25");
define("PATCH_LEVEL","141");
define("APPLI","cosmogramme");
define("CRLF", chr(13) . chr(10));
define("BR","<br />");
define("COSMOPATH", "/var/www/html/vhosts/opac2/www/htdocs");

date_default_timezone_set('Europe/Paris');

$argc = isset($argc) ? $argc : 0;
if ($argc != 3) {
	$site= "/" . substr($_SERVER['SCRIPT_NAME'], 1, strpos($_SERVER['SCRIPT_NAME'], "/" . APPLI . "/") -1) . "/" ;

	if ($_SERVER['SERVER_ADDR'] != "127.0.0.1" and $_SERVER['SERVER_ADDR'] != "::1")	{
		$cfgfile= COSMOPATH . $site . APPLI . "/config.php" ;
	}	else {
		$cfgfile= "config.php";
	}
}

else $cfgfile="./config.php";

define("URL_BASE","http://" . $_SERVER["HTTP_HOST"] . $site . APPLI . "/");
define("URL_IMG", URL_BASE ."images/");

// Includes
set_include_path(get_include_path().PATH_SEPARATOR.'./php/' . PATH_SEPARATOR . "./php/classes/");

require("classe_sql.php");
require("fonctions/sql.php");
require("fonctions/fonctions_base.php");
require("fonctions/string.php");
require("fonctions/erreur.php");
require("fonctions/date_heure.php");
require("fonctions/variables.php");

// Lire la config
$cfg=lireConfig($cfgfile);

// Add cg
if($argc == 3) chdir("../../../php/cosmo/");

// Mode web_service
if(strpos($_SERVER["REQUEST_URI"],"/web_services/")) $mode_web_service=true;

// Connexion à la base de données
global $sql;
$sql=new sql($cfg["integration_server"],$cfg["integration_user"],$cfg["integration_pwd"],$cfg["integration_base"]);
//sqlExecute('set names "UTF8"');
// Url pergame service
define("URL_SERVICE",getVariable("url_services"));

// Session
if (!session_id())
	session_start();

// Controle login
if (isset($_REQUEST["action"]) && $_REQUEST['action'] == "logout") unset($_SESSION["passe"]);
if (!isset($_SESSION["passe"])) include("_identification.php");

?>
