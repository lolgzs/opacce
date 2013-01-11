ALTER TABLE `variables` CHANGE `liste` `liste` TEXT CHARACTER SET utf8 NOT NULL ; 
UPDATE `variables` SET `liste` =  '0:notices\r\n1:abonnés\r\n2:prêts\r\n3:réservations' WHERE `clef` =  'type_fichier';

delete from variables where clef='champs_pret';
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
'champs_pret', NULL , 'Champs pour la table des prêts (historique et prêts en cours)', '2',
'ID_PERGAME:id_pret Pergame\r\nIDABON:id abonné\r\nORDREABON:no d''ordre\r\nEN_COURS:prêt en cours\r\nDATE_PRET:date du prêt\r\nDATE_RETOUR:date de retour\r\nID_NOTICE_ORIGINE:id notice Pergame\r\nSUPPORT:code support\r\nETIQUETTE:numéro d''étiquette (incrémental)',
'5', '85', 'checked'
);

delete from variables where clef='champs_reservation';
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
'champs_reservation', NULL , 'Champs pour la table des réservations en cours', '2',
'ID_PERGAME:id_réservation Pergame\r\nIDABON:id abonné\r\nORDREABON:no d''ordre\r\nDATE_RESA:date de réservation\r\nID_NOTICE_ORIGINE:id notice Pergame\r\nSUPPORT:code support',
'5', '85', 'checked'
);

delete from variables where clef='mode_doublon';
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
'mode_doublon', '0' ,
'Mode de dédoublonnage pour les insertions de notices.<br>Le mode clef_alpha est utilisé quand les identifiants standards (isbn et ean) ne sont pas fiables dans le catalogage de la bibliothèque.',
'2', '0:normal (tous indentifiants)\r\n1:sur clef alpha',
'4', '98', 'checked'
);

--
-- Structure de la table `prets`
--

DROP TABLE IF EXISTS `prets`;
CREATE TABLE `prets` (
  `ID_PRET` int(11) NOT NULL auto_increment,
	`ID_SITE` int(11) NOT NULL,
	`ID_PERGAME` int(11) NOT NULL,
  `IDABON` int(11) NOT NULL,
  `ORDREABON` smallint(6) NOT NULL default '1',
  `EN_COURS` tinyint(1) NOT NULL,
  `DATE_PRET` varchar(10) NOT NULL,
  `DATE_RETOUR` varchar(10) NOT NULL,
  `ID_NOTICE_ORIGINE` int(11) NOT NULL,
  `SUPPORT` tinyint(4) NOT NULL,
  `ETIQUETTE` int(11) NOT NULL,
  PRIMARY KEY  (`ID_PRET`),
  KEY `IDABON` (`IDABON`),
  KEY `ETIQUETTE` (`ETIQUETTE`),
	KEY `ID_PERGAME` (`ID_PERGAME`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `ID_RESA` int(11) NOT NULL auto_increment,
	`ID_SITE` int(11) NOT NULL,
  `ID_PERGAME` int(11) NOT NULL,
  `IDABON` int(11) NOT NULL,
  `ORDREABON` smallint(6) NOT NULL default '1',
  `DATE_RESA` varchar(10) NOT NULL,
  `SUPPORT` tinyint(4) NOT NULL,
  `ID_NOTICE_ORIGINE` int(11) NOT NULL,
  PRIMARY KEY  (`ID_RESA`),
  KEY `IDABON` (`IDABON`),
  KEY `ID_NOTICE_ORIGINE` (`ID_NOTICE_ORIGINE`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
