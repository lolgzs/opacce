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
/////////////////////////////////////////////////////////////////////////
// INTEGRATION DES RESERVATIONS
/////////////////////////////////////////////////////////////////////////
require_once("classe_transaction.php");
$transac=new transaction();
$bib=new bibliotheque();

setVariable("traitement_phase","Intégration des fichiers de réservations");
if($phase==12)
{
	$log->ecrire("<h4>Intégration des fichiers de réservations</h4>");
	unset($phase_data);
	$reprise=false;
	$phase_data["nombre"]=0;
	$phase_data["nb_fic"]=0;
	$phase_data["timeStart"]=time();
	$phase_data["pointeur"]="";
	$phase = 13;
}
if($phase == 13)
{
	if(!$mode_cron and $phase_data["nombre"]>0) print("<h4>Intégration des fichiers de réservations</h4>");
	$resultat=$sql->prepareListe("select * from integrations Where traite='non' Order by id");
	while($ligne=$sql->fetchNext($resultat))
	{
		extract($ligne);
		$format=$sql->fetchOne("select format from profil_donnees where id_profil=$profil");
		$type_fichier=$sql->fetchOne("select type_fichier from profil_donnees where id_profil=$profil");
		if($type_fichier != 3 ) continue; // Si autre que réservations on passe
		$nom_bib=$bib->getNomCourt($id_bib);
		$libelle_type_operation=getLibCodifVariable("import_type_operation",$type_operation);
		$trace='<b><span class="vert">'.$nom_bib." (".$libelle_type_operation.")</b></span><br>";
		$trace.='<span class="bib">Fichier : '.$fichier."</span><br>";
		$trace.='<span class="bib">Profil : '.$sql->fetchOne("select libelle from profil_donnees where id_profil=$profil").'</span><br>';
		$trace.='<span class="bib">Format : '.getLibCodifVariable("import_format",$format).'</span><br>';
		if(!$pointeur_reprise) $log->ecrire($trace); else print($trace.BR);

		// Delete si export total
		if($type_operation == 2 and !$phase_data["pointeur"])
		{
			$sql->execute("delete from reservations where ID_SITE=$id_bib");
		}
	
		// Traitement d'un fichier
		$transac->setParamsIntegration($id_bib,$type_operation,$profil);
		if($reprise) $reprise=false;
		else
		{
			unset($phase_data);
			$phase_data["nb_erreurs"]=0;
			$phase_data["nb_warnings"]=0;
			$phase_data["nombre"]=0;
			$chrono100notices->start();
			$chrono_fichier->start();
		}
		if( false == $parseur->open( $integration_path . $fichier,$format,$phase_data["pointeur"]) )
		{
			incrementeVariable("traitement_erreurs");
			$log->ecrire('<span class="rouge">Impossible d\'ouvrir le fichier : '. $fichier .'</span><br>');
			continue;
		}

		// Parser les enregs
		while(true)
		{
			if(!$mode_cron and $chrono->tempsPasse() > $timeout) sauveContexte();
			$ret=$parseur->nextEnreg();
			if($ret["statut"]=="erreur")
			{
				incrementeVariable("traitement_erreurs");
				$log->ecrire('<span class="rouge">'.$ret["erreur"].'</span><br>');
				if($phase_data["nombre"] > 0) $msg= $phase_data["nombre"] . " fiches ont pu être traitées."; else $msg="aucune fiche n'a pu être traitée.";
				$log->ecrire('<span class="vert">'.$msg.'</span>');
				break;
			}
			if($ret["statut"]=="eof")
			{
				if($phase_data["nombre"] == 0 ) $msg="Le fichier ne contenait aucune fiche";
				else
				{
					$log->ecrire(BR.'<span class="vert">'.$phase_data["nombre"].' fiches ont été traitées.</span>');
					$msg="temps de traitement ".$chrono_fichier->end($phase_data["timeStart"])." (".$chrono_fichier->moyenne($phase_data["nombre"],"fiches").")";
				}
				$log->ecrire(BR.'<span class="vert">'.$msg.'</span><br>');
				break;
			}
			if($ret["statut"]=="ok")
			{
				if(substr($ret["data"],0,13)=='BIB_T_RESERVE') continue; // Entete pergame
				$transac->importFicheReservation($ret["data"]);
				$phase_data["nombre"]++;
				traceTraitementTransaction();
				$ptr=$ret["pointeur_reprise"];
				$phase_data["pointeur"]=$ptr;
				$sql->execute("Update integrations set pointeur_reprise = $ptr Where id = ". $ligne["id"]);
			}
		}

		// Maj base
		$date=dateDuJour(0);
		$sql->execute("update integrations Set traite='$date', pointeur_reprise=".$phase_data["nombre"]." Where id = " .$ligne["id"]);
		$phase_data["nb_fic"]++;
	}
	$phase_data["pointeur"]=0;
	if($phase_data["nb_fic"]> 0) $msg=$phase_data["nb_fic"]. " fichier(s) traité(s).";
	else $msg="aucun fichier traité";
	$log->ecrire(BR.'<span class="violet">'.$msg.'</span><br>');
}

?>
