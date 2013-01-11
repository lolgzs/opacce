CREATE TABLE `frbr_linktype` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`libelle` VARCHAR(255) NOT NULL,
	`from_source` VARCHAR(255) NOT NULL,
	`from_target` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;


CREATE TABLE `frbr_link` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`type_id` VARCHAR(255) NOT NULL,
	`source` VARCHAR(255) NOT NULL,
	`target` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
	INDEX `type_id` (`type_id`),
	INDEX `source` (`source`),
	INDEX `target` (`target`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
