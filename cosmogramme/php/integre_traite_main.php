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
// TRAITEMENT D'INTEGRATION AUTOMATIQUE (BOUCLE DE TRAITEMENT GLOBALE)
/////////////////////////////////////////////////////////////////////////

error_reporting(E_ERROR | E_PARSE);

// Arguments de la commande en mode cron
if ($argc == 3)
{
	$user = $argv[1];
	$passe = $argv[2];
	$mode_cron = true;
}
include("_init_frame.php");

// Includes
if ($mode_cron == true) setVariable("clef_traitements", "0");	// en mode cron : on debloque la base
require_once("classe_chronometre.php");
require_once("classe_parseur.php");
require_once("classe_log.php");
require_once("classe_notice_integration.php");
require_once("classe_maj_auto.php");
require_once("classe_bib.php");

// Instanciations
$log = new Class_log("integration");
$log_erreur = new Class_log("erreur", false);
$log_warning = new Class_log("warning", false);
$chrono = new chronometre();
$chrono_fichier = new chronometre();
$chrono100notices = new chronometre();
$parseur = new parseur();
$notice = new notice_integration();
$bib = new bibliotheque();

// Initialisations
$integration_path = getVariable("integration_path");
$debug_level = getVariable("debug_level");
$nb_afi_retry = getVariable("Z3950_retry_level");
$compteur = array();
$date = dateDuJour(0);
$nb_notices = 0;
$timeout = intval(ini_get("max_execution_time") * 0.75);
if (!$timeout) $timeout = 25; // Pour le debugger
$timeStart = time();
$chrono->start();
$phase = "0";
$phase_data = array();

// ----------------------------------------------------------------
// Début du traitement
// ----------------------------------------------------------------
if ($_REQUEST["reprise"] == "oui")
{
	$log->open(true);
	$log_erreur->open(true);
	$log_warning->open(true);
	restaureContext();
	$reprise = true;
}
else
{
	// init variables
	setVariable("traitement_erreurs", 0);
	setVariable("traitement_warnings", 0);
	setVariable("traitement_phase", "Déplacement des fichiers");
	$log->open(false);
	$log_erreur->open(false);
	$log_warning->open(false);
	$log->ecrire('<h4>Début du traitement</h4>');
	$log->ecrire("Date : " . date("d-m-Y") . BR);
	$log->ecrire("Heure : " . date("G:i:s") . BR);
	if ($mode_cron == true) $log->ecrire("Mode : automatique (cron)" . BR); else $log->ecrire("Mode : manuel" . BR);

	// controle du trigger de maj des notices
	@sqlExecute('DROP TRIGGER datemaj_notices_update');
	@sqlExecute("
				CREATE TRIGGER datemaj_notices_update BEFORE DELETE
				ON exemplaires
				FOR EACH ROW
				BEGIN
				update notices  set date_maj=NOW() where id_notice=OLD.id_notice;
				END
			");

	// ----------------------------------------------------------------
	// Déplacement des fichiers dans Upload
	// ----------------------------------------------------------------
	$log->ecrire("<h4>Déplacement des fichiers à intégrer</h4>");
	$transfert = new maj_auto();
	$erreur = $transfert->transfertfichiersFtp();
	if ($erreur)
	{
		$log->ecrire(BR . BR . '<span class="rouge">' . $erreur . ' : Abandon du traitement !</span><br>');
		exit;
	}
	setVariable("integration_date", $date);
	$transfert->supprimerEntetesPergame();

	// ----------------------------------------------------------------
	// Test blocage de la base
	// ----------------------------------------------------------------
	$log->ecrire("<h4>Traitement des notices</h4>");
	if (getVariable("clef_traitements") == "1")
	{
		$log->ecrire('<span class="rouge">La base est bloquée (clef_traitements) : Abandon du traitement !</span><br>');
		$log->close();
		exit;
	}
	$log->ecrire("Blocage de la base<br>");
	setVariable("clef_traitements", "1");
}

// ----------------------------------------------------------------
// Integration des notices (PHASE 0)
// ----------------------------------------------------------------
setVariable("traitement_phase", "Intégration des notices");
$resultat = $sql->prepareListe("select * from integrations Where traite='non' Order by id");
while ($ligne = $sql->fetchNext($resultat))
{
	extract($ligne);
	$format = $sql->fetchOne("select format from profil_donnees where id_profil=$profil");
	$type_fichier = $sql->fetchOne("select type_fichier from profil_donnees where id_profil=$profil");

	// Si autre que notices on passe
	if ($type_fichier > 0 and $type_fichier < 10) continue;
	$nom_bib = $bib->getNomCourt($id_bib);
	$libelle_type_operation = getLibCodifVariable("import_type_operation", $type_operation);
	$trace = '<br><b><span class="vert">' . $nom_bib . " (" . $libelle_type_operation . ")</b></span><br>";
	$trace.='<span class="bib">Fichier : ' . $fichier . "</span><br>";
	$trace.='<span class="bib">Profil : ' . $sql->fetchOne("select libelle from profil_donnees where id_profil=$profil") . '</span><br>';
	$trace.='<span class="bib">Format : ' . getLibCodifVariable("import_format", $format) . '</span><br>';
	if ($type_doc > '') $trace.='<span class="bib">Type de doc. forcé : ' . $type_doc . '</span><br>';
	if (!$pointeur_reprise) $log->ecrire($trace); else print($trace);

	// Suppression des exemplaires si export total
	if ($type_operation == 2 and !$reprise and !$pointeur_reprise)
	{
		$log->ecrire(BR . '<span class="violet">Supression des exemplaires</span><br>');
		if(filesize($integration_path . $fichier)>0)
		{
			$nb = $sql->execute("delete from exemplaires where id_bib=$id_bib");
			$nb1 = $sql->execute("delete from notices_succintes where id_bib=$id_bib");
			$log->ecrire('<span class="vert">' . $nb . ' exemplaires supprimés</span>' . BR);
			$log->ecrire('<span class="vert">' . $nb1 . ' notices succintes supprimées</span>' . BR . BR);
		}
		else $log->ecrire('<span class="rouge">Le fichier d\'import total est vide : aucun exemplaire supprimé.</span>' . BR . BR);
	}

	// Traitement d'un fichier
	$notice->setParamsIntegration($id_bib, $type_operation, $profil, $type_doc);
	if ($reprise) $reprise = false;
	else
	{
		unset($phase_data);
		$phase_data["nb_erreurs"] = 0;
		$phase_data["nb_warnings"] = 0;
		$nb_notices = 0;
		$chrono100notices->start();
		$chrono_fichier->start();
	}
	if (false == $parseur->open($integration_path . $fichier, $format, $pointeur_reprise))
	{
		incrementeVariable("traitement_erreurs");
		$log->ecrire('<span class="rouge">Impossible d\'ouvrir le fichier : ' . $fichier . '</span><br>');
		continue;
	}

	// Parser les enregs
	while (true)
	{
		if (!$mode_cron and $chrono->tempsPasse() > $timeout) sauveContexte();
		$ret = $parseur->nextEnreg();
		if ($ret["statut"] == "erreur")
		{
			incrementeVariable("traitement_erreurs");
			$log->ecrire('<span class="rouge">' . $ret["erreur"] . '</span><br>');
			$msg = "Unimarc incorrect.";
			$log->ecrire('<span class="vert">' . $msg . '</span>');
			continue;
		}

		// fin de fichier
		if ($ret["statut"] == "eof")
		{
			if ($nb_notices == 0) $msg = "Le fichier ne contenait aucune notice";
			else
			{
				$log->ecrire(BR . '<span class="vert">' . $nb_notices . ' notices ont été traitées.</span>');
				$msg = "temps de traitement " . $chrono_fichier->end() . " (" . $chrono_fichier->moyenne($nb_notices, "notices") . ")";
			}
			$log->ecrire(BR . '<span class="vert">' . $msg . '</span><br>');
			break;
		}

		// notice lue avec succes
		if ($ret["statut"] == "ok")
		{
			$notice->traiteNotice($ret["data"]);
			$nb_notices++;
			traceTraitementNotice();
			$ptr = $ret["pointeur_reprise"];
			$sql->execute("Update integrations set pointeur_reprise = $ptr Where id = " . $ligne["id"]);
		}
	}

	// Maj base et rapport erreurs et warnings
	$champ_erreurs = addslashes(serialize($phase_data["erreurs"]));
	$champ_warnings = addslashes(serialize($phase_data["warnings"]));
	if(!$phase_data["nb_erreurs"]) $phase_data["nb_erreurs"]=0;
	if(!$phase_data["nb_warnings"]) $phase_data["nb_warnings"]=0;
	$sql->execute("update integrations Set traite='$date', pointeur_reprise=$nb_notices,nb_erreurs=" . $phase_data["nb_erreurs"] . ",nb_warnings=" . $phase_data["nb_warnings"] . ",erreurs='$champ_erreurs',warnings='$champ_warnings' Where id = " . $ligne["id"]);
	$sql->execute("update int_bib set dernier_ajout='$date' where id_bib=$id_bib");
}
if ($phase == "0") $phase = "0.1";

// ----------------------------------------------------------------
// PSEUDO-NOTICES - cms rss sitotheque et albums (phase 0.1 a 0.6)
// ----------------------------------------------------------------
if ($phase > 0 and $phase < 1)
{
	include("integration/pseudo_notices.php");
	$phase = 1;
}

// ----------------------------------------------------------------
// Suppression des notices sans exemplaires (PHASE 2)
// ----------------------------------------------------------------
setVariable("traitement_phase", "Suppression des notices sans exemplaire");
if (!$mode_cron and $chrono->tempsPasse() > 10) sauveContexte();
if ($phase == 1)
{
	$log->ecrire("<h4>Suppression des notices sans exemplaire</h4>");
	unset($phase_data);
	$phase_data["nombre"] = 0;
	$phase_data["timeStart"] = time();
	$phase = 2;
}
if ($phase == 2)
{
	if ($phase_data["nombre"] and !$mode_cron) print("<h4>Suppression des notices sans exemplaire</h4>");
	$chrono->start();
	$result = $sql->prepareListe("select id_notice from notices where id_notice not in(select id_notice from exemplaires)");
	while ($id_notice = $sql->fetchNext($result, 1))
	{
		if (!$mode_cron and $chrono->tempsPasse() > $timeout) sauveContexte();
		$sql->execute("delete from notices where id_notice=$id_notice");
		$phase_data["nombre"]++;
		if ($phase_data["nombre"] % 100 == 0)
		{
			print("notice " . $phase_data["nombre"] . "<br>");
			flush();
		}
	}
	if ($phase_data["nombre"] == 0) $log->ecrire('<span class="vert">Aucune notice sans exemplaire</span>');
	else
	{
		$log->ecrire('<span class="vert">' . $phase_data["nombre"] . ' notices(s) sans exemplaire supprimée(s)</span>' . BR);
		$chrono->timeStart = $phase_data["timeStart"];
		$log->ecrire('<span class="vert">Temps de traitement : ' . $chrono->end() . " (" . $chrono->moyenne($phase_data["nombre"], "notices") . ")</span>" . BR);
	}
}
if ($phase == 2) $phase = "PERIODIQUES_0";

// ----------------------------------------------------------------
// INDEXATION DES ARTICLES DE PERIODIQUES
// ----------------------------------------------------------------
if (substr($phase, 0, 11) == "PERIODIQUES")
{
	include("integration/periodiques.php");
	$phase = 2;
}

// ----------------------------------------------------------------
// Traiter les notices succintes (PHASE 3)
// ----------------------------------------------------------------
require_once("classe_communication.php");
setVariable("traitement_phase", "Intégration des notices succintes");
$frequence = getVariable("succintes_frequence");
$date_homogene = getVariable("succintes_date");
$Z3950_retry_level = getVariable("Z3950_retry_level");
$ecart = ecartDates($date, $date_homogene);
if ($phase == 2)
{
	$log->ecrire("<h4>Intégration des notices succintes</h4>");
	$phase = 3;
	unset($phase_data);
	$phase_data["nombre"] = 0;
	$phase_data["timeStart"] = time();
	$chrono100notices->start();
	$phase_data["nb_homogene"] = 0;
	$phase_data["id"] = 0;
	if ($ecart < $frequence) $log->ecrire('<span class="vert">Sera fait dans ' . ($frequence - $ecart) . ' jour(s)</span>');
	elseif (!$mode_cron) sauveContexte();
}
if ($phase == 3 and $ecart >= $frequence)
{
	if (!$mode_cron) print("<h4>Intégration des notices succintes</h4>");
	$chrono->start();
	if (!$phase_data["nombre"]) $log->ecrire('<span class="vert">Niveau de tentatives d\'homogénéisation : ' . $Z3950_retry_level . '</span>' . BR . BR);
	$result = $sql->prepareListe("select * from notices_succintes where id > '" . $phase_data["id"] . "' and z3950_retry <= $Z3950_retry_level order by id");
	while ($data = $sql->fetchNext($result))
	{
		if (!$mode_cron and $chrono->tempsPasse() > $timeout) sauveContexte();
		$ret = $notice->traiteSuccinte($data);
		$phase_data["id"] = $data["id"];
		$phase_data["nombre"]++;
		traceSuccinte($ret);
	}
	// Recap
	$log->ecrire(BR . '<span class="vert">' . ($phase_data["nombre"]) . ' notices traitées</span>' . BR);
	$chrono->timeStart = $phase_data["timeStart"];
	$log->ecrire('<span class="vert">Temps de traitement : ' . $chrono->end() . " (" . $chrono->moyenne($phase_data["nombre"], "notices") . ")</span>" . BR);

	for ($i = 0; $i < 5; $i++) if (!$phase_data[$i]) $phase_data[$i] = "0";
	$log->ecrire('<table class="blank" cellspacing="0" cellpadding="5px" style="margin-left:25px;margin-bottom:10px;margin-top:10px">');
	$log->ecrire('<tr><td class="blank">Notices trouvées dans la base</td><td class="blank" align="right">' . $phase_data[1] . '</td></tr>');
	$log->ecrire('<tr><td class="blank">Notices trouvées sur serveurs z39.50</td><td class="blank" align="right">' . $phase_data[2] . '</td></tr>');
	$log->ecrire('<tr><td class="blank">Notices non trouvées</td><td class="blank" align="right">' . $phase_data[3] . '</td></tr>');
	$log->ecrire('<tr><td class="blank">Echecs de connexions</td><td class="blank" align="right">' . $phase_data[4] . '</td></tr>');
	$log->ecrire('</table>');
	setVariable("succintes_date", $date);
}

// ----------------------------------------------------------------
// Traiter l'homogénéisation des notices par ISBN (PHASE 4)
// ----------------------------------------------------------------
setVariable("traitement_phase", "Homogénéisation par l'ISBN");
$homogene_actif = getVariable("homogene");
$homogene_cache_only = getVariable("Z3950_cache_only");
$frequence = getVariable("homogene_frequence");
$date_homogene = getVariable("homogene_date");
$Z3950_retry_level = getVariable("Z3950_retry_level");
$Z3950_max_reconnect = getVariable("Z3950_max_reconnect");
$qualite_homogene = getVariable("homogene_code_qualite");
$ecart = ecartDates($date, $date_homogene);
if ($phase == 3)
{
	$log->ecrire("<h4>Homogénéisation Z39.50 des notices par l'ISBN</h4>");
	unset($phase_data);
	$phase_data["nombre"] = 0;
	$phase_data["timeStart"] = time();
	$chrono100notices->start();
	$phase = 4;
	if ($homogene_actif == 0)
	{
		$log->ecrire('<span class="rouge">Le processus d\'homogénéisation des notices est désactivé</span>' . BR);
		$phase = 7;
	} else
	{
		if ($ecart < $frequence) $log->ecrire('<span class="vert">Sera fait dans ' . ($frequence - $ecart) . ' jour(s)</span>');
		else if (!$mode_cron) sauveContexte();
	}
}
if ($phase == 4 and $ecart >= $frequence and $homogene_actif == 1)
{
	if (!$mode_cron) print("<h4>Homogénéisation Z39.50 des notices par l'ISBN</h4>");
	$chrono->start();
	$ret["timeout"] = 10;
	if (!$phase_data["nombre"])
	{
		$log->ecrire('<span class="vert">Niveau de tentatives d\'homogénéisation : ' . $Z3950_retry_level . '</span>' . BR);
		$log->ecrire('<span class="vert">Nombre maxi de tentatives de reconnexions : ' . $Z3950_max_reconnect . '</span>' . BR);
		$log->ecrire('<span class="vert">Mode de recherche sur les serveurs z39.50 : ' . getLibCodifVariable("Z3950_cache_only", $homogene_cache_only) . '</span>' . BR);
	}
	$result = $sql->prepareListe("select id_notice,isbn from notices where isbn > '" . $phase_data["isbn"] . "' and qualite < $qualite_homogene and z3950_retry <= $Z3950_retry_level order by isbn");
	while ($data = $sql->fetchNext($result))
	{
		while (true)
		{
			if (!$mode_cron and ($chrono->tempsPasse() + $ret["timeout"]) > $timeout) sauveContexte();
			$ret = $notice->traiteHomogene($data["id_notice"], $data["isbn"], "", "", $homogene_cache_only);
			traceHomogene($data["id_notice"], $data["isbn"], "", "");
			if ($ret["statut"] != "erreur" and $ret["statut_z3950"] > 0)
			{
				$phase_data["nb_reconnexions"] = 0;
				break;
			}

			// Tentatives de reconnexion
			$phase_data["nb_reconnexions"]++;
			if ($phase_data["nb_reconnexions"] >= $Z3950_max_reconnect)
			{
				$log->ecrire(BR . '<span class="rouge">Abandon du traitement : maximum de tentatives de reconnexions atteint</span>' . BR);
				$fin = true;
				break;
			}
			sleep(5);
		}
		if ($fin == true) break;
		$phase_data["isbn"] = $data["isbn"];
		$phase_data["nombre"]++;
	}
	afficherRecapHomogene($phase_data, $chrono);
}
// ----------------------------------------------------------------
// Traiter l'homogénéisation des notices par EAN (PHASE 5)
// ----------------------------------------------------------------
setVariable("traitement_phase", "Homogénéisation par l'EAN");
$fin = false;
if ($phase == 4)
{
	$log->ecrire("<h4>Homogénéisation Z39.50 des notices par l'EAN</h4>");
	unset($phase_data);
	$phase_data["nombre"] = 0;
	$phase_data["timeStart"] = time();
	$phase_data["ean"] = "";
	$chrono100notices->start();
	$phase = 5;
	if ($homogene_actif == 0)
	{
		$log->ecrire('<span class="rouge">Le processus d\'homogénéisation des notices est désactivé</span>' . BR);
		$phase = 7;
	} else
	{
		if ($ecart < $frequence) $log->ecrire('<span class="vert">Sera fait dans ' . ($frequence - $ecart) . ' jour(s)</span>');
		else if (!$mode_cron) sauveContexte();
	}
}
if ($phase == 5 and $ecart >= $frequence and $homogene_actif == 1)
{
	if (!$mode_cron) print("<h4>Homogénéisation Z39.50 des notices par l'EAN</h4>");
	$chrono->start();
	$ret["timeout"] = 10;
	if (!$phase_data["nombre"])
	{
		$log->ecrire('<span class="vert">Niveau de tentatives d\'homogénéisation : ' . $Z3950_retry_level . '</span>' . BR);
		$log->ecrire('<span class="vert">Nombre maxi de tentatives de reconnexions : ' . ($Z3950_max_reconnect) . '</span>' . BR);
		$log->ecrire('<span class="vert">Mode de recherche sur les serveurs z39.50 : ' . getLibCodifVariable("Z3950_cache_only", $homogene_cache_only) . '</span>' . BR);
	}
	$result = $sql->prepareListe("select id_notice,ean from notices where ean > '" . $phase_data["ean"] . "' and qualite < $qualite_homogene and z3950_retry <= $Z3950_retry_level order by ean");
	while ($data = $sql->fetchNext($result))
	{
		while (true)
		{
			if (!$mode_cron and ($chrono->tempsPasse() + $ret["timeout"]) > $timeout) sauveContexte();
			$ret = $notice->traiteHomogene($data["id_notice"], "", $data["ean"], "", $homogene_cache_only);
			traceHomogene($data["id_notice"], "", $data["ean"], "");
			if ($ret["statut"] != "erreur" and $ret["statut_z3950"] > 0)
			{
				$phase_data["nb_reconnexions"] = 0;
				break;
			}

			// Tentatives de reconnexion
			$phase_data["nb_reconnexions"]++;
			if ($phase_data["nb_reconnexions"] >= $Z3950_max_reconnect)
			{
				$log->ecrire(BR . '<span class="rouge">Abandon du traitement : maximum de tentatives de reconnexions atteint</span>' . BR);
				$fin = true;
				break;
			}
			sleep(5);
		}
		if ($fin == true) break;
		$phase_data["ean"] = $data["ean"];
		$phase_data["nombre"]++;
	}
	afficherRecapHomogene($phase_data, $chrono);
}

// --------------------------------------------------------------------
// Traiter l'homogénéisation des notices par ID_COMMERCIALE (PHASE 6)
// --------------------------------------------------------------------
setVariable("traitement_phase", "Homogénéisation par le numéro commercial");
$fin = false;
if ($phase == 5)
{
	$log->ecrire("<h4>Homogénéisation Z39.50 des notices par le numéro commercial</h4>");
	unset($phase_data);
	$phase_data["nombre"] = 0;
	$phase_data["timeStart"] = time();
	$phase_data["ean"] = "";
	$chrono100notices->start();
	$phase = 6;
	if ($homogene_actif == 0)
	{
		$log->ecrire('<span class="rouge">Le processus d\'homogénéisation des notices est désactivé</span>' . BR);
		$phase = 7;
	} else
	{
		if ($ecart < $frequence) $log->ecrire('<span class="vert">Sera fait dans ' . ($frequence - $ecart) . ' jour(s)</span>');
		else if (!$mode_cron) sauveContexte();
	}
}
if ($phase == 6 and $ecart >= $frequence and $homogene_actif == 1)
{
	if (!$mode_cron) print("<h4>Homogénéisation Z39.50 des notices par le numéro commercial</h4>");
	$chrono->start();
	$ret["timeout"] = 10;
	if (!$phase_data["nombre"])
	{
		$log->ecrire('<span class="vert">Niveau de tentatives d\'homogénéisation : ' . $Z3950_retry_level . '</span>' . BR);
		$log->ecrire('<span class="vert">Nombre maxi de tentatives de reconnexions : ' . ($Z3950_max_reconnect) . '</span>' . BR);
		$log->ecrire('<span class="vert">Mode de recherche sur les serveurs z39.50 : ' . getLibCodifVariable("Z3950_cache_only", $homogene_cache_only) . '</span>' . BR);
	}
	$result = $sql->prepareListe("select id_notice,id_commerciale from notices where id_commerciale > '" . $phase_data["id_commerciale"] . "' and qualite < $qualite_homogene and z3950_retry <= $Z3950_retry_level order by id_commerciale");
	while ($data = $sql->fetchNext($result))
	{
		while (true)
		{
			if (!$mode_cron and ($chrono->tempsPasse() + $ret["timeout"]) > $timeout) sauveContexte();
			$ret = $notice->traiteHomogene($data["id_notice"], "", "", $data["id_commerciale"], $homogene_cache_only);
			traceHomogene($data["id_notice"], "", "", $data["id_commerciale"]);
			if ($ret["statut"] != "erreur" and $ret["statut_z3950"] > 0)
			{
				$phase_data["nb_reconnexions"] = 0;
				break;
			}

			// Tentatives de reconnexion
			$phase_data["nb_reconnexions"]++;
			if ($phase_data["nb_reconnexions"] >= $Z3950_max_reconnect)
			{
				$log->ecrire(BR . '<span class="rouge">Abandon du traitement : maximum de tentatives de reconnexions atteint</span>' . BR);
				$fin = true;
				break;
			}
			sleep(5);
		}
		if ($fin == true) break;
		$phase_data["id_commerciale"] = $data["id_commerciale"];
		$phase_data["nombre"]++;
	}
	afficherRecapHomogene($phase_data, $chrono);
	setVariable("homogene_date", $date);
	$phase = 7;
}
elseif ($phase == 6) $phase = 7;

// ----------------------------------------------------------------
// Recalcul des facettes bibliothèque (phases 7 et 7.1)
// ----------------------------------------------------------------
if (!$mode_cron and $chrono->tempsPasse() > 5) sauveContexte();
if ($phase > 6 and $phase < 8) include("integration/facettes.php");

// ----------------------------------------------------------------
// Integration des abonnés
// ----------------------------------------------------------------
if ($phase == 8 or $phase == 9)
{
	include("integration/abonnes.php");
	$phase = 10;
}

// ----------------------------------------------------------------
// Integration des prets et des reservations
// ----------------------------------------------------------------
if ($phase == 10 or $phase == 11)
{
	include("integration/prets.php");
	$phase = 12;
}
if ($phase == 12 or $phase == 13)
{
	include("integration/reservations.php");
	$phase = 20;
}

// ----------------------------------------------------------------
// Envoi de mails aux bibs qui ont du retard
// ----------------------------------------------------------------
setVariable("traitement_phase", "Envoi des mails aux bibliothèques qui ont du retard");
if ($phase == 20)
{
	$log->ecrire("<h4>Envoi des mails aux bibliothèques qui ont du retard</h4>");
	unset($phase_data);
	$phase_data["nombre"] = 0;
	$phase_data["nb_erreurs"] = 0;
	$phase_data["timeStart"] = time();
	$phase_data["pointeur"] = "";
	$phase = 21;
} else
{
	if (!$mode_cron) print("<h4>Envoi des mails aux bibliothèques qui ont du retard</h4>");
}
if ($phase == 21)
{
	require_once("classe_mail.php");
	$mail = new classe_mail();
	$bib = new bibliotheque();
	$bibs = $bib->getListeRetardIntegration();
	if ($bibs)
	{
		foreach ($bibs as $enreg)
		{
			if (!$mode_cron and $chrono->tempsPasse() > $timeout) sauveContexte();
			if ($enreg["nom_court"] <= $phase_data["pointeur"]) continue;
			$log->ecrire('<span class="violet">' . $enreg["nom_court"] . " : " . $enreg["retard"] . ' de retard</span><br>');
			$data["NOM_BIB"] = $enreg["nom"];
			$data["DATE_ENVOI"] = $enreg["dernier_ajout"];
			$data["RETARD"] = $enreg["retard"];
			$data["ECART_AJOUTS"] = $enreg["ecart_ajouts"];
			$erreur = $mail->sendMail($enreg["mail"], $data);
			if ($erreur)
			{
				$phase_data["nb_erreurs"]++;
				$log->ecrire('<span class="rouge" style="margin-left:20px">' . $erreur . '</span><br>');
			} else
			{
				$phase_data["nombre"]++;
				$date_mail = date("d-m-Y");
				$id_bib = $enreg["id_bib"];
				$sql->execute("update int_bib set date_mail='$date_mail' where id_bib=$id_bib");
			}
			$phase_data["pointeur"] = $enreg["nom_court"];
		}
		$chrono100notices->timeStart = $phase_data["timeStart"];
		$log->ecrire(BR);
		if ($phase_data["nb_erreurs"] > 0) $log->ecrire('<span class="rouge">Echecs d\'envois : ' . $phase_data["nb_erreurs"] . '</span><br>');
		$log->ecrire('<span class="vert">' . $phase_data["nombre"] . ' mail(s) envoyé(s)</span>' . BR);
		$log->ecrire('<span class="vert">Temps de traitement : ' . $chrono100notices->end() . '</span>' . BR);
	}
	else $log->ecrire('<span class="vert">Aucune alerte de retard à signaler</span>' . BR);
}

// ----------------------------------------------------------------
// Fin
// ----------------------------------------------------------------
setVariable("clef_traitements", "0");
setVariable("traitement_phase", "Traitement terminé");
$chrono->timeStart = $timeStart;
$log->ecrire("<h4>Fin des traitements</h4>");
$log->ecrire("Déblocage de la base<br>");
$log->ecrire("Heure :  " . date("G:i:s") . BR);
$log->ecrire('Temps de traitement : ' . $chrono->end() . BR);

// Résumé
if ($compteur[0] > 0) $log->ecrire('<span class="rouge">Notices rejetées : ' . $compteur[0] . '</span>' . BR);
$lib = $notice->libStatut;
$log->ecrire(BR . '<table class="blank" cellpadding="5px" style="margin-left:15px;margin-top:5px;">');
for ($i = 1; $i < count($lib); $i++)
{
	if (!$compteur[$i]) $compteur[$i] = "aucune";
	$log->ecrire('<tr class="blank"><td class="blank">' . $lib[$i] . '</td><td class="blank" align="right">' . $compteur[$i] . '</td></tr>');
}
$log->ecrire('</table>');

$log->close();
$log_erreur->close();
$log_warning->close();
print(BR . BR . '</body></html>');
exit;

// ----------------------------------------------------------------
// Ecriture logs et affichage écran
// ----------------------------------------------------------------
function traceTraitementNotice()
{
	global $log, $log_erreur, $log_warning;
	global $notice, $compteur, $phase_data, $nb_notices, $chrono100notices, $debug_level;
	global $nom_bib, $libelle_type_operation, $ret;

	// Recup du statut
	$statut = $notice->getLastStatut();
	$code_statut = $statut["statut"];

	// Maj des compteurs
	$compteur[$code_statut]++;
	$compteur["nb_notices"]++;

	// logs
	if ($code_statut == 0)
	{
		//incrementeVariable("traitement_erreurs");
		$phase_data["nb_erreurs"]++;
		$phase_data["erreurs"][$statut["erreur"]][] = $ret["adresse"];
		if ($debug_level == 1 or $debug_level == 2) $log->ecrire('<span class="rouge">Notice n° ' . $nb_notices . ' - ' . $statut["erreur"] . '</span>' . BR);
	}
	if (count($statut["warnings"]) > 0)
	{
		//incrementeVariable("traitement_warnings");
		if ($debug_level == 2) $log->ecrire('<span class="num_notice">notice n° ' . $nb_notices . '</span>' . BR);
		foreach ($statut["warnings"] as $warning)
		{
			if ($debug_level == 2) $log->ecrire('<font color="purple">Anomalie : ' . $warning[0] . " &raquo; " . $warning[1] . '</font>' . BR);
			if ($warning[0] == "Genre non reconnu" or $warning[0] == "Section non reconnue") continue;
			$phase_data["nb_warnings"]++;
			$phase_data["warnings"][$warning[0]][] = $ret["adresse"] . chr(9) . $warning[1];
		}
	}

	// Affichage toutes les 100 notices
	if ($nb_notices % 100 == 0)
	{
		$log->ecrire("notice $nb_notices (" . $chrono100notices->tempsPasse() . " secondes)<br>");
		$chrono100notices->start();
	}
	// Debug level a 1 on affiche le mode d'identificationde la notice
	if ($debug_level > 1)
	{
		if ($nb_notices == 1) print(BR);
		$log->ecrire('<span class="num_notice">Notice n° ' . $nb_notices . '</span>' . BR);
		$log->ecrire('<span>Mode d\'identification ---> ' . $statut["identification"] . '</span>' . BR);
	}
	// Tout afficher si debug_level maxi
	if ($debug_level > 2)
	{
		$detail = $notice->getNotice();
		$log->ecrire('<div style=width:700px;margin-left:15px;margin-bottom:10px;">');
		$log->ecrire('<table class="blank" cellspacing="0" cellpadding="5px">');
		// Statut de retour
		$log->ecrire('<tr><td class="blank">Traitement</td>');
		if ($statut["erreur"]) $erreur = '<span class="rouge"> - ' . $statut["erreur"] . '</span>'; else $erreur="";
		$log->ecrire('<td class="blank">' . $notice->libStatut[$code_statut] . $erreur . '</td></tr>');
		// warnings
		$log->ecrire('<tr><td class="blank" style="vertical-align:top">Anomalies</td>');
		if (count($statut["warnings"]) > 0)
		{
			$log->ecrire('<td class="blank"><font color="purple">');
			foreach ($statut["warnings"] as $warning)
			{
				$log->ecrire($warning[0] . " &raquo; " . $warning[1] . BR);
			}
			$log->ecrire('</font></td></tr>');
		}
		else $log->ecrire('<td class="blank">aucune</td></tr>');
		// Détail de l'enreg
		foreach ($detail as $clef => $data)
		{
			if ($clef == "exemplaires")
			{
				for ($i = 0; $i < count($data); $i++)
				{
					$log->ecrire('<tr><td class="blank" style="vertical-align:top">Exemplaire ' . ($i + 1) . '</td>');
					$log->ecrire('<td class="blank">');
					foreach ($data[$i] as $key => $valeur) $log->ecrire($key . " = " . $valeur . BR);
					$log->ecrire('</td></tr>');
				}
				continue;
			} elseif ($clef == "warnings") continue;
			elseif (gettype($data) == "array")
			{
				$aff = "";
				foreach ($data as $key => $valeur) $aff.=$key . " = " . $valeur . BR;
				$data = $aff;
			}
			$log->ecrire('<tr><td class="blank" style="vertical-align:top">' . $clef . '</td>');
			if ($data == "") $data = "&nbsp;";
			$log->ecrire('<td class="blank">' . $data . '</td></tr>');
		}
		$log->ecrire("</table></div>");
	}
}

// ----------------------------------------------------------------
// Affichage detail pour l'homogeneisation
// ----------------------------------------------------------------
function traceHomogene($id_notice, $isbn, $ean, $id_commerciale)
{
	global $debug_level, $ret, $phase_data, $log, $chrono100notices;

	$phase_data[$ret["statut_z3950"]]++;
	if ($debug_level == 0)
	{
		if ($ret["statut"] == "erreur") $log->ecrire('<span class="vert">Connexion au serveur : ' . $ret["serveur"] . ' ---> </span><span class="rouge">' . $ret["erreur"] . '</span>' . BR);
		elseif (!$phase_data["nombre"]) $log->ecrire('<span class="vert">Connexion au serveur : ' . $ret["serveur"] . ' ---> ok</span>' . BR);
		if ($phase_data["nombre"] and $phase_data["nombre"] % 100 == 0)
		{
			$log->ecrire("notice " . $phase_data["nombre"] . " (" . $chrono100notices->tempsPasse() . " secondes)" . BR);
			$chrono100notices->start();
		}
		return;
	}
	$log->ecrire('<a class="notice" href="' . URL_BASE . "php/analyse_afficher_notice_full.php?id_notice=" . $id_notice . '">Notice n° ' . $id_notice . '</a>' . BR);
	$log->ecrire('<table class="blank" cellspacing="0" cellpadding="5px" style="margin-left:15px;margin-bottom:10px">');
	if ($isbn) $log->ecrire('<tr><td class="blank">Isbn</td><td class="blank">' . $isbn . '</td></tr>');
	if ($ean) $log->ecrire('<tr><td class="blank">Ean</td><td class="blank">' . $ean . '</td></tr>');
	if ($id_commerciale) $log->ecrire('<tr><td class="blank">No commercial</td><td class="blank">' . $id_commerciale . '</td></tr>');
	$log->ecrire('<tr><td class="blank">Serveur</td><td class="blank">' . $ret["serveur"] . '</td></tr>');
	if ($ret["statut_z3950"] > 1) $class = "vert";
	$log->ecrire('<tr><td class="blank">Statut</td><td class="blank"><span class="' . $class . '">' . communication::getLibelleStatutZ3950($ret["statut_z3950"]) . '</span></td></tr>');
	if ($ret["erreur"]) $log->ecrire('<tr><td class="blank">Erreur</td><td class="blank"><span class="rouge">' . $ret["erreur"] . '</span></td></tr>');
	$log->ecrire('</table>');
}

function afficherRecapHomogene($phase_data, $chrono)
{
	global $log, $compteur;
	$compteur[6]+=$phase_data[2] + $phase_data[3]; // Incrementer compteur global d'homogeneisation
	for ($i = 0; $i < 4; $i++) if (!$phase_data[$i]) $phase_data[$i] = "0";

	$log->ecrire(BR . '<span class="vert">' . ($phase_data["nombre"]) . ' notices traitées</span>' . BR);
	$chrono->timeStart = $phase_data["timeStart"];
	$log->ecrire('<span class="vert">Temps de traitement : ' . $chrono->end() . " (" . $chrono->moyenne($phase_data["nombre"], "notices") . ")</span>" . BR);
	$log->ecrire('<table class="blank" cellspacing="0" cellpadding="5px" style="margin-left:25px;margin-bottom:10px;margin-top:10px">');
	$log->ecrire('<tr><td class="blank">Notices homogenéisées</td><td class="blank" align="right">' . ($phase_data[2] + $phase_data[3]) . '</td></tr>');
	$log->ecrire('<tr><td class="blank">Notices non trouvées</td><td class="blank" align="right">' . $phase_data[1] . '</td></tr>');
	$log->ecrire('<tr><td class="blank">Echecs de connexions</td><td class="blank" align="right">' . $phase_data[0] . '</td></tr>');
	$log->ecrire('</table>');
}

// ----------------------------------------------------------------
// Affichage detail pour les notices succintes
// ----------------------------------------------------------------
function traceSuccinte($ret)
{
	global $debug_level, $phase_data, $log, $chrono100notices, $bib, $compteur;

	// compteurs
	if ($ret["statut"] > 0) $compteur[$ret["statut"]]++;
	switch ($ret["statut"])
	{
		case 1: $phase_data[2]++;
			break;
		case 4: $phase_data[1]++;
			break;
		default:
			{
				if ($ret["statut_z3950"] == 1) $phase_data[3]++;
				else $phase_data[4]++;
			}
	}

	// Traces
	if ($debug_level == 0)
	{
		if ($phase_data["nombre"] and $phase_data["nombre"] % 100 == 0)
		{
			$log->ecrire("notice " . $phase_data["nombre"] . " (" . $chrono100notices->tempsPasse() . " secondes)" . BR);
			$chrono100notices->start();
		}
		return;
	}
	$log->ecrire('<a class="notice" href="' . URL_BASE . "php/analyse_afficher_notice_full.php?id_notice=" . $ret["id_notice"] . '">Notice n° ' . $ret["id_notice"] . '</a>' . BR);
	$log->ecrire('<table class="blank" cellspacing="0" cellpadding="5px" style="margin-left:15px;margin-bottom:10px">');
	$log->ecrire('<tr><td class="blank">Bibliothèque</td><td class="blank">' . $bib->getNomCourt($ret["id_bib"]) . '</td></tr>');
	if ($ret["isbn"]) $log->ecrire('<tr><td class="blank">Isbn</td><td class="blank">' . $ret["isbn"] . '</td></tr>');
	if ($ret["ean"]) $log->ecrire('<tr><td class="blank">Ean</td><td class="blank">' . $ret["ean"] . '</td></tr>');
	if ($ret["id_commerciale"]) $log->ecrire('<tr><td class="blank">No commercial</td><td class="blank">' . $ret["id_commerciale"] . '</td></tr>');
	if ($ret["statut"] == 4) $log->ecrire('<tr><td class="blank">Statut</td><td class="blank"><span class="vert">Trouvée dans la base</span></td></tr>');
	else
	{
		$log->ecrire('<tr><td class="blank">Serveur</td><td class="blank">' . $ret["serveur"] . '</td></tr>');
		if ($ret["statut_z3950"] > 1) $class = "vert";
		$log->ecrire('<tr><td class="blank">Statut</td><td class="blank"><span class="' . $class . '">' . communication::getLibelleStatutZ3950($ret["statut_z3950"]) . '</span></td></tr>');
	}
	if ($ret["erreur"]) $log->ecrire('<tr><td class="blank">Erreur</td><td class="blank"><span class="rouge">' . $ret["erreur"] . '</span></td></tr>');
	$log->ecrire('</table>');
}

// ----------------------------------------------------------------
// Gestion du contexte pour les timeout
// ----------------------------------------------------------------
function sauveContexte()
{
	global $timeStart, $chrono_fichier, $chrono100notices;
	global $nb_notices, $compteur;
	global $phase, $phase_data;

	$timeStart_fichier = $chrono_fichier->timeStart;
	$timeStart_100notices = $chrono100notices->timeStart;
	$data = compact("nb_notices", "compteur", "timeStart", "timeStart_fichier", "timeStart_100notices", "phase", "phase_data");
	$_SESSION["reprise"] = $data;
	redirection("integre_traite_main.php?reprise=oui");
}

function restaureContext()
{
	global $timeStart, $chrono_fichier, $chrono100notices;
	global $nb_notices, $compteur;
	global $phase, $phase_data;

	extract($_SESSION["reprise"]);
	$chrono_fichier->timeStart = $timeStart_fichier;
	$chrono100notices->timeStart = $timeStart_100notices;
	unset($_SESSION["reprise"]);
}

?>
