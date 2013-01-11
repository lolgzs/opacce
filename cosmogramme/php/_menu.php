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
//////////////////////////////////////////////////////////////////////////////////////////
// MENU GAUCHE
//////////////////////////////////////////////////////////////////////////////////////////

include("_init_frame.php");

// Menu catalogueur
if($_SESSION["passe"] == "catalogueur")
{
?>
<body class="menu">
	<a href="<?php print(URL_BASE)?>" target="_top"><img src="<?php print(URL_IMG); ?>home.gif" style="cursor:pointer;margin-top:5px;margin-left:7px" title="Retour à l'accueil" border="0"></a>
	<a href="<?php print(getVariable("url_site"));?>" target="_top"><img src="<?php print(URL_IMG); ?>icone_site.gif" style="cursor:pointer;margin-top:5px;margin-left:10px" title="Aller sur le portail" border="0"></a>
	<a href="<?php print(substr(URL_BASE,0,strlen(URL_BASE)-1)."?action=logout");?>" target="_top"><img src="<?php print(URL_IMG); ?>deconnexion.gif" style="cursor:pointer;margin-top:5px;margin-left:10px" title="Se déconnecter" border="0"></a>
	<div class="menu_section">Recherches</div>
	<?php
	ligneMenu("Recherche de notices","recherche_recherche.php");
	ligneMenu("Accès notices par identifiants","recherche_identifiants.php");
	?>
	<div class="menu_section">Autorités et codifications</div>
	<?php
	ligneMenu("Auteurs","codif_auteur.php");
	ligneMenu("Matières","codif_matiere.php");
	ligneMenu("Indices Dewey","codif_dewey.php");
	ligneMenu("Indices PCDM4","codif_pcdm4.php");
	?>
	<div class="menu_section">Statistiques</div>
	<?php
	ligneMenu("Statistiques globales","stat_globales.php");
	ligneMenu("Détail par bibliothèques","stat_detail_bibliotheques.php");
	ligneMenu("Types de documents","stat_types_documents.php");
	ligneMenu("Qualité des notices","stat_qualite.php");
	?>
	<br><br>
</body>
</html>

<?php
}
else
{
?>
<body class="menu">
	<a href="<?php print(URL_BASE)?>" target="_top"><img src="<?php print(URL_IMG); ?>home.gif" style="cursor:pointer;margin-top:5px;margin-left:7px" title="Retour à l'accueil" border="0"></a>
	<a href="<?php print(getVariable("url_site"));?>" target="_top"><img src="<?php print(URL_IMG); ?>icone_site.gif" style="cursor:pointer;margin-top:5px;margin-left:10px" title="Aller sur le portail" border="0"></a>
	<a href="<?php print(substr(URL_BASE,0,strlen(URL_BASE)-1)."?action=logout");?>" target="_top"><img src="<?php print(URL_IMG); ?>deconnexion.gif" style="cursor:pointer;margin-top:5px;margin-left:10px" title="Se déconnecter" border="0"></a>
	<div class="menu_section">Intégration</div>
	<?php
	ligneMenu("Contrôle des intégrations","integre_controle_integrations.php");
	ligneMenu("Journal des intégrations","integre_log.php");
	ligneMenu("Traitements en cours","integre_traitements_attente.php");
	ligneMenu("Fichiers en attente","integre_fichiers_attente.php");
	ligneMenu("Lancer les traitements","integre_traite_main.php",true);
	?>
	<div class="menu_section">Analyse des données</div>
	<?php
	ligneMenu("Analyser un fichier unimarc","integre_analyse_fichier_unimarc.php?action=PARAM");
	ligneMenu("Listes de contrôle","analyse_liste_controle.php");
	ligneMenu("Recherche de doublons","analyse_recherche_doublons.php");
	ligneMenu("Articles de périodiques","analyse_articles_periodiques.php");

	?>
	<div class="menu_section">Recherches</div>
	<?php
	ligneMenu("Recherche de notices","recherche_recherche.php");
	ligneMenu("Accès notices par identifiants","recherche_identifiants.php");
	ligneMenu("Nuages de tags Auteurs","recherche_tags_auteurs.php");
	ligneMenu("Nuages de tags tous mots","recherche_tags.php");
	ligneMenu("Recherche guidée","recherche_guide.php?mode=INTRO");
	?>
	<div class="menu_section">Configurations</div>
	<?php
	ligneMenu("Génération de site Pergame","integre_generation_pergame.php");
	ligneMenu("Plannification des intégrations","integre_plannification.php");
	ligneMenu("Intégrations programmées","config_integrations.php");
	ligneMenu("Profils de données","config_profil_donnees.php");
	ligneMenu("Variables","config_variables.php");
	?>
	<div class="menu_section">Autorités et codifications</div>
	<?php
	ligneMenu("Auteurs","codif_auteur.php");
	ligneMenu("Matières","codif_matiere.php");
	ligneMenu("Indices Dewey","codif_dewey.php");
	ligneMenu("Indices PCDM4","codif_pcdm4.php");
	ligneMenu("Sections","codif_section.php");
	ligneMenu("Genres","codif_genre.php");
	ligneMenu("Emplacements","codif_emplacement.php");
	ligneMenu("Annexes","codif_annexe.php");
	?>
	<div class="menu_section">Statistiques</div>
	<?php
	ligneMenu("Statistiques globales","stat_globales.php");
	ligneMenu("Détail par bibliothèques","stat_detail_bibliotheques.php");
	ligneMenu("Types de documents","stat_types_documents.php");
	ligneMenu("Qualité des notices","stat_qualite.php");
	?>
	<div class="menu_section">Exports</div>
	<?php
	ligneMenu("Export de notices","export_fichier_unimarc.php?action=PARAM");
	?>
	<div class="menu_section">Utilitaires</div>
	<?php
	ligneMenu("Suppression d'exemplaires","integre_supprimer_exemplaires.php");
	ligneMenu("Test envoi mails","test_envoi_mails.php");
	ligneMenu("Logs des erreurs SQL","integre_log_sql.php");
	ligneMenu("Réindexation des identifiants","util_indexation.php");
	ligneMenu("Réindexation phonétique","util_fulltext.php?action=PARAM");
	?>
</body>
</html>

<?php
}

function ligneMenu( $texte, $url,$confirmation=false)
{
	if($confirmation) $onclick=' onclick="if(confirm(\'Etes vous-sûr de vouloir lancer ce traitement ?\')==false) return false;"';

	print('<div class="menu_ligne">');
	print('<img src="'. URL_IMG .'puce_menu.gif">');
	print('&nbsp;<a href="' . $url .'" target="droite"'.$onclick.'>');
	print($texte);
	print('</a></div>');
}
?>