insert into bib_admin_var(CLEF, VALEUR) values('CACHE_ACTIF', '1') 
on duplicate key update VALEUR='1';