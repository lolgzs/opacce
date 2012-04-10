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
require_once 'Class/NewsletterSubscription.php';
require_once 'Class/Users.php';

class UserSubscriptionsFixtures {
	public static function miles() {
		return array('ID_USER' => 1,
								 'LOGIN' => 'mdavis',
								 'ROLE' => 'invite',
								 'ROLE_LEVEL' => 0,
								 'PASSWORD' => 'nifnif',
								 'ID_SITE' => 1,
								 'NOM' => 'Davis',
								 'PRENOM' => 'Miles');
	}

	public static function truffaz() {
		return array('ID_USER' => 34,
								 'LOGIN' => 'etruffaz',
								 'ROLE' => 'invite',
								 'ROLE_LEVEL' => 0,
								 'PASSWORD' => 'nafnaf',
								 'ID_SITE' => 1,
								 'NOM' => 'Truffaz',
								 'PRENOM' => 'Erik');
	}

	public static function marcus() {
		return array('ID_USER' => 18,
								 'LOGIN' => 'mmiller',
								 'ROLE' => 'invite',
								 'ROLE_LEVEL' => 0,
								 'PASSWORD' => 'noufnouf',
								 'ID_SITE' => 1,
								 'NOM' => 'Miller',
								 'PRENOM' => 'Marcus');
	}

	public static function marcus_instance() {
		return Class_Users::getLoader()->newFromRow(UserSubscriptionsFixtures::marcus());
	}


	public static function concerts() {
		return array('id' => 26,
								 'titre' => 'Concerts',
								 'contenu' => 'Du festival de Jazz');
	}

	public static function concerts_instance() {
		return Class_Newsletter::getLoader()->newFromRow(UserSubscriptionsFixtures::concerts());
	}

	public static function animations() {
		return array('id' => 32,
								 'titre' => 'Animations',
								 'contenu' => 'Des noctibules');
	}

	public static function animations_instance() {
		return Class_Newsletter::getLoader()->newFromRow(UserSubscriptionsFixtures::animations());
	}

	public static function marcus_subscriptions() {
		return array('id' => 12,
								 'user_id' => 18,
								 'newsletter_id' => 26);
	}

	public static function marcus_subscriptions_instance() {
		return Class_Newsletter::getLoader()->newFromRow(UserSubscriptionsFixtures::marcus_subscriptions());
	}
}


class NewSubscriptionTest extends ModelTestCase {
	public function setUp() {
		$this->subscription = new Class_NewsletterSubscription();
	}

	public function testUserReturnsNull() {
		$this->assertEquals(null, $this->subscription->getUser());
	}

	public function testNewsletterReturnsNull() {
		$this->assertEquals(null, $this->subscription->getNewsletter());
	}
}


class MarcusSubscriptionTest extends ModelTestCase {
	public function setUp() {
		$this->_setFindExpectation('Class_NewsletterSubscription',
															 UserSubscriptionsFixtures::marcus_subscriptions(),
															 12);

		$this->marcus_newsletter = Class_NewsletterSubscription::getLoader()->find(12);
	}

	public function testSubscriptionUserIdIs18() {
		$this->assertEquals(18, $this->marcus_newsletter->getUserId());
	}


	public function testUserReturnsMarcus() {
		$this->_setFindExpectation('Class_Users', UserSubscriptionsFixtures::marcus(), 18);

		$marcus = $this->marcus_newsletter->getUser();
		$this->assertEquals('Miller', $marcus->getNom());
	}


	public function testNewsletterReturnsConcerts() {
		$this->_setFindExpectation('Class_Newsletter', UserSubscriptionsFixtures::concerts(), 26);

		$nl = $this->marcus_newsletter->getNewsletter();
		$this->assertEquals('Concerts', $nl->getTitre());
	}
}


abstract class AbstractFindAllByTestCase extends ModelTestCase {
	public function setUp() {
		$this->select = new Zend_Db_Table_Select(new Storm_Model_Table(array('name' => 'newsletters_users')));
		$rs_subscriptions = $this->_buildRowset(array(UserSubscriptionsFixtures::marcus_subscriptions()));

		$tbl_nls_users = $this->_buildTableMock('Class_NewsletterSubscription', array('fetchAll', 'select'));

		$tbl_nls_users
			->expects($this->once())
			->method('select')
			->will($this->returnValue($this->select));

		$tbl_nls_users
			->expects($this->once())
			->method('fetchAll')
			//->with($this->select)
			->will($this->returnValue($rs_subscriptions));
	}

	public function testExpectedSubscriptionInstance() {
		$this->assertEquals(18, $this->subscriptions[0]->getUserId());
		$this->assertEquals(26, $this->subscriptions[0]->getNewsletterId());
	}

	public function testOneSubscriptionReturned() {
		$this->assertEquals(1, count($this->subscriptions));
	}
}


class FindAllByNewsletterTest extends AbstractFindAllByTestCase {
	public function setUp() {
		parent::setUp();
		$concerts = Class_Newsletter::getLoader()->newFromRow(UserSubscriptionsFixtures::concerts());
		$this->subscriptions = Class_NewsletterSubscription::getLoader()
			->findAllBy(array('role' => 'newsletter',
												'model' => $concerts));
	}

	public function testExpectedSQLQuery() {
 		$this->assertEquals('SELECT `newsletters_users`.* FROM `newsletters_users` WHERE (newsletter_id=26)',
												$this->select->assemble());
	}
}


class FindAllByUserTest extends AbstractFindAllByTestCase {
	public function setUp() {
		parent::setUp();
		$marcus = Class_Users::getLoader()->newFromRow(UserSubscriptionsFixtures::marcus());

		$this->subscriptions = Class_NewsletterSubscription::getLoader()
			->findAllBy(array('role' => 'user',
												'model' => $marcus));
	}

	public function testExpectedSQLQuery() {
 		$this->assertEquals('SELECT `newsletters_users`.* FROM `newsletters_users` WHERE (user_id=18)',
												$this->select->assemble());
	}
}



class UserWithOneSubscriptionTest extends ModelTestCase {
	public function setUp() {
		$this->concerts = UserSubscriptionsFixtures::concerts_instance();
		$this->marcus = UserSubscriptionsFixtures::marcus_instance();
		$this->marcus_subscription = $this->getMock('NewsletterSubscription',
																			array('getNewsletter', 'getUser', 'delete',
																						'getId', 'getUserId', '_set', 'save', 
																						'hasBelongsToRelashionShipWith', 'isNew'));

		$this->marcus_subscription
			->expects($this->any())
			->method('getUser')
			->will($this->returnValue($this->marcus));

		$this->marcus_subscription
			->expects($this->any())
			->method('getNewsletter')
			->will($this->returnValue($this->concerts));

		$this->marcus_subscription
			->expects($this->any())
			->method('getId')
			->will($this->returnValue(123));

		$this->marcus_subscription
			->expects($this->any())
			->method('getUserId')
			->will($this->returnValue(18));

		$this->marcus_subscription
			->expects($this->any())
			->method('_set');

		$this->marcus_subscription
			->expects($this->any())
			->method('hasBelongsToRelashionShipWith')
			->will($this->returnValue(true));

		$this->marcus_subscription
			->expects($this->any())
			->method('isNew')
			->will($this->returnValue(false));


		$this->subscription_loader = $this->getMock('ModelLoader',
																								array('findAllBy', 'delete', 'findBy', 'save'));

		Class_NewsletterSubscription::setLoaderFor('Class_NewsletterSubscription',
																							 $this->subscription_loader);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
						->whenCalled('delete')
						->answers(true)
						->getWrapper()
						->whenCalled('save')
						->answers(true);
	}


	protected function _setLoaderFindAllReturnsSubscriptionFor($params) {
		$this->subscription_loader
			->expects($this->atLeastOnce())
			->method('findAllBy')
			->with($params)
			->will($this->returnValue(array($this->marcus_subscription)));
	}


	public function testMarcusSubscriptions() {
		$this->_setLoaderFindAllReturnsSubscriptionFor(array('role' => 'user',
																												 'model' => $this->marcus));

		$subscriptions = $this->marcus->getSubscriptions();
		$this->assertEquals(array($this->marcus_subscription), $subscriptions);
	}


	public function testConcertsNewsletterSubscriptions() {
		$this->_setLoaderFindAllReturnsSubscriptionFor(array('role' => 'newsletter',
																												 'model' => $this->concerts));
		$subscriptions = $this->concerts->getSubscriptions();

		$this->assertEquals(array($this->marcus_subscription), $subscriptions);
	}


	public function testConcertsNewsletterHasMarcus() {
		$this->_setLoaderFindAllReturnsSubscriptionFor(array('role' => 'newsletter',
																												 'model' => $this->concerts));

		$this->users = $this->concerts->getUsers();
		$this->assertEquals(array($this->marcus), $this->users);
	}


	public function testDeleteMarcusDeletesSubscriptions() {
		$this->_setLoaderFindAllReturnsSubscriptionFor(array('role' => 'user',
																												 'model' => $this->marcus));

		$this->marcus_subscription
			->expects($this->once())
			->method('delete');

		$this->marcus->delete();
	}


	public function testSavingMarcusWithSubscriptionsRemovedDeleteThem() {
		$this->_setLoaderFindAllReturnsSubscriptionFor(array('role' => 'user',
																												 'model' => $this->marcus));

		$this->marcus_subscription
			->expects($this->once())
			->method('delete');

		$this->marcus->setSubscriptions(array());

		$this->marcus->save();
	}


	public function testSavingMarcusWithNewslettersRemovedDeleteSubscription() {
		$this->subscription_loader
			->expects($this->atLeastOnce())
			->method('findAllBy')
			->will($this->returnValue(array($this->marcus_subscription)));

		$this->marcus_subscription
			->expects($this->once())
			->method('delete');

		$this->marcus->removeNewsletter($this->concerts);

		$this->marcus->save();
	}


	public function testSavingMarcusWithNewslettersSetSubscription() {
		$this->subscription_loader
			->expects($this->atLeastOnce())
			->method('findAllBy')
			->will($this->returnValue(array($this->marcus_subscription)));

		$animations = UserSubscriptionsFixtures::animations_instance();

		$this->subscription_loader
			->expects($this->once())
			->method('save');

		$this->marcus->setNewsletters(array($animations));

		$this->marcus->save();

		$subscriptions = $this->marcus->getSubscriptions();
		$this->assertEquals($animations, $subscriptions[0]->getNewsletter());
	}


	public function testSavingMarcusWithNewslettersAddedAddSubscription() {
		$this->subscription_loader
			->expects($this->atLeastOnce())
			->method('findAllBy')
			->will($this->returnValue(array($this->marcus_subscription)));

		$animations = UserSubscriptionsFixtures::animations_instance();

		$this->subscription_loader
			->expects($this->once())
			->method('save');

		$this->marcus->addNewsletter($animations);

		$this->marcus->save();

		$this->marcus->removeNewsletter($this->concerts);

		$subscriptions = $this->marcus->getSubscriptions();
		$this->assertEquals($animations, $subscriptions[0]->getNewsletter());
	}


	public function testSavingMarcusWithNewSubscriptionsAddThem() {
		$this->_setLoaderFindAllReturnsSubscriptionFor(array('role' => 'user',
																												 'model' => $this->marcus));

		$new_subscription = $this->getMock('NewsletterSubscription',
																			 array('save', '_set'));

		$new_subscription
			->expects($this->atLeastOnce())
			->method('_set')
			->with('user', $this->equalTo($this->marcus));

		$this->marcus->addSubscription($new_subscription);
	}



	public function testDeleteConcertsDeletesSubscriptions() {
		$this->_setLoaderFindAllReturnsSubscriptionFor(array('role' => 'newsletter',
																												 'model' => $this->concerts));

		$this->marcus_subscription
			->expects($this->once())
			->method('delete');

		$this->concerts->delete();
	}
}




?>
