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

	const BASE_URL = 'http://www.mediatheque-numerique.com/ws/';
	const FILMS = 'films';
	const AUTHORS = 'authors';
	const ACTORS = 'actors';


	public static function setDefaultWebClient($web_client) {
		self::$_default_web_client = $web_client;
	}


	public static function getDefaultWebClient() {
		if (isset(self::$_default_web_client))
			return self::$_default_web_client;
		return new Class_WebService_SimpleWebClient();
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
		$content = $this->open_authenticated_url(self::BASE_URL . self::FILMS);
		if ('' == $content) {
			$this->getLogger()->err('Erreur de communication');
			return;
		}

		$existing_ids = array();

		$this->getLogger()->info('Réponse reçue');
		$reader = $this->getFilmsReader()->parse($content);

		$this->getLogger()->info($reader->getTotalCount() .' films dans la base');
		$this->getLogger()->info(sprintf('Traitement de la page %s / %s',
																		 $reader->getPageNumber(),
																		 $reader->getPageCount()));

		foreach ($reader->getFilms() as $film) {
			$existing_ids[] = $film->getId();
			$this->loadFilm($film);
		}
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