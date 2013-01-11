<?PHP
// Constantes
define("VERSION_COSMOGRAMME","6.24");
define("PATCH_LEVEL","141");
define("APPLI","cosmogramme");
define("CRLF", chr(13) . chr(10));
define("BR","<br />");
define("COSMOPATH", "/var/www/html/vhosts/opac2/www/htdocs");

date_default_timezone_set('Europe/Paris');

// Add cg: site= nom du site Opac (Ex: mabellebib.net)
$argc = isset($argc) ? $argc : 0;
if ($argc != 3) {
	if ($_SERVER['SERVER_ADDR'] != "127.0.0.1" and $_SERVER['SERVER_ADDR'] != "::1")
	{
		$site= "/" . substr($_SERVER['SCRIPT_NAME'], 1, strpos($_SERVER['SCRIPT_NAME'], "/" . APPLI . "/") -1) . "/" ;
		$cfgfile= COSMOPATH . $site . APPLI . "/config.php" ;
	}
	else
	{
		$site= "/" ;
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
