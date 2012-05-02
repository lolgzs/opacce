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

class Class_ModeleFusion extends Storm_Model_Abstract {
	protected $_table_name = 'modele_fusion';
	protected $_table_primary = 'id';


	public static function get($name) {
		return self::getLoader()->findFirstBy(array('nom' => $name));
	}

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public function getDataSourceNamed($name) {
		return array_at($name, $this->getDataSource());
	}


	public function getContenuFusionne() {
		$contenu = $this->getContenu();
		$contenu_decode = preg_replace_callback('/\{[^\}]+\}/',
																						create_function('$matches',
																														'return html_entity_decode($matches[0],ENT_QUOTES);'),
																						$contenu);
		$matches = array();
		preg_match_all('/'.  //ex: @session_formation.stagiaires[nom, prenom]@
									 '\{'. //delimiteur
									 '([\w|\.]+)'. //session_formation.stagiaires
									 '(?:'.
									   '\[([^\]]+)\]'.     // [nom, prenom]
									 ')?'.
									 '\}'. //delimiteur
									 '/', 
									 $contenu_decode, 
									 $matches, 
									 PREG_SET_ORDER);

		foreach ($matches as $match) {
			$tag = array_shift($match);

			try {
				$tag_value = $this->getTagValueForString($match);
			} catch (Storm_Model_Exception $e) {
				continue;
			} 

			$contenu_decode = str_replace($tag,
																		$tag_value,
																		$contenu_decode);
		}

		return $contenu_decode;
	}


	public function getTagValueForString($match) {
		$attributes = explode('.',array_shift($match));

		$model = $this->getDataSourceNamed(array_shift($attributes));

		if (is_array($value = $this->getValue($model, $attributes))) 
			return $this->buildTable($value, array_shift($match));

		return htmlentities(utf8_decode($value));
	}

	
	public function getValue($modele, &$attributes) {
		if (!$next_attribute = array_shift($attributes))
			return '';

		if (!$value_or_model = $modele->callGetterByAttributeName($next_attribute))
			return '';

		if (count($attributes)==0) 
			return $value_or_model;
		
		return $this->getValue($value_or_model, $attributes);
	}


	public function buildTable($items, $columns_def_string) {
		$matches = array();

		preg_match_all('/(?:\"([^\"]+)\"\:)([\w|\.]+)?(?:\s*,\s*)?/', $columns_def_string, $matches, PREG_SET_ORDER);

		$columns = array();
		foreach ($matches as $match)
			$columns[$match[1]] = count($match)>2 ? $match[2] : '';

		$content = '<tr>';
		foreach($columns as $label => $attribute)
			$content .= '<td>'.htmlentities($label).'</td>';
		$content .= '</tr>';

			
		foreach($items as $item)
			$content .= $this->buildTableRow($item, $columns);

		return sprintf('<table>%s</table>', $content);
	}


	public function buildTableRow($model, &$attributes) {
		$row = '';

		foreach($attributes as $attribute) {
			$requested_attributes = explode('.', $attribute);
			$value = $this->getValue($model, $requested_attributes);

			$row .= sprintf('<td>%s</td>', 
											$attribute ? htmlentities(utf8_decode($value)) : '');
		}

		return sprintf('<tr>%s</tr>', $row);
	}
}


?>