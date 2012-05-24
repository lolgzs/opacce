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
// OPAC3 - PREMIERE.fr
//////////////////////////////////////////////////////////////////////////////////////////

class Class_WebService_Premiere
{
	var $url_base;										// Urls de base
	
	function __construct()
	{
		$url_base="http://www.premiere.fr";
		$this->url_base["root"]=$url_base;
		$this->url_base["resume"]=$url_base."/film/@TITRE@/";
		$this->url_base["image"]=$url_base."/film/@TITRE@/";
	}


	public function getResumes($notice) {
		if (!$notice->isDVD())
			return array();

		if ($resume = $this->get_resume($notice->getTitre()))
			return array(array('source' => 'Premiere.fr',
												 'texte' => $resume));
		return array();
	}
		
//------------------------------------------------------------------------------------------------------
// Résumé
//------------------------------------------------------------------------------------------------------
	function get_resume($titre)
	{
		// Changer l'url pour recuperer la page
		$titre=$this->encoder_titre($titre);
		$url=str_replace("@TITRE@",$titre,$this->url_base["resume"]);
		// Get http de la page
			try{
			$httpClient = Zend_Registry::get('httpClient');
			$httpClient->setUri($url);
			$response = $httpClient->request();
			$data = $response->getBody();
			if(!$data) return false;
		}catch (Exception $e){
			return false;
		}
		// Recherche du bon bloc
		$data=utf8_decode($data);
		$pos=strPos($data,"<strong>Synopsis :</strong>");
		if(!$pos) return false;
		$posfin=strPos($data,"</li>",$pos);
		$resume=substr($data,($pos+28),($posfin-$pos));
		return utf8_encode($resume);
	}

//------------------------------------------------------------------------------------------------------
// Image
//------------------------------------------------------------------------------------------------------
	function getImages($titre)
	{
		$titre=$this->encoder_titre($titre);
		$url=str_replace("@TITRE@",$titre,$this->url_base["image"]);
		// Get http de la page
			try{
			$httpClient = Zend_Registry::get('httpClient');
			$httpClient->setUri($url);
			$response = $httpClient->request();
			$data = $response->getBody();
			if(!$data) return false;
		}catch (Exception $e){
			return false;
		}
		// Recherche du bon bloc
		$data=utf8_decode($data);
		$pos=strPos($data,'div class="fichefilm_left"');
		if(!$pos) return false;
		$pos=strPos($data,"<img",$pos);
		if(!$pos) return false;
		$pos=strPos($data,"src",$pos)+5;
		$posfin=strPos($data,'"',$pos);
		$img=$this->url_base["root"].substr($data,$pos,($posfin-$pos));
		return $img;
	}

//------------------------------------------------------------------------------------------------------		
// encoder le titre 
//------------------------------------------------------------------------------------------------------
	function encoder_titre($titre)
	{
		// Retirer l'auteur
		$pos=strscan($titre," / ",0);
		if($pos >0) $titre=trim(strleft($titre,$pos));
		// Clef alphamaj
		$ix=new Class_Indexation();
		$titre=$ix->setArticleDebut($titre);
		$titre=strtolower($ix->AlphaMaj($titre));
		// Formatter avec des tirets
		$titre=str_replace(" ","-",$titre);
		$titre=str_replace("--","-",$titre);
		return $titre;		
	}
}
