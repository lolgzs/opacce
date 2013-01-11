delete from variables where clef='unicite_code_barres';
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
'unicite_code_barres', 0 , 'Mode de reconnaissance des codes-barres. ', '2', '0:Bibliothèque + codes-barres\r\n1:Code-barres uniquement', '4', '0', 'checked'
);

UPDATE `variables` SET `liste` =
'
0:utf-8\r\n
1:iso 2709\r\n
2:windows ansi\r\n
3:accents dos\r\n
4:marc21
'
WHERE clef='transco_accents';