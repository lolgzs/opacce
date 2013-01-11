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
/////////////////////////////////////////////////////////////////////
//    MAJ DES TRANSACTIONS EN TEMPS REEL PERGAME
//
//    MISE EN PLACE DU TEMPS REEL PERGAME
//-----------------------------------
//
//Côté client/serveur :
//--------------------
//	- Programme console : bib_console_internet.exe
//	- Configuration (depuis le programme console :
//			- url du service : http://URL_COSMOGRAMME/php/web_services/temps_reel_pergame.php
//			- parametres de connexion a la base situé sur internet :
//					- nom de la base
//					- login de connexion a mysql
//					- mot de passe de connexion mysql
//
//	- Autres configurations
//			- Activation du temps reel :
//					- aller dans la config internet de la page d'accueil
//					- cocher la case : dispo en temps reel
//					- ATTENTION : il faut egalement specifier la version de pergame net : opac 3.0 (en haut et a droite de l'ecran)
//			- Configurer s'il y a lieu, les parametres du proxy (menu internet de la page d'accueil)
//
//	Côté internet :
//	--------------
//		- Le service lui meme : temps_reel_pergame.php (les arguments sont fournis par pergame)
//		- Un script de mesure de la charge :  charge_serveur.php (pas d'argument) : affiche les nombre de connexions pour les 60 dernieres secondes
//		- Le fichier charge.txt qui contient un tableau associatif contenant le nombre de connexions pour les 60 dernieres secondes.
//
//		- 2 parametres de config (les premieres lignes du script : temps_reel.php)
//				- Le nom du fichier de mesure (charge.txt)
//				- le nombre maxi de connexions a accepter pour 1 seconde
//
//NB : un mode test est disponible en ajoutant à l'url : &mode=test
//
//exemple d'url :
//	http://localhost/cosmogramme/php/web_services/temps_reel_pergame.php?base=opac3&user=root&pass=&cmd=5&ligne=851790|2009-07-28&mode=test
//
////////////////////////////////////////////////////////////////////
//    MANQUE LE PARAMETRE : id_site
//    l'entete est inutile
/////////////////////////////////////////////////////////////////////

// Initialisations
if ($_SERVER['SERVER_ADDR'] != "127.0.0.1" and $_SERVER['SERVER_ADDR'] != "::1" and $_SERVER['SERVER_ADDR'] != "192.168.2.71")
{
	define("PATH_CHARGE", "/var/www/html/vhosts/opac2/www/ftp/") ;
}
else define("PATH_CHARGE","../../");
define("FICHIER",PATH_CHARGE."charge.txt");
define("FICHIER_JOUR",PATH_CHARGE."charge_jour.txt");
define("MAX_CONNECT","2");
define("TYPE_OPERATION",0);
define("ID_PROFIL_PRET",102);
define("ID_PROFIL_RESERVATION",103);

// Controle charge du serveur
$trace=$_REQUEST["trace"];
if($trace) trace("mode trace");
$statut=setConnect();
if($statut == "busy") retour($statut,"Trop de connexions");

// Controle des parametres et connexion a la base
$nomBase=$_REQUEST["base"];
$user=$_REQUEST["user"];
$pwd=$_REQUEST["pass"];
$sql = mysql_connect("localhost", $user, $pwd);
if(!$sql) retour("sqlerror" ,"Impossible de se connecter a mysql");
if(! mysql_select_db($nomBase)) retour("sqlerror","Impossible de selectionner la base de donnees");

// Traitement de l'action
$action=$_REQUEST["cmd"];
$values=str_replace("|",";",strip_tags($_REQUEST["ligne"]));
$values_array=explode(";",$values);

// Lancer le traitement
switch($action)
{
	case "2": // Nouveau prêt
		$req="insert into prets(ID_PERGAME,IDABON,ORDREABON,EN_COURS,DATE_PRET,DATE_RETOUR,SUPPORT,ID_NOTICE_ORIGINE,CODE_BARRES,ID_SITE) values(";
		$req.=$values_array[0].",";
		$req.=$values_array[1].",";
		$req.=$values_array[2].",";
		$req.="1,";
		$req.="'".$values_array[4]."',";
		$req.="'".$values_array[5]."',";
		$req.=$values_array[6].",";
		$req.=$values_array[7].",";
		$req.="'".$values_array[11]."',";
		$req.=$values_array[8].")";
		break;
	case "3": // Retour de prêt
		$req="Update prets Set EN_COURS=0 Where ID_PERGAME=".$_REQUEST["ligne"];
		break;
	case "4": //Suppression de prêt
		$req="Delete from prets Where ID_PERGAME=".$_REQUEST["ligne"];
		break;
	case "5": // prolongation de pret
		$req="Update prets Set DATE_RETOUR='".$values_array[1]."' Where ID_PERGAME=".$values_array[0];
		break;
	case "6" : // Nouvelle réservation
		$req="insert into reservations(ID_PERGAME,IDABON,ORDREABON,SUPPORT,ID_NOTICE_ORIGINE,DATE_RESA,ID_SITE) values(";
		$req.=$values_array[0].",";
		$req.=$values_array[1].",";
		$req.=$values_array[2].",";
		$req.=$values_array[3].",";
		$req.=$values_array[4].",";
		$req.="'".$values_array[5]."',";
		$req.=$values_array[6].")";
		break;
	case "7": // Suppression de réservation
		$req="Delete from reservations Where ID_PERGAME=".$_REQUEST["ligne"];
		break;
	default: 
		retour("erreur","fonction incorrecte : doit etre comprise entre 2 et 7");
		break;
}

// Execute requete et renvoie ok
if($trace) trace("REQUETE=".$req);
try
{
	$controle=mysql_query($req);
	if($controle == false)
	{
		$statut="sqlerror";
		$msg="Erreur sql à l'execution de la requete : " .$req;
	}
}
catch (Exception $e) 
{
	$statut="sqlerror";
	$msg="Erreur sql à l'execution de la requete : " .$req.'<br>'.$e->getMessage();
}
mysql_close();

// Retour
retour($statut,$msg);

//-------------------------------------------------------------------
// FONCTIONS
//-------------------------------------------------------------------
function setConnect()
{
	global $trace;
	// Lire le fichier
	$charge=@file_get_contents(FICHIER);
	if($charge == false) 
	{
		$charge=array_fill(0, 61, 0);
		$charge[0]=time();
	}
	else $charge=unserialize($charge);
	$last_connect=array_shift($charge);
	
	// Raz des secondes selon les cas
	$actual_connect=time();
	$diff=$actual_connect-$last_connect;
	if($trace) trace("Diff=".$diff);
	if($diff > 119) $charge=array_fill(0, 60, 0);
	elseif($diff > 59)
	{
		$actual_sec=intval(date("s",$actual_connect));
		$last_sec=intval(date("s",$last_connect));
		if($actual_sec < $last_connect) $actual_sec+=60;
		if($trace) trace("ACTUAL_SEC=".$actual_sec." - LAST_SEC=". $last_sec);
		for($i=0; $i<=($actual_sec-$last_sec); $i++)
		{
			$index=$last_sec+$i;
			if($index > 59) $index-=60;
			$charge[$index]=0;
		}
	}

	// Incremente la seconde courante
	$seconde=intval(date("s",$actual_connect));
	$charge[$seconde]++;
	$moyenne=array_sum($charge) /60;
	if($moyenne > MAX_CONNECT) $statut="busy"; else $statut="ok";
	
	// Raz de la seconde suivante
	$seconde++;
	if($seconde == 60) $seconde=0;
	$charge[$seconde]=0;
	
	// Reecriture de la charge
	array_unshift($charge,$actual_connect);
	$charge=serialize($charge);
	file_put_contents(FICHIER,$charge);
	
	// Maj stats du jour
	$charge_jour=@file_get_contents(FICHIER_JOUR);
	if($charge_jour) $charge_jour=unserialize($charge_jour);
	if($charge_jour["date"] != date("d-m-Y")) 
	{
		$charge_jour=array("busy" => 0,"ok" => 0);
	}
	$charge_jour["date"]=date("d-m-Y");
	$charge_jour[$statut]++;
	$charge_jour=serialize($charge_jour);
	file_put_contents(FICHIER_JOUR,$charge_jour);
	
	// Retour du statut
	return $statut;
}

function retour($statut,$msg)
{
	print($statut);
	if($_REQUEST["mode"]=="test") print('<br>'.$msg.'<br>');
	exit;
}

function trace($msg)
{
	print($msg.'<br>');
}
?>
