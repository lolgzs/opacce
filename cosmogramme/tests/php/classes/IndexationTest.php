<?php
require_once 'classe_indexation.php';

class IndexationPhonetixTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->_indexation = new indexation();
	}


	public function phonetix() {
		return [ 
			['choux', 'CHOU'],
			['escroc', 'ESKRO'],
			['paix', 'PAI'],
			['paie', 'PAI'],
			['Donnée', 'DON'],
			['Compte', 'KONT'],
			['caoutchouc', 'KAOUTCHOU']
		];
	}


	/**
	 * @test
	 * @dataProvider phonetix
	 */
	public function resultShouldBePhonetix($str, $expected) {
		$this->assertEquals($expected, $this->_indexation->phonetix($str));
	}
}

?>

