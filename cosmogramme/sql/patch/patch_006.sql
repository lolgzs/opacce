-------------------------------------------------------------------------
-- Mode communication avec le sigb
-------------------------------------------------------------------------

ALTER TABLE `int_bib` ADD `comm_sigb` TINYINT NOT NULL ;
ALTER TABLE `int_bib` ADD `comm_params` TEXT NOT NULL ;
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
VALUES (
'comm_sigb', NULL , 'Mode de communication avec le sigb pour obtenir la disponibilité des exemplaires et la récupération des données liées aux abonnés.',
'2', '0:aucun\r\n1:pergame\r\n2:web-service opsys\r\n3:serveur Z39.50',
'2', '3', 'checked'
);

ALTER TABLE `bib_c_zone` ADD `IMAGE` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE `bib_c_zone` ADD `COULEUR` VARCHAR( 7 ) NOT NULL ;
ALTER TABLE `bib_c_site` ADD `AFF_ZONE` TEXT NOT NULL ;