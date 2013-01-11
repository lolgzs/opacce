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
// OPAC3 : Selection des BIBS et Comptage des notices en fonction de la selection
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Controller_Plugin_SelectionBib extends Zend_Controller_Plugin_Abstract
{
//------------------------------------------------------------------------------------------------------
// Initialise les paramètres de sélection des bibs et le comptage par id geographique
//------------------------------------------------------------------------------------------------------
	function preDispatch(Zend_Controller_Request_Abstract $request)	{
		if ($request->getModuleName() != 'opac')
			return;

		// Changement de profil : on reset la selection
		if(array_isset("id_profil", $_REQUEST)) {
			$profil = Class_Profil::getCurrentProfil();
			unset($_SESSION["selection_bib"]);

			if ($profil->getIdSite()) {
				$id_bibs = $profil->getIdSite();
				$message="la biblioth&egrave;que : ".utf8_decode($profil->getBibLibelle());
			}
		}

		// Retour de selection de bibliotheques par formulaire
		if (array_key_exists('bib_select', $_REQUEST))
		{
			unset($_SESSION["selection_bib"]);
			$id_bibs="";
			if($_REQUEST["bib_select"] != "TOUT")
			{
				$bibs=explode(",",$_REQUEST["bib_select"]);
				foreach($bibs as $id_bib)
				{
					$id_id_bib=intval($id_bib);
					$data=fetchEnreg("select ID_ZONE,LIBELLE from bib_c_site where ID_SITE='$id_bib'");
					if(!$data) continue;
					$compteur_zone[$data["ID_ZONE"]]++;
					$nb++;
					if($message) $message.=", ";
					$message.=$data["LIBELLE"];
					if($id_bibs) $id_bibs .=",";
					$id_bibs .= $id_bib;
				}

				// Controle si zone entiere selectionnee
				if($nb)
				{
					$tous=true;
					foreach($compteur_zone as $z => $compteur)
					{
						$controle=fetchOne("select count(*) from bib_c_site where ID_ZONE='".$z."'");
						if($controle == $compteur)
						{
							if($msg_zone) $msg_zone.=", ";
							$msg_zone.= fetchOne("select LIBELLE from bib_c_zone where ID_ZONE='$z'");
						}
						else $tous = false;
					}
					// Territoire(s) entier(s)
					if($tous == true )
					{
						if(count($compteur_zone) == 1) $message = "le territoire de : ".utf8_decode($msg_zone);
						else $message = "les territoires suivants : ".utf8_decode($msg_zone);
					}
					else $message="dans les biblioth&egrave;ques suivantes : ".utf8_decode($message);
				}
			}
		}

		// Navigation dans les zones geographiques (module opac)
		$params=$request->getParams();
		if (($params["controller"]=="bib") and (array_key_exists('id', $params))) {
			if ($params["action"] == "zoneview") 
				$_REQUEST["geo_zone"] = $params["id"];
			elseif ($params["action"] == "bibview") 
				$_REQUEST["geo_bib"]=$params["id"];
		}

		// Si on a lance une recherche par sélection géographique
		$zone = null;
		$bib = null;
		if (array_key_exists('geo_zone', $_REQUEST) or array_key_exists("geo_bib", $_REQUEST))	{
			unset($_SESSION["selection_bib"]);
			$zone = array_isset("geo_zone", $_REQUEST) ? $_REQUEST["geo_zone"] : 0;
			$bib = array_isset("geo_bib", $_REQUEST) ? $_REQUEST["geo_bib"] : 0;

			if($zone and $zone != "reset")	{
				$message="le territoire de : " .utf8_decode(fetchOne("select LIBELLE From bib_c_zone Where ID_ZONE='$zone'"));
				$data=fetchAll("select ID_SITE from bib_c_site where ID_ZONE='$zone'");
				$id_bibs="";
				for($i=0;$i < count($data); $i++)
				{
					if($id_bibs) $id_bibs.=",";
					$id_bibs.=$data[$i]["ID_SITE"];
				}
			}

			if($bib) {
				$id_bibs=$bib;
				$message="la biblioth&egrave;que : ".utf8_decode(fetchOne("select LIBELLE From bib_c_site Where ID_SITE='$bib'"));
			}
		}

		// Comptage des notices
		if (array_key_exists("selection_bib", $_SESSION)) extract($_SESSION["selection_bib"]);
		if(!isset($message)) $message="toutes les biblioth&egrave;ques du r&eacute;seau";
		if(!isset($html)) $message="La recherche s'effectue dans $message.";
		if(!isset($nb_notices)) $nb_notices=$this->getComptage($zone,$bib);
		$html='<div style="width:100%">';
		$html.='<h2>'.utf8_encode($message).'</h2>';
		$html.='<a href="'.BASE_URL.'/bib/selection" >Chercher dans les biblioth&egrave;ques de votre choix</a></div>';
		$html=$html;

		// Valorisation de la session
		$_SESSION["selection_bib"]=compact("message","nb_notices","html","id_bibs");
	}

//------------------------------------------------------------------------------------------------------
// Comptage par id geographique et stockage dans la session
//------------------------------------------------------------------------------------------------------
	private function getComptage($zone,$bib) {
		// Clef session et requete de comptage
		if($zone=="reset") $zone="";
		if($zone)	{
			$clef="z".$zone;
			$req="select count(*) from exemplaires where id_bib in(select ID_SITE from bib_c_site where ID_ZONE='$zone')";
		}
		elseif($bib)	{
			$clef="b".$bib;
			$req="select count(*) from exemplaires where id_bib='$bib'";
		}
		else	{
			$clef="all";
			$req="select count(*) from exemplaires";
		}

		// Lancer le comptage
		if (!array_key_exists("nombre_geo", $_SESSION)) 
			$_SESSION["nombre_geo"] = array();
		if(!array_key_exists($clef, $_SESSION["nombre_geo"])) 
			$_SESSION["nombre_geo"][$clef]=fetchOne($req);
		return number_format($_SESSION["nombre_geo"][$clef], 0, '', ' ');
	}
}