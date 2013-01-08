-------------------------------------------------------------------------
-- modif Exemplaires, création table emplacement et modif table profils
-------------------------------------------------------------------------

 ALTER TABLE `cms_article` CHANGE `DEBUT` `DEBUT` DATE NULL DEFAULT NULL;
 ALTER TABLE `cms_article` CHANGE `FIN` `FIN` DATE NULL DEFAULT NULL ;
 ALTER TABLE `cms_article` CHANGE `EVENTS_DEBUT` `EVENTS_DEBUT` DATE NULL DEFAULT NULL;
 ALTER TABLE `cms_article` CHANGE `EVENTS_FIN` `EVENTS_FIN` DATE NULL DEFAULT NULL;