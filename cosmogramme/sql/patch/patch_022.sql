DROP TABLE IF EXISTS `etageres`;
CREATE TABLE IF NOT EXISTS `etageres` (
  `id_etagere` int(11) NOT NULL auto_increment,
  `id_mere` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL,
  `vignette` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `requete` text NOT NULL,
  PRIMARY KEY  (`id_etagere`),
  KEY `id_mere` (`id_mere`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
