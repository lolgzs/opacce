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
require_once 'Class/AvisNotice.php';
require_once 'Class/Notice.php';
require_once 'Class/Users.php';


abstract class AdminAvisModerationControllerTestCase extends AbstractControllerTestCase {
	protected function _initProfilHook($profil) {
		$profil->setLibelle('AFI');
	}

	protected function _loginHook($account) {
		$account->ROLE = "admin_portail";
		$account->LOGIN = "sysadmin";
		$account->PSEUDO = "admin";
	}

	public function setUp() {
		parent::setUp();

		$this->erik = new Class_Users();
		$this->erik
			->setId(3)
			->setRoleLevel(2)
			->setPseudo('Erik');

		$this->marcus = new Class_Users();
		$this->marcus
			->setId(5)
			->setRoleLevel(2)
			->setPseudo('Marcus');

		$this->club_cinq = new Class_Notice();
		$this->club_cinq
			->setId(18)
			->setTitrePrincipal('Club des cinq');

		$this->guide_routard = new Class_Notice();
		$this->guide_routard
			->setId(25)
			->setTitrePrincipal('Guide du routard');

		$this->avis_erik_club = new Class_AvisNotice();
		$this->avis_erik_club
			->setId(38)
			->setUser($this->erik)
			->setNotice($this->club_cinq)
			->setEntete('pour les jeunes')
			->setAvis('ça me rappelle mon enfance')
			->setDateAvis('2005-03-27')
			->setAbonOuBib(0)
			->setStatut(1)
			->setNote(4);

		$this->avis_marcus_routard = new Class_AvisNotice();
		$this->avis_marcus_routard
			->setId(42)
			->setUser($this->marcus)
			->setNotice($this->guide_routard)
			->setEntete('pour les routard')
			->setAvis('qui aiment bien manger')
			->setDateAvis('2010-07-21')
			->setAbonOuBib(0)
			->setStatut(1)
			->setNote(2);


		$this->avis_loader = $this->_generateLoaderFor('Class_AvisNotice', 
																									 array('findAllBy', 'save', 'delete', 'find'));
	}
}


class AdminAvisModerationControllerAvisToModerateTest extends AdminAvisModerationControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->avis_loader
			->expects($this->once())
			->method('findAllBy')
			->with(array('statut' => 0, 'limit' => 100))
			->will($this->returnValue(array($this->avis_marcus_routard, $this->avis_erik_club)));

		$this->dispatch('/admin/modo/avisnotice');
	}

	public function testClubCinqTitle() {
		$this->assertQueryContentContains('h2', 'Club des cinq');
	}

	public function testGuideRoutardTitle() {
		$this->assertQueryContentContains('h2', 'Guide du routard');
	}

	public function testClubCinqLinkValidate() {
		$this->assertXPath("//a[@href='/admin/modo/validateavisnotice/id/38']");
	}

	public function testRoutardLinkValidate() {
		$this->assertXPath("//a[@href='/admin/modo/validateavisnotice/id/42']");
	}

	public function testClubCinqLinkEdit() {
		//		$this->assertXPath("//a[@href='/admin/modo/editavisnotice/id/38']");
	}

	public function testRoutardLinkEdit() {
		//		$this->assertXPath("//a[@href='/admin/modo/editavisnotice/id/42']");
	}

	public function testClubCinqLinkDel() {
		$this->assertXPath("//a[@href='/admin/modo/delavisnotice/id/38']");
	}

	public function testRoutardLinkDel() {
		$this->assertXPath("//a[@href='/admin/modo/delavisnotice/id/42']");
	}
}


class AdminAvisModerationControllerAvisDelTest extends AdminAvisModerationControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->avis_loader
			->expects($this->once())
			->method('find')
			->with(38)
			->will($this->returnValue($this->avis_erik_club));

		$this->avis_loader
			->expects($this->once())
			->method('delete')
			->with($this->avis_erik_club);

		$this->dispatch('/admin/modo/delavisnotice/id/38');
	}

	public function testRedirectToAvisPage() {
		$this->assertRedirectTo('/admin/modo/avisnotice');
	}
}


class AdminAvisModerationControllerAvisValidateTest extends AdminAvisModerationControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->avis_loader
			->expects($this->once())
			->method('find')
			->with(38)
			->will($this->returnValue($this->avis_erik_club));

		$this->avis_loader
			->expects($this->once())
			->method('save')
			->with($this->avis_erik_club);

		$this->dispatch('/admin/modo/validateavisnotice/id/38');
	}

	public function testRedirectToAvisPage() {
		$this->assertRedirectTo('/admin/modo/avisnotice');
	}

	public function testStatutIsOne() {
		$this->assertEquals(1, $this->avis_erik_club->getStatut());
	}

	public function testIsModerationOK() {
		$this->assertTrue($this->avis_erik_club->isModerationOK());
	}
}



class AdminAvisModerationControllerCmsTest extends Admin_AbstractControllerTestCase {
	public function setUp() { 
		parent::setUp();

		$this->mock_sql = $this->getMockBuilder('Class_Systeme_Sql')
			->disableOriginalConstructor()
			->getMock();
		Zend_Registry::set('sql', $this->mock_sql);

		$this->mock_sql
			->expects($this->at(1))
			->method('fetchAll')
			->with("Select * from cms_avis Where STATUT=0 AND ABON_OU_BIB=0 order by DATE_AVIS DESC")
			->will($this->returnValue(array()));

		$this->mock_sql
			->expects($this->at(2))
			->method('fetchAll')
			->with("Select * from cms_avis Where STATUT=0 AND ABON_OU_BIB=1 order by DATE_AVIS DESC")
			->will($this->returnValue(array()));

		$this->dispatch('/admin/modo/aviscms');
	}

	/** @test */
	public function h4ShouldContainAucunAvisAModerer() {
		$this->assertXPathContentContains('//h4', "Il n'y a aucun avis à modérer");
	}
}



?>