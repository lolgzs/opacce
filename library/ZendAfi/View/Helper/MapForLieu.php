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

class ZendAfi_View_Helper_MapForLieu extends Zend_View_Helper_HtmlElement {
	public function mapForLieu($lieu, $options=null) {
		$full_adresse = implode(',',
														array($lieu->getAdresse(),
																	$lieu->getCodePostal(),
																	$lieu->getVille(),
																	$lieu->getPays()));

		$params = array('sensor' => 'false',
										'zoom' => 15,
										'size' => '200x200',
										'center' => $full_adresse,
										'markers' => $full_adresse);

		if (is_array($options))
			$params = array_merge($params, $options);
		
		return sprintf('<img style="cursor:pointer" '.
									 'class="google_static_map" '.
									 'src="http://maps.googleapis.com/maps/api/staticmap?%s" '.
									 'alt="%s" '.
									 'onclick="window.open(\'http://maps.google.com/maps?%s\');return false;" />',

									 http_build_query($params),
									 $lieu->getLibelle(),
									 http_build_query(array('q' => $full_adresse)));
	}
}

?>