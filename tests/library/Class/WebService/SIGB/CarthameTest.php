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
class CarthameGetServiceTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		Class_WebService_SIGB_Carthame::reset();
		$this->service = Class_WebService_SIGB_Carthame::getService(array('url_serveur' => 'http://ifr.ro/webservices/index.php'));
	}

	/** @test */
	public function getServiceShouldCreateAnInstanceOfCarthameService() {
		$this->assertInstanceOf('Class_WebService_SIGB_Carthame_Service',
														$this->service);
	}

	/** @test */
	public function serverRootShouldBeIfrDotEU() {
		$this->assertEquals('http://ifr.ro/webservices/index.php',
												$this->service->getServerRoot());
	}

	/** @test */
	public function getServiceWithoutSchemeShouldAddHttpScheme() {
		Class_WebService_SIGB_Carthame::reset();
		$this->service = Class_WebService_SIGB_Carthame::getService(array('url_serveur' => 'ifr.ro/webservices/index.php'));
		$this->assertEquals('http://ifr.ro/webservices/index.php',
												$this->service->getServerRoot());
	}

}

abstract class CarthameTestCase extends PHPUnit_Framework_TestCase{
	/** @var Storm_Test_ObjectWrapper */
	protected $mock_web_client;

	/** @var Class_WebService_SIGB_Carthame_Service */
	protected $service;

	public function setUp() {
		$this->mock_web_client = Storm_Test_ObjectWrapper::on(new Class_WebService_SimpleWebClient())
																->whenCalled('open_url')
																->answers('')
																->getWrapper();

		$this->service = Class_WebService_SIGB_Carthame_Service::newInstance()
			->setServerRoot('http://ifr.ro/webservices/index.php')
			->setWebClient($this->mock_web_client);

	}
}


class CarthameNoticeAlbatorTest extends CarthameTestCase {
	/** @var Class_WebService_SIGB_Notice */
	protected $albator;

	public function setUp() {
		parent::setUp();

		$this->mock_web_client
			->whenCalled('open_url')
			->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=copyDetails&nn=I86355')
			->answers(CarthameTestFixtures::createAnonymousNoticeXml());

		$this->albator = $this->service->getNotice('I86355');
	}

	/** @test */
	public function shouldAnswerOneNotice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->albator);
	}


	/** @test */
	public function firstCopyGetDisponibiliteShouldReturnDisponible() {
		$copies = $this->albator->getExemplaires();
		$this->assertEquals('Disponible', $copies[0]->getDisponibilite());
	}
}



class CarthameAnonymousNoticeTest extends CarthameTestCase {
	/** @var Class_WebService_SIGB_Notice */
	protected $anonymous;

	public function setUp() {
		parent::setUp();

		$this->mock_web_client
			->whenCalled('open_url')
			->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=copyDetails&nn=xxx')
			->answers(CarthameTestFixtures::createAnonymousNoticeXml());

		$this->anonymous = $this->service->getNotice('xxx');

	}

	/** @test */
	public function shouldAnswerOneNotice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->anonymous);
	}


	/** @test */
	public function noticeIdShouldBeX108() {
		$this->assertEquals('X108', $this->anonymous->getId());
	}

	/** @test */
	public function noticeShouldHaveTwoCopies() {
		$this->assertEquals(2, count($this->anonymous->getExemplaires()));
	}

	/** @test */
	public function firstCopyIdShouldBe45698() {
		$copies = $this->anonymous->getExemplaires();
		$this->assertEquals('45698', $copies[0]->getId());
	}


	/** @test */
	public function firstCopyGetDisponibiliteShouldReturnDisponible() {
		$copies = $this->anonymous->getExemplaires();
		$this->assertEquals('Disponible', $copies[0]->getDisponibilite());
	}


	/** @test */
	public function firstCopyBarCodeShouldBe786516467646167() {
		$copies = $this->anonymous->getExemplaires();
		$this->assertEquals('786516467646167', $copies[0]->getCodeBarre());
	}

	/** @test */
	public function firstCopyShouldBeNotBeReservable() {
		$copies = $this->anonymous->getExemplaires();
		$this->assertFalse($copies[0]->isReservable());
	}

	/** @test */
	public function firstCopyReturnDateShouldBeNull() {
		$copies = $this->anonymous->getExemplaires();
		$this->assertNull($copies[0]->getDateRetour());
	}

	/** @test */
	public function secondCopyIdShouldBe45699() {
		$copies = $this->anonymous->getExemplaires();
		$this->assertEquals('45699', $copies[1]->getId());
	}

	/** @test */
	public function secondCopyBarCodeShouldBe88446464646() {
		$copies = $this->anonymous->getExemplaires();
		$this->assertEquals('88446464646', $copies[1]->getCodeBarre());
	}

	/** @test */
	public function secondCopyShoudBeReservable() {
		$copies = $this->anonymous->getExemplaires();
		$this->assertTrue($copies[1]->isReservable());
	}

	/** @test */
	public function secondCopyReturnDateShouldBe20111111() {
		$copies =	$this->anonymous->getExemplaires();
		$this->assertEquals('11/11/2011', $copies[1]->getDateRetour());
	}

}

class CarthameEmprunteurPatrickBTest extends CarthameTestCase {
	/** @var Class_WebService_SIGB_Emprunteur */
	protected $emprunteur;

	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findFirstBy')
			->with(array('id_origine' => 'L8984'))
			->answers(Class_Exemplaire::getLoader()
								->newInstanceWithId(3)
								->setIdOrigine('L8984')
								->setIdNotice('123')
								->setIdBib(47));

		Class_Notice::getLoader()
			->newInstanceWithId(123)
			->setTitrePrincipal('Harry Potter')
			->setAuteurPrincipal('JK Rowling');

		Class_Bib::getLoader()
			->newInstanceWithId(47)
			->setLibelle('Bucarest');


		$this->mock_web_client->whenCalled('open_url')
													->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=login&username=pbarroca&password=1974')
													->answers(CarthameTestFixtures::createEmprunteurPatrickBLoginXml())
													->getWrapper()
													->whenCalled('open_url')
													->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=accountDetails&userid=3')
													->answers(CarthameTestFixtures::createEmprunteurPatrickBXml())
													;

		$this->emprunteur = $this->service->getEmprunteur(
													Class_Users::getLoader()->newInstance()
														->setLogin('pbarroca')
														->setPassword('1974'));

	}

	/** @test */
	public function idShouldBeThree() {
		$this->assertEquals(3, $this->emprunteur->getId());
	}

	/** @test */
	public function nomShouldBeBarroca() {
		$this->assertEquals('Barroca', $this->emprunteur->getNom());
	}

	/** @test */
	public function prenomShouldBePatrick() {
		$this->assertEquals('Patrick', $this->emprunteur->getPrenom());
	}

	/** @test */
	public function nbReservationsShouldBeThree() {
		$this->assertEquals(3, $this->emprunteur->getNbReservations());
	}

	/** @test */
	public function nbEmpruntShouldBeTwo() {
		$this->assertEquals(2, $this->emprunteur->getNbEmprunts());
	}

	/** @test */
	public function returnDateOfFirstEmpruntShouldBeTwentySixOfMay2011() {
		$this->assertEquals('26/05/2011', $this->emprunteur->getEmpruntAt(0)->getDateRetour());
	}

	/** @test */
	public function nbPretEnRetardShouldBeOne() {
		$this->assertEquals(1, $this->emprunteur->getNbPretsEnRetard());
	}

	/** @test */
	public function firstEmpruntCopyIdShouldBe45699() {
		$this->assertEquals('45699', $this->emprunteur->getEmpruntAt(0)->getExemplaire()->getId());
	}

	/** @test */
	public function firstEmpruntIdShouldBe45699() {
		$this->assertEquals('45699', $this->emprunteur->getEmpruntAt(0)->getId());
	}

	/** @test */
	public function firstEmpruntCopyNoticeShouldBeX108() {
		$this->assertEquals('X108', $this->emprunteur->getEmpruntAt(0)->getExemplaire()->getNoNotice());
	}

	/** @test */
	public function secondEmpruntCopyIdShouldBe46666() {
		$this->assertEquals('46666', $this->emprunteur->getEmpruntAt(1)->getExemplaire()->getId());
	}

	/** @test */
	public function secondEmpruntCopyNoticeShouldBeZ6PO() {
		$this->assertEquals('Z6PO', $this->emprunteur->getEmpruntAt(1)->getExemplaire()->getNoNotice());
	}

	/** @test */
	public function firstReservationCopyNoticeShouldBeMillenium() {
		$this->assertEquals('Millenium', $this->emprunteur->getReservationAt(0)->getExemplaire()->getNoNotice());
	}

	/** @test */
	public function firstReservationStatusShouldBeEnAttente() {
		$this->assertEquals('En attente', $this->emprunteur->getReservationAt(0)->getEtat());
	}

	/** @test */
	public function firstReservationIdShouldBeMillRes() {
		$this->assertEquals('MillRes', $this->emprunteur->getReservationAt(0)->getId());
	}


	/** @test */
	public function secondReservationCopyNoticeShouldBeL8984() {
		$this->assertEquals('L8984', $this->emprunteur->getReservationAt(1)->getExemplaire()->getNoNotice());
	}

	/** @test */
	public function secondReservationTitreShouldBeHarryPotter() {
		$this->assertEquals('Harry Potter', $this->emprunteur->getReservationAt(1)->getTitre());
	}

	/** @test */
	public function secondReservationAuteurShouldBeJKRowling() {
		$this->assertEquals('JK Rowling', $this->emprunteur->getReservationAt(1)->getAuteur());
	}

	/** @test */
	public function secondReservationBibliothequeShouldBeBucarest() {
		$this->assertEquals('Bucarest', $this->emprunteur->getReservationAt(1)->getBibliotheque());
	}

	/** @test */
	public function secondReservationStatusShouldBeDisponible() {
		$this->assertEquals('Disponible', $this->emprunteur->getReservationAt(1)->getEtat());
	}

	/** @test */
	public function secondReservationIdShouldBeHarrRes() {
		$this->assertEquals('HarrRes', $this->emprunteur->getReservationAt(1)->getId());
	}

	/** @test */
	public function thirdReservationCopyNoticeShouldBeAlAmut() {
		$this->assertEquals('Al Amut', $this->emprunteur->getReservationAt(2)->getExemplaire()->getNoNotice());
	}

	/** @test */
	public function thirdReservationStatusShouldBeEnTransfert() {
		$this->assertEquals('En transfert', $this->emprunteur->getReservationAt(2)->getEtat());
	}

	/** @test */
	public function thirdReservationIdShouldBeAmutRes() {
		$this->assertEquals('AmutRes', $this->emprunteur->getReservationAt(2)->getId());
	}

	/** @test */
	public function thirdReservationTitreShouldBeEmpty() {
		$this->assertEmpty($this->emprunteur->getReservationAt(2)->getTitre());
	}

	/** @test */
	public function thirdReservationAuteurShouldBeEmpty() {
		$this->assertEmpty($this->emprunteur->getReservationAt(2)->getAuteur());
	}

	/** @test */
	public function thirdReservationBibliothequeShouldBeEmpty() {
		$this->assertEmpty($this->emprunteur->getReservationAt(2)->getBibliotheque());
	}


}

class CarthameOperationsTest extends CarthameTestCase {
	public function setUp() {
		parent::setUp();
		$this->_exemplaire_millenium = Class_Exemplaire::getLoader()
																							->newInstanceWithId(123)
																							->setIdOrigine('Millenium');
	}


	/** @test */
	public function reserverExemplaireShouldReturnSuccessIfNoError() {
		$this->mock_web_client
			->whenCalled('open_url')
			->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=login&username=pbarroca&password=1974')
			->answers(CarthameTestFixtures::createEmprunteurPatrickBLoginXml())
			
			->whenCalled('open_url')
			->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=reserveInfo&userid=3&nn=Millenium')
			->answers('<?xml version="1.0" encoding="utf-8"?>
					<root>
						<reservationInfo>
							<sigb>KTM</sigb>
							<user>16644</user>
							<nn>CL16028</nn>
							<site id="2" coche="luj-Napoca">=F</site>
							<error>0</error>
							<code>0</code>
						</reservationInfo>
					</root>')
			
			->whenCalled('open_url')
			->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=reserveDocument&userid=3&nn=Millenium&site=2')
			->answers('<?xml version="1.0"?>
					<root>
						<reservationDoc>
							<sigb>ktm</sigb>
							<user>xxx</user>
							<nn>Millenium</nn>
							<infores>Millenium</infores>
							<error>1</error>
							<code>1</code>
						</reservationDoc>
					</root>');

		$this->assertEquals(
			array('statut' => true, 'erreur' => ''),
			$this->service->reserverExemplaire(Class_Users::getLoader()->newInstance()
																				         ->setLogin('pbarroca')
																								 ->setPassword('1974'),
																				 $this->_exemplaire_millenium,
																				 'Cluj-Napoc'));

	}


	/** @test */
	public function reserverExemplaireInAnNotAllowedSiteShouldReturnAvailableSites() {
		$this->mock_web_client
			->whenCalled('open_url')
			->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=login&username=pbarroca&password=1974')
			->answers(CarthameTestFixtures::createEmprunteurPatrickBLoginXml())
			
			->whenCalled('open_url')
			->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=reserveInfo&userid=3&nn=Millenium')
			->answers('<?xml version="1.0" encoding="utf-8"?>
					<root>
						<reservationInfo>
							<sigb>KTM</sigb>
							<user>16644</user>
							<nn>CL16028</nn>
							<site id="3" coche="Iasi">Iasi</site>
							<site id="4" coche="Timis">Timisoara</site>
							<error>0</error>
							<code>0</code>
						</reservationInfo>
					</root>');

		$this->assertEquals(
			array('statut' => false, 'erreur' => 'Réservation impossible. Autorisée seulement sur Iasi,Timisoara'),
			$this->service->reserverExemplaire(Class_Users::getLoader()->newInstance()
																				         ->setLogin('pbarroca')
																								 ->setPassword('1974'),
																				 $this->_exemplaire_millenium,
																				 'Cluj-Napoc'));

	}


	/** @test */
	public function reserverExemplaireShouldReturnErrorIfFail() {
		Zend_Registry::get('translate')->setLocale('fr');
		$this->assertReserveInfoMessageIs('101', 'Réservation interdite au public');
	}


	public function assertReserveInfoMessageIs($code, $message) {
		$this->mock_web_client
			->whenCalled('open_url')
			->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=login&username=pbarroca&password=1974')
			->answers(CarthameTestFixtures::createEmprunteurPatrickBLoginXml())

			->whenCalled('open_url')
			->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=reserveInfo&userid=3&nn=Millenium')
			->answers('<?xml version="1.0"?>
											<root>
												 <reservationInfo>
													 <sigb>ktm</sigb>
													 <user>3</user>
													 <nn>Millenium</nn>
													 <error>1</error>
													 <code>'.$code.'</code>
												 </reservationInfo>
											 </root>')

			->beStrict();

		$this->assertEquals(
			array('statut' => false, 'erreur' => $message),
			$this->service->reserverExemplaire(Class_Users::getLoader()->newInstance()
														->setLogin('pbarroca')
														->setPassword('1974'), $this->_exemplaire_millenium, '1')
		);
	}


	/** @test */
	public function reserverExemplaireShouldReturnEchecDeLaReservationIfFailAndUnknownErrorMessage() {
		$this->assertReserveInfoMessageIs('999', 'Réservation impossible');
	}


	/** @test */
	public function supprimerReservationShouldReturnSuccessIfNoError() {
		$this->mock_web_client->whenCalled('open_url')
													->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=reserveCancel&resid=MillRes')
													->answers('<?xml version="1.0"?>
																				<root>
																					<reservationCancel>
																						<sigb>KTM</sigb>
																						<resid>MillRes</resid>
																						<error>0</error>
																						<code></code>
																					</reservationCancel>
																				</root>
																				');

		$this->assertEquals(
			array('statut' => true, 'erreur' => ''),
			$this->service->supprimerReservation(Class_Users::getLoader()->newInstance()
														->setLogin('pbarroca')
														->setPassword('1974'), 'MillRes')
		);

	}

	/** @test */
	public function supprimerReservationShouldReturnErrorIfFail() {
		$this->mock_web_client->whenCalled('open_url')
													->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=reserveCancel&resid=HarrRes')
													->answers('<?xml version="1.0"?>
<root>
	<reservationCancel>
		<sigb>KTM</sigb>
		<resid>HarrRes</resid>
		<error>-1</error>
		<code>Error from WS</code>
	</reservationCancel>
</root>
');

		$this->assertEquals(
			array('statut' => false, 'erreur' => 'Error from WS'),
			$this->service->supprimerReservation(Class_Users::getLoader()->newInstance()
														->setLogin('pbarroca')
														->setPassword('1974'), 'HarrRes')
		);
	}

	/** @test */
	public function prolongerPretShouldReturnSuccessIfNoError() {
		$this->mock_web_client->whenCalled('open_url')
													->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=prolongLoan&loanid=Millenium')
													->answers('<?xml version="1.0"?>
<root>
	<prolongation>
		<sigb>ktm</sigb>
		<id>Millenium</id>
		<error>0</error>
		<code>0</code>
	</prolongation>
</root
');

		$this->assertEquals(
			array('statut' => true, 'erreur' => ''),
			$this->service->prolongerPret(Class_Users::getLoader()->newInstance()
														->setLogin('pbarroca')
														->setPassword('1974'), 'Millenium')
		);

	}

	/** @test */
	public function prolongerPretShouldReturnErrorIfFail() {
		$this->mock_web_client->whenCalled('open_url')
													->with('http://ifr.ro/webservices/index.php?sigb=ktm&version=standalone&action=prolongLoan&loanid=095124224')
													->answers('<?xml version="1.0"?>
<root>
	<prolongation>
		<sigb>ktm</sigb>
		<id>095124224</id>
		<error>1</error>
		<code>Error from WS</code>
	</prolongation>
</root
');

		$this->assertEquals(
			array('statut' => false, 'erreur' => 'Error from WS'),
			$this->service->prolongerPret(Class_Users::getLoader()->newInstance()
														->setLogin('pbarroca')
														->setPassword('1974'), '095124224')
		);

	}

}

class CarthameTestFixtures {
	public static function createEmprunteurPatrickBXml() {
		return '<?xml version="1.0"?>
<root>
	<AbCompte>
		<F000>
			<SF3>20</SF3>
			<SFb>I</SFb>
			<SFd>5</SFd>
			<SFe>10</SFe>
		</F000>
		<F100>
			<SFa>Barroca, Patrick</SFa>
			<SFb>I</SFb>
			<SFd>F</SFd>
			<SF3>20</SF3>
		</F100>
		<F200>
			<SFa>Int&#195;&#402;&#194;&#169;rieur 5
			&#195;&#8218;&#226;&#8218;&#172;</SFa>
			<SFb>20110426</SFb>
			<SFc>20120426</SFc>
		</F200>
		<F200>
			<SFa>Ext&#195;&#402;&#194;&#169;rieur 10
			&#195;&#8218;&#226;&#8218;&#172;</SFa>
			<SFb>20110426</SFb>
			<SFc>20120426</SFc>
		</F200>
		<F400>
			<SFa>X108</SFa>
			<SFb>20110505</SFb>
			<SFc>20110526</SFc>
			<SFk>45699</SFk>
			<SFl>-2</SFl>
		</F400>
		<F400>
			<SFa>Z6PO</SFa>
			<SFb>20110505</SFb>
			<SFc>20370526</SFc>
			<SFk>46666</SFk>
			<SFl>-2</SFl>
		</F400>
		<F500>
			<SFa>Millenium</SFa>
			<SFb>20110505</SFb>
			<SFd>A</SFd>
			<SFh>MillRes</SFh>
		</F500>
		<F500>
			<SFa>L8984</SFa> <!-- Harry Potter -->
			<SFb>20110506</SFb>
			<SFd>D</SFd>
			<SFh>HarrRes</SFh>
		</F500>
		<F500>
			<SFa>Al Amut</SFa>
			<SFb>20110517</SFb>
			<SFd>T</SFd>
			<SFh>AmutRes</SFh>
		</F500>
	</AbCompte>
</root>
';
	}

	public static function createEmprunteurPatrickBLoginXml() {
		return '<?xml version="1.0"?>
<root>
	<login>
		<sigb>ktm</sigb>
		<user>80001</user>
		<id>3</id>
		<email/>
		<fullname>Patrick Barroca</fullname>
		<error>0</error>
		<code>0</code>
	</login>
</root>
';
	}


	public static function createNoticeAlbatorXml() {
		return '<item>
    <institution />
    <noticeid>I86355</noticeid>
    <Notice>
        <Unimarc>
            <F941>
                <SFh>61234</SFh>
                <SFa>0040811887</SFa>
                <SFz>Sono-Vidéothèque</SFz>
                <SFc>X KAZ</SFc>
                <SFi>20040722</SFi>
                <SFk>Institut Français de Bucarest </SFk>
                <SFv>Institut Français de Bucarest </SFv>
                <SFo>1</SFo>
                <SFr>Institut Français de Bucarest </SFr>
                <SFt>1</SFt>
            </F941>
        </Unimarc>
    </Notice>
</item>';
	}


	public static function createAnonymousNoticeXml() {
		return '<?xml version="1.0"?>
<root>
	<items>
		<item>
			<institution></institution>
			<noticeid>X108</noticeid>
			<Notice>
				<Unimarc>
					<F941>
						<SFh>45698</SFh>
						<SFa>786516467646167</SFa>
						<SFz>Adultes</SFz>
						<SFc>006.7 MEZ</SFc>
						<SFi>20110101</SFi>
						<SFk>Quetigny</SFk>
						<SFv>Quetigny</SFv>
						<SFo>1</SFo>
					</F941>
					<F941>
						<SFh>45699</SFh>
						<SFa>88446464646</SFa>
						<SFz>Adultes</SFz>
						<SFc>006.7 MEZ</SFc>
						<SFi>20090101</SFi>
						<SFk>Quetigny</SFk>
						<SFv>Quetigny</SFv>
						<SFo>3</SFo>
						<SFq>20111111</SFq>
					</F941>
				</Unimarc>
			</Notice>
		</item>
	</items>
</root>
';
	}
}
?>