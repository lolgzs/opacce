CREATE TABLE `oai_notices` (
  `id` int(11) NOT NULL auto_increment,
  `id_entrepot` int(11) NOT NULL,
  `date` varchar(10) NOT NULL,
  `id_oai` varchar(100) NOT NULL,
  `alpha_titre` varchar(250) NOT NULL,
  `recherche` text NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_entrepot` (`id_entrepot`,`id_oai`),
  KEY `alpha_titre` (`alpha_titre`),
  FULLTEXT KEY `recherche` (`recherche`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `oai_entrepots` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`libelle` VARCHAR( 100 ) NOT NULL ,
`handler` VARCHAR( 100 ) NOT NULL
) ENGINE = MYISAM ;

 ALTER TABLE `oai_entrepots` ADD UNIQUE (`handler`);
