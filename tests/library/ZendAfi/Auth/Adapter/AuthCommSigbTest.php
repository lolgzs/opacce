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


class AuthAdapterCommSigbAuthenticationWithoutWebServicesTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_IntBib')
			->whenCalled('findAllBy')
			->answers([]);
	}

	/** @test */
	public function authenticateResultShouldNotBeValid() {
		$this->assertFalse($this->_adapter->authenticate()->isValid());		
	}
}




class AuthAdapterCommSigbSuccessfullAuthenticationTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		$this->setUpUserZorkInSIGB();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('save')
			->willDo(function($user) {
					$user->setId(23); 
					return true; });

		$this->_adapter = (new ZendAfi_Auth_Adapter_CommSigb())
			->setIdentity('zork_sigb')
			->setCredential('secret');
	}


	public function setUpUserZorkInSIGB() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_IntBib')
			->whenCalled('findAllBy')
			->with(['comm_sigb' => Class_IntBib::allCommSigbCodes()])
			->answers([Class_IntBib::newInstanceWithId(1)
								 ->setCommSigb(Class_IntBib::COM_NANOOK),

								 Class_IntBib::newInstanceWithId(95)
								 ->setCommSigb(Class_IntBib::COM_ORPHEE),

								 Class_IntBib::newInstanceWithId(74)
								 ->setCommSigb(Class_IntBib::COM_OPSYS)]);

		Class_WebService_SIGB_Nanook::setService($this->nanook = Storm_Test_ObjectWrapper::mock());
		Class_WebService_SIGB_Orphee::setService($this->orphee = Storm_Test_ObjectWrapper::mock());
		Class_WebService_SIGB_Opsys::setService($this->opsys = Storm_Test_ObjectWrapper::mock());

		$this->nanook
			->whenCalled('getEmprunteur')
			->answers(null);

		$this->orphee
			->whenCalled('getEmprunteur')
			->answers(Class_WebService_SIGB_Emprunteur::nullInstance());

		$this->opsys
			->whenCalled('getEmprunteur')
			->answers(Class_WebService_SIGB_Emprunteur::newInstance('001234')
								->setNom('Zork')
								->setPrenom('Zinn')
								->setEMail('zork@gmail.com')
								->beValid());
		return $this;
	}
	

	/** @test */
	public function authenticateZorkShouldReturnValidResult() {
		$this->assertTrue($this->_adapter->authenticate()->isValid());
	}

	
	/** @test */
	public function resultObjectShouldBeSetUp() {
		$this->_adapter->authenticate();
		$result = $this->_adapter->getResultObject();
		$this->assertEquals(23, $result->ID_USER);
		$this->assertEquals('001234', $result->IDABON);
		$this->assertEquals(74, $result->ID_SITE);
		$this->assertEquals('Zork', $result->NOM);
		$this->assertEquals('Zinn', $result->PRENOM);
		$this->assertEquals('zork@gmail.com', $result->MAIL);
	}
}
?>