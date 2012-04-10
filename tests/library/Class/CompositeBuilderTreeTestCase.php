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
abstract class CompositeBuilderTreeTestCase extends PHPUnit_Framework_TestCase {
	public function setUp() {
		/*
			root 0
			 |
			 +--- animation 1
			         |
							 +-- animation jeunesse 11
							       - pere noel 2
										 - carnaval 3
							 +-- animation adulte 12
							 - visite bib 4
			 + evenements 2
			      - fete internet 5
		 */
		$builder = new CompositeBuilder("ItemCategory", "BaseItem");
		$this->root = $builder->getRoot();
		$this->animation = $builder->newSubcategoryIn(1, 0);
		$this->animation_jeunesse = $builder->newSubcategoryIn(11, 1);
		$this->animation_adulte = $builder->newSubcategoryIn(12, 1);
		$this->evenements = $builder->newSubCategoryIn(2, 0);

		$this->rencontre_pere_noel = $builder->newItemIn(2, 11);
		$this->carnaval = $builder->newItemIn(3, 11);
		$this->visite_bib = $builder->newItemIn(4,1);
		$this->fete_internet = $builder->newItemIn(5,2);

		$this->categorie_fantome = $builder->newSubcategoryIn(99, 99);
		$this->article_fantome = $builder->newItemIn(99, 99);
	}
}
?>