INSERT INTO `variables` (`clef`,`commentaire`,`type_champ`,`liste`,`groupe`,`ordre`) VALUES ('nature_docs',"Nature de documents utilisé pour l'export OAI/Dublin core (codes de 1 à 12 réservés)",
2,'1:Collection\r\n2:Dataset\r\n3:Event\r\n4:Image\r\n5:Interactive resource\r\n6:Moving image\r\n7:Physical object\r\n8:Service\r\n9:Software\r\n10:Sound\r\n11:Still image\r\n12:Text\r\n13:Monographie imprimée\r\n14:Publication en série imprimée\r\n15:Image fixe\r\n16:Document cartographique\r\n17:Musique imprimée\r\n18:Enregistrement sonore\r\n19:Manuscrit\r\n20:Livre',2,8);

ALTER TABLE `album` ADD COLUMN `nature_doc` VARCHAR(50) not null default '';
