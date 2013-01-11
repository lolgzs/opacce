CREATE TABLE IF NOT EXISTS `album_categorie` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`parent_id` int( 11 ) NOT NULL default '0',
`libelle` varchar( 100 ) default NULL ,
`site_id` smallint( 6 ) NOT NULL default '0',
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `album` (
`id` smallint( 6 ) NOT NULL AUTO_INCREMENT ,
`cat_id` smallint( 6 ) NOT NULL ,
`notice_id` int( 11 ) NOT NULL default '0',
`titre` varchar( 100 ) NOT NULL ,
`description` text,
`date_maj` datetime NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;