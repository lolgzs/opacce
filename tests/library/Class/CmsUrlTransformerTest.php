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

class CmsUrlTransformerTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$_SERVER['HTTP_HOST'] = 'localhost';
		$this->transformer = new Class_CmsUrlTransformer;
	}


	public function testRelativeUrlToAbsolute() {
		$this->assertEquals('<p>plan: <img src="http://localhost/userfile/image.png" />'.
												'</p><img src="http://example.com/test.jpg"></img>',

												$this->transformer->forEditing('<p>plan: <img src="/userfile/image.png" />'.
																											 '</p><img src="http://example.com/test.jpg"></img>'));

		$this->assertEquals('<div> photo <img src="http://localhost/test/desc.png" /></div>',
												$this->transformer->forEditing('<div> photo <img src="/test/desc.png" /></div>'));
	}


	public function testAbsoluteToRelativeUrl() {
		$this->assertEquals('<p>plan: <img src="'.BASE_URL.'/userfile/image.png" />'.
												'</p><img src="'.BASE_URL.'/images/test.jpg"></img>'.
												'<img src="http://example.com/../test.jpg" />',
												$this->transformer->forSaving('<p>plan: <img src="http://localhost'.BASE_URL.'/userfile/image.png" />'.
																											'</p><img src="../../images/test.jpg"></img><img src="http://example.com/../test.jpg" />'));

		$this->assertEquals('<div> photo <img src="'.BASE_URL.'/test/desc.png" /></div>',
												$this->transformer->forSaving('<div> photo <img src="http://localhost'.BASE_URL.'/test/desc.png" /></div>'));
	}


	public function testChatenaySaveNonRegression() {
		$this->assertEquals('<a href="http://www.histoireencartes.com/" target="_blank">'.
												'<img alt="" src="'.BASE_URL.'/userfiles/image/Histoire/armistice.jpg" style="width: 123px; height: 93px; " />'.
												'<img alt="" src="'.BASE_URL.'/userfiles/image/Histoire/armistice.jpg" />'.
												'<br /></a>',

												$this->transformer->forSaving('<a href="http://www.histoireencartes.com/" target="_blank">'.
																											'<img alt="" src="'.BASE_URL.'/userfiles/image/Histoire/armistice.jpg" style="width: 123px; height: 93px; " />'.
																											'<img alt="" src="'.BASE_URL.BASE_URL.'/userfiles/image/Histoire/armistice.jpg" />'.
																											'<br /></a>'));
	}
}


?>