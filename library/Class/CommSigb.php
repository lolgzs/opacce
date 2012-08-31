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
class Class_CommSigb {
	use Trait_Translator;
	protected static $_instance;

	public static function getInstance() {
		if (null != self::$_instance)
			return self::$_instance;
		return new self();
	}

	public static function setInstance($instance) {
		self::$_instance = $instance;
	}

	/**
	 * @param array $exemplaires_to_check
	 * @return array
	 */
	public function getDispoExemplaires($exemplaires_to_check) {
		$exemplaires = array();

		foreach ($exemplaires_to_check as $exemplaire)	{
			if ($dispo = $this->getDispoExemplaire($exemplaire["id_bib"], 
																						 $exemplaire["id_origine"], 
																						 $exemplaire["code_barres"]))
				$exemplaires []= array_merge($exemplaire, $dispo);
		}
		return $exemplaires;
	}


	public function getDispoExemplaire($id_bib, $id_origine, $code_barre) {
		$exemplaire = ["dispo" => "non connue",
									 "reservable" => false];

		$int_bib = Class_IntBib::find($id_bib);
		if (! ($int_bib && ($sigb = $int_bib->getSIGBComm())))
			return $exemplaire;

		$sigb_exemplaire = $sigb->getExemplaire($id_origine, $code_barre);

		if ($sigb_exemplaire->isPilonne() || !$sigb_exemplaire->isVisibleOPAC()) 
			return null;

		if (!$sigb_exemplaire->isValid())
			return $exemplaire;

		$exemplaire["dispo"]=$sigb_exemplaire->getDisponibilite();
		$exemplaire["date_retour"]=$sigb_exemplaire->getDateRetour();
		$exemplaire["reservable"]=$sigb_exemplaire->isReservable();
		$exemplaire["id_exemplaire"]=$sigb_exemplaire->getId();

		if (!$code_annexe = $sigb_exemplaire->getCodeAnnexe())
			return $exemplaire;

		//pour la localisation de l'exemplaire en temps reel
		$exemplaire["annexe"] = $code_annexe;
		if (is_numeric($code_annexe))
			$exemplaire["id_bib"] = $code_annexe;

		return $exemplaire;
	}


	/**
	 * @param Class_Users $user
	 * @return array
	 */
	public function ficheAbonne($std_user) {
		$user = Class_Users::getLoader()->find($std_user->ID_USER);
		$cache = Class_WebService_SIGB_EmprunteurCache::newInstance();
		if ($cache->isCached($user))
			return ['fiche' => $cache->load($user)];

		$ficheAbonneClosure = function ($user, $sigb) use ($cache) {
			try {
				return ['fiche' =>  $cache->loadFromCacheOrSIGB($user, $sigb)];
			} catch (Exception $e) {
				return ['erreur' =>  $e->getMessage()];
			}
		};


    return $this->withUserAndSIGBDo($std_user, $ficheAbonneClosure);
	}


	/**
	 * @param int $id_bib
	 * @param int $id_origine
	 * @param string $code_annexe
	 * @return array
	 */
	public function reserverExemplaire($id_bib, $exemplaire_id, $code_annexe) {
		if (!$user = Class_Users::getIdentity())
			return ['statut' => 2,
							'erreur' => $this->_('Vous devez vous connecter pour réserver un document.')];
		
		if (!$user->getIdabon())
			return ['statut' => 2,
							"erreur" => $this->_('Vous devez vous connecter sous votre numéro de carte pour effectuer une réservation.')];

		$exemplaire = Class_Exemplaire::find($exemplaire_id);

		$reserver = function ($user, $sigb) use ($exemplaire, $code_annexe) {
			return $sigb->reserverExemplaire($user, $exemplaire, $code_annexe);
		};

		return $this->withUserAndSIGBDo($user, $reserver);
	}


	/**
	 * @param Class_Users $user
	 * @param int $id_reservation
	 * @return array
	 */
	public function supprimerReservation($std_user, $id_reservation) {
		$supprimer = function ($user, $sigb) use ($id_reservation) {
					return $sigb->supprimerReservation($user, $id_reservation);			
		};

		return $this->withUserAndSIGBDo($std_user, $supprimer);
	}


	/**
	 * @param Class_Users $user
	 * @param int $id_pret
	 * @return array
	 */
	public function prolongerPret($std_user, $id_pret) {
		$prolonger = function($user, $sigb) use ($std_user, $id_pret) {
				return $sigb->prolongerPret($user, $id_pret);
		};

		return $this->withUserAndSIGBDo($std_user, $prolonger);
	}


	public function withUserAndSIGBDo($std_user, $closure) {
		$user = is_a($std_user, 'Class_Users') ? $std_user : Class_Users::find($std_user->ID_USER);
		Class_WebService_SIGB_EmprunteurCache::newInstance()->remove($user);

		if (null == $sigb = $user->getSIGBComm())
			return ['erreur' => $this->_('Communication SIGB indisponible')];
		
		if (!$sigb->isConnected())
			return ['erreur' => $this->_("Une erreur de communication avec le serveur a fait échouer la réservation. Merci de signaler ce problème à la bibliothèque.")];

		return $closure($user, $sigb);
	}
}