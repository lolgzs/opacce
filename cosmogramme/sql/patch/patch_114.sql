ALTER TABLE `multimedia_location` 
	ADD COLUMN `slot_size` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN `max_slots` TINYINT UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE `multimedia_device` 
  ADD COLUMN `disabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0, 
  ADD INDEX `disabled` (`disabled` ASC) ;


CREATE TABLE `multimedia_devicehold` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_device` INT(11) UNSIGNED NOT NULL ,
  `id_user` INT(11) NOT NULL,
  `start` INT(11) UNSIGNED NOT NULL ,
  `end` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `id_device` (`id_device` ASC) ,
	INDEX `id_user` (`id_user` ASC) ,
  INDEX `start` (`start` ASC) ,
  INDEX `end` (`end` ASC)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
