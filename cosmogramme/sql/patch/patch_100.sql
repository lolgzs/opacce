CREATE TABLE `opds_catalogs` (
  `id` INT(11) UNSIGNED auto_increment NOT NULL ,
  `libelle` VARCHAR(255) NOT NULL ,
  `url` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE = MyISAM;