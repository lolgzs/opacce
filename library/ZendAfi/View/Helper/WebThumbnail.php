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
// OPAC3 :	Créer un thumbnail d'un site web et renvoi l'adresse de l'image
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_WebThumbnail extends ZendAfi_View_Helper_BaseHelper {
	const THUMBS_DIR = 'web_thumbnails';
	protected $_thumbnailer;

	public function webThumbnail($url) {
		$this->checkThumbsDir();

		$filename = $this->fileNameFromUrl($url);
		$filepath = $this->fullPath($filename);
		
		if (!file_exists($filepath)) {
			if (!$this->getThumbnailer()->fetchUrlToFile($url, $filepath))
				return '';
		}

		return $this->fullUrl($filename);
	}

	public function getThumbsDir() {
		$path = USERFILESPATH.'/'.self::THUMBS_DIR.'/';
		return str_replace('//', '/', $path);
	}

	public function checkThumbsDir() {
		$dir = $this->getThumbsDir();
		if (!is_dir($dir))
			mkdir($dir);
	}

	public function fullUrl($thumbnail) {
		$url = USERFILESURL.'/'.self::THUMBS_DIR.'/'.$thumbnail;
		return str_replace('//', '/', $url);
	}

	public function fullPath($thumbnail) {
		return $this->getThumbsDir().$thumbnail;
	}

	public function fileNameFromUrl($url) {
		$decoded = urldecode($url);
		$wo_http = preg_replace('/^.*:\/\//', '', $decoded);
		$filename = preg_replace('/[^\w\-]/', '_', $wo_http);
		return $filename.'.jpg';
	}

	public function setThumbnailer($thumbnailer) {
		$this->_thumbnailer = $thumbnailer;
	}

	public function getThumbnailer() {
		if (!isset($this->_thumbnailer))
			$this->_thumbnailer = new WebThumbnailer();
		return $this->_thumbnailer;
	}
}


class WebThumbnailer {
	protected $_bluga;
	protected $_try_timeout = 5;

	public function getBlugaWebthumb() {
		if (!isset($this->_bluga))
			$this->_bluga = new Bluga_Webthumb();
		return $this->_bluga;
	}

	public function setTryTimeout($try_timeout) {
		$this->_try_timeout = $try_timeout;
	}

	public function setBlugaWebthumb($instance) {
		$this->_bluga = $instance;
	}

	public function fetchUrlToFile($url, $filename) {
		$api_key = $this->getApiKey();
		if (empty($api_key))
			return false;

		$bluga = $this->getBlugaWebthumb();
		$bluga->httpRequestAdapter = $this->_buildAdapter();

		try {
			$bluga->setApiKey($api_key);
			$job = $bluga->addUrl($url, 'small');
			$bluga->submitRequests();

			$nb_of_try = 3;

			while (!$bluga->readyToDownload()) {
				$nb_of_try -= 1;
				if ($nb_of_try < 0) return false;

				sleep($this->_try_timeout);
				$bluga->httpRequestAdapter = $this->_buildAdapter();
        $bluga->checkJobStatus();
			}

			$bluga->httpRequestAdapter = $this->_buildAdapter();
			$bluga->fetchToFile($job, $filename);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function getApiKey() {
		return Class_AdminVar::get('BLUGA_API_KEY');
	}


	protected function _buildAdapter() {
		$adapter = new Bluga_HTTP_Request_Adapter_PhpStream();

		$cfg = Zend_Registry::get('cfg');
		if (!$proxy = $cfg->get('proxy')) return $adapter;
		if (!$host = $proxy->get('host')) return $adapter;
		if (!$port = $proxy->get('port')) return $adapter;
		$adapter->proxy = new Bluga_HTTP_Request_Uri("tcp://$host:$port");

		if (!$user = $proxy->get('user')) return $adapter;

		$password = $proxy->get('pass');
		$authProxy = base64_encode("$user:$password");
		$adapter->headers['Proxy-Authorization'] = " Basic $authProxy";
		return $adapter;
	}
}