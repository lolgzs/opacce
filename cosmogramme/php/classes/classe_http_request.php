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
//	RECUPERE LE CONTENU D'UNE URL
//////////////////////////////////////////////////////////////////////////////////////////

class HTTPRequest
{
   private $_url;									// full URL
   private $_host;								// HTTP host
   private $_protocol;						// protocol (HTTP/HTTPS)
   private $_uri;									// request URI
   private $_port;								// port
   private $proxy;								// Param�tres de connexion proxy
   private $header;								// Header de retour
   
   // constructor
   function __construct($url)
   {
       global $cfg;
       $this->proxy["host"]=$cfg["PROXY_IP"];
       $this->proxy["port"]=$cfg["PROXY_PORT"];
       $this->proxy["user"]=$cfg["PROXY_USER"];
       $this->proxy["passe"]=$cfg["PROXY_PASSE"];
    
       $this->_url = $url;
       if((strScan(strtoupper($url),"LOCALHOST",0)>0) or (strScan($url,"127.0.0.1",0)>0)) unset($this->proxy);
       $this->_scan_url();
   }
   
   // scan url
   function _scan_url()
   {
       $req = $this->_url;    
       $pos = strpos($req, '://');
       $this->_protocol = strtolower(substr($req, 0, $pos));
       
       $req = substr($req, $pos+3);
       $pos = strpos($req, '/');
       if($pos === false)
           $pos = strlen($req);
       $host = substr($req, 0, $pos);
       
       if(strpos($host, ':') !== false)
       {
           list($this->_host, $this->_port) = explode(':', $host);
       }
       else 
       {
           $this->_host = $host;
           $this->_port = ($this->_protocol == 'https') ? 443 : 80;
       }
       if($this->proxy["host"]) $this->_uri=$this->_url;
       else
       {
       	$this->_uri = substr($req, $pos);
       	if($this->_uri == '') $this->_uri = '/';
      }
   }
   
   // download URL to string
   function DownloadToString()
   {
       $crlf = "\r\n";
       // generate request
       if($this->proxy["host"])
       {
       		if($this->proxy["user"]) $req='GET '.$this->_uri.' HTTP/1.1'.$crlf .'Host: '.$this->_host.":".$this->port.$crlf."Proxy-Authorization: Basic ". base64_encode($this->proxy["user"].":".$this->proxy["passe"]);
       		else $req='GET '.$this->_uri.' HTTP/1.0'.$crlf .'Host: '.$this->_host.":".$this->port;
       }
       else $req='GET '.$this->_uri.' HTTP/1.0'.$crlf .'Host: '.$this->_host;
       $req.=$crlf;
       $req.="User-Agent: MediaWiki open proxy check".$crlf;
       $req.='Content-type: text/html; charset=ISO-8859-1'.$crlf;

       $req.=$crlf;
       
       
      // print($req); exit;
       
       // open
       if($this->proxy["host"]) $fp = @fsockopen( $this->proxy["host"], $this->proxy["port"]);
       else $fp = @fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host, $this->_port);
       if(!$fp) return false;
       fwrite($fp, $req);
       // get data
       while(is_resource($fp) && $fp && !feof($fp)) $response .= fread($fp, 512);
       fclose($fp);
       
       // split header and body
       $pos = strpos($response, $crlf . $crlf);
       if($pos === false)
           return($response);
       $header = substr($response, 0, $pos);$this->header=$header;
       $body = substr($response, $pos + 2 * strlen($crlf));
       
       // parse headers
       $headers = array();
       $lines = explode($crlf, $header);
       foreach($lines as $line)
           if(($pos = strpos($line, ':')) !== false)
               $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));
       // redirection?
       if(isset($headers['location']))
       {
           $http = new HTTPRequest($headers['location']);
           return($http->DownloadToString($http));
       }
       else 
       {
           return($body);
       }
   }
}
?> 
