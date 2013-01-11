ALTER TABLE `cms_article` ADD COLUMN `ID_LIEU` int after ID_NOTICE;
ALTER TABLE `cms_article` ADD INDEX (ID_LIEU);

