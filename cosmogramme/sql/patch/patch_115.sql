update variables set liste=
'
0:Pas informatisé\r\n
1:Pergame\r\n
2:Paprika\r\n
3:Orphée\r\n
4:Opsys\r\n
5:Microbib\r\n
6:Atalante\r\n
7:Multilis\r\n
8:Bibal\r\n
9:Milord\r\n
10:Elissa\r\n
11:v-smart\r\n
12:Koha\r\n
13:Nanook\r\n
14:Carthame\r\n
15:Dynix\r\n
16:BiblixNet\r\n
'
where clef='sigb';


UPDATE  `variables` SET  `liste` =  '0:aucun\r\n
1:pergame\r\n
2:web-service Opsys\r\n
3:serveur Z39.50\r\n
4:web-service V-Smart\r\n
5:web-service Koha\r\n
6:web-service Carthame\r\n
7:web-service AFI-Nanook\r\n
8:web-service Orphée\r\n
9:web-service Microbib\r\n
10:web-service BiblixNet' WHERE  `variables`.`clef` =  'comm_sigb';
