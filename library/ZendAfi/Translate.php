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

class ZendAfi_Translate extends Zend_Translate {
	public function _() {
    $args = func_get_args();
    $num = func_num_args();

		if (!$num)
			return '';

    $args[0] = parent::_($args[0]);
        
    if($num <= 1) {
      return $args[0];
    }

    return call_user_func_array('sprintf', $args);  
	}



	/**
	 * Voir TranslateTest.php
	 *
	 * $this->translate->plural(1, 
	 *                          "Pas d'enfants manquant", 
	 *                          "%d enfant manquant sur %d", 
	 *													"%d enfants manquant sur %d", 
	 *													1, 20);
	 * ==> "1 child missing among 20"
	 */
	public function plural() {
		$args = func_get_args();

		$sentence_nb = (int)$args[0] > 1 ? 3 : $args[0] + 1;
		$sentence = $args[$sentence_nb];
		if (!$sentence)
			return '';
		$translation = $this->translate($sentence);

		$_args = array_slice($args, 4);
		array_unshift($_args, $sentence);

		return call_user_func_array(array($this, '_'), $_args);
	}
}

?>