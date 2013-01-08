DROP TABLE IF EXISTS `codif_interet`;
CREATE TABLE IF NOT EXISTS `codif_interet` (
  `id_interet` int(11) NOT NULL auto_increment,
  `libelle` varchar(250) NOT NULL,
  `code_alpha` varchar(250) NOT NULL,
  PRIMARY KEY  (`id_interet`),
  KEY `code_alpha` (`code_alpha`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `catalogue` ADD `INTERET` VARCHAR( 100 ) NOT NULL ;