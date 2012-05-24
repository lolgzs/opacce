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
// OPAC3 - WEB-SERVICE BIBLIOSURF
//////////////////////////////////////////////////////////////////////////////////////////

class Class_WebService_Bibliosurf {
	private $id_client;											// Id de la bib issue des variables
	private $req;														// Racine requete http

//------------------------------------------------------------------------------------------------------
// Constructeur
//------------------------------------------------------------------------------------------------------
	function __construct()
	{
		$this->req="http://www.bibliosurf.com/spip.php?page=widget_@BIB@&ean=";
		$this->id_client=fetchOne("select VALEUR from bib_admin_var where clef='ID_BIBLIOSURF'");
	}
	
//------------------------------------------------------------------------------------------------------
// Execution requete http et test erreur
//------------------------------------------------------------------------------------------------------
	function requete($isbn)
	{
		// Controle id_ client
		if(!$this->id_client) return false;
		
		// Formattage parametres
		$isbn=str_replace("-","",$isbn);
		$url=str_replace("@BIB@",$this->id_client,$this->req);
		$url.=$isbn;

		// Lancer la requete
		try
		{
			$httpClient = Zend_Registry::get('httpClient');
			$httpClient->setUri($url);
			$response = $httpClient->request();
			$data = $response->getBody();
			return $data;
		}catch (Exception $e){
			return false;
		}
	}


	public function getResumes($notice) {
		if ($resume = $this->getUrls($notice->getIsbnOrEan()))
			return array(array('source' => 'Bibliosurf (liens)',
												 'texte' => $resume));
		return array();
	}


//------------------------------------------------------------------------------------------------------
// Retourne les urls
//------------------------------------------------------------------------------------------------------
	function getUrls($isbn)
	{
		// recup des données
		$data=$this->requete($isbn);
		if(!$data) return false;
		if(strpos($data,"Erreur 404")) return false;

		// Decoupage du texte
		$data=explode("document.write('",$data);
		if(count($data)<3) return false;
		for($i=2; $i<=count($data); $i++)
		{
			$ligne=str_replace("')","",$data[$i]);
			$ligne=str_replace('href="','id="surf'.$i.'" href="javascript:bibliosurf(\'surf'.$i.'\')" url="',$ligne);
			$ligne=str_replace('class="spip_out"','',$ligne);
			$ligne=str_replace(';','',$ligne);
			if(strpos($ligne,'<b>')===false) $ligne=str_replace('<a','<img src="'.URL_IMG.'fleche_verte.gif" style="margin-left:10px;margin-right:5px;padding-top:3px"><a',$ligne);
			else $ligne='<br>'.$ligne;
			$ret.=$ligne;
		}
		$ret.='<div id="dialog_surf" style=display:none"></div>';
		return $this->getJavascript().substr($ret,6);
	}

//------------------------------------------------------------------------------------------------------
// Javascript affichage
//------------------------------------------------------------------------------------------------------
	private function getJavascript()
	{
		$ret="<script>
			function bibliosurf(sId)
			{
				// Si over-blog on ouvre un nouveau navigateur
				var sUrl=$('#'+sId).attr('url');
				if(sUrl.indexOf('over-blog')>0)
				{
					window.open(sUrl,\"_blank\",\"location=yes, width=800, height=410, scrollbars=1, left=100\");
					return;
				}
				
				else
				{
					sUrl=$('#'+sId).attr('url');
					$('#dialog_surf').html('<div align=\"center\"><a href=\"'+sUrl+'\" target=\"_blank\" style=\"color:blue\"><b>Ouvrir ce lien dans une nouvelle fenêtre</b></a></div><iframe src=\"'+sUrl+'\" width=\"100%\" height=\"96%\" frameborder=\"0\"></iframe>');
					$('#dialog_surf').dialog
				(
					{
						width: 900,
						height:600,
						modal:true,
						title:'Liens proposés par la librairie <a href=\"http://www.bibliosurf.com\" target=\"_blank\" style=\"text-decoration:underline;color:blue\" title=\"Aller sur bibliosurf.com\">Bibliosurf.com</a>'
					}
				);
			}
			}
			</script>";
		return $ret;
	}
}