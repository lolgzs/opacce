<?php 
/*
STORM is under the MIT License (MIT)

Copyright (c) 2010-2011 Agence Française Informatique http://www.afi-sa.fr

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

class Storm_Test_ObjectWrapperOnUserTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->user = new Storm_Test_Mock_User();
		$this->user
			->setName('James Bond')
			->setLicence('007');
		$this->user_wrapper = Storm_Test_ObjectWrapper
			::on($this->user)
			->setGoal('Kill GoldenEye')
			->setLocation('Shangai');
	}
	
		
	/** @test */
	public function callOnWrapperGetNameShouldCallUserGetName() {
		$this->assertEquals('James Bond', $this->user_wrapper->getName());
	}


	/** @test */
	function getWrappedObjectShouldReturnJames() {
		$this->assertEquals($this->user, $this->user_wrapper->getWrappedObject());
	}


	/** @test */
	public function callOnWrapperGetLicenceShouldCallUserGetLicence() {
		$this->assertEquals('007', $this->user_wrapper->getLicence());
	}


	/** @test */
	public function zorkShouldNotHaveBeenCalled() {
		$this->assertFalse($this->user_wrapper->methodHasBeenCalled('zork'));
	}


	/** @test */
	public function setLocationParisShouldNotHaveBeenCalled() {
		$this->assertFalse($this->user_wrapper->methodHasBeenCalledWithParams('setLocation', 
																																					array('Paris')));
	}


	/** @test */
	public function setLocationShangaiShouldNotHaveBeenCalled() {
		$this->assertTrue($this->user_wrapper->methodHasBeenCalledWithParams('setLocation', 
																																				 array('Shangai')));
	}


	/** @test */
	public function setLocationShouldHaveBeenCalled() {
		$this->assertTrue($this->user_wrapper->methodHasBeenCalled('setLocation'));
	}


	/** @test */
	public function setLocationParamsShouldContainShangai() {
		$this->assertEquals(array('Shangai'), 
												$this->user_wrapper->getAttributesForLastCallOn('setLocation'));
	}


	/** @test */
	public function callSetGoalOnWrapperShouldCallSetGoalOnUser() {
		$this->assertEquals('Kill GoldenEye', $this->user->getGoal());
	}


	/** @test */
	public function setGoalParamsShouldContainKillGoldenEye() {
		$this->assertEquals(array('Kill GoldenEye'), 
												$this->user_wrapper->getAttributesForLastCallOn('setGoal'));
	}
	

	/** @test  */
	public function setGoalShouldHaveBeenCalled() {
		$this->assertTrue($this->user_wrapper->methodHasBeenCalled('setGoal')); 
	}	


	/** @test */
	public function setExpectationOnGetNameToReturnOSS117() {
		$this->user_wrapper->whenCalled('getName')->answers('OSS117');
		$this->assertEquals('OSS117', $this->user_wrapper->getName());	
	}


	/** @test */
	public function setExpectationOnGetLocationToReturnMiami() {
		$this->user_wrapper->whenCalled('getLocation')->answers('Miami');
		$this->assertEquals('Miami', $this->user_wrapper->getLocation());	
	}


	/**
	 * @expectedException Storm_Test_ObjectWrapperException
	 * @expectedExceptionMessage Method 'zork' has never been called
	 * @test
	 */
	public function shouldRaiseExceptionWhenGettingAttributesForZork() {
    $this->user_wrapper->getAttributesForLastCallOn('zork');
	}
}
?>