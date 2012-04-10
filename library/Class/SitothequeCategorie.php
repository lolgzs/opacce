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
class Class_SitothequeCategorie extends Storm_Model_Abstract {
	protected $_table_name = 'sito_categorie';
	protected $_table_primary = 'ID_CAT';
	protected $_belongs_to = array('parent_categorie' => array('model' => 'Class_SitothequeCategorie',
																														 'referenced_in' => 'ID_CAT_MERE'),
																 'bib' => array('model' => 'Class_Bib',
																								'referenced_in' => 'ID_SITE'));

	protected $_has_many = array('sous_categories' => array('model' => 'Class_SitothequeCategorie',
																													 'role' => 'parent_categorie',
																													 'dependents' => 'delete'),

                                 'sitotheques' => array('model' => 'Class_Sitotheque',
																												'role' => 'categorie',
																												'dependents' => 'delete'));


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	/**
	 * @return array
	 */
	public function getRecursiveSousCategories() {
		$all_categories = $sous_categories = $this->getSousCategories();
		foreach ($sous_categories as $categorie)
			$all_categories = array_merge($all_categories, $categorie->getRecursiveSousCategories());
		return $all_categories;
	}
}
?>