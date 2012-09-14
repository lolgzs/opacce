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

abstract class ZendAfi_View_Helper_RenderAlbumTestCase extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_RenderForm */
	protected $_helper;

	/** @var string */
	protected $_html;

	public function setUp() {
		parent::setUp();

		$view = new ZendAfi_Controller_Action_Helper_View();
		$this->_helper = new ZendAfi_View_Helper_RenderAlbum();
		$this->_helper->setView($view);
	}
}



class ZendAfi_View_Helper_RenderAlbumEPUBTest extends ZendAfi_View_Helper_RenderAlbumTestCase {
	public function setUp() {
		parent::setUp();

		$this->_album_epub = Class_Album::getLoader()
			->newInstanceWithId(999)
			->setLibelle('Versailles')
			->beEPUB()
			->setRessources(array(Class_AlbumRessource::getLoader()
														->newInstanceWithId(123)
														->setFichier('versailles.epub')
														->setIdAlbum(999)));

		$this->html = $this->_helper->renderAlbum($this->_album_epub);
	}


	/** @test */
	public function albumTypeDocShouldBe102() {
		$this->assertEquals(102, $this->_album_epub->getTypeDocId());
	}


	/** @test */
	public function pageShouldContainsLinkToDownloadEPUB() {
		$this->assertXPathContentContains($this->html, 
																			'//a[contains(@href, "'.BASE_URL.'/bib-numerique/download-resource/id/123")]',
																			'versailles.epub');
	}
}




class ZendAfi_View_Helper_RenderAlbumGallicaTest extends ZendAfi_View_Helper_RenderAlbumTestCase {
	public function setUp() {
		parent::setUp();

		$this->_album_gallica = Class_Album::getLoader()
			->newInstanceWithId(999)
			->setLibelle('Fleurs de nice')
			->beOAI()
			->setIdOrigine('http://gallica.bnf.fr/ark:/1234');

		$this->html = $this->_helper->renderAlbum($this->_album_gallica);
	}


	/** @test */
	public function pageShouldContainsGallicaPlayer() {
		$this->assertXPath($this->html,
											 '//object//param[@name="FlashVars"][contains(@value, "1234")]', 
											 $this->html);
	}
	
}


?>