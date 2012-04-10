<?php
/**
 * Copyright (c) 2012, Agence Française Informatique (AFI). All rights reserved.
 *
 * AFI-OPAC 2.0 is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation.
 *
 * There are special exceptions to the terms and conditions of the AGPL as it
 * is applied to this software (see README file).
 *
 * AFI-OPAC 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with AFI-OPAC 2.0; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301  USA 
 */

/*
			root 0
			 |
			 +--- animation 1
							 |
							 +-- animation jeunesse 11
										 - pere noel 2
										 - carnaval 3 
										 + tout petit
							 +-- animation adulte 12
								 - visite bib 4
			 + evenements 2
						- fete internet 5
*/

class CmsResultSetFixtures {
	public static function articles(){
		return array(
								 array("ID_ARTICLE" => 2, "ID_CAT" => 11, "TITRE" => "Pere Noel"),
								 array("ID_ARTICLE" => 3, "ID_CAT" => 11, "TITRE" => "Carnaval"),
								 array("ID_ARTICLE" => 4, "ID_CAT" => 12, "TITRE" => "Visite bib."),
								 array("ID_ARTICLE" => 5, "ID_CAT" => 2, "TITRE" => "Fete internet"),
								 array("ID_ARTICLE" => 99, "ID_CAT" => 99, "TITRE" => "Article fantôme."));
	}


	public static function categories(){
		return array(
								 array("ID_CAT" => 1,  "ID_CAT_MERE" => 0, "LIBELLE" => "Animation"),
								 array("ID_CAT" => 11, "ID_CAT_MERE" => 1, "LIBELLE" => "Animation jeunesse"),
								 array("ID_CAT" => 111, "ID_CAT_MERE" => 11, "LIBELLE" => "Tout petit"),
								 array("ID_CAT" => 12, "ID_CAT_MERE" => 1, "LIBELLE" => "Animation adulte"),
								 array("ID_CAT" => 2,  "ID_CAT_MERE" => 0, "LIBELLE" => "Evenements"),
								 array("ID_CAT" => 99, "ID_CAT_MERE" => 99, "LIBELLE" => "Catégorie fantôme"));
	}
} 
?>