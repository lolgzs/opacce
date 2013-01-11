alter table cms_avis drop primary key;
alter table cms_avis add column id int not null auto_increment primary key first;
alter table cms_avis add index user_cms (id_user, id_cms);
