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
// INDEXATION DES PERIODIQUES
/////////////////////////////////////////////////////////////////////////

require_once("classe_notice_integration.php");
require_once("classe_indexation.php");
$notice=new notice_integration();
$ix = new indexation();
$notice->setParamsIntegration(0,0,1,"");

$titre="Indexation des articles de périodiques";
setVariable("traitement_phase",$titre);
if($phase=="PERIODIQUES_0")
{
	$log->ecrire("<h4>$titre</h4>");
	$chrono100notices->start();
	unset($phase_data);
	$phase_data["nombre"]=0;
	$phase_data["not_found"]=0;
	$phase_data["timeStart"]=time();
	$phase_data["pointeur_article"]="0";
	$phase = "PERIODIQUES_1";
}

if($phase == "PERIODIQUES_1")
{
	$start_periodiques=date("Y-m-d H:i:s",$phase_data["timeStart"]);
	if(!$mode_cron and $phase_data["nombre"]>0) print("<h4>$titre</h4>");
	$req="select * from notices_articles"
		." Where id_article>".	$phase_data["pointeur_article"]
		." and date_maj >='".date("Y-m-d",$timeStart)."'"
		." and clef_chapeau > ''"
		." and unimarc > ''"
		." order by id_article";
	$resultat=$sql->prepareListe($req);

	// Parser les articles
	while($enreg=$sql->fetchNext($resultat))
	{
		if(!$mode_cron and $chrono->tempsPasse() > $timeout) sauveContexte();

		// données article
		$data=$notice->getDataArticlePeriodique($enreg["unimarc"]);

		// lire la notice du numero
		$numero=$sql->fetchEnreg("select id_notice,facettes,titres,auteurs,matieres,date_maj from notices where clef_chapeau='".$enreg["clef_chapeau"]."' and tome_alpha='".$enreg["clef_numero"]."'");
		if($numero["id_notice"])
		{
			$new_enreg=array();
			$facettes=$numero["facettes"];

			// reset facettes
			if($numero["date_maj"]<$start_periodiques)
			{
				$facettes="";
				$controle=explode(" ",$numero["facettes"]);
				for($i=0; $i < count($controle); $i++)
				{
					$tp=substr($controle[$i],0,1);
					if($tp !="A" and $tp!="M") $facettes.=" ".$controle[$i];
				}
			}

			// Facettes Auteurs
			if($data["auteurs"])
			{
				foreach($data["auteurs"] as $auteur)
				{
					$code_alpha=$ix->alphaMaj($auteur);
					$code_alpha=str_replace(" ","x",$code_alpha);
					if(!$code_alpha) continue;
					$enreg_auteur=$sql->fetchEnreg("Select * from codif_auteur where MATCH(formes) AGAINST('\"".$code_alpha."\"' IN BOOLEAN MODE) ");
					if(!$enreg_auteur["id_auteur"])
					{
						$pos=strscan($auteur,"|");
						$nom_prenom = trim(substr($auteur,($pos+1))." ".substr($auteur,0,$pos));
						$id_auteur=$sql->insert("codif_auteur",array("libelle" => $nom_prenom,"formes" => $code_alpha));
					}
					else $id_auteur=$enreg_auteur["id_auteur"];
					$facette=" A".$id_auteur;
					if(strpos($facettes." ",$facette." ")===false) $facettes.=$facette;
				}
			}

			// Facettes Matieres
			if($data["matieres"])
			{
				foreach($data["matieres"] as $matiere)
				{
					$code_alpha=$ix->alphaMaj($matiere);
					if(!$code_alpha) continue;
					$enreg_matiere=$sql->fetchEnreg("Select * from codif_matiere where code_alpha='$code_alpha'");
					if(!$enreg_matiere["id_matiere"]) $id_matiere=$sql->insert("codif_matiere",array("libelle" => $matiere,"code_alpha" => $code_alpha));
					else $id_matiere=$enreg_matiere["id_matiere"];
					$facette=" M".$id_matiere;
					if(strpos($facettes." ",$facette." ")===false) $facettes.=$facette;
				}
			}

			// index fulltext
			$data["titres"][]=$numero["titres"];
			$new_enreg["titres"]=$ix->getFulltext($data["titres"]);

			$data["auteurs"][]=$numero["auteurs"];
			$new_enreg["auteurs"]=$ix->getFulltext($data["auteurs"]);

			$data["matieres"][]=$numero["matieres"];
			$new_enreg["matieres"]=$ix->getFulltext($data["matieres"]);

			// maj enreg
			$new_enreg["date_maj"]=$start_periodiques;
			$new_enreg["facettes"]=trim($facettes);
			$req="update notices set @SET@ where id_notice=".$numero["id_notice"];
			$sql->update($req,$new_enreg);
		}
		else
		{
			$phase_data["not_found"]++;
		}

		// Pointeur d'avancement et trace
		$phase_data["pointeur_article"]=$enreg["id_article"];
		$phase_data["nombre"]++;
		traceTraitementPeriodique();
	}

	// recap
	if($phase_data["nombre"]==0) $log->ecrire(BR.'<span class="vert">Aucun article à traiter</span><br>');
	else
	{
		$log->ecrire(BR.'<span class="vert">'.$phase_data["nombre"].' articles(s) traité(s)</span>'.BR);
		$chrono->timeStart=$phase_data["timeStart"];
		$log->ecrire('<span class="vert">Temps de traitement : '.$chrono->end()." (".$chrono->moyenne($phase_data["nombre"],"articles").")</span>".BR);
	}
	if($phase_data["not_found"]) $log->ecrire('<span class="violet">Notices orphelines : '.$phase_data["not_found"].'</span>'.BR);
}

// ----------------------------------------------------------------
// Ecriture logs et affichage écran
// ----------------------------------------------------------------
function traceTraitementPeriodique()
{
	global $log,$phase_data,$chrono100notices;

	// Affichage toutes les 100
	if($phase_data["nombre"] % 100 == 0)
	{
		$log->ecrire("article ".$phase_data["nombre"]." (" .$chrono100notices->tempsPasse()." secondes)<br>");
		$chrono100notices->start();
	}

}
?>
