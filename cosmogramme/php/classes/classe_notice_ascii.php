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
// CLASSE NOTICE AU FORMAT ASCII 
//////////////////////////////////////////////////////////////////////////////////////

class notice_ascii
{
	private $id_profil;								// Id du profil de données pour le fichier chargé
	private $profil;									// Instance du profil pour optimiser
	private $indexation;							// Instance de la classe d'indexation
	private $champs;									// Champs du fichier (lus dans le profil)
	private $enreg;										// Enreg notice brut
	private $warnings;								// Erreurs non bloquantes
	private $erreur;									// Erreur bloquante

// ----------------------------------------------------------------
// Constructeur 
// ----------------------------------------------------------------
	function __construct()
	{
		require_once("classe_profil_donnees.php");
		require_once("classe_indexation.php");
		
		$this->profil=new profil_donnees();
		$this->indexation=new indexation();
	}

// ----------------------------------------------------------------
// Initialisation nouvelle notice
// ----------------------------------------------------------------
	public function ouvrirNotice($data,$id_profil)
	{
		// Traitement du profil
		if($this->id_profil != $id_profil)
		{ 
			$this->id_profil=$id_profil;
			$profil=$this->profil->getProfil($id_profil);
			$format=$profil["format"];
			if(!trim($profil["attributs"][$format]["champs"])) {$this->erreur="il n'y a aucun champ défini dans le profil de données"; return false;}
			$this->champs=explode(";",$profil["attributs"][$format]["champs"]);
		}
		unset($this->enreg);
		$this->enreg["data"]=$data;
		$data=explode(chr(9),$data);
		$i=0;
		foreach($this->champs as $champ)
		{
			if($champ!= "NULL") $this->enreg[$champ]=addslashes(trim($data[$i]));
			$i++;
		}
		return true;
	}

// ----------------------------------------------------------------
// Rend l'enregistrement de base découpé par champs
// ----------------------------------------------------------------
	public function getEnreg()
	{
		return $this->enreg;
	}

// ----------------------------------------------------------------
// Rend la structure complète pour l'integration
// ----------------------------------------------------------------
	public function getNoticeIntegration()
	{
		$notice["statut"] = 0;
		$notice["type_doc"] = $this->enreg["type_doc"];
		$notice["isbn"] ="";
		$notice["isbn10"] ="";
		$notice["isbn13"] ="";
		$notice["ean"] = "";
		$notice["titre_princ"] = $this->enreg["titre_princ"];
		$notice["titres"]="";
		$notice["auteurs"]=$this->enreg["auteurs"];
		$notice["editeur"]=$this->enreg["editeur"];
		$notice["collection"]=$this->enreg["collection"];
		$notice["matieres"]=$this->enreg["matieres"];
		$notice["tome_alpha"] = $this->enreg["tome_alpha"];
		$notice["annee"] = $this->enreg["annee"];
		$notice["exportable"] = true;
		$notice["unimarc"] = "";
		$notice["data"] = $this->enreg["data"];
		$notice["warnings"]=array();
		
		if($this->enreg["isbn"])
		{ 
			$ret=$this->getIsbn($this->enreg["isbn"]);
			$notice["isbn"]=$ret["isbn"];
			$notice["isbn10"]=$ret["isbn10"];
			$notice["isbn13"]=$ret["isbn13"];
		}
		elseif($this->enreg["ean"]) $notice["ean"]=$this->getEan($this->enreg["ean"]);
		
		$ex=$this->getExemplaire();
		$notice["statut_exemplaires"] = $ex["statut_exemplaires"];
		$notice["exemplaires"][0] = $ex["exemplaire"];
		return $notice;
	}
// ----------------------------------------------------------------
// Exemplaire
// ----------------------------------------------------------------
	public function getExemplaire()
	{
		$ex["code_barres"]=strleft(trim($this->enreg["code_barres"]),20);
		$ex["cote"]=$this->enreg["cote"];
		$ex["activite"]=$this->enreg["activite"];
		if( !$ex["activite"] ) $ex["activite"]="non disponible";
		$ret["exemplaire"]=$ex;
		
		$statut["nb_ex"]=1;
		$statut["nb_ex_detruits"]=0;
		if(!$ex["code_barres"]) $statut["codes_barres"]=0; else $statut["codes_barres"]=1;
		if(!$ex["cote"]) $statut["cotes"]=0; else $statut["cotes"]=1;
		$ret["statut_exemplaires"]=$statut;

		return $ret;
	}
// ----------------------------------------------------------------
//  Verifie l'Isbn
// ----------------------------------------------------------------
	public function getIsbn($isbn)
	{
		$isbn=trim($isbn);
		if($isbn) 
		{
			$oIsbn=new class_isbn($isbn);
			$ret=$oIsbn->getAll();
			$notice["isbn"]=$ret["isbn"];
			$notice["isbn10"]=$ret["isbn10"];
			$notice["isbn13"]=$ret["isbn13"];
		}
		return $notice;
	}
// ----------------------------------------------------------------
//  Verifie l'ean
// ----------------------------------------------------------------
	public function getEan($ean)
	{
		$ean=trim($ean);
		if($ean) 
		{
			$oIsbn=new class_isbn($ean);
			$ean=$oIsbn->getAll();
			$ean=$ean["ean"];
		}
		return $ean;
	}
// ----------------------------------------------------------------
// Rend la derniere erreur bloquante
// ----------------------------------------------------------------
	public function getLastError()
	{
		return $this->erreur;
	}
}
?>