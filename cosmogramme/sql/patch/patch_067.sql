CREATE TABLE IF NOT EXISTS `session_formation_interventions` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`session_intervention_id` int(11) default NULL ,
`intervenant_id` int(11) default NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

ALTER TABLE  `session_formation_interventions` ADD INDEX  `session_intervention_id` (  `session_intervention_id` );
ALTER TABLE  `session_formation_interventions` ADD INDEX  `intervenant_id` (  `intervenant_id` );
