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
// INTEGRATION DES ABONNES
/////////////////////////////////////////////////////////////////////////
require_once("classe_abonne.php");
$abon=new abonne();
$bib=new bibliotheque();

setVariable("traitement_phase","Intégration des fichiers d'abonnés");
if($phase==8)
{
	$log->ecrire("<h4>Intégration des fichiers d'abonnés</h4>");
	unset($phase_data);
	$reprise=false;
	$phase_data["nombre"]=0;
	$phase_data["nb_fic"]=0;
	$phase_data["timeStart"]=time();
	$phase_data["pointeur"]="";
	$phase = 9;
}
if($phase == 9)
{
	if(!$mode_cron and $phase_data["nombre"]>0) print("<h4>Intégration des fichiers d'abonnés</h4>");
	$resultat=$sql->prepareListe("select * from integrations Where traite='non' Order by id");
	while($ligne=$sql->fetchNext($resultat))
	{
		extract($ligne);
		$format=$sql->fetchOne("select format from profil_donnees where id_profil=$profil");
		$type_fichier=$sql->fetchOne("select type_fichier from profil_donnees where id_profil=$profil");
		if($type_fichier != 1 ) continue; // Si autre que abonnés on passe
		$nom_bib=$bib->getNomCourt($id_bib);
		$libelle_type_operation=getLibCodifVariable("import_type_operation",$type_operation);
		$trace='<b><span class="vert">'.$nom_bib." (".$libelle_type_operation.")</b></span><br>";
		$trace.='<span class="bib">Fichier : '.$fichier."</span><br>";
		$trace.='<span class="bib">Profil : '.$sql->fetchOne("select libelle from profil_donnees where id_profil=$profil").'</span><br>';
		$trace.='<span class="bib">Format : '.getLibCodifVariable("import_format",$format).'</span><br>';
		if(!$phase_data["pointeur"]) $log->ecrire($trace); else print($trace.BR);

		// Flag statut a 1 abonnés si export total
		if($type_operation == 2 and !$phase_data["pointeur"])
		{
			$sql->execute("update bib_admin_users set STATUT=1");
		}
	
		// Traitement d'un fichier
		$abon->setParamsIntegration($id_bib,$type_operation,$profil);
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
		if( false == $parseur->open( $integration_path . $fichier,$format,$phase_data["pointeur"],$profil) )
		{
			incrementeVariable("traitement_erreurs");
			$log->ecrire('<span class="rouge">'.$parseur->getLastError().'</span><br>');
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
				if(substr($ret["data"],0,14)=='BIB_ABON_CARTE') continue; // Entete pergame
				if(substr($ret["data"],0,10)=='nom,prenom') continue; // Entete microbib
				$abon->importFiche($ret["data"],$format);
				$phase_data["nombre"]++;
				traceTraitementAbonne();
				$ptr=$ret["pointeur_reprise"];
				$phase_data["pointeur"]=$ptr;
				$sql->execute("Update integrations set pointeur_reprise = $ptr Where id = ". $ligne["id"]);
			}
		}

		// Suppression des abonnés non flagués en cas d'import total
		if($type_operation == 2)
		{
			$log->ecrire(BR.'<span class="violet">Supression des fiches (import total)</span><br>');
			$nb=$sql->execute("delete from bib_admin_users where ID_SITE=$id_bib and IDABON > '' and STATUT=1");
			$log->ecrire('<span class="vert">'.$nb.' fiches supprimées</span>'.BR.BR);
			$id_max=$sql->fetchOne("select max(ID_USER) from bib_admin_users")+1;
			$sql->execute("ALTER TABLE `bib_admin_users` AUTO_INCREMENT =".$id_max);
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

// ----------------------------------------------------------------
// Ecriture logs et affichage écran
// ----------------------------------------------------------------
function traceTraitementAbonne()
{
	global $log,$phase_data,$chrono100notices;

	// Maj des compteurs
	$compteur[$code_statut]++;
  $compteur["nb_notices"]++;

  // Affichage tout les 1000 abonnés
  if ($phase_data["nombre"] % 1000 == 0)
  {
  	$log->ecrire("fiche ".$phase_data["nombre"]." (" .$chrono100notices->tempsPasse()." secondes)<br>");
    $chrono100notices->start();
  }

}
?>
