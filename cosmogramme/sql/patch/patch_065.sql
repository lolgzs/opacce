CREATE TABLE IF NOT EXISTS `user_groups` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`libelle` varchar( 100 ) default NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `user_group_memberships` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`user_id` int(11) default NULL ,
`user_group_id` int(11) default NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

ALTER TABLE  `user_group_memberships` ADD INDEX  `user_id` (  `user_id` );
ALTER TABLE  `user_group_memberships` ADD INDEX  `user_group_id` (  `user_group_id` );