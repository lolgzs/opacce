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
class OaiController extends Zend_Controller_Action {
	public function init() {
		$this->_helper->getHelper('contextSwitch')
			->addActionContext('list-identifiers', 'xml')
			->addActionContext('identify', 'xml')
			->addActionContext('list-metadata-formats', 'xml')
			->addActionContext('list-records', 'xml')
			->addActionContext('list-sets', 'xml')
			->addActionContext('get-record', 'xml')
			->addActionContext('bad-verb', 'xml')
			->initContext();
	}


	public function requestAction() {
		$this->getResponse()->setHeader('Content-Type', 'text/xml;charset=utf-8');
		$this->getHelper('ViewRenderer')->setNoRender();
		if (!Class_AdminVar::isOAIServerEnabled()) 
			return;

		$verbsMapping = array('ListIdentifiers' => 'list-identifiers',
													'Identify' => 'identify',
													'ListMetadataFormats' => 'list-metadata-formats',
													'ListRecords' => 'list-records',
													'ListSets' => 'list-sets',
													'GetRecord' => 'get-record');

		if (array_key_exists($this->_getParam('verb'), $verbsMapping)) {
			$this->_forward($verbsMapping[$this->_getParam('verb')], null, null, 
											$this->_request->getParams() + array('format' => 'xml'));
			return;
		}

		$this->_forward('bad-verb', null, null, 
										$this->_request->getParams() + array('format' => 'xml'));
	}


	protected function buildBaseUrl() {
		return $this->_request->getScheme() . '://' . $_SERVER['SERVER_NAME'] 
			. BASE_URL . '/opac/oai/request';
	}


	public function listIdentifiersAction() {
		$this->getHelper('ViewRenderer')->setLayoutScript('empty.phtml');
		$request = new Class_WebService_OAI_Request_ListIdentifiers($this->_request->getParams(), 
																																$this->buildBaseUrl());
		$builder = new Class_Xml_Builder();
		$this->view->request = $request;
		$this->view->error = $request->getErrorOn($builder);

		if ($notices = $request->getNotices()) {
			$visitor = new Class_Notice_DublinCoreVisitor();
			$recordBuilder = new Class_WebService_OAI_Response_RecordHeadersBuilder();
			$headers = '';
			foreach ($notices as $notice) {
				$visitor->visit($notice);
				$headers .= $recordBuilder->xml($builder, $visitor);
			}
			$this->view->headers = $headers;
		}
		$this->view->builder = $builder;
		$this->view->token = $request->getToken();
	}


	public function listRecordsAction() {
		$this->getHelper('ViewRenderer')->setLayoutScript('empty.phtml');
		$request = new Class_WebService_OAI_Request_ListRecords($this->_request->getParams(), 
																														$this->buildBaseUrl());
		$builder = new Class_Xml_Builder();
		$this->view->request = $request;
		$this->view->error = $request->getErrorOn($builder);

		if ($notices = $request->getNotices()) {
			$visitor = new Class_Notice_DublinCoreVisitor();
			$recordBuilder = new Class_WebService_OAI_Response_RecordBuilder();
			$records = '';
			foreach ($notices as $notice) {
				$visitor->visit($notice);
				$records .= $builder->record($recordBuilder->xml($builder, $visitor));
			}
			$this->view->records = $records;
		}
		$this->view->builder = $builder;
		$this->view->token = $request->getToken();
	}


	public function identifyAction() {
		$this->getHelper('ViewRenderer')->setLayoutScript('empty.phtml');
		$baseUrl = $this->buildBaseUrl();
		$request = new Class_WebService_OAI_Request_Identify($this->_request->getParams(), 
																												 $baseUrl);
		$this->view->request = $request;
		$this->view->builder = new Class_Xml_Builder();

		$this->view->repositoryName = $_SERVER['SERVER_NAME'] . ' Oai repository';
		$this->view->baseUrl = $baseUrl;
		$this->view->earliestDatestamp = ($notice = Class_Notice::getLoader()->getEarliestNotice()) ? 
			substr($notice->getDateMaj(), 0, 10) : '';
		$this->view->adminEmail = Class_CosmoVar::get('mail_admin');
	}


	public function listMetadataFormatsAction() {
		$this->getHelper('ViewRenderer')->setLayoutScript('empty.phtml');
		$baseUrl = $this->buildBaseUrl();
		$request = new Class_WebService_OAI_Request_ListMetadataFormats($this->_request->getParams(), 
																																		$baseUrl);
		$this->view->request = $request;
		$this->view->builder = new Class_Xml_Builder();
	}


	public function listSetsAction() {
		$this->getHelper('ViewRenderer')->setLayoutScript('empty.phtml');
		$baseUrl = $this->buildBaseUrl();
		$request = new Class_WebService_OAI_Request_ListSets($this->_request->getParams(), 
																												 $baseUrl);
		$this->view->request = $request;
		$builder = new Class_Xml_Builder();

		if ($catalogs = $request->getCatalogs()) {
			$visitor = new Class_WebService_OAI_CatalogueVisitor($builder);
			$sets = '';
			foreach ($catalogs as $catalog) {
				$visitor->visitCatalogue($catalog);
				$sets .= $builder->set($visitor->xml());
			}
			$this->view->sets = $sets;
		}
		$this->view->builder = $builder;
	}


	public function getRecordAction() {
		$this->getHelper('ViewRenderer')->setLayoutScript('empty.phtml');
		$baseUrl = $this->buildBaseUrl();
		$request = new Class_WebService_OAI_Request_GetRecord($this->_request->getParams(), 
																													$baseUrl);
		$this->view->request = $request;
		$builder = new Class_Xml_Builder();

		$this->view->error = $request->getErrorOn($builder);

		if ($notice = $request->getNotice()) {
			$visitor = new Class_Notice_DublinCoreVisitor();
			$visitor->visit($notice);
			$recordBuilder = new Class_WebService_OAI_Response_RecordBuilder();
			$this->view->record = $recordBuilder->xml($builder, $visitor);
		}
		$this->view->builder = $builder;
	}


	public function badVerbAction() {
		$this->getHelper('ViewRenderer')->setLayoutScript('empty.phtml');
		$this->view->baseUrl = $this->buildBaseUrl();
	}
}

?>