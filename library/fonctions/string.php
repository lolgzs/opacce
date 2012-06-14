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
//////////////////////////////////////////////////////////////////////////////////////
//                  FONCTIONS CHAINES DE CARACTERES
//
// @TODO@ : Ya plein de fonctions a virer mais avec prudence
/////////////////////////////////////////////////////////////////////////////////////

//----------------------------------------------------------------------------------
// Teste si on est sur un telephone portable
//----------------------------------------------------------------------------------
function isTelephone() {
	if (!array_key_exists('HTTP_USER_AGENT', $_SERVER))
		return false;

	// Test sur le user-agent
	$regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
	$regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
	$regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";	
	$regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
	$regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
	$regex_match.=")/i";		

	return (isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']))); 
}

//----------------------------------------------------------------------------------
// Debug dans un fichier log
//----------------------------------------------------------------------------------
function debug_log($chaine,$stop=false)
{
	$handle=fopen(USERFILESPATH."/debug.log","a");
	fwrite($handle,$chaine.chr(13).chr(10));
	fclose($handle);
	if($stop==true) exit;
}

//----------------------------------------------------------------------------------
// Rend une variable admin
//----------------------------------------------------------------------------------
function getVar($cle)
{
	$valeur=fetchOne("select VALEUR from bib_admin_var where CLEF='$cle'");
	return $valeur;
}

//----------------------------------------------------------------------------------
// Ecrit une variable admin
//----------------------------------------------------------------------------------
function setVar($clef,$valeur)
{
	$valeur=addslashes($valeur);
	$existe=fetchOne("select count(*) from bib_admin_var where CLEF='$clef'");
	if(!$existe)sqlExecute("insert into bib_admin_var(CLEF,VALEUR) values('$clef','$valeur')");
	else sqlExecute("update bib_admin_var set VALEUR='$valeur' where CLEF='$clef'");
}

//----------------------------------------------------------------------------------
// Variables cosmogramme : rend une liste
//----------------------------------------------------------------------------------
function getVarListeCosmogramme($clef)
{
	$sql = Zend_Registry::get('sql');
	$data=$sql->fetchOne("Select liste from variables where clef='$clef'");
	$v=split(chr(13).chr(10),$data);
	for($i=0; $i<count($v); $i++)
	{
		$elem=split(":",$v[$i]);
		if(!trim($elem[1])) continue;
		$item["code"]=$elem[0];
		$item["libelle"]=$elem[1];
		$liste[]=$item;
	}
	return $liste;
}

//----------------------------------------------------------------------------------
// Affichage d'un tableau pour debugging
//----------------------------------------------------------------------------------
function dump_array( $tableau)
{
	print( "<br><br><big><b>Tableau</b></big>");
	print("<br><br><pre>");
	print_r($tableau);
	print("</pre><br><br>");
}

//----------------------------------------------------------------------------------
// Formattage de date
//----------------------------------------------------------------------------------
function formatDate($date,$format)
{
	if(!$date) return false;
	// Decouper (francais ou anglais)
	$date=str_replace("/","-",$date);
	$elem = explode( "-", $date);
	if(strLen($elem[0]) == 4)
	{
		$an =$elem[0];
		$jour =(int)$elem[2];
	}
	else
	{
		$an =$elem[2];
		$jour =(int)$elem[0];
	}
	$mois =(int)$elem[1];
	if(strlen($jour)==1) $jour2="0".$jour; else $jour2=$jour;
	if(strlen($mois)==1) $mois2="0".$mois; else $mois2=$mois;
	$dateUs=$an . "-" . $mois2. "-" . $jour2;

	// Formatter
	switch( $format)
	{
		case 0: $date = $dateUs; break;
		case 1: $date = $jour2 . "-" . $mois2. "-" . $an;	break;
	}
	return $date;
}

/////////////////////////////////////////////////////////////////////////////////////
//////////////////// TOUT LE RESTE C'EST A VERIFIER /////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////

function stripAccents($string) {
	$string = convertFromUtf8($string);
	$string = htmlentities(strtolower($string), ENT_NOQUOTES);
	$string = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $string);
	return $string;
}

function splitArg( $arg )
{
	$res = explode('&',$arg);
	foreach($res as $args)
	{
		$res2[] = explode('=',$args);
	}
	return $res2;
}

function strRight( $chaine, $n )
{
	$chaine = convertFromUtf8($chaine);
	$len = strLen( $chaine );
	if( $n < 0 ){
		$result = substr( $chaine, -$n, $len );
		return convertToUtf8($result);
	}
	$result = subStr( $chaine, ($len-$n), $n );
	return convertToUtf8($result);
}

function strLeft( $chaine, $n )
{
	$chaine = convertFromUtf8($chaine);
	if($n < 0 ) $n = strLen($chaine) +$n;
	$result = subStr( $chaine, 0, $n );
	return convertToUtf8($result);
}

function strMid( $chaine, $deb, $len )
{
	$chaine = convertFromUtf8($chaine);
	$result = substr( $chaine, $deb, $len );
	return convertToUtf8($result);
}

function strScan( $chaine, $cherche, $posDeb)
{
	$chaine = convertFromUtf8($chaine);
	$cherche = convertFromUtf8($cherche);

	$posdeb=0;
	if(!trim($cherche)) return -1;
	if( $posDeb >0 ) $chaine = strRight( $chaine, -$posDeb );
	$pos = strpos( $chaine, $cherche, $posdeb );
	if( $pos > 0 ) return $pos + $posDeb;
	if( strLeft($chaine, strLen($cherche)) == $cherche) return $posDeb;
	return -1;
}

function strScanReverse( $chaine, $cherche, $pos ) {

	$chaine = convertFromUtf8($chaine);
	$cherche = convertFromUtf8($cherche);

	$len = strLen($cherche);
	if( $pos == -1 ) 
		$pos = strLen($chaine);
	for( $i=$pos; $i>=0; $i-- )	{
		if( substr( $chaine, $i, $len ) == $cherche ) 
			return $i;
	}
	return -1;
}

// Cherche une valeur dans un tableau et renvoie l'indice sinon -1
function array_find( $tableau, $valeur )
{
	for($i=0; $i < count($tableau); $i++)
	{
		if( strScan( $tableau[$i], $valeur, 0 ) >= 0 ) return $i;
	}
	return -1;
}

function convertFromUtf8($string){
	if (is_array($string)){
		$modified = false;
		foreach	($string as $key => $value)
		{
			$encoding = mb_detect_encoding($value, 'UTF-8, ISO-8859-1');
			if ($encoding == 'UTF-8'){
				$tmp[$key] = mb_convert_encoding($value, "ISO-8859-1", mb_detect_encoding($value, "UTF-8, ISO-8859-1, ISO-8859-15", true));
				$modified = true;
			}
		}
		if ($modified){
			$string = $tmp;
		}
		}else{
			$encoding = mb_detect_encoding($string, 'UTF-8, ISO-8859-1');
			if ($encoding == 'UTF-8'){
				$tmp = mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
				$string = $tmp;
			}

		}

		return $string;
	}

	function convertToUtf8($string){
		if (is_array($string)){
			$modified = false;
			foreach	($string as $key => $value)
			{
				$encoding = mb_detect_encoding($value, 'UTF-8, ISO-8859-1');
				if ($encoding != 'UTF-8'){
					$tmp[$key] = mb_convert_encoding($value, "UTF-8", mb_detect_encoding($value, "UTF-8, ISO-8859-1, ISO-8859-15", true));;
					$modified = true;
				}
			}
			if ($modified){
				$string = $tmp;
			}

			}else{
				$encoding = mb_detect_encoding($string, 'UTF-8, ISO-8859-1');
				if ($encoding != 'UTF-8'){
					$tmp = mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
					$string = $tmp;
				}

			}

			return $string;
		}

		function utf8_substr($str,$from,$len){
			return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
			'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
			'$1',$str);
		}

		function strlen_utf8 ($str)
		{
			$i = 0;
			$count = 0;
			$len = strlen ($str);
			while ($i < $len)
			{
				$chr = ord ($str[$i]);
				$count++;
				$i++;
				if ($i >= $len)
				break;

				if ($chr & 0x80)
				{
					$chr <<= 1;
					while ($chr & 0x80)
					{
						$i++;
						$chr <<= 1;
					}
				}
			}
			return $count;
		}

		function isPositiveInt($number) {
			if(ereg("^[0-9]+$", $number) && (int)$number >= 0){
				return true;
				} else {
					return false;
				}
			}
