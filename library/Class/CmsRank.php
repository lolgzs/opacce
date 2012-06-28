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


class Class_CmsRank extends Storm_Model_Abstract {
	protected $_table_name = 'cms_rank';
	protected $_belongs_to = array('article' => array('model' => 'Class_Article',
			                                              'referenced_in' => 'id_cms'));
	protected $_default_attribute_values = array(
																					'abon_nombre_avis' => 0,
																					'bib_nombre_avis' => 0,
																					'abon_note' => 0,
	                                        'bib_note' => 0);

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	
	public function getNbAvisTotal() {
		return $this->getBibNombreAvis() + $this->getAbonNombreAvis();
	}
}

		
?>