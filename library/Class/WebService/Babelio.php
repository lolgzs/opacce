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
class Class_WebService_Babelio {
	const USER = 'afi_test';
	const PASS = 'af_45_POQ_d';

	/** @var simple_xml ressource */
	private $_xml;

	/** @var string */
	private $_baseUrl = 'http://www.babelio.info/sxml/';

	/** @var int */
	private $_longueur_min = 150;

	/** @var int */
	private $_longueur_titre = 40;

	/** @var Zend_Http_Client */
	private $_httpClient;

	/** @var int */
	private $_volatileTime;

	/**
	 * Le service est activé ou non dans config.ini
	 * Par défaut désactivé.
	 * babelio.expire_at = 2010/08/23 //expire à la date indiquée
	 * babelio.expire_at = never // n'expire jamais
	 * 
	 * @return array
	 */
	public static function getActivationStatus() {
		$status = array('enabled' => false,
										'expire_at' => null);

		$cfg = Zend_Registry::get('cfg');

		if (null === $babelio = $cfg->get('babelio')) {
			return $status;
		}

	  $expire_at = $babelio->get('expire_at', null);
		
		if ($expire_at === 'never') {
			$status['enabled'] = true;
			return $status;
		}

		try {
			$expiration_date = new Zend_Date($expire_at, null, Zend_Registry::get('locale'));
		} catch (Zend_Date_Exception $e) {
			return $status; // n'a pas réussi à parser
		}
		$status['expire_at'] = $expiration_date;
		$status['enabled'] = $expiration_date->compare(new Zend_Date()) > 0;
		return $status;
	}
	

	/**
	 * @param string $isbn
	 * @return bool
	 */
	public function requete($isbn) {
		$isbn = str_replace('-', '', (string)$isbn);
		$url = $this->_baseUrl . $isbn . $this->_getAuth();
		
		try {
			$httpClient = $this->getHttpClient();
			$httpClient->setUri($url);
			$response = $httpClient->request();
			$data = $response->getBody();
			@$this->_xml = simplexml_load_string($data);

		}catch (Exception $e) {
			return false;
		}

		return true;
	}


	/**
	 * @category testing
	 * @return Zend_Http_Client
	 */
	public function getHttpClient() {
		if (null === $this->_httpClient) {
			$this->_httpClient = Zend_Registry::get('httpClient');
		}

		return $this->_httpClient;
	}


	/**
	 * @param Zend_Http_Client
	 * @return Class_WebService_Babelio
	 */
	public function setHttpClient($client) {
		$this->_httpClient = $client;
		return $this;
	}


	/**
	 * pour rendre le service polymorphique avec Babelio, Amazon, Notices ....
	 *
	 * @param Class_Notice $notice
	 * @param mixed $page
	 * @return mixed
	 */
	public function getAvis($notice, $page) {
		if (! $notice->isLivre())
			return false;

		if (! $this->_serviceActivated())
			return false;

		$avis = $this->getCritiques($notice);
		if ($avis == false)
			return false;

		$avis['titre'] = 'Lecteurs Babelio';
		return $avis;
	}


	/**
	 * @see getActivationStatus
	 * @return bool
	 */
	protected function _serviceActivated() {
		$status = self::getActivationStatus();
		return $status['enabled'];
	}

	
	/**
	 * @param Class_Notice $notice
	 */
	public function getCritiques($notice) {
		$isbn = $notice->getIsbn();
		$this->requete($isbn);
		if (!$this->_xml)
			return false;

		$liste_avis = array();
		foreach($this->_xml->url as $avis)	{
			if ('critique' != $avis->type)
				continue;

			$texte = (string)$avis->snippet;
			if (strlen($texte) < $this->_longueur_min)
				continue;

			$avis_notice = new Class_AvisNotice();
			$avis_notice
				->setDateAvis($avis->dt)
				->setEntete($this->_getTitre($texte))
				->setAvis($texte)
				->setNote(($avis->note ? $avis->note : 0))
				->setNotice($notice)
				->setUser(null);

			$liste_avis[] = $avis_notice;
		}

		return array("liste" => $liste_avis,
								 "nombre" => count($liste_avis),
								 "note" => Class_AvisNotice::getNoteAverage($liste_avis));
	}


	public function getResumes($notice) {
		if (!$service = $notice->getIsbnOrEan())
			return array();

		if ($resume = $this->getCitations($service))
			return array(array('source' => 'Babelio (citations)',
												 'texte' => $resume));
		return array();
	}

	/**
	 * @param string $isbn
	 * @return string
	 */
	public function getCitations($isbn)	{
		if (!$this->_serviceActivated())
			return '';

		$this->requete($isbn);
		if (!$this->_xml)
			return false;

		$citations = array();

		foreach($this->_xml->url as $avis) {
			if ('citation' != $avis->type)
				continue;

			$texte = (string)$avis->snippet;

			$citation ='<img src="'.URL_ADMIN_IMG.'avis/quote_up.png" style="margin-left:10px;margin-right:5px;foat:left">'
				. $texte
				. '<img src="'.URL_ADMIN_IMG.'avis/quote_down.png" style="margin-left:5px;foat:right">';

			$citations[] = $citation;
		}

		if (0 == count($citations))
			return false;

		return implode('<br/><br/>', $citations);
	}


	/**
	 * @category testing
	 * @param int $time
	 * @return Class_WebService_Babelio
	 */
	public function setVolatileTime($time) {
		$this->_volatileTime = $time;
		return $this;
	}


	/** @return int */
	protected function _getTime() {
		if (null === $this->_volatileTime) {
			return time();
		}

		$volatileTime = $this->_volatileTime;
		$this->_volatileTime = null;

		return $volatileTime;
	}


	/**
	 * @param string $texte
	 * @return string
	 */
	private function _getTitre($texte) {
		$pos = strpos($texte, '.');
		if ($pos > 0 and $pos <= $this->_longueur_titre)
			return substr($texte, 0, $pos) . '...';

		$titre = substr($texte, 0, $this->_longueur_titre);
		return $titre . '...';
	}


	/** @return string */
	protected function _getAuth() {
		$time = $this->_getTime();
		$key = md5(self::USER . md5(self::PASS) . (string)$time);
		return '?auth=' . $key . '&timestamp=' . $time;
	}
}