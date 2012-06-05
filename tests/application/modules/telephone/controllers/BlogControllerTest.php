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

require_once 'TelephoneAbstractControllerTestCase.php';


abstract class Telephone_BlogControllerAvisActionTestCase extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$patouche = Class_Users::getLoader()
			->newInstanceWithId(2)
			->setPseudo('Patouche la mouche');

		$patouche->setAvis(array(
														 Class_AvisNotice::getLoader()
														 ->newInstanceWithId(34)
														 ->setDateAvis('2012-01-01')
														 ->setNotices(array(Class_Notice::getLoader()
																								->newInstanceWithId(3)
																								->setTitrePrincipal('Harry Potter')))
														 ->beWrittenByBibliothecaire()
														 ->setNote(3)
														 ->setEntete('bien')
														 ->setAvis('bla bla')
														 ->setUser($patouche)));
	}
}




class Telephone_BlogControllerAvisActionTest extends Telephone_BlogControllerAvisActionTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('/telephone/blog/viewavis/id/34', true);
	}


	/** @test */
	public function pageShouldDisplayEnteteAvisBien() {
		$this->assertXPathContentContains('//a', 'bien');
	}
}




class Telephone_BlogControllerViewAuteurActionTest extends Telephone_BlogControllerAvisActionTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('/telephone/blog/viewauteur/id/2', true);
	}


	/** @test */
	public function pageShouldContainsCritiqueBien() {
		$this->assertXPathContentContains('//a', 'bien');
	}
}


class Telephone_BlogControllerViewCritiquesActionTest extends Telephone_BlogControllerAvisActionTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('/telephone/blog/viewcritiques', true);
	}


	/** @test */
	public function actionShouldBeViewCritiques() {
		$this->assertAction('viewcritiques');
	}
	
}

?>