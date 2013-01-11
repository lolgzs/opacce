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

class Class_WebService_SIGB_Pergame_Service extends Class_WebService_SIGB_AbstractService {
	protected $_id_bib;
	protected $_legacy_service;

	public static function newInstance() {
		return new self();
	}


	public static function getService($id_bib) {
		return self::newInstance()->setIdBib($id_bib);
	}


	public function isPergame() {
		return true;
	}


	public function setLegacyService($service) {
		$this->_legacy_service = $service;
	}


	public function getLegacyService() {
		if (!isset($this->_legacy_service))
			$this->_legacy_service = (new Class_Systeme_PergameService(Class_Users::getIdentity()));
		return $this->_legacy_service;
	}


	public function setIdBib($id_bib) {
		$this->_id_bib = $id_bib;
		return $this;
	}


	public function getEmprunteur($user) {
		$emprunteur = Class_WebService_SIGB_Emprunteur::newInstance($user->getId(), $user->getLogin())
			->setService($this);
		return $emprunteur;
	}


	public function getEmpruntsOf($emprunteur)
	{
		$params = Class_IntBib::getLoader()->find($this->_id_bib)->getCommParamsAsArray();
		$renouvelable = isset($params['Autoriser_prolongations']) ? $params['Autoriser_prolongations'] : false;
		
		$user = Class_Users::getLoader()->find($emprunteur->getId());
		$prets = Class_Pret::getLoader()->findAllBy(array('IDABON' => $user->getIdabon(),
																											'ORDREABON' => $user->getOrdreabon(),
																											'EN_COURS' => 1));
		$emprunts = array();
		foreach($prets as $pret)
		{
			$emprunts []= Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
				->setId($pret->getIdPret())
				->setExemplaireOPAC($pret->getExemplaire())
				->setDateRetour(implode('/', array_reverse(explode('-',$pret->getDateRetour()))))
				->setRenewable($renouvelable);
		}

		return $emprunts;
	}


	public function getReservationsOf($emprunteur) {
		$user = Class_Users::getLoader()->find($emprunteur->getId());
		$reservations_db = Class_Reservation::getLoader()->findAllBy(array('IDABON' => $user->getIdabon(),
																																			 'ORDREABON' => $user->getOrdreabon()));
		$reservations = array();
		foreach($reservations_db as $reservation) {
			$reservations []= Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire()
				->setId($reservation->getId())
				->setNoticeOPAC($reservation->getNotice())
				->setRang($reservation->getRang())
				->setEtat($reservation->getEtat());
		}

		return $reservations;
	}


	public function reserverExemplaire($user, $exemplaire, $code_annexe) {
		return $this->getLegacyService()->reserverExemplaire($this->_id_bib, 
																												 $exemplaire->getIdOrigine(), 
																												 $code_annexe);
	}


	public function supprimerReservation($user, $reservation_id) {
		return $this->getLegacyService()->supprimerReservation($reservation_id);
	}


	public function prolongerPret($user, $pret_id) {
		return $this->getLegacyService()->prolongerPret($pret_id);
	}

	
	public function getNotice($id){
		if (!$exemplaire = Class_Exemplaire::getLoader()->findFirstBy(array('id_origine' => $id,
																																				'id_bib' => $this->_id_bib)))
			return null;

		$notice = $exemplaire->getNotice();

		$exemplaires = $notice->getExemplairesByIdSite($this->_id_bib);
		$reservations = Class_Reservation::getLoader()->findAllBy(array('id_site' => $this->_id_bib,
																																		'id_notice_origine' => $id));
		$notice_sigb = new Class_WebService_SIGB_Notice($id);
		foreach($exemplaires as $exemplaire) {
			$exemplaire_sigb = new Class_WebService_SIGB_Exemplaire($exemplaire->getId());
			$notice_sigb->addExemplaire($exemplaire_sigb);

			$exemplaire_sigb
				->setNoNotice($id)
				->setCodeBarre($exemplaire->getCodeBarres())
				->setDisponibilite($exemplaire->getActivite());

			if ($this->isDocDispoReservables())
				$exemplaire_sigb->beReservable();

			if ($exemplaire->isPrete()) {
				$exemplaire_sigb
					->setDisponibiliteEnPret()
					->beReservable()
					->setDateRetour($exemplaire->getDateRetour());
			} else if (count($reservations) > 0) {
				$exemplaire_sigb
					->setDisponibilite('Réservé')
					->beReservable();
				array_pop($reservations);
			}
		}

		return $notice_sigb;
	}


	public function isDocDispoReservables() {
		$params = unserialize(Class_IntBib::getLoader()->find($this->_id_bib)->getCommParams());
		return $params['Autoriser_docs_disponibles'] == 1;
	}

}

?>
