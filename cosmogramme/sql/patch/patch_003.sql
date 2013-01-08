-------------------------------------------------------------------------
-- modif Exemplaires, création table emplacement et modif table profils
-------------------------------------------------------------------------

ALTER TABLE `exemplaires` ADD `emplacement` SMALLINT NOT NULL AFTER `activite`;
ALTER TABLE `exemplaires` CHANGE `genre` `genre` SMALLINT NULL DEFAULT NULL ;
ALTER TABLE `exemplaires` CHANGE `section` `section` SMALLINT NOT NULL;

CREATE TABLE `codif_emplacement` (
  `id_emplacement` tinyint(4) NOT NULL auto_increment,
  `libelle` varchar(50) NOT NULL,
  `regles` text NOT NULL,
  `nb_notices` int(11) NOT NULL,
  PRIMARY KEY  (`id_emplacement`),
  KEY `nb_notices` (`nb_notices`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;

ALTER TABLE `bib_admin_profil` ADD `CFG_NOTICE` TEXT NOT NULL;