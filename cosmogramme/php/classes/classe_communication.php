<?PHP
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
////////////////////////////////////////////////////////////////////////
// COMMUNICATION : envoi et reception des donnees du service
////////////////////////////////////////////////////////////////////////

class communication
{
// ----------------------------------------------------------------
// Get clef de securite
// ----------------------------------------------------------------
	static function getClefSecurite()
	{
		$clef="IMG".date("DxzxYxM")."VIG";
		$clef=md5($clef);
		return $clef;
	}

// ---------------------------------------------------	
// Controle Clef se securite
// ---------------------------------------------------	
	function controleClefSecurite()
	{
		$clef="IMG".date("DxzxYxM")."VIG";
		$clef=md5($clef);
		if($_REQUEST["src"] != $clef) erreurService("Clef d'accès incorrecte");
	}

// ----------------------------------------------------------------
// Envoi des données
// ----------------------------------------------------------------
	static function envoiDonnees($statut,$data)
	{
		$sep = chr(9);
		if(gettype($data) != "array") $data["data"]=$data;
		print("statut=".$statut.$sep);
		foreach($data as $clef => $valeur)
		{
			print($clef."=".$valeur.$sep);
		}
		exit;
	}

// ----------------------------------------------------------------
// Appeler un service et renvoi des données
// ----------------------------------------------------------------
	static function runService($service,$args,$format="tab")
	{
		// Constituer l'url
		$url=URL_SERVICE."?action=".$service."&src=".communication::getClefSecurite();
		if($args)	foreach($args as $clef => $valeur) $url.="&".$clef."=".$valeur;

		// Lancer la requete
		require_once("classe_http_request.php");
		$http=new HTTPRequest($url);
		$response=$http->DownloadToString();

		// format tab
		if($format=="tab")
		{
			$sep = chr(9);
			if(strpos($response,chr(9)) === false)
			{
				$ret["statut"]="ERREUR";
				if($response) $ret["erreur"]=$response;
				else $ret["erreur"]="erreur http request";
				return $ret;
			}

			// Decouper la reponse
			$items=explode($sep,$response);
			foreach($items as $item)
			{
				$pos=strpos($item,"=");
				if($pos > 0)
				{
					$clef=substr($item,0,$pos);
					$data=substr($item,($pos+1));
					$ret[$clef]=$data;
				}
			}
		}

		// json
		if($format=="json")
		{
			if(!$response) $ret["erreur"]="erreur http request";
			else $ret=json_decode($response,true);
		}
		return $ret;
	}

// ----------------------------------------------------------------
// Rend le libelle du statut z39.50
// ----------------------------------------------------------------
	static function getLibelleStatutZ3950($statut)
	{
		$lib=array("Erreur","notice non trouvée","Trouvée sur le serveur","trouvée dans le cache");
		return $lib[$statut];
	}

}

?>