ALTER TABLE `album` 
	ADD COLUMN `url_origine` VARCHAR(255) NULL DEFAULT NULL, 
  ADD INDEX `url_origine` (`url_origine` ASC);