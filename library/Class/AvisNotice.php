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
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3: Avis sur les notices
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class AvisNoticeLoader extends Storm_Model_Loader {
	protected function _addCatalogueConditions($select, $preferences) {
		// voir fonction Class_Catalogue::getReguetes
		$preferences["nb_notices"] = null; // charge toutes les notices avec avis
		$preferences["aleatoire"] = 0; // on veut toutes les notices
		$preferences["avec_avis"] = 1; //seulement les notices avec avis

		$catalogue = new Class_Catalogue();
		$notices = $catalogue->getNoticesByPreferences($preferences);

    if (count($notices) == 0) {
			//on ne doit retourner aucun avis
			$select->where('0=1');
			return $this;
    }

		$clefs_oeuvres = array();
		foreach($notices as $notice)
			$clefs_oeuvres []= $notice['clef_oeuvre'];

		$select->where('notices_avis.clef_oeuvre in (?)', $clefs_oeuvres);
		return $this;
	}


	protected function _addStatutAbonBibWhereClause($select, $abon_ou_bib) {
		$modo_avis_abo = Class_AdminVar::get('MODO_AVIS');       // 0 apres / 1 avant de publier sur le site
		$modo_avis_bib = Class_AdminVar::get('MODO_AVIS_BIBLIO');   // 0 apres / 1 avant de publier sur le site

		if (($abon_ou_bib == '0') || ($abon_ou_bib == '1'))
			$select->where("ABON_OU_BIB=?", $abon_ou_bib);
			
		if ($modo_avis_abo == 1)
			$select->where('STATUT=1 OR ABON_OU_BIB=1');

		if ($modo_avis_bib == 1)
			$select->where('STATUT=1 OR ABON_OU_BIB=0');

		return $this;
	}


	/*
		Renvoie les avis correspondants aux préférences de recherches données
		- id_catalogue: le catalogue de notices dont on veut récupérer les avis
		- id_panier: le panier de notices dont on veut récupérer les avis
		On prends les avis soit du catalogue, soit du panier, soit les dernier avis
	 */
	public function getAvisFromPreferences($preferences) {
		$select = $this
			->getTable()
			->select()
			->order('DATE_AVIS DESC');

		$preferences = array_merge(array('id_panier' => 0,
																		 'id_catalogue' => 0,
																		 'abon_ou_bib' => 'all'),
															 $preferences);

		$id_panier = $preferences['id_panier'];
		$id_catalogue = $preferences['id_catalogue'];
		$abon_ou_bib = $preferences['abon_ou_bib'];

		/* Retourne les derniers avis si aucun catalogue ni panier de spécifié */
		if ($id_panier or $id_catalogue) 
			$this->_addCatalogueConditions($select, $preferences);

		$this->_addStatutAbonBibWhereClause($select, $abon_ou_bib);
		return $this->findAll($select);
	}
}



class Class_AvisNotice  extends Storm_Model_Abstract {
	protected $_loader_class = 'AvisNoticeLoader';
	protected $_table_name = 'notices_avis';
	protected $_table_primary = 'ID';
	protected $_belongs_to = array('user' => array('model' => 'Class_Users',
																								  'referenced_in' => 'id_user'));
	protected $_notices;

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	public static function sortByDateAvisDesc($avis_list) {
		usort($avis_list, array('Class_AvisNotice', 'compareByDateAvisDesc'));
		return $avis_list;
	}

	public static function compareByDateAvisDesc($one, $two) {
		return strtotime($one->getDateAvis()) < strtotime($two->getDateAvis());
	}


	public static function filterByMethod($avis_list, $method, $params = null) {
		return array_filter_by_method($avis_list, $method, $params);
	}


	public static function filterByAbonne($avis_list) {
		return self::filterByMethod($avis_list, 'isWrittenByAbonne');
	}


	public static function filterByBibliothequaire($avis_list) {
		return self::filterByMethod($avis_list, 'isWrittenByBibliothequaire');
	}


	public static function filterVisibleForUser($user, $avis_list) {
		return self::filterByMethod($avis_list, 'isVisibleForUser', array($user));
	}


	public function getNote() {
		if (!$this->_attributes['note'])
			return 0;
		return $this->_attributes['note'];
	}


	public static function getNoteAverage($avis_list) {
		if (!$avis_list or count($avis_list) == 0) return 0; 

		$sum = 0;
		foreach($avis_list as $avis) $sum += $avis->getNote();
		$avg = $sum / count($avis_list);
		return round($avg * 2) / 2;
	}


	public function getNotices() {
		if (!isset($this->_notices)) {
			$this->_notices = Class_Notice::getLoader()
				->findAllBy(array('clef_oeuvre' => $this->getClefOeuvre()));
		}
		return $this->_notices;
	}


	public function setNotice($notice) {
		return $this->setNotices(array($notice));
	}

	public function getFirstNotice() {
		$notices = $this->getNotices();
		if (count($notices) == 0)
			return null;

		foreach ($notices as $notice)
			if ($notice->hasVignette())
				return $notice;

		return $notices[0];
	}

	public function setNotices($notices) {
		$this->_notices = $notices;
		return $this;
	}

	public function getZendDateAvis() {
		return new Zend_Date($this->getDateAvis(), null, Zend_Registry::get('locale'));
	}

	public function getFormattedDateAvis($format = 'dd-MM-yyyy') {
		return $this->getZendDateAvis()->toString($format);
	}

	public function getReadableDateAvis() {
		return $this->getFormattedDateAvis("d MMMM yyyy");
	}

	public function getUserName() {
		if (null == $user = $this->getUser())
			return '';
		return $user->getNomAff();
	}


	public function isWrittenByBibliothequaire() {
		return $this->getAbonOuBib() == 1;
	}


	public function beWrittenByBibliothecaire() {
		return $this->setAbonOuBib(1);
	}


	public function isWrittenByAbonne() {
		return $this->getAbonOuBib() == 0;
	}


	public function beWrittenByAbonne() {
		return $this->setAbonOuBib(0);
	}


	public function setModerationOK() {
		$this->setStatut(1);
		return $this;
	}


	public function isModerationOK() {
		return $this->getStatut() == 1;
	}


	public function isWaitingForModeration() {
		if ($this->isWrittenByAbonne() && Class_AdminVar::get('MODO_AVIS') == 1)
			return $this->isModerationOK() == false;

		if ($this->isWrittenByBibliothequaire() && Class_AdminVar::get('MODO_AVIS_BIBLIO') == 1)
			return $this->isModerationOK() == false;

		return false;
	}


	public function isVisibleForUser($user) {
		if ($this->getUser() == null || $this->getIdUser() == $user->ID_USER)
			return true;

		return $this->isWaitingForModeration() == false;
	}


	public function setUser($user) {
		if ($user == null) {
			$this->setAbonOuBib(0);
			$this->setStatut(1);
		}
		return parent::setUser($user);
	}


	public function validate() {
		$longueur_min = Class_AdminVar::get("AVIS_MIN_SAISIE");
		$longueur_max = Class_AdminVar::get("AVIS_MAX_SAISIE");
		$longueur_avis = strlen($this->getAvis());

		$translate = Zend_Registry::get('translate');

		$this->check( $longueur_avis >= $longueur_min && $longueur_avis <= $longueur_max,
									$translate->_("L'avis doit avoir une longueur comprise entre %s et %s caractères", $longueur_min, $longueur_max));

		$this->check( $this->getEntete(), $translate->_('Vous devez saisir un titre') );
	}

	public function beforeSave() {
		if ((null !== $user = $this->getUser()) && $user->isBibliothequaire())
			$this->setAbonOuBib(1);
		else
			$this->setAbonOuBib(0);
	}


}

?>
