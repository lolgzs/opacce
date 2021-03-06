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
/////////////////////////////////////////////////////////////////////////////////////

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
function getVar($cle) {
	return Class_AdminVar::get($cle);
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
// Ajoute ou soustrait des jours à une date et renvoie une date (format sql)
function ajouterJours( $date, $jours)
{
	if(!$date) return false;
	if(!$jours) return $date;
	$date=RendTimeStamp($date);
	$jours=$jours*3600*24;
	$new = $date+$jours;
	$dt=getdate($new);
	if( $dt["mon"] < 10 ) $dt["mon"] ="0".$dt["mon"];
	if( $dt["mday"] < 10 ) $dt["mday"] ="0".$dt["mday"];
	$new = $dt["year"]."-".$dt["mon"]."-".$dt["mday"];
	return $new;
}

// Soustrait date1 par date2 et rend le nombre de jours
function ecartDates( $date1, $date2 )
{
	$date1 = rendTimeStamp($date1);
	$date2 = rendTimeStamp($date2);
	$sec = $date1-$date2;
	$heures = (int)($sec/3600);
	$jours = (int)($heures/24);
	return $jours;
}

// Rend un timestamp à partir d'une date (francais ou US)
function rendTimeStamp( $date )
{
	if(! $date or substr($date,0,10) == "0000-00-00") return false;
	$elem = explode( "-", $date );
	if(strlen($elem[0]) == 4) $new = mkTime(0,0,0,$elem[1],$elem[2],$elem[0]);
	else $new = mkTime(0,0,0,$elem[1], $elem[0], $elem[2]);
	return $new;
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
