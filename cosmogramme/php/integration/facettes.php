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
// CALCUL DES FACETTES
/////////////////////////////////////////////////////////////////////////

setVariable("traitement_phase", "Mise à jour des facettes exemplaires");
if ($phase == 7)
{
	$log->ecrire("<h4>Mise à jour des facettes exemplaires</h4>");
	unset($phase_data);
	$phase_data["nombre"] = 0;
	$phase_data["timeStart"] = time();
	$phase_data["pointeur_notice"]=0;
	$phase_data["pointeur"]=getVariable('date_maj_facettes');
	if(!$phase_data["pointeur"]) $phase_data["pointeur"]='0000-00-00 00:00:00';
	$chrono100notices->start();
	$phase = 7.1;
}

// Lancer requete
if ($phase == 7.1)
{
	if(!$mode_cron and $phase_data["pointeur_notice"] > 0) print("<h4>Mise à jour des facettes exemplaires</h4>");
	while(true)
	{
		//$resultat = fetchAll("select id_notice,type_doc,facettes from notices where id_notice > " . $phase_data["pointeur_notice"] . " Order by id_notice limit 0,20000");
		$resultat = fetchAll("select id_notice,type_doc,facettes,date_maj from notices
								where id_notice > ".$phase_data["pointeur_notice"]."
								and date_maj >='" . $phase_data["pointeur"] . "'
								Order by id_notice limit 0,10000");

		if(!$resultat) break;
		foreach ($resultat as $ligne)
		{
			if (!$mode_cron and $chrono->tempsPasse() > $timeout) sauveContexte();
			$id_notice = $ligne["id_notice"];
			$last_date_maj=$ligne["date_maj"];
			$facette = explode(" ", $ligne["facettes"]);
			$facettes = "";
			for ($i = 0; $i < count($facette); $i++)
			{
				if (!$facette[$i]) continue;
				$type = substr($facette[$i], 0, 1);
				if ($type != "B" and $type != "G" and $type != "E" and $type != "S" and $type != "Y" and $type != "T" and $type > "2")
				{
					$facettes.=" " . $facette[$i];
				}
			}

			// facette type de document
			$facettes.=" T" . $ligne["type_doc"];

			// facette bibliotheque
			$bibs = $sql->fetchAll("select distinct(id_bib) from exemplaires where id_notice=$id_notice");
			if (count($bibs))
			{
				foreach ($bibs as $enreg)
				{
					$bib = " B" . $enreg["id_bib"];
					$facettes.= $bib;
				}
			}

			// facette genre
			$genres = $sql->fetchAll("select distinct(genre) from exemplaires where id_notice=$id_notice");
			if (count($genres))
			{
				foreach ($genres as $enreg)
				{
					if (!trim($enreg["genre"])) continue;
					$genre = " G" . $enreg["genre"];
					$facettes.= $genre;
				}
			}

			// facette section
			$sections = $sql->fetchAll("select distinct(section) from exemplaires where id_notice=$id_notice");
			if (count($sections))
			{
				foreach ($sections as $enreg)
				{
					if (!trim($enreg["section"])) continue;
					$section = " S" . $enreg["section"];
					$facettes.= $section;
				}
			}

			// facette emplacement
			$emplacements = $sql->fetchAll("select distinct(emplacement) from exemplaires where id_notice=$id_notice");
			if (count($emplacements))
			{
				foreach ($emplacements as $enreg)
				{
					if (!trim($enreg["emplacement"])) continue;
					$emplacement = " E" . $enreg["emplacement"];
					$facettes.= $emplacement;
				}
			}

			// facette annexe
			$annexes = $sql->fetchAll("select distinct(annexe) from exemplaires where id_notice=$id_notice");
			if (count($annexes))
			{
				foreach ($annexes as $enreg)
				{
					if (!trim($enreg["annexe"])) continue;
					$annexe = " Y" . $enreg["annexe"];
					$facettes.= $annexe;
				}
			}

			// date de nouveauté
			$date_nouveaute = "";
			$dates = fetchAll("select date_nouveaute from exemplaires where id_notice=$id_notice");
			if (count($dates))
			{
				foreach ($dates as $item)
				{
					$date = $item["date_nouveaute"];
					if ($date > $date_nouveaute) $date_nouveaute = $date;
				}
			}
			if ($date_nouveaute)
			{
				if(substr($date, 0, 1) == "2") $facettes.=" " . substr($date_nouveaute, 0, 7);
				$date_nouveaute.=" 00:00:00";
			}
			else $date_nouveaute=null;

			// Ecrire
			$sql->execute("update notices set facettes='$facettes',date_creation='$date_nouveaute' where id_notice=$id_notice");
			$phase_data["pointeur_notice"] = $id_notice;
			$phase_data["nombre"]++;
			if ($phase_data["nombre"] % 5000 == 0) { print($phase_data["nombre"] . BR); flush(); }
		}
	}
	setVariable('date_maj_facettes', $last_date_maj);
	$log->ecrire('<span class="vert"> '.$phase_data['nombre'].' notices traitées.</span>').BR;
	$log->ecrire('<span class="vert">Temps de traitement : ' . $chrono100notices->end() . " (" . $chrono100notices->moyenne($phase_data["nombre"], "notices") . ")</span>" . BR);
	$phase = 8;
}
?>
