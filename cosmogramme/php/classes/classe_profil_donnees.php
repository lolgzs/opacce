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
//////////////////////////////////////////////////////////////////////////////////////
// PROFILS DE DONNEES POUR LES IMPORTS
//////////////////////////////////////////////////////////////////////////////////////

class profil_donnees
{
	private $id_profil;									// Id sgbd
	private $libelle;										// Libellé du profil
	private $rejet_periodiques;					// Rejet des periodiques dans les imports
	private $id_article_periodique;			// Mode de reconnaissance pour les articles de periodiques
	private $accents=0;									// Types de caractères accentués
	private $type_fichier=0;						// Type de fichier à parser (variable type_fichier)
	private $format;										// Format du fichier à parser (variable import_format)
	private $attributs;									// Bloc de donnees associe au format
	private $varTypeDoc;								// Liste des types de docs définie dans les variables

//---------------------------------------------------------------------------------
// Constructeur lit la variable types_doc et la decompose
//---------------------------------------------------------------------------------	
	function __construct()
	{
		$this->varTypeDoc=getCodifsVariable("types_docs");
	}
//---------------------------------------------------------------------------------
// Lit un profil et le decompresse
//---------------------------------------------------------------------------------
	public function lire($id_profil)
	{
		// Profils d'homogeneisation : -1= isbn -2=ean
		if($id_profil < 0)
		{
			$this->getProfilStandard($id_profil);
			return $id_profil;
		}
		// lire
		global $sql;
		$data=$sql->fetchEnreg("Select * from profil_donnees where id_profil=$id_profil");
		// si rien on initialise la structure
		if(!$data)
		{
			// prendre l'unimarc standard si il existe
			$test=$sql->fetchOne("select id_profil from profil_donnees where id_profil=1");
			if($test == 1) 
			{
				$this->lire(1);
				$this->id_profil=0;
				$this->libelle="** nouveau profil **";
				return 0;
			} 
			// Sinon on initialise a vide
			$this->id_profil=0;
			$this->libelle="** nouveau profil **";
			$this->accents=0;
			$this->rejet_periodiques=0;
			$this->id_article_periodique=0;
			$this->type_fichier=0;
			$this->format=0;
			$this->attributs=array();

			// Init structure unimarc
			foreach($this->varTypeDoc as $td)
			{
				$td["label"]=array();
				$td["zone_995"]=array();
				$this->attributs[0]["type_doc"][]=$td;
			}
		}
		else
		{
			$this->id_profil=$id_profil;
			$this->libelle=$data["libelle"];
			$this->accents=$data["accents"];
			$this->rejet_periodiques=$data["rejet_periodiques"];
			$this->id_article_periodique=$data["id_article_periodique"];
			$this->type_fichier=$data["type_fichier"];
			$this->format=$data["format"];
			$this->attributs=unserialize($data["attributs"]);

			// Decompacter et consolider les types de docs 
			$td=$this->attributs[0]["type_doc"];
			for($i=0;$i < count($this->varTypeDoc); $i++)
			{
				$this->attributs[0]["type_doc"][$i]["code"]=$this->varTypeDoc[$i]["code"];
				$this->attributs[0]["type_doc"][$i]["libelle"]=$this->varTypeDoc[$i]["libelle"];
				$this->attributs[0]["type_doc"][$i]["label"] = array();
				$this->attributs[0]["type_doc"][$i]["zone_995"] = array();
				for($j=0; $j < count($td); $j++)
				{
					if($td[$j]["code"]==$this->varTypeDoc[$i]["code"])
					{
						$this->attributs[0]["type_doc"][$i]["label"]=explode(";",$td[$j]["label"]);
						$this->attributs[0]["type_doc"][$i]["zone_995"]=explode(";",$td[$j]["zone_995"]);
						break;
					}
				}
			}

			// decompacter et consolider champs xml
			$xml=$this->attributs[5]["xml_champs_abonne"];
			$champs=getCodifsVariable("champs_abonne");
			foreach($champs as $champ)
			{
				$code=$champ["code"];
				if($code !="NULL" and !isset($this->attributs[5]["xml_champs_abonne"][$code]))$this->attributs[5]["xml_champs_abonne"][$code]="";
			}
		}
		return $this->id_profil;
	}
//---------------------------------------------------------------------------------
// Renvoie toute la structure du profil
//---------------------------------------------------------------------------------
	public function getProfil($id_profil)
	{
		$this->lire($id_profil);
		$profil["id_profil"]=$this->id_profil;
		$profil["libelle"]=$this->libelle;
		$profil["accents"]=$this->accents;
		$profil["rejet_periodiques"]=$this->rejet_periodiques;
		$profil["id_article_periodique"]=$this->id_article_periodique;
		$profil["type_fichier"]=$this->type_fichier;
		$profil["format"]=$this->format;
		$profil["attributs"]=$this->attributs;
		if(!$profil["attributs"][0]["champ_cote"]) $profil["attributs"][0]["champ_cote"]="k";
		return $profil;
	}
//---------------------------------------------------------------------------------
// Profils standard réhomogénéisations : -1: panier d'isbn -2:panier d'ean 
//---------------------------------------------------------------------------------
	private function getProfilStandard($id_profil)
	{
		$this->id_profil=$id_profil;
		$this->accents=1;
		$this->rejet_periodiques=0;
		$this->id_article_periodique=0;
		$this->type_fichier=0;
		$this->format=1;
		if($id_profil == -1)
		{
			$this->libelle="Homogénéisation d'isbn";
			$this->attributs[1]["champs"]="isbn";
		}
		else
		{
			$this->libelle="Homogénéisation d'ean";
			$this->attributs[1]["champs"]="ean";
		}
	}
//---------------------------------------------------------------------------------
// Rend un combo avec la liste des profils
//---------------------------------------------------------------------------------
	public function getCombo($valeur)
	{
		global $sql;
		$data=$sql->fetchAll("Select id_profil,libelle from profil_donnees");
		$combo='<select name="profil">';
		for($i=0; $i<count($data); $i++)
		{
			$lig=$data[$i];
			if($valeur==$lig["id_profil"]) $selected=" selected"; else $selected="";
			$combo.='<option value="'.$lig["id_profil"].'"'.$selected.'>'.$lig["libelle"].'</option>';
		}
		$combo.='</select>';
		return $combo;
	}
//---------------------------------------------------------------------------------
// Ecrire
//---------------------------------------------------------------------------------
	public function ecrire($id_profil,$libelle,$accents,$rejet_periodiques,$type_fichier,$format,$attributs,$id_article_periodique)
	{
		//tracedebug(1,$attributs,true);
		if(!trim($libelle)) return false;
		global $sql;
		$attributs=serialize($attributs);
		$data=compact("id_profil","libelle","accents","rejet_periodiques","type_fichier","format","attributs","id_article_periodique");
		if( $id_profil == 0 ) $sql->insert("profil_donnees", $data);
		else $sql->update("Update profil_donnees set @SET@ Where id_profil ='$id_profil'",$data);
	}

//---------------------------------------------------------------------------------
// Analyse d'un label et zones 995 pour determiner un type de doc
//---------------------------------------------------------------------------------
	public function getTypeDoc($label,$z995,$z995p)
	{
		// Si 995$p est à p c'est un periodique
		if(strToUpper(trim($z995p[0]))=="P")
		{
			$typeDoc["code"]=2;
			$typeDoc["libelle"]="Périodiques";
			return $typeDoc;
		}

		// article de périodique
		if($this->isArticlePeriodique($label))
		{
			$typeDoc["code"]=100;
			$typeDoc["libelle"]="article de périodique";
			return $typeDoc;
		}

		// Chercher le type de doc
		for($i=0; $i < count($z995); $i++) $z995[$i]=strtolower($z995[$i]);
		foreach($this->attributs[0]["type_doc"] as $td)
		{
			// label
			for($i=0; $i < count($td["label"]); $i++)
			{
				$item=$td["label"][$i];
				if( strLeft($label,strlen($item)) == $item and $item > "") 
				{ 
					$typeDoc["code"]=$td["code"];
					$typeDoc["libelle"]=$td["libelle"]; 
					break; 
				}
			}
			// Zone 995
			for($i=0; $i < count($td["zone_995"]); $i++)
			{
				$item=strtolower($td["zone_995"][$i]);
				for($j=0; $j<count($z995); $j++)
				{
					if(strLeft($z995[$j],strlen($item)) == $item and $item > "") 
					{ 
						$typeDoc["code"]=$td["code"];
						$typeDoc["libelle"]=$td["libelle"]; 
						break; 
					}
				}
			}
		}
		return $typeDoc;
	}

//---------------------------------------------------------------------------------
// rend les infos d'un fichier a itegrer
//---------------------------------------------------------------------------------
	public function getInfosFichierIntegration($id_integration)
	{
		// enregistrement integration
		if(!$id_integration) return false;
		$data=fetchEnreg("select * from integrations where id=$id_integration");
		if(!$data) return false;

		// infos integration
		$path=getVariable("integration_path");
		$ret["fichier"]=$data["fichier"];
		if(file_exists($path.$ret["fichier"]))
		{
			$ret["taille"]=(int)filesize($path.$ret["fichier"])/1024;
			$ret["taille"]=number_format($ret["taille"],0,","," ")." ko";
			$ret["taille"]=str_replace(" ","&nbsp;",$ret["taille"]);
		}
		else $ret["taille"]="?";

		// type du fichier
		$type_fic=fetchOne("select type_fichier from profil_donnees where id_profil=".$data["profil"]);
		$ret["type_fichier"]=getLibCodifVariable("type_fichier",$type_fic);

		// retour
		return $ret;
	}

//---------------------------------------------------------------------------------
// Analyse article de périodique
//---------------------------------------------------------------------------------
	public function isArticlePeriodique($label)
	{
		switch($this->id_article_periodique)
		{
			case 1: if($label=="aa") return true; // pergame
			case 2: if($label=="aa") return true; // opsys indexpresse
			default: return false;
		}
	}
}
?>