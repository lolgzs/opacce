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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Interrogation dépôts Open Archives Initiative
//
// Voir l'exploreur: http://re.cs.uct.ac.za/
// Lister toutes les ensembles de Gallica: http://oai.bnf.fr/oai2/OAIHandler?verb=ListSets
// Lister toutes les notices de l'ensemble gallica:5:54 : http://oai.bnf.fr/oai2/OAIHandler?verb=ListRecords&metadataPrefix=oai_dc&set=gallica:5:54
// Outil Repository Explorer, très utile: http://re.cs.uct.ac.za/
//////////////////////////////////////////////////////////////////////////////////////////

/* Permet d'aller chercher les données depuis un entrepôt OAI
 * Utilisation:
 * $oai_service = new Class_WebService_OAI()
 * $oai_service->setOAIHandler('http://oai.bnf.fr/oai2/OAIHandler');
 * $oai_service->getSets()   //retourne les sets sous forme de tableau associatifs
 * $oai_service->getRecords('gallica'); //les 100 premiers records du set gallica
 * while ($oai_service->hasNextRecords())  //prends les enregistrements suivants 
 *     $oai->service->getNextRecords();    // tant qu'il y en a
 */
class Class_WebService_OAI {
	const ListSets = 'ListSets';
	const ListRecords = 'ListRecords';

	public function setOAIHandler($oai_handler) {
		$this->oai_handler = $oai_handler;
		return $this;
	}


	public function setWebClient($web_client) {
		$this->web_client = $web_client;
		return $this;
	}

	public function getWebClient() {
		if (!isset($this->web_client))
			$this->setWebClient(new Class_WebService_SimpleWebClient());
		return $this->web_client;
	}


	public function getContent($url) {
		return $this->getWebClient()->open_url($url);
	}


	public function oaiAsks($verb, $parameters) {
		$url = $this->oai_handler.'?verb='.$verb;
		foreach ($parameters as $name => $value) 
			$url .= '&'.$name.'='.urlencode($value);
		return $this->getContent($url);
	}


	public function listSets() {
		return $this->oaiAsks(self::ListSets, array());
	}


	public function getSetsFromHandler($oai_handler) {
		$this->setOAIHandler($oai_handler);
		return $this->getSets();
	}


	public function getSets() {
		/* Pour l'instant ne gère pas le resumptionToken, ça suffit pour la démo*/
		$xml_data = $this->listSets();
		$setspecs = array();
		$setnames = array();
		if (0==preg_match_all('/<setSpec>([^<]*)<\/setSpec>/i', $xml_data, $setspecs))
			return array();
		preg_match_all('/<setName>([^<]*)<\/setName>/i', $xml_data, $setnames);

		return array_combine($setspecs[1], $setnames[1]);
	}


	public function getRecordsFromHandlerAndSet($oai_handler, $set) {
		$this->setOAIHandler($oai_handler);
		return $this->getRecordsFromSet($set);
	}


	public function getRecordsFromSet($set) {
		$xml_data = $this->oaiAsks(self::ListRecords, 
															 array('metadataPrefix' => 'oai_dc',
																		 'set' => $set));
		return $this->parseListRecordsXML($xml_data);
	}


	protected function parseListRecordsXML($xml_data) {
		$parser = new Class_WebService_DublinCoreParser();
		$parser->parse($xml_data);
		$this->setListRecordsResumptionToken($parser->getResumptionToken());
		return $parser->getRecords();
	}


	public function hasNextRecords() {
		return ($this->getListRecordsResumptionToken()->getToken() != null);
	}


	public function getNextRecords() {
		if (!$this->hasNextRecords()) 
			return array();
		$xml_data = $this->oaiAsks(
											 self::ListRecords, 
									     array('resumptionToken' => $this->getListRecordsResumptionToken()->getToken()));
		return $this->parseListRecordsXML($xml_data);
	}

	public function setListRecordsResumptionToken($token) {
		$this->_listRecordsResumptionToken = $token;
		return $this;
	}

	public function getListRecordsResumptionToken() {
		return $this->_listRecordsResumptionToken;
	}

	public function getTotalNumberOfRecords() {
		if (!isset($this->_listRecordsResumptionToken)) return 0;
		return $this->_listRecordsResumptionToken->getListSize();
	}
}

?>