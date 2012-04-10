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
include_once('VSmartFixtures.php');

class MockSoapClientForVSmart {
	protected $_wsdl;
	protected $_options;
	protected $_user_params;
	protected $_user_response;
	protected $_raise_error_on_user;

	public function __construct($wsdl, $options) {
		$this->_wsdl = $wsdl;
		$this->_options = $options;
		$this->_raise_error_on_user = false;
	}

	public function getWSDL() {
		return $this->_wsdl;
	}


	public function getOptions() {
		return $this->_options;
	}


	public function getUserParams() {
		return $this->_user_params;
	}


	public function setUserResponse($response) {
		$this->_user_response = $response;
		return $this;
	}

	public function User($user_params) {
		if ($this->_raise_error_on_user) {
			$this->_raise_error_on_user = false;
			throw new Exception('An error occured');
		}

		$this->_user_params = $user_params;
		return $this->_user_response;
	}

	public function triggerErrorOnUser() {
		$this->_raise_error_on_user = true;
	}
}


class DefaultVSmartAuthenticateServiceTest extends PHPUnit_Framework_TestCase {
	/** @test */
	function getSoapClientClassShouldReturnMappedSoapClient() {
		$this->assertEquals('Class_WebService_MappedSoapClient',
												Class_WebService_SIGB_VSmart_Service::getSoapClientClass());
	}
}


class VSmartAuthenticateServiceTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		Class_WebService_SIGB_VSmart_Service::setSoapClientClass('MockSoapClientForVSmart');
		$this->service = Class_WebService_SIGB_VSmart_Service::getService('http://12.34.56.78/moulins');
		$this->soap_client = $this->service->getSoapClient();
	}


	/** @test */
	function getSoapClientShouldBeAnInstanceOfMockSoapClient() {
		$this->assertInstanceOf('MockSoapClientForVSmart', $this->soap_client);
	}


	/** @test */
	function soapClientWsdlAdressShouldBeSSO_Authenticate_CLS_WSDL_1() {
		$this->assertEquals('http://12.34.56.78/moulins/SSO.Authenticate.CLS?WSDL=1', $this->soap_client->getWSDL());

	}


	/** @test */
	function soapClientOptionsShouldContainsCacheWSDL() {
		$this->assertEquals(array('cache_wsdl' => WSDL_CACHE_BOTH),
												$this->soap_client->getOptions());
	}


	/** @test */
	function getAuthenticateTokenForUserShouldAnswerDEC157D9C0DA11AFABCE58C56300C30B() {
		$response = new UserResponse;
		$response->UserResult = 'DEC157D9C0DA11AFABCE58C56300C30B';
		$this->soap_client->setUserResponse($response);

		$this->assertEquals('DEC157D9C0DA11AFABCE58C56300C30B',
												$this->service->getAuthenticateTokenForUser(
														Class_Users::getLoader()->newInstance()
															->setLogin('manon')
															->setPassword('21-07-2010')));
		return $this->soap_client->getUserParams();
	}


	/**
	 * @test
	 * @depends getAuthenticateTokenForUserShouldAnswerDEC157D9C0DA11AFABCE58C56300C30B
	 */
	function userEncodedUserIdShouldBeManonBase64Encoded($user_params) {
		$this->assertEquals('UkVTLm1hbm9u', $user_params->EncodedUserId);
	}


	/**
	 * @test
	 * @depends getAuthenticateTokenForUserShouldAnswerDEC157D9C0DA11AFABCE58C56300C30B
	 */
	function userEncodedPasswordShouldBeMotDePasseBase64Encoded($user_params) {
		$this->assertEquals('REFURS4yMS0wNy0yMDEw', $user_params->EncodedPassword);
	}


	/** @test */
	function withUserMario() {
		$response = new UserResponse;
		$response->UserResult = 'ABCD';
		$this->soap_client->setUserResponse($response);
		return $this->service;
	}


	/**
	 * @depends withUserMario
	 * @test
	 */
	function popupUrlForReservationShouldBeFunctionReservationWithToken($service) {
		$expected_url = sprintf('http://%s/LoginWebSso.csp?Token=ABCD&Function=Reservation&RecordNumber=%s',
														Class_WebService_SIGB_VSmart_Service::MOULINS_POPUP_SERVER,
														urlencode('2:01234'));

		$this->assertEquals($expected_url,
												$service->getPopupUrlForReservation(
													Class_Users::getLoader()->newInstance()
														->setLogin('mario')
														->setPassword('password'),
													'2/01234'));
	}


	/**
	 * @depends withUserMario
	 * @test
	 */
	function reserverExemplaireShouldReturnArrayWithPopup($service) {
		$expected_response = array('popup' => sprintf('http://%s/LoginWebSso.csp?Token=ABCD&Function=Reservation&RecordNumber=%s',
																									Class_WebService_SIGB_VSmart_Service::MOULINS_POPUP_SERVER,
																									urlencode('2:01234')));
		$this->assertEquals($expected_response,
												$service->reserverExemplaire(
													Class_Users::getLoader()->newInstance()
														->setLogin('mario')
														->setPassword('password'),

													Class_Exemplaire::getLoader()
													->newInstanceWithId(234)
													->setIdOrigine('2/01234'), 
													
													''));
	}


	/**
	 * @depends withUserMario
	 * @test
	 */
	function whenGetPopupRaiseErrorReserverExemplaireShouldReturnArrayWithErrorMessage($service) {
		$service->getSoapClient()->triggerErrorOnUser();
		$this->assertEquals(array('erreur' => 'An error occured'),
												$service->reserverExemplaire(
													Class_Users::getLoader()->newInstance()
														->setLogin('mario')
														->setPassword('password'),

													Class_Exemplaire::getLoader()
													->newInstanceWithId(234)
													->setIdOrigine('2/01234'), 

													''));
	}


	/**
	 * @depends withUserMario
	 * @test
	 */
	function popupUrlForUserInformationsShouldBeFunctionModuleADM($service) {
		$expected_url = sprintf('http://%s/LoginWebSso.csp?Token=ABCD&Function=UserActivities&Module=ADM',
														Class_WebService_SIGB_VSmart_Service::MOULINS_POPUP_SERVER);
		$this->assertEquals($expected_url,
												$service->getPopupUrlForUserInformations(
													Class_Users::getLoader()->newInstance()
														->setLogin('mario')
														->setPassword('password'),
													'2/01234'));
	}



	/**
	 * @depends withUserMario
	 * @test
	 */
	function emprunteurUserInfoUrlShouldBeFunctionModuleADM($service) {
		$expected_url = sprintf('http://%s/LoginWebSso.csp?Token=ABCD&Function=UserActivities&Module=ADM',
														Class_WebService_SIGB_VSmart_Service::MOULINS_POPUP_SERVER);

		$mock_web_client = $this->getMock('Class_WebService_SimpleWebClient');
		$mock_web_client
			->expects($this->once())
			->method('open_url')
			->with(sprintf('http://12.34.56.78/moulins/VubisSmartHttpApi.csp?fu=GetBorrower&MetaInstitution=RES&BorrowerId=mario',
										 Class_WebService_SIGB_VSmart_Service::MOULINS_POPUP_SERVER))
			->will($this->returnValue(VSmartFixtures::xmlBorrowerEvelyne()));

		$service->setWebClient($mock_web_client);
		$this->assertEquals($expected_url,
												$service->getEmprunteur(
													Class_Users::getLoader()->newInstance()
														->setLogin('mario')
														->setPassword('password')
												)->getUserInformationsPopupUrl(
													Class_Users::getLoader()->newInstance()
														->setLogin('mario')
														->setPassword('password')));
	}
}

?>
