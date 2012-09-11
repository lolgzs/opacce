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

class ZendAfi_View_Helper_ComboCodification extends ZendAfi_View_Helper_BaseHelper {
	const TYPE_DOC = 'type_doc';
	const SECTION = 'section';
	
	public function ComboCodification($type, $valeur_select, $events = '') {
		$profil = Class_Profil::getCurrentProfil();
		
		if (self::TYPE_DOC == $type) {
			$name = 'type_doc';
			$id = 'select_type_doc';
			$items = $this->getTypeDocs();
		}

		if (self::SECTION == $type) {
			$name = 'section';
			$id = 'select_section';

			if ($profil->getSelSection())
				$controle = ';' . $profil->getSelSection() . ';';

			$data = fetchAll('Select id_section,libelle from codif_section order by libelle');
			$items[] = array('value' => '', 'libelle' => $this->translate()->_('toutes'));
			$controle = '';
			for ($i = 0; $i < count($data); $i++) {
				$code = $data[$i]["id_section"];
				$libelle = $data[$i]["libelle"];
				if ($controle && (strpos($controle,';'.$code.';') === false))
					continue;
				if ($code)
					$items[] = array("value" => $code, "libelle" => $libelle);
			}
		}

		// Composer le html
		if ('' != $events)
			$events = ' ' . $events;
		$combo = '<select id="' . $id . '" name="' . $name . '"' . $events . ' class="typeDoc">';
		foreach ($items as $item) {
			$selected = ($valeur_select == $item["value"]) ? ' selected="selected"': '';
			$combo .= '<option value="' . $item["value"] . '"' . $selected.'>' . stripSlashes($item["libelle"]) . '</option>';
		}
		$combo .= '</select>';
		return $combo;
	}


	/**
	 * @return array
	 */
	public function getTypeDocs() {
		$items[] = array('value' => '', 'libelle' => $this->view->_('tous'));

		$used_ids = Class_TypeDoc::findUsedTypeDocIds();
		$types = array_filter(Class_TypeDoc::findAll(), 
													function ($type_doc) use ($used_ids) {
														return in_array($type_doc->getId(),
																						$used_ids);
													});

		$profil = Class_Profil::getCurrentProfil();

		$filter = array();
		if ($selection = $profil->getSelTypeDoc()) {
			$filter = explode(';', $selection);			
		}

		foreach ($types as $type) {
			if (0 < count($filter) && !in_array($type->getId(), $filter))
				continue;
				$items[] = array('value' => $type->getId(), 'libelle' => $type->getLabel());
		}

		return $items;
	}
}