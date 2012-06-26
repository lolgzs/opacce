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

class Class_WebService_ArteVOD {
	protected static $_default_web_client;
	protected $web_client;
	protected $_logger;
	protected $_harvested_ids = array();

	const BASE_URL = 'http://www.mediatheque-numerique.com/ws/';
	const FILMS = 'films';
	const AUTHORS = 'authors';
	const ACTORS = 'actors';

	const CATEGORY_LABEL = 'ArteVOD';


	public static function setDefaultWebClient($web_client) {
		self::$_default_web_client = $web_client;
	}


	public static function getDefaultWebClient() {
		if (!isset(self::$_default_web_client))
			self::$_default_web_client = new Class_WebService_SimpleWebClient();
		return self::$_default_web_client;
	}


	public function setWebClient($client) {
		$this->web_client = $client;
		return $this;
	}


	public function getWebClient() {
		if (!isset($this->web_client))
			$this->setWebClient(self::getDefaultWebClient());
		return $this->web_client;
	}


	public function setLogger($logger) {
		$this->_logger = $logger;
		return $this;
	}


	public function getLogger() {
		if (null == $this->_logger)
			return new Zend_Log(new Zend_Log_Writer_Null());
		return $this->_logger;
	}


	public function harvest() {
		$current_page = 1;
		$total_page = 0;
		$this->_harvested_ids = array();
		do {
			if (null == ($reader = $this->loadPage($current_page)))
				return;

			$total_page = $reader->getPageCount();

			$this->getLogger()->info(sprintf('Traitement de la page %s / %s',
																			 $current_page,
																			 $total_page));

			foreach ($reader->getFilms() as $film) {
				$this->_harvested_ids[] = $film->getId();
				if ($film->isAlreadyHarvested())
					continue;
				
				$this->loadFilm($film);
				$film->import();
			}
			
			$current_page++;
		} while ($current_page <= $total_page);
	}


	public function getHarvestedIds() {
		return $this->_harvested_ids;
	}


	public function harvestPage($page_number = 1) {
		$response = array();
		if (null == ($reader = $this->loadPage($page_number))) {
			$response['error'] = 'Erreur de communication';
			return $response;
		}
		
		$response['total_count'] = $reader->getTotalCount();
		$response['current_page'] = $reader->getPageNumber();
		$response['page_count'] = $reader->getPageCount();
		$response['has_next'] = $reader->getPageNumber() < $reader->getPageCount();

		foreach ($reader->getFilms() as $film) {
			$this->loadFilm($film);
			$film->import();
		}

		return $response;
	}


	protected function loadPage($page_number = 1) {
		$url = self::BASE_URL . self::FILMS . ((1 != $page_number) ? '?page_nb=' . $page_number: '');
		$content = $this->open_authenticated_url($url);
		if ('' == $content) {
			$this->getLogger()->err('Erreur de communication');
			return;
		}

		$existing_ids = array();

		$this->getLogger()->info('Réponse reçue');
		$reader = $this->getFilmsReader()->parse($content);
		if (1 == $page_number) 
			$this->getLogger()->info($reader->getTotalCount() .' films dans la base');
		return $reader;
	}


	public function loadFilm($film) {
		$content = $this->open_authenticated_url(self::BASE_URL . self::FILMS . '/' . $film->getId());
		if ('' == $content) {
			$this->getLogger()->err(sprintf('Erreur de communication lors de la récupération du film %s',
																			$film->getId()));
			return;
		}

		$reader = $this->getFilmReader();
		$reader->parseContentOn($content, $film);
	}


	public function open_authenticated_url($url) {
		$this->getWebClient()->setAuth(Class_AdminVar::get('ARTE_VOD_LOGIN'), 
																	 Class_AdminVar::get('ARTE_VOD_KEY'));
		return $this->getWebClient()->open_url($url);
	}


	public function getFilmsReader() {
		return new Class_WebService_ArteVOD_FilmsReader();
	}


	public function getFilmReader() {
		return new Class_WebService_ArteVOD_FilmReader();
	}
}