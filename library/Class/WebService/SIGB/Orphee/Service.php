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

class Class_WebService_SIGB_Orphee_Service extends Class_WebService_SIGB_AbstractService {
	protected $_search_client;
	protected $_guid;
	protected $_wsdl;
	protected $_soap_options;

	protected static $SOAP_CLIENT_CLASS='Class_WebService_MappedSoapClient';

	public static function newSoapClient($wsdl, $options) {
		$soap_class = self::$SOAP_CLIENT_CLASS;
		return new $soap_class($wsdl, $options);
	}


	public static function setSoapClientClass($soap_class) {
		self::$SOAP_CLIENT_CLASS = $soap_class;
	}


	public static function getService($wsdl) {
		return new self($wsdl,
										array(
													'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
													'cache_wsdl' => WSDL_CACHE_BOTH,
													'trace' => false));
	}


	/**
	 * @param Class_WebService_MappedSoapClient $search_client
	 */
	public function __construct($wsdl, $soap_options) {
		$this->_wsdl = $wsdl;
		$this->_soap_options = $soap_options;
		$this->connect();
	}

	public function __destruct(){
		$this->disconnect();
	}


	public function disconnect() {
		if ($this->isConnected())
			$this->_search_client->EndSession(new EndSession());
	}


	public function getSearchClient() {
		if ($this->isConnected())
			return $this->_search_client;

		try {
			$this->_search_client = self::newSoapClient($this->_wsdl, $this->_soap_options);
			$result = $this->_search_client->GetId(new GetId());
			$this->_guid = $result->GetIdResult;
			$this->_search_client->__setCookie('ASP.NET_SessionId', $this->_guid);
		} catch (SoapFault $e) {
			$this->_guid = null;
		}
		return $this->_search_client;
	}


	public function connect() {
		try {
			$this->getSearchClient();
		} catch (SoapFault $e) {
			$this->_guid = null;
		}
		return $this;
	}


	public function isConnected() {
		return !empty($this->_guid);
	}


	/** @codeCoverageIgnore */
	protected function _dumpSoapTrace() {
		var_dump($this->_search_client->__getLastRequestHeaders());
		var_dump($this->_search_client->__getLastRequest());
		var_dump($this->_search_client->__getLastResponseHeaders());
		var_dump($this->_search_client->__getLastResponse());
	}

	
	public function getEmprunteur($user) {
		try {
			$result = $this->_search_client->GetInfoUserCarte(GetInfoUserCarte::withNo($user->getLogin()));

			if (!$emprunteur = Class_WebService_SIGB_Orphee_GetInfoUserCarteResponseReader
					::newInstance()
					->getEmprunteurFromXML($result->getXml()))
				return $this->newNullEmprunteur();
		} catch (SoapFault $e) {
			//$this->_dumpSoapTrace();
			//return $this->newNullEmprunteur();
			$this->_newOrpheeException($e->getMessage());
		}

		return $emprunteur->setService($this);
	}


	public function newNullEmprunteur() {
		return Class_WebService_SIGB_Emprunteur::newInstance()
				->reservationsAddAll(array())
				->empruntsAddAll(array());
	}


	public function getReservationsOf($emprunteur) {
		try {
			$result = $this->getSearchClient()->GetLstRsv(GetLstRsv::withAdh($emprunteur->getId()));

			return Class_WebService_SIGB_Orphee_GetLstRsvResponseReader
				::newInstance()
				->getReservationsFromXML($result->getXml());
	  } catch (SoapFault $e) {
			$this->_newOrpheeException($e->getMessage());
	  }
	}


	public function getEmpruntsOf($emprunteur) {
		try {
			$result = $this->getSearchClient()->GetLstPret(GetLstPret::withAdh($emprunteur->getId()));

			return Class_WebService_SIGB_Orphee_GetLstPretResponseReader
				::newInstance()
				->getEmpruntsFromXML($result->getXml());
	  } catch (SoapFault $e) {
			$this->_newOrpheeException($e->getMessage());
	  }
	}


	public function reserverExemplaire($user, $exemplaire, $code_annexe) {
		try {
			$emprunteur = $this->getEmprunteur($user);
		} catch (Exception $e) {
			return array('statut' => false, 'erreur' => $e->getMessage());
		}

		try {
			$notice_id = $this->removeOrpheeNoticePrefix($exemplaire->getIdOrigine());
			$result = $this->getSearchClient()->RsvNtcAdh(RsvNtcAdh::withNoticeUserNo($notice_id,
																																								$emprunteur->getId()));
		} catch (SoapFault $e) {
			return array('statut' => false, 'erreur' => 'Le SIGB Orphée a retourné l\'erreur suivante: '.$e->getMessage());
		}

		$datas = simplexml_load_string($result->getXml());
		if ($datas->msg->code == 1)
			return array('statut' => true, 'erreur' => '');
		
		return array('statut' => false, 'erreur' => $datas->msg->libelle);
	}


	public function supprimerReservation($user, $notice_id) {
		try {
			$emprunteur = $this->getEmprunteur($user);
		} catch (Exception $e) {
			return array('statut' => false, 'erreur' => $e->getMessage());
		}

		try {
			$result = $this->getSearchClient()->DelRsv(DelRsv::withNoticeUserNo($notice_id,
																																					$emprunteur->getId()));
		} catch (SoapFault $e) {
			return array('statut' => false, 'erreur' => 'Le SIGB Orphée a retourné l\'erreur suivante: '.$e->getMessage());
		}

		if ($result->DelRsvResult == 1)
			return array('statut' => true, 'erreur' => '');

		return array('statut' => false, 'erreur' => 'La suppression a échoué');
	}


	public function prolongerPret($user, $document_id) {
		try {
			$emprunteur = $this->getEmprunteur($user);
		} catch (Exception $e) {
			return array('statut' => false, 'erreur' => $e->getMessage());
		}

		try {
			$result = $this->getSearchClient()->ProlongePret(ProlongePret::withDocumentUser($document_id,
																																										$emprunteur->getId()));
			$datas = simplexml_load_string($result->getXml());
		} catch (SoapFault $e) {
			return array('statut' => false, 'erreur' => 'Le SIGB Orphée a retourné l\'erreur suivante: '.$e->getMessage());
		}
		
		if ($datas->msg->code == 1)
			return array('statut' => true, 'erreur' => '');
		
		return array('statut' => false, 'erreur' => $datas->msg->libelle);
	}


	public function getNotice($id) {
		$id = $this->removeOrpheeNoticePrefix($id);

		$result = $this->getSearchClient()->GetLstDmt(GetLstDmt::withNtcAndFas($id, 0));

		$xml = $result->getXml();

 		$notice = Class_WebService_SIGB_Orphee_GetLstDmtResponseReader
			::newInstance()
			->getNoticeFromXML($xml);

		if ($notice)
			$this->cacheNotice($notice);

		return $notice;
	}


	public function removeOrpheeNoticePrefix($id) {
		return str_replace('frOr', '', $id);
	}


	public function getGUID() {
		return $this->_guid;
	}


	public function _newOrpheeException($message) {
		throw new Class_WebService_Exception('Le SIGB Orphée a retourné l\'erreur suivante: '.$message);
	}
}







class Class_WebService_SIGB_Orphee_XMLFilter {
	public static function filter($xml) {
		$xml = trim($xml);
		$xml = preg_replace('/<!\[CDATA\[+/', '', $xml);
		$xml = preg_replace('/\]+>+/', '', $xml);

		return $xml;
		if (substr($xml, 0, 9)== '<![CDATA[')
			return substr($xml, 9, -3);

	}
}




class GetId {
  public $str; // string
  public $key; // string
}




class GetIdResponse {
  public $GetIdResult; // string

	public static function withIdResult($id) {
		$instance = new self();
		$instance->GetIdResult = $id;
		return $instance;
	}
}




class GetLstDmt {
  public $ntc; // string
  public $fas; // int

	public static function withNtcAndFas($ntc, $fas) {
		$instance = new self();
		$instance->ntc = $ntc;
		$instance->fas = $fas;
		return $instance;
	}
}




class GetLstDmtResponse {
  public $GetLstDmtResult; // string

	public static function withResult($xml) {
		$instance = new self();
		$instance->GetLstDmtResult = $xml;
		return $instance;
	}


	public function getXml() {
		return Class_WebService_SIGB_Orphee_XMLFilter::filter($this->GetLstDmtResult);
	}
}




class GetInfoUserCarte {
  public $cb; // int

	public static function withNo($no) {
		$instance = new self();
		$instance->cb = $no;
		return $instance;
	}
}




class GetInfoUserCarteResponse {
  public $GetInfoUserCarteResult; // String

	public static function withResult($xml) {
		$instance = new self();
		$instance->GetInfoUserCarteResult = $xml;
		return $instance;
	}


	public function getXml() {
		return Class_WebService_SIGB_Orphee_XMLFilter::filter($this->GetInfoUserCarteResult);
	}
}




class GetLstPret {
  public $adh; // int
  public $scrit; // string
  public $nb_res; // int
  public $export; // short


	public static function withAdh($adh) {
		$instance = new self();
		$instance->adh = $adh;
		$instance->scrit = '';
		$instance->nb_res = -1;
		$instance->export = 0;
		return $instance;
	}
}




class GetLstPretResponse {
  public $GetLstPretResult; // string

	public static function withResult($xml) {
		$instance = new self();
		$instance->GetLstPretResult = $xml;
		return $instance;
	}


	public function getXml() {
		return Class_WebService_SIGB_Orphee_XMLFilter::filter($this->GetLstPretResult);
	}
}



class GetLstRsv {
  public $adh; // int
  public $nb_res; // int


	public static function withAdh($adh) {
		$instance = new self();
		$instance->adh = $adh;
		$instance->nb_res = -1;
		return $instance;
	}
}




class GetLstRsvResponse {
  public $GetLstRsvResult; // string


	public static function withResult($xml) {
		$instance = new self();
		$instance->GetLstRsvResult = $xml;
		return $instance;
	}


	public function getXml() {
		return Class_WebService_SIGB_Orphee_XMLFilter::filter($this->GetLstRsvResult);
	}	
}




class RsvNtcAdh {
  public $ntc; // string
  public $fas; // int
  public $adh; // int

	public static function withNoticeUserNo($ntc, $adh) {
		$instance = new self();
		$instance->ntc = $ntc;
		$instance->adh = $adh;
		$instance->fas = 0;
		return $instance;
	}
}




class RsvNtcAdhResponse {
  public $RsvNtcAdhResult; // string


	public static function withResult($xml) {
		$instance = new self();
		$instance->RsvNtcAdhResult = $xml;
		return $instance;
	}

	public function getXml() {
		return Class_WebService_SIGB_Orphee_XMLFilter::filter($this->RsvNtcAdhResult);
	}
}




class DelRsv {
  public $adh; // int
  public $ntc; // int
  public $fas; // int

	public static function withNoticeUserNo($ntc, $adh) {
		$instance = new self();
		$instance->ntc = $ntc;
		$instance->adh = $adh;
		$instance->fas = 0;
		return $instance;
	}
}




class DelRsvResponse {
  public $DelRsvResult; // short

	public static function withResult($result) {
		$instance = new self();
		$instance->DelRsvResult = $result;
		return $instance;
	}
}




class ProlongePret {
  public $adh; // int
  public $dmt; // string

	public static function withDocumentUser($dmt, $adh) {
		$instance = new self();
		$instance->dmt = $dmt;
		$instance->adh = $adh;
		return $instance;
	}
}




class ProlongePretResponse {
  public $ProlongePretResult; // string


	public static function withResult($result) {
		$instance = new self();
		$instance->ProlongePretResult = $result;
		return $instance;
	}

	public function getXml() {
		return Class_WebService_SIGB_Orphee_XMLFilter::filter($this->ProlongePretResult);
	}
}




class EndSession {
}

class EndSessionResponse {
}



?>