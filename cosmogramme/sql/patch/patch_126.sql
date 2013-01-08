CREATE TABLE `suggestion_achat` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL ,
	`date_creation` DATE NOT NULL ,
	`titre` VARCHAR(100) NOT NULL ,
	`auteur` VARCHAR(100) NOT NULL ,
	`description_url` VARCHAR(255) NOT NULL , 
	`isbn` VARCHAR(20) NOT NULL ,
	`commentaire` TEXT NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
