<?php
require_once 'classe_iso2709.php';

abstract class Iso2709RecordTestCase extends PHPUnit_Framework_TestCase {
	protected $_old_sql;
	protected $_record;

	public function setUp() {
		global $sql;
		$this->_old_sql = $sql;
		$sql = $this->getMockBuilder('sql')
			->disableOriginalConstructor()
			->getMock();
		$sql->expects($this->any())
			->method('fetchOne')
			->will($this->returnValue(''));

		$this->_record = new iso2709_record();
		$this->_record->setNotice('');
	}


	public function tearDown() {
		global $sql;
		$sql = $this->_old_sql;
	}
}



class Iso2709RecordAddSerializedNullTest extends Iso2709RecordTestCase {
	public function setUp() {
		parent::setUp();
		$this->_record->addSerializedFields(null);
	}


	/** @test */
	public function addSerializedNullShouldDoNothing() {
		$this->assertEquals(0, count($this->_record->getInnerData()));
	}

}



class Iso2709RecordAddSerializedSimpleTest extends Iso2709RecordTestCase {
	public function setUp() {
		parent::setUp();
		$this->_record->addSerializedFields(serialize(array('200$a' => 'Le magnifique',
																												'856$a' => 'http://www.google.fr?q=magnifico')));
	}

	/** @test */
	public function titleShouldBeLeMagnifique() {
		$titles = $this->_record->get_subfield('200', 'a');
		$this->assertEquals('Le magnifique', $titles[0]);
	}

	
	/** @test */
	public function urlShouldBeCorrect() {
		$urls = $this->_record->get_subfield('856', 'a');
		$this->assertEquals('http://www.google.fr?q=magnifico', $urls[0]);
	}
}



class Iso2709RecordAddSerializedRepeatedFieldTest extends Iso2709RecordTestCase {
	protected $_subfields;

	public function setUp() {
		parent::setUp();
		$fields = array(array('field' => '856$a',
													'data' => 'http://media.universcine.com/7e/1d/7e1dc11e-7d56-11e1-99aa-8775a2d902d1.jpg'),
										array('field' => '856$a',
													'data' => 'http://media.universcine.com/aa/ee/iuiueuie-iued-uiei-iuei-iueuieuieeee.gif'));
		$this->_record->addSerializedFields(serialize($fields));
		$this->_subfields = $this->_record->get_subfield('856', 'a');
	}


	/** @test */
	public function shouldHaveTwo856Fields() {
		$this->assertEquals(2, count($this->_subfields));
	}
}




class Iso2709RecordAddSerializedRepeatedWithSubfieldsTest extends Iso2709RecordTestCase {
	protected $_subfields;

	public function setUp() {
		parent::setUp();
		$fields = array(array('field' => '856',
													'data' => array('x' => 'poster',
																					'a' => 'http://media.universcine.com/7e/1d/7e1dc11e-7d56-11e1-99aa-8775a2d902d1.jpg')),
										array('field' => '856',
													'data' => array('x' => 'trailer',
																					'a' => 'http://media.universcine.com/aa/ee/iuiueuie-iued-uiei-iuei-iueuieuieeee.mp4')));
		$this->_record->addSerializedFields(serialize($fields));
		$this->_subfields = $this->_record->get_subfield('856', 'a', 'x');
	}


	/** @test */
	public function shouldHaveTwo856Fields() {
		$this->assertEquals(2, count($this->_subfields));
	}


	/** @test */
	public function firstSubfieldXShouldBePoster() {
		$this->assertEquals('poster', $this->_subfields[0]['x']);
	}


	/** @test */
	public function secondSubfieldXShouldBeTrailer() {
		$this->assertEquals('trailer', $this->_subfields[1]['x']);
	}
}




class Iso2709RecordInvalidAddFieldTest extends Iso2709RecordTestCase {
	/** @test */
	public function withoutEnoughParamsShouldReturnFalse() {
		$this->assertFalse($this->_record->add_field());
	}


	/** @test */
	public function withNotANumberLabelShouldReturnFalse() {
		$this->assertFalse($this->_record->add_field('a', '', 'z'));
	}


	/** @test */
	public function withTooLongIndicateurShouldReturnFalse() {
		$this->assertFalse($this->_record->add_field('856', '88888888', 'z'));
	}


	/** @test */
	public function withNotCompleteNumberLabelShouldReturnFalse() {
		$this->assertFalse($this->_record->add_field('8a7', '', 'Z'));
	}
}




class Iso2709RecordGetNoticeTest extends Iso2709RecordTestCase {
	/** @test */
	public function getNoticeShouldReturnUtf8Encoded() {

		$notice="01178cam  22003251i 450 0010009000000050017000090100034000260350026000600390015000860730018001011000041001191010013001601020007001731050018001801060006001982000036002042100035002402150064002752250020003393300219003594100020005787000053005987020045006518010033006968010025007298300028007548520058007829900006008409930006008461/12139420110914113217.0  a2-211-03401-2bRel.d9.80 EUR  aELC737022z8745000116  a0638020148 0a9782211034012  a20110703d1987    |  |0fre|0103||||ba1 afrecger  aFR  a        0||y|  ar1 aLollipopfChristian Nöstlinger  aPariscEcole des loisirsd1987  a1 vol. (121 p.)cillustrations en noir et blancd22 x 16 cm2 aNeufx0295-7191  aL'histoire d'un petit garçon qui s'est choisi comme nom Lollipop (qui signifie sucette en américain). Quand Lollipop regarde à travers une sucette presque tous ses problèmes sont résolus. A partir de neuf ans. 0tNeufx0295-7191 1112012357aNöstlingerbChristinef1936-....4070 1112072714aFriotbBernardf1951-....4730 3aFRbElectrec20110703gAFNOR 0bplivbd_11jan2011.xls  aELECTREElectre 20110914  aMOULqMOJEf0638020148g0638020148kJR NOSrLFJn0u1  a1  a1";

		$this->_record->setNotice($notice);
		$this->assertEquals("Lollipop",$this->_record->get_subfield("200","a")[0]);
		$this->assertEquals("Christian Nöstlinger",$this->_record->get_subfield("200","f")[0]);
		$this->assertEquals("Friot",$this->_record->get_subfield("702","a")[0]);
		$this->assertEquals("ELECTREElectre 20110914", $this->_record->get_subfield("830","a")[0]);
	}

}




class Iso2709RecordAnsidecodeTest extends Iso2709RecordTestCase {
	public function decoded() {
		return [ 
			["",chr(127)],
			//		["€",chr(128)],
			["",chr(129)],
			[",",chr(130)],
			//	[utf8_decode("ƒ"),chr(131)],
			//["„",chr(132)],
			["...",chr(133)],
			//["†",chr(134)],
			["‡",chr(135)],
			["",chr(136)],
			["",chr(137)],
			//		["Š",chr(138)],
			["{",chr(139)],
			["Oe",chr(140)],
			["",chr(141)],
			["Ž",chr(142)],
			["",chr(143)],
			["",chr(144)],
			["'",chr(145)],
			["'",chr(146)],
			["'",chr(147)],
			["'",chr(148)],
			[".",chr(149)],
			["-",chr(150)],
			["-",chr(151)],
			["~",chr(152)],
			["™",chr(153)],
			["š",chr(154)],
			["}",chr(155)],
			["oe",chr(156)],
			["",chr(157)],
			["ž",chr(158)],
			["Ÿ",chr(159)],
			["Â",chr(160)],
			["É",chr(0xC9)]
		];

	}


	/**
	 * @test
	 * @dataProvider decoded
	 */
	public function resultShouldBeDecoded( $expected, $str) {
		$this->assertEquals(utf8_encode($expected), $this->_record->ansi_decode($str),sprintf("%d :%d",ord(utf8_encode($expected)),ord($this->_record->ansi_decode($str))));
	}
}




class Iso2709RecordIsodecodeTest extends Iso2709RecordTestCase {
	public function decoded() {
		return [ 
			['6' , '6'],

			['a978-2-203-16703-2' , 'a978-2-203-16703-2'],

			['1 aPieter brueghel l\'histoire d\'un bâilleur et d\'une cruchefPierre Sterckx (Auteur Principal)gClaudine Roucha (Autres)', 
			 '1 aPieter brueghel l\'histoire d\'un b'.chr(0xc3).'ailleur et d\'une cruchefPierre Sterckx (Auteur Principal)gClaudine Roucha (Autres)'],

			['aFRbBibliotheque Municipale De Mazéc20121106', 'aFRbBibliotheque Municipale De Maz'.chr(0xc2).'ec20121106']
		];
	}


	/**
	 * @test
	 * @dataProvider decoded
	 */
	public function resultShouldBeDecoded( $expected,$str) {
		$this->assertEquals($expected, 
												$this->_record->iso_decode($str), 
												'value returned: '.$this->_record->iso_decode($str));
	}
}





class Iso2709RecordMarc21decodeTest extends Iso2709RecordTestCase {
	public function decoded() {
		return [ 
			['6' , '6'],

			['a978-2-203-16703-2' , 'a978-2-203-16703-2'],

			['1 aPieter brueghel l\'histoire d\'un bâilleur et d\'une cruchefPierre Sterckx (Auteur Principal)gClaudine Roucha (Autres)', 
			 '1 aPieter brueghel l\'histoire d\'un b'.chr(0xe3).'ailleur et d\'une cruchefPierre Sterckx (Auteur Principal)gClaudine Roucha (Autres)'],

			['aFRbBibliotheque Municipale De Mazéc20121106', 'aFRbBibliotheque Municipale De Maz'.chr(0xe2).'ec20121106']
		];
	}


	/**
	 * @test
	 * @dataProvider decoded
	 */
	public function resultShouldBeDecoded( $expected,$str) {
		$this->assertEquals($expected, 
												$this->_record->marc21_decode($str), 
												'value returned: '.$this->_record->marc21_decode($str));
	}
}
?>