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

/*
	Interface aux services SOAP OPSYS.

	Modèles:
	** Emprunteur: représente un abonné avec ses réservations et emprunts
	- getEmprunts: retourne an tableau d'instances Emprunt
	- getReservations: retourne un tableau d'instances Reservation
	- getReservationAt: retourne la réservation à la position donnée
	- getName: nom de l'emprunteur
	- getId: identifiant

	** Notice: 
	- getId
	- getExemplaires: liste des exemplaires disponibles
	- nbExemplaires: nombre d'exemplaires disponibles

	** Exemplaire:
	- getId
	- getTitre
	- getNotice

  ** Reservation: 
	- getId
	- getExemplaire

  ** Emprunt: 

	- getId
	- getExemplaire


	Communication:
	** MappedSoapClient: Client SOAP qui associe automatiquement les types WSDL avec les classes PHP du même nom

  ** OpsysServiceFactory: fabrique et configure une intance OpsysService
	- ::production: créé un OpsysService connecté ou serveur Opsys
	- ::soapui: créé un OsysService connecté à un service SOAPUI local

	** OpsysService: interface de haut niveau pour communiquer avec le serveur Opsys
	- connect: ouvre une session, récupère un GUID 
	- disconnect: ferme la session ouverte
	- getEmprunteur(login, password): retourne une instance Emprunteur avec ses réservations et emprunts
	- getNotice($id): retourne une instance Notice avec ses exemplaires disponible
	- reserverExemplaire(login, password, notice_id): réserve un exemplaire de l'id notice 
	donnée pour l'utilisateur
	- supprimerReservation($reservation_id): supprime la réservation avec l'id donné. 
	(pour récupérer l'id en question, voir Emprunteur)
	- getExemplaire($notice_id): retourne l'exemplaire suivant de la notice (via cache de notice)

  Façade:
  ** Class_WebService_SIGB_Opsys: point d'entrée pour la partie cliente AFI_OPAC
	- ::getService($wsdl, $proxy_host=NULL, $proxy_port=NULL, ...): construit et retourne un OpsysService


	ex. d'utilisation:
  //configure le proxy globalement
  Class_WebService_SIGB_Opsys::setProxy('192.168.2.2', '3128', 'login', 'password');

	$service = Class_WebService_SIGB_Opsys::getService('chemin/vers/opsys.wsdl');
	$exemplaire=$service->getExemplaire("2305283");
	print $exemplaire->getDisponibilite();
	print $exemplaire->isReservable();

	$notice=$service->getNotice("2305283");
	if ($notice->isReservable())
	$res_ok=$opsys->reserverExemplaire("234566652", "pass", $notice->getId());

	unset($service);


	note: 
	- les __toString() des modèles sont tous implémentés. On peut faire "echo $notice"
	- toutes les autres classes servent à faire le mapping WSDL
*/


class Class_WebService_SIGB_Opsys {
	protected static $service_options;
	protected static $service;


	public static function reset() {
		self::$service = null;
	}

	public static function setProxy($proxy_host, $proxy_port,	$proxy_login, $proxy_password){
		self::$service_options=array(
																 'proxy_host' => $proxy_host, 
																 'proxy_port' => $proxy_port, 
																 'proxy_login' => $proxy_login, 
																 'proxy_password' =>	$proxy_password);
	}

	public static function getService($params) {
		if (!isset(self::$service)) {
			$instance = new self();
			self::$service = $instance->createService($params['url_serveur']);
		}

		return self::$service;
	}


	public static function setService($service) {
		self::$service = $service;
	}


	protected static function createServiceOptions(){
		if (class_exists("Zend_Registry") && 
				Zend_Registry::isRegistered('http_proxy') &&
				array_isset('proxy_host', Zend_Registry::get('http_proxy'))) {
			$proxy = Zend_Registry::get('http_proxy');
			self::setProxy(
										 $proxy['proxy_host'],
										 $proxy['proxy_port'],		
										 $proxy['proxy_user'],
										 $proxy['proxy_pass']);
		} 
		if (!isset(self::$service_options))
			self::$service_options = array();
	}

	public static function getServiceOptions(){
		self::createServiceOptions();
		return self::$service_options;
	}

	public function createService($url_aloes){
		return $this->newOpsysServiceFactory()->createOpsysService($url_aloes, 
																															 self::getServiceOptions());
	}

	public function newOpsysServiceFactory(){
		return new Class_WebService_SIGB_Opsys_ServiceFactory();
	}
}


?>
