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

include_once('Service.php');

class Class_WebService_SIGB_Opsys_ServiceFactory{
	const ALOES_ROOT = "http://81.80.216.130/websrvaloes";
	const SERVICE_SEARCH = "servicerecherche.asmx?WSDL";
	const SERVICE_CATALOG = "catalogueWeb.asmx?WSDL";
	const MOCK_WSDL = "http://localhost:8088/mockServiceRechercheSoap?WSDL";

	protected static $SOAP_CLIENT_CLASS='Class_WebService_MappedSoapClient';

	
	public static function newSoapClient($wsdl, $options) {
		$soap_class = self::$SOAP_CLIENT_CLASS;
		return new $soap_class($wsdl, $options);
	}


	public static function setSoapClientClass($soap_class) {
		self::$SOAP_CLIENT_CLASS = $soap_class;
	}


	public function defaultOptions(){
		return array('features' => SOAP_SINGLE_ELEMENT_ARRAYS,
								 'cache_wsdl' => WSDL_CACHE_BOTH,
								 'exceptions' => true,
								 'trace' => false,
								 'connection_timeout' => 2);
	}

	public function getAloesRootFromUrl($url) {
		return array_first(explode('websrvaloes', $url)).'websrvaloes';
	}

	public function getWsdlSearchURL($url_aloes) {
		return $this->getAloesRootFromUrl($url_aloes).'/'.self::SERVICE_SEARCH;
	}

	public function getWsdlCatalogURL($url_aloes) {
		return $this->getAloesRootFromUrl($url_aloes).'/'.self::SERVICE_CATALOG;
	}

	public function createOpsysService($url_aloes, $with_catalog_web, $extra_options){
		$options = array_merge($this->defaultOptions(), $extra_options);
		$search_client = self::newSoapClient($this->getWsdlSearchURL($url_aloes), $options);

		try {
			$catalog_client = $with_catalog_web 
				? self::newSoapClient($this->getWsdlCatalogURL($url_aloes), $options) 
				: new NullCatalogSoapClient;
		} catch (Exception $e) {
			$catalog_client = new NullCatalogSoapClient();
		}

		return new Class_WebService_SIGB_Opsys_Service($search_client, $catalog_client);
	}

	public static function createService($url_aloes, $extra_options=array()){
		$instance = new self();
		return $instance->createOpsysService($url_aloes, $extra_options);
	}

	/** @codeCoverageIgnore */
	public static function production(){
		return self::createService(
															 self::OPSYS_WSDL, 
															 array(
																		 'proxy_host' => '192.168.2.2', 
																		 'proxy_port' => '3128', 
																		 'proxy_login' => 'guest', 
																		 'proxy_password' =>	'guest'));
	}

	/** @codeCoverageIgnore */
	public static function soapui(){
		return self::createService(self::MOCK_WSDL);
	}
}




class NullCatalogSoapClient {
	public function EcrireNotice() {
		return new EcrireNoticeResponse();
	}
}

?>