CREATE TABLE IF NOT EXISTS `album_ressources` (
`id` smallint( 6 ) NOT NULL AUTO_INCREMENT ,
`id_album` smallint( 6 ) NOT NULL ,
`ordre` smallint( 6 ) NOT NULL default '0',
`titre` varchar( 100 ) NOT NULL ,
`description` text NOT NULL ,
`fichier` varchar( 20 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;