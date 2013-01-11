CREATE TABLE `ouvertures` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_site` INT(11) UNSIGNED NOT NULL ,
	`jour` DATE NOT NULL ,
  `debut_matin` TIME NOT NULL ,
  `fin_matin` TIME NOT NULL ,
  `debut_apres_midi` TIME NOT NULL ,
  `fin_apres_midi` TIME NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
