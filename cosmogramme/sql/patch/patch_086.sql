CREATE TABLE IF NOT EXISTS `modele_fusion` (
	 `id` int( 11 ) NOT NULL AUTO_INCREMENT,
	 `nom` varchar(100) NOT NULL,
	 `contenu` TEXT,
	 PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

ALTER TABLE `modele_fusion` ADD UNIQUE (`nom`); 

ALTER TABLE `bib_admin_users` ADD COLUMN telephone varchar(25) NOT NULL;

INSERT INTO `modele_fusion` (`nom`, `contenu`)
VALUES('FORMATION_EMARGEMENT',
			 '<p>Fiche d\'&eacute;margement de la session du {session_formation.date_debut_texte}</p>
			  <h1>{session_formation.formation.libelle}</h2>
				{session_formation.stagiaires["Nom":nom, "Pr&eacute;nom":prenom, "Signature"]}');


INSERT INTO `modele_fusion` (`nom`, `contenu`)
VALUES('FORMATION_CONVOCATION',
			 '<div>
				<h1>Convocation pour {stagiaire.nom}, {stagiaire.prenom}</h1>
				<p>Le stage {session_formation.formation.libelle} d&eacute;butera le {session_formation.date_debut}</p>
				</div>');


INSERT INTO `modele_fusion` (`nom`, `contenu`)
VALUES('FORMATION_LISTE_STAGIAIRES',
			 '<h1>Liste des stagiaires pour la session du {session_formation.date_debut_texte}</h1>
			  <h2>{session_formation.formation.libelle}</h2>
				{session_formation.stagiaires["Nom":nom, "Pr&eacute;nom":prenom, "Biblioth&egrave;que":bib.libelle, "T&eacute;l&eacute;phone":telephone]}');


INSERT INTO `modele_fusion` (`nom`, `contenu`)
VALUES('FORMATION_ATTESTATION',
			 '<h1>Je soussign&eacute; {stagiaire.nom}, {stagiaire.prenom} avoir particip&eacute; &agrave; la session du {session_formation.date_debut_texte}</h1>
			  <h2>{session_formation.formation.libelle}</h2>');


INSERT INTO `modele_fusion` (`nom`, `contenu`)
VALUES('FORMATION_REFUS',
			 '<p>A l\'attention de {stagiaire.nom}, {stagiaire.prenom}</p>
			  <p>Votre inscription au stage {session_formation.formation.libelle} n\'a pu &ecirc;tre retenue</p>');

