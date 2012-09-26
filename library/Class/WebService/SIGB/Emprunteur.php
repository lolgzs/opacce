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

class Class_WebService_SIGB_Emprunteur {
	protected $_id;
	protected $_name;
	protected $_emprunts;
	protected $_reservations;
	protected $_email;
	protected $_nom = null;
	protected $_prenom = null;
	protected $_password = null;
	protected $_nb_reservations = null;
	protected $_nb_emprunts = null;
	protected $_nb_retards = null;
	protected $_service = null;
	protected $_valid = false;
	protected $_end_date = null;


	public function __sleep() {
		$this->getEmprunts();
		$this->getReservations();
		return ['_id',
					 '_name',
					 '_emprunts',
					 '_reservations',
					 '_email',
					 '_nom',
					 '_prenom',
					 '_password',
					 '_nb_reservations',
					 '_nb_emprunts',
           '_nb_retards',
           '_end_date'];
	}


	/**
	 * @param mixed $id
	 * @param string $name
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public static function newInstance($id = '', $name = '') {
		return new self($id, $name);
	}


	/**
	 * Return an empty emprunteur
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public static function nullInstance() {
		return self::newInstance()->empruntsAddAll(array())->reservationsAddAll(array());
	}


	/**
	 * @param string $id
	 * @param string $name
	 */
	function __construct($id, $name){
		$this->_id=$id;
		$this->_name=$name;
		$this->_emprunts=null;
		$this->_reservations=null;
	}

	/**
	 * @param string $id
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setId($id) {
		$this->_id = $id;
		return $this;
	}


	/**
	 * @param string $name
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}


	/**
	 * @param array $emprunts
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function empruntsAddAll($emprunts){
		if (null === $this->_emprunts)
			$this->_emprunts = array();

		$this->_emprunts = array_merge($this->_emprunts, $emprunts);
		$this->_nb_emprunts = null;
		$this->_nb_retards = null;
		$this->sortByDateRetour($this->_emprunts);
		return $this;
	}


	/**
	 * @param Class_WebService_SIGB_Emprunt $emprunt
	 */
	public function empruntsAdd($emprunt) {
		$this->empruntsAddAll(array($emprunt));
	}

	/**
	 * @param array $reservations
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function reservationsAddAll($reservations){
		if (null === $this->_reservations)
			$this->_reservations = array();

		$this->_reservations = array_merge($this->_reservations, $reservations);
		$this->_nb_reservations = count($this->_reservations);
		return $this;
	}

	/**
	 * @param Class_WebService_SIGB_Reservation $reservation
	 */
	public function reservationsAdd($reservation) {
		$this->reservationsAddAll(array($reservation));
	}

	/**
	 * @return array
	 */
	public function getReservations(){
		if (null === $this->_reservations) {
			if (isset($this->_service))
				$this->reservationsAddAll($this->_service->getReservationsOf($this));
			else
				$this->_reservations = array();
		}
		return $this->_reservations;
	}

	/**
	 * @param int $index
	 * @return Class_WebService_SIGB_Reservation
	 */
	public function getReservationAt($index){
		$reservations = $this->getReservations();
		return $reservations[$index];
	}

	/**
	 * @return int
	 */
	public function getNbReservations(){
		if (null === $this->_nb_reservations)
			$this->_nb_reservations = count($this->getReservations());
		return $this->_nb_reservations;
	}

	/**
	 * @param int $nb_reservations
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setNbReservations($nb_reservations) {
		$this->_nb_reservations = $nb_reservations;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getPretsEnRetard(){
		$retards = array();
		$emprunts = $this->getEmprunts();

		foreach ($emprunts as $emprunt) {
			if ($emprunt->enRetard()) {
				$retards []= $emprunt;
			}
		}

		return $retards;
	}

	/**
	 * @return int
	 */
	public function getNbPretsEnRetard(){
		if (null == $this->_nb_retards)
			$this->_nb_retards = count($this->getPretsEnRetard());
		return $this->_nb_retards;
	}

	/**
	 * @param int $nb_retards
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setNbPretsEnRetard($nb_retards) {
		$this->_nb_retards = $nb_retards;
		return $this;
	}

	/**
	 * @param array $items
	 */
	public function sortByDateRetour(&$items) {
		$keys = array();
		foreach($items as $item) {
			$keys []= $item->getDateRetourTimestamp();
		}

		array_multisort($keys,
										SORT_ASC,
										SORT_NUMERIC,
										$items);
	}

	/**
	 * @return array
	 */
	public function getEmprunts(){
		if (null === $this->_emprunts) {
			if (isset($this->_service))
				$this->empruntsAddAll($this->_service->getEmpruntsOf($this));
			else
				$this->_emprunts = array();
		}

		return $this->_emprunts;
	}

	/**
	 * @param int $index
	 * @return Class_WebService_SIGB_Emprunt
	 */
	public function getEmpruntAt($index){
		$emprunts = $this->getEmprunts();
		return $emprunts[$index];
	}

	/**
	 * @return int
	 */
	public function getNbEmprunts(){
		if (null == $this->_nb_emprunts)
			$this->_nb_emprunts = count($this->getEmprunts());
		return $this->_nb_emprunts;
	}

	/**
	 * @param int $nb_emprunts
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setNbEmprunts($nb_emprunts) {
		$this->_nb_emprunts = $nb_emprunts;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->_name;
	}

	/**
	 * @return string
	 */
	public function getId(){
		return $this->_id;
	}

	/**
	 * @param string $email
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setEmail($email){
		$this->_email = $email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail(){
		return $this->_email;
	}

	/**
	 * @param string $nom
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setNom($nom) {
		$this->_nom = $nom;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getNom() {
		return $this->_nom;
	}

	/**
	 * @param string $prenom
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setPrenom($prenom) {
		$this->_prenom = $prenom;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrenom() {
		return $this->_prenom;
	}

	/**
	 * @param string $password
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setPassword($password) {
		$this->_password = $password;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->_password;
	}


	/**
	 * @param $date string YYYY-MM-DD format
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setEndDate($date) {
		$this->_end_date = $date;
		return $this;
	}


	/** @return string YYYY-MM-DD format */
	public function getEndDate() {
		return $this->_end_date;
	}
		

	/**
	 * @param Class_WebService_SIGB_AbstractService $service
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function setService($service) {
		$this->_service = $service;
		return $this;
	}


	public function save() {
		if (!isset($this->_service))
			return false;

		$this->_service->saveEmprunteur($this);
	}


	/**
	 * @param Class_Users $user
	 * @return string
	 */
	public function getUserInformationsPopupUrl($user) {
		if (!isset($this->_service))
			return '';

		return $this->_service->getPopupUrlForUserInformations($user);
	}


	/**
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function beValid() {
		$this->_valid = true;
		return $this;
	}

	
	/**
	 * @return boolean
	 */
	public function isValid() {
		return $this->_valid;
	}


	/**
	 * @param $user Class_Users
	 */
	public function updateUser($user) {
		$user
				->setIdabon($this->getId())
				->setNom($this->getNom())
				->setPrenom($this->getPrenom())
				->setMail($this->getEmail());

		if ($this->_end_date)
				$user->setDateFin($this->getEndDate());
	}


	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function __toString(){
		$str = 'Emprunteur ['.$this->id.'] '.$this->name."\n";

		$str .= "	 Emprunts\n";
		foreach($this->getEmprunts() as $emprunt)
			$str .= '		 '.$emprunt."\n";

		$str .= "	 Reservations\n";
		foreach($this->getReservations() as $reservation)
			$str .= '		 '.$reservation."\n";

		return $str;
	}
}

?>