alter table album add column type_doc tinyint not null after id;
alter table album add column annee varchar(4) not null after editeur;
update variables set commentaire='Types de documents.<br>ATTENTION :<br>- Modifier cette liste peut perturber les profils d\'import.<br>- Vous pouvez changer l\'ordre de la liste, mais en gardant le même code.<br>- Le code 0 ne doit pas être supprimé.<br>- Les ressources numériques sont identifiées par des codes compris entre 100 et 127.<br>- Les codes doivent être un nombre compris entre 0 et 127.'
where  clef='types_docs';

delete from variables where clef='cms_date';
delete from variables where clef='rss_date';
delete from variables where clef='sithotheque_date';