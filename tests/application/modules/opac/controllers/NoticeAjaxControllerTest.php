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

class NoticeAjaxControllerNonRegressionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_Notice::getLoader()->newInstanceWithId('197143');
	}


	/** @test */
	function exemplairesWithoutIdNoticeShouldRenderEmptyHtml() {
		$this->dispatch('noticeajax/exemplaires');
		$this->assertEmpty($this->_response->getBody());
	}


	/** @test */
	function detailShouldRenderAucuneNoticeTrouvee() {
		$mock_sql = $this->getSqlMock();
		$mock_sql
			->expects($this->at(1))
			->method('fetchEnreg')
			->with('select titres,auteurs,collection,matieres,dewey from notices where id_notice=197143')
			->will($this->returnValue(array('titres' => 'IRM',
																			'auteurs' => '',
																			'collection' => '',
																			'matieres' => '',
																			'dewey' => '')));

		$mock_sql
			->expects($this->at(2))
			->method('fetchAll')
			->with("select id_notice from notices where MATCH(titres,auteurs,editeur,collection,matieres,dewey) AGAINST(' (IRM IRMS )') and id_notice !=197143 Limit 0,10")
			->will($this->returnValue(array(array('id_notice' => null))));

		$this->dispatch('/opac/noticeajax/similaires?isbn=&onglet=set197143_onglet_0&page=0&id_notice=197143');
		$this->assertXPathContentContains('//td', utf8_encode("Aucune information n'a été trouvée"));
	}



	/** @test */
	function detailShouldRenderOneNoticeTrouvee() {
		$mock_sql = Storm_Test_ObjectWrapper::on(Zend_Registry::get('sql'));
		Zend_Registry::set('sql', $mock_sql);

		$mock_sql
			->whenCalled('fetchOne')
			->with('select count(*) from exemplaires')
			->answers(123);

		$mock_sql
			->whenCalled('fetchOne')
			->with("select valeur from variables where clef ='url_services'")
			->answers('http://localhost');

			
		$mock_sql
			->whenCalled('fetchEnreg')
			->with('select titres,auteurs,collection,matieres,dewey from notices where id_notice=197143', false)
			->answers(array('titres' => 'IRM',
											'auteurs' => '',
											'collection' => '',
											'matieres' => '',
											'dewey' => ''));

		$mock_sql
			->whenCalled('fetchAll')
			->with("select id_notice from notices where MATCH(titres,auteurs,editeur,collection,matieres,dewey) AGAINST(' (IRM IRMS )') and id_notice !=197143 Limit 0,10", false)
			->answers(array(array('id_notice' => 1)));

		$mock_sql
			->whenCalled('fetchAll')
			->with("select id_notice from notices where MATCH(titres,auteurs,editeur,collection,matieres,dewey) AGAINST('') and id_notice !=197143 Limit 0,10", false)
			->answers(array(array('id_notice' => 1)));

		$mock_sql
			->whenCalled('fetchEnreg')
			->with("select type_doc,facettes,isbn,ean,annee,tome_alpha,clef_alpha,unimarc from notices where id_notice=1", false)
			->answers(array('id_notice' => 1,
											'facettes' => '',
											'isbn' => '',
											'ean' => '',
											'type_doc' => 1,
											'tome_alpha' => '',
											'clef_alpha' => 'SOULEYMANE',
											'unimarc' => "01328ngm0 2200265   450 0010007000001000041000071010013000481020007000611150025000682000071000932100022001642150053001863000035002393000045002743300454003193450027007735100018008006060027008186060039008457000042008847020043009267020033009697020032010028010028010342247456  a20021213i20041975u  y0frey0103    ba0 abamjfre  aFR  ac086baz|zba    zz  c1 aLa jeune fillebDVDdDen MusofSouleymane Cisse, réal., scénario  cPathédcop. 2004  a1 DVD vidéo monoface zone 2 (1 h 26 min)ccoul.  aDate de sortie du film : 1975.  aFilm en bambara sous-titré en français  aSékou est renvoyé de l'usine parce qu'il a osé demander une augmentation. Chômeur, il sort avec Ténin, une jeune fille muette ; il ignore qu'elle est la fille de son ancien patron. Ténin, qui sera violée par Sékou lors d'une sortie entre jeunes, se retrouve enceinte et subit la colère de ses parents. Elle se trouve alors confrontée brutalement à la morale de sa famille et à la lâcheté de Sékou, qui refuse de reconnaiîre l'enfant.  b3388334509824d14.00 ?1 aDen Musozbam| 31070135aCinémayMali| 32243367aCinéma30076549yAfrique 131070144aCissébSouleymane43704690 132247457aCoulibalibDounamba Dani4590 132247458aDiabatebFanta4590 132247459aDiarrabOumou4590 0aFRbBNc20011120gAFNOR",
											'annee' => '2000'));


		$mock_sql
			->whenCalled('fetchOne')
			->with("select url_vignette from notices where id_notice=1")
			->answers("NO");


		$mock_sql->beStrict();

		$this->dispatch('/opac/noticeajax/similaires?isbn=&onglet=set197143_onglet_0&page=0&id_notice=197143');
		$this->assertXPathContentContains('//td', utf8_encode('Auteur : Souleymane Cissé'));
		$this->assertContains("images/supports", $this->_response->getBody());
	}
}


class NoticeAjaxControllerResNumeriquesTest extends AbstractControllerTestCase {
	/** @test */
	function bookletShouldBeLoadedWithAlbumTypeLivreNumerique() {
		$exemplaire = Class_Exemplaire::getLoader()->newInstanceWithId(34)->setIdOrigine(8);

		$album = Class_Album::getLoader()
			->newInstanceWithId(8)
			->setTypeDocId(Class_TypeDoc::LIVRE_NUM);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findFirstBy')
			->with(array('id_notice' => 123))
			->answers($exemplaire);

		$this->dispatch('noticeajax/resnumeriques?id_notice=123');

		$this->assertXPathContentContains('//script', '_load_in_scriptsRoot');
	}


	/** @test */
	function messageAucuneRessourceShouldBeDisplayedIfAlbumDoesNotExists() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findFirstBy')
			->answers(null);

		$this->dispatch('noticeajax/resnumeriques?id_notice=124');

		$this->assertXPathContentContains('//p', 'Aucune ressource correspondante');
	}


	/** @test */
	function diaporamaShouldBeLoadedWithAlbumTypeDiaporama() {
		$exemplaire = Class_Exemplaire::getLoader()->newInstanceWithId(34)->setIdOrigine(8);

		$album = Class_Album::getLoader()
			->newInstanceWithId(8)
			->setTypeDocId(Class_TypeDoc::DIAPORAMA)
			->setRessources([]);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findFirstBy')
			->with(array('id_notice' => 123))
			->answers($exemplaire);

		$this->dispatch('noticeajax/resnumeriques?id_notice=123', true);

		$this->assertXPath('//script[contains(@src, "jquery.cycle.all")]');
	}
}



class NoticeAjaxControllerResumeAlbumTest extends AbstractControllerTestCase {
	protected $_notice;

	public function setUp() {
		parent::setUp();

		$this->_notice = Class_Notice::getLoader()
			->newInstanceWithId(123)
			->beLivreNumerique()
			->setExemplaires(array(Class_Exemplaire::getLoader()
														 ->newInstanceWithId(34)
														 ->setIdOrigine(2)));

		Class_Album::getLoader()
			->newInstanceWithId(2)
			->setDescription('Lucky Luke est un grand cow-boy');
	}


	/** @test */
	public function contenuShouldContainsResume() {
		$this->dispatch('noticeajax/resume?id_notice=123');
		$this->assertXPathContentContains('//div', 'Lucky Luke est un grand cow-boy');
	}


	/** @test */
	public function withNoExemplairesShouldNotFail() {
		$this->_notice->setExemplaires(array());		
		$this->dispatch('noticeajax/resume?id_notice=123');
	}


	/** @test */
	public function withNoAlbumFoundShouldNotFail() {
		array_first($this->_notice->getExemplaires())->setIdOrigine(999999999);
		$this->dispatch('noticeajax/resume?id_notice=123');
	}
}



class NoticeAjaxControllerExemplairesTest extends AbstractControllerTestCase {
	protected $_notice;

	public function setUp() {
		parent::setUp();
		
		$this->_notice = Class_Notice::getLoader()
			->newInstanceWithId(123)
			->beLivreNumerique()
			->setExemplaires(array());
		
		$mock_sql = Storm_Test_ObjectWrapper::on(Zend_Registry::get('sql'));
		Zend_Registry::set('sql', $mock_sql);

		$exemplaires = array(array('id_bib' => 99,
															 'id_notice' => 123,
															 'id' => '6778778778',
															 'annexe' => 'MOUL',
															 'section' => 'A9',
															 'emplacement' => 'emplacement de test',
															 'count(*)' => 3,
															 'cote' => 'VOD-T-DLJ',
															 'dispo' => 'Disponible',
															 'date_retour' => 'En mai',
															 'code_barres' => '7777734343488'));

		$mock_sql
			->whenCalled('fetchAll')
			->with('Select id_notice,id_bib,cote,count(*) from exemplaires  where id_notice=123 group by 1,2,3',
						 false)
			->answers($exemplaires);

		$this->dispatch('noticeajax/exemplaires?id_notice=123');
	}


	/** @test */
	public function shouldRenderNumber() {
		$this->assertXPathContentContains('//td', '1');
	}


	/** @test */
	public function shouldRenderCote() {
		$this->assertXPathContentContains('//td', 'VOD-T-DLJ');
	}
}




class NoticeAjaxControllerBabelthequeTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Notice::getLoader()
			->newInstanceWithId(157675)
			->setIsbn('978-2-226-21993-0');

		Class_AdminVar::getLoader()
			->newInstanceWithId('BABELTHEQUE_JS')
			->setValeur('http://www.babeltheque.com/bw_85.js');

		$this->dispatch('/opac/noticeajax/babeltheque?id_notice=N157675', true);
	}


	/** @test */
	public function responseShouldContainsBabelthequeScript() {
		$this->assertXPath('//script[@src="http://www.babeltheque.com/bw_85.js"]');
	}


	/** @test */
	public function pageShouldContainsInputHiddenWithIsbn() {
		$this->assertXPath('//input[@type="hidden"][@id="BW_id_isbn"][@value="978-2-226-21993-0"]');
	}


	/** @test */
	public function pageShouldContainsNotes() {
		$this->assertXPath('//div[@id="BW_notes"]');
	}


	/** @test */
	public function pageShouldContainsCritiques() {
		$this->assertXPath('//div[@id="BW_critiques"]');
	}
}




class NoticeAjaxControllerBabelthequeWithoutOptionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('BABELTHEQUE_JS')
			->setValeur('');

		$this->dispatch('/opac/noticeajax/babeltheque?id_notice=N157675', true);
	}


	/** @test */
	public function responseShouldBeEmpty() {
		$this->assertEquals('', $this->_response->getBody());
	}
}



class NoticeAjaxControllerFrbrNotFoundTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
				->whenCalled('find')
				->with(777)
				->answers(null);
				
		$this->dispatch('/opac/noticeajax/frbr?id_notice=777', true);
	}


	/** @test */
	public function actionShouldBeFrbr() {
		$this->assertAction('frbr');
	}


	/** @test */
	public function responseShouldContainAucunLien() {
		$this->assertTrue(false !== strpos($this->_response->getBody(), 'Aucun lien'));
	}
}


abstract class NoticeAjaxControllerFrbrWithLinksTestCase extends AbstractControllerTestCase {
	private $_lesGrandsTextes;
	private $_moiCEstQuoi;
	
	public function setUp() {
		parent::setUp();

		$leCombat = 'LECOMBATDESJUGES--BILLYY--ZARAFAFILMSDISTRIB-2006-4';
		$this->_lesGrandsTextes = 'LESGRANDSTEXTESDEDROITINTERNATIONALPUBLIC--DUPUYP--DALLOZ-2010-1';
		$this->_moiCEstQuoi = 'MOICESTQUOI--BRENIFIERO--NATHANJEUNESSE-2004-1';
		
		$type = Class_FRBR_LinkType::newInstanceWithId(1)
				->setLibelle('Suite')
				->setFromSource('a pour suite')
				->setFromTarget('est une suite de');
				
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_FRBR_Link')
				->whenCalled('getLinksForSource')
				->with($leCombat)
				->answers([Class_FRBR_Link::newInstanceWithId(1)
						       ->setType($type)
						       ->setSource($this->_getUrlForKey($leCombat))
						       ->setSourceType(Class_FRBR_Link::TYPE_NOTICE)
						       ->setTarget($this->_getUrlForKey($this->_lesGrandsTextes))
						       ->setTargetType(Class_FRBR_Link::TYPE_NOTICE)])

				->whenCalled('getLinksForTarget')
				->with($leCombat)
				->answers([Class_FRBR_Link::newInstanceWithId(2)
						       ->setType($type)
						       ->setSource($this->_getUrlForKey($this->_moiCEstQuoi))
						       ->setSourceType(Class_FRBR_Link::TYPE_NOTICE)
						       ->setTarget($this->_getUrlForKey($leCombat))
						       ->setTargetType(Class_FRBR_Link::TYPE_NOTICE)]);
		

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
				->whenCalled('getNoticeByClefAlpha')
				->with($this->_lesGrandsTextes)
				->answers(Class_Notice::newInstanceWithId(888)
					        ->setTitrePrincipal('Les grands textes de droit')
					        ->setClefAlpha($this->_lesGrandsTextes))

				->whenCalled('getNoticeByClefAlpha')
				->with($this->_moiCEstQuoi)
				->answers(Class_Notice::newInstanceWithId(999)
					        ->setTitrePrincipal('Moi, c\'est quoi ?')
					        ->setClefAlpha($this->_moiCEstQuoi))

				->whenCalled('find')
				->with(777)
				->answers(Class_Notice::newInstanceWithId(777)
					          ->setClefAlpha($leCombat));
	}


	protected function _getUrlForKey($key) {
		return 'http://localhost' . BASE_URL . '/recherche/viewnotice/clef/' . $key
				. '?id_profil=1&type_doc=1';
	}

		
	/** @test */
	public function linkTypeAPourSuiteShouldBePresent() {
		$this->assertXPathContentContains('//div', 'a pour suite');
	}

		
	/** @test */
	public function lesGrandsTextesShouldBePresent() {
		$this->assertXPathContentContains('//td', 'Les grands textes de droit');
	}


	/** @test */
	public function linkToTargetShouldBePresent() {
		$this->assertXPath('//td[contains(@onclick, "/viewnotice/clef/' . $this->_lesGrandsTextes . '")]');
	}


	/** @test */
	public function linkTypeEstSuiteShouldBePresent() {
		$this->assertXPathContentContains('//div', 'est une suite de', $this->_response->getBody());
	}


	/** @test */
	public function moiCEstQuoiShouldBePresent() {
		$this->assertXPathContentContains('//td', 'Moi, c\'est quoi');
	}


	/** @test */
	public function linkToSourceShouldBePresent() {
		$this->assertXPath('//td[contains(@onclick, "/viewnotice/clef/' . $this->_moiCEstQuoi . '")]');
	}
}



class NoticeAjaxControllerFrbrWithLinksTest extends NoticeAjaxControllerFrbrWithLinksTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/noticeajax/frbr?id_notice=777', true);
	}
}



class NoticeAjaxControllerFrbrWithLinksAndNInIdTest extends NoticeAjaxControllerFrbrWithLinksTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/noticeajax/frbr?id_notice=N777', true);
	}
}