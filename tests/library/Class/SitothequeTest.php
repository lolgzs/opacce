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
class SitothequeTest extends Storm_Test_ModelTestCase {
	/** @test */
	function getSitesFromIdAndCategoriesShouldReturnFeeds() {
		$site_premiere = Class_Sitotheque::getLoader()->newInstanceWithId(26);
		$site_allocine = Class_Sitotheque::getLoader()->newInstanceWithId(27);
		$site_telerama = Class_Sitotheque::getLoader()->newInstanceWithId(28);

		$cat_cinema = Class_SitothequeCategorie::getLoader()->newInstanceWithId(4);
		$cat_cinema
			->setSousCategories(array())
			->setSitotheques(array($site_telerama));
		
		$sites = Class_Sitotheque::getLoader()->getSitesFromIdsAndCategories(array(26,27), array(4));

		$this->assertEquals(array($site_premiere, $site_allocine, $site_telerama),
												$sites);
	}

}
?>