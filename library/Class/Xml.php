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
// OPAC3 - XML Parser
//////////////////////////////////////////////////////////////////////////////////////////

class Class_Xml {
	public $index;								// Tableau des index
	public $valeurs;							// Tableau des valeurs
	public static $_http_client;

	public static function setDefaultHttpClient($http_client) {
		self::$_http_client = $http_client;
	}

	
	public static function getHttpClient() {
		if (!isset(self::$_http_client))
			self::$_http_client = new Class_WebService_SimpleWebClient();

		return	self::$_http_client;
	}


	function open_url( $url ) {
		try{
			$data = self::getHttpClient()->open_url($url);
			// Decodage utf-8
			$test=strtoUpper(strleft($data,50));
			if(strScan($test,"UTF-8",0) >0) $charset="UTF-8"; else $charset="ISO-8859-1";

			// Découper le xml
			$parser = xml_parser_create($charset);
			xml_parser_set_option ($parser,XML_OPTION_TARGET_ENCODING,$charset);
			xml_parse_into_struct($parser, $data, $this->valeurs, $this->index);
			xml_parser_free($parser);
				
		}catch (Exception $e){
			$data = false;
			$errorMessage = "Impossible d'accéder à l'url" . " : " . $url;
		}
	}

	// Ouvrir un fichier xml
	function open_fichier( $fic)
	{
		include_once( "fonctions/file_system.php" );
		$data=lire_fichier($fic);
		$parser = xml_parser_create("ISO-8859-1");
		xml_parse_into_struct($parser, $data, $this->valeurs, $this->index);
		xml_parser_free($parser);
	}
	// Rend un flux rss dans un tableau
	function open_rss($url)
	{
		$this->open_url($url);
		$rss[0]["titre"]=$this->get_value($this->get_child_node($this->index["CHANNEL"][0],"TITLE"));
		foreach($this->index["ITEM"] as $item)
		{
			if($this->valeurs[$item]["type"]=="open")
			{
				$index=count($rss);
				$rss[$index]["titre"]=$this->get_value($this->get_child_node($item,"TITLE"));
				$rss[$index]["description"]=$this->get_value($this->get_child_node($item,"DESCRIPTION"));
				$rss[$index]["url"]=$this->get_value($this->get_child_node($item,"LINK"));
				$rss[$index]["date"]=$this->get_value($this->get_child_node($item,"PUBDATE"));
				if(trim($rss[$index]["date"])) $rss[$index]["date"]=@date("j-m-Y G:i", strtotime ( $rss[$index]["date"]));
			}
		}
		return $rss;
	}
	// Rend la valeur d'un fils
	function get_child_value($parent, $tag)
	{
		$child=$this->get_child_node($parent,$tag);
		if(!$child) return false;
		return $this->get_value($child);
	}

	// Cherche un noeud
	function getNode($tag)
	{
		$tag=strToUpper($tag);
		if(isset($this->index[$tag])) return $this->index[$tag][0];
		return -1;
	}
	// Rend un item de même tag et même niveau
	function get_sibling($node)
	{
		$niveau=$this->valeurs[$node]["level"];
		$tag=$this->valeurs[$node]["tag"];
		for($i=$node+1; $i<32000; $i++)
		{
			if(!$this->valeurs[$i]) return false;
			if($this->valeurs[$i]["level"]< $niveau ) return false;
			if($this->valeurs[$i]["tag"]==$tag and $this->valeurs[$i]["type"] != "close" and $this->valeurs[$i]["level"]==$niveau) return $i;
		}
		return false;
	}
	// Rend un fils par son tag
	function get_child_node( $node, $tag)
	{
		if($this->valeurs[$node]["type"]=="complete") return false;
		$tag=strToUpper($tag);
		for($i=$node; $i<32000; $i++)
		{
			if(!$this->valeurs[$i]) return false;
			if($this->valeurs[$i]["tag"]==$tag) return $i;
			if($this->fin_node($node, $i) == true) return false;
		}
	}

	// Dump du contenu d'une balise
	function dump_node( $node)
	{
		if($this->valeurs[$node]["type"]=="complete"){dump_array($this->valeurs[$node]); return true;}
		for($i=$node; $i< 32000; $i++)
		{
			if(!$this->valeurs[$i]) return false;
			if($this->fin_node($node, $i) == true)
			{
				//dump_array($data);
				return true;
			}
			//$data[]= $this->valeurs[$i];
			dump_array($this->valeurs[$i]);flush();
		}
	}

	// Rend la valeur
	function get_value( $node)
	{
		return $this->valeurs[$node]["value"];
	}

	// Rend la valeur d'un attribut a partir d'un noeud
	function get_attribut($node, $attribut)
	{
		$attribut=strtoupper($attribut);
		return $this->valeurs[$node]["attributes"][$attribut];
	}
	
	// Afficher les tableaux de data
	function dump_xml($format)
	{
		if(strToUpper($format)=="XML")
		{
			foreach($this->valeurs as $balise)
			{
				$balise["tag"]=strToLower($balise["tag"]);
				$data.=str_repeat("&nbsp;", 5 * $balise["level"]);
				if($balise["type"] == "close" ) $data.="@D@/"; else $data.="@D@";
				$data.= '<font color="#772B1A">'. $balise["tag"] .'</font>';
				if($balise["attributes"])
				{
					foreach( $balise["attributes"] as $key => $value) $data.=' <font color="#ff0000">'.strToLower($key).'</font><font color="#0000ff">="</font><font color="#ff0000">'.$value.'</font><font color="#0000ff">"</font>';
				}
				if($balise["type"]=="complete" and $balise["value"]) $data.= "@F@<b>" .$balise["value"] ."</b>@D@/".'<font color="#772B1A">'.$balise["tag"] .'</font>';
				$data.= "@F@";
				$data.= BR;
			}
			$data = str_replace("@D@/", '<font color="#0000ff">&lt;/</font>', $data);
			$data = str_replace("@D@", '<font color="#0000ff">&lt;</font>', $data);
			$data = str_replace("@F@", '<font color="#0000ff">&gt;</font>', $data);
			print($data);
		}
		else
		{
			dump_array($this->index);
			dump_array($this->valeurs);
		}
	}

	// Teste si on est à la fin d'un noeud
	function fin_node( $node, $test)
	{
		if($this->valeurs[$node]["type"]=="complete") return true;
		if($this->valeurs[$test]["tag"]==$this->valeurs[$node]["tag"]
		and $this->valeurs[$test]["type"]=="close"
			and $this->valeurs[$test]["level"]== $this->valeurs[$node]["level"]) 
		return true;
	}
}

?>