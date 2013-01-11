CREATE TABLE newsletters (
  id int(11) NOT NULL AUTO_INCREMENT,
  last_distribution_date datetime DEFAULT NULL,
  titre varchar(50) NOT NULL,
  contenu text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE newsletters_users (
id int(11) NOT NULL AUTO_INCREMENT,
newsletter_id int(11) NOT NULL,
user_id int(11) NOT NULL,  PRIMARY KEY (id),
KEY newsletter_id (newsletter_id,user_id)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `notices` ADD `cote` VARCHAR( 25 ) NOT NULL AFTER `facettes` ;
ALTER TABLE `notices` ADD INDEX `cote` ( `cote` );

ALTER TABLE `catalogue` ADD `COTE_DEBUT` VARCHAR( 25 ) NOT NULL ;
ALTER TABLE `catalogue` ADD `COTE_FIN` VARCHAR( 25 ) NOT NULL ;