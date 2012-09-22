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
abstract class ZendAfi_View_Helper_Notice_Abstract extends Zend_View_Helper_HtmlElement {
	use Trait_Translator;

	protected function getOnclick($rubrique,$isbn,$id_onglet)	{
		$action = sprintf("(this.id,'%s','%s',0,'',0)", $isbn, $rubrique);
		switch($rubrique) {
			case "avis" : $action="(this.id,'".$isbn."','avis',0,'',1)"; break;
			case "exemplaires" : $action="(this.id,'".$isbn."','exemplaires',0,'',1)"; break;
			case "tags" : 
				if (is_array($isbn))
					$isbn = $isbn["isbn"];
				$action="(this.id,'".$isbn."','tags',0,'',0)"; 
        break;
			case "resume" : $action="(this.id,'".$isbn."','resume',0,'',0)"; break;
			case "similaires" : $action="(this.id,'".$isbn."','similaires',0,'',0)"; break;
		}
		$_SESSION["onglets"][$rubrique]=$id_onglet;
		return $action;
	}



	public function selectOngletsFromPreferences($preferences, $aff_values) {
		$onglets = [];
		foreach($preferences['onglets'] as $nom => $config) {
			if (!in_array((int)$config['aff'], $aff_values)) 
				continue;

			if (!$config['titre'])
				$config['titre'] = Class_Codification::getNomOnglet($nom);

			$onglets[$nom] = $config;
		}
		return $onglets;
	}
}

?>