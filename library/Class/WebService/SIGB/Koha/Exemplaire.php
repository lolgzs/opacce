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
class Class_WebService_SIGB_Koha_Exemplaire extends Class_WebService_SIGB_Exemplaire {
	protected $_endommage = false;
	protected $_retire		= false;
	protected $_perdu			= false;
	protected $_loanable = true;

	/**
	 * @param bool $flag
	 */
	public function setEndommage($flag)	{
		$this->_endommage = (bool)$flag;
		$this->updateDisponibilite();
	}


	/**
	 *
	 * @return bool
	 */
	public function getEndommage() {
		return $this->_endommage;
	}


	/**
	 * @param bool $flag
	 */
	public function setPerdu($flag)	{
		$this->_perdu = (bool)$flag;
		$this->updateDisponibilite();
	}


	/**
	 * @return bool
	 */
	public function getPerdu() {
		return $this->_perdu;
	}


	/**
	 * @param bool $flag
	 */
	public function setRetire($flag) {
		$this->_retire = (bool)$flag;
		$this->updateDisponibilite();
		return $this;
	}


	/**
	 * @return bool
	 */
	public function getRetire()	{
		return $this->_retire;
	}


	/**
	 * @param string $date_retour
	 */
	public function setDateRetour($date_retour)	{
		parent::setDateRetour($date_retour);
		$this->updateDisponibilite();
	}


	/**
	 * @return bool
	 */
	public function isReservable() {
		return (
			parent::isReservable()
			&& (!$this->isPiege()
			&& $this->isLoanable())
		);
	}


	/**
	 * @return string
	 */
	public function updateDisponibilite() {
		if (!$this->isPiege() and ('' != $this->getDateRetour()))
				$this->setDisponibiliteEnPret();
	}


	/**
	 * @return bool
	 */
	public function isPiege()	{
		return $this->getRetire() || $this->getPerdu() || $this->getEndommage();
	}


	/**
	 * Indique que l'exemplaire ne peut être emprunté
	 */
	public function notForLoan() {
		$this->_loanable = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isLoanable()	{
		return $this->_loanable;
	}

}
?>