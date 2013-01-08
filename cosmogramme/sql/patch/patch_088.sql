alter table sessions_formation add column is_annule bool not null default false;
alter table `bib_admin_users` change column telephone TELEPHONE varchar(25) not null;