CREATE TABLE `multimedia_location` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_origine` INT(11) UNSIGNED NOT NULL ,
  `libelle` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_origine` (`id_origine` ASC) )
ENGINE = MyISAM DEFAULT CHARACTER SET = utf8;

CREATE TABLE `multimedia_devicegroup` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_origine` INT(11) UNSIGNED NOT NULL,
	`id_location` INT(11) UNSIGNED NOT NULL,
  `libelle` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
	UNIQUE INDEX `id_origine` (`id_origine` ASC),
	INDEX `id_location` (`id_location` ASC))
ENGINE = MyISAM DEFAULT CHARACTER SET = utf8;

CREATE TABLE `multimedia_device` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_origine` INT(11) UNSIGNED NOT NULL,
  `id_devicegroup` INT(11) UNSIGNED NOT NULL,
  `libelle` VARCHAR(255) NOT NULL,
	`os` VARCHAR(255) NULL,
  PRIMARY KEY (`id`) ,
	UNIQUE INDEX `id_origine` (`id_origine` ASC),
  INDEX `id_devicegroup` (`id_devicegroup` ASC) )
ENGINE = MyISAM DEFAULT CHARACTER SET = utf8;
