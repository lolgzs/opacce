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


class Class_WebService_Lastfm  extends Class_WebService_Abstract {
	var $xml;																// Pointeur sur la classe xml de base
	var $req;																// Racine requete http
	var $id_afi;														// ID afi chez LastFm dans cfg
	var $ix;																// Classe d'indexation
	var $player;														// Template du player
	
//------------------------------------------------------------------------------------------------------
// Constructeur
//------------------------------------------------------------------------------------------------------
	function __construct()
	{
		$cfg = Zend_Registry::get('cfg');
		$this->id_afi = "b25b959554ed76058ac220b7b2e0a026";
		
		$this->xml= new Class_Xml();
		$this->req="http://ws.audioscrobbler.com/2.0/?";
		$this->req.="api_key=".$this->id_afi;
		$this->ix=new Class_Indexation();
		$this->player='<embed id="lfmPlayer" height="221" width="300" align="middle" swliveconnect="true" name="lfmPlayer" allowfullscreen="true" allowscriptaccess="always" flashvars="lang=fr&lfmMode=playlist&FOD=true&resname=@TITRE@&restype=track&artist=@AUTEUR@&albumArt=&autostart=true" bgcolor="#fff" wmode="transparent" quality="high" menu="true" pluginspage="http://www.macromedia.com/go/getflashplayer" src="http://cdn.last.fm/webclient/s12n/s/5/lfmPlayer.swf" type="application/x-shockwave-flash"/>';
	}
	
//------------------------------------------------------------------------------------------------------
// Execution requete http et test erreur
//------------------------------------------------------------------------------------------------------
	function requete($req)
	{
		$url=$this->req.$req;
		$this->xml->open_url($url);
		return $this->test_erreur();
	}

//------------------------------------------------------------------------------------------------------	
// Cherche album 
//------------------------------------------------------------------------------------------------------	
	public function getAlbum($titre,$auteur)	{
		$titre = implode(' ', $this->ix->getMots($this->ix->codeAlphaTitre($titre)));

		$req=$this->getRequete("album.search","album",$titre);
		if($this->requete($req)==false) return false;
		
		// Controle avec l'auteur
		$nodes=$this->xml->index["ALBUM"];
		if(!$nodes) return false;
		foreach($nodes as $node)
		{
			$album=$this->xml->get_child_value($node, "NAME");
			$artist=$this->xml->get_child_value($node, "ARTIST");
			if($this->ix->compare_expression($titre,$album) == true and $this->ix->compare_expression($auteur,$artist) == true) break;
			$album="";
			$artist="";
		}
		if(!$album) return false;
		
		// Données de l'album (url et images)
		$notice["auteur"]=$artist;
		$notice["url"]=$this->xml->get_child_value($node, "URL");
		$node_image=$this->xml->get_child_node($node, "IMAGE");
		if($this->xml->get_attribut($node_image,"SIZE") == "small") $notice["vignette"]=$this->xml->get_value($node_image);
		$node_image+=4;
		if($this->xml->get_attribut($node_image,"SIZE") == "large") $notice["image"]=$this->xml->get_value($node_image);
		return $notice;
	}

//------------------------------------------------------------------------------------------------------	
// Cherche liste des morceaux d'un album
//------------------------------------------------------------------------------------------------------	
	public function getMorceaux($titre,$auteur)
	{
		$album=$this->getAlbum($titre,$auteur);
		if(!$album) return false;

		$data = self::getHttpClient()->open_url($album['url']);
		// Get de la tables des tracks
		$pos=strPos($data,'<table id="albumTracklist"');
		if(!$pos) return false;
		$posfin=strPos($data,'</table>',$pos);
		$pos=strPos($data,'<tbody',$pos);
		$data=substr($data,$pos,($posfin-$pos));
				
		// Prendre les pistes
		$volume=1;
		$piste=0;
		while(true)
		{
			// Url ecoute
			$pos=strPos($data,'playbuttonCell');
			$pos=strPos($data,">",$pos)+1;
			$posfin=strPos($data,"</td>",$pos);
			$lig=trim(substr($data,$pos,($posfin-$pos)));
			if(strlen($lig)>10)$url_ecoute=true;
			else $url_ecoute="";
			// Morceau
			$pos=strPos($data,'subjectCell',$pos);
			if(!$pos) break;
			$posfin=strPos($data,"</td>",$pos);
			$lig=substr($data,$pos,($posfin-$pos));

			$pos=strScanReverse($lig,'">',-1)+1;
			$piste++;
			$track=trim(str_replace('">', '', substr($lig,($pos+1))));
			$album["morceaux"][$volume][$piste]["titre"]=strip_tags(str_replace('</a>','',$track));
			// Calcul url ecoute
			if($url_ecoute == true)
			{
				$track=str_replace(" ","+",$album["morceaux"][$volume][$piste]["titre"]);
				$auteur=str_replace(" ","+",$auteur);
				$rep=urlencode("'");
				$album["morceaux"][$volume][$piste]["url_ecoute"]=str_replace("'",$rep,$track . ";" .$auteur);
			}
			$data=substr($data,$posfin);
		}
		$album["nb_resultats"]=$piste;

		return $album;
	}

//------------------------------------------------------------------------------------------------------	
// Cherche photos d'un artiste
//------------------------------------------------------------------------------------------------------	
	public function getPhotos($auteur)
	{
		$req=$this->getRequete("artist.search","artist",$auteur);
		if($this->requete($req)==false) return false;
		
		// Controle de l'auteur
		$nodes=$this->xml->index["ARTIST"];
		if(!$nodes) return false;
		foreach($nodes as $node)
		{
			$artist=$this->xml->get_child_value($node, "NAME");
			if($this->ix->compare_expression($auteur,$artist) == true) break;
			$artist="";
		}
		if(!$artist) return false;
		
		// Url pour les photos
		$url=$this->xml->get_child_value($node, "URL")."/+images";
		if(substr($url,0,4)!="http") $url="http://".$url;
		$url=str_replace(" ","+",$url);

		$data = self::getHttpClient()->open_url($url);
		
		// Bloc des photos
		$pos=strPos($data,'<ul id="pictures"',0);
		if(!$pos) return false;
		$posfin=strPos($data,'</ul>',$pos);
		$data=substr($data,$pos,($posfin-$pos));
		while(true)
		{
			$pos=strPos($data,'<img alt=');
			if(!$pos) break;
			$pos=strPos($data,'src=',$pos)+5;
			$posfin=strPos($data,'"',$pos);
			$url_img=substr($data,$pos,($posfin-$pos));
			$photo[]=$url_img;
			$data = substr($data,$posfin);
		}
		return $photo;
	}
	
//------------------------------------------------------------------------------------------------------	
// Cherche discographie
//------------------------------------------------------------------------------------------------------	
	public function getDiscographie($auteur)
	{
		$req=$this->getRequete("artist.search","artist",$auteur);
		if($this->requete($req)==false) return false;
		
		// Controle de l'auteur
		$nodes=$this->xml->index["ARTIST"];
		if(!$nodes) return false;
		foreach($nodes as $node)
		{
			$artist=$this->xml->get_child_value($node, "NAME");
			if($this->ix->compare_expression($auteur,$artist) == true) break;
			$artist="";
		}
		if(!$artist) return false;
		
		// Url pour la discographie
		$url=$this->xml->get_child_value($node, "URL")."/+albums";
		if(substr($url,0,4)!="http") $url="http://".$url;
		$url=str_replace(" ","+",$url);

		$data = self::getHttpClient()->open_url($url);
		
		// Bloc des albums
		$pos=strPos($data,'<ul class="albums',0);
		if(!$pos) return false;
		$posfin=strPos($data,'</ul>"',$pos);
		$data=substr($data,$pos,($posfin-$pos));
		
		while(true)
		{
			$pos=strPos($data,'<div class="resContainer">');
			if(!$pos) break;
			$index=count($albums);
			// Vignette
			$pos=strPos($data,'src=',$pos)+5;
			$posfin=strPos($data,'"',$pos);
			$albums[$index]["vignette"]=substr($data,$pos,($posfin-$pos));
			// Titre
			$posfin=strPos($data,'</a>',$posfin);
			for($pos=$posfin; $data[$pos] != ">"; $pos--);
			$pos=$pos+1;
			$albums[$index]["titre"]=trim(substr($data, $pos,($posfin-$pos)));
			// date et nbre de pistes
			$pos=strpos($data,'<p class="label">',$posfin);
			if($pos)
			{
				$posfin=strpos($data,'</p>',$pos);
				$bloc=substr($data,$pos,($posfin-$pos));
				$bloc=str_replace("Released","Sortie le",$bloc);
				$bloc=str_replace("track","titre",$bloc);
				$elem=explode('<br />',$bloc);
				for($i=0; $i < count($elem); $i++) $elem[$i]=trim(strip_tags($elem[$i]));
				$albums[$index]["infos"]=$elem;
			}
			// Eliminer le bloc
			$data = substr($data,$posfin);
		}
		//tracedebug($albums,true);
		return $albums;
	}

//------------------------------------------------------------------------------------------------------	
// Formatte les parametres et rend la requete
//------------------------------------------------------------------------------------------------------
	private function getRequete($operation, $argTitre, $titre)
	{
		if(!trim($titre)) return "";
		$titre=$this->ix->Alphamaj($titre);
		$titre=str_replace(" ","+",$titre);
		$titre=str_replace("+++","+",$titre);
		$titre=str_replace("++","+",$titre);
		$req="&method=".$operation."&".$argTitre."=".$titre;
		return $req;
	}

//------------------------------------------------------------------------------------------------------	
// Analyse de la réponse 
//------------------------------------------------------------------------------------------------------
	function test_erreur()
	{
		$item=$this->xml->getNode("OPENSEARCH:TOTALRESULTS");
		if(!$item) return false;
		$value=$this->xml->get_value($item);
		if($value<1) return false;
		return true;
	}
}