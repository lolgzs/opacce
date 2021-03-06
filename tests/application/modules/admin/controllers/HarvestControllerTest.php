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
require_once 'AdminAbstractControllerTestCase.php';

abstract class HarvestControllerArteVodTestCase extends Admin_AbstractControllerTestCase {
	protected $_web_client;

	public function setUp() {
		parent::setUp();

		$this->_web_client = Storm_Test_ObjectWrapper::mock()
			->whenCalled('open_url')->answers('')
			->whenCalled('setAuth')->answers(null);

		Class_WebService_ArteVOD::setDefaultWebClient($this->_web_client);
		Class_WebService_ArteVOD_Vignette::setInstance(Storm_Test_ObjectWrapper::mock()
																									 ->whenCalled('updateAlbum')
																									 ->answers(true));
	}
}



abstract class HarvestControllerArteVodNotActivatedTestCase extends HarvestControllerArteVodTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::getLoader()
			->newInstanceWithId('ARTE_VOD_LOGIN')
			->setValeur('');
	}
}



abstract class HarvestControllerArteVodActivatedTestCase extends HarvestControllerArteVodTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::newInstanceWithId('ARTE_VOD_LOGIN')->setValeur('user');
		Class_AdminVar::newInstanceWithId('ARTE_VOD_KEY')->setValeur('pass');
		Class_AdminVar::newInstanceWithId('ARTE_VOD_SSO_KEY')->setValeur('secret');
	}
}



abstract class HarvestControllerArteVodWithFilmTestCase extends HarvestControllerArteVodActivatedTestCase {
	protected $_categoryWrapper;
	protected $_albumWrapper;

	public function setUp() {
		parent::setUp();
		$this->_web_client
			->whenCalled('open_url')
			->with('http://www.mediatheque-numerique.com/ws/films')
			->answers(HarvestArteVODFixtures::films())

			->whenCalled('open_url')
			->with('http://www.mediatheque-numerique.com/ws/films/5540')
			->answers(HarvestArteVODFixtures::film())

			->whenCalled('setAuth')
			->with('user', 'pass')
			->answers(null)
			->beStrict();

		$this->_categoryWrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('findFirstBy')->answers(null)
			->whenCalled('save')->answers(true);

		$this->_albumWrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('findFirstBy')->answers(null)
			->whenCalled('save')->answers(true)
			->whenCalled('deleteBy')->answers(null);
	}


	/** @test */
	public function shouldDeleteNotHarvestedIds() {
		$this->assertTrue($this->_albumWrapper->methodHasBeenCalled('deleteBy'));
		$parameter = $this->_albumWrapper->getFirstAttributeForLastCallOn('deleteBy');
		$this->assertEquals(2, count($parameter));
		$this->assertTrue(false !== strpos($parameter[0], Class_WebService_ArteVOD::BASE_URL));
		$this->assertTrue(false !== strpos($parameter[1], "not in ('5540')"));
	}
}



class HarvestControllerArteVodActionNotActivatedTest extends HarvestControllerArteVodNotActivatedTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/harvest/arte-vod', true);
	}


	/** @test */
	public function shouldRedirect() {
		$this->assertRedirectTo('/admin/index');
	}
}



class HarvestControllerArteVodActivatedWithErrorTest extends HarvestControllerArteVodActivatedTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/harvest/arte-vod', true);		
	}


	/** @test */
	public function shouldLogHarvestStart() {
		$this->assertXPathContentContains('//div', 'Début du moissonnage');
	}


	/** @test */
	public function shouldLogHarvestError() {
		$this->assertXPathContentContains('//div', 'Erreur de communication');
	}
}




class HarvestControllerArteVodActivatedWithFilmsTest extends HarvestControllerArteVodWithFilmTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/harvest/arte-vod', true);		
	}


	/** @test */
	public function shouldLogReceivedResponse() {
		$this->assertXPathContentContains('//div', 'Réponse reçue');
	}


	/** @test */
	public function shouldLogTotalCount() {
		$this->assertXPathContentContains('//div', '1 films dans la base');
	}


	/** @test */
	public function shouldLogFirstPage() {
		$this->assertXPathContentContains('//div', 'Traitement de la page 1 / 1');
	}
}



class HarvestControllerArteVodAjaxNotActivatedTest extends HarvestControllerArteVodNotActivatedTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/harvest/arte-vod-ajax', true);
	}


	/** @test */
	public function shouldReturnEmptyResponse() {
		$this->assertEquals('', $this->_response->getBody());
	}
}



class HarvestControllerArteVodAjaxFirstPageTest extends HarvestControllerArteVodWithFilmTestCase {
	protected $_json;
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/harvest/arte-vod-ajax', true);
		$this->_json = json_decode($this->_response->getBody());
	}


	/** @test */
	public function responseShouldContainFilmsCount() {
		$this->assertEquals(1, $this->_json->total_count);
	}


	/** @test */
	public function responseShouldContainCurrentPage() {
		$this->assertEquals(1, $this->_json->current_page);
	}


	/** @test */
	public function responseShouldNotHaveNextPage() {
		$this->assertFalse($this->_json->has_next);
	}
}



class HarvestArteVODFixtures {
	public static function films() {
		return '<?xml version="1.0" encoding="utf-8"?><wsObjectListQuery page_nb="1" page_size="5" total_count="1" count="1">
<film href="/ws/films/5540"><pk>5540</pk><externalUri>http://www.mediatheque-numerique.com/films/blanche-neige</externalUri><editorial><title>Blanche Neige</title><description>Une adaptation drôle et poétique du conte des frères Grimm, dans une collection de théâtre pour jeune public.</description></editorial><media><posters><media src="http://media.universcine.com/7e/5c/7e5c210a-b4ad-11e1-b992-959e1ee6d61d.jpg"><modificationDate>2012-06-13T11:45:26</modificationDate></media></posters><trailers><media src="http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.mp4"><modificationDate>2012-04-03T08:31:24</modificationDate></media></trailers></media></film>
</wsObjectListQuery>';
	}


	public static function film() {
		return '<?xml version="1.0" encoding="utf-8"?><wsObjectQuery>
<film><pk>5540</pk><externalUri>http://www.mediatheque-numerique.com/films/blanche-neige</externalUri><editorial><title>Blanche Neige</title><description>Une adaptation drôle et poétique du conte des frères Grimm, dans une collection de théâtre pour jeune public.</description><original_title></original_title><body>La pomme, les sept nains, le cercueil de verre, le prince à cheval, le miroir magique... : le metteur en scène Nicolas Liautard a parié sur les images évoquées dans le conte pour faire du théâtre sans texte. Une succession de tableaux vivants, où le langage du corps, les jeux de lumière et la scénographie créent une féerie intemporelle qui sollicite l\'imaginaire des enfants.</body><genre code="drama"><label lang="fr">Dramatique</label></genre><tags></tags></editorial><technical><duration>70</duration><target_audience code="all-1"><label lang="fr">target_audience_all_1</label></target_audience><production_year>2011</production_year><production_countries><country code="FR"><label lang="fr">France</label></country></production_countries><codes><code type="ARTE">131333</code></codes><release_dates></release_dates><languages><language code="fr"><label lang="fr">Français</label></language></languages><copyright></copyright></technical><staff><authors><person><first_name>Florent</first_name><last_name>Trochel</last_name><full_name>Florent Trochel</full_name></person></authors><actors></actors></staff><media><posters><media src="http://media.universcine.com/7e/5c/7e5c210a-b4ad-11e1-b992-959e1ee6d61d.jpg"><modificationDate>2012-06-13T11:45:26</modificationDate></media></posters><trailers><media src="http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.mp4"><modificationDate>2012-04-03T08:31:24</modificationDate></media></trailers><photos><media src="http://media.universcine.com/7d/f8/7df8bc21-7d56-11e1-baed-69499da4469c.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media><media src="http://media.universcine.com/7e/14/7e142c4c-7d56-11e1-bef3-a980e4936291.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media><media src="http://media.universcine.com/7e/19/7e199359-7d56-11e1-9d9b-a9fbcefd86db.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media><media src="http://media.universcine.com/7e/1d/7e1dc11e-7d56-11e1-99aa-8775a2d902d1.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media><media src="http://media.universcine.com/7e/28/7e28cbe1-7d56-11e1-a80d-d78d88d4aa56.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media></photos></media></film></wsObjectQuery>';
	}
}
