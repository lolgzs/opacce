CREATE TABLE `formulaires` (
       `id`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
       `id_article` INT(11) UNSIGNED NOT NULL,
       `id_user`  INT(11) UNSIGNED NOT NULL,
       `date_creation` DATETIME NOT NULL,
       `data` TEXT,
       PRIMARY KEY (`id`),	
       KEY `id_article` (`id_article`),
       KEY `id_user` (`id_user`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;    
