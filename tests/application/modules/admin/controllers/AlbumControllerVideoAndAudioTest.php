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

require_once 'AlbumControllerTest.php';

class Admin_AlbumControllerVideoAndAudio extends Admin_AlbumControllerTestCase {
	protected $_xpath;

	public function setUp() {
		parent::setUp();

		$album = Class_Album::newInstanceWithId(999)
			->beDiaporama()
			->setTitre('Plein de medias')
			->setRessources([Class_AlbumRessource::newInstanceWithId(2)
											 ->setFichier('mimi_jolie.mp3')
											 ->setTitre('Emilie jolie')
											 ->setVignette('mimi_jolie.png'),
											 
											 Class_AlbumRessource::newInstanceWithId(4)
											 ->setFichier('dark_night.mp4')
											 ->setTitre('Batman Dark Knight')
											 ->setVignette('batman.jpg'),

											 Class_AlbumRessource::newInstanceWithId(4)
											 ->setUrl('http://progressive.totaleclips.com.edgesuite.net/107/e107950_227.mp4')
											 ->setTitre('Hunger Games')
											 ->setVignette('hunger.jpg')]);

		$this->dispatch('/admin/album/preview_album/id/999', true);
		$this->_xpath = new Storm_Test_XPath();
	}


	/** @test */
	public function osmPlayerJsShouldBeLoaded() {
		$this->_xpath->assertXpath(
			Class_ScriptLoader::getInstance()->html(),
			'//script[contains(@src, "admin/js/osmplayer/src/osmplayer.js")]');		
	}


	/** @test */
	public function osmPlayerCssShouldBeLoaded() {
		$this->_xpath->assertXpath(
			Class_ScriptLoader::getInstance()->html(),
			'//link[contains(@href, "admin/js/osmplayer/templates/default/css/osmplayer_default.css")]');		
	}


	/** @test */
	public function osmplayerShouldLoadXspfPlaylist() {
		$this->_xpath->assertXpathContentContains(
			Class_ScriptLoader::getInstance()->html(),
			'//script',
			'"playlist":"\/bib-numerique\/album-xspf-playlist\/id\/999.xml"'
		);
	}


	/** @test */
	public function pageShouldContainsDivIdOsmplayer999() {
		$this->assertXpath('//div[@id="osmplayer999"]');
	}
}


?>