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
//////////////////////////////////////////////////////////////////////////////////////
//                  FONCTIONS DATES ET HEURES
//
// Formats de dates :
// 	0=Date sql
// 	1=En français
// 	2=Avec mois (ex : 10 septembre 2008)
// 	3= Format long (ex mardi 10 septembre 2008) 
// 	4= Format unimarc (yyyymmdd) 
/////////////////////////////////////////////////////////////////////////////////////


// Formattage de date
function rendDate($date, $format)
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
		case 1: $date = $jour2 . "-" . $mois2. "-" . $an; break;
		case 2: $date = $jour . " " . rendMois($mois). " " . $an; break;
		case 3: $timeStamp = strtotime($dateUs); $date=rendJour(date("w",$timeStamp)) . " " . $jour . " " . rendMois($mois). " " . $an; break;
		case 4: $date=$an.$mois2.$jour2;
	}
	return $date;
}

// Rend un libellé de mois
function rendMois($mois)
{
	$lib = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
	return $lib[$mois];
}

// Rend un libellé de jour
function rendJour($jour)
{
	$lib = array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi");
	return $lib[$jour];
}

// Renvoie la date du jour au format : 0=SQL, 1=français
function dateDuJour($format)
{
	if( $format == 0 ) $date=date("Y-m-d");
	if( $format == 1 ) $date=date("d-m-y");
	if( $format == 2 ) $date=date("Y-m-d H:i:s");
	return $date;
}

// Renvoie la date du jour et l'heure au format SQL
function dateTimeDuJour()
{
	$date=getDate(time());
	if( $date["mon"] < 10 ) $date["mon"] ="0".$date["mon"];
	if( $date["mday"] < 10 ) $date["mday"] ="0".$date["mday"];
	if( $date["hours"] < 10 ) $date["hours"] ="0".$date["hours"];
	if( $date["minutes"] < 10 ) $date["minutes"] ="0".$date["minutes"];
	if( $date["seconds"] < 10 ) $date["seconds"] ="0".$date["seconds"];
	$new = $date["year"]."-".$date["mon"]."-".$date["mday"]." ".$date["hours"].":".$date["minutes"].":".$date["seconds"];
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

// Rend un timestamp à partir d'une date (francais ou US)
function rendTimeStamp( $date )
{
	if(! $date or substr($date,0,10) == "0000-00-00") return false;
	$elem = explode( "-", $date );
	if(strlen($elem[0]) == 4) $new = mkTime(0,0,0,$elem[1],$elem[2],$elem[0]);
	else $new = mkTime(0,0,0,$elem[1], $elem[0], $elem[2]);
	return $new;
}

?>