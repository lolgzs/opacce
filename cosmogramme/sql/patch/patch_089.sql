ALTER TABLE `notices` ADD INDEX ( `date_maj` );
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
'date_maj_facettes', NOW() , 'Dernière date de mise à jour des facettes exemplaires.', '0', '', '6', '0', ''
);