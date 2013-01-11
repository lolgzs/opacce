-------------------------------------------------------------------------
-- Annexes
-------------------------------------------------------------------------

CREATE TABLE `codif_annexe` (
`id_annexe` SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_bib` INT NOT NULL ,
`code` VARCHAR( 10 ) NOT NULL ,
`libelle` VARCHAR( 50 ) NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `exemplaires` ADD `annexe` VARCHAR(10) NOT NULL AFTER `emplacement` ;
ALTER TABLE `bib_localisations` ADD `ANNEXE` VARCHAR( 10 ) NOT NULL AFTER `TYPE_DOC` ;