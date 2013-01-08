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
//////////////////////////////////////////////////////////////////////////////
//   FONCTIONS DE BASE
//
//		-> lireConfig
//		-> RendArgumentsUrl
//		-> Redirection
//////////////////////////////////////////////////////////////////////////////

// Vérif et découpage fichier de config
function lireConfig($fic)
{
	@$v = file($fic) or afficherErreur( "Impossible d'ouvrir le fichier de configuration : ".$fic);
	$debut = false;
	for($i=0;$i<count($v);$i++)
	{
		if( trim($v[$i]) == "" Or subStr( $v[$i], 0, 2 ) == "//") continue;
		if($debut == true)
		{
			$l=explode("=",$v[$i]);
			$cfg[$l[0]]=trim($l[1]);
		}
		elseif( trim($v[$i]) == "?>" ) $debut = true;
	}
	return $cfg;
}
// ---------------------------------------------------	
// Clef se securite
// ---------------------------------------------------	
function getClefSecurite()
{
	$clef="IMG".date("DxzxYxM")."VIG";
	$clef=md5($clef);
	return $clef;
}
// Redirection en javascript
function redirection( $url )
{
	if(strleft($url,4) != "http") $url=URL_BASE."php/".$url;
	print("<script>");
	print("document.location='".$url."'");
	print("</script>");
	print("</body>");
	print("</html>");
	exit;
}

// Rend une balise <img> avec un onclick= ver une url
function rendUrlImg($img,$script,$arguments,$bulle="")
{
	if($bulle) $bulle=' title="'.$bulle.'"';
	$url='<img src="' . URL_IMG . $img .'" border="0" style="cursor:pointer" onclick="document.location=\'';
	$url .= URL_BASE .'php/' .$script . '?'. $arguments . '\'"'.$bulle.'>';
	return $url;
}
// Rend un bouton simple avec un onclick= vers une url
function rendBouton($libelle,$script,$arguments,$confirmation="")
{
	if( strRight($script,3) != "php") $script.=".php";
	$libelle = str_replace(" ","&nbsp;", $libelle);
	if($confirmation) $confirmation="if(confirm('".$confirmation."')==true) ";
	$url='<input type="button" class="bouton" value=' .$libelle. ' onclick="'.$confirmation.'document.location=\'';
	$pos=strScan($script,"/php/");
	if($pos > 0) $script=strMid($script,($pos+5),256);
	$url .= URL_BASE .'php/' .$script . '?'. $arguments . '\'">';
	return $url;
}

///////////////////////////////////////////////////////////////////////////////////////
// DEBUG
///////////////////////////////////////////////////////////////////////////////////////

// Affiche le contenu d'un tableau
function dump_array( $tableau)
{
	print( "<br><br><big><b>Tableau</b></big>");
	print("<br><br><pre>");
	print_r($tableau);
	print("</pre><br><br>");
}
function dump_var($texte)
{
	print(htmlspecialchars($texte));
}
function dump_ascii($chaine)
{
	for($i=0;$i<strlen($chaine);$i++)
	{
		$car=strmid($chaine,$i,1);
		print("CHAR=".$car."=".ord($car).BR);
	}
}
function traceDebug($trace=false,$exit=false)
{
	// Script et fonction
	$stack=debug_backtrace();
	$lig=$stack[1];
	print('<div class="trace_debug">');
	if($niveau==100) dump_array($stack);
	else
	{
		print("<b>Script : </b>". $lig["file"]);
		print(" - <b>Ligne : </b>". $lig["line"]);
		if($lig["class"]) print(" - <b>Classe : </b>". $lig["class"]);
		print(" - <b>Fonction : </b>". $lig["function"].BR);
		// Données
		if($trace)
		{
			if(gettype($trace) == "array") dump_array($trace);
			else print('<b>Message : </b>'.$trace.BR);
		}
	}
	print('</div>');
	flush();
	if($exit == true) exit;
}
?>