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
class Class_WebService_BabelioTest extends PHPUnit_Framework_TestCase {
	/** @test */
	public function clientShouldBeCalledWithAuthAndTimestamp() {
		$mock = $this->getMock('Zend_Http_Client');
		$mock
			->expects($this->once())
			->method('setUri')
			->with('http://www.babelio.info/sxml/999?auth=0b5490cb6a7efd50f48303315ad5ba63&timestamp=1311003574')
			->will($this->throwException(new Exception()));

		$service = new Class_WebService_Babelio();
		$service->setHttpClient($mock);
		
		$service
			->setVolatileTime(1311003574)
			->requete('999');
		
	}
	
}