ALTER TABLE sessions_formation DROP COLUMN lieu;
ALTER TABLE sessions_formation ADD COLUMN lieu_id int(11) NOT NULL;