ALTER TABLE  `bib_admin_profil` ADD  `PARENT_ID` INT  AFTER  `ID_PROFIL` , ADD INDEX (  `PARENT_ID` );