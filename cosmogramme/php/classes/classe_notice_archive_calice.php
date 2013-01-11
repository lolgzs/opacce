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
// CLASSE NOTICE POUR LES ARCHIVES DE CALICE68
//////////////////////////////////////////////////////////////////////////////////////

class notice_archive_calice
{
	private $id_profil;								// Id du profil de données pour le fichier chargé
	private $profil;									// Instance du profil pour optimiser
	private $indexation;							// Instance de la classe d'indexation
	private $enreg;										// Enreg notice brut
	private $champs;									// Champs de données découpés
	private $warnings;								// Erreurs non bloquantes
	private $erreur;									// Erreur bloquante

// ----------------------------------------------------------------
// Constructeur 
// ----------------------------------------------------------------
	function __construct()
	{
		require_once("classe_profil_donnees.php");
		require_once("classe_indexation.php");
		require_once("classe_isbn.php");
		require_once("classe_unimarc.php");
		
		$this->profil=new profil_donnees();
		$this->indexation=new indexation();
	}

// ----------------------------------------------------------------
// Initialisation nouvelle notice
// ----------------------------------------------------------------
	public function ouvrirNotice($data,$id_profil)
	{
		// Découper les champs
		unset($this->enreg);
		if(!$data) return false;
		$data=utf8_encode($data);
		$this->enreg=$data;
		$this->champs=explode(chr(9),$data);
		return true;
	}

// ----------------------------------------------------------------
// Rend la structure complète pour l'integration
// ----------------------------------------------------------------
	public function getNoticeIntegration()
	{
		$notice["warnings"]=array();
		$notice["statut"] = 0;
		$notice["id_origine"]=$this->getIdOrigine();
		$notice["type_doc"] = 1;
		
		// Isbn /ean
		$trav=$this->getIsbn();
		if($trav["statut"] == 1) $warnings[]=array("isbn incorrect",$trav["code_brut"]);
		else
		{
			$notice["isbn"] = $trav["isbn"];
			$notice["isbn10"] = $trav["isbn10"];
			$notice["isbn13"] = $trav["isbn13"];
			$notice["ean"]=$trav["ean"];
		}

		// données principales
		$notice["titres"]= $this->getTitres();
		$notice["titre_princ"] =$notice["titres"][0];
		$notice["auteurs"]=$this->getAuteurs();
		$notice["editeur"]=$this->getEditeur();
		$notice["lieu_edition"]=$this->getLieuEdition();
		$notice["collection"]=$this->getCollection();
		$notice["matieres"]=$this->getMatieres();
		$notice["tome_alpha"] = "";
		$notice["annee"] = $this->getAnnee();
		$notice["collation"] = $this->getCollation();
		$notice["notes"] = $this->getNotes();
		$notice["exportable"] = true;

		// clefs d'index
		$notice["alpha_titre"]=$this->indexation->codeAlphaTitre($notice["titre_princ"]." ".$notice["tome_alpha"]);
		$notice["clef_alpha"]=$this->getClefAlpha($notice);
		$notice["clef_oeuvre"]=$this->getClefAlpha($notice,true);
		$notice["alpha_auteur"]=$this->indexation->alphaMaj($notice["auteurs"][0]);

		// unimarc
		$notice["unimarc"] = $this->getUnimarc($notice);
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
		$ex["code_barres"]=trim($this->champs[0]);
		$ex["cote"]=trim($this->champs[8]);
		$ex["activite"]="à consulter sur place";
		$ex["id_origine"]=trim($this->champs[0]);
		$ret["exemplaire"]=$ex;
		
		$statut["nb_ex"]=1;
		$statut["nb_ex_detruits"]=0;
		if(!$ex["code_barres"]) $statut["codes_barres"]=0; else $statut["codes_barres"]=1;
		if(!$ex["cote"]) $statut["cotes"]=0; else $statut["cotes"]=1;
		$ret["statut_exemplaires"]=$statut;

		return $ret;
	}

// ----------------------------------------------------------------
// Id origine
// ----------------------------------------------------------------
	public function getIdOrigine()
	{
		return trim($this->champs[0]);
	}

// ----------------------------------------------------------------
// Titres
// ----------------------------------------------------------------
	public function getTitres()
	{
		$titres[0]=str_replace('"','',$this->champs[2]);
		return $titres;
	}

// ----------------------------------------------------------------
// Auteurs
// ----------------------------------------------------------------
	public function getAuteurs()
	{
		$data=$this->champs[1];
		if(!$data) return false;
		$enregs=explode(" - ",$data);
		foreach($enregs as $enreg)
		{
			$elems=explode(",",$enreg);
			$nom=trim($elems[0]);
			$prenom=trim($elems[1]);
			$auteurs[]=str_replace('"',"",$nom . "|" .$prenom);
		}
		return $auteurs;
	}

// ----------------------------------------------------------------
// Editeur
// ----------------------------------------------------------------
	public function getEditeur()
	{
		return trim($this->champs[4]);
	}

// ----------------------------------------------------------------
// Lieu d'edition
// ----------------------------------------------------------------
	public function getLieuEdition()
	{
		return trim($this->champs[3]);
	}

// ----------------------------------------------------------------
// Collation
// ----------------------------------------------------------------
	public function getCollation()
	{
		return trim($this->champs[6]);
	}

// ----------------------------------------------------------------
// Année d'édition
// ----------------------------------------------------------------
	public function getAnnee()
	{
		$data=$this->champs[5];
		for($i=0; $i < strlen($data); $i++)
		{
			$car=substr($data,$i,1);
			if($car >="0" And $car <= "9") $annee = $annee .$car;
			if(strLen($annee) == 4 ) break;
		}
		if($annee < "1000" or $annee > "2020") $annee="";
		return($annee);
	}

// ----------------------------------------------------------------
// Collection
// ----------------------------------------------------------------
	public function getCollection()
	{
		return trim($this->champs[7]);
	}

// ----------------------------------------------------------------
// Notes
// ----------------------------------------------------------------
	public function getNotes()
	{
		if(trim($this->champs[13])>'') $notes["300"]=trim($this->champs[13]);
		if(trim($this->champs[11])>'') $notes["317"]=trim($this->champs[11]);
		return $notes;
	}

// ----------------------------------------------------------------
// Matieres
// ----------------------------------------------------------------
	public function getMatieres()
	{
		$data=$this->champs[9];
		if(!$data) return false;
		$enregs=explode("~",$data);
		foreach($enregs as $enreg)
		{
			if(trim($enreg)) $matieres[]=str_replace('"','',trim($enreg));
		}
		return $matieres;
	}

// ----------------------------------------------------------------
// Isbn
// ----------------------------------------------------------------
	public function getIsbn()
	{
		// Recup du premier
		$isbn=trim($this->champs[14]);
		if($isbn)
		{
			$oIsbn=new class_isbn($isbn);
			$isbn=$oIsbn->getAll();
		}
		return $isbn;
	}

// ----------------------------------------------------------------
// Unimarc
// ----------------------------------------------------------------
	public function getUnimarc($notice)
	{
		$notice_sgbd=new notice_unimarc();
		$zone_100=rendDate($this->champs[12],4)."a|||||||||||y0frey0103####ba";
		$notice_sgbd->setNotice("");
		$notice_sgbd->set_dt("m");
		$notice_sgbd->set_bl("5");
		$notice_sgbd->add_field("001","","".$notice["id_origine"]);
		if($notice["isbn"]) $notice_sgbd->add_field("010","","a".$notice["isbn"]);
		$notice_sgbd->add_field("100","  ","a".$zone_100);
		$notice_sgbd->add_field("200","1 ","a".$notice["titre_princ"]);
		if($notice["lieu_edition"]) $notice_sgbd->add_field("210","1 ","a".$notice["lieu_edition"]);
		if($notice["editeur"]) $notice_sgbd->add_field("210","1 ","c".$notice["editeur"]);
		if($notice["annee"]) $notice_sgbd->add_field("210","1 ","d".$notice["annee"]);
		if($notice["collation"]) $notice_sgbd->add_field("215","  ","a".$notice["collation"]);
		if($notice["collection"]) $notice_sgbd->add_field("225","  ","a".$notice["collection"]);
		if($notice["notes"])
		{
			foreach($notice["notes"] as $key=>$valeur)
			{
				$notice_sgbd->add_field($key,"1 ","a".$valeur);
			}
		}
		if($notice["matieres"])
		{
			foreach($notice["matieres"] as $matiere)
			{
				$notice_sgbd->add_field("610","1 ","a".trim($matiere));
			}
		}
		if($notice["auteurs"])
		{
			foreach($notice["auteurs"] as $auteur)
			{
				if(!trim($auteur)) continue;
				$elems=explode("|",$auteur);
				$notice_sgbd->add_field("700","1 ","a".trim($elems[0]." ".trim($elems[1])));
			}
		}
		$unimarc=$notice_sgbd->update();
		return $unimarc;
	}

// ----------------------------------------------------------------
// Rend la clef alpha
// ----------------------------------------------------------------
	public function getClefAlpha($notice,$oeuvre=false)
	{
		$type_doc=$notice["type_doc"];
		$titre=$notice["titre_princ"];
		$complement_titre="";
		$auteur=$notice["auteurs"][0];
		$editeur=$notice["editeur"];
		$annee=$notice["annee"];
		$tome=$notice["tome_alpha"];
		if($oeuvre==true) $clef_alpha=$this->indexation->getClefOeuvre($titre,$complement_titre,$auteur,$tome);
		else $clef_alpha=$this->indexation->getClefAlpha($type_doc,$titre,$complement_titre,$auteur,$tome,$editeur,$annee);
		return $clef_alpha;
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