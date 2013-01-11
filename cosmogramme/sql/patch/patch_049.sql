ALTER TABLE `etageres` ADD `groupe` SMALLINT NOT NULL AFTER `id_etagere` ;
ALTER TABLE `etageres` ADD `type_doc` VARCHAR( 20 ) NOT NULL ;
ALTER TABLE `etageres` ADD `section` VARCHAR( 20 ) NOT NULL ;
ALTER TABLE `etageres` DROP `image`;
