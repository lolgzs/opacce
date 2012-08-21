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
	const COM_PERGAME = 1;
	const COM_OPSYS = 2;
	const COM_VSMART = 4;
	const COM_KOHA = 5;
	const COM_CARTHAME = 6;
	const COM_NANOOK = 7;
	const COM_ORPHEE = 8;
	const COM_MICROBIB = 9;
	const COM_BIBLIXNET = 10;



	private $COM_CLASSES = array(self::COM_PERGAME => 'Class_WebService_SIGB_Pergame',
															 self::COM_OPSYS => 'Class_WebService_SIGB_Opsys',
															 self::COM_VSMART => 'Class_WebService_SIGB_VSmart',
															 self::COM_KOHA => 'Class_WebService_SIGB_Koha',
															 self::COM_CARTHAME => 'Class_WebService_SIGB_Carthame',
															 self::COM_NANOOK => 'Class_WebService_SIGB_Nanook',
															 self::COM_ORPHEE => 'Class_WebService_SIGB_Orphee',
															 self::COM_MICROBIB => 'Class_WebService_SIGB_Microbib',
															 self::COM_BIBLIXNET => 'Class_WebService_SIGB_BiblixNet');

	protected static $_instance;

	private $mode_comm;								// memo de modes de comm pour les bibs
	private $msg_erreur_comm;					// Message d'erreur pour la connexion au service de communication
	private $_translate;


	public static function getInstance() {
		if (null != self::$_instance)
			return self::$_instance;
		return new self();
	}


	public static function setInstance($instance) {
		self::$_instance = $instance;
	}


	public function exemplaireFor($exemplaire) {
		$mode_comm = $this->getModeComm($exemplaire->getBib()->getId());
		if ($sigb = $this->getSIGBComm($mode_comm))
			return $sigb->getExemplaire($exemplaire->getIdOrigine(),
																	$exemplaire->getCodeBarres());
	}


	public function  __construct() {
		$this->_translate = Zend_Registry::get('translate');
		$this->msg_erreur_comm = $this->_translate->_("Une erreur de communication avec le serveur a fait échouer la réservation. Merci de signaler ce problème à la bibliothèque.");
	}


	/**
	 * @param int $id_bib
	 * @return int
	 */
	public function getTypeComm($id_bib) {
		$comm = $this->getModeComm($id_bib);
		return $comm['type'];
	}


	/**
	 * @param array $exemplaires_to_check
	 * @return array
	 */
	public function getDispoExemplaires($exemplaires_to_check) {
		$exemplaires = array();

		foreach ($exemplaires_to_check as $exemplaire)	{
			$exemplaire["dispo"] = "non connue";
			$exemplaire["reservable"]=false;

			$mode_comm=$this->getModeComm($exemplaire['id_bib']);
			if ($sigb = $this->getSIGBComm($mode_comm)) {
				$sigb_exemplaire = $sigb->getExemplaire($exemplaire["id_origine"],
																								$exemplaire["code_barres"]);

				if ($sigb_exemplaire->isPilonne() || !$sigb_exemplaire->isVisibleOPAC()) continue;

				if ($sigb_exemplaire->isValid()) {
					$exemplaire["dispo"]=$sigb_exemplaire->getDisponibilite();
					$exemplaire["date_retour"]=$sigb_exemplaire->getDateRetour();
					$exemplaire["reservable"]=$sigb_exemplaire->isReservable();
					$exemplaire["id_exemplaire"]=$sigb_exemplaire->getId();
					//Cas Nanook pour la localisation de l'exemplaire en temps reel
					if ($code_annexe = $sigb_exemplaire->getCodeAnnexe()) {
						if (is_numeric($code_annexe))
							$exemplaire["id_bib"] = $code_annexe;
						$exemplaire["annexe"] = $code_annexe;
					}
				}
			}
			
			$exemplaires []= $exemplaire;
		}
		return $exemplaires;
	}


	/**
	 * @param int $id_bib
	 * @param int $id_origine
	 * @param string $code_annexe
	 * @return array
	 */
	public function reserverExemplaire($id_bib, $exemplaire_id, $code_annexe) {
		$mode_comm = $this->getModeComm($id_bib);
		if (!$user = Class_Users::getLoader()->getIdentity())
			return array('erreur' => $this->_translate->_('Vous devez vous connecter pour réserver un document.'));
		Class_WebService_SIGB_EmprunteurCache::newInstance()->remove($user);

		$exemplaire = Class_Exemplaire::getLoader()->find($exemplaire_id);
		$ret = array('statut' => 2,
								 'erreur' => '');

		switch ($mode_comm['type']) {
			// Pergame
			case self::COM_PERGAME:
				$pergame = new Class_Systeme_PergameService($user);
				$ret = $pergame->ReserverExemplaire($id_bib, $exemplaire->getIdOrigine(), $code_annexe);
				break;

			// Autres
		  case self::COM_OPSYS:
		  case self::COM_VSMART:
  		case self::COM_KOHA:
		  case self::COM_CARTHAME:
			case self::COM_NANOOK:
			case self::COM_ORPHEE:
			case self::COM_MICROBIB:
		  case self::COM_BIBLIXNET:
				if (false == $sigb = $this->getSIGBComm($mode_comm))
					return array("erreur" => $this->msg_erreur_comm);
				
				if (!$user->IDABON)
					return array("erreur" => $this->_translate->_('Vous devez vous connecter sous votre numéro de carte pour effectuer une réservation.'));
					
				$ret = $sigb->reserverExemplaire($user, $exemplaire, $code_annexe);
				break;
		}

		return $ret;
	}


	/**
	 * @param Class_Users $user
	 * @return array
	 */
	public function ficheAbonne($user) {
		$mode_comm = $this->getModeComm($user->ID_SITE);
		if (!$mode_comm['type'])
			return array();

		$user = Class_Users::getLoader()->find($user->ID_USER);
		$cache = Class_WebService_SIGB_EmprunteurCache::newInstance();
		if ($cache->isCached($user))
			return array('fiche' => $cache->load($user));

		if (false == $sigb = $this->getSIGBComm($mode_comm))
			return array('erreur' => $this->msg_erreur_comm);

		try {
			return array('fiche' =>  $cache->loadFromCacheOrSIGB($user, $sigb));
		} catch (Exception $e) {
			return array('erreur' =>  $e->getMessage());
		}
	}


	/**
	 * @param Class_Users $user
	 * @param int $id_reservation
	 * @return array
	 */
	public function supprimerReservation($std_user, $id_reservation) {
		$supprimer = function ($user, $sigb) use ($id_reservation) {
			              if ($sigb->isPergame())
											return (new Class_Systeme_PergameService($user))->supprimerReservation($id_reservation);

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
			              if ($sigb->isPergame())
											return (new Class_Systeme_PergameService($std_user))->prolongerPret($id_pret);
	
										return $sigb->prolongerPret($user, $id_pret);
		};

		return $this->withUserAndSIGBDo($std_user, $prolonger);
	}


	public function withUserAndSIGBDo($std_user, $closure) {
		$user = Class_Users::getLoader()->find($std_user->ID_USER);
		Class_WebService_SIGB_EmprunteurCache::newInstance()->remove($user);

		if (null == $sigb = $user->getSIGBComm())
			return [];
		
		if (!$sigb->isConnected())
			return array('erreur' => $this->msg_erreur_comm);

		return $closure($user, $sigb);
	}


	/**
	 * @param int $id_bib
	 * @return array
	 */
	public function getModeComm($id_bib){
		$ret = ['type' => 0, 'id_bib' => 0];
		if ($bib = Class_IntBib::find($id_bib))
			$ret = $bib->getModeComm();

		$this->mode_comm[$id_bib] = $ret;
		return $ret;
	}


	/**
	 * @param array $mode_comm
	 * @return Class_WebService_SIGB_AbstractService
	 */
	private function getSIGBComm($mode_comm) {
		if ($bib = Class_IntBib::find($mode_comm['id_bib']))
			return $bib->getSIGBComm();
		return false;
	}
}