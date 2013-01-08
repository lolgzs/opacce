CREATE TRIGGER datemaj_notices_update BEFORE DELETE
			ON exemplaires
			FOR EACH ROW
			BEGIN
			update notices  set date_maj=NOW() where id_notice=OLD.id_notice;
			END