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
////////////////////////////////////////////////////////////////////////////
// INITIALISATION POUR LES FRAMES
////////////////////////////////////////////////////////////////////////////

// Se positionner à la racine de l'appli
while( true )
{ 
	if( file_exists("config.php")) break;
	if(chdir("../") == false) exit;
}
include("php/_init.php");
if(!$mode_web_service)
{
?>

	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="<?PHP print(URL_BASE) ?>css/main.css"/>
		<link rel="stylesheet" type="text/css" media="screen" href="<?PHP print(URL_BASE) ?>css/menu.css"/>
		<link rel="stylesheet" type="text/css" media="screen" href="<?PHP print(URL_BASE) ?>css/form.css"/>
		<link rel="stylesheet" type="text/css" media="screen" href="<?PHP print(URL_BASE) ?>css/notice.css"/>
		<link rel="stylesheet" type="text/css" media="screen" href="<?PHP print(URL_BASE) ?>css/nuage_tags.css"/>
		<script type="text/javascript" language="javascript">var sUrlImg="<?PHP print(URL_IMG); ?>";</script>
		<script src="<?PHP print(URL_BASE) ?>java_script/main.js" type="text/javascript" language="javascript"></script>
	</head>

<?PHP
	// Mettre le bon body
	$script=$_SERVER["SCRIPT_NAME"];
	if(strScan($script,"_menu",0) == -1 And strScan($script,"_banniere",0) == -1) print('<body class="droite">');
}
?>
