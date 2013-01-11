-------------------------------------------------------------------------
-- Localisations
-------------------------------------------------------------------------

DROP TABLE IF EXISTS `bib_localisations`;
CREATE TABLE `bib_localisations` (
  `ID_LOCALISATION` int(11) NOT NULL auto_increment,
  `ID_BIB` int(11) NOT NULL,
  `ID_PLAN` int(11) NOT NULL,
  `LIBELLE` varchar(100) NOT NULL,
  `DESCRIPTION` text NOT NULL,
  `POS_X` float NOT NULL,
  `POS_Y` float NOT NULL,
  `TYPE_DOC` varchar(30) NOT NULL,
  `SECTION` varchar(30) NOT NULL,
  `EMPLACEMENT` varchar(30) NOT NULL,
  `COTE_DEBUT` varchar(15) NOT NULL,
  `COTE_FIN` varchar(15) NOT NULL,
  `IMAGE` varchar(100) NOT NULL,
	`ANIMATION` VARCHAR( 30 ) NOT NULL,
  PRIMARY KEY  (`ID_LOCALISATION`),
  KEY `ID_BIB` (`ID_BIB`),
  KEY `ID_PLAN` (`ID_PLAN`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
