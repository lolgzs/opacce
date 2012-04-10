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

class AlbumCategorieRootTest extends ModelTestCase {
	public function setUp() {
		$this->root = new Class_AlbumCategorie;
		$this->root
			->setId(0)
			->setLibelle('ROOT')
			->setSousCategories(array());
			
		$this->cat_jeunesse = Class_AlbumCategorie::getLoader()
			->newInstanceWithId(23)
			->setLibelle('Jeunesse')
			->setSousCategories(array())
			->setParentCategorie($this->root);

		$this->root->addSousCategorie($this->cat_jeunesse);

		$this->album_tintin = Class_Album::getLoader()
			->newInstanceWithId(5)
			->setLibelle('Tintin')
			->setCategorie($this->cat_jeunesse);

		$this->album_lagaffe = Class_Album::getLoader()
			->newInstanceWithId(9)
			->setLibelle('Gaston Lagaffe')
			->setCategorie($this->cat_jeunesse);

		$this->cat_jeunesse
			->setAlbums(array($this->album_tintin,
												$this->album_lagaffe));
	}


	/** @test */
	public function shouldNotHaveParentCategory() {
		$this->assertFalse($this->root->hasParentCategorie());
	}
	

	/** @test */
	public function libelleShouldBeRoot() {
		$this->assertEquals('ROOT', $this->root->getLibelle());
	}


	/** @test */
	public function getSousCategoriesShouldReturnAnArrayWithAlbumJeunesse() {
		$this->assertEquals(array($this->cat_jeunesse), $this->root->getSousCategories());
	}

	/** @test */
  public function catJeunesseGetAlbumsShouldReturnLagaffeAndTintin() {
		$this->assertEquals(array($this->album_tintin, $this->album_lagaffe),
												$this->cat_jeunesse->getAlbums());
	}


	/** @test */
  public function albumTintinCategorieShouldBeCatJeunesse() {
		$this->assertEquals($this->cat_jeunesse->getId(), 
												$this->album_tintin->getCategorie()->getId());
	}


	/** @test */
	public function albumTintinHierarchyShouldIncludeCatJeunesseAndRoot() {
		$this->assertEquals(array($this->cat_jeunesse, $this->root), 
												$this->album_tintin->getHierarchy());
	}


	/** @test */
	public function catJeunesseHierarchyShouldIncludeRoot() {
		$this->assertEquals(array($this->root), 
												$this->cat_jeunesse->getHierarchy());
	}


}


?>