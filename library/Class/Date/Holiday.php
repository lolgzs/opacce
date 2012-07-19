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

class Class_Date_Holiday {
	public static function getTimestampsForYear($year = null) {
		if (null == $year)
			$year = (int) date('Y');

		$stamps = array(
			mktime(0, 0, 0, 1,  1,  $year),  // 1er janvier
			mktime(0, 0, 0, 5,  1,  $year),  // Fête du travail
			mktime(0, 0, 0, 5,  8,  $year),  // Victoire des alliés
			mktime(0, 0, 0, 7,  14, $year),  // Fête nationale
			mktime(0, 0, 0, 8,  15, $year),  // Assomption
			mktime(0, 0, 0, 11, 1,  $year),  // Toussaint
			mktime(0, 0, 0, 11, 11, $year),  // Armistice
			mktime(0, 0, 0, 12, 25, $year),  // Noel
		);

		// dates en rapport avec paques
		// php ne les calcul que pour des timestamp valides
		if (1970 > $year or 2037 < $year)
			return $stamps;
		
		$easterDate = easter_date($year);
		$easterDay = date('j', $easterDate);
		$easterMonth = date('n', $easterDate);
		$easterYear = date('Y', $easterDate);

		$stamps[] = $easterDate; // paques
		$stamps[] = mktime(0, 0, 0, $easterMonth, $easterDay + 1,  $easterYear); // lundi de paques
		$stamps[] = mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear); // ascension
		$stamps[] = mktime(0, 0, 0, $easterMonth, $easterDay + 49, $easterYear); // pentecote
		$stamps[] = mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear); // lundi pentecote

		sort($stamps);
		return $stamps;
	}
}