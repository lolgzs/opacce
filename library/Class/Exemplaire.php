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

class Class_Exemplaire extends Storm_Model_Abstract {
	protected $_table_name = 'exemplaires';
	protected $_table_primary = 'id'; 

	protected $_belongs_to = array(
																 'notice' => array('model' => 'Class_Notice',
																									 'referenced_in' => 'id_notice'),

																 'bib' => array('model' => 'Class_Bib',
																								'referenced_in' => 'id_bib'),

																 'album' => array('model' => 'Class_Album',
																									'referenced_in' => 'id_origine'));

	protected $_default_attribute_values = array('id_origine' => null,
																							 'code_barres' => null);

	protected $_sigb_exemplaire;


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	
	public function isPrete() {
		return $this->hasPret();
	}

	
	public function getPret() {
		return Class_Pret::getLoader()->findFirstBy(array('id_site' => $this->getIdBib(),
																											'id_notice_origine' => $this->getIdOrigine(),
																											'code_barres' => $this->getCodeBarres(),
																											'EN_COURS' => 1));
	}


	public function getDateRetour() {
		if ($this->hasPret())
			return $this->getPret()->getDateRetour();
		return '';
	}


	public function getSigbExemplaire() {
		if (null !== $this->_sigb_exemplaire)
			return $this->_sigb_exemplaire;
		$comm = new Class_CommSigb();
		return $comm->exemplaireFor($this);
	}


	public function setSigbExemplaire($sigb_exemplaire) {
		$this->_sigb_exemplaire = $sigb_exemplaire;
		return $this;
	}


	public function getTitrePrincipal() {
		return $this->getNotice()->getTitrePrincipal();
	}
}

?>