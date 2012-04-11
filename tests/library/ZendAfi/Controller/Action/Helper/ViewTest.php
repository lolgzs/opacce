<?php

class ActionHelperViewTranslateTest extends PHPUnit_Framework_TestCase {
	/** @test */
	public function translateEmptyStringShouldReturnEmptyString() {
		$view = new ZendAfi_Controller_Action_Helper_View();
		$this->assertEquals('', $view->_(''));
	}
}


?>