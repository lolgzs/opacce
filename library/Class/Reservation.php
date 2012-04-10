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

class Class_Reservation extends Storm_Model_Abstract {
	protected $_table_name = 'reservations';
	protected $_table_primary = 'id_resa'; 

	protected $_belongs_to = array('bib' => array('model' => 'Class_Bib',
																								'referenced_in' => 'id_site'));

	protected $_notice;


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public function getNotice() {
		if (isset($this->_notice))
			return $this->_notice;

		if ($exemplaire = Class_Exemplaire::getLoader()->findFirstBy(array('id_origine' => $this->getIdNoticeOrigine())))
			return $this->_notice = $exemplaire->getNotice();

		return null;
	}


	public function getRang() {
		return 1 + $this->getLoader()->countBy(array('ID_NOTICE_ORIGINE' => $this->getIdNoticeOrigine(),
																								 'where' => sprintf('DATE_RESA<"%s"', $this->getDateResa())));
	}


	public function getEtat() {
		$nb_prets = Class_Pret::getLoader()->countBy(array('ID_NOTICE_ORIGINE' => $this->getIdNoticeOrigine(),
																											 'EN_COURS' => 1));
		if ($nb_prets>0) return "En prêt";
		if ($this->getRang() == 1) return "Disponible";
		return "Réservé";
	}
}

?>