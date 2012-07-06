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

class Class_WebService_SIGB_Reservation extends Class_WebService_SIGB_ExemplaireOperation {
	protected $rang;
	protected $etat;


	public static function newInstanceWithEmptyExemplaire() {
		return new self(null, new Class_WebService_SIGB_Exemplaire(null));
	}


	public function getRang() {
		if (!isset($this->rang)) $this->rang=1;
		return $this->rang;
	}


	public function setRang($rang) {
		$this->rang = (int)$rang;
		if ($this->rang == 0) 
			$this->rang = 1;
		return $this;
	}


	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}


	public function getEtat() {
		return $this->etat;
	}


	public function onParseAttributes() {
		$this->setRang($this->getAttribute('Rang'));
		$this->setEtat($this->getAttribute('Etat'));

		if (!$code_annexe = $this->getAttribute('Lieu'))
			return;

		if ($annexe = Class_CodifAnnexe::getLoader()->findFirstBy(array('code' => $code_annexe)))
				$this->setBibliotheque($annexe->getLibelle());
	}
	

	/** @codeCoverageIgnore */
	public function __toString(){
		return parent::__toString().", Rang:".$this->getRang();
	}
}

?>