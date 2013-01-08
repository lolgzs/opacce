CREATE TABLE IF NOT EXISTS `formations` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`libelle` varchar( 100 ) default NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `sessions_formation` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`formation_id` int(11) default NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

ALTER TABLE  `sessions_formation` ADD INDEX  `formation_id` (  `formation_id` );
ALTER TABLE  `sessions_formation` ADD date_debut datetime NOT NULL;
ALTER TABLE  `sessions_formation` ADD effectif_min tinyint NOT NULL;
ALTER TABLE  `sessions_formation` ADD effectif_max tinyint NOT NULL;


CREATE TABLE IF NOT EXISTS `session_formation_inscriptions` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`session_formation_id` int(11) default NULL ,
`stagiaire_id` int(11) default NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

ALTER TABLE  `session_formation_inscriptions` ADD INDEX  `session_formation_id` (  `session_formation_id` );
ALTER TABLE  `session_formation_inscriptions` ADD INDEX  `stagiaire_id` (  `stagiaire_id` );
