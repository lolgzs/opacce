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

class Class_Ouverture extends Storm_Model_Abstract {
	const LUNDI=1, MARDI=2, MERCREDI=3, JEUDI=4, VENDREDI=5, SAMEDI=6, DIMANCHE=7;

	protected $_table_name = 'ouvertures';
	protected $_default_attribute_values = ['jour_semaine' => null,
																					'jour' => null,
																					'debut_matin' => '10:00',
																					'fin_matin' => '12:00',
																					'debut_apres_midi' => '12:00',
																					'fin_apres_midi' => '18:00'];

	protected $_belongs_to = ['bib' => ['model' => 'Class_Bib',
																			'referenced_in' => 'id_site']];

	public static function __callStatic($method, $args) {
		if (!preg_match('/chaque(Lundi|Mardi|Mercredi|Jeudi|Vendredi|Samedi|Dimanche)/', $method, $matches)) 
			return parent::__callStatic($method, $args);

		return static::getLoader()->newInstance()
			->setJourSemaine(constant('Class_Ouverture::'.strtoupper($matches[1])))
			->setHoraires($args);
	}


	public static function compare($a, $b) {
		if ($a->getJourSemaine() && $b->getJourSemaine() && $a->getJourSemaine() > $b->getJourSemaine())
			return 1;
		if ($a->getJourSemaine())
			return -1;
		if ($b->getJourSemaine())
			return 1;
		return $a->getJour() > $b->getJour() ? 1 : -1;
	}


	public function setHoraires($horaires) {
		return $this
			->setDebutMatin($horaires[0])
			->setFinMatin($horaires[1])
			->setDebutApresMidi($horaires[2])
			->setFinApresMidi($horaires[3]);
	}


	public function getLibelle() {
		return '';
	}


	public function getLibelleBib() {
		return $this->getBib()->getLibelle();
	}


	public function getDebutMatin() {
		return $this->getHourAttributeNamed('debut_matin');
	}

	
	public function getFinMatin() {
		return $this->getHourAttributeNamed('fin_matin');
	}


	public function getDebutApresMidi() {
		return $this->getHourAttributeNamed('debut_apres_midi');
	}

	
	public function getFinApresMidi() {
		return $this->getHourAttributeNamed('fin_apres_midi');
	}


	public function getHourAttributeNamed($name) {
		return substr($this->_get($name), 0, 5);
	}


	/**
	 * @return string
	 */
	public function getFormattedJour() {
		if (!$this->hasJourSemaine())
			return Class_Date::humanDate($this->getJour(), 'dd/MM/yyyy');
		$days = ['Aucun', 
						 'Lundi', 
						 'Mardi', 
						 'Mercredi', 
						 'Jeudi', 
						 'Vendredi', 
						 'Samedi', 
						 'Dimanche'];
		return $days[(int)$this->getJourSemaine()];
	}


	/**
	 * @param string $jour
	 */
	public function setJour($jour) {
		try {
			if ($jour)
				return $this->_set('jour', Class_Date::humanDate($jour, 'yyyy-MM-dd'));
		} catch (Zend_Date_Exception $e) {}

		return $this->_set('jour', 0);
	}


	/**
	 * @return bool
	 */
	public function isJourneeContinue() {
		return $this->getFinMatin() == $this->getDebutApresMidi();
	}


	/**
	 * Retourne la prochaine fermeture: soit fin de matinée, soit fin d'après-midi selon l'heure
	 * et si journée continue ou non
	 * @param int $timestamp
	 * @return timestamp
	 */
	public function getNextCloseFrom($timestamp) {
		$to_date = function($hour) use($timestamp) {
			return strtotime(date('Y-m-d', $timestamp).' '.$hour);
		};

		if ($this->isJourneeContinue() 
				|| $timestamp >= $to_date($this->getDebutApresMidi()))
			return $to_date($this->getFinApresMidi());

		return $to_date($this->getFinMatin());
	}

	
	public function beforeSave() {
		if ($this->getJourSemaine() > 0)
			$this->setJour(0);
	}
}

?>