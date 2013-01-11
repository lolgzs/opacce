alter table album drop column type_doc;
alter table album add column type_doc_id int not null default 100 after fichier;
alter table album add column matiere varchar(100) not null after type_doc_id;
alter table album add column dewey varchar(100) not null after type_doc_id;
alter table album add column genre varchar(100) not null after type_doc_id;
alter table album add column id_langue varchar(3) not null after type_doc_id;
