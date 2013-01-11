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
// OPAC3 - WEB-SERVICE AMAZON
//////////////////////////////////////////////////////////////////////////////////////////

class Class_WebService_Amazon
{
	var $xml;																													// Pointeur sur la classe xml de base
	private $req;																											// Racine requete http
	private $id_afi="AKIAINZSICEPECFZ4RPQ";														// ID afi chez amazon dans cfg
	private $secret_key="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";		// Clé secrete chez amazon
	
//------------------------------------------------------------------------------------------------------
// Constructeur
//------------------------------------------------------------------------------------------------------
	function __construct()
	{
		$this->xml= new Class_Xml();
		$this->req="http://webservices.amazon.fr/onca/xml?Service=AWSECommerceService";
		$this->req.="&AWSAccessKeyId=".$this->id_afi;
	}

	
//------------------------------------------------------------------------------------------------------
// Execution requete http et test erreur
//------------------------------------------------------------------------------------------------------
	function requete($req)
	{
		$url=$this->req.$req;
		
		// Ajout de signature AMAZON
		$secret = $this->secret_key;
		$timestamp = gmstrftime('%Y-%m-%dT%H:%M:%S');
		$host = parse_url($url,PHP_URL_HOST);
		$url= $url. '&Timestamp=' . $timestamp;
		$paramstart = strpos($url,'?');
		$workurl = substr($url,$paramstart+1);
		$workurl = str_replace(',','%2C',$workurl);
		$workurl = str_replace(':','%3A',$workurl);
		$params = explode('&',$workurl);
		sort($params); 
		$signstr = "GET\n" . $host . "\n/onca/xml\n" . implode('&',$params);
		$signstr = base64_encode(hash_hmac('sha256', $signstr, $secret, true)); 
		$signstr = urlencode($signstr);
		$signedurl = $url . "&Signature=" . $signstr;
		
		// Lancer la requete
		$this->xml->open_url($signedurl);
		return $this->test_erreur();
	}
	
//------------------------------------------------------------------------------------------------------
// Retourne la notice d'après un noeud de type item
//------------------------------------------------------------------------------------------------------
	function rend_notice($node)
	{
		$notice["asin"]=$this->xml->get_child_value($node,"asin");
		$img=$this->xml->get_child_node($node,"smallimage");
		if($img)
		{
			$notice["vignette"]=$this->xml->get_child_value($img,"url");
			$img=$this->xml->get_child_node($node,"largeimage");
			if($img) $notice["image"]=$this->xml->get_child_value($img,"url");
		}
		$node=$this->xml->get_child_node($node,"itemattributes");
		$notice["isbn"]=$this->xml->get_child_value($node,"isbn");
		$notice["ean"]=$this->xml->get_child_value($node,"ean");
		$notice["titre"]=$this->xml->get_child_value($node,"title");
		$notice["editeur"]=$this->xml->get_child_value($node,"publisher");
		$dateClass = new Class_Date();
		$notice["date"]=$dateClass->LocalizedDate($this->xml->get_child_value($node,"publicationdate"), 'yyyy-MM-dd');
		$auteur=$this->xml->get_child_node($node,"author");
		while($auteur)
		{
			$index=count($notice["auteurs"]);
			$notice["auteurs"][$index]=$this->xml->get_value($auteur);
			$auteur=$this->xml->get_sibling($auteur);
		}
		$auteur=$this->xml->get_child_node($node,"creator");
		while($auteur)
		{
			$index=count($notice["auteurs"]);
			$notice["auteurs"][$index]=$this->xml->get_value($auteur);
			$auteur=$this->xml->get_sibling($auteur);
		}
		return $notice;
	}


	// pour rendre le service polymorphique avec Babelio, Amazon, Notices ....
	public function getAvis($notice, $page) {
		if (! $notice->isLivre()) return false;
		$avis = $this->rend_avis($notice, $page);
		if ($avis == false) return false;

		$avis['titre'] = 'Lecteurs Amazon';
		return $avis;
	}
	
//------------------------------------------------------------------------------------------------------
// Avis des lecteurs
//------------------------------------------------------------------------------------------------------
	function rend_avis($notice, $page)	{
		if ($notice instanceof Class_Notice)
			$isbn = $notice->getIsbn();
		else
			$isbn = $notice;

		if(!trim($isbn)){
			return false;
		}
		if($page>0){
			$page="&ReviewPage=".$page;
		}
		$req=$this->req_isbn($isbn)."&ResponseGroup=Reviews".$page;
		if(!$this->requete($req)){
			return false;
		}
		$item=$this->xml->getNode("customerreviews");
		$avis["note"]=$this->xml->get_child_value($item,"averagerating");

		if(!$avis["note"]){
			return false;
		}
		$avis["nombre"]=$this->xml->get_child_value($item,"totalreviews");
		$avis["nb_pages"]=$this->xml->get_child_value($item,"totalreviewpages");
		$item=$this->xml->get_child_node($item,"review");
		$dateClass = new Class_Date();
		while( $item ) {
			$avis_notice = new Class_AvisNotice();
			$avis_notice
				->setNote($this->xml->get_child_value($item,"rating"))
				->setDateAvis($dateClass->LocalizedDate($this->xml->get_child_value($item,"date"), 'yyyy-MM-dd'))
				->setEntete(utf8_encode($this->xml->get_child_value($item,"summary")))
				->setAvis(utf8_encode($this->xml->get_child_value($item,"content")))
				->setNotice($notice)
				->setUser(null);

			$index=count($avis["liste"]);
			$avis["liste"][$index] = $avis_notice;
			$item=$this->xml->get_sibling($item);
		}
		
		return $avis;
	}
	
//------------------------------------------------------------------------------------------------------
// Résumés et analyses
//------------------------------------------------------------------------------------------------------
	public function getResumes($notice) {
		if (!$service = $notice->getIsbnOrEan())
			return array();

		return $this->rend_analyses($service);
	}


	function rend_analyses($isbn)	{
		if(!trim($isbn)) 
			return array();

		$req=$this->req_isbn($isbn)."&ResponseGroup=Medium";
		if (!$this->requete($req)) 
			return array();

		$item=$this->xml->getNode("EditorialReviews");
		$item=$this->xml->get_child_node($item,"EditorialReview");
		while( $item )	{
			$index=count($avis);
			$avis[$index]["source"]=$this->xml->get_child_value($item,"source");
			$avis[$index]["texte"]=utf8_encode($this->xml->get_child_value($item,"content"));
			$item=$this->xml->get_sibling($item);
		}		
		return $avis;
	}
	
//------------------------------------------------------------------------------------------------------
// List des listmanias (plus utilisé)
//------------------------------------------------------------------------------------------------------
	function rend_bibliographies($isbn)
	{
		// Auteurs
		if(!trim($isbn)) return false;
		$req=$this->req_isbn($isbn)."&ResponseGroup=Small";
		if(!$this->requete($req)) return false;
		$item=$this->xml->getNode("item");
		$auteur=$this->xml->get_child_node($item,"author");
		while($auteur)
		{
			$index=count($biblio["auteurs"]);
			$biblio["auteurs"][$index]=$this->xml->get_value($auteur);
			$auteur=$this->xml->get_sibling($auteur);
		}
		$auteur=$this->xml->get_child_node($item,"creator");
		while($auteur)
		{
			$index=count($biblio["auteurs"]);
			$biblio["auteurs"][$index]=$this->xml->get_value($auteur);
			$auteur=$this->xml->get_sibling($auteur);
		}
		
		// Themes
		$req=$this->req_isbn($isbn)."&ResponseGroup=Subjects";
		if(!$this->requete($req)) return $biblio;
		$item=$this->xml->getNode("subjects");
		$item=$this->xml->get_child_node($item,"subject");
		while( $item )
		{
			$index=count($biblio["theme"]);
			$biblio["theme"][$index]=$this->xml->get_value($item);
			$item=$this->xml->get_sibling($item);
		}
		
		// Listmania
		$req=$this->req_isbn($isbn)."&ResponseGroup=ListmaniaLists";
		if(!$this->requete($req)) return $biblio;
		$item=$this->xml->getNode("listmanialists");
		$item=$this->xml->get_child_node($item,"listmanialist");
		while( $item )
		{
			$index=count($biblio["listmania"]);
			$biblio["listmania"][$index]["id"]=$this->xml->get_child_value($item,"listid");
			$biblio["listmania"][$index]["texte"]=$this->xml->get_child_value($item,"listname");
			$item=$this->xml->get_sibling($item);
		}
		
		return $biblio;
	}
	
//------------------------------------------------------------------------------------------------------
// Du meme auteur chez amazon (plus utilise)
//------------------------------------------------------------------------------------------------------
	function rend_livres_auteur($cherche,$page)
	{
		if($page>0) $page="&ItemPage=".$page;
		$req="&Operation=ItemSearch&SearchIndex=Books&Author=".$cherche.$page."&ResponseGroup=Medium";
		if(!$this->requete($req)) return false;
		$item=$this->xml->getNode("Items");
		$ret["nb_resultats"]=$this->xml->get_child_value($item,"totalresults");
		$ret["nb_pages"]=$this->xml->get_child_value($item,"totalpages");
		$item=$this->xml->get_child_node($item,"item");
		while( $item )
		{
			$index=count($ret["liste"]);
			$ret["liste"][$index]=$this->rend_notice($item);
			$item=$this->xml->get_sibling($item);
		}	
		return $ret;
	}
	
//------------------------------------------------------------------------------------------------------
// Livre d'une listmania (plus utilise)
//------------------------------------------------------------------------------------------------------
	function rend_livres_listmania($idListe)
	{
		$req="&Operation=ListLookup&ListType=Listmania&ListId=".$idListe."&ResponseGroup=Medium";
		if(!$this->requete($req)) return false;
		$item=$this->xml->getNode("list");
		$listitem=$this->xml->get_child_node($item,"listitem");
		$ret["nb_resultats"]=0;
		while( $listitem )
		{
			$item=$this->xml->get_child_node($listitem,"item");
			if(!$item) break;
			$index=count($ret["liste"]);
			$ret["liste"][$index]=$this->rend_notice($item);
			$ret["nb_resultats"]++;
			$listitem=$this->xml->get_sibling($listitem);
		}
		return $ret;
	}
	
//------------------------------------------------------------------------------------------------------
// Ouvrages similaires chez AMAZON (plus utilise)
//------------------------------------------------------------------------------------------------------
	function rend_livres_similaires($isbn)
	{
		if(!trim($isbn)) return false;
		$req="&Operation=SimilarityLookup&ItemId=".$isbn."&ResponseGroup=Medium";
		if(!$this->requete($req)) return false;
		$item=$this->xml->getNode("item");
		if($item<0) return false;
		$ret["nb_resultats"]=0;
		while($item)
		{
			$index=count($ret["liste"]);
			$ret["liste"][$index]=$this->rend_notice($item);
			$ret["nb_resultats"]++;
			$item=$this->xml->get_sibling($item);
		}
		return $ret;
	}
	
//------------------------------------------------------------------------------------------------------
// Retourne la vignette et la grande image
//------------------------------------------------------------------------------------------------------
	function rend_images($isbn)
	{
		if(!trim($isbn)) return false;
		$req=$this->req_isbn($isbn)."&ResponseGroup=Images";
		if(!$this->requete($req)) return false;
		$item=$this->xml->getNode("item");
		if($item<0) return false;
		$vignette=""; $image="";
		// Grande
		$img=$this->xml->get_child_node($item,"largeimage");
		if($img) $image=$this->xml->get_child_value($img,"url");
		// vignette
		$img=$this->xml->get_child_node($item,"mediumimage");
		if($img) $vignette=$this->xml->get_child_value($img,"url");
		if($image > "" and $vignette > "") return array("vignette" => $vignette,"image" => $image);
		
		if($vignette > "" and $image == "") $image=$vignette;
		if($image > "" and $vignette == "") $vignette=$image;
		if($vignette > "") return array("vignette" => $vignette,"image" => $image);
		else return false;
	}

//------------------------------------------------------------------------------------------------------	
// Formatte et rend l'argument isbn pour la requete
//------------------------------------------------------------------------------------------------------
	function req_isbn($isbn)
	{
		if(!trim($isbn)) return "";
		$isbn = str_replace("-","",$isbn);
		$url="&Operation=ItemLookup&ItemId=" .$isbn;
		return $url;
	}

//------------------------------------------------------------------------------------------------------	
// Analyse de la réponse amazon
//------------------------------------------------------------------------------------------------------
	function test_erreur()
	{
		$item=$this->xml->getNode("isvalid");
		$value=$this->xml->get_value($item);
		if( strToUpper($value)=="FALSE") return false;
		return true;
	}
}