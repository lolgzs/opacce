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

class Class_WebService_SIGB_Notice {
	protected $id;
	protected $exemplaires;
	protected $reservable;

	public function __construct($id) {
		$this->id = $id;
		$this->exemplaires = array();
		$this->reservable = false;
	}


	public function getId() {
		return $this->id;
	}


	public function setId($id) {
		$this->id = $id;
		return $this;
	}


	public function getExemplaires(){
		return $this->exemplaires;
	}

	public function addExemplaire($exemplaire){
		$this->exemplaires[]=$exemplaire;
		$exemplaire->setNotice($this);
		return $this;
	}

	public function addAllExemplaires($exemplaires){
		foreach($exemplaires as $ex)
			$this->addExemplaire($ex);
		return $this;
	}

	public function nbExemplaires(){
		return count($this->exemplaires);
	}

	public function popDisponibilite(){
		return ($this->popExemplaire()!==NULL);
	}

	public function popExemplaire(){
		return array_pop($this->exemplaires);
	}


	/**
	 * @param int $id
	 * @return Class_WebService_SIGB_Exemplaire
	 */
	public function exemplaireAt($id){
		return $this->exemplaires[$id];
	}

	public function getExemplaireByCodeBarre($code_barre) {
		foreach ($this->exemplaires as $ex) {
			if ($ex->getCodeBarre()==$code_barre) return $ex;
		}

		return null;
	}

	public function setReservable($reservable){
		$this->reservable = ($reservable == "true");
	}

	public function isReservable(){
		return $this->reservable;
	}

	/** @codeCoverageIgnore */
	public function __toString(){
		$str = "Notice #".$this->id.": ".$this->nbExemplaires()." exemplaires\n";
		foreach ($this->exemplaires as $exemplaire)
			$str .= "	 - ".$exemplaire."\n";

		return $str;
	}
}

?>