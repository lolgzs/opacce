-------------------------------------------------------------------------
-- Mise à niveau des variables
-------------------------------------------------------------------------
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
VALUES ('champ_cote', 'k', 'Sous-champs pour la reconnaissance de la cote.',
'2','k:995$k\r\nf:995$f','5', '1', 'checked');
