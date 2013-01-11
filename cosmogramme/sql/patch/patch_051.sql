ALTER TABLE `codif_auteur` ADD `mots_renvois` TEXT NOT NULL AFTER `formes` ;
ALTER TABLE `codif_matiere` ADD `mots_renvois` TEXT NOT NULL AFTER `code_alpha` ;