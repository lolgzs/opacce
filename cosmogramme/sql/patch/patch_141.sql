ALTER TABLE album_ressources ADD INDEX ( id_album );


ALTER TABLE bib_admin_profil ADD INDEX ( libelle );
ALTER TABLE bib_admin_profil ADD INDEX ( browser );

ALTER TABLE bib_admin_users ADD INDEX ( id_site );

ALTER TABLE bib_localisations ADD INDEX ( libelle );

ALTER TABLE cms_avis ADD INDEX ( statut );

ALTER TABLE codif_annexe ADD INDEX ( code );
ALTER TABLE codif_annexe ADD INDEX ( invisible );
ALTER TABLE codif_annexe ADD INDEX ( id_bib );


ALTER TABLE codif_section ADD INDEX ( libelle );

ALTER TABLE lieux ADD INDEX ( libelle );

ALTER TABLE newsletters_users ADD INDEX ( user_id );


ALTER TABLE notices_avis ADD INDEX ( statut );
ALTER TABLE notices_avis ADD INDEX ( date_avis );

ALTER TABLE prets ADD INDEX ( id_notice_origine );
ALTER TABLE prets ADD INDEX ( en_cours );

ALTER TABLE reservations ADD INDEX ( id_pergame );

ALTER TABLE rss_flux ADD INDEX ( id_cat );

ALTER TABLE sito_categorie ADD INDEX ( id_cat_mere );

ALTER TABLE sito_url ADD INDEX ( id_cat );
ALTER TABLE sito_url ADD INDEX ( titre );

ALTER TABLE user_groups ADD INDEX ( role_level );
ALTER TABLE user_groups ADD INDEX ( group_type );
