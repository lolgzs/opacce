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
require_once 'AbstractControllerTestCase.php';


class BibControllerIndexActionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$coords = array(
										"93,14,87,20,84,30,79,40,75,53,69,65,64,77,64,81,67,84,69,96,77,101,82,104,90,105,96,100,101,95,106,95,117,96,128,97,132,91,139,83,145,73,146,65,152,63,158,62,159,59,165,60,168,66,171,58,169,51,137,36,128,37,125,32,126,28,118,26,119,20,115,15,101,13" ,
										"65,96,61,107,55,117,48,130,45,140,47,151,50,157,56,160,64,160,71,153,79,145,87,146,90,139,97,128,105,125,117,126,130,129,139,130,152,134,164,136,170,139,172,147,189,153,191,157,192,161,204,162,209,155,215,144,213,135,205,124,199,117,201,111,199,103,201,94,200,84,187,82,184,76,173,71,163,68,157,65,151,67,150,72,148,79,142,85,138,90,136,98,127,101,116,101,105,101,98,106,85,108,76,108" ,
										"39,138,33,146,32,155,29,166,30,172,30,179,27,187,28,194,30,200,26,206,18,213,12,216,16,225,27,231,39,234,51,241,60,243,66,249,74,255,82,254,90,255,101,257,107,262,112,260,104,249,107,243,101,233,106,227,120,228,123,222,128,210,128,204,116,206,105,200,96,197,88,198,84,192,81,185,73,178,64,179,58,164" ,
										"59,162,64,171,71,175,79,180,86,184,87,191,94,193,102,196,113,201,121,202,133,201,149,200,163,200,176,201,188,196,201,196,201,186,201,178,208,170,205,165,185,166,181,161,187,158,181,155,169,150,167,142,153,139,142,136,127,132,114,129,101,130,95,143,91,149,81,150,71,158" ,
										"137,205,133,210,128,222,124,230,114,231,107,232,108,241,109,247,113,256,117,258,118,250,126,247,133,247,134,253,133,261,140,268,147,259,157,255,170,248,182,247,186,248,197,248,201,238,200,226,197,217,200,202,194,199,180,203,165,204,150,205" ,
										"65,255,67,264,68,275,66,282,59,286,64,300,71,299,80,305,92,320,98,333,105,341,117,341,111,350,109,359,122,361,128,365,145,362,160,362,174,357,182,348,172,341,178,331,174,322,174,318,163,315,162,307,165,300,160,294,148,292,146,282,142,275,135,269,132,261,129,252,121,255,121,261,114,265,103,266,99,262,85,258" ,
										"153,261,146,267,148,274,149,280,151,286,157,290,165,291,171,299,167,308,175,314,178,324,183,334,187,338,192,338,196,335,195,326,202,322,194,318,200,314,206,303,212,300,217,299,217,291,213,284,208,276,198,267,194,259,191,253,181,251,168,252");

		$zones = array();
		for($i=1; $i<=7; $i++) {
			$zones []= Class_Zone::getLoader()
				->newInstanceWithId($i)
				->setCouleur('#000')
				->setLibelle(sprintf('zone%d', $i))
				->setMapCoords($coords[$i-1])
				->setBibs(array(Class_Bib::getLoader()
												->newInstanceWithId($i)
												->setVisibilite(Class_Bib::V_DATA)
												->setLibelle(sprintf('BibZone%d', $i))));
		}

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Zone')
			->whenCalled('findAll')
			->answers($zones);


		$ecrivez_des_tests = Class_Article::getLoader()
			->newInstanceWithId(2)
			->setIdSite(0)
			->setTitre('Ecrivez des tests !');
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array('id_bib' => 3))
			->answers(array($ecrivez_des_tests))
			->getWrapper()

			->whenCalled('filterByLocaleAndWorkflow')
			->with(array($ecrivez_des_tests))
			->answers(array($ecrivez_des_tests));

		$this->dispatch('/bib/index');
	}

	/** @test */
	function globalImagePathShouldBeUserFiles_PhotoBib_GlobalDotJpg() {
		$this->assertXPath('//img[contains(@src, "photobib/global.jpg")]');
	}

	/** @test */
	function tooltipBib1ShouldContainsCoordinatesForFirstBib() {
		$this->assertXPath(sprintf('//map//area[@coords="%s"][contains(@href, "%s")][@class="tooltip_bib1"]',
															 '93,14,87,20,84,30,79,40,75,53,69,65,64,77,64,81,67,84,69,96,77,101,82,104,90,105,96,100,101,95,106,95,117,96,128,97,132,91,139,83,145,73,146,65,152,63,158,62,159,59,165,60,168,66,171,58,169,51,137,36,128,37,125,32,126,28,118,26,119,20,115,15,101,13',
															 'bib/zoneview/id/1'));
	}


	/** @test */
	function tooltipBib7ShouldContainsCoordinatesForLastBib() {
		$this->assertXPath(sprintf('//map//area[@coords="%s"][@href="%s"][@class="tooltip_bib7"]',
															 '153,261,146,267,148,274,149,280,151,286,157,290,165,291,171,299,167,308,175,314,178,324,183,334,187,338,192,338,196,335,195,326,202,322,194,318,200,314,206,303,212,300,217,299,217,291,213,284,208,276,198,267,194,259,191,253,181,251,168,252',
															 '/bib/zoneview/id/7'),
											 $this->_response->getBody());
	}


	/** @test */
	function setTooltipJSShouldBeGenerated() {
		$this->assertTrue(false !== strpos($this->_response->getBody(),
																			 "setTooltip($('.tooltip_bib3'), '<a href=\"" . BASE_URL . "/bib/bibview/id/3\"><b>BibZone3</b></a><br />'"),
											$this->_response->getBody());
	}


	/** @test */
	function articleEcrivezDesTestShouldBeVisible() {
		$this->assertXPathContentContains("//li//a", "Ecrivez des tests !", $this->_response->getBody());		
	}
}

?>