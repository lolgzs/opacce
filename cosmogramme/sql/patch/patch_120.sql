ALTER TABLE `user_groups` 
	ADD COLUMN `max_day` INT(11) UNSIGNED NOT NULL DEFAULT 0,
	ADD COLUMN `max_week` INT(11) UNSIGNED NOT NULL DEFAULT 0,
	ADD COLUMN `max_month` INT(11) UNSIGNED NOT NULL DEFAULT 0;
