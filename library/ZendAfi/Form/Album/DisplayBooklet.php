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

class ZendAfi_Form_Album_DisplayBooklet extends ZendAfi_Form_Album_DisplayBookletAbstract {
	public function getGroupDefinitions() {
		$textInputWithLabel = function($label) {
			return ['element' => 'text',
							'options' => ['label' => $label,
														'size' => 3,
														'validators' => ['int']]];};

		return array_merge(
			parent::getGroupDefinitions(),
			['thumbnails_left_page' => [
				'legend' => 'Page de gauche',
				'elements' => [
					'thumbnail_left_page_crop_top' => $textInputWithLabel('Rognage haut'),
					'thumbnail_left_page_crop_right' => $textInputWithLabel('Rognage droit'),
					'thumbnail_left_page_crop_bottom' => $textInputWithLabel('Rognage bas'),
					'thumbnail_left_page_crop_left' => $textInputWithLabel('Rognage gauche')]],
													
			 'thumbnails_right_page' => [
				 'legend' => 'Page de droite',
				 'elements' => [
					 'thumbnail_right_page_crop_top' => $textInputWithLabel('Rognage haut'),
					 'thumbnail_right_page_crop_right' => $textInputWithLabel('Rognage droit'),
					 'thumbnail_right_page_crop_bottom' => $textInputWithLabel('Rognage bas'),
					 'thumbnail_right_page_crop_left' => $textInputWithLabel('Rognage gauche')]]]);
	}
}

?>