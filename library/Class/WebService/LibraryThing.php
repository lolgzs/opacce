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
/*
 * WEB SERVICE LIBRARY THING
 *	Renvoie une liste d'isbn d'autres editions à partir d'un ISBN
 */

class Class_WebService_LibraryThing
{
	var $xml;																// Pointeur sur la classe xml de base
	var $req;																// Racine requete http
	
	// constructeur
	function __construct()
	{
		$this->xml= new Class_Xml();
		$this->req="http://www.librarything.com/api/thingISBN/@ISBN@&compare=1";
	}
	
	function requete($url)
	{
		$this->xml->open_url($url);
		return $this->test_erreur();
	}
	
	// Retourne la notice d'après un noeud de type item
	function rend_isbn_proches($isbn)
	{
		// Requete http
		if(! $isbn ) return false;
		$req=str_replace("@ISBN@",$isbn,$this->req);
		if( $this->requete($req) == false) return false;
		
		// Renvoie les isbn
		$notice=array();
		$node=$this->xml->getNode("idlist");
		$node=$this->xml->get_child_node($node,"isbn");
		while( $node > 0 )
		{
			$notice[]=$this->xml->valeurs[$node]["value"];
			$node=$this->xml->get_sibling($node);
		}	
		if(!count($notice)) return false;
		return $notice;
	}
	
	// Analyse de la réponse Library thing
	function test_erreur()
	{
		$item=$this->xml->getNode("unknownID");
		if( $item >= 0 ) return false;
		return true;
	}
}