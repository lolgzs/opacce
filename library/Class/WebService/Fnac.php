<?php
/**
 * Copyright (c) 2012, Agence FranÃ§aise Informatique (AFI). All rights reserved.
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
// OPAC3 : FNAC
//////////////////////////////////////////////////////////////////////////////////////

class Class_WebService_Fnac
{
	private $url;											// Url de base

//------------------------------------------------------------------------------------------------------
// Constructeur
//------------------------------------------------------------------------------------------------------
	function __construct()
	{
		$this->url='http://www3.fnac.com/advanced/book.do?isbn=';
	}


	public function getResumes($notice) {
		if (!$service = $notice->getIsbnOrEan())
			return array();

		if ($resume = $this->getResume($service))
			return array(array('source' => 'Editeur',
												 'texte' => $resume));
		return array();
	}

//------------------------------------------------------------------------------------------------------
// Résumé de l'editeur
//------------------------------------------------------------------------------------------------------	
	public function getResume($isbn) {
		if(!$isbn) return false;
		$isbn=str_replace("-","",$isbn);
		
		// Get http
		$url=$this->url.$isbn;
		$httpClient = Zend_Registry::get('httpClient');
		$httpClient->setUri($url);
		$response = $httpClient->request();
		$data = $response->getBody();
		$matches = array();

		$pos=striPos($data,"resume");
		if(!$pos) 
			return array();

		$pos = strPos($data,">",$pos)+1;
		$posfin = strPos($data,"</div",$pos);
		$resume = substr($data,$pos,($posfin-$pos));

		return trim(str_replace('Avis de la Fnac&nbsp;:', '', strip_tags($resume)));
	}
	
}