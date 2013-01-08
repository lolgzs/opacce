ALTER TABLE `codif_annexe` ADD `no_pickup` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `codif_annexe` ADD INDEX `no_pickup` (`no_pickup` ASC);