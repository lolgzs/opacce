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
// helpers sur les arrays

function array_first(array $array) {
	$values = array_values($array);
	return $values[0];
}


function array_last(array $array) {
	if (0 == $count = count($array)) 
		return null;
	$values = array_values($array);
	return $values[$count - 1];
}


function array_at($key, array $array) {
	return $array[$key];
}


function array_isset($key, array $array) {
	return array_key_exists($key, $array) and $array[$key];
}


function array_filter_by_method($items, $method, $params=null) {
	if (!is_array($params)) $params = array();

	$filtered = array();
	foreach ($items as $item)
		if (call_user_func_array(array($item, $method), $params))
			$filtered []= $item;
	return $filtered;
}



?>