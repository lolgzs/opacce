UPDATE  `variables` 
SET  `liste` =  '0:Pas informatisé\r\n1:Pergame\r\n2:Paprika\r\n3:Orphée\r\n4:Opsys\r\n5:Microbib\r\n6:Atalante\r\n7:Multilis\r\n8:Bibal\r\n9:Milord\r\n10:Elissa\r\n11:v-smart\r\n12:Koha'
WHERE  `variables`.`clef` =  'sigb';

UPDATE  `variables` SET  `liste` =  '0:aucun\r\n
1:pergame\r\n
2:web-service opsys\r\n
3:serveur Z39.50\r\n
4:web-service v-smart\r\n
5:web-service ILSDI Koha' WHERE  `variables`.`clef` =  'comm_sigb';


