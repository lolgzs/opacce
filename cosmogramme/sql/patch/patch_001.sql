-------------------------------------------------------------------------
-- Mise à niveau des variables
-------------------------------------------------------------------------

insert into variables (clef,valeur,commentaire,groupe) values('patch_level','0','Niveau de patch de la base de données',7);
delete from variables where clef='trace_debug';
update variables set liste='0:Unimarc\r\n1:Ascii tabulé\r\n2:Ascii séparé par des points-virgule\r\n3:Ascii séparé par des "|"' where clef='import_format';
UPDATE `variables` SET `clef` = 'type_fichier',
`commentaire` = 'Types de fichiers pour les imports.',
`liste` = '0:notices\r\n1:abonnés\r\n2:prêts '
where clef='tables';
update variables set ordre=80 where clef='champs_ascii';
INSERT INTO `variables` (
`clef` ,
`valeur` ,
`commentaire` ,
`type_champ` ,
`liste` ,
`groupe` ,
`ordre` ,
`verrou`
)
VALUES ('champs_abonne', NULL , 'Champs pour les fichiers abonnés.',
'2',
'IDABON:id abonné (n° de carte)\r\nORDREABON:n°d''ordre dans la famille\r\nNOM:nom\r\nPRENOM:prénom\r\nNAISSANCE:date de naissance\r\nPASSWORD:mot de passe\r\nMAIL:adresse e-mail\r\nDATE_DEBUT:date début abonnement\r\nDATE_FIN:date fin abonnement',
'5', '81', 'checked');

ALTER TABLE `profil_donnees` ADD `type_fichier` TINYINT NOT NULL DEFAULT '0' AFTER `rejet_periodiques`;

-------------------------------------------------------------------------
-- Mise à niveau table des abonnés
-------------------------------------------------------------------------
ALTER TABLE `bib_admin_users` ADD `NOM` VARCHAR( 50 ) NOT NULL AFTER `ID_USER`;
ALTER TABLE `bib_admin_users` ADD `PRENOM` VARCHAR( 50 ) NOT NULL AFTER `NOM` ;
ALTER TABLE `bib_admin_users` ADD `NAISSANCE` VARCHAR( 10 ) NOT NULL AFTER `PRENOM` ;
ALTER TABLE `bib_admin_users` ADD `DATE_DEBUT` VARCHAR( 10 ) NOT NULL ;
ALTER TABLE `bib_admin_users` ADD `DATE_FIN` VARCHAR( 10 ) NOT NULL ;
ALTER TABLE `bib_admin_users` ADD INDEX ( `NOM` );
ALTER TABLE `bib_admin_users` ADD INDEX ( `IDABON` );
ALTER TABLE `bib_admin_users` ADD INDEX ( `LOGIN` );
ALTER TABLE `bib_admin_users` CHANGE `IDABON` `IDABON` VARCHAR( 20 ) NOT NULL;
ALTER TABLE `bib_admin_users` CHANGE `ID_USER` `ID_USER` INT NOT NULL AUTO_INCREMENT;

-------------------------------------------------------------------------
-- Index cms
-------------------------------------------------------------------------
ALTER TABLE `cms_article` ADD `DESCRIPTION` TEXT NOT NULL AFTER `TITRE`;
ALTER TABLE `cms_article` ADD INDEX ( `DATE_CREATION` );
ALTER TABLE `cms_article` ADD INDEX ( `EVENTS_DEBUT` );
ALTER TABLE `cms_article` ADD INDEX ( `EVENTS_FIN` );
ALTER TABLE `cms_article` ADD INDEX ( `ID_CAT` );
ALTER TABLE `cms_categorie` ADD INDEX ( `ID_SITE` );