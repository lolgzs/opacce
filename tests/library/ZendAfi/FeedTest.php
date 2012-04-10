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

class ZendAfi_FeedIsoTest extends PHPUnit_Framework_TestCase {
	/** @test */
	public function channelTitleShouldHaveAnIsoQuote() {
		$this->assertEquals(
			'<?xml version="1.0" encoding="iso-8859-1"?><channel><title>Toute la \'culture</title></channel>',
			ZendAfi_Feed::escapeIsoChars('<?xml version="1.0" encoding="iso-8859-1"?><channel><title>Toute la '
																			. chr(145) . 'culture</title></channel>'));
	}


	/** @test */
	public function contentShouldNotBeDetectedAsUtf() {
		$this->assertFalse(
			ZendAfi_Feed::isUtf('<?xml version="1.0" encoding="iso-8859-1"?><channel><title>Toute la '
																			. chr(145) . 'culture</title></channel>'));
	}
}




class ZendAfi_FeedUtfTest extends PHPUnit_Framework_TestCase {
	/** @test */
	public function withoutPrologShouldBeDetectedAsUtf() {
		$this->assertTrue(
			ZendAfi_Feed::isUtf('<channel><title>Toute la culture</title></channel>')
		);
	}


	/** @test */
	public function withoutEncodingInPrologShouldBeDetectedAsUtf() {
		$this->assertTrue(
			ZendAfi_Feed::isUtf('<?xml version="1.0"?><channel><title>Toute la culture</title></channel>')
		);
	}


	/** @test */
	public function utf8InPrologShouldBeDetectedAsUtf() {
		$this->assertTrue(
			ZendAfi_Feed::isUtf('<?xml version="1.0" encoding="UTF-8"?><channel><title>Toute la culture</title></channel>')
		);
	}


	/** @test */
	public function whenUtfShouldNotReplaceChars() {
		$this->assertEquals(
			'<?xml version="1.0" encoding="utf-8"?><channel><title>Toute la '
																			. chr(145) . 'culture</title></channel>',
			ZendAfi_Feed::escapeIsoChars('<?xml version="1.0" encoding="utf-8"?><channel><title>Toute la '
																			. chr(145) . 'culture</title></channel>'));
	}
}