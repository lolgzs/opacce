ALTER TABLE `notices` ADD `clef_chapeau` VARCHAR( 200 ) NOT NULL AFTER `clef_oeuvre` ;
ALTER TABLE `notices` ADD INDEX ( `clef_chapeau` );
ALTER TABLE `exemplaires` CHANGE `activite` `activite` VARCHAR( 100 );