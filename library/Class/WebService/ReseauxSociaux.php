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
//////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : RESEAUX SOCIAUX
//////////////////////////////////////////////////////////////////////////////////////

class Class_WebService_ReseauxSociaux {
	static protected $_web_client;
	protected $reseaux=array																					// Description des reseaux
	        (
	        	"facebook" => array("url" => "http://www.facebook.com/share.php?u=%s"),
	        	"twitter"	 => array("url" => "http://twitter.com/home?status=%s")	        	
	        );

	protected $url_shortener="http://is.gd/api.php?longurl=";					// Url pour obtenir une une url courte

	//------------------------------------------------------------------------------------------------------
	// Rend la structure
	//------------------------------------------------------------------------------------------------------
	public function getReseau($id_reseau=false)	{
		if($id_reseau) 
			return $this->reseaux[$id_reseau];
		else 
			return $this->reseaux;
	}
	

	//------------------------------------------------------------------------------------------------------
	// Rend l'url a passer en parametre
	//------------------------------------------------------------------------------------------------------
	public function getUrl($id_reseau,$url_afi, $message = '')	{
		// Short url
		if (false === strpos($url_afi, 'http'))
			$url_afi = "http://".$_SERVER["HTTP_HOST"].BASE_URL.$url_afi;

		// Url réseau
		return sprintf($this->reseaux[$id_reseau]["url"], 
									 urlencode(trim($message.' '.$this->shortenUrl($url_afi))));
	}


	public function shortenUrl($original_url) {
		$short_url = self::getDefaultWebClient()->open_url($this->url_shortener.urlencode($original_url));
		if (!$short_url or substr($short_url,0,5)=="Error") 
			return $original_url;
		return $short_url;
	}


	static public function getDefaultWebClient() {
		if (!isset(self::$_web_client))
			self::$_web_client = new Class_WebService_SimpleWebClient();
		return self::$_web_client;
	}


	static public function setDefaultWebClient($web_client) {
		self::$_web_client = $web_client;
	}

	static public function resetDefaultWebClient() {
		self::$_web_client = null;
	}
}