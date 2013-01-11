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

class Class_WebService_SIGB_Emprunt extends Class_WebService_SIGB_ExemplaireOperation {
	protected $enRetard;
	protected $renewable = true;


	/**
	 * @return string
	 */
	public function getDateRetour(){
		return $this->_exemplaire->getDateRetour();
	}


	/**
	 * @param string $retour
	 * @return Class_WebService_SIGB_Emprunt
	 */
	public function setDateRetour($retour) {
		$this->_exemplaire->setDateRetour($retour);
		return $this;
	}


	/**
	 * @return int
	 */
	public function getDateRetourTimestamp(){
		//date if format d/m/Y
		$date = explode('/', $this->getDateRetour());
		if (count($date) < 3) return 0;

		return mktime(0, 0, 0, $date[1], $date[0], $date[2]);
	}


	public function onParseAttributes() {
		if (!$date_retour = $this->getAttribute('retour'))
			$date_retour = $this->getAttribute('rendre');
		return $this->setDateRetour($date_retour);
	}

	/**
	 * @return bool
	 */
	public function enRetard() {
		if (!isset($this->enRetard)) {
			$this->enRetard = ($this->getDateRetourTimestamp() <= strtotime('Yesterday'));
		}

		return $this->enRetard;
	}

	/**
	 * @param bool $enRetard
	 */
	public function setEnRetard($enRetard) {
		$this->enRetard = $enRetard;
		return $this;
	}
	
	/**
	 * @param bool $renewable
	 */
	public function setRenewable($renewable) {
		$this->renewable = $renewable;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isRenewable() {
		return $this->renewable;
	}


	public function beNotRenewable() {
		$this->renewable = false;
		return $this;
	}


	/** @codeCoverageIgnore */
	public function __toString(){
		return parent::__toString().", Retour prévu:".$this->getDateRetour();
	}
}

?>