-------------------------------------------------------------------------
-- Annexes
-------------------------------------------------------------------------
ALTER TABLE `bib_admin_profil` ADD `SEL_TYPE_DOC` VARCHAR( 50 ) NOT NULL AFTER `ID_SITE`;
ALTER TABLE `bib_admin_profil` ADD `SEL_SECTION` VARCHAR( 50 ) NOT NULL AFTER `SEL_TYPE_DOC` ;