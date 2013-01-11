-------------------------------------------------------------------------
-- Ajouts des colonnes compteurs dans les autorites
-------------------------------------------------------------------------
--
-- Structure de la table `sito_url`
--

CREATE TABLE `sito_url` (
  `ID_SITO` smallint(6) NOT NULL auto_increment,
  `ID_CAT` smallint(6) NOT NULL,
  `ID_NOTICE` int(11) NOT NULL default '0',
  `TITRE` varchar(100) NOT NULL,
  `DESCRIPTION` text,
  `URL` text,
  `DATE_MAJ` datetime NOT NULL,
  `TAGS` text NOT NULL,
  PRIMARY KEY  (`ID_SITO`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;




alter table codif_auteur add date_creation varchar(10) NOT NULL;
alter table codif_auteur add id_bnf varchar(20) NOT NULL;
alter table codif_auteur add nb_notices int NOT NULL;
alter table codif_auteur add index (date_creation);
alter table codif_auteur add index (nb_notices);

alter table codif_matiere add date_creation varchar(10) NOT NULL;
alter table codif_matiere add id_bnf varchar(20) NOT NULL;
alter table codif_matiere add nb_notices int NOT NULL;
alter table codif_matiere add index (date_creation);
alter table codif_matiere add index (nb_notices);

alter table codif_dewey add nb_notices int NOT NULL;
alter table codif_dewey add index (nb_notices);

alter table codif_pcdm4 add nb_notices int NOT NULL;
alter table codif_pcdm4 add index (nb_notices);

alter table codif_genre add nb_notices int NOT NULL;
alter table codif_genre add index (nb_notices);

alter table codif_langue add nb_notices int NOT NULL;
alter table codif_langue add index (nb_notices);

alter table codif_section add nb_notices int NOT NULL;
alter table codif_section add index (nb_notices);

alter table codif_tags add nb_notices int NOT NULL;
alter table codif_tags add index (nb_notices);