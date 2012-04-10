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
require_once 'Class/Newsletter.php';
require_once 'ModelTestCase.php';

class NewsletterFixtures {
	public static function nouveautesClassique() {
		return array('id' => 1,
								 'titre' => 'Nouveautés classique',
								 'contenu' => 'Notre sélection du mois');
	}
	
	public static function animations() {
		return array('id' => 2,
								 'titre' => 'Animations',
								 'contenu' => 'Pour les jeunes');
	}

	public static function all() {
		return array(self::nouveautesClassique(), 
								 self::animations());
	}
}


class NewsletterTestFindAll extends ModelTestCase {
	public function setUp() {
		$this->_setFindAllExpectation('Class_Newsletter', NewsletterFixtures::all());
		$this->newsletters = Class_Newsletter::getLoader()->findAll();
	}

	public function testFirstIsNouveauteClassique() {
		$nouveaute = $this->newsletters[0];
		$this->assertEquals(1, $nouveaute->getId());
		$this->assertEquals('Nouveautés classique', $nouveaute->getTitre());
		$this->assertEquals('Notre sélection du mois', $nouveaute->getContenu());
	}

	public function testSecondIsAnimations() {
		$animations = $this->newsletters[1];
		$this->assertEquals(2, $animations->getId());
		$this->assertEquals('Animations', $animations->getTitre());
		$this->assertEquals('Pour les jeunes', $animations->getContenu());
	}
}



class NewsletterTestFindById extends ModelTestCase {
	public function testFindByIdOneReturnsNouveautes() {
		$this->_setFindExpectation('Class_Newsletter', NewsletterFixtures::nouveautesClassique(), 1);
		$nouveaute  = Class_Newsletter::getLoader()->find(1);
		$this->assertEquals(1, $nouveaute->getId());
	}


	public function testFindByIdTwoReturnsAnimations() {
		$this->_setFindExpectation('Class_Newsletter',  NewsletterFixtures::animations(), 2);
		$animations  = Class_Newsletter::getLoader()->find(2);
		$this->assertEquals(2, $animations->getId());
	}
}


class NewsletterTestNew extends ModelTestCase {
	public function testGetUsersReturnsEmptyArray() {
		$newsletter = new Class_Newsletter();
		$this->assertEquals(array(), $newsletter->getUsers());
	}
}


class NewsletterTestSave extends ModelTestCase {
	public function setUp() {
		$this->tbl_newsletters = $this->_buildTableMock('Class_Newsletter',
																						array('insert','update'));
	}
	
	public function testSaveNewNewsletter() {
		$this->tbl_newsletters
			->expects($this->once())
			->method('insert')
			->with(array('titre' => 'Conférence',
									 'contenu' => 'Pourquoi PHP sucks et Smalltalk rulez'));

		$conference = new Class_Newsletter();
		$conference
			->setTitre('Conférence')
			->setContenu('Pourquoi PHP sucks et Smalltalk rulez');
		$conference->save();
	}


	public function testSaveExistingNewsletter() {
		$this->tbl_newsletters
			->expects($this->once())
			->method('update')
			->with(array('id' => 2,
									 'titre' => 'Animations',
									 'contenu' => 'Pour les jeunes et les moins jeunes'),
						 'id=\'2\'');

		Class_Newsletter::getLoader()
			->newFromRow(NewsletterFixtures::animations())
			->setContenu('Pour les jeunes et les moins jeunes')
			->save();
	}
}


class NewsletterWithoutUserTestDelete extends ModelTestCase {
	protected function _expectDelete($id, $fixture) {
		$this
			->_buildTableMock('Class_Newsletter', array('delete'))
			->expects($this->once())
			->method('delete')
			->with('id='.$id);

		Class_Newsletter::getLoader()
			->newFromRow($fixture)
			->delete();
	}


	public function testDeleteNouveautesClassiqueCallsDeleteWithIdOne() {
		$this->_expectDelete(1, NewsletterFixtures::nouveautesClassique());
	}


	public function testDeleteAnimationsCallsDeleteWithId2() {
		$this->_expectDelete(2, NewsletterFixtures::animations());
	}
}


?>