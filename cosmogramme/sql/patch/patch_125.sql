alter table user_groups add column group_type tinyint unsigned default 0 not null after id;
alter table user_groups add column role_level tinyint unsigned default 0 not null after libelle;
