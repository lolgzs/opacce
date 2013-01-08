-------------------------------------------------------------------------
-- Changements des tables en MyIsam
-------------------------------------------------------------------------

ALTER TABLE `cms_categorie` ADD INDEX ( `ID_CAT_MERE` ) ;
UPDATE `variables` SET `liste` = '0:utf-8\r\n1:iso 2709\r\n2:windows ansi\r\n3:accents dos' WHERE `clef`='transco_accents' LIMIT 1 ;
ALTER TABLE `bib_admin_profil`  ENGINE = MYISAM ;
ALTER TABLE `bib_admin_users`  ENGINE = MYISAM ;
ALTER TABLE `bib_admin_users_non_valid`  ENGINE = MYISAM ;
ALTER TABLE `bib_admin_var`  ENGINE = MYISAM ;
ALTER TABLE `bib_config`  ENGINE = MYISAM ;
ALTER TABLE `bib_c_site`  ENGINE = MYISAM ;
ALTER TABLE `bib_c_zone`  ENGINE = MYISAM ;
ALTER TABLE `catalogue`  ENGINE = MYISAM ;
ALTER TABLE `cms_article`  ENGINE = MYISAM ;
ALTER TABLE `cms_categorie`  ENGINE = MYISAM ;
ALTER TABLE `int_analyse`  ENGINE = MYISAM ;
ALTER TABLE `rss_categorie`  ENGINE = MYISAM ;
ALTER TABLE `rss_flux`  ENGINE = MYISAM ;
ALTER TABLE `sito_url`  ENGINE = MYISAM ;
ALTER TABLE `sito_categorie`  ENGINE = MYISAM ;
ALTER TABLE `int_maj_auto`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `integrations`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `variables`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;