CREATE TABLE IF NOT EXISTS `lieux` (
  `id` int(11) NOT NULL auto_increment,
  `libelle` varchar(50) NOT NULL,
  `adresse` varchar(250) NOT NULL,
  `code_postal` varchar(50) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `pays` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
