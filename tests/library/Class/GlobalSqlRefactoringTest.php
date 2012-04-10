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

class GlobalSqlRefactoringTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->mock_sql = $this->getMockBuilder('Class_Systeme_Sql')
                         			->disableOriginalConstructor()
                        			->getMock();
		Zend_Registry::set('sql', $this->mock_sql);
	}


	/** @test */
	public function rssGetLastRss() {
		$rss = new Class_Rss();

		$this->mock_sql
			->expects($this->once())
			->method('fetchAll')
			->with('select * from rss_flux order by DATE_MAJ DESC LIMIT 7')
			->will($this->returnValue(array('feeds')));

		$this->assertEquals(array('feeds'),	$rss->getLastRss(7));
	}


	/** @test */
	public function deweyGetLibelleFound() {
		$this->mock_sql
			->expects($this->once())
			->method('fetchOne')
			->with("select libelle from codif_dewey where id_dewey='00465'")
			->will($this->returnValue('Réseaux d\'ordinateurs'));

		$this->assertEquals("Réseaux d'ordinateurs",	Class_Dewey::getLibelle('00465'));
	}


	/** @test */
	public function deweyGetLibelleNotFound() {
		$this->mock_sql
			->expects($this->once())
			->method('fetchOne')
			->with("select libelle from codif_dewey where id_dewey='00465'")
			->will($this->returnValue(false));

		$this->assertEquals("004.65",	Class_Dewey::getLibelle('00465'));
	}


	/** @test */
	public function deweyGetIndicesRoot() {
		$this->mock_sql
			->expects($this->once())
			->method('fetchAll')
			->with("select * from codif_dewey where LENGTH(id_dewey)=1 order by id_dewey")
			->will($this->returnValue(array('some dewey')));

		$this->assertEquals(array('some dewey'),	Class_Dewey::getIndices('root'));
	}


	/** @test */
	public function deweyGetIndices0046() {
		$this->mock_sql
			->expects($this->once())
			->method('fetchAll')
			->with("select * from codif_dewey where id_dewey like '0046%' and LENGTH(id_dewey)=5 order by id_dewey")
			->will($this->returnValue(array('some dewey')));

		$this->assertEquals(array('some dewey'),	Class_Dewey::getIndices('0046'));
	}



	/** @test */
	public function pdcm4GetIndicesRoot() {
		$this->mock_sql
			->expects($this->once())
			->method('fetchAll')
			->with("select * from codif_pcdm4 where LENGTH(id_pcdm4)=1 order by id_pcdm4")
			->will($this->returnValue(array('some indices')));

		$this->assertEquals(array('some indices'),	Class_Pcdm4::getIndices('root'));
	}



	/** @test */
	public function pcdm4GetIndices0046() {
		$this->mock_sql
			->expects($this->once())
			->method('fetchAll')
			->with("select * from codif_pcdm4 where id_pcdm4 like '0046%' and LENGTH(id_pcdm4)=5 order by id_pcdm4")
			->will($this->returnValue(array('some indices')));

		$this->assertEquals(array('some indices'),	Class_Pcdm4::getIndices('0046'));
	}


	/** @test */
	public function pcdm4GetLibelleFound() {
		$this->mock_sql
			->expects($this->once())
			->method('fetchOne')
			->with("select libelle from codif_pcdm4 where id_pcdm4='00465'")
			->will($this->returnValue('Traditions'));

		$this->assertEquals("Traditions",	Class_Pcdm4::getLibelle('00465'));
	}


	/** @test */
	public function pcdm4GetLibelleNotFound() {
		$this->mock_sql
			->expects($this->once())
			->method('fetchOne')
			->with("select libelle from codif_pcdm4 where id_pcdm4='00465'")
			->will($this->returnValue(false));

		$this->assertEquals("0.0465",	Class_Pcdm4::getLibelle('00465'));
	}


	/** @test */
	public function panierNoticeCreerPanier() {
		$this->mock_sql
			->expects($this->at(0))
			->method('fetchOne')
			->with("select max(ID_PANIER) from notices_paniers where ID_USER=5")
			->will($this->returnValue(2));

		$date = date("Y-m-d");
		$this->mock_sql
			->expects($this->at(1))
			->method('execute')
			->with("insert into notices_paniers(ID_USER,ID_PANIER,NOTICES,LIBELLE,DATE_MAJ) Values(5,3,'','Panier no 3','$date')");

		$panier = new Class_PanierNotice();
		$this->assertEquals(3, $panier->creerPanier(5));

	}


	/** @test */
	public function panierNoticeSupprimerPanier() {
		$this->mock_sql
			->expects($this->once())
			->method('execute')
			->with("delete from notices_paniers where ID_USER=5 and ID_PANIER=7");

		$panier = new Class_PanierNotice();
		$panier->supprimerPanier(5, 7);

	}


	/** @test */
	public function panierNoticeMajTitre() {
		$this->mock_sql
			->expects($this->once())
			->method('execute')
			->with("update notices_paniers set LIBELLE='toto' where ID_PANIER=2 and ID_USER=5");

		$this->mock_sql
			->expects($this->once())
			->method('quote')
			->with('toto')
			->will($this->returnValue("'toto'"));

		$panier = new Class_PanierNotice();
		$panier->majTitre(5, 2, 'toto');

	}


	/** @test */
	public function sitoGetLastSito() {
		$sito = new Class_Sitotheque();

		$this->mock_sql
			->expects($this->once())
			->method('fetchAll')
			->with('select * from sito_url order by DATE_MAJ DESC LIMIT 7')
			->will($this->returnValue(array('urls')));

		$this->assertEquals(array('urls'),	$sito->getLastSito(7));
	}


	/** @test */
	public function tagNoticeCreerTag() {
		$this->mock_sql
			->expects($this->at(0))
			->method('quote')
			->with("test")
			->will($this->returnValue("'test'"));


		$this->mock_sql
			->expects($this->at(1))
			->method('fetchOne')
			->with("select id_tag from codif_tags where code_alpha='TEST'")
			->will($this->returnValue(5));


		$this->mock_sql
			->expects($this->at(2))
			->method('fetchOne')
			->with("select facettes from notices where id_notice=3")
			->will($this->returnValue(" ZTEST"));


		$this->mock_sql
			->expects($this->at(3))
			->method('execute')
			->with("update notices set facettes=' ZTEST Z5' where id_notice=3");


		$this->mock_sql
			->expects($this->at(4))
			->method('fetchEnreg')
			->with("select notices,a_moderer from codif_tags where id_tag=5")
			->will($this->returnValue(array("notices" => ";3;", 
																			"a_moderer" => ";3;")));

		$this->mock_sql
			->expects($this->at(5))
			->method('execute')
			->with("update codif_tags set notices=';3;', a_moderer=';3;' where id_tag=5");
		
		$tag = new Class_TagNotice();
		$tag->creer_tag('test', 3);
	}
}

?>