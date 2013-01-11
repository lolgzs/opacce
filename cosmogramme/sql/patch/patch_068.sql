ALTER TABLE  `sessions_formation` ADD horaires varchar(100) NOT NULL;
ALTER TABLE  `sessions_formation` ADD lieu varchar(250) NOT NULL;
ALTER TABLE  `sessions_formation` ADD date_limite_inscription date NOT NULL;
ALTER TABLE  `sessions_formation` CHANGE date_debut date_debut date;
ALTER TABLE  `formations` ADD description TEXT NOT NULL;
