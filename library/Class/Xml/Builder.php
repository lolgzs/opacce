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

class Class_Xml_Builder {
	public function __call($method, $arguments) {
		if (is_array($first_arg = $arguments[0]))
			return $this->_tag(array($method => $first_arg), $arguments[1]);
		return $this->_tag($method, $first_arg);
	}


	public function _tag($tag, $content) {
		if (!is_array($tag)) 
			return $this->_xmlString((string)$tag, $content);

		$attributes = '';
		foreach (current($tag) as $k => $v)
			$attributes .= ' ' . $k . '="' . $v . '"';

		return $this->_xmlString(key($tag), $content, $attributes);
	}


	public function _xmlString($name, $content, $attributes = '') {
		return '<'.$name.$attributes.'>'.$content.'</'.$name.'>';
	}
}

?>