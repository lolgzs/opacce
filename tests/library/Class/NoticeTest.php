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
require_once 'Class/Notice.php';
require_once 'ModelTestCase.php';

class NoticeFixtures extends TestFixtures {
	protected $_fixtures = array(
					 'millenium' => array('id_notice' => 12,
																'titres' => 'MILLENIUM'),
					 'potter' => array('id_notice' => 48,
														 'titres' => 'POTTER')
	);

	public static function instance() {
		return new self();
	}
}




class NoticeTestFindAll extends ModelTestCase {
	public function setUp() {
		$this->_setFindAllExpectation('Class_Notice', 'NoticeFixtures');
		$this->notices = Class_Notice::getLoader()->findAll();
	}

	public function testFirstIsMillenium() {
		$millenium = $this->notices[0];
		$this->assertEquals(12, $millenium->getIdNotice());
		$this->assertEquals(12, $millenium->getId());
		$this->assertEquals('MILLENIUM', $millenium->getTitres());
	}

	public function testSecondPotter() {		
		$potter = $this->notices[1];
		$this->assertEquals(48, $potter->getIdNotice());
		$this->assertEquals(48, $potter->getId());
		$this->assertEquals('POTTER', $potter->getTitres());
	}
}




class NoticeTestTypeDoc extends ModelTestCase {
	public function setUp() {
		$this->livre = new Class_Notice();
		$this->livre->setTypeDoc(1);

		$this->periodique = new Class_Notice();
		$this->periodique->setTypeDoc(2);
	}

	public function testIsLivre() {
		$this->assertTrue($this->livre->isLivre());
		$this->assertFalse($this->periodique->isLivre());
	}

	public function testIsPeriodique() {
		$this->assertFalse($this->livre->isPeriodique());
		$this->assertTrue($this->periodique->isPeriodique());
	}
}




class NoticeTestGetAvis extends ModelTestCase {
	public function setUp() {
		$this->avis_bib1 = new Class_AvisNotice();
		$this->avis_bib1->setAbonOuBib(1)->setNote(4);

		$this->avis_bib2 = new Class_AvisNotice();
		$this->avis_bib2->setAbonOuBib(1)->setNote(5);

		$this->avis_abon1 = new Class_AvisNotice();
		$this->avis_abon1->setAbonOuBib(0)->setNote(2);

		$this->avis_abon2 = new Class_AvisNotice();
		$this->avis_abon2->setAbonOuBib(0)->setNote(3);

		$this->notice = new Class_Notice();
		$this->notice->setAvis(array($this->avis_bib1, 
																 $this->avis_bib2,
																 $this->avis_abon1,
																 $this->avis_abon2));

	}


	public function testGetAvis() {
		$this->assertEquals(array($this->avis_bib1, 
															$this->avis_bib2,
															$this->avis_abon1,
															$this->avis_abon2),
												$this->notice->getAvis());
	}


	public function testGetAvisBibliothequaires() {	
		$this->assertEquals(array($this->avis_bib1, 
															$this->avis_bib2),
												$this->notice->getAvisBibliothequaires());
	}


	public function testGetNoteMoyenneAvisBibliothequaires() {	
		$this->assertEquals(4.5,
												$this->notice->getNoteMoyenneAvisBibliothequaires());
	}


	public function testGetAvisAbonnes() {	
		$this->assertEquals(array($this->avis_abon1, 
															$this->avis_abon2),
												$this->notice->getAvisAbonnes());
	}


	public function testGetNoteMoyenneAvisAbonnes() {	
		$this->assertEquals(2.5,
												$this->notice->getNoteMoyenneAvisAbonnes());
	}	
}

?>