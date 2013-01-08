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
//////////////////////////////////////////////////
// GENERATION DE SITE PERGAME
//////////////////////////////////////////////////
include("_init_frame.php");

// Instanciations
require_once("classe_bib.php");
$oBib = new bibliotheque();
require_once("classe_indexation.php");
$ix = new indexation();

// Titre page
echo "<h1>Génération automatique des paramètres d'intégration pour Pergame et Nanook</h1>";
flush();

// ----------------------------------------------------------------
// FORM DE SAISIE
// ----------------------------------------------------------------
if(!$_REQUEST["action"])
{
	echo '<h3>Mode d\'emploi :</h3>';
	echo '<div class="liste">';
	echo '1 - Faire un export total depuis Pergame ou Nanook dans le dossier ftp de réplication des fichiers.'.BR;
	echo '2 - Sélectionner le dossier dans le formulaire ci-dessous.'.BR;
	echo '3 - Sélectionner le SIGB dans le formulaire ci-dessous.'.BR;
	echo '4 - Valider.'.BR;
	echo '</div>'.BR;

	// liste des dossiers de réception ftp
	$root=getVariable("ftp_path");
	if(file_exists($root)==false) afficherErreur('La variable ftp_path est absente, incorrecte ou le dossier n\'a pas été créé.',true);
	$dir=opendir($root);
	while (($fic = readdir($dir)) !== false)
	{
		if(filetype($root.$fic) != "dir") continue;
		if(substr($fic,0,1)=="." or $fic=="test") continue;
		$dossiers[$fic]=$fic;
	}
	if(!count($dossiers)) afficherErreur('Aucun dossier n\'a été trouvé dans le dossier des transferts ftp : .'.$root,true);

	// formulaire
	require("fonctions/objets_saisie.php");
	print('<div class="liste">');
	print('<form method="post" action="'.URL_BASE.'php/integre_generation_pergame.php?action=CONTROLE">');
	print('<table class="form" width="100%" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2" align="left">Sélection du dossier ftp</th></tr>');
	print('<tr><td class="form_first" align="right" width="43%">Nom du dossier ftp</td><td class="form_first">'.getComboSimple("path_ftp","",$dossiers,"").'</td></tr>');
	print('<tr><td class="form_first" align="right">SIGB</td><td class="form_first">'.getComboSimple("type_sigb","",array(1=>"pergame", 13=>"nanook"),"").'</td></tr>');
	print('<tr><td class="form_first" align="right">url du web-service (Nanook uniquement)</td><td class="form_first">'.getChamp("service_nanook","",55).'</td></tr>');
	print('<tr><td  class="form_first" align="right">Format(0.8.7) :</td><td class="form_first">ip:port/chemin_tomcat/ilsdi/nom_base</td></tr>');
	print('<tr><td class="form_first" align="right">Exemple :</td><td class="form_first">62.193.55.152:8080/afi_NanookWs/ilsdi/NANOOK</td></tr>');
	print('<tr><td class="form_first" align="right">Créer les annexes comme les bibliothèques</td><td class="form_first">'.getComboSimple("creer_annexes","",array(1=>"oui", 2=>"non"),"").'</td></tr>');
	print('<tr><td class="form_first" align="right">Synchroniser l\'étalon à chaque import</td><td class="form_first">'.getComboSimple("synchro","",array(1=>"oui", 2=>"non"),"").'</td></tr>');
	//print('<tr><td class="form_first" align="right">Générer uniquement les intégrations</td><td class="form_first">'.getComboSimple("inegration_only","",array(0=>"non",1=>"oui"),"").'</td></tr>');
	print('<tr><th class="form" colspan="2" align="center"><input type="submit" class="bouton" value="Valider"></th></tr>');
	print('</table></form></div>');
	exit;
}

// ---------------------------------------------------------------
// CREATION DES DONNEES
// ----------------------------------------------------------------
if($_REQUEST["action"]=="CONTROLE")
{
	// repertoire etalon
	$path=getVariable("ftp_path");
	$path_etalon=$path.$_REQUEST["path_ftp"]."/";
	$type_sigb = $_REQUEST["type_sigb"];
	if(file_exists($path_etalon."/etalon") == false) afficherErreur('Impossible de trouver le dossier "etalon" dans le dossier : '.$path_etalon,true);
	$path_etalon.="/etalon/";

	// controle des profils de données
	if($type_sigb==1) // pergame
	{
		$enreg=fetchEnreg("select * from profil_donnees where id_profil=100");
		if(!$enreg or $enreg["type_fichier"]!= 0) afficherErreur("Le profil de données : Unimarc Pergame n'est pas conforme à l'étalon");
		$enreg=fetchEnreg("select * from profil_donnees where id_profil=101");
		if(!$enreg or $enreg["type_fichier"]!= 1) afficherErreur("Le profil de données : Abonnés Pergame n'est pas conforme à l'étalon");
		$enreg=fetchEnreg("select * from profil_donnees where id_profil=102");
		if(!$enreg or $enreg["type_fichier"]!= 2) afficherErreur("Le profil de données : Prêts Pergame n'est pas conforme à l'étalon");
		$enreg=fetchEnreg("select * from profil_donnees where id_profil=103");
		if(!$enreg or $enreg["type_fichier"]!= 3) afficherErreur("Le profil de données : Réservations Pergame n'est pas conforme à l'étalon");
	}

	if($type_sigb==13) // nanook
	{
		$enreg=fetchEnreg("select * from profil_donnees where id_profil=104");
		if(!$enreg or $enreg["type_fichier"]!= 0) afficherErreur("Le profil de données : Unimarc Nanook n'est pas conforme à l'étalon");
		$enreg=fetchEnreg("select * from profil_donnees where id_profil=105");
		if(!$enreg or $enreg["type_fichier"]!= 1) afficherErreur("Le profil de données : Abonnés Nanook n'est pas conforme à l'étalon");
		$id_profil_code_barre=fetchOne("select id_profil from profil_donnees where libelle ='Liste de codes-barres'");
		if(!$id_profil_code_barre)
		{
			$attributs='a:6:{i:0;a:7:{s:8:"type_doc";a:11:{i:0;a:3:{s:4:"code";s:1:"0";s:5:"label";s:0:"";s:8:"zone_995";s:0:"";}i:1;a:3:{s:4:"code";s:1:"1";s:5:"label";s:5:"am;na";s:8:"zone_995";s:0:"";}i:2;a:3:{s:4:"code";s:1:"2";s:5:"label";s:2:"as";s:8:"zone_995";s:0:"";}i:3;a:3:{s:4:"code";s:1:"3";s:5:"label";s:3:"i;j";s:8:"zone_995";s:0:"";}i:4;a:3:{s:4:"code";s:1:"4";s:5:"label";s:1:"g";s:8:"zone_995";s:0:"";}i:5;a:3:{s:4:"code";s:1:"5";s:5:"label";s:3:"l;m";s:8:"zone_995";s:0:"";}i:6;a:3:{s:4:"code";s:1:"8";s:5:"label";s:0:"";s:8:"zone_995";s:0:"";}i:7;a:3:{s:4:"code";s:1:"9";s:5:"label";s:0:"";s:8:"zone_995";s:0:"";}i:8;a:3:{s:4:"code";s:2:"10";s:5:"label";s:0:"";s:8:"zone_995";s:0:"";}i:9;a:3:{s:4:"code";s:3:"100";s:5:"label";s:0:"";s:8:"zone_995";s:0:"";}i:10;a:3:{s:4:"code";s:2:"10";s:5:"label";s:0:"";s:8:"zone_995";s:0:"";}}s:17:"champ_code_barres";s:1:"f";s:10:"champ_cote";s:1:"k";s:11:"champ_genre";s:0:"";s:13:"champ_section";s:1:"q";s:17:"champ_emplacement";s:1:"u";s:12:"champ_annexe";s:1:"a";}i:1;a:1:{s:6:"champs";s:11:"code_barres";}i:2;a:1:{s:6:"champs";s:11:"code_barres";}i:3;a:1:{s:6:"champs";s:11:"code_barres";}i:5;a:3:{s:6:"champs";s:11:"code_barres";s:17:"xml_balise_abonne";s:0:"";s:17:"xml_champs_abonne";a:10:{s:6:"IDABON";s:0:"";s:9:"ORDREABON";s:0:"";s:3:"NOM";s:0:"";s:6:"PRENOM";s:0:"";s:9:"NAISSANCE";s:0:"";s:8:"PASSWORD";s:0:"";s:4:"MAIL";s:0:"";s:10:"DATE_DEBUT";s:0:"";s:8:"DATE_FIN";s:0:"";s:7:"ID_SIGB";s:0:"";}}i:4;a:5:{s:4:"zone";s:0:"";s:5:"champ";s:0:"";s:6:"format";s:0:"";s:5:"jours";s:0:"";s:7:"valeurs";s:0:"";}}';
			sqlExecute("insert into profil_donnees(libelle,accents,type_fichier,format,attributs) values('Liste de codes-barres',0,0,1,'$attributs')");
			$id_profil_code_barre=fetchOne("select id_profil from profil_donnees where libelle ='Liste de codes-barres'");
		}
	}

	// sites
	$phase=0;
	unset($enreg);
	$sites=@file($path_etalon."annexes.txt");
	if(!$sites) afficherErreur('Impossible de trouver le fichier : annexes.txt',true);

	print('<h3>'.++$phase.' - Création des bibliothèques</h3>');
	print('<div class="liste"><table class="blank">');
	foreach($sites as $ligne)
	{
		$ligne=utf8_encode($ligne);
		$elem=explode("|",$ligne);
		if($elem[0]=="BIB_SPS_UTT") continue;
		$enreg["ID_SITE"]=$elem[0];
		$enreg["LIBELLE"]=$elem[1];
		$enreg["VILLE"]=$_REQUEST["path_ftp"];
		$enreg["ID_ZONE"]=1;
		$enreg["VISIBILITE"]=2;
		echo '<tr><td class="blank">Site n° '.$enreg["ID_SITE"].'</td><td class="blank">'.$enreg["LIBELLE"].'</td></tr>';
		$controle=fetchOne("select count(*) from bib_c_site where ID_SITE=".$enreg["ID_SITE"]);
		if($controle) sqlUpdate("update bib_c_site set @SET@ where ID_SITE=".$enreg["ID_SITE"], $enreg);
		else sqlInsert("bib_c_site", $enreg);

		sqlExecute("delete from int_bib where id_bib=".$enreg["ID_SITE"]);
		$enreg1["id_bib"]=$enreg["ID_SITE"];
		$enreg1["nom"]=$enreg["LIBELLE"];
		$enreg1["nom_court"]=$enreg["LIBELLE"];
		$enreg1["qualite"]=5;
		$enreg1["sigb"]=$type_sigb;
		$enreg1["planif_mode"]="r";
		$enreg1["planif_jours"]="1111111";
		//mode de comm
		if($type_sigb==13)
		{
			$enreg1["comm_sigb"]=7;
			$enreg1["comm_params"]=serialize(array("url_serveur"=>$_REQUEST["service_nanook"]));
		}
		else
		{
			$enreg1["comm_sigb"]=1;
			$enreg1["comm_params"]=serialize(array("Autoriser_docs_disponibles"=>"0","Max_par_carte"=>"3","Max_par_document"=>"3"));
		}
		sqlInsert("int_bib", $enreg1);
		$nb_bibs++;
	}
	print('</table></div>');

	// annexes
	if($_REQUEST["creer_annexes"]==1)
	{
		sqlExecute("delete from codif_annexe");
		unset($enreg);
		$sites=@file($path_etalon."annexes.txt");
		if(!$sites) afficherErreur('Impossible de trouver le fichier : annexes.txt',true);

		print('<h3>'.++$phase.' - Création des annexes</h3>');
		print('<div class="liste"><table class="blank">');
		foreach($sites as $ligne)
		{
			$ligne=utf8_encode($ligne);
			$elem=explode("|",$ligne);
			if($elem[0]=="BIB_SPS_UTT") continue;
			$enreg["id_bib"]=$elem[0];
			$enreg["code"]=$elem[0];
			$enreg["libelle"]=$elem[1];
			$enreg["invisible"]=0;
			echo '<tr><td class="blank">Site n° '.$enreg["id_bib"].'</td><td class="blank">'.$enreg["libelle"].'</td></tr>';
			sqlInsert("codif_annexe", $enreg);
		}
		print('</table></div>');
	}

	// intégrations programmées
	unset($enreg);
	print('<h3>'.++$phase.' - Programmation des intégrations</h3>');
	print('<div class="liste"><table class="blank">');

	$path_fichier=$_REQUEST["path_ftp"]."/";
	for($i=1; $i<=$nb_bibs; $i++)
	{
		// supprimer anciens enregs
		$elem=explode("|",$sites[$i]);
		$id_bib=$elem[0];
		sqlExecute("delete from int_maj_auto where id_bib=$id_bib");

		// identifiants communs
		$id_prog=$id_bib*100;
		$enreg["id_bib"]=$id_bib;

		// suppression d'exemplaires
		if($type_sigb==13)
		{
			$enreg["id_prog"]=$id_prog;
			$enreg["libelle"]="Notices - suppression d'exemplaires";
			$enreg["profil"]=$id_profil_code_barre;
			$enreg["type_operation"]=1;
			$enreg["nom_fichier"]=$path_fichier."site".$id_bib."/suppressions.txt";
			$enreg["rang"]=$id_prog;
			sqlInsert("int_maj_auto", $enreg);
		}

		// notices total
		echo '<tr><td class="blank">Site n° '.$id_bib.'</td><td class="blank">Import total des notices</td></tr>';
		$id_prog++;
		$enreg["id_prog"]=$id_prog;
		$enreg["libelle"]="Notices - import total";
		if($type_sigb==13) $enreg["profil"]=104; // nanook
		else $enreg["profil"]=100; // pergame
		$enreg["type_operation"]=2;
		$enreg["nom_fichier"]=$path_fichier."site".$id_bib."/notices_total.txt";
		$enreg["rang"]=$id_prog;
		sqlInsert("int_maj_auto", $enreg);

		// notices incrémentiel
		echo '<tr><td class="blank">&nbsp;</td><td class="blank">Import incrémentiel des notices</td></tr>';
		$id_prog++;
		$enreg["id_prog"]=$id_prog;
		$enreg["libelle"]="Notices - import incrémentiel";
		if ($type_sigb==13) $enreg["profil"]=104; //Nanook
		else $enreg["profil"]=100; // pergame
		$enreg["type_operation"]=0;
		$enreg["nom_fichier"]=$path_fichier."site".$id_bib."/notices.txt";
		$enreg["rang"]=$id_prog;
		sqlInsert("int_maj_auto", $enreg);

		// abonnés
		echo '<tr><td class="blank">&nbsp;</td><td class="blank">Import des abonnés</td></tr>';
		$id_prog++;
		$enreg["id_prog"]=$id_prog;
		$enreg["libelle"]="Abonnés";
		if ($type_sigb==13) $enreg["profil"]=105; //Nanook
		else $enreg["profil"]=101; // pergame
		$enreg["type_operation"]=2;
		$enreg["nom_fichier"]=$path_fichier."site".$id_bib."/abonnes.txt";
		$enreg["rang"]=$id_prog;
		sqlInsert("int_maj_auto", $enreg);

		// prêts
		echo '<tr><td class="blank">&nbsp;</td><td class="blank">Import des prêts</td></tr>';
		$id_prog++;
		$enreg["id_prog"]=$id_prog;
		$enreg["libelle"]="Prêts";
		$enreg["profil"]=102;
		$enreg["type_operation"]=2;
		$enreg["nom_fichier"]=$path_fichier."site".$id_bib."/prets.txt";
		$enreg["rang"]=$id_prog;
		sqlInsert("int_maj_auto", $enreg);

		// réservations
		echo '<tr><td class="blank">&nbsp;</td><td class="blank">Import des réservations</td></tr>';
		$id_prog++;
		$enreg["id_prog"]=$id_prog;
		$enreg["libelle"]="Réservations";
		$enreg["profil"]=103;
		$enreg["type_operation"]=2;
		$enreg["nom_fichier"]=$path_fichier."site".$id_bib."/reservations.txt";
		$enreg["rang"]=$id_prog;
		sqlInsert("int_maj_auto", $enreg);
	}
	print('</table></div>');

	// Sections
	sqlExecute("truncate table codif_section");
	unset($enreg);
	print('<h3>'.++$phase.' - Création des sections</h3>');
	print('<div class="liste"><table class="blank">');

	$sections=@file($path_etalon."sections.txt");
	if(!$sections) afficherErreur('Impossible de trouver le fichier "sections.txt" dans le dossier : '.$path_etalon,true);
	foreach($sections as $ligne)
	{
		$ligne=utf8_encode($ligne);
		$elems=explode("|",$ligne);
		if(strlen(trim($elems[0])) != 1) continue;
		echo '<tr><td class="blank">'.$elems[0].'</td><td class="blank">'.$elems[1].'</td></tr>';
		$enreg["libelle"]=trim($elems[1]);
		if ($type_sigb==1) $enreg["regles"]="995\$q=".strtolower($elems[0]); //PERGAME
		else if ($type_sigb==13) $enreg["regles"]="995\$9=".strtolower($elems[0]);//NANOOK
		sqlInsert("codif_section", $enreg);
	}
	print('</table></div>');

// emplacements
	sqlExecute("truncate table codif_emplacement");
	unset($enreg);
	print('<h3>'.++$phase.' - Création des emplacements</h3>');
	print('<div class="liste"><table class="blank">');

	$emplacements=@file($path_etalon."emplacements.txt");
	if(!$emplacements) afficherErreur('Impossible de trouver le fichier "emplacements.txt" dans le dossier : '.$path_etalon,true);
	foreach($emplacements as $ligne)
	{
		$ligne=utf8_encode($ligne);
		$elems=explode("|",$ligne);
		if(!trim($elems[0]) or substr($elems[0],0,4)=="BIB_") continue;
		echo '<tr><td class="blank">'.$elems[0].'</td><td class="blank">'.$elems[1].'</td></tr>';
		$enreg["libelle"]=trim($elems[1]);
		$enreg["regles"]="995\$6=".$elems[0];
		sqlInsert("codif_emplacement", $enreg);
	}
	print('</table></div>');

	// genres
	sqlExecute("truncate table codif_genre");
	unset($enreg);
	print('<h3>'.++$phase.' - Création des genres</h3>');
	print('<div class="liste"><table class="blank">');

	$data=@file($path_etalon."genres.txt");
	if(!$data) afficherErreur('Impossible de trouver le fichier "genres.txt" dans le dossier : '.$path_etalon,true);

	foreach($data as $ligne)
	{
		$ligne=utf8_encode($ligne);
		$elems=explode("|",$ligne);
		if(!trim($elems[1]) or substr($elems[0],0,4)=="BIB_") continue;
		$clef=$ix->alphaMaj($elems[2]);
		$code=trim($elems[1]);
		if(strpos($genres[$clef]["codes"],$code) === false) $genres[$clef]["codes"].=";".$code;
		$genres[$clef]["libelle"]=trim($elems[2]);
	}

	foreach($genres as $genre)
	{
		echo '<tr><td class="blank">'.substr($genre["codes"],1).'</td><td class="blank">'.$genre["libelle"].'</td></tr>';
		$enreg["libelle"]=trim($genre["libelle"]);

		// Pergame
		if($type_sigb==1) $enreg["regles"]="930\$4=".substr($genre["codes"],1);

		// Nanook
		if($type_sigb==13) $enreg["regles"]="995\$7=".substr($genre["codes"],1);

		sqlInsert("codif_genre", $enreg);
	}
	print('</table></div>');

	// classes Dewey
	print('<h3>'.++$phase.' - Création des classes Dewey</h3>');
	print('<div class="liste"><table class="blank">');
	sqlExecute("delete from codif_dewey where id_dewey in(0,1,2,3,4,5,6,7,8,9)");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(0,'Généralités')");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(1,'Philosophie et disciplines connexes')");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(2,'Religion')");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(3,'Sciences sociales')");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(4,'Langues')");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(5,'Sciences de la nature et mathématiques')");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(6,'Technique (sciences appliquées)')");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(7,'Arts')");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(8,'Littérature (Belles-lettres)')");
	sqlExecute("insert into codif_dewey (id_dewey,libelle) values(9,'Histoire, géographie')");

	$dewey=fetchAll("select id_dewey, libelle from codif_dewey where LENGTH(id_dewey) = 1 order by 1");
	foreach($dewey as $ligne)
	{
		echo '<tr><td class="blank">'.$ligne["id_dewey"].'</td><td class="blank">'.$ligne["libelle"].'</td></tr>';
	}

	print('</table></div>');

	// Fini
	echo BR.'<h2>Traitement terminé.</h2>'.BR.BR;
}

function quit($msg)
{
	if($msg) print(BR.BR.'<h3 style="margin-left:30px">'.$msg.'</h3>');
	print('</body></html>');
	exit;
}
?>