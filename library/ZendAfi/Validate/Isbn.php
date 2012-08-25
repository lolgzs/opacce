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
class ZendAfi_Validate_Isbn extends Zend_Validate_Abstract {
	const INVALID_ISBN = 'invalidIsbn';
	
	protected $_messageTemplates = array(self::INVALID_ISBN   => "'%value%' n'est pas un ISBN valide");
	
	public function isValid($value)	{
		if ('' === $valueString = preg_replace('/[\s\.\-\_]/', '', (string)$value))
			return true;

		$this->_setValue($valueString);
		
		if ($this->isISBN10Valid($valueString) || $this->isISBN13Valid($valueString)) 
			return true;

		$this->_error(self::INVALID_ISBN);
		return false;
	}


	/** cf http://en.wikipedia.org/wiki/International_Standard_Book_Number#ISBN-13_check_digit_calculation */
	public function isISBN13Valid($n) {
    $check = 0;
    for ($i = 0; $i < 13; $i+=2) $check += substr($n, $i, 1);
    for ($i = 1; $i < 12; $i+=2) $check += 3 * substr($n, $i, 1);
    return $check % 10 == 0;
	}


	/** cf http://en.wikipedia.org/wiki/International_Standard_Book_Number#ISBN-10_check_digit_calculation */
	public function isISBN10Valid($ISBN10){
		if(strlen($ISBN10) != 10)
			return false;
 
		$a = 0;
		for($i = 0; $i < 10; $i++){
			if ($ISBN10[$i] == "X"){
				$a += 10*intval(10-$i);
			} else { $a += intval($ISBN10[$i]) * intval(10-$i); }
		}
		return ($a % 11 == 0);
	}
}
?>

