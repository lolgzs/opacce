UPDATE  `variables` SET  `liste` =  '0:Pas informatisé\r\n1:Pergame\r\n2:Paprika\r\n3:Orphée\r\n4:Opsys\r\n5:Microbib\r\n6:Atalante\r\n7:Multilis\r\n8:Bibal\r\n9:Milord\r\n10:Elissa\r\n11:v-smart'
WHERE  `variables`.`clef` =  'sigb';

UPDATE  `variables` SET  `liste` =  'f:995$f\r\nh:995$h\r\n997:997$a\r\n852:852$g' WHERE  `variables`.`clef` =  'champ_code_barres';

UPDATE  `variables` SET  `liste` =  'IDABON:id abonné (n° de carte)\r\nORDREABON:n° d''ordre dans la famille\r\nNOM:nom\r\nPRENOM:prénom\r\nNAISSANCE:date de naissance\r\nPASSWORD:mot de passe\r\nMAIL:adresse e-mail\r\nDATE_DEBUT:date début abonnement\r\nDATE_FIN:date fin abonnement\r\nNULL:ignorer ce champ' WHERE  `variables`.`clef` =  'champs_abonne';