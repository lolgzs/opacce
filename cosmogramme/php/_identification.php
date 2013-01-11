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
// Arguments de la ligne de commande ou retour de la form de saisie
if(isset($_REQUEST["admin_login"]))
{
	$user = trim($_REQUEST["admin_login"]);
	$passe = trim($_REQUEST["admin_pwd"]);
}

// Controle de l'identification
if( $user )
{
	$sgbd_user=getVariable("admin_login");
	$sgbd_passe=getVariable("admin_pwd");
	if($user == $sgbd_user and $passe==$cfg["pwd_master"]) $_SESSION["passe"]="admin_systeme";
	elseif( $user == $sgbd_user And $passe == $sgbd_passe ) $_SESSION["passe"]="admin_portail";
	elseif($user == getVariable("catalog_login") and $passe == getVariable("catalog_pwd")) $_SESSION["passe"]="catalogueur";
	if($_SESSION["passe"])
	{
		if($mode_cron == true or $mode_web_service== true) return;
		redirection( URL_BASE );
	}
}

// Add CG
if($argc > 1) return true ;

// Mode web_service on renvoie une erreur
if($mode_web_service == true)
{
	// Add CG
	if($_SERVER['REMOTE_ADDR'] == "87.98.197.227"  or $_SERVER['REMOTE_ADDR'] == "217.128.119.22") return true ;
	// EndAdd CG
	require_once("classe_transaction_opac.php");
	$transac=new transaction_opac();
	$transac->erreur("Identification user ou mot de passe incorrect");
}

// On demande l'identification
if( !defined("APPLI") ) exit;
while( true )
{ 
	if( file_exists("config.php") ) break;
	if(chdir("../") == false) exit;
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset="UTF-8">
	<link rel="stylesheet" type="text/css" media="screen" href="<?PHP print(URL_BASE) ?>css/main.css"/>
	<link rel="stylesheet" type="text/css" media="screen" href="<?PHP print(URL_BASE) ?>css/form.css"/>
</head>
<body overflow="hidden">
<div  style="background-color:#f0f2f0" width="100%">
	<img src="<?PHP print(URL_IMG) ?>banniere.png">
</div>
<br><br><br><br><br><br>
<center>
<div class="form" style="width:350px;">
<form method="post" action="<?PHP print(URL_BASE) ?>index.php">
	<table class="form" width="100%" align="center">
		<tr>
			<th class="form" colspan="2">Identification administrateur du portail</td>
		</tr>
		<tr><td class="form">&nbsp;</td></tr>
		<tr>
			<td class="form" align="right">Utilisateur</td>
			<td class="form"><input type="text" name="admin_login"></td>
		</tr>
		<tr>
			<td class="form" align="right" style="height:50px">Mot de passe</td>
			<td class="form" style="height:50px"><input type="password" name="admin_pwd"></td>
		</tr>
		<tr>
			<th class="form" colspan="2"><input type="submit" class="bouton" value="Valider")</th>
		</tr>
	</table>
</form>
</div>

<?PHP
exit;
?>
