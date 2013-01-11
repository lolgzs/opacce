ALTER TABLE `frbr_link` 
			ADD COLUMN `source_type` VARCHAR(255) NOT NULL, 
			ADD COLUMN `target_type` VARCHAR(255) NOT NULL,
			DROP INDEX `source`, 
			ADD INDEX `source` (`source`(255) ASC), 
			DROP INDEX `target`,
			ADD INDEX `target` (`target`(255) ASC),
			CHANGE COLUMN `source` `source` TEXT NOT NULL, 
			CHANGE COLUMN `target` `target` TEXT NOT NULL;
