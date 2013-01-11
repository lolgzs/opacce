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
/////////////////////////////////////////////////////////////////////////////////////
// NOTICE D'INTEGRATION
/////////////////////////////////////////////////////////////////////////////////////

require_once("classe_isbn.php");
require_once("classe_indexation.php");
require_once("classe_unimarc.php");
require_once("classe_codif_matiere.php");
require_once("classe_codif_langue.php");

class notice_integration
{
	private $id_profil;									// Id du profil de données initialisé
	private $format;										// Format de fichier 0=unimarc
	private $id_article_periodique;			// Mode d'indentification des articles de periodiques
	private $type_doc_force;						// Type de document forcé dans maj_auto
	private $analyseur;									// Instance de la classe qui découpe la notice
	private $indexation;								// Instance de la classe d'indexation
	private $id_bib;										// Bibliotheque pour qui on integre
	private $type_operation;						// Maj ou suppression
	private $notice;										// Structure notice en cours de traitement
	private $qualite_bib;								// Code qualite de la bib
	private $sigb;											// Sigb de la bib 
	private $statut;										// Statut de la notice traitee
	public  $libStatut=array("Rejetées","Créée","Supprimée","Mise à jour notice et exemplaires","Mise à jour exemplaires","Remplacées","Homogénéisées","Mises en notices succintes");
	private $erreur;										// Message d'erreur notice traitee
	private $filtrer_fulltext;					// Vient de la variable filtrer_fulltext. 
	private $identification;						// Mode d'identification de la notice
	private $notice_sgbd;								// Instance unimarc pour notices de la base
	private $mode_doublon;							// Mode de dédoublonnage (tous identifiants ou clef alpha prioritaire)
	private $url_site;                  // Url d'origine du site opac
	
// ----------------------------------------------------------------
// Constructeur
// ----------------------------------------------------------------
	function __construct()
	{
		// Classe d'indexation
		$this->indexation=new indexation();
		$this->filtrer_fulltext=getVariable("filtrer_fulltext");
		$this->mode_doublon=getVariable("mode_doublon");
		$this->notice_sgbd=new notice_unimarc();
	}
	
// ----------------------------------------------------------------
// Init du format
// ----------------------------------------------------------------
	function setParamsIntegration($id_bib,$type_operation,$id_profil,$type_doc_force="")
	{
		global $sql;
		$this->id_bib=$id_bib;
		$this->type_operation=$type_operation;
		$this->type_doc_force=$type_doc_force;
		
		$bib=$sql->fetchEnreg("select * from int_bib where id_bib=$id_bib");
		$this->qualite_bib=$bib["qualite"];
		$this->sigb=$bib["sigb"];
		
		$this->id_profil=$id_profil;
		if($id_profil < 0)$format=1; // Paniers d'homogeneisation
		else
		{
			$enreg=fetchEnreg("select * from profil_donnees where id_profil=$id_profil");
			$this->id_article_periodique=$enreg["id_article_periodique"];
			$format=$enreg["format"];
			if(!$format) $format=0;
			if($enreg["type_fichier"]==10) $format=10;
		}
		$this->format=$format;
		unset($this->analyseur);
		switch($format)
		{
			// Unimarc
			case 0:
				$this->analyseur=new notice_unimarc();
				break;
			// archives calice
			case 10:
				require_once("classe_notice_archive_calice.php");
				$this->analyseur=new notice_archive_calice();
				break;
			// marc21
			case 6:
				require_once("classe_notice_marc21.php");
				$this->analyseur=new notice_marc21();
				break;
			// Ascii
			default:
				require_once("classe_notice_ascii.php");
				$this->analyseur=new notice_ascii();
				break;
		}
	}
// ----------------------------------------------------------------
// Traitement d'une notice
// ----------------------------------------------------------------
	public function traiteNotice($data)
	{
		global $sql;
		$id_bib=$this->id_bib;
		if(! $this->analyseur ) return false;
		$this->statut=0;
		$this->erreur="";
		unset($this->notice);

		if(!$this->analyseur->ouvrirNotice($data,$this->id_profil,$this->sigb,$this->type_doc_force))
		{
			$this->erreur=$this->analyseur->getLastError();
			return 0;
		}
		$this->notice=$this->analyseur->getNoticeIntegration();

		// article de périodique
		if($this->notice["type_doc"]==100)
		{
			$this->ecrireArticlePeriodique();
			return;
		}
	
		// Chercher la notice dans la base 
		$id_notice=$this->chercheNotice();
		
		// Traiter le type d'operation : suppressions
		if($this->type_operation == 1)
		{
				$this->statut=2;
				if($this->format == 0 )$this->supprimerNotice($id_notice,$this->notice["id_origine"]);
				else $this->supprimerExemplaire($id_notice,$this->notice["exemplaires"][0]);
				return;
		}
		// Notice a supprimer
		if($this->notice["statut"]==1)
		{
			$this->statut=2;
			$this->supprimerNotice($id_notice,$this->notice["id_origine"]); 
			return;
		}

		// suppression ou creation des articles si périodique
		if($this->notice["type_doc"]==2)
		{
			// pergame : on efface tous les articles
			if($this->id_article_periodique == 1)
			{
				$clef_chapeau=$this->notice["clef_chapeau"];
				$clef_numero=$this->notice["tome_alpha"];
				$date=dateDuJour(0);
				$sql->execute("delete from notices_articles where clef_chapeau='$clef_chapeau' and clef_numero='$clef_numero' and date_maj!='$date'");
			}

			// opsys indexpresse : on cree les articles manquants
			if($this->id_article_periodique == 2 and $this->notice["articles_periodiques"])
			{
				$enreg=array("clef_chapeau"=>$this->notice["articles_periodiques"]["clef_chapeau"],
										"clef_numero"=>$this->notice["articles_periodiques"]["clef_numero"],
										"date_maj"=>date("Y-m-d"),
										);
				foreach($this->notice["articles_periodiques"]["articles"] as $id_unimarc)
				{
					$clef_unimarc=intval($id_unimarc);
					if(!$clef_unimarc) { $this->notice["warnings"][]=array("Identifiant article de périodique incorrect",$id_unimarc); continue; }
					$existe=$sql->fetchOne("select count(*) from notices_articles where clef_unimarc='$clef_unimarc'");
					if($existe)
					{
						unset($enreg["clef_unimarc"]);
						$sql->update("update notices_articles set @SET@ where clef_unimarc='$clef_unimarc'",$enreg);
					}
					else
					{
						$enreg["clef_unimarc"]=$clef_unimarc;
						$sql->insert("notices_articles",$enreg);
					}
				}
			}
		}

		// Mise a jour
		if(!$id_notice)
		{	
			$this->notice["qualite"]=$this->qualite_bib;
			$id_notice=$this->insertNotice();
			if(!$id_notice) return;
			else $this->statut=1;
		}
		else
		{
			// Controle des identifiants pour dedoublonnage eventuel
			if($this->identification["statut"] == "code_barres")
			{
				// Lecture identifiants de la notice de la base
				$new_id_notice=0;
				$enreg=$sql->fetchEnreg("Select isbn,ean,id_commerciale from notices where id_notice=$id_notice");
				if($this->notice["isbn10"] and $this->notice["isbn10"] != $enreg["isbn"] and $this->notice["isbn13"] != $enreg["isbn"]) 
				{ 
					$new_id_notice=$this->identification["isbn"]; 
					if(!$new_id_notice) $new_id_notice="creation";
				}
				if(!$new_id_notice and $this->notice["ean"] and $this->notice["ean"] != $enreg["ean"]) 
				{
					$new_id_notice=$this->identification["ean"];
					if(!$new_id_notice) $new_id_notice="creation"; 
				}
//				if(!$new_id_notice and !$ean and $this->notice["id_commerciale"] != $enreg["id_commerciale"])
//				{ 
//					$new_id_notice=$this->identification["id_commerciale"];  
//					if(!$new_id_notice) $new_id_notice="creation"; 
//				}
				if($new_id_notice)
				{
					if($new_id_notice == "creation") { $new_id_notice=$this->insertNotice(); if(!$new_id_notice) return; }
					else $this->updateNotice($new_id_notice,$this->qualite_bib);
					
					// on supprime les exemplaires de l'ancienne notice
					for($i=0; $i<count($this->notice["exemplaires"]); $i++)
					{
						$ex=$this->notice["exemplaires"][$i];
						$sql->execute("delete from exemplaires where id_bib=$id_bib and id_notice=$id_notice and code_barres='".$ex["code_barres"]."'");
					}
					$id_notice=$new_id_notice;
				}
				else $id_notice=$this->updateNotice($id_notice,$this->qualite_bib);
			}
			else $id_notice=$this->updateNotice($id_notice,$this->qualite_bib);
		}
		$this->ecrireExemplaires($id_notice);
    return $this->notice;
	}
	
// ----------------------------------------------------------------
// Traitement notice homogene 
// ----------------------------------------------------------------
	public function traiteHomogene($id_notice,$isbn,$ean,$id_commerciale,$no_request)
	{
		global $sql;
		// Appel du service
		$args["isbn"]=$isbn;
		$args["ean"]= $ean;
		$args["id_commerciale"]=$id_commerciale;
		$args["no_request"]=$no_request;
		$ret=communication::runService(4,$args);

		// Formatter la reponse
		$ret["timeout"]=10;
		
		// Statut not found : Mise a jour nombre de retries
		if($ret["statut_z3950"] == "1") $sql->execute("Update notices set z3950_retry = z3950_retry +1 Where id_notice=$id_notice"); 

		// Statut ok : on remplace la notice
		if(!$this->analyseur) $this->analyseur=new notice_unimarc();
		if($ret["statut_z3950"] > "1")
		{
			$this->analyseur->ouvrirNotice($ret["unimarc"],1);
			$this->notice=$this->analyseur->getNoticeIntegration();
			$qualite=getVariable("homogene_code_qualite");
			$this->updateNotice($id_notice,$qualite);
		}
		return $ret;
	}
	
// ----------------------------------------------------------------
// Traitement notice succinte
// ----------------------------------------------------------------
	public function traiteSuccinte($enreg)
	{
		global $sql;
		
		extract($enreg);
		$this->id_bib=$id_bib;
		$notice=unserialize($data);
		$this->notice=$notice;
		
		// On la cherche dans la base
		$id_notice=$this->chercheNotice();
		if($id_notice)
		{
			$unimarc=$sql->fetchOne("select unimarc from notices where id_notice=$id_notice");
			$this->notice_sgbd->ouvrirNotice($unimarc,0);
			$this->notice=$this->notice_sgbd->getNoticeIntegration();
			$this->notice["id_origine"]=$notice["id_origine"];
			$this->notice["statut_exemplaires"] = $notice["statut_exemplaires"];
			$this->notice["exemplaires"] = $notice["exemplaires"];
			$this->updateNotice($id_notice,$this->notice["qualite"]);
			$this->ecrireExemplaires($id_notice);
			$ret["statut"]=4;
		}
		
		// On cherche sur les serveurs z3950
		else
		{
			$args["isbn"]=$notice["isbn"];
			$args["ean"]= $notice["ean"];
			$args["id_commerciale"]=$notice["id_commerciale"];
			$args["no_request"]=getVariable("Z3950_cache_only");
			$ret=communication::runService(4,$args);
			// Statut ok : on remplace la notice
			if(!$this->analyseur) $this->analyseur=new notice_unimarc();
			if($ret["statut_z3950"] > "1")
			{
				$this->analyseur->ouvrirNotice($ret["unimarc"],1);
				$this->notice=$this->analyseur->getNoticeIntegration();
				$this->notice["id_origine"]=$notice["id_origine"];
				$this->notice["statut_exemplaires"] = $notice["statut_exemplaires"];
				$this->notice["exemplaires"] = $notice["exemplaires"];
				$this->notice["qualite"]=getVariable("homogene_code_qualite");
				$id_notice=$this->insertNotice();
				$this->ecrireExemplaires($id_notice);
				$ret["statut"]=1;
			}
			else $ret["statut"]=0;
		}
		
		// Mise a jour 
		$ret["id_notice"]=$id_notice;
		$ret["id_bib"]=$id_bib;
		$ret["isbn"]=$notice["isbn"];
		$ret["ean"]= $notice["ean"];
		$ret["id_commerciale"]=$notice["id_commerciale"];
		if($id_notice) $sql->execute("delete from notices_succintes where id=$id");
		else $nb=$sql->execute("Update notices_succintes set z3950_retry = z3950_retry +1 Where id=$id");
		return $ret;
	}

// ----------------------------------------------------------------
// Traitement pseudo_notices
// ----------------------------------------------------------------
	public function traitePseudoNotice($type_doc,$enreg)
	{
		global $sql;
		// Init valeurs
		if(!$enreg["id_bib"]) $enreg["id_bib"]=$sql->fetchOne("select min(id_bib) from int_bib");
		$code_barres=str_repeat("0",(4-strlen($enreg["id_bib"]))).$enreg["id_bib"]."-".$enreg["ID_NOTICE"];
		
		// Init nom de la table en fonction du type de document
		$colonne_id_notice="ID_NOTICE";
		switch($type_doc)
		{
			case 8: 	// Cms
				$table="cms_article";
				$colonne_clef="ID_ARTICLE";
				$label="m1";
				$enreg["DESCRIPTION"]=strip_tags($enreg["CONTENU"]);
				break;
			case 9: 	// rss
				$table="rss_flux";
				$colonne_clef="ID_RSS";
				$label="m2";
				break;
			case 10: 	// sitotheque
				$table="sito_url";
				$colonne_clef="ID_SITO";
				$label="m3";
				break;
			default: // Albums
				if($type_doc>99) {
					$table="album";
					$colonne_clef="id";
					$colonne_id_notice="notice_id";
					$enreg["TITRE"]=$enreg["titre"];
					$enreg["TAGS"]=$enreg["tags"];
					$label="m4";
					if ($enreg["id_origine"])
						$enreg["URL"] = $this->getUrlSite().'bib-numerique/notice/ido/'.$enreg["id_origine"];
				}
		}

		$id_ressource=$enreg[$colonne_clef];

		// Création notice
		$enreg[$colonne_id_notice]=sqlInsert("notices",array("type_doc" => $type_doc));
		$code_barres=str_repeat("0",(4-strlen($enreg["id_bib"]))).$enreg["id_bib"]."-".$enreg[$colonne_id_notice];
		$sql->execute("insert into exemplaires(id_notice,id_bib,activite) Values(".$enreg[$colonne_id_notice].",".$enreg["id_bib"].",'A consulter sur le portail')");
		$sql->execute("update ".$table." set ".$colonne_id_notice."=".$enreg[$colonne_id_notice]." where ".$colonne_clef."=".$enreg[$colonne_clef]);
		$ret["statut"]=1;

		
		// Fabriquer l'unimarc
		$zone_100=rendDate($enreg["DATE_MAJ"],4)."a|||||||||||y0frey0103####ba";
		$this->notice_sgbd->setNotice("");
		$this->notice_sgbd->set_dt($label[0]);
		$this->notice_sgbd->set_bl($label[1]);
		$this->notice_sgbd->add_field("001","","".$enreg[$colonne_id_notice]);
		$this->notice_sgbd->add_field("100","  ","a".$zone_100);
		$this->notice_sgbd->add_field("200","1 ","a".$enreg["TITRE"]);

		if ($enreg["sous_titre"]) $this->notice_sgbd->add_field("200","1 ","e".$enreg["sous_titre"]);

		if($enreg["auteur"]) $this->notice_sgbd->add_field("700","1 ","a".$enreg["auteur"]);
		if($enreg["editeur"]) $this->notice_sgbd->add_field("210","1 ","c".$enreg["editeur"]);
		if($enreg["annee"]) $this->notice_sgbd->add_field("210","  ","d".$enreg["annee"]);
		if($enreg["id_langue"]) $this->notice_sgbd->add_field("101","0 ","a".$enreg["id_langue"]);
		$this->notice_sgbd->add_field("856","1 ","b".$id_ressource);
		if($enreg["DESCRIPTION"]) $this->notice_sgbd->add_field("300","  ","a".$enreg["DESCRIPTION"]);
		if($enreg["provenance"]) $this->notice_sgbd->add_field("317","  ","a".$enreg["provenance"]);
		if($enreg["URL"]) $this->notice_sgbd->add_field("856","  ","a".$enreg["URL"]);
		if($enreg["notes"]) $this->notice_sgbd->addSerializedFields($enreg['notes']);

		if($enreg["nature_doc"]) {
			
			$nature_docs=explode(';',$enreg['nature_doc']) ;
			foreach($nature_docs as $nature_doc) {
				$this->notice_sgbd->add_field('200','1 ','b'.getLibCodifVariable('nature_docs',$nature_doc));
			}

		}
		$this->notice_sgbd->update();
		
		// Tags
		if($enreg["TAGS"])
		{
			$tags=explode(";",$enreg["TAGS"]);
			foreach($tags as $tag)
			{ 
				$code_alpha=$this->indexation->alphamaj($tag);
				if(!$code_alpha) continue;
				$id_tag=$sql->fetchOne("select id_tag from codif_tags where code_alpha = '$code_alpha'");
				if(!$id_tag) 
				{
					$data_tag["libelle"]=$tag;
					$data_tag["code_alpha"]=$code_alpha;
					$id_tag=$sql->insert("codif_tags",$data_tag);
				}
				$facettes.=" "."Z".$id_tag;
			}
		}

		// genre
		if($enreg["genre"])
		{
			$genres=explode(";",$enreg["genre"]);
			$genre=$genres[0];
		}

		if($enreg["cote"]) {
			$cote = $enreg["cote"];
		}

		// dewey
		if($enreg["dewey"])
		{
			$deweys=explode(";",$enreg["dewey"]);
			foreach($deweys as $dewey)
			{
				if(!trim($dewey)) continue;
				$facettes.=" "."D".$dewey;
				$lib=fetchOne("select libelle from codif_dewey where id_dewey=$dewey");
				$data["dewey"].=" ".$lib;
			}
			$data["dewey"]=$this->indexation->getfullText($data["dewey"]);
		}

		// matieres
		if($enreg["matiere"])
		{
			$matieres=explode(";",$enreg["matiere"]);
			foreach($matieres as $matiere)	{
				if(!trim($matiere)) continue;
				$facettes.=" "."M".$matiere;
				$lib=codif_matiere::getInstance()->getLibelle($matiere);
				$data["matieres"].=" ".$lib;

				$this->notice_sgbd->add_field("610","1 ","a".$lib);
				$this->notice_sgbd->update();
			}
			$data["matieres"]=$this->indexation->getfullText($data["matieres"]);
		}

		// langue
		if($enreg["id_langue"]) $facettes.=" "."L".$enreg["id_langue"];

		// Creation notice
		$id_notice=$enreg[$colonne_id_notice];
		$data["type_doc"]=$type_doc;
		$data["alpha_titre"]=$this->indexation->codeAlphaTitre($enreg["TITRE"]);
		$data["clef_alpha"]=$this->indexation->getClefAlpha($type_doc,$enreg["TITRE"],"",$enreg["auteur"],"",$enreg["editeur"],$enreg["annee"]);
		$data["clef_oeuvre"]=$this->indexation->getClefOeuvre($enreg["TITRE"],"",$enreg["auteur"],"");
		$data["titres"]=$this->indexation->getfullText($enreg["TITRE"]);
		if($enreg["auteur"])
		{
			$data["auteurs"]=$this->indexation->getfullText($enreg["auteur"]);
			$data["alpha_auteur"]=$this->indexation->alphaMaj($enreg["auteur"]);
		}
		if($enreg["editeur"]) $data["editeur"]=$this->indexation->getfullText($enreg["editeur"]);
		$data["facettes"]=$facettes;
		if ($enreg["annee"]) $data["annee"]=$enreg["annee"];
		else $data["annee"]=substr($enreg["DATE_MAJ"],0,4);
		$data["qualite"]=3; // Qualite = pseudo_notice
		$data["exportable"]="1";
		$data["unimarc"]=$this->notice_sgbd->getFullRecord();
		$data["date_maj"]=dateDuJour(2);
		if($enreg["fichier"])
		{
			$data["url_vignette"]=$this->getUrlSite()."bib-numerique/notice-thumbnail/id/".$enreg["id"];
			$data["url_image"]=$data["url_vignette"];
		}
		$sql->update("update notices set @SET@ Where id_notice=$id_notice",$data);
		
		// Creation exemplaire
		$sql->execute("update exemplaires set code_barres='$code_barres', genre='$genre', id_origine='".$id_ressource."', cote='$cote' where id_notice=".$id_notice);

		// retour
		$ret["id_notice"]=$id_notice;
		$ret["unimarc"]=$data["unimarc"];
		$ret["code_barres"]=$code_barres;
		return $ret;
	}


// ----------------------------------------------------------------
// Retourne l'url d'origine du site (ex: http://opac3.pergame.net/)
// ----------------------------------------------------------------
	private function getUrlSite() {
		if (isset($this->url_site))
			return $this->url_site;

		$adresse=getVariable("url_site");
		if(strtolower(substr($adresse,0,7)) !="http://") $adresse="http://".$adresse;
		if(substr($adresse,-1,1)!="/") $adresse.="/";
		return $this->url_site = $adresse;
	}


// ----------------------------------------------------------------
// Cherche dans la base fusionnée
// ----------------------------------------------------------------
	private function chercheNotice()
	{
		global $sql;
		$id_bib = $this->id_bib;
		if(!$this->mode_doublon)
		{
			$isbn10=$this->notice["isbn10"];
			$isbn13=$this->notice["isbn13"];
			$ean=$this->notice["ean"];
			$id_commerciale=$this->notice["id_commerciale"];
		}
		$clef_alpha=$this->notice["clef_alpha"];
		
		$this->identification=array("statut"=>"non trouvée");
		if($this->notice["statut_exemplaires"]["nb_ex"]>0)
		{
			$unicite_codes_barres=getVariable("unicite_code_barres");
			if($unicite_codes_barres=="1") $condition="";
			else $condition='id_bib='.$id_bib.' and ';
			foreach($this->notice["exemplaires"] as $ex)
			{
				$code_barres=$ex["code_barres"];
				if($code_barres >"")$id_notice=$sql->fetchOne("select id_notice from exemplaires where ".$condition." code_barres='$code_barres'");
				if($id_notice) 
				{
					$this->identification["statut"]="code_barres";
					$this->identification["code_barres"]=$id_notice;
					break;
				}
			}
		}
		if($isbn10) 
		{
			$this->identification["isbn"]=$sql->fetchOne("select id_notice from notices where isbn='$isbn10'"); 
			if(!$id_notice and $this->identification["isbn"])
			{
				$this->identification["statut"]="isbn";
				$id_notice=$this->identification["isbn"];
			}
		}
		if($isbn13 and !$this->identification["isbn"]) 
		{
			$this->identification["isbn"]=$sql->fetchOne("select id_notice from notices where isbn='$isbn13'"); 
			if(!$id_notice and $this->identification["isbn"])
			{
				$this->identification["statut"]="isbn";
				$id_notice=$this->identification["isbn"];
			}
		}
		if($ean) 
		{
			$this->identification["ean"]=$sql->fetchOne("select id_notice from notices where ean='$ean'"); 
			if(!$id_notice and $this->identification["ean"]) 
			{
				$this->identification["statut"]="ean";
				$id_notice=$this->identification["ean"];
			}
		}
		if($id_commerciale and !$ean) 
		{
			$this->identification["id_commerciale"]=$sql->fetchOne("select id_notice from notices where id_commerciale='$id_commerciale'"); 
			if(!$id_notice and $this->identification["id_commerciale"]) 
			{
				$this->identification["statut"]="id_commerciale";
				$id_notice=$this->identification["id_commerciale"];
			}
		}
		if($this->mode_doublon==1)
		{
			$this->identification["clef_alpha"]=$sql->fetchOne("select id_notice from notices where clef_alpha='$clef_alpha'");
			if(!$id_notice and $this->identification["clef_alpha"])
			{
				$this->identification["statut"]="clef_alpha";
				$id_notice=$this->identification["clef_alpha"];
			}
		}
		if(!$id_notice)$this->identification["statut"]="non trouvée";
		return $id_notice;
	}

// ----------------------------------------------------------------
// Ecriture nouvelle notice
// ----------------------------------------------------------------
	private function insertNotice()
	{
		global $sql;
		
		// Test presence exemplaires
		if(!$this->notice["statut_exemplaires"]["nb_ex"])
		{
			$this->erreur="notice sans exemplaire";
			$this->statut=0;
			return false;
		}
		
		// Test titre principal
		if( !$this->notice["titre_princ"] )
		{
			if($this->notice["isbn"] or $this->notice["ean"] or $this->notice["id_commerciale"])
			{
				$id_bib=$this->id_bib;
				$data=serialize($this->notice);
				//$sql->execute("delete from notices_succintes where id_bib=$id_bib and profil=$profil and data='".utf8_encode(addslashes($data))."'");
				$id_succinte=$sql->insert("notices_succintes",compact("id_bib","data"));
				$this->statut=7;
				return false;
			}
			if($this->format == 0 or $this->format==10) $this->erreur="pas de titre principal";
			else $this->erreur="Aucun identifiant valide";
			$this->statut=0;
			return false;
		}

		// Traitement des facettes
		$this->traiteFacettes();

		$id_notice=$sql->insert("notices", $this->noticeToDBEnreg($this->notice));
		$this->statut=1;
		return $id_notice;
	}


	public function noticeToDBEnreg(&$notice) {
		return [
			"type_doc" => $this->notice["type_doc"],
			"alpha_titre" => $this->notice["alpha_titre"],
			"alpha_auteur" => $this->notice["alpha_auteur"],

			"titres" => $this->indexation->getfullText(array_merge($this->notice["titres"], 
																														 [$this->notice["clef_chapeau"], 
																															$this->notice["tome_alpha"]])),

			"auteurs" => $this->indexation->getfullText( $this->notice["auteurs"] 
																									 ? $this->notice["auteurs"] 
																									 : $this->notice["200_f"]),

			"editeur" => $this->indexation->getfullText($this->notice["editeur"]),
			"collection" => $this->indexation->getfullText($this->notice["collection"]),
			"matieres" => $this->indexation->getfullText($this->notice["matieres"]),
			"dewey" => $this->indexation->getfullText($this->notice["full_dewey"]),
			"facettes" => $this->notice["facettes"],
			"isbn" => $this->notice["isbn"],
			"ean" => $this->notice["ean"],
			"id_commerciale" => $this->notice["id_commerciale"],
			"clef_alpha" => $this->notice["clef_alpha"],
			"clef_chapeau" => $this->notice["clef_chapeau"],
			"clef_oeuvre" => $this->notice["clef_oeuvre"],
			"tome_alpha" => $this->notice["tome_alpha"],
			"annee" => $this->notice["annee"],
			"qualite" => $this->notice["qualite"],
			"exportable" => $this->notice["exportable"],
			"cote" => $this->notice["cote"],
			"unimarc" => $this->notice["unimarc"],
			"date_maj" => dateDuJour(2) ];
	}

// ----------------------------------------------------------------
// MAJ notice (remplacement si homogeneisation)
// ----------------------------------------------------------------
	private function updateNotice($id_notice,$qualite)
	{
		global $sql;
		
		$notice_enreg=$sql->fetchEnreg("select qualite,unimarc,facettes from notices where id_notice=$id_notice");
		$this->notice["qualite"]=$notice_enreg['qualite'];
		$unimarc=$notice_enreg['unimarc'];
		$this->notice["facette"]=$notice_enreg['facettes'];
		// Test qualite
		//		$this->notice["qualite"]=$sql->fetchOne("select qualite from notices where id_notice=$id_notice");	
		if($qualite > $this->notice["qualite"])
		{
			$this->notice["qualite"]=$qualite;
			$this->statut=5;
		}
		elseif($qualite < $this->notice["qualite"]) $this->statut=4;
		else
		{
			$this->notice["qualite"]=$qualite;
			$this->statut=3;
		}
			
		// Si la notice n'a pas de titre on substitue par celle de la base en forcant a une qualite inferieure
		if( !$this->notice["titre_princ"] ) $this->statut=4;
		
		// Zones forcees
		//		$unimarc=$sql->fetchOne("select unimarc from notices where id_notice=$id_notice");
		$this->notice_sgbd->ouvrirNotice($unimarc,0);
		$champs_forces=$this->notice_sgbd->getChampsForces();
		if($champs_forces and $champs_forces != $this->notice["champs_forces"])
		{
			// On sauvegarde les données propres à la bib
			$id_origine=$this->notice["id_origine"];
			$qualite=$this->notice["qualite"];
			$statut_exemplaires = $this->notice["statut_exemplaires"];
			$exemplaires = $this->notice["exemplaires"];
			$warnings=$this->notice["warnings"];

			// Merge champs forces
			if($this->notice["champs_forces"])$new=array_merge_recursive($champs_forces,$this->notice["champs_forces"]); 
			else $new = $champs_forces;
			
			// Si la notice de la base est de meilleure qualite on la prend
			if($this->statut==5) $this->notice_sgbd->ouvrirNotice($this->notice["unimarc"],1);

			// Fusion des champs forces
			$champs_forces=array();
			foreach($new as $zone => $valeurs)
			{
				$zone=substr($zone,1); // on retire le Z qui sert au array_merge_recursive
				$this->notice_sgbd->delete_field($zone); // On supprime l'ancienne zone
				// si champ matiere on dedoublonne directement
				if(substr($zone,0,1)=="6")
				{
					$champs=array_unique($valeurs);
					$champs_forces[$zone]=$champs;
				}
				// sinon on decoupe les elements on les dedoublonne et on les remet dans 1 seule zone 
				else
				{
					$champs=array();
					foreach($valeurs as $valeur) $champs=array_merge($champs,$this->notice_sgbd->getValeursBloc($valeur));
					$champs=array_unique($champs);
					$champs_forces[$zone][]=$this->notice_sgbd->makeZoneByValeurs(substr($valeurs[0],0,2),"a",$champs);
				}
			}
			// On remet les nouvelles zones
			foreach($champs_forces as $zone => $valeurs)
			{
				foreach($valeurs as $valeur) $this->notice_sgbd->add_zone($zone,$valeur);
			}
			$this->notice["unimarc"]=$this->notice_sgbd->update();

			// On reprend les titres et les matieres
			$this->notice=$this->notice_sgbd->getNoticeIntegration();
			$this->notice["statut_exemplaires"] = $statut_exemplaires;
			$this->notice["exemplaires"] = $exemplaires;
			$this->notice["warnings"] = $warnings;
			$this->notice["qualite"]=$qualite;
			$this->notice["id_origine"]=$id_origine;
			$this->statut=3;
		}
		// Recup des facettes
		//		$this->notice["facettes"]=$sql->fetchOne("select facettes from notices where id_notice=$id_notice");
		if($this->statut == 4) return $id_notice;

		$this->traiteFacettes();

		// Qualité égale ou zones forcees : on update		
		$sql->update("update notices set @SET@ Where id_notice=$id_notice",
								 $this->noticeToDBEnreg($data));
		return $id_notice;
	}
// --------------------------------------------------------------------------------
// Suppression de notice (ne supprime pas l'enreg notice)
// --------------------------------------------------------------------------------
	private function supprimerNotice($id_notice,$id_origine)
	{
		global $sql;
		$id_bib=$this->id_bib;
		if(!$id_notice)
		{ 
			// On cherche par id_origine
			if($id_origine) $id_notice=$sql->fetchOne("select id_notice from exemplaires where id_origine='$id_origine'and id_bib=$id_bib");
			if(!$id_notice)
			{
				$this->statut=0;
				$this->erreur="notice à supprimer non reconnue";
				return false;
			}
		}
		$sql->execute( "delete from exemplaires where id_notice=$id_notice and id_bib=$id_bib");
	}
	
// ----------------------------------------------------------------
// Ecriture des exemplaires
// ----------------------------------------------------------------	
	private function ecrireExemplaires($id_notice)
	{
		global $sql;
		$id_bib=$this->id_bib;
		$to_insert=[];

		$code_barres = [];
		foreach($this->notice['exemplaires'] as $ex) {
			$code_barres []= '\''.$ex['code_barres'].'\'';
			if($ex["activite"]=="d") continue;

			$data=$ex;
			$data["id_notice"]=$id_notice;
			$data["id_bib"]=$id_bib;
			$data["id_origine"]=$this->notice["id_origine"];
			$to_insert[]=$data;
		}

		if (!empty($code_barres))  {
			$req_delete="delete from exemplaires where id_notice=$id_notice and id_bib=$id_bib and code_barres in (".implode(',', $code_barres).")";
			$sql->execute($req_delete);
		}

		foreach($to_insert as $data) {
			$sql->insert("exemplaires",$data);
		}

		$date_maj=dateDuJour(2);
		$sql->execute("update notices set date_maj='$date_maj' where id_notice=$id_notice");
	}
// --------------------------------------------------------------------------------
// Suppression d'un exemplaire venant d'un fichier ascii
// --------------------------------------------------------------------------------
	private function supprimerExemplaire($id_notice,$ex)
	{
		if(!$id_notice)
		{ 
			$this->statut=0; 
			$this->erreur="notice de l\'exemplaire à supprimer non trouvée";
			return false;
		}
		global $sql;
		$id_bib=$this->id_bib;
		$code_barres=$ex["code_barres"];
		$ret=$sql->execute( "delete from exemplaires where id_notice=$id_notice and id_bib=$id_bib and code_barres='$code_barres'");
		if(!$ret)
		{ 
			$this->statut=0;
			$this->erreur="code-barres à supprimer non trouvé";
			return false;
		}	
		$date_maj=dateDuJour(2);
		$sql->execute("update notices set date_maj='$date_maj' where id_notice=$id_notice");
	}
// --------------------------------------------------------------------------------
// Traitement des facettes
// --------------------------------------------------------------------------------
	public function traiteFacettes()
	{
		global $sql;

		// Virer les facettes sauf les tags
		$controle=explode(" ",$this->notice["facettes"]);
		$this->notice["facettes"]="";
		for($i=0; $i < count($controle); $i++)
		{
			$tp=substr($controle[$i],0,1);
			if($tp =="Z") $this->notice["facettes"].=" ".$controle[$i];
		}
		
		// Dewey
		if($this->notice["dewey"])
		{
			foreach($this->notice["dewey"] as $indice)
			{
				$enreg=$sql->fetchEnreg("Select * from codif_dewey where id_dewey='$indice'");
				if(!$enreg["id_dewey"]) $sql->insert("codif_dewey",array("id_dewey"=>$indice));
				else $this->notice["full_dewey"].=$enreg["libelle"]." ";
				$facettes[]="D".$indice;
			}
		}
		// Pcdm4
		if($this->notice["pcdm4"])
		{
			$indice=$this->notice["pcdm4"];
			$enreg=$sql->fetchEnreg("Select * from codif_pcdm4 where id_pcdm4='$indice'");
			if(!$enreg["id_pcdm4"]) $sql->insert("codif_pcdm4",array("id_pcdm4"=>$indice));
			else $this->notice["full_dewey"].=$enreg["libelle"]." ";
			$facettes[]="P".$indice;
		}
		
		// Auteurs
		if($this->notice["auteurs"])
		{
			foreach($this->notice["auteurs"] as $auteur)
			{
				$code_alpha=$this->indexation->alphaMaj($auteur);
				$code_alpha=str_replace(" ","x",$code_alpha);
				if(!$code_alpha) continue;
				$enreg=$sql->fetchEnreg("Select * from codif_auteur where MATCH(formes) AGAINST('\"".$code_alpha."\"' IN BOOLEAN MODE) ");
				if(!$enreg["id_auteur"])
				{ 
					$pos=strscan($auteur,"|");
					$nom_prenom = trim(substr($auteur,($pos+1))." ".substr($auteur,0,$pos));
					$id_auteur=$sql->insert("codif_auteur",array("libelle" => $nom_prenom,"formes" => $code_alpha));
				}
				else $id_auteur=$enreg["id_auteur"];
				$facettes[]="A".$id_auteur;
			}
		}
		
		// Matieres
		if($this->notice["matieres"])
		{
			foreach($this->notice["matieres"] as $matiere)
			{
				$code_alpha=$this->indexation->alphaMaj($matiere);
				if(!$code_alpha) continue;
				$enreg=$sql->fetchEnreg("Select * from codif_matiere where code_alpha='$code_alpha'");
				if(!$enreg["id_matiere"]) $id_matiere=$sql->insert("codif_matiere",array("libelle" => $matiere,"code_alpha" => $code_alpha));
				else $id_matiere=$enreg["id_matiere"];
				$facettes[]="M".$id_matiere;
			}
		}
		// Centres d'interet
		if($this->notice["interet"])
		{
			foreach($this->notice["interet"] as $interet)
			{
				$code_alpha=$this->indexation->alphaMaj($interet);
				if(!$code_alpha) continue;
				$enreg=$sql->fetchEnreg("Select * from codif_interet where code_alpha='$code_alpha'");
				if(!$enreg["id_interet"]) $id_interet=$sql->insert("codif_interet",array("libelle" => $interet,"code_alpha" => $code_alpha));
				else $id_interet=$enreg["id_interet"];
				$facettes[]="F".$id_interet;
				$this->notice["full_dewey"].=$interet." ";
			}
		}
		// Langues
		if($this->notice["langues"])
		{
			foreach($this->notice["langues"] as $langue)
			{
				if (codif_langue::getInstance()->isCodifExists($langue))
					$facettes[]="L".$langue;
				else 
					$this->notice["warnings"][]=array("Code langue non reconnu",$langue);
			}
		}
				
		// Maj enreg facette
		if(!$facettes) return;
		foreach($facettes as $facette)
		{
			if(strpos($this->notice["facettes"],$facette) === false) $this->notice["facettes"].=" ".$facette;
		}
	}

// --------------------------------------------------------------------------------
// Ecrit une notice : article de périodique
// --------------------------------------------------------------------------------
	private function ecrireArticlePeriodique()
	{
		global $sql;

		// identifiants
		$clef_chapeau=$this->notice["clef_chapeau"];
		$clef_numero=$this->notice["clef_numero"];
		$clef_article=$this->notice["clef_article"];
		$clef_unimarc=$this->notice["clef_unimarc"];

		// suppression
		if($this->notice["statut"]==1)
		{
			$this->statut=2;
			if($clef_unimarc) $controle=$sql->execute("delete from notices_articles where clef_unimarc='$clef_unimarc'");
			else $controle=$sql->execute("delete from notices_articles where clef_chapeau='$clef_chapeau' and clef_numero='$clef_numero' and clef_article='$clef_article'" );
			if(!$controle)
			{
				$this->statut=0;
				$this->erreur="notice à supprimer non reconnue";
			}
			return;
		}

		// constitution enregistrement
		$enreg["clef_chapeau"]=$clef_chapeau;
		$enreg["clef_numero"]=$clef_numero;
		$enreg["clef_article"]=$clef_article;
		$enreg["clef_unimarc"]=$clef_unimarc;
		$enreg["unimarc"]=$this->notice["unimarc"];
		$enreg["date_maj"]=dateDuJour(0);
		$enreg["qualite"]=$this->qualite_bib;

		// cherche la notice
		if($clef_unimarc)
		{
			$enreg_existe=$sql->fetchEnreg("select * from notices_articles where clef_unimarc='$clef_unimarc'");
			$enreg["clef_chapeau"]=$enreg_existe["clef_chapeau"];
			$enreg["clef_numero"]=$enreg_existe["clef_numero"];
		}
		else $enreg_existe=$sql->fetchEnreg("select id_article,qualite from notices_articles where clef_chapeau='".$clef_chapeau."' and clef_numero='".$clef_numero."' and clef_article='$clef_article'" );

		// ecrire
		if($enreg_existe["id_article"])
		{
			$id_article=$enreg_existe["id_article"];

			// remplacer si qualite superieure ou egale
			if($enreg["qualite"] >= $enreg_existe["qualite"])
			{
				$sql->update("update notices_articles set @SET@ where id_article=$id_article",$enreg);
				$this->statut=5;
			}
		}
		else
		{
			$sql->insert("notices_articles",$enreg);
			$this->statut=1;
		}
	}

// --------------------------------------------------------------------------------
// Recupere titre, auteurs, matieres pour 1 article de périodique
// --------------------------------------------------------------------------------
	public function getDataArticlePeriodique($unimarc)
	{
		if(!$unimarc) { $this->erreur="notice sans unimarc"; return false; }
		if(!$this->analyseur->ouvrirNotice($unimarc,$this->id_profil,0,"")) { $this->erreur=$this->analyseur->getLastError(); return false; }

		$ret["titres"]=$this->analyseur->getTitres();
		$ret["auteurs"]=$this->analyseur->getAuteurs();
		$ret["matieres"]=$this->analyseur->getMatieres();
		return $ret;
	}

// --------------------------------------------------------------------------------
// Rend la derniere erreur et les derniers warnings
// --------------------------------------------------------------------------------
	public function getLastStatut()
	{
		$ret["statut"]=$this->statut;
		$ret["erreur"]=$this->erreur;
		$ret["warnings"]=$this->notice["warnings"];
		$ret["identification"]=$this->identification["statut"];
		return $ret;
	}
	
// --------------------------------------------------------------------------------
// Rend tout le contenu de la notice
// --------------------------------------------------------------------------------
	public function getNotice()
	{
		if(!$this->notice) $this->notice=array();
		return $this->notice;
	}

// ----------------------------------------------------------------
// Test d'une notice unimarc
// ----------------------------------------------------------------
	public function testNotice($data,$piege_numero="",$piege_titre="",$piege_code_barres="",$piege_isbn="",$piege_type_doc="")
	{
		global $sql;
		
		// lire la notice
		$this->analyseur->ouvrirNotice($data,$this->id_profil,$this->sigb);
		$notice=$this->analyseur->getNoticeIntegration();


		//tracedebug($notice,true);

		// Titre principal
		$ret["titre"]=$notice["titre_princ"];
		
		// Gestion des pieges
		if($piege_numero){$ret["statut"]=0; return $ret;}
		if($piege_titre >"" and $this->indexation->codeAlphaTitre($ret["titre"]) == $this->indexation->codeAlphaTitre($piege_titre)) {$ret["statut"]=1; return $ret;}
		if($piege_code_barres > "" and $notice["statut_exemplaires"]["nb_ex"]>0)
		{
			foreach($notice["exemplaires"] as $ex)
			{
				if($ex["code_barres"]==$piege_code_barres) {$ret["statut"]=1; return $ret;}
			}
		}
		if($piege_isbn > "" and ($notice["isbn10"] == $piege_isbn or $notice["isbn13"] == $piege_isbn)) {$ret["statut"]=1; return $ret;}
		if($piege_type_doc >"" and $notice["infos_type_doc"]["code"]==$piege_type_doc) {$ret["statut"]=1; return $ret; }
		if($piege_titre > "" or $piege_code_barres > "" or $piege_isbn > "" or $piege_type_doc >""){$ret["statut"]=0; return $ret;}
		
		// Type de document
		$td=$notice["infos_type_doc"];
		if(!$td["code"]){	$statut=1; $libelle=" code non reconnu"; $ret["type_doc"]=0; } 
		else {$statut=0; $libelle=$td["code"]." - ".$td["libelle"]; $ret["type_doc"]=$td["code"]; }
		$ret["lig"][]=array($statut,"Type&nbsp;de&nbsp;document", $libelle,$td["infos"]);

		// Statut : 0=notice vivante 1=à détruire
		$lig[0]=0;
		$lig[1]="Statut";
		$lig[2]="ok";
		if($notice["statut"]==1)
		{
			$lig[0]=3;
			$lig[2]='<font color="purple">Notice à supprimer</font>';
		}
		else if($td["code"]!="100")
		{
			if($ret["titre"] == "") {$lig[0]=2; $lig[2]="pas de titre principal";$ret["lig"][]=$lig;}
			if($notice["statut_exemplaires"]["nb_ex"]==0)
			{
				$lig[0]=2;
				$lig[2]="pas de zone 995";
			}
			else
			{
				if($notice["statut_exemplaires"]["codes_barres"]==0) {$lig[0]=1; $lig[2]="code-barres non trouvé";}
				if($notice["statut_exemplaires"]["cotes"]==0) {$lig[0]=1; $lig[2] ="cote non trouvée";}
				if($notice["exportable"]==false)$lig[3]='<font color="purple">notice non libre de droits</font>'; else $lig[3]="notice libre de droits";
			}
		}
		$ret["lig"][]=$lig;

		// articles de periodiques
		if($td["code"]== "100")
		{
			$lig[0]=0;
			$lig[1]="titre du numéro";
			$lig[2]=$notice["titre_numero"];
			$ret["lig"][]=$lig;

			$lig[0]=0;
			$lig[1]="identifiant 001";
			$lig[2]=$notice["info_id"];
			$ret["lig"][]=$lig;
		}
		
		// tout sauf articles de periodiques
		else
		{		
			// exemplaires
			$ret["nb_ex"]=$notice["statut_exemplaires"]["nb_ex"];
			$lig[0]=0;
			$lig[1]="Exemplaires";
			$lig[2]=$notice["statut_exemplaires"]["nb_ex"]." ex. actif(s)";
			$lig[3]="";
			if($notice["statut_exemplaires"]["nb_ex_detruits"] > 0 ) $lig[3]=$notice["statut_exemplaires"]["nb_ex_detruits"]." ex. à supprimer";
			$ret["lig"][]=$lig;
		
			// Identifiants (isbn / ean)
			$isbn=$this->analyseur->getIsbn();
			if(!$isbn) $isbn=$this->analyseur->getEan();
		
			$lig[0]=0;
			$lig[1]="Identifiant";
			$lig[2]="";
			$lig[3]="";
			if($isbn["multiple"]==true)
			{
				$lig[0]=1;
				$lig[2]="Isbn multiple";
				$lig[3]=$isbn["isbn"];
			}
			if($isbn["statut"] == 1)
			{
				$lig[0]=1;
				$lig[2]=$isbn["erreur"];
				$lig[3]=$isbn["code_brut"];
			}
			else
			{
				if($isbn["isbn"]) {$lig[2]="isbn"; $lig[3]=$isbn["isbn"];}
				if($isbn["ean"]) {$lig[2]="ean"; $lig[3]=$isbn["ean"];}
			}
			$ret["lig"][]=$lig;
		
			// Identifiants ID_COMMERCIALE
			$lig[0]=0;
			$lig[1]="No commercial";
			$lig[2]=$notice["id_commerciale"];
			$lig[3]="";
			$ret["lig"][]=$lig;
		}
		
		// Donnes principales
		$titre=$this->analyseur->getTitres();
		$auteur=$this->analyseur->getAuteurs();
		$lig[0]=0;
		$lig[1]="Données&nbsp;principales";
		$lig[2]="titre(s) : ".count($titre);
		$lig[3]="auteur(s) : ".count($auteur);
		$ret["lig"][]=$lig;
		
		if($td["code"]!= "100")  // tout sauf articles de periodiques
		{
			// section
			$lig[1]="Section";
			if(!$notice["sections"]) {$lig[0]=1; $lig[2]="non reconnue";}
			else
			{
				$lig[0]=0;
				$lig[2]="";
				foreach($notice["sections"] as $section) $lig[2].=$sql->fetchOne("select libelle from codif_section where id_section=".$section).BR;
			}
			$lig[3]="";
			$ret["lig"][]=$lig;
		
			// genre
			$lig[1]="Genre";
			if(!$notice["genres"]) {$lig[0]=1; $lig[2]="non reconnu";}
			else
			{
				$lig[0]=0;
				$lig[2]="";
				foreach($notice["genres"] as $genre) $lig[2].=$sql->fetchOne("select libelle from codif_genre where id_genre=".$genre).BR;
			}
			$lig[3]="";
			$ret["lig"][]=$lig;

			// emplacement
			$lig[1]="Emplacement";
			if(!$notice["emplacements"]) {$lig[0]=1; $lig[2]="non reconnu";}
			else
			{
				$lig[0]=0;
				$lig[2]="";
				foreach($notice["emplacements"] as $emplacement) $lig[2].=$sql->fetchOne("select libelle from codif_emplacement where id_emplacement=".$emplacement).BR;
			}
			$lig[3]="";
			$ret["lig"][]=$lig;

			// Champs forcés
			$lig[0]=0;
			$lig[1]="Champs à conserver";
			$lig[2]="";
			$lig[3]="";
			if(count($notice["champs_forces"]) > 0)
			{
				foreach($notice["champs_forces"] as $zone => $champ) $lig[2].= substr($zone,1)." ";
			}
			else $lig[2]="aucun";
			$ret["lig"][]=$lig;
		}
		
		// Statut general du retour
		$ret["statut"]=0;
		for($i=0; $i < count($ret["lig"]); $i++)
		{
			if($ret["lig"][$i][0]==2)
			{
				$ret["statut"]=2;
				break;
			}
			if($ret["lig"][$i][0]==1) $ret["statut"]=1;
			if($ret["lig"][$i][0]==3 and $ret["statut"] <2) $ret["statut"]=3;
		}
		return $ret;
	}
// ----------------------------------------------------------------
// Analyse de synthese d'une notice unimarc
// ----------------------------------------------------------------
	public function syntheseNotice($data)
	{
		global $sql;
		
		$this->analyseur->ouvrirNotice($data,$this->id_profil,$this->sigb);
		$notice=$this->analyseur->getNoticeIntegration();
	
		// Notice à supprimer
		if($notice["statut"]==1) {$ret["statut"]="suppr"; return $ret;}
		
		// Notice rejetee
		$ret["statut"]="rejet";
		if(!$notice["titre_princ"]) {$ret["rejet"]="notice sans titre"; return $ret;}
		if($notice["type_doc"]!="100" and !$notice["statut_exemplaires"]["nb_ex"]) {$ret["rejet"]="notice sans exemplaire"; return $ret;}
		
		// Warnings
		$ret["statut"]="warning";
		if($notice["type_doc"]!="100")
		{
			if(!$notice["statut_exemplaires"]["codes_barres"]) $ret["warnings"][]="code-barres non trouvé";
			if(!$notice["statut_exemplaires"]["cotes"]) $ret["warnings"][]="cote non trouvée";
		}
		if(!$notice["type_doc"])$ret["warnings"][]="Type de document non reconnu";
		
		// Type de doc
		if($notice["type_doc"]) $ret["type_doc"]=$notice["infos_type_doc"]["libelle"];
		
		// Identifiants (isbn / ean)
		if($notice["type_doc"]!="100")
		{
			$isbn=$this->analyseur->getIsbn();
			if(!$isbn) $isbn=$this->analyseur->getEan();
			if($isbn["multiple"]==true) $ret["warnings"][]="Isbn multiple";
			if($isbn["statut"] == 1) $ret["warnings"][]="ISBN ou EAN incorrect";
			if($isbn["isbn"]) $ret["identifiant"]="ISBN identifié";
			elseif($isbn["ean"])  $ret["identifiant"]="EAN identifié";
			else $ret["identifiant"]="Aucun identifiant";

			// Genres
			if($notice["genres"])
			{
				foreach($notice["genres"] as $genre) $ret["genres"][]=$sql->fetchOne("Select libelle from codif_genre where id_genre='$genre'");
			}
			else $ret["warnings"][]="Code genre non reconnu";

			// Sections
			if($notice["sections"])
			{
				foreach($notice["sections"] as $section) $ret["sections"][]=$sql->fetchOne("Select libelle from codif_section where id_section='$section'");
			}
			else $ret["warnings"][]="Code section non reconnu";

			// Emplacements
			if($notice["emplacements"])
			{
				foreach($notice["emplacements"] as $emplacement) $ret["emplacements"][]=$sql->fetchOne("Select libelle from codif_emplacement where id_emplacement='$emplacement'");
			}
			else $ret["warnings"][]="Code emplacement non reconnu";
		
			// Langues
			if($notice["langues"])
			{
				foreach($notice["langues"] as $langue)
				{
					$controle=$sql->fetchOne("Select libelle from codif_langue where id_langue='$langue'");
					if($controle) $ret["langues"][]=$controle;
					else $ret["warnings"][]="Code langue non reconnu";
				}
			}
		
			// Champs forcés
			if(count($notice["champs_forces"]) > 0)
			{
				foreach($notice["champs_forces"] as $zone => $champ) $ret["zones_forcees"][]= substr($zone,1);
			}
		}
		
		// Notice sans anomalie
		if(!$ret["warnings"]) $ret["statut"]="ok";
		
		// Nombre d'exemplaires
		$ret["nb_ex"]=$notice["statut_exemplaires"]["nb_ex"];
		if($ret["nb_ex"] > 100) $ret["warnings"][]="trop d'exemplaires";
	
		return $ret;
	}

// ----------------------------------------------------------------
// Analyse des valeurs distinctes
// ----------------------------------------------------------------
	public function valeursDistinctes($champs,$data)
	{
		$this->analyseur->ouvrirNotice($data,$this->id_profil,$this->sigb);
		foreach($champs as $champ)
		{
			$zone=substr($champ,0,3);
			$sous_champ=substr($champ,-1);
			$valeur=$this->analyseur->get_subfield($zone,$sous_champ);
			if(!$valeur) $valeur[0] = "non renseigné";
			foreach($valeur as $code)
			{
				$ret[$champ][$code]++;
			}
			//tracedebug(1,$ret,true);
		}
		return $ret;
	}
}
?>