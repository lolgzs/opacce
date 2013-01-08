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
// ----------------------------------------------------------------
// PSEUDO-NOTICES - cms rss sitotheque et albums (phase 0.1 a 0.6)
// ----------------------------------------------------------------

// ----------------------------------------------------------------
// CMS
// ----------------------------------------------------------------
if ($phase > 0 and $phase < 0.2)
{
	// init variables
	if ($phase == 0.1)
	{
		unset($phase_data);
		$phase_data["nombre"] = 0;
		$phase_data["timeStart"] = time();
		$phase_data["pointeur_reprise"] = 0;
		setVariable("traitement_phase", "Pseudo-notices : CMS");
		$log->ecrire("<h4>Traitement des pseudo-notices</h4>");
		$log->ecrire('<span class="violet">Notices CMS :</span>' . BR);
	}
	else print('<span class="violet">Notices CMS :</span>' . BR);

	// suppression des notices et des exemplaires
	$phase = 0.11;
	$items = fetchAll("select id_notice from notices where type_doc=8");
	if ($items)
	{
		foreach ($items as $item)
		{
			if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
			$id_notice = $item["id_notice"];
			sqlExecute("delete from exemplaires where id_notice=$id_notice");
			sqlExecute("delete from notices where id_notice=$id_notice");
		}
	}
	$phase = 0.2;
}
if ($phase == 0.2)
{
	if ($phase_data["nombre"] and !$mode_cron) print('<span class="violet">Notices CMS :</span>' . BR);
	if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
	$chrono->start();
	$result = $sql->prepareListe("select * from cms_article where ID_ARTICLE >" . $phase_data["pointeur_reprise"] . " and INDEXATION=1 order by ID_ARTICLE");
	if ($result)
	{
		while ($enreg = $sql->fetchNext($result))
		{
			if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
			$enreg["id_bib"] = $sql->fetchOne("select ID_SITE from cms_categorie where ID_CAT=" . $enreg["ID_CAT"]);
			$phase_data["nombre"]++;
			$ret = $notice->traitePseudoNotice(8, $enreg);
			tracePseudoNotice($ret, $enreg);
			$phase_data["pointeur_reprise"] = $enreg["ID_ARTICLE"];
		}
	}
	traceRecapPseudoNotices($phase_data);
	$phase = 0.2;
}

// ----------------------------------------------------------------
// FILS RSS
// ----------------------------------------------------------------
if ($phase < 0.3)
{
	// init variables
	if ($phase == 0.2)
	{
		unset($phase_data);
		$phase_data["nombre"] = 0;
		$phase_data["timeStart"] = time();
		$phase_data["pointeur_reprise"] = 0;
		setVariable("traitement_phase", "Pseudo-notices : FILS RSS :");
		$log->ecrire('<span class="violet">Notices FILS RSS :</span>' . BR);
	}
	else print('<span class="violet">Notices FILS RSS :</span>' . BR);

	// suppression des notices et des exemplaires
	$phase = 0.21;
	$items = fetchAll("select id_notice from notices where type_doc=9");
	if ($items)
	{
		foreach ($items as $item)
		{
			if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
			$id_notice = $item["id_notice"];
			sqlExecute("delete from exemplaires where id_notice=$id_notice");
			sqlExecute("delete from notices where id_notice=$id_notice");
		}
	}
	$phase = 0.3;
}

if ($phase == 0.3)
{
	if ($phase_data["nombre"] and !$mode_cron) print('<span class="violet">Notices FILS RSS :</span>' . BR);
	if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
	$chrono->start();
	$result = $sql->prepareListe("select * from rss_flux where ID_RSS >" . $phase_data["pointeur_reprise"] . " order by ID_RSS");
	if ($result)
	{
		while ($enreg = $sql->fetchNext($result))
		{
			if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
			$enreg["id_bib"] = $sql->fetchOne("select ID_SITE from rss_categorie where ID_CAT=" . $enreg["ID_CAT"]);
			$phase_data["nombre"]++;
			$ret = $notice->traitePseudoNotice(9, $enreg);
			tracePseudoNotice($ret, $enreg);
			$phase_data["pointeur_reprise"] = $enreg["ID_RSS"];
		}
	}
	traceRecapPseudoNotices($phase_data);
	$phase = 0.4;
}

// ----------------------------------------------------------------
// SITHOTHEQUE
// ----------------------------------------------------------------
if ($phase < 0.6)
{
	// init variables
	if ($phase == 0.4)
	{
		unset($phase_data);
		$phase_data["nombre"] = 0;
		$phase_data["timeStart"] = time();
		$phase_data["pointeur_reprise"] = 0;
		setVariable("traitement_phase", "Pseudo-notices : SITOTHEQUE :");
		$log->ecrire('<span class="violet">Notices SITOTHEQUE :</span>' . BR);
	}
	else print('<span class="violet">Notices SITOTHEQUE :</span>' . BR);

	// suppression des notices et des exemplaires
	$phase = 0.41;
	$items = fetchAll("select id_notice from notices where type_doc=10");
	if ($items)
	{
		foreach ($items as $item)
		{
			if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
			$id_notice = $item["id_notice"];
			sqlExecute("delete from exemplaires where id_notice=$id_notice");
			sqlExecute("delete from notices where id_notice=$id_notice");
		}
	}
	$phase = 0.5;
}

if ($phase == 0.5)
{
	if ($phase_data["nombre"] and !$mode_cron) print('<span class="violet">Notices SITOTHEQUE :</span>' . BR);
	if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
	$chrono->start();
	$result = $sql->prepareListe("select * from sito_url where ID_SITO >" . $phase_data["pointeur_reprise"] . " order by ID_SITO");
	if ($result)
	{
		while ($enreg = $sql->fetchNext($result))
		{
			if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
			$enreg["id_bib"] = $sql->fetchOne("select ID_SITE from sito_categorie where ID_CAT=" . $enreg["ID_CAT"]);
			$phase_data["nombre"]++;
			$ret = $notice->traitePseudoNotice(10, $enreg);
			tracePseudoNotice($ret, $enreg);
			$phase_data["pointeur_reprise"] = $enreg["ID_SITO"];
		}
	}
	traceRecapPseudoNotices($phase_data);
	$phase = 0.6;
}

// ----------------------------------------------------------------
// ALBUMS
// ----------------------------------------------------------------
if ($phase < 0.7)
{
	// init variables
	if ($phase == 0.6)
	{
		unset($phase_data);
		$phase_data["nombre"] = 0;
		$phase_data["timeStart"] = time();
		$phase_data["pointeur_reprise"] = 0;
		setVariable("traitement_phase", "Pseudo-notices : RESSOURCES NUMERIQUES :");
		$log->ecrire('<span class="violet">Notices RESSOURCES NUMERIQUES :</span>' . BR);
	}
	else print('<span class="violet">Notices RESSOURCES NUMERIQUES : suppressions</span>' . BR);

	// suppression des notices et des exemplaires
	$phase = 0.61;
	$items = fetchAll("select id_notice from notices where type_doc>99");
	if ($items)
	{
		foreach ($items as $item)
		{
			if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
			$id_notice = $item["id_notice"];
			sqlExecute("delete from exemplaires where id_notice=$id_notice");
			sqlExecute("delete from notices where id_notice=$id_notice");
		}
	}
	$phase = 0.7;
}

if ($phase == 0.7)
{
	if ($phase_data["nombre"] and !$mode_cron) print('<span class="violet">Notices RESSOURCES NUMERIQUES : ajouts</span>' . BR);
	if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
	$chrono->start();
	$result = $sql->prepareListe("select * from album where id >" . $phase_data["pointeur_reprise"] . " and visible=true order by id");
	if ($result)
	{
		while ($enreg = $sql->fetchNext($result))
		{
			if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
			$enreg["id_bib"] = $sql->fetchOne("select site_id from album_categorie where id=" . $enreg["cat_id"]);
			$phase_data["nombre"]++;
			if(!$enreg["type_doc_id"]) $enreg["type_doc_id"]=100;
			$ret = $notice->traitePseudoNotice($enreg["type_doc_id"], $enreg);
			tracePseudoNotice($ret, $enreg);
			$phase_data["pointeur_reprise"] = $enreg["id"];
		}
	}
	traceRecapPseudoNotices($phase_data);
	$phase = 1;
}

// ----------------------------------------------------------------
// Affichage detail pour les pseudo-notices
// ----------------------------------------------------------------
function tracePseudoNotice($ret, $enreg)
{
	global $debug_level, $phase_data, $log, $compteur;

	// compteurs
	if ($ret["statut"] > 0) $compteur[$ret["statut"]]++;
	$phase_data["compteur"][$ret["statut"]]++;
	if ($debug_level == 0) return;

	// Traces
	if ($ret["id_notice"]) $log->ecrire('<a class="notice" href="' . URL_BASE . "php/analyse_afficher_notice_full.php?id_notice=" . $ret["id_notice"] . '">Notice n° ' . $ret["id_notice"] . '</a>' . BR);
	$log->ecrire('<table class="blank" cellspacing="0" cellpadding="5px" style="margin-left:15px;margin-bottom:10px">');
	$log->ecrire('<tr><td class="blank">Titre</td><td class="blank">' . $enreg["TITRE"] . '</td></tr>');
	$log->ecrire('<tr><td class="blank">Tags</td><td class="blank">' . $enreg["TAGS"] . '</td></tr>');
	$log->ecrire('<tr><td class="blank">Code-barres</td><td class="blank">' . $ret["code_barres"] . '</td></tr>');
	$log->ecrire('<tr><td class="blank">Unimarc</td><td class="blank">' . $ret["unimarc"] . '</td></tr>');
	$log->ecrire('</table>');
}

// ----------------------------------------------------------------
// Recap pour les pseudo-notices
// ----------------------------------------------------------------
function traceRecapPseudoNotices($phase_data)
{
	global $log, $chrono;

	if ($phase_data["nombre"] == 0)
	{
		$log->ecrire('<span class="vert">Aucune notice à traiter</span><br>');
		return;
	} else
	{
		$log->ecrire('<span class="vert">' . $phase_data["nombre"] . ' notices(s) traitée(s)</span>' . BR);
		$chrono->timeStart = $phase_data["timeStart"];
		$log->ecrire('<span class="vert">Temps de traitement : ' . $chrono->end() . " (" . $chrono->moyenne($phase_data["nombre"], "notices") . ")</span>" . BR.BR);
	}
}

?>
