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
// CLASSE UNIMARC (Surcharge la classe iso2709)
//////////////////////////////////////////////////////////////////////////////////////

require_once("classe_profil_donnees.php");
require_once("classe_iso2709.php");
require_once("classe_dewey.php");
require_once("classe_pcdm4.php");
require_once("classe_indexation.php");
require_once("classe_isbn.php");

class notice_unimarc extends iso2709_record
{
	private $id_profil;									// Id du profil de données pour le fichier chargé
	private $profil_unimarc;						// Instance du profil unimarc pour optimiser
	private $type_doc_force;						// Type de document forcé dans maj_auto
	private $indexation;								// Instance de la classe d'indexation
	protected $profil;									// Structure des valeurs du profil en cours
	private $copyright;									// Mots clefs pour les notices non libres de droits (801$b)
	private $sigb;											// Pour traitements specifiques
	private $champs_forces;							// Liste des champs forcés
	private $ean345;										// Reconnaissance des ean par la zone 345$b
	private $regles_sections_genres;		// Règles de reconnaissance des sections et des genres
	private $id_genre_documentaire;			// Identifiant pour le genre "documentaire"
	private $controle_codes_barres;     // Exception de filtrage des codes-barres

// ----------------------------------------------------------------
// Constructeur 
// ----------------------------------------------------------------
	function __construct()
	{
		$this->profil_unimarc=new profil_donnees();
		$this->indexation=new indexation();
		$data=getVariable("non_exportable");
		$this->copyright=explode(";",$data);
		$this->controle_codes_barres=getVariable("controle_codes_barres");
		$data=getVariable("champs_sup");
		$this->champs_forces=explode(";",$data);
		$this->ean345=getVariable("ean_345");

		// Règles sections, genres, emplacements
		$this->regles_sections_genres=$this->extractRegles();
		parent::__construct();
	}

// ----------------------------------------------------------------
// Initialisation nouvelle notice
// ----------------------------------------------------------------
	public function ouvrirNotice($data,$id_profil,$sigb=0,$type_doc_force="")
	{
		$this->type_doc_force["label"]=$type_doc_force;
		switch($type_doc_force)
		{
			case "a":$this->type_doc_force["code"]="1"; break;
			case "j":$this->type_doc_force["code"]="3"; break;
			case "g":$this->type_doc_force["code"]="4"; break;
			case "l":$this->type_doc_force["code"]="5"; break;
			case "c":$this->type_doc_force["code"]="6"; break;
			case "f":$this->type_doc_force["code"]="7"; break;
		}
		$this->sigb=$sigb;
		if($this->id_profil !== $id_profil)
		{ 
			$this->id_profil=$id_profil;
			if($id_profil==0)
			{
				$this->profil=$this->profil_unimarc->getProfil(1);
				$this->profil["accents"]=0;
			}
			else $this->profil=$this->profil_unimarc->getProfil($id_profil);
		}

		$this->setNotice($data,$this->profil["accents"]);
		return true;
	}
// ----------------------------------------------------------------
// Rend la structure complète pour l'integration
// ----------------------------------------------------------------
	public function getNoticeIntegration()
	{
		// type de doc
		$type_doc=$this->getTypeDoc(true);
		if($type_doc["code"]==100) return $this->getNoticeIntegrationArticlePeriodique();
		if($type_doc["code"]==2) $notice["articles_periodiques"]=$this->getIdArticlesPeriodiques();

		// exemplaires
		$ex=$this->getExemplaires();
		$warnings=$ex["warnings"];
		
		// Isbn /ean
		$trav=$this->getIsbn();
		if($trav["statut"] == 1) $warnings[]=array("isbn incorrect",$trav["code_brut"]);
		$isbn=$trav["isbn"];
		$ean=$trav["ean"];
		if(!$isbn and !$ean)
		{
			$trav=$this->getEan();
			if($trav["statut"] == 1) $warnings[]=array("ean incorrect",$trav["code_brut"]);
			$isbn=$trav["isbn"];
			$ean=$trav["ean"];
		}
		// Virer ISBN si autre que livre
		if($type_doc["code"] != 1)
		{
			$trav["isbn10"]="";
			$trav["isbn13"]="";
			$isbn="";
		}
				
		// Structure notice
		$notice["statut"] = $this->getStatut();
		$notice["isbn"] = $isbn;
		$notice["isbn10"] = $trav["isbn10"];
		$notice["isbn13"] = $trav["isbn13"];
		$notice["ean"] = $ean;
		$notice["id_origine"]=$this->getIdOrigine();
		$notice["clef_alpha"]=$this->getClefAlpha();
		$notice["clef_oeuvre"]=$this->getClefAlpha(true);
		$notice["clef_chapeau"]=$this->getClefChapeau();
		$notice["titre_princ"] = $this->getTitrePrincipal();
		$notice["tome_alpha"] = $this->getTome();
		$notice["alpha_titre"]=$this->indexation->codeAlphaTitre($notice["titre_princ"]." ".$notice["tome_alpha"]);
		$notice["id_commerciale"]=$this->getIdCommerciale($notice["alpha_titre"]);
		$notice["titres"] = $this->getTitres();
		$notice["auteurs"] = $this->getAuteurs();
		// si pas d'auteur on prend le 200$f
		if(!$notice["auteurs"]) $notice["200_f"]=$this->get200f();
		$notice["alpha_auteur"]=$this->indexation->alphaMaj($notice["auteurs"][0]);
		$notice["editeur"] = $this->getEditeur();
		$notice["collection"] = $this->getCollection();
		$notice["matieres"] = $this->getMatieres();
		$notice["annee"] = $this->getAnnee();
		$notice["type_doc"] = $type_doc["code"];
		$notice["infos_type_doc"] = $type_doc;
		$notice["exportable"] = $this->getExportable();
		$notice["dewey"]=$this->getDewey();
		$notice["pcdm4"]=$this->getPcdm4();
		$notice["cote"]=$this->getCote();
		$notice["langues"]=$this->getLangues();
		$notice["champs_forces"] = $this->getChampsForces();
		if($this->sigb==1 or $this->sigb==13) $notice["interet"]=$this->getCentreInteret(); // Centre d'interet Pergame et Nanook
		$notice["statut_exemplaires"] = $ex["statut_exemplaires"];
		$notice["exemplaires"] = $ex["exemplaires"];

		// Analyse sections, genres et emplacements
		if($notice["dewey"]) $notice["genre"]=$this->id_genre_documentaire;
		$ret=$this->getSectionGenre($notice["genre"]);

		if($notice["statut_exemplaires"]["nb_ex"]>0)
		{
			for($i=0; $i <count($notice["exemplaires"]); $i++)
			{
				$exemplaire=$notice["exemplaires"][$i];
				if($exemplaire["section"]) $notice["sections"][]=$exemplaire["section"];
				else $notice["exemplaires"][$i]["section"]=$ret["section"];

				if($exemplaire["genre"]) $notice["genres"][]=$exemplaire["genre"];
				else $notice["exemplaires"][$i]["genre"]=$ret["genre"];

				if($exemplaire["emplacement"]) $notice["emplacements"][]=$exemplaire["emplacement"];
				else $notice["exemplaires"][$i]["emplacement"]=$ret["emplacement"];
				
				if(!$notice["cote"] and $exemplaire["cote"]) $notice["cote"] = $exemplaire["cote"];
			}
		}
		
		// On supprime les exemplaires
		$this->delete_field("995");
		if($this->profil["attributs"][0]["champ_code_barres"] == "997") $this->delete_field("997"); // astrolabe
		if($this->profil["attributs"][0]["champ_code_barres"] == "852") $this->delete_field("852"); // Moulins
		$notice["unimarc"] = $this->update();

		// Warnings
		$notice["warnings"] = $warnings;

		return $notice;
	}

// ----------------------------------------------------------------
// Notice de periodique
// ----------------------------------------------------------------
	public function getNoticeIntegrationArticlePeriodique()
	{
		// type de doc
		$notice["type_doc"] = 100;
		$notice["infos_type_doc"] = array("code"=>100,libelle=>"article de périodique");

		// statut
		$notice["statut"] = $this->getStatut();
		$notice["clef_chapeau"]="";
		$notice["clef_numero"]="";
		$notice["clef_unimarc"]="";
		$notice["titre_numero"]="";
		$notice["info_id"]="";

		// identifiants
		switch($this->profil["id_article_periodique"])
		{
			// pergame
			case 1:
				$titre=$this->get_subfield("461","t");
				$numero=$this->get_subfield("461","v");
				$notice["clef_chapeau"]=$this->indexation->codeAlphaTitre($titre[0]);
				$notice["clef_numero"]=$this->indexation->alphaMaj($numero[0]);
				$notice["titre_numero"]=$titre[0]." n° ".$numero[0];
				break;
			
			// opsys indexpresse
			case 2:
				$id=$this->get_subfield("001");
				$notice["clef_unimarc"]=$id[0];
				$notice["info_id"]=$id[0];
				break;
		}

		// clef titre de l'article
		$titre=$this->getTitrePrincipal();
		$notice["titre_princ"]=$titre;
		$titre=$this->indexation->codeAlphaTitre($titre);
		$notice["clef_article"]=substr($titre,0,20);

		// unimarc
		$notice["unimarc"]=$this->update();
		return $notice;
	}

// ----------------------------------------------------------------------------
// Identifiants des articles de periodiques a partir de la notice du numero
// ----------------------------------------------------------------------------
	private function getIdArticlesPeriodiques()
	{
		// opsys indexpresse
		if($this->profil["id_article_periodique"] == 2)
		{
			$data=$this->get_subfield("462","3");
			if(!trim($data[0])) return false;
			
			$ret["articles"]=$data;
			$chapeau=$this->get_subfield("461","t");
			$ret["clef_chapeau"]=$this->indexation->codeAlphaTitre($chapeau[0]);
			$numero=$this->get_subfield("461","v");
			$ret["clef_numero"]=$this->indexation->alphaMaj($numero[0]);
		}
		if(!$ret["clef_chapeau"] or !$ret["clef_numero"]) return false;
		return $ret;
	}

// ----------------------------------------------------------------
// Titre principal
// ----------------------------------------------------------------
	public function getTitrePrincipal()
	{
		$titre=$this->get_subfield("200","a");
		$titre=trim($titre[0]);
		$titre=$this->filtreTitre($titre);
		return $titre;
	}
	
// ----------------------------------------------------------------
// Exemplaires
// ----------------------------------------------------------------
	public function getExemplaires()
	{
		// Reperage des champs exemplaires
		$champ_code_barres=$this->profil["attributs"][0]["champ_code_barres"];
		$champ_cote=$this->profil["attributs"][0]["champ_cote"];
		if(!$champ_cote) $champ_cote="k";
		$champ_section=$this->profil["attributs"][0]["champ_section"];
		if($champ_section=="#") $champ_section="0";
		$champ_genre=$this->profil["attributs"][0]["champ_genre"];
		if($champ_genre=="#") $champ_genre="0";
		$champ_emplacement=$this->profil["attributs"][0]["champ_emplacement"];
		if($champ_emplacement=="#") $champ_emplacement="0";
		$champ_annexe=$this->profil["attributs"][0]["champ_annexe"];
		if($champ_annexe=="#") $champ_annexe="0";
		$champs_nouveaute=$this->profil["attributs"][4];
		if($champ_code_barres == "997") return $this->getExemplaires997(); // Astrolabe
		if($champ_code_barres == "852") return $this->getExemplaires852(); // Moulins
		if($champ_code_barres == "999") return $this->getExemplaires999(); // La plaine centrale

		// Decoupage de la zone 995
		$exemplaires=$data=$this->get_subfield("995");
		$ret["warnings"]=array();
		for($i=0; $i < count($exemplaires); $i++)
		{
			unset($ex);
			$ex["activite"]="peut être prêté";
			$champs=$this->decoupe_bloc_champ($exemplaires[$i]);
			foreach($champs as $champ)
			{
				if(strpos("1abcfkyz",$champ["code"]) === false) $ex["zone995"].=$champ["code"]."=".$champ["valeur"]." ";

				// Code-barres
				if($champ["code"] == $champ_code_barres)
				{
					if(!trim($champ["valeur"])) $ret["warnings"][]=array("code-barres vide","");
					elseif(!$ex["code_barres"])
					{
						$ex["code_barres"]=$this->filtreCodeBarres($champ["valeur"]);
						if($ex["code_barres"]) $codes_barres=true;
						else $ret["warnings"][]=array("code-barres incorrect",$champ["valeur"]);
					}
				}
				// Cote
				elseif($champ["code"]==$champ_cote)
				{
					$cotes=true;
					$ex["cote"]=$champ["valeur"];
				}

				// activité
				elseif($champ["code"]=="o")
				{
					if($champ["valeur"]=="d") {$nb_ex_detruits++; $ex["activite"]="d";}
					elseif($champ["valeur"]=="c") $ex["activite"]="à consulter sur place";
				}
				// activité specifique koha
				elseif(($champ["code"]=="3" or $champ["code"]=="2") and $this->sigb=="12")
				{
					if($champ["valeur"]=="1") {$nb_ex_detruits++; $ex["activite"]="d";}
				}

				// Activité Pergame / nanook
				elseif($champ["code"]=="2" and $ex["activite"]!="d")
				{
					$valeurs=str_replace("[","",$champ["valeur"]);
					$elems=explode("]",$valeurs);
					if(trim($elems[4])) $ex["activite"]=$elems[4];
					else if(trim($elems[1])) $ex["activite"]=$elems[1];
				}

				// Champs parametres
				if($champ_section and $champ["code"] == $champ_section) $ex["section"]=$this->getIdCodeExemplaire("section","995",$champ_section,$champ["valeur"]);
				if($champ_genre and $champ["code"] == $champ_genre) $ex["genre"]=$this->getIdCodeExemplaire("genre","995",$champ_genre,$champ["valeur"]);
				if($champ_emplacement and strtolower($champ["code"]) == $champ_emplacement) $ex["emplacement"]=$this->getIdCodeExemplaire("emplacement","995",$champ_emplacement,$champ["valeur"]);
				if($champ_annexe and $champ["code"] == $champ_annexe) $ex["annexe"]=$champ["valeur"];

				// date de nouveauté
				if($champs_nouveaute["zone"]=="995" and $champ["code"]==$champs_nouveaute["champ"])
				{
					$ex["date_nouveaute"]=$this->calculDateNouveaute($champ["valeur"]);
				}
			}
			if($ex["code_barres"]>"")
			{
				$nb_ex++;
				$ret["exemplaires"][]=$ex;
			}
		}
		
		// Mettre des 0 pour les ret vides
		if(!$nb_ex) $statut["nb_ex"]=0; else $statut["nb_ex"]=$nb_ex;
		if(!$nb_ex_detruits) $statut["nb_ex_detruits"]=0; else $statut["nb_ex_detruits"]=$nb_ex_detruits;
		if(!$codes_barres) $statut["codes_barres"]=0; else $statut["codes_barres"]=$codes_barres;
		if(!$cotes) $statut["cotes"]=0; else $statut["cotes"]=$cotes;
		$ret["statut_exemplaires"]=$statut;
		return $ret;
	}
	
// ----------------------------------------------------------------
// Champs exemplaires en 997 (astrolabe)
// ----------------------------------------------------------------
private function getExemplaires997()
{
	$exemplaires=$data=$this->get_subfield("997");
	$ret["warnings"]=array();
	for($i=0; $i < count($exemplaires); $i++)
	{
		unset($ex);
		$ex["activite"]="peut être prêté";
		$champs=$this->decoupe_bloc_champ($exemplaires[$i]);
		foreach($champs as $champ)
		{
			if(strpos("adtghi",$champ["code"]) === false) $ex["zone995"].=$champ["code"]."=".$champ["valeur"]." ";
			// Code-barres
			if($champ["code"] == "a")
			{
				if(!trim($champ["valeur"])) $ret["warnings"][]=array("code-barres vide","");
				elseif(!$ex["code_barres"])
				{
					$ex["code_barres"]=$this->filtreCodeBarres($champ["valeur"]);
					if($ex["code_barres"]) $codes_barres=true;
					else $ret["warnings"][]=array("code-barres incorrect",$champ["valeur"]);
				}
			}
			// Cote
			elseif($champ["code"]=="g")
			{
				$cotes=true;
				$ex["cote"]=$champ["valeur"];
			}
			elseif($champ["code"]=="h") if($champ["valeur"]) $ex["cote"].="-".$champ["valeur"];
			elseif($champ["code"]=="i") if($champ["valeur"]) $ex["cote"].="-".$champ["valeur"];
		}
		if($ex["code_barres"]>"")
		{
			$nb_ex++;
			$ret["exemplaires"][]=$ex;
		}
	}	
	// Mettre des 0 pour les ret vides
	if(!$nb_ex) $statut["nb_ex"]=0; else $statut["nb_ex"]=$nb_ex;
	if(!$nb_ex_detruits) $statut["nb_ex_detruits"]=0; else $statut["nb_ex_detruits"]=$nb_ex_detruits;
	if(!$codes_barres) $statut["codes_barres"]=0; else $statut["codes_barres"]=$codes_barres;
	if(!$cotes) $statut["cotes"]=0; else $statut["cotes"]=$cotes;
	$ret["statut_exemplaires"]=$statut;
	return $ret;
}

// ----------------------------------------------------------------
// Champs exemplaires en 852 (spécifique moulins)
// ----------------------------------------------------------------
private function getExemplaires852()
{
	$exemplaires=$data=$this->get_subfield("852");
	$ret["warnings"]=array();
	for($i=0; $i < count($exemplaires); $i++)
	{
		unset($ex);
		$ex["activite"]="peut être prêté";
		$champs=$this->decoupe_bloc_champ($exemplaires[$i]);
		foreach($champs as $champ)
		{
			if(strpos("abqfgktrnu",$champ["code"]) === false) $ex["zone995"].=$champ["code"]."=".$champ["valeur"]." ";
			// Code-barres
			if($champ["code"] == "g")
			{
				if(!trim($champ["valeur"])) $ret["warnings"][]=array("code-barres vide","");
				elseif(!$ex["code_barres"])
				{
					$ex["code_barres"]=$this->filtreCodeBarres($champ["valeur"]);
					if($ex["code_barres"]) $codes_barres=true;
					else $ret["warnings"][]=array("code-barres incorrect",$champ["valeur"]);
				}
			}
			// Cote
			elseif($champ["code"]=="k")
			{
				$cotes=true;
				$ex["cote"]=$champ["valeur"];
			}
			// Section
			elseif($champ["code"]=="q")
			{
				$section=$this->getIdCodeExemplaire("section","852","q",$champ["valeur"]);
				if($section)
				{
					$enreg=fetchEnreg("select * from codif_section where id_section=$section");
					if($enreg["invisible"]==1)
					{
						$ex["ignore_exemplaire"]=true;
						continue;
					}
				}
				$ex["section"]=$section;
			}

			// Annexe
			elseif($champ["code"]=="a")
			{
				$annexe=$champ["valeur"];
				if($annexe)
				{
					$enreg=fetchEnreg("select * from codif_annexe where code='$annexe'");
					if($enreg["invisible"]==1)
					{
						$ex["ignore_exemplaire"]=true;
						continue;
					}
				}
				$ex["annexe"]=$annexe;
			}

			// exemplaire détruit (activité != 1)
			elseif($champ["code"]=="u")
			{
				if($champ["valeur"] == "1") $ex["activite"]="d";
			}
		}
		
		// ajouter aux exemplaires
		if($ex["code_barres"]>"" and !$ex["ignore_exemplaire"])
		{
			$nb_ex++;
			$ret["exemplaires"][]=$ex;
		}
	}
	// Mettre des 0 pour les ret vides
	if(!$nb_ex) $statut["nb_ex"]=0; else $statut["nb_ex"]=$nb_ex;
	if(!$nb_ex_detruits) $statut["nb_ex_detruits"]=0; else $statut["nb_ex_detruits"]=$nb_ex_detruits;
	if(!$codes_barres) $statut["codes_barres"]=0; else $statut["codes_barres"]=$codes_barres;
	if(!$cotes) $statut["cotes"]=0; else $statut["cotes"]=$cotes;
	$ret["statut_exemplaires"]=$statut;
	return $ret;
}

// ----------------------------------------------------------------
// Champs exemplaires en 999 (spécifique la plaine centrale dynix)
// ----------------------------------------------------------------
private function getExemplaires999()
{
	$champ_annexe=$this->profil["attributs"][0]["champ_annexe"];
	$champ_section=$this->profil["attributs"][0]["champ_section"];
	$champ_emplacement=$this->profil["attributs"][0]["champ_emplacement"];
	$exemplaires=$this->get_subfield("996");
	$ret["warnings"]=array();
	
	// date de nouveaute
	$data=$this->get_subfield("005");
	if($data[0])
	{
		if(substr($data[0],0,2)>'50') $data="19".substr($data[0],0,6);
		else $data="20".substr($data[0],0,6);
		$date_nouveaute=$this->calculDateNouveaute($data);
	}
	
	for($i=0; $i < count($exemplaires); $i++)
	{
		unset($ex);
		$ex["activite"]="peut être prêté";
		$champs=$this->decoupe_bloc_champ($exemplaires[$i]);
		foreach($champs as $champ)
		{
			$ex["zone995"].=$champ["code"]."=".$champ["valeur"]." ";
			
			// Code-barres
			if($champ["code"] == "i")
			{
				if(!trim($champ["valeur"])) $ret["warnings"][]=array("code-barres vide","");
				elseif(!$ex["code_barres"])
				{
					$ex["code_barres"]=$this->filtreCodeBarres($champ["valeur"]);
					if($ex["code_barres"]) $codes_barres=true;
					else $ret["warnings"][]=array("code-barres incorrect",$champ["valeur"]);
				}
			}
			// Cote
			elseif($champ["code"]=="a")
			{
				$cotes=true;
				$ex["cote"]=$champ["valeur"];
			}
			// Section
			elseif($champ["code"]==$champ_section)
			{
				$section=$this->getIdCodeExemplaire("section","996",$champ_section,$champ["valeur"]);
				if($section)
				{
					$enreg=fetchEnreg("select * from codif_section where id_section=$section");
					if($enreg["invisible"]==1)
					{
						$ex["ignore_exemplaire"]=true;
						continue;
					}
				}
				$ex["section"]=$section;
			}

			// Annexe
			elseif($champ["code"]==$champ_annexe)
			{
				$annexe=$champ["valeur"];
				if($annexe)
				{
					$enreg=fetchEnreg("select * from codif_annexe where code='$annexe'");
					if($enreg["invisible"]==1)
					{
						$ex["ignore_exemplaire"]=true;
						continue;
					}
				}
				$ex["annexe"]=$annexe;
			}

			// emplacement
			elseif($champ["code"]==$champ_emplacement)
			{
			 $ex["emplacement"]=$this->getIdCodeExemplaire("emplacement","996",$champ_emplacement,$champ["valeur"]);
			 if($ex['emplacement']) $invisible=fetchOne("select ne_pas_afficher from codif_emplacement where id_emplacement=".$ex['emplacement']);
			 else $invisible=0;
			 if($invisible==1) $ex["ignore_exemplaire"]=true;
			}
		}

		
		// date de nouveaute
		$ex["date_nouveaute"]=$date_nouveaute;
		
		// ajouter aux exemplaires
		if($ex["code_barres"]>"" and !$ex["ignore_exemplaire"])
		{
			$nb_ex++;
			$ret["exemplaires"][]=$ex;
		}
	}
	// Mettre des 0 pour les ret vides
	if(!$nb_ex) $statut["nb_ex"]=0; else $statut["nb_ex"]=$nb_ex;
	if(!$nb_ex_detruits) $statut["nb_ex_detruits"]=0; else $statut["nb_ex_detruits"]=$nb_ex_detruits;
	if(!$codes_barres) $statut["codes_barres"]=0; else $statut["codes_barres"]=$codes_barres;
	if(!$cotes) $statut["cotes"]=0; else $statut["cotes"]=$cotes;
	$ret["statut_exemplaires"]=$statut;
	return $ret;
}

// ----------------------------------------------------------------
// Teste un code barres et le renvoie s'il est valide
// ----------------------------------------------------------------
	public function filtreCodeBarres($code_barres)
	{
		if($this->controle_codes_barres==1) return utf8_encode(addslashes($code_barres));
		$cab=trim(strleft($code_barres,20));
		if(is_numeric($code_barres)) return $cab;
		$nb_num=0;
		for($i=0; $i< strlen($cab); $i++)
		{
			if($cab[$i] >="0" and $cab[$i] <="9") $nb_num++;
		}
		if($nb_num < 4) return false;
		else return utf8_encode(addslashes($cab));
	}
	
// ----------------------------------------------------------------
// Id bib origine (champ 001)
// ----------------------------------------------------------------
	public function getIdOrigine()
	{
		$data=$this->get_subfield("001");
		$id=substr(trim($data[0]),0,20);
		return $id;
	}
// ----------------------------------------------------------------
// Isbn
// ----------------------------------------------------------------
	public function getIsbn()
	{
		// Recup du premier
		$data=$this->get_subfield("010","a");
		$isbn=trim($data[0]);
		if($isbn)
		{
			$oIsbn=new class_isbn($isbn);
			$isbn=$oIsbn->getAll();
		}
		//controle ISBN doubles
		$data=$this->get_subfield("010");
		if(count($data)>1)
		{ 
			$warnings[]=array("isbn multiples","");
			$isbn["multiple"]=true;
			$this->delete_field("010");
			$this->add_zone("010",$data[0]);
		}
		if(!is_array($isbn)) $isbn=array();
		return $isbn;
	}
// ----------------------------------------------------------------
// EAN
// ----------------------------------------------------------------
	public function getEan()
	{
		$data=$this->get_subfield("073","a");
		$ean=trim($data[0]);
		if($ean)
		{
			$oIsbn=new class_isbn($ean);
			$ean=$oIsbn->getAll();
		}
		elseif($this->ean345 == 1)
		{
			$data=$this->get_subfield("345","b");
			$code=trim($data[0]);
			if($code) 
			{
				$oIsbn=new class_isbn($code);
				$ean=$oIsbn->getAll();
			}
		}
		if(!$ean)
		{
			$data=$this->get_subfield("071","a");
			$code=trim($data[0]);
			if(strlen($code) == 13 and is_numeric($code))
			{
				$oIsbn=new class_isbn($code);
				$ean=$oIsbn->getAll();
			}
		}
		if(!is_array($ean)) $ean=array();
		return $ean;
	}

// ----------------------------------------------------------------
// Identifiant no commercial
// ----------------------------------------------------------------
	public function getIdCommerciale($clef_alpha)
	{
		$data=$this->get_subfield("071","a");
		$id=trim($data[0]);
		if(strlen($id) == 13 and is_numeric($id)) return ""; // c'est un ean
		// On concatene avec le $b
		$data=$this->get_subfield("071","b");
		$id=trim($data[0]).$id;
		$id=$this->indexation->alphaMaj($id);
		$id=str_replace(" ","",$id);
		if(!$id) return "";
		if(substr($id,0,19) == "REFERENCEEDITORIALE") $id=substr($id,19); 
		if(substr($id,0,18) == "MARQUEINDETERMINEE") $id=substr($id,18);
		// controle s'il y a au moins 2 chiffres
		$nb_digit=0;
		for($i=0; $i < strlen($id); $i++) if( $id[$i] >= "0" and $id[$i] <= "9") $nb_digit++;
		if($nb_digit < 2) return "";
		$clef_alpha=str_replace(" ","",$clef_alpha);
		$id=$id.substr($clef_alpha,0,20);
		return substr($id,0,50);
	}

// ----------------------------------------------------------------
// Identifiant BNF
// ----------------------------------------------------------------
	public function getIdBnf()
	{
		$data=$this->get_subfield("001");
		$id=strToUpper(trim($data[0]));
		if(substr($id,0,5) != "FRBNF") $id= '';
		elseif(is_numeric(substr($id,5,5))== false) $id='';
		return $id;
	}

// ----------------------------------------------------------------
// Rend la clef alpha ou la clef oeuvre
// ----------------------------------------------------------------
	public function getClefAlpha($oeuvre=false)
	{
		$type_doc=$this->getTypeDoc();
		$titre=$this->getTitrePrincipal();
		$complement_titre=$this->getComplementTitre();
		$auteur=$this->getAuteurs(true,true);
		$editeur=$this->getEditeur();
		$annee=$this->getAnnee();
		$tome=$this->getTome();
		if($oeuvre==true) $clef_alpha=$this->indexation->getClefOeuvre($titre,$complement_titre,$auteur,$tome);
		else $clef_alpha=$this->indexation->getClefAlpha($type_doc,$titre,$complement_titre,$auteur,$tome,$editeur,$annee);
		return $clef_alpha;
	}

// ----------------------------------------------------------------
// Rend la clef chapeau
// ----------------------------------------------------------------
	public function getClefChapeau()
	{
		$titre=$this->get_subfield("461","t");
		$titre=trim($titre[0]);
		$clef_chapeau=$this->indexation->codeAlphaTitre($titre);
		return $clef_chapeau;
	}
	
// ----------------------------------------------------------------
// TYPE DE DOCUMENT
// ----------------------------------------------------------------
	public function getTypeDoc($infos=false)
	{
		if($this->type_doc_force["label"] > "") $this->inner_guide["dt"]=$this->type_doc_force["label"];
		$label=$this->inner_guide["dt"].$this->inner_guide["bl"];
		if($this->profil["attributs"][0]["champ_type_doc"] > "")
		{
			if(strlen($this->profil["attributs"][0]["champ_code_barres"])==3) $zone=$this->profil["attributs"][0]["champ_code_barres"];
			else $zone="995";
			$z995r=$this->get_subfield($zone,$this->profil["attributs"][0]["champ_type_doc"]);
		}
		else
		{	
			if($this->profil["attributs"][0]["champ_code_barres"] == "997") $z995r=$this->get_subfield("997","t");
			elseif($this->profil["attributs"][0]["champ_code_barres"] == "852") $z995r=$this->get_subfield("852","r");
			elseif($this->profil["attributs"][0]["champ_code_barres"] == "999") $z995r=$this->get_subfield("996","x");
			else
			{
				$z995r=$this->get_subfield("995","r");
				$z995p=$this->get_subfield("995","p");
			}
		}
		if($this->type_doc_force["label"] > "") $typeDoc["code"]=$this->type_doc_force["code"];
		else $typeDoc=$this->profil_unimarc->getTypeDoc($label, $z995r, $z995p);
		if($infos==true)
		{
			$ret["code"]=$typeDoc["code"];
			$ret["infos"]="Label=".$label." - "."995\$r=".$z995r[0]." - \$p=".$z995p[0];
			if($this->type_doc_force["label"] > "") $typeDoc["libelle"]=getLibCodifVariable("types_docs",$typeDoc["code"]);
			$ret["libelle"]=$typeDoc["libelle"];
			return $ret;
		}
		if(!$typeDoc["code"]) $typeDoc["code"]=0;
		return $typeDoc["code"];
	}

// ----------------------------------------------------------------
// Calcul de la date de nouveauté
// ----------------------------------------------------------------
	public function calculDateNouveaute($valeur)
	{
		// controle
		$valeur=trim($valeur);
		if($valeur)
		{
			$params=$this->profil["attributs"][4];
			if($params["format"]=="1") $date=substr($valeur,0,10);
			if($params["format"]=="2") $date=substr($valeur,0,4)."-".substr($valeur,4,2)."-".substr($valeur,6,2);
			if($params["format"]=="4") $date=rendDate($valeur,0);
			if($params["format"]=="5") $date=rendDate($valeur,0);
			if($params["format"]=="3")
			{
				$compare=";".$params["valeurs"].";";
				if(strpos($compare, ";".$valeur.";") !== false) $date="2030-12-31";
			}
		}
		
		// ajouter les jours
		if(!$date) $date="2000-01-01";
		else $date=ajouterJours($date, $params["jours"]);

		// retour
		return $date;
	}

// ----------------------------------------------------------------
// TITRES
// ----------------------------------------------------------------
	public function getTitres()
	{
		// Recup des zones titres dans les variables
		$zones=getVariable("unimarc_zone_titre");
		$zones=explode(";",trim($zones));
		foreach($zones as $elem)
		{
			$zone=substr($elem,0,3);
			$champ=substr($elem,-1,1);
			$data=$this->get_subfield($zone);
			foreach($data as $items)
			{
				$sous_champs=$this->decoupe_bloc_champ($items);
				foreach($sous_champs as $item)
				{
					if($item["code"]==$champ)
					{
						$item = trim($item["valeur"]);
						if($item) $titre[]=$this->filtreTitre($item);
					}
				}
			}
		}
		return($titre);
	}

// ----------------------------------------------------------------
// Complément du titre (1er seulement)
// ----------------------------------------------------------------
	public function getComplementTitre()
	{
		$titre=$this->get_subfield("200","e");
		$titre=$this->filtreTitre($titre[0]);
		return trim($titre);
	}
	
// ----------------------------------------------------------------
// Nettoyage des titres
// ----------------------------------------------------------------
	private function filtreTitre($valeur)
	{
		$titre=trim($valeur);
		$titre=str_replace("[","",$titre);
		$titre=str_replace("]"," ",$titre);
		$titre=str_replace("<","",$titre);
		$titre=str_replace(">"," ",$titre);
		$titre=str_replace(chr(136),"",$titre);
		$titre=str_replace(chr(137),"",$titre);
		if(substr($titre,0,1) == "?")
		{
			$deb=substr($titre,0,6);
			$titre=str_replace("?","",$deb).substr($titre,6);
		}
		$titre=trim($titre);
		$car=substr($titre,-1,1);
		if($car=="/" or $car==";" or $car=="," or $car=="." or $car==":" or $car=="-") $titre=substr($titre,0,strlen($titre)-1);
		//tracedebug($valeur." = ".$titre);
		return trim($titre);
	}
	
// ----------------------------------------------------------------
// no de partie
// ----------------------------------------------------------------
	public function getTome()
	{
		$data=$this->get_subfield("200","v");
		for($i=0; $i< count($data); $i++)
		{
			if($data[$i]>"") $tome=$data[$i];
		}
		$data=$this->get_subfield("461","v");
		for($i=0; $i< count($data); $i++)
		{
			if($data[$i]>"") $tome=$data[$i];
		}
		$data=$this->get_subfield("200","h");
		for($i=0; $i< count($data); $i++)
		{
			if($data[$i]>"") $tome=$data[$i];
		}
		return $tome;
	}
	
// ----------------------------------------------------------------
// AUTEURS
// ----------------------------------------------------------------
	public function getAuteurs($auteurPrincipal=false,$clef_alpha=false)
	{
		$zones=array("700","710","720","730","701","702","711","712","721","722");
		foreach($zones as $zone)
		{
			$data=$this->get_subfield($zone);
			foreach($data as $items)
			{
				$sous_champs=$this->decoupe_bloc_champ($items);
				$nom="";
				$prenom="";
				foreach($sous_champs as $item)
				{
					if($item["code"]=="a") $nom=trim($item["valeur"]);
					elseif($item["code"]=="b") $prenom=trim($item["valeur"]);
				}
				$nm=$nom . "|" .$prenom;
				if((strlen($nm) > 2 or $this->indexation->isMotInclu($nom))and striPos($nm,"ANONYME") === false) // On elimine les auteurs avec 1 seule lettre
				{
					$auteur[]=$nm;
					if($clef_alpha==true)return trim($nom.substr($prenom,0,1));
				  if($auteurPrincipal==true) return trim($prenom." ".$nom);
				}
			}
		}

		// retour
		return($auteur);
	}

// ----------------------------------------------------------------
// Auteurs en 200$f
// ----------------------------------------------------------------
	public function get200f()
	{
		$data=$this->get_subfield("200","f");
		return($data);
	}

// ----------------------------------------------------------------
// DEWEY
// ----------------------------------------------------------------
	public function getDewey()
	{
		$data=$this->get_subfield("676");
		foreach($data as $items)
		{
			$sous_champs=$this->decoupe_bloc_champ($items);
			foreach($sous_champs as $item)
			{
				if($item["code"]=="a")
				{ 
					$indice=dewey::filtreIndice($item["valeur"]);
					if($indice) $dewey[]=$indice;
				}
			}
		}
		return $dewey;
	}
// ----------------------------------------------------------------
// PCDM4
// ----------------------------------------------------------------
	public function getPcdm4()
	{
		// specifique pergame
		$data=$this->get_subfield("934","a");
		$code=trim($data[0]);
		if(trim($data[0]) and substr($code,0,1)=="<")
		{
			$elems=str_replace("<","",$code);
			$elems=explode(">",$elems);
			if($elems[0])$indice=$elems[0].$elems[1].$elems[2];
			if($indice) return $indice;
		}

		// autres sigb
		$data=$this->get_subfield("686");
		$data=$data + $this->get_subfield("676");
		foreach($data as $items)
		{
			$sous_champs=$this->decoupe_bloc_champ($items);
			foreach($sous_champs as $item)
			{
				if($item["code"]=="a")
				{ 
					$indice=pcdm4::filtreIndice($item["valeur"]);
					if($indice) return $indice;
				}
			}
		}
	}
// ----------------------------------------------------------------
// COTE NIVEAU NOTICE (pergame uniquement)
// ----------------------------------------------------------------
public function getCote()
{
	if($this->sigb!=1) return false;
	$data=$this->get_subfield("686","a");
	return(trim($data[0]));
}

// ----------------------------------------------------------------
// EDITEUR
// ----------------------------------------------------------------
	public function getEditeur()
	{
		$data=$this->get_subfield("210","c");
		return(trim($data[0]));
	}
// ----------------------------------------------------------------
// CENTRE D'INTERET PERGAME
// ----------------------------------------------------------------
	public function getCentreInteret()
	{
		$data=$this->get_subfield("932");
		foreach($data as $items)
		{
			$sous_champs=$this->decoupe_bloc_champ($items);
			foreach($sous_champs as $item)
			{
				if($item["code"]=="a")
				{
					if(trim($item["valeur"])) $interet[]=$item["valeur"];
				}
			}
		}
		return $interet;
	}
	
// ----------------------------------------------------------------
// ANNEE
// ----------------------------------------------------------------
	public function getAnnee()
	{
		$data=$this->get_subfield("210","d");
		for($i=0; $i < strlen($data[0]); $i++)
		{
			$car=strMid($data[0],$i,1);
			if($car >="0" And $car <= "9") $annee = $annee .$car;
			if(strLen($annee) == 4 ) break;
		}
		if($annee < "1000" or $annee > "2020") $annee="";
		return($annee);
	}

// ----------------------------------------------------------------
// Statut de la notice 0=ok 1=detruire la notice
// ----------------------------------------------------------------
	public function getStatut()
	{
		$statut=0;
		if($this->inner_guide["rs"] == "d" ) $statut=1; 
		$ex=$data=$this->get_subfield("995");
		$nb_ex=count($ex);
		
		// Verif du flag delete dans le 995$o	
		$nb_ex_detruit=0;
		$ex=$data=$this->get_subfield("995","o");
		for($i=0; $i<count($ex); $i++) if($ex[$i]=="d") $nb_ex_detruit++;
		if($nb_ex_detruits >= $nb_ex and $nb_ex > 0) $statut=1;

		return $statut;
	}
// ----------------------------------------------------------------
// Rend si une notice est exportable ou pas 
// controle par la zone 801$b et la clef variable : non_exportable
// ----------------------------------------------------------------
	public function getExportable()
	{
		$champs=$this->get_subfield("801","b");
		for($item=0; $item < count($champs); $item++)
		{
			$champ="x".$champs[$item];
			for($i=0; $i < count($this->copyright); $i++)
			{ 
				if( stripos($champ,"x".$this->copyright[$i]) !== false ) return false;
			}
		}
		return true;
	}
// ----------------------------------------------------------------
// Collections
// ----------------------------------------------------------------
	public function getCollection()
	{
		$data=$this->get_subfield(225,"a");
		return $data;
	}
// ----------------------------------------------------------------
// Langues
// ----------------------------------------------------------------
	public function getLangues()
	{
		$data=$this->get_subfield(101);
		foreach($data as $items)
			{
				$sous_champs=$this->decoupe_bloc_champ($items);
				foreach($sous_champs as $item)
				{
					if($item["code"]=="a") 
					{
						$code=strtolower($item["valeur"]);
						$code=substr($code,0,3);
						if($code=="fra") $code="fre";
						if($code != "und") $langues[]=$code;
					}
				}
			}
		return $langues;
	}
// ----------------------------------------------------------------
// Matieres
// ----------------------------------------------------------------
	public function getMatieres()
	{
		// Recup des zones matières dans les variables
		$zones=getVariable("unimarc_zone_matiere");
		$zones=explode(";",trim($zones));
		foreach($zones as $elem)
		{
			$data=$this->get_subfield(strLeft($elem,3));
			$champs=strMid($elem,3,10);
			foreach($data as $items)
			{
				$sous_champs=$this->decoupe_bloc_champ($items);
				$mot="";
				foreach($sous_champs as $item)
				{
					if(strscan($champs,$item["code"]) >=0 )
					{ 
						if($mot) $mot.=" : ";
						$mot.=$item["valeur"];
					}
				}
				$matiere[]=trim($mot);
			}
		}
		return($matiere);
	}

// ----------------------------------------------------------------
// Récupere les champs forces
// ----------------------------------------------------------------	
	public function getChampsForces()
	{
		for($i=0; $i < count($this->champs_forces); $i++)
		{
			$champ=$this->get_subfield($this->champs_forces[$i]);
			for($j=0; $j < count($champ); $j++)
			{
				$champ_forces["Z".$this->champs_forces[$i]][]=$champ[$j];
			}
		}
		return $champ_forces;
	} 
// ----------------------------------------------------------------
// Rend une notice unimarc complete dans 1 structure
// ----------------------------------------------------------------
	public function getAll()
	{

		// Titre principal
		$data=$this->get_subfield(200,"a");
		$notice["titre_princ"]=$data[0];
		// Label
		$label[]=array("Longueur de la notice",$this->inner_guide["rl"]);
		$label[]=array("Statut de la notice",$this->inner_guide["rs"]);
		$label[]=array("Type de document",$this->inner_guide["dt"].$this->inner_guide["bl"]);
		$label[]=array("Niveau hiérarchique",$this->inner_guide["hl"]);
		$label[]=array("Adresse des données",$this->inner_guide["ba"]);
		$label[]=array("Niveau de catalogage",$this->inner_guide["el"]);
		$notice["label"]=$label;

		// Champs
    foreach ($this->inner_data as $label => $contents) {
      foreach($contents as $content) {
			  $sc=$this->decoupe_field($label, $content);
			  $sc["zone"]=$label;
			  $notice["zones"][]=$sc;
      }
		}
		return $notice;
	}
// ----------------------------------------------------------------
// Rend l'unimarc natif découpé par blocs
// ----------------------------------------------------------------
	public function getUnimarcNatif()
	{
		$ret["label"]=$this->guide;
		$ret["zones"]=$this->inner_directory;
		$ret["data"]=$this->inner_data;
		$ret["bloc"]=$this->full_record;
		return $ret;
	}
// ----------------------------------------------------------------
// Decoupage de regles (sections et genres)
// ----------------------------------------------------------------	
	private function extractRegles()
	{
		$types=array("section","genre","emplacement");
		foreach($types as $type)
		{
			$enregs=fetchAll("select * from codif_".$type);
			if(!$enregs) continue;
			foreach($enregs as $enreg)
			{
				// Specif genre documentaire
				//if($type == "genre" and strToUpper(substr($enreg["libelle"],0,5))=="DOCUM")
				//{
				//	$this->id_genre_documentaire=$enreg["id_genre"];
				//	continue;
				//}
				if(!$enreg["regles"]) continue;
				$regles=explode("\n",$enreg["regles"]);
				foreach($regles as $regle)
				{
					$zone=substr($regle,0,5);
					$signe=substr($regle,5,1);
					$valeurs=explode(";",strToUpper(substr($regle,6)));
					foreach($valeurs as $valeur)
					{ 
						$valeur=trim($valeur);
						if($valeur) $ret[$zone][$type][$signe][$valeur]=$enreg["id_".$type];
					}
				}
			}
		}
		return $ret;
	}
// ----------------------------------------------------------------
// Analyse des règles (sections et genres)
// ----------------------------------------------------------------	
	private function getSectionGenre($genre)
	{
		$ret["section"]=0;
		$ret["genre"]=$genre;
		foreach($this->regles_sections_genres as $zone_champ => $types)
		{
			// Champs notice
			$zone=substr($zone_champ,0,3);
			$champ=substr($zone_champ,4,1);
			$data=$this->get_subfield($zone,$champ);
			if(!count($data)) continue;
			foreach($data as $valeur_notice)
			{
				$valeur_notice=strToUpper($valeur_notice);
				$valeur_notice=str_replace(" ","",$valeur_notice);
				if(!$valeur_notice) continue;
				// Section et genre
				foreach($types as $type => $signes)
				{
					// Si on l'a deja on continue
					if($ret[$type]) continue;
					// Signes de comparaison
					foreach($signes as $signe => $valeurs)
					{
						// Valeurs de controle
						$retenu="";
						foreach($valeurs as $valeur => $code)
						{
							if($signe == "=") {if($valeur == $valeur_notice) $retenu=$code;}
							elseif($signe == "/") {if($valeur == substr($valeur_notice,0,strlen($valeur))) $retenu=$code;}
							elseif($signe == "*") {if( strpos($valeur_notice,$valeur) !== false) $retenu=$code;}
							// Si on a trouvé on break
							if($retenu) break;
						}
						if($retenu)
						{ 
							$ret[$type]=$retenu;
							if($ret["section"] and $ret["genre"]) return $ret;
							break;
						}
					}
				}
			}
		}
		return $ret;
	}

// ----------------------------------------------------------------
// Rend l'id section genre ou emplacement depuis les regles
// ----------------------------------------------------------------
	private function getIdCodeExemplaire($type,$champ,$sous_champ,$valeur)
	{
		$valeur=trim(strtoupper($valeur));
		$champ=$champ."$".$sous_champ;
		$id=$this->regles_sections_genres[$champ][$type]["="][$valeur];
		if(!$id) $id="";
		return $id;
	}
}
?>