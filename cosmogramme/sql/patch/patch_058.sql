
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
'portail_nom_affichage', NULL , 'Intitulé du portail pour affichage dans l''interface. Sert pour alimenter les zones 801$b et 995$a dans les exports de notices.', '0', '', '1', '1', ''
);

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
'export_fichier', 'export_notices.txt', 'Nom du fichier par défaut pour les exports de notices.', '0', '', '3', '110', ''
);

UPDATE `variables` SET `commentaire` = 'Url du portail (sans slash final).' WHERE `clef`= 'url_site';

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
'export_type_doc_995', NULL , 'Table d''équivalence des types de documents à mettre en zone 995$r pour les exports de notices. *nb : si un type de document n''a pas d''équivalence, les données du bloc de label seront prises.', '2', '1:am\r\n2:as\r\n3:je\r\n4:gd\r\n5:ld\r\n6:iz\r\n7:zz\r\n', '3', '120', ''
);

ALTER TABLE `int_bib` ADD `pas_exporter` TINYINT NOT NULL DEFAULT '0';
