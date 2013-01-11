UPDATE  `variables` SET  `liste` =  '
IDABON:id abonné (n° de carte)\r\n
ORDREABON:n° d''ordre dans la famille\r\n
NOM:nom\r\n
PRENOM:prénom\r\n
NAISSANCE:date de naissance\r\n
PASSWORD:mot de passe\r\n
MAIL:adresse e-mail\r\n
DATE_DEBUT:date début abonnement\r\n
DATE_FIN:date fin abonnement\r\n
ID_SIGB:Identifiant interne dans le sigb\r\n
NULL:ignorer ce champ'
WHERE  `variables`.`clef` =  'champs_abonne';