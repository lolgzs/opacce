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

class Class_SessionFormation extends Storm_Model_Abstract {
	protected $_table_name = 'sessions_formation';

	protected $_belongs_to = array(
																 'formation' => array('model' => 'Class_Formation'),
																 'lieu' => array('model' => 'Class_Lieu'));
	protected $_has_many = array(
															 'session_formation_inscriptions' => array('model' => 'Class_SessionFormationInscription',
																																				 'role' => 'session_formation',
																																				 'dependents' => 'delete'),

															 'stagiaires' => array('through' => 'session_formation_inscriptions',
																										 'unique' => true),

															 'session_formation_interventions' => array('model' => 'Class_SessionFormationIntervention',
																																					'role' => 'session_intervention',
																																					'dependents' => 'delete'),

															 'intervenants' => array('through' => 'session_formation_interventions',
																											 'unique' => true));

	protected $_default_attribute_values = array('effectif_min' => 1,
																							 'effectif_max' => 10,
																							 'cout' => 0,
																							 'duree' => 0,
																							 'date_debut' => '',
																							 'date_fin' => '',
																							 'date_limite_inscription' => null,
																							 'contenu' => '',
																							 'objectif' => '',
																							 'horaires' => '',
																							 'is_annule' => false);

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public function getAnnee() {
		return array_first(explode('-', $this->getDateDebut()));
	}

	
	public function getLibelleLieu() {
		if ($this->hasLieu())
			return $this->getLieu()->getLibelle();
		return '';
	}


	/**
	 * @return bool
	 */
	public function isInscriptionClosed() {
		if (strtotime($this->getDateDebut()) < time())
			return true;

		if ($this->isAnnule())
			return true;

		return ($this->hasDateLimiteInscription() && (strtotime($this->getDateLimiteInscription()) < time()));
	}


	/**
	 * @return bool
	 */
	public function isAnnule() {
		return 0 != (int)$this->getIsAnnule();
	}


	/**
	 * @return Class_SessionFormation
	 */
	public function beAnnule() {
		return $this->setIsAnnule(true);
	}


	/**
	 * @return int
	 */
	public function getNbStagiaires() {
		return count($this->getStagiaires());
	}


	/**
	 * @return array of Class_Users
	 */
	public function getStagiairesSortedByNom() {
		$stagiaires = $this->getStagiaires();
		usort($stagiaires, array('Class_Users', 'sortByNom'));
		return $stagiaires;
	}


	public function getDateFin() {
		$date = parent::_get('date_fin');
		return $date ? $date : null; //pour ne pas retourner chaine vide, probleme zend_form
	}


	public function getDateLimiteInscription() {
		$date = parent::_get('date_limite_inscription');
		if ('0000-00-00' == $date)
			$date = null;
		return $date;
	}


	public function validate() {
		$this->checkAttribute("effectif_max",
													$this->getEffectifMin() <= $this->getEffectifMax(),
													"L'effectif maximum doit être supérieur ou égal à l'effectif minimum");

		$this->checkAttribute("effectif_max",
													$this->getNbStagiaires() <= $this->getEffectifMax(),
													"Le nombre de stagiaires ne peux dépasser l'effectif maximum");

		foreach($this->getStagiaires() as $stagiaire) {
			$this->checkAttribute("effectif_max",
														$stagiaire->hasRightSuivreFormation(),
														sprintf("Le stagiaire %s n'a pas les droits suffisants pour suivre une formation", $stagiaire->getLogin()));
		}

		$this->checkAttribute('date_limite_inscription',
													Class_Date::isEndDateAfterStartDateNotEmpty($this->getDateLimiteInscription(), $this->getDateDebut()),
													"La date limite d'inscription doit être inférieure ou égale à la date de début");

		$this->checkAttribute('date_fin',
													Class_Date::isEndDateAfterStartDate($this->getDateDebut(), $this->getDateFin()),
													"La date de fin doit être supérieure ou égale à la date de début");
	}


	/** @return string */
	public function getLibelleFormation() {
		return $this->getFormation()->getLibelle();
	}


	/** @return bool */
	public function isFull() {
		return count($this->getStagiaires()) >= $this->getEffectifMax();
	}


	/** @return string */
	public function getDateDebutTexte() {
		return Class_Date::humanDate($this->getDateDebut(), 'd MMMM yyyy');
	}

	/** @return string */
	public function getDateFinTexte() {
		return Class_Date::humanDate($this->getDateFin(), 'd MMMM yyyy');
	}
}

?>