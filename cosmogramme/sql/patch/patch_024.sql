DROP TABLE IF EXISTS `notices_articles`;
CREATE TABLE IF NOT EXISTS `notices_articles` (
  `id_article` int(11) NOT NULL auto_increment,
  `clef_chapeau` varchar(100) NOT NULL,
  `clef_numero` varchar(20) NOT NULL,
  `clef_article` varchar(20) NOT NULL,
  `clef_unimarc` varchar(15) NOT NULL,
  `unimarc` text NOT NULL,
  `date_maj` varchar(20) NOT NULL,
  `qualite` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_article`),
  KEY `clef_chapeau` (`clef_chapeau`,`clef_numero`),
  KEY `clef_unimarc` (`clef_unimarc`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

delete from variables where clef='id_article_periodique';
insert into variables (`clef`, `valeur`, `commentaire`, `type_champ`, `liste`, `groupe`, `ordre`, `verrou`)
	values('id_article_periodique'
				,''
				,'Mode de reconnaissance pour les articles de périodiques.'
				, 2
				, '0:aucun\r\n1:pergame\r\n\r\n2:opsys indexpresse\r\n'
				, 2, 6, 'checked');

ALTER TABLE `profil_donnees` ADD `id_article_periodique` TINYINT NOT NULL AFTER `rejet_periodiques` ;
ALTER TABLE `integrations` ADD INDEX ( `traite` );

delete from `variables` where clef='heure_limite_integration';
