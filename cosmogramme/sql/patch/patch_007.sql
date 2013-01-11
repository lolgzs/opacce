-------------------------------------------------------------------------
-- Plans et localisations
-------------------------------------------------------------------------

CREATE TABLE `bib_plans` (
`ID_PLAN` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ID_BIB` INT NOT NULL ,
`LIBELLE` VARCHAR( 100 ) NOT NULL ,
`DESCRIPTION` TEXT NOT NULL ,
`IMAGE` VARCHAR( 100 ) NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `bib_plans` ADD INDEX ( `ID_BIB` );

CREATE TABLE `bib_localisations` (
`ID_LOCALISATION` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ID_BIB` INT NOT NULL ,
`ID_PLAN` INT NOT NULL ,
`LIBELLE` VARCHAR( 100 ) NOT NULL ,
`DESCRIPTION` TEXT NOT NULL ,
`POS_X` FLOAT NOT NULL ,
`POS_Y` FLOAT NOT NULL ,
`PARAMS` TEXT NOT NULL ,
`IMAGE` VARCHAR( 100 ) NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `bib_localisations` ADD INDEX ( `ID_BIB` ) ;
ALTER TABLE `bib_localisations` ADD INDEX ( `ID_PLAN` ) ;

ALTER TABLE `bib_c_zone` ADD `COULEUR_TEXTE` VARCHAR( 7 ) NOT NULL ;
ALTER TABLE `bib_c_zone` ADD `COULEUR_OMBRE` VARCHAR( 7 ) NOT NULL ;
ALTER TABLE `bib_c_zone` ADD `TAILLE_FONTE` VARCHAR( 5 ) NOT NULL ;