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

class Class_WebService_SIGB_VSmart_SearchResponseReader extends Class_WebService_SIGB_MarcXMLNoticeReader {
	protected $_current_exemplaire;
	protected $_STATUT_SYSTEME = array('0' => 'Disponible',
																		 '4' => 'En prêt',
																		 '8' => 'En attende de retrait',
																		 '10' => 'En déplacement',
																		 '23' => 'Non empruntable',
																		 '11' => 'Prétendu rendu',
																		 '12' => 'Perdu',
																		 '13' => 'Manquant',
																		 '21' => 'Autre',
																		 '22' => 'Nouveauté',
																		 '25' => 'En rachat',
																		 '26' => 'En réparation',
																		 '27' => 'En magasin',
																		 '28' => 'Pilon',
																		 '29' => 'Exclu du prêt',
																		 '30' => 'En commande',
																		 '31' => 'A l\'équipement');

	public static function newInstance() {
		return new self();
	}


	/** callbacks */

	public function endControlfield_001($data) {
		$this->_notice = new Class_WebService_SIGB_Notice($data);
	}


	public function startDatafield_852() {
		$this->_current_exemplaire = new Class_WebService_SIGB_Exemplaire(null);
	}


	public function endDatafield_852() {
		if ($this->_current_exemplaire->isVisibleOpac())
			$this->_notice->addExemplaire($this->_current_exemplaire);
	}


	public function endSubfield_852_q($data) {
		$this->_current_exemplaire
			->setId($data)
			->setCodeBarre($data);
	}


	public function endSubfield_852_y($data) {
		$this->_current_exemplaire->setReservable($data == '4');

		if (array_key_exists($data, $this->_STATUT_SYSTEME))
			$dispo = $this->_STATUT_SYSTEME[$data];
		else
			$dispo = 'Inconnu';
		$this->_current_exemplaire->setDisponibilite($dispo);
	}


	public function endSubfield_852_u($data) {
		$this->_current_exemplaire->setVisibleOpac($data != '0');
	}


	public function endSubfield_852_x($data) {
		$date = $data;
		// date reçue au format AAAAMMJJ, transformée en jj/mm/aaaa
		if (8 == strlen($date)) {
			$parts = array(
				substr($data, 6, 2),
				substr($date, 4, 2),
				substr($date, 0, 4),
			);

			$date = implode('/', $parts);

		}

		$this->_current_exemplaire->setDateRetour($date);

	}
}

?>