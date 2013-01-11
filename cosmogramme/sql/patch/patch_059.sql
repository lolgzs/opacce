alter table album add column auteur varchar(250) not null after titre;
alter table album add column editeur varchar(250) not null after auteur;
alter table album add column tags text not null after description;