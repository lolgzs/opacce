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
// OPAC3 : REPERTOIRE DES WEB_SERVICES
//////////////////////////////////////////////////////////////////////////////////////

class Class_WebService_AllServices {
	private static $_http_client;

	private $services = array
		(
			"Amazon" => array
				(
					"valeurs" => array("isbn" => "2709624931","auteur" => "zola","page" => "1"),
					"services" => array("rend_avis(@isbn,@page)"
												,"rend_analyses(@isbn)"
												,"rend_bibliographies(@isbn)"
												,"rend_livres_auteur(@auteur,@page)"
												,"rend_livres_similaires(@isbn)"
												,"rend_images(@isbn)")
				),
			"AmazonSonores" => array
				(
					"valeurs" => array("ean" => "0794881405923","asin" => "B00005BH6V","volume" => "1","track" => "1"),
					"services" => array("rend_notice_ean(@ean)"
												,"getImages(@ean)"
												,"get_url_ecoute(@asin,@volume,@track)")
				),
			"AmazonVideo" => array
				(
					"valeurs" => array("ean" => "3388334500012"),
					"services" => array("rend_notice_ean(@ean)"
												,"getImages(@ean)")
				),
			"AmazonCdrom" => array
				(
					"valeurs" => array("ean" => "5030931031182"),
					"services" => array("rend_notice_ean(@ean)"
												,"getImages(@ean)")
				),
			"Lastfm" => array
				(
					"valeurs" => array("titre" => "Unplugged","auteur" => "Eric Clapton"),
					"services" => array("getAlbum(@titre,@auteur)"
												,"getMorceaux(@titre,@auteur)"
												,"getPhotos(@auteur)"
												,"getDiscographie(@auteur)")
				),
			"Premiere" => array
				(
					"valeurs" => array("titre" => "avatar"),
					"services" => array("get_resume(@titre)"
												,"getImages(@titre)")
				),
			"LibraryThing" => array
				(
					"valeurs" => array("isbn" => "978-2-07-061239-0"),
					"services" => array("rend_isbn_proches(@isbn)")
				),
			"Fnac" => array
				(
					"valeurs" => array("isbn" => "978-2-7427-6501-0"),
					"services" => array("getResume(@isbn)")
				 ),
			"OAI" => array
			(
			 "valeurs" => array("oai_handler" => 'http://oai.bnf.fr/oai2/OAIHandler', "set" => "gallica"),
			 "services" => array("getSetsFromHandler(@oai_handler)",
													 "getRecordsFromHandlerAndSet(@oai_handler, @set)")
			 ),
			"SRU" => array
			(
			 "valeurs" => array("service_url" => 'http://bvpb.mcu.es/i18n/sru/sru.cmd', "query" => "spain"),
			 "services" => array("search(@service_url, @query)")
			 )
		);

//------------------------------------------------------------------------------------------------------
// Lance un service AFI et renvoie le resultat
//------------------------------------------------------------------------------------------------------
	static function runServiceAfiBiographie($args) {
		return self::runServiceAfi(8, $args);
	}


	static function runServiceAfiVideo($args) {
		return self::runServiceAfi(9, $args);
	}


	static function runServiceAfiInterviews($args) {
		return self::runServiceAfi(7, $args);
	}

	static function runServiceAfiUploadVignette($args) {
		return self::runServiceAfi(12, $args);
	}

	static function setHttpClient($client) {
		self::$_http_client = $client;
	}


	static function uploadVignetteForNotice($url, $id) {
		$notice = Class_Notice::find($id);
		$result = static::runServiceAfiUploadVignette(array_filter(['isbn' => $notice->getIsbn(),
																																'type_doc' => $notice->getTypeDocPergame(),
																																'titre' => $notice->getTitrePrincipal(),
																																'auteur' => $notice->getAuteurPrincipal(),
																																'image' => $url,
																																'tome_alpha' => $notice->getTomeAlpha(),
																																'clef_chapeau' => $notice->getClefChapeau()]));

		if ('ok' !== $result['statut'])
			return $result['message'];

		$notice
			->setUrlVignette($result['vignette'])
			->setUrlImage($result['image'])
			->save();
	}


	static function httpGet($url, $args) {
		if (!isset(self::$_http_client))
			self::$_http_client = new Class_WebService_SimpleWebClient();
		return self::$_http_client->open_url($url.'?'.http_build_query($args));
	}


	static function runServiceAfi($service,$args)	{
		if (!$url_service = Class_CosmoVar::get('url_services'))
			return false;

		if (!$args)
			$args = array();

		$args['src'] = self::createSecurityKey();
		$args['action'] = $service;

		return json_decode(self::httpGet($url_service, $args),
											 true);
	}


	public static function createSecurityKey() {
		return md5("IMG".date("DxzxYxM")."VIG");
	}
	

//------------------------------------------------------------------------------------------------------
// Test d'un service
//------------------------------------------------------------------------------------------------------
	public function testService($id_service,$id_fonction)	{
		if(!$id_service) return false;
		$instruction="\$cls=new Class_WebService_".$id_service."();";
		eval($instruction);
		$num_fonction = 0;
		foreach($this->services[$id_service]["services"] as $instruction)
		{
			$num_fonction++;
			if($id_fonction and $num_fonction != $id_fonction) continue;
			
			// On met les arguments
			foreach($this->services[$id_service]["valeurs"] as $param => $valeur)
			{
				$instruction=str_replace("@".$param,"'".$valeur."'",$instruction);
			}
			
			// on fabrique l'instruction d'appel au web service et on l'execute
			$instruction="\$test=\$cls->".$instruction.";";
			eval($instruction);

			// On compose le resultat affichable
			if(!$test) $ret[$id_service][$num_fonction]="<b><font family='verdana' color='red'><b>Le service n'a renvoyé aucun résultat</b></font></b>";
			else $ret[$id_service][$num_fonction]=$test;
		}
		return $ret;
	}

//------------------------------------------------------------------------------------------------------
// Liste des services
//------------------------------------------------------------------------------------------------------
	public function getServices()
	{
		return $this->services;
	}

}