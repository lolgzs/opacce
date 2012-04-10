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
require_once 'Class/AvisNotice.php';
require_once 'ModelTestCase.php';

class AvisNoticeFixtures extends TestFixtures {
	protected $_fixtures = array(
										'marcus_on_millenium' => array('ID' => 23,
																									 'ID_USER' => 2,
																									 'ENTETE' => 'excellent', 
																									 'AVIS' => 'on en redemande',
																									 'CLEF_OEUVRE' => 'MILLENIUM',
																									 'DATE_AVIS' => '2010-05-21',
																									 'NOTE' => '4'),
										'marcus_on_potter' => array('ID' => 25,
																								'ID_USER' => 2,
																								'ENTETE' => 'pour les enfants',
																								'AVIS' => 'ils aiment bien',
																								'CLEF_OEUVRE' => 'POTTER',
																								'DATE_AVIS' => '2010-05-20',
																								'NOTE' => 3),
										'steve_on_millenium' => array('ID' => 12,
																									'ID_USER' => 5,
																									'ENTETE' => 'ça fait peur',
																									'AVIS' => 'très glauque',
																									'CLEF_OEUVRE' => 'MILLENIUM',
																									'DATE_AVIS' => '2009-05-17',
																									'NOTE' => null),
										'steve_on_potter' => array('ID' => 48,
																							 'ID_USER' => 5,
																							 'ENTETE' => 'Vive potter',
																							 'AVIS' => "c'est génial",
																							 'CLEF_OEUVRE' => 'POTTER',
																							 'DATE_AVIS' => '2011-05-17',
																							 'NOTE' => 3)
										);

	public static function instance() {
		return new self();
	}
}


class AvisTestBibAbonne extends ModelTestCase {
	public function setUp() {
		$this->avis_bib1 = new Class_AvisNotice();
		$this->avis_bib1->setAbonOuBib(1)->setNote(4);

		$this->avis_bib2 = new Class_AvisNotice();
		$this->avis_bib2->setAbonOuBib(1)->setNote(5);

		$this->avis_abon1 = new Class_AvisNotice();
		$this->avis_abon1->setAbonOuBib(0)->setNote(2);

		$this->avis_abon2 = new Class_AvisNotice();
		$this->avis_abon2->setAbonOuBib(0)->setNote(3);

		$this->all_avis = array($this->avis_bib1, 
														$this->avis_bib2,
														$this->avis_abon1,
														$this->avis_abon2);
	}

	public function testIsWrittenByBibliothequaire() {
		$this->assertTrue($this->avis_bib1->isWrittenByBibliothequaire());
		$this->assertFalse($this->avis_abon1->isWrittenByBibliothequaire());
	}

	public function testIsWrittenByAbonne() {
		$this->assertFalse($this->avis_bib1->isWrittenByAbonne());
		$this->assertTrue($this->avis_abon1->isWrittenByAbonne());
	}

	public function testFilterByAbonne() {
		$this->assertEquals(array($this->avis_abon1, $this->avis_abon2),
												Class_AvisNotice::filterByAbonne($this->all_avis));
	}

	public function testFilterByBibliothequaire() {
		$this->assertEquals(array($this->avis_bib1, $this->avis_bib2),
												Class_AvisNotice::filterByBibliothequaire($this->all_avis));
	}

	public function testGetNoteAverage() {
		$this->assertEquals(0, Class_AvisNotice::getNoteAverage(null));
		$this->assertEquals(0, Class_AvisNotice::getNoteAverage(array()));
		$this->assertEquals(3, Class_AvisNotice::getNoteAverage(array($this->avis_abon2)));
		$this->assertEquals(2.5, Class_AvisNotice::getNoteAverage(array($this->avis_abon2, 
																																		$this->avis_abon1)));
		$this->assertEquals(3.5, Class_AvisNotice::getNoteAverage(array($this->avis_abon2,
																																		$this->avis_abon1,
																																		$this->avis_bib2)));
		
	}
}



class AvisTestSortByDateAvisDesc extends ModelTestCase {
	public function	setUp() {
		$this->_setFindAllExpectation('Class_AvisNotice', 'AvisNoticeFixtures');
		$this->avis = Class_AvisNotice::getLoader()->findAll();
		$this->avis_sorted = Class_AvisNotice::sortByDateAvisDesc($this->avis);
	}

	public function testFirstIsSteveOnPotter() {
		$avis = $this->avis_sorted[0];
		$this->assertEquals(48, $avis->getId());
	}

	public function testSecondIsMarcusOnMillenium() {
		$avis = $this->avis_sorted[1];
		$this->assertEquals(23, $avis->getId());
	}

	public function testThirdIsMarcusOnPotter() {
		$avis = $this->avis_sorted[2];
		$this->assertEquals(25, $avis->getId());
	}

	public function testLastIsSteveOnMillenium() {
		$avis = $this->avis_sorted[3];
		$this->assertEquals(12, $avis->getId());
	}
}


class AvisNoticeTestLoader extends ModelTestCase {
	public function testLoaderIsInstanceOfAvisNoticeLoader() {
		$loader = Class_AvisNotice::getLoader();
		$this->assertTrue($loader instanceof AvisNoticeLoader);
	}
}

class AvisNoticeTestFindAll extends ModelTestCase {
	public function setUp() {
		$this->_setFindAllExpectation('Class_AvisNotice', 'AvisNoticeFixtures');
		$this->avis = Class_AvisNotice::getLoader()->findAll();
	}

	public function testFirstIsMarcusOnMillenium() {
		$first = $this->avis[0];
		$this->assertEquals('excellent', $first->getEntete());
	}

	public function testNoteMarcusOnMilleniumIsFour() {
		$first = $this->avis[0];
		$this->assertEquals(4, $first->getNote());
	}

	public function testLastIsSteveOnPotter() {		
		$last = $this->avis[3];
		$this->assertEquals('Vive potter', $last->getEntete());
	}

	public function testSteveOnMilleniumNoteIsZero() {
		$steve_millenium = $this->avis[2];
		$this->assertEquals(12, $steve_millenium->getId());
		$this->assertEquals(0, $steve_millenium->getNote());
	}
}


class AvisNoticeTestBelongsToUserRelation extends ModelTestCase {
	public function setUp() {
		$this->user_loader = $this->_generateLoaderFor('Class_Users', array('find'));
		$this->avis_loader = $this->_generateLoaderFor('Class_AvisNotice', array('find', 'findAllBy'));

		$this->steve = new Class_Users();
		$this->steve
			->setPrenom('Steve')
			->setId(5);

		$this->avis_millenium = new Class_AvisNotice();
		$this->avis_millenium
			->setId(12)
			->setIdUser(5)
			->setEntete('ça fait peur')
			->setAvis("c'est glauque")
			->setNote(2);
	}


	public function testAvisGetUserReturnsSteve() {
		$this->user_loader
			->expects($this->any())
			->method('find')
			->with(5)
			->will($this->returnValue($this->steve));

		$this->assertEquals($this->steve, 
												$this->avis_millenium->getUser());
	}


	public function testAvisGetUserNotFoundReturnsNil() {
		$this->assertEquals(null, 
												$this->avis_millenium->getUser());
	}


	public function testAvisGetIdUserReturnsUserId() {
		$this->steve->setId(43);
		$this->avis_millenium->setUser($this->steve);
		$this->assertEquals(43, $this->avis_millenium->getIdUser());
	}


	public function testUserGetAvisReturnsAvisMillenium() {
		$this->avis_loader
			->expects($this->any())
			->method('findAllBy')
			->with(array('role' => 'user',
									 'order' => 'date_avis desc',
									 'model' => $this->steve))
			->will($this->returnValue(array($this->avis_millenium)));

		$this->steve->getAvis();
		$this->assertEquals(array($this->avis_millenium), 
												$this->steve->getAvis());
	}


	public function testUserAddAvisSetUserOnAvis() {
		$avis_potter = new Class_AvisNotice();
		$avis_potter
			->setId(12)
			->setNote(4);

		$this->avis_loader
			->expects($this->any())
			->method('findAllBy')
			->with(array('role' => 'user',
									 'order' => 'date_avis desc',
									 'model' => $this->steve))
			->will($this->returnValue(array($this->avis_millenium)));

		$this->steve->addAvis($avis_potter);
		$this->assertEquals($this->steve, 
												$avis_potter->getUser());
	}
}



abstract class AvisTestFindAllTestCase extends ModelTestCase {
	public function setUp() {
		$this->select = new Zend_Db_Table_Select(new Storm_Model_Table(array('name' => 'notices_avis')));
		$rs_avis = $this->_buildRowset(array(
																				 array('ID' => 25,
																							 'ENTETE' => 'pour les enfants',
																							 'ID_USER' => 34,
																							 'CLEF_OEUVRE' => 'POTTER'),
																				 array('ID' => 48,
																							 'ENTETE' => 'Vive potter',
																							 'ID_USER' => 34,
																							 'CLEF_OEUVRE' => 'POTTER')));

		$tbl_avis = $this->_buildTableMock('Class_AvisNotice', 
																			 array('fetchAll', 'select'));

		$tbl_avis
			->expects($this->once())
			->method('select')
			->will($this->returnValue($this->select));
			
		$tbl_avis
			->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue($rs_avis));
	}
}


class AvisTestFindAllByUserTestCase extends AvisTestFindAllTestCase {
	public function setUp() {
		parent::setUp();

		$this->steve = new Class_Users();
		$this->steve
			->setPrenom('Steve')
			->setId(34);

		$this->result = Class_AvisNotice::getLoader()->findAllBy(array('role' => 'user',
																																	 'model' => $this->steve));
	}

	public function testExpectedSQLQuery() {
 		$this->assertEquals("SELECT `notices_avis`.* FROM `notices_avis` WHERE (id_user=34)",
												$this->select->assemble());
	}
}



class AvisTestFindAllByDateAvisWithLimitAndOrderTestCase extends AvisTestFindAllTestCase {
	public function setUp() {
		parent::setUp();
		$this->result = Class_AvisNotice::getLoader()->findAllBy(array('order' => 'date_avis desc',
																																	 'limit' => 5));
	}

	public function testExpectedSQLQuery() {
 		$this->assertEquals("SELECT `notices_avis`.* FROM `notices_avis` ORDER BY `date_avis` desc LIMIT 5",
												$this->select->assemble());
	}
}


class AvisTestFindAllByUsersIdTest extends AvisTestFindAllTestCase {
	public function testExpectedSQLQuery() {
		$this->result = Class_AvisNotice::getLoader()->findAllBy(array('id_user' => array(23, 34, 4)));
 		$this->assertEquals("SELECT `notices_avis`.* FROM `notices_avis` WHERE (id_user in (23, 34, 4))",
												$this->select->assemble());
	}
}


class AvisTestFindAllByUserAndClefOeuvreTestCase extends AvisTestFindAllTestCase {
	public function setUp() {
		parent::setUp();
		$this->result = Class_AvisNotice::getLoader()->findAllBy(array('clef_oeuvre' => 'POTTER',
																																	 'id_user' => 34));
	}

	public function testResultCountIsTwo() {
		$this->assertEquals(2, count($this->result));
	}

	public function testExpectedSQLQuery() {
 		$this->assertEquals("SELECT `notices_avis`.* FROM `notices_avis` WHERE (clef_oeuvre='POTTER') AND (id_user=34)",
												$this->select->assemble());
	}

	public function testFirstAvis() {
		$first = $this->result[0];
		$this->assertEquals('pour les enfants', $first->getEntete());
		$this->assertEquals(34, $first->getIdUser());
	}

	public function testSecondAvis() {
		$second = $this->result[1];
		$this->assertEquals('Vive potter', $second->getEntete());
	}
}


class NoticeTestHasManyAvisTest extends ModelTestCase {
	public function setUp() {
		$this->avis_loader = $this->_generateLoaderFor('Class_AvisNotice', array('find', 'findAllBy'));
		$this->notice_loader = $this->_generateLoaderFor('Class_Notice', array('find', 'findAllBy'));

		$this->steve = new Class_Users();
		$this->steve
			->setPrenom('Steve')
			->setId(5);

		$this->millenium = new Class_Notice();
		$this->millenium
			->setClefOeuvre('MILLENIUM');

		$this->avis_millenium_steve = new Class_AvisNotice();
		$this->avis_millenium_steve
			->setId(12)
			->setUser($this->steve)
			->setEntete('ça fait peur')
			->setAvis("c'est glauque")
			->setClefOeuvre('MILLENIUM')
			->setNote(2);
	}


	public function testNoticeGetAvisByUser() {
		$this->avis_loader
			->expects($this->once())
			->method('findAllBy')
			->with(array('clef_oeuvre' => 'MILLENIUM',
									 'id_user' => 5))
			->will($this->returnValue(array($this->avis_millenium_steve)));

		$found_avis = $this->millenium->getAvisByUser($this->steve);
		$this->assertEquals(array($this->avis_millenium_steve),
												$found_avis);
	}


	public function testNoticeGetAvis() {
		$avis_millenium_robert = new Class_AvisNotice();

		$this->avis_loader
			->expects($this->once())
			->method('findAllBy')
			->with(array('clef_oeuvre' => 'MILLENIUM'))
			->will($this->returnValue(array($this->avis_millenium_steve,
																			$avis_millenium_robert)));

		$found_avis = $this->millenium->getAvis();
		$this->assertEquals(array($this->avis_millenium_steve, 
															$avis_millenium_robert),
												$found_avis);
	}


	public function testAvisGetNotices() {
		$this->notice_loader
			->expects($this->once())
			->method('findAllBy')
			->with(array('clef_oeuvre' => 'MILLENIUM'))
			->will($this->returnValue(array($this->millenium)));

		$notices_found = $this->avis_millenium_steve->getNotices();
		$this->assertEquals(array($this->millenium),
												$notices_found);
	}
}



class AvisValidationsTest  extends ModelTestCase {
	public function setUp() {
		$this->avis_min_saisie = new Class_AdminVar();
		$this->avis_min_saisie
			->setId('AVIS_MIN_SAISIE')
			->setValeur(10);

		$this->avis_max_saisie = new Class_AdminVar();
		$this->avis_max_saisie
			->setId('AVIS_MAX_SAISIE')
			->setValeur(1200);

		Class_AdminVar::getLoader()
			->cacheInstance($this->avis_min_saisie)
			->cacheInstance($this->avis_max_saisie);

		$this->avis_loader = $this->_generateLoaderFor('Class_AvisNotice', 
																									 array('save'));


		$this->avis = new Class_AvisNotice();
		$this->avis->setEntete('a title');
	}

	public function testValidWithRightAvisSize() {
		$this->avis->setAvis('more than 10 characters');

		$this->avis_loader
			->expects($this->once())
			->method('save')
			->with($this->avis);

		$this->assertTrue($this->avis->isValid());
		$this->assertTrue($this->avis->save());
	}

	public function testInvalidWithWrongAvisSize() {
		$this->avis->setAvis('0');

		$this->assertFalse($this->avis->isValid());
		$this->assertFalse($this->avis->save());
	}
}



class AvisSetAbonOuBibTest extends ModelTestCase {
	public function setUp() {
		$this->marcel = new Class_Users();
		$this->marcel
			->setId(5)
			->setRoleLevel(2);

		$this->admin = new Class_Users();
		$this->admin
			->setId(12)
			->setRoleLevel(3);

		$this->avis = new Class_AvisNotice();
		$this->avis
			->setEntete('Surprenant')
			->setAvis('et plein de suspens');

		$this
			->_generateLoaderFor('Class_AvisNotice', array('save'))
			->expects($this->once())
			->method('save')
			->with($this->avis)
			->will($this->returnValue(true));
	}
	
	public function testSaveWithAdminSetAbonOuBibToOne() {
		$this->avis->setUser($this->admin);
		$this->avis->save();
		$this->assertEquals(1, $this->avis->getAbonOuBib());
	}

	public function testSaveWithFlorenceSetAbonOuBibToZero() {
		$this->avis->setUser($this->marcel);
		$this->avis->save();
		$this->assertEquals(0, $this->avis->getAbonOuBib());
	}

	public function testSaveWithNoUserSetAbonOuBibToZero() {		
		$this->avis->setUser(null);
		$this->avis->save();
		$this->assertEquals(0, $this->avis->getAbonOuBib());
	}
}




class AvisLoaderGetAvisFromPreferencesTest extends AvisTestFindAllTestCase {
	public function setUp() {
		parent::setUp();

		$this->modo_avis = new Class_AdminVar();
		$this->modo_avis
			->setId('MODO_AVIS')
			->setValeur(0);

		$this->modo_avis_biblio = new Class_AdminVar();
		$this->modo_avis_biblio
			->setId('MODO_AVIS_BIBLIO')
			->setValeur(0);

		Class_AdminVar::getLoader()
			->cacheInstance($this->modo_avis)
			->cacheInstance($this->modo_avis_biblio);

		$this->preferences = array( 'id_panier' => null,
																'id_catalogue' => null,
																'abon_ou_bib' => '',
																'only_img' => 0);
	}

	protected function assertQueryIs($expected) {
		Class_AvisNotice::getLoader()->getAvisFromPreferences($this->preferences);
 		$this->assertEquals(trim($expected),
												trim(str_replace("\n", "", $this->select->assemble())));
	}


	public function testDefaultSQLQuery() {
 		$this->assertQueryIs("SELECT `notices_avis`.* ".
												 "FROM `notices_avis` ".
												 "ORDER BY `DATE_AVIS` DESC");
	}

	public function testWithAllAndModoAPosteriori() { 		
		$this->preferences['abon_ou_bib'] = 'all';
 		$this->assertQueryIs("SELECT `notices_avis`.* ".
												 "FROM `notices_avis` ".
												 "ORDER BY `DATE_AVIS` DESC");
	}


	public function testWithAllAndModoAPrioriForReaders() { 		
		$this->modo_avis->setValeur(1);
		$this->preferences['abon_ou_bib'] = 'all';
		$this->assertQueryIs("SELECT `notices_avis`.* ".
												 "FROM `notices_avis` ".
												 "WHERE (STATUT=1 OR ABON_OU_BIB=1) ".
												 "ORDER BY `DATE_AVIS` DESC");
	}


	public function testWithAllAndModoAPrioriForBiblio() { 		
		$this->modo_avis_biblio->setValeur(1);
		$this->preferences['abon_ou_bib'] = 'all';
		$this->assertQueryIs("SELECT `notices_avis`.* ".
												 "FROM `notices_avis` ".
												 "WHERE (STATUT=1 OR ABON_OU_BIB=0) ".
												 "ORDER BY `DATE_AVIS` DESC");
	}


	public function testWithAllAndModoAPrioriForBiblioAndReaders() { 		
		$this->modo_avis->setValeur(1);
		$this->modo_avis_biblio->setValeur(1);

		$this->preferences['abon_ou_bib'] = 'all';

		$this->assertQueryIs("SELECT `notices_avis`.* ".
												 "FROM `notices_avis` ".
												 "WHERE (STATUT=1 OR ABON_OU_BIB=1) ".
												 "AND (STATUT=1 OR ABON_OU_BIB=0) ".
												 "ORDER BY `DATE_AVIS` DESC");
	}


	public function testWithReadersAndModoAPosteriori() { 		
		$this->preferences['abon_ou_bib'] = '0';
		$this->assertQueryIs("SELECT `notices_avis`.* ".
												 "FROM `notices_avis` ".
												 "WHERE (ABON_OU_BIB='0') ".
												 "ORDER BY `DATE_AVIS` DESC");
	}


	public function testWithReadersAndModoAPrioriForReaders() { 		
		$this->modo_avis->setValeur(1);

		$this->preferences['abon_ou_bib'] = '0';
		$this->assertQueryIs("SELECT `notices_avis`.* ".
												 "FROM `notices_avis` ".
												 "WHERE (ABON_OU_BIB='0') ".
												 "AND (STATUT=1 OR ABON_OU_BIB=1) ".
												 "ORDER BY `DATE_AVIS` DESC");
	}


	public function testWithBiblioAndModoAPosteriori() {
		$this->preferences['abon_ou_bib'] = '1';
		$this->assertQueryIs("SELECT `notices_avis`.* ".
												 "FROM `notices_avis` ".
												 "WHERE (ABON_OU_BIB='1') ".
												 "ORDER BY `DATE_AVIS` DESC");
	}

	public function testWithBiblioAndModoAPriori() {
		$this->modo_avis_biblio->setValeur(1);

		$this->preferences['abon_ou_bib'] = '1';
		$this->assertQueryIs("SELECT `notices_avis`.* ".
												 "FROM `notices_avis` ".
												 "WHERE (ABON_OU_BIB='1') ".
												 "AND (STATUT=1 OR ABON_OU_BIB=0) ".
												 "ORDER BY `DATE_AVIS` DESC");
	}
}



class AvisVisibilityTest extends ModelTestCase {
	public function setUp() {
		$this->user_reader = new Class_Users();
		$this->user_reader->setRoleLevel(1)->setId(2);

		$this->user_reader_other = new Class_Users();
		$this->user_reader_other->setRoleLevel(1)->setId(10);


		$this->user_bib = new Class_Users();
		$this->user_bib->setRoleLevel(3)->setIdUser(5);

		$this->user_bib_other = new Class_Users();
		$this->user_bib_other->setRoleLevel(3)->setIdUser(23);

		$this->avis_reader_invalid = new Class_AvisNotice();
		$this->avis_reader_invalid
			->setAbonOuBib(0)
			->setUser($this->user_reader)
			->setStatut(0);
		$this->assertFalse($this->avis_reader_invalid->isModerationOK());
		$this->assertTrue($this->avis_reader_invalid->isWrittenByAbonne());
			
		$this->avis_reader_valid = new Class_AvisNotice();
		$this->avis_reader_valid
			->setAbonOuBib(0)
			->setUser($this->user_reader)
			->setStatut(1);		

		$this->avis_biblio_invalid = new Class_AvisNotice();
		$this->avis_biblio_invalid
			->setAbonOuBib(1)
			->setStatut(0)
			->setUser($this->user_bib);

		$this->avis_biblio_valid = new Class_AvisNotice();
		$this->avis_biblio_valid
			->setAbonOuBib(1)
			->setUser($this->user_bib)
			->setStatut(1);

		$this->modo_avis = new Class_AdminVar();
		$this->modo_avis
			->setId('MODO_AVIS')
			->setValeur(0);

		$this->modo_avis_biblio = new Class_AdminVar();
		$this->modo_avis_biblio
			->setId('MODO_AVIS_BIBLIO')
			->setValeur(0);

		Class_AdminVar::getLoader()
			->cacheInstance($this->modo_avis)
			->cacheInstance($this->modo_avis_biblio);
	}


	protected function assertVisible($avis, $user) {
		$this->assertTrue($avis->isVisibleForUser($user));
	}

	protected function assertNotVisible($avis, $user) {
		$this->assertFalse($avis->isVisibleForUser($user));
	}


	public function testWithModoReaderAPriori() {
		$this->assertVisible($this->avis_reader_valid, $this->user_reader);
		$this->assertVisible($this->avis_reader_invalid, $this->user_reader);
		$this->assertVisible($this->avis_biblio_valid, $this->user_reader);	
		$this->assertVisible($this->avis_biblio_invalid, $this->user_reader);	

		$this->assertVisible($this->avis_reader_valid, $this->user_reader_other);
		$this->assertVisible($this->avis_reader_invalid, $this->user_reader_other);
		$this->assertVisible($this->avis_biblio_valid, $this->user_reader_other);	
		$this->assertVisible($this->avis_biblio_invalid, $this->user_reader_other);	

		$this->assertVisible($this->avis_reader_valid, $this->user_bib);
		$this->assertVisible($this->avis_reader_invalid, $this->user_bib);
		$this->assertVisible($this->avis_biblio_valid, $this->user_bib_other);	
		$this->assertVisible($this->avis_biblio_invalid, $this->user_bib_other);	
	}

	public function testWithModoReaderAPosteriori() {
		$this->modo_avis->setValeur(1);
		$this->assertVisible($this->avis_reader_valid, $this->user_reader);
		$this->assertVisible($this->avis_reader_invalid, $this->user_reader);	
		$this->assertVisible($this->avis_biblio_valid, $this->user_reader);	
		$this->assertVisible($this->avis_biblio_invalid, $this->user_reader);	

		$this->assertVisible($this->avis_reader_valid, $this->user_reader_other);
		$this->assertNotVisible($this->avis_reader_invalid, $this->user_reader_other);
		$this->assertVisible($this->avis_biblio_valid, $this->user_reader);	
		$this->assertVisible($this->avis_biblio_invalid, $this->user_reader);	

		$this->assertVisible($this->avis_reader_valid, $this->user_bib);
		$this->assertNotVisible($this->avis_reader_invalid, $this->user_bib);
		$this->assertVisible($this->avis_biblio_valid, $this->user_reader);	
		$this->assertVisible($this->avis_biblio_invalid, $this->user_reader);	
	}

	public function testWithModoBibrAPosteriori() {
		$this->modo_avis_biblio->setValeur(1);
		$this->assertVisible($this->avis_reader_valid, $this->user_reader);
		$this->assertVisible($this->avis_reader_invalid, $this->user_reader);	
		$this->assertVisible($this->avis_biblio_valid, $this->user_reader);	
		$this->assertNotVisible($this->avis_biblio_invalid, $this->user_reader);	

		$this->assertVisible($this->avis_reader_valid, $this->user_reader_other);
		$this->assertVisible($this->avis_reader_invalid, $this->user_reader_other);
		$this->assertVisible($this->avis_biblio_valid, $this->user_reader_other);	
		$this->assertNotVisible($this->avis_biblio_invalid, $this->user_reader_other);	

		$this->assertVisible($this->avis_reader_valid, $this->user_bib);
		$this->assertVisible($this->avis_reader_invalid, $this->user_bib);
		$this->assertVisible($this->avis_biblio_valid, $this->user_bib_other);	
		$this->assertNotVisible($this->avis_biblio_invalid, $this->user_bib_other);	
	}
}