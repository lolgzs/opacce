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
// OPAC3 - Moteur de recherche Pergame
//////////////////////////////////////////////////////////////////////////////////////////

class Class_MoteurRecherche
{
	private $ix;																			// Classe d'indexation
	private $limite_facettes=" limit 15000";					// limite pour le calcul des facettes
	private $_translate;

//------------------------------------------------------------------------------------------------------
// Constructeur
//------------------------------------------------------------------------------------------------------
	function __construct()
	{
		$this->ix = new Class_Indexation();
		$this->_translate = Zend_Registry::get('translate');
	}

//------------------------------------------------------------------------------------------------------
// Recherche simple
//------------------------------------------------------------------------------------------------------
	function lancerRechercheSimple($crit)
	{
		extract($crit);
		$selection_sections=$crit["selection_sections"];
		if(trim($selection_bib)=="B") $selection_bib="";
		
		// Analyse de l'expression
		$expressionRecherche=trim($expressionRecherche);
		if(!$expressionRecherche) {
			$ret["statut"]="erreur"; 
			$ret["erreur"]=$this->_translate->_("Tapez une expression &agrave; rechercher"); 
			return $ret;
		}
		
		// Recherche par isbn (1 seul mot et isbn ou ean valide)
		if(strpos($expressionRecherche," ") === false and strlen($expressionRecherche) > 9)
		{
			$cls=new Class_Isbn($expressionRecherche);
			$isbn=$cls->getAll();
			if($isbn["isbn10"])
			{
				$where="Where (isbn='".$isbn["isbn10"]."' or isbn='".$isbn["isbn13"]."') ";
				$mode_isbn=true;
			}
			elseif($isbn["ean"])
			{
				$where="Where ean='".$isbn["ean"]."' ";
				$mode_isbn=true;
			}
		}
		if(!$where)
		{
			$mots=$this->ix->getMots($expressionRecherche);
			$recherche="";
			foreach($mots as $mot)
			{
				$mot=$this->ix->getExpressionRecherche($mot);
				if($mot)
				{
					$ret["nb_mots"]++;
					if($pertinence == true) $recherche.=" ".$mot;
					else $recherche.=" +".$mot;
				}
			}
			$recherche=trim($recherche);
			if(!$recherche)  {
				$ret["statut"]="erreur"; 
				$ret["erreur"]=$this->_translate->_("Il n'y aucun mot assez significatif pour la recherche");
				return $ret;
			}
		
			// Constitution des requetes
			if($pertinence == true) $against=" AGAINST('".$recherche."')";
			else $against=" AGAINST('".$recherche."' IN BOOLEAN MODE)";
			$where="Where MATCH(titres,auteurs,editeur,collection,matieres,dewey)";
		}
		if($facette) {$facette=str_replace("["," +",$facette); $facette=str_replace("]"," ",$facette);}
		if($selection_bib) $facette.="+(".$selection_bib.") ";
		if($selection_sections)
		{
			$facette.="+(".str_replace(";"," S",$selection_sections).") ";
		}
		if($facette) $conditions=" And MATCH(facettes) AGAINST('".$facette."' IN BOOLEAN MODE)";
		if($clef_chapeau) $where="where clef_chapeau='$clef_chapeau'";
		if($type_doc) $conditions.=" And type_doc in(".$type_doc.")";
		if($annexe) $conditions.=" And MATCH(facettes) AGAINST('+Y".$annexe."' IN BOOLEAN MODE)";
		if($avec_vignette) $conditions.=" and url_vignette >'' and url_vignette !='NO'";
		
		if($tri and $tri!="*" and !$pertinence or $mode_isbn==true)
		{
			$select = "select id_notice from notices ";
			if($tri) $order_by=" order by ".$tri;
		}
		else
		{
			$filtre_against=str_replace("+"," ",$against);
			$filtre_against=str_replace("IN BOOLEAN MODE","",$filtre_against);
			$select="select id_notice,MATCH(alpha_titre) ".$filtre_against." as rel1, ";
			$select.="MATCH(alpha_auteur)".$filtre_against." as rel2 ";
			$select.="from notices ";
			$order_by=" order by (rel1 * 1.5)+(rel2) desc";
		}

		$req_liste = $select.$where.$against.$conditions.$order_by;
		$req_comptage = "Select count(*) from notices ".$where.$against.$conditions;
		$req_facettes = "select id_notice,type_doc,facettes from notices ".$where.$against.$conditions.$this->limite_facettes;

		// Lancer les requetes
		$nb=fetchOne($req_comptage);
		if(!$nb)
		{
			$ret["statut"]="erreur";
			$ret["erreur"]=$this->_translate->_("Aucun résultat trouvé");
			$this->addStatEchec(1,$crit);
			return $ret;
		}

		$ret["nombre"]=$nb;
		$ret["req_liste"]=$req_liste;
		$ret["req_facettes"]=$req_facettes;
		return $ret;
	}
	
//------------------------------------------------------------------------------------------------------
// Recherche Avancée
//------------------------------------------------------------------------------------------------------
	function lancerRechercheAvancee($crit)
	{
		extract($crit);
		if(trim($selection_bib)=="B") $selection_bib="";
		if($section) $selection_sections="S".$section;
		else $selection_sections=$crit["selection_sections"];

		// Analyse des expressions
		foreach($crit as $clef => $valeur)
		{
			if(!$valeur) continue;
			// Champs Fulltext
			if(substr($clef,0,5) == "rech_")
			{
				// Commence par
				if($type_recherche == "commence") $recherche = $this->ix->alphaMaj($valeur);
				
				// Champs Fulltext
				else
				{
					$recherche="";
					$mots=$this->ix->getMots($valeur);
					foreach($mots as $mot)
					{
						$mot=$this->ix->getExpressionRecherche($mot);
						if($mot)
						{
							if($pertinence == true) $recherche.=" ".$mot;
							else $recherche.=" +".$mot;
						}
					}
				}
				$recherche=trim($recherche);
				if(!$recherche) continue;
				$champ=substr($clef,5);
				
				// Opérateur (et ou sauf)
				$operateur=$crit["operateur_".$champ];
				if($conditions) $conditions.=" ".$operateur." ";
				elseif(striPos($operateur,"NOT")) $conditions.=" not ";
				
				// Type de recherche
				if($type_recherche == "fulltext") $conditions.="MATCH(".$champ.") AGAINST('".$recherche."' IN BOOLEAN MODE) ";
				else $conditions.=$champ." like '".$recherche."%'";
			}
		}
		
		if(!$conditions)
		{
			//$ret["statut"]="erreur";
			//$ret["erreur"]=$this->_translate->_("Il n'y aucun mot assez significatif pour la recherche");
			//return $ret;
			$conditions="1";
		}
		
		// Conditions sur champs
		if($facette) {$facette=str_replace("["," +",$facette); $facette=str_replace("]"," ",$facette);}
		if($selection_bib) $facette.="+(".$selection_bib.") ";
		if($selection_sections) $facette.="+(".str_replace(";"," S",$selection_sections).") ";
		if($facette) $conditions.=" And MATCH(facettes) AGAINST('".$facette."' IN BOOLEAN MODE)";
		if($type_doc) $conditions.=" And type_doc in(".$type_doc.")";
		if($annee_debut) $conditions.=" and annee >='".$annee_debut."' ";
		if($annee_fin) $conditions.=" and annee <='".$annee_fin."' ";
		if($annexe) $conditions.=" And MATCH(facettes) AGAINST('+Y".$annexe."' IN BOOLEAN MODE)";
		if($nouveaute) 
		{
			$secs=$nouveaute * 30.5 * 24 * 3600;
			$date=time() - $secs;
			$date=date("Y-m-d",$date);
			$conditions.=" and date_creation >'".$date."' ";
		}
		if($tri > "") $order_by=" order by ".$tri;

		// Finalisation des requetes
		$req_notices = "Select id_notice from notices Where ".$conditions.$order_by;
		$req_comptage = "Select count(*) from notices  Where ".$conditions;
		$req_facettes = "select id_notice,type_doc,facettes from notices Where ".$conditions.$this->limite_facettes;

		// Lancer les requetes
		$nb=fetchOne($req_comptage);
		if(!$nb) 
		{
			$ret["statut"]="erreur"; 
			$ret["erreur"]=$this->_translate->_("Aucun résultat trouvé");
			$this->addStatEchec(2,$crit);
			return $ret;
		}
		$ret["nombre"]=$nb;
		$ret["req_liste"]=$req_notices;
		$ret["req_facettes"]=$req_facettes;
		return $ret;
	}
	
//------------------------------------------------------------------------------------------------------
// Recherche guidée
//------------------------------------------------------------------------------------------------------
	function lancerRechercheGuidee($indice,$fil_ariane,$selection_bib)
	{
		// Fil d'ariane
		$ret["fil_ariane"]=$this->getFilAriane($fil_ariane,$indice);
		// Racine
		if(!$indice) $rubriques=array("X1","X2");
		else $rubriques=$this->getRubriquesGuidees($indice);
		
		// Tableau des rubriques
		if($rubriques)
		{
			foreach($rubriques as $rubrique)
			{
				$libelle=Class_Codification::getLibelleFacette($rubrique);
				$url=BASE_URL."/opac/recherche/guidee?rubrique=".$rubrique;
				$ret["rubriques"][]=array("libelle" => $libelle,"url" => $url);
			}
		}
		// Liste des notices
		$type=$indice[0];
		if($type=="D" or $type=="P") 
		{
			$indice ="+".$indice."*";
			$facette=$indice;
			if($selection_bib) $facette.=$selection_bib;
			$req=" from notices Where MATCH(facettes) AGAINST('".$facette."' IN BOOLEAN MODE)";		
			$req_comptage="select count(*)".$req;
			$ret["nombre"]=fetchOne($req_comptage);
			$ret["req_liste"]="select id_notice".$req ." order by alpha_titre";
			$ret["req_facettes"]="select id_notice,type_doc,facettes".$req;
		}
		return $ret;
}
//------------------------------------------------------------------------------------------------------
// Recherche rebondissante
//------------------------------------------------------------------------------------------------------
	function lancerRechercheRebond($recherche)
	{
		// Parametres
		$type_doc=$recherche["type_doc"];
		$selection_bib=$recherche["selection_bib"];
		$rebond=$recherche["code_rebond"];
		$facette=$recherche["facette"];

		// Constitution des requetes
		if($facette) {$facette=str_replace("["," +",$facette); $facette=str_replace("]"," ",$facette);}
		$facette.="+".$rebond." ";
		if($selection_bib) $facette.=$selection_bib;
		$conditions=" Where MATCH(facettes) AGAINST('".$facette."' IN BOOLEAN MODE)";
		if($type_doc) $conditions.=" And type_doc in(".$type_doc.")";
		$order_by=" order by alpha_titre";

		$req_liste = "select id_notice from notices ".$conditions.$order_by;
		$req_comptage = "Select count(*) from notices ".$conditions;
		$req_facettes = "select id_notice,type_doc,facettes from notices ".$conditions.$this->limite_facettes;
	
		// Lancer les requetes
		$nb=fetchOne($req_comptage);
		if(!$nb) 
		{
			$ret["statut"]="erreur"; 
			$ret["erreur"]=$this->_translate->_("Aucun résultat trouvé");
			return $ret;
		}
		$ret["nombre"]=$nb;
		$ret["req_liste"]=$req_liste;
		$ret["req_facettes"]=$req_facettes;
		
		return $ret;
	}
//------------------------------------------------------------------------------------------------------
// recup des rubriques guidees
//------------------------------------------------------------------------------------------------------
	private function getRubriquesGuidees($tag)
	{
		$rubrique=$tag[0];
		$id=substr($tag,1);
		
		// Rubriques racine
		if($rubrique == "X")
		{
			switch(intval($id))
			{
				case 1: 	// dewey
					$liste=Class_Dewey::getIndices("root");
					foreach($liste as $indice) $items[]="D".$indice["id_dewey"];
					break;
				case 2: 	// pcdm4
					$liste=Class_Pcdm4::getIndices("root");
					foreach($liste as $indice) $items[]="P".$indice["id_pcdm4"];
					break;
			}
		}
		// Dewey
		if($rubrique == "D")
		{
			$liste=Class_Dewey::getIndices($id);
			if(!$liste) return false;
			foreach($liste as $indice) $items[]="D".$indice["id_dewey"];
		}
		// Pcdm4
		if($rubrique == "P")
		{
			$liste=Class_Pcdm4::getIndices($id);
			if(!$liste) return false;
			foreach($liste as $indice) $items[]="P".$indice["id_pcdm4"];
		}
		return $items;
	}
// ----------------------------------------------------------------
// Fil d'ariane pour le catalogue guidé
// ----------------------------------------------------------------
	private function getFilAriane($fil_ariane_session,$rubrique)	{
		$fil_ariane = null;
		$ret = array('rubriques' => array());

		if($fil_ariane_session) 
			$elems=explode(";",$fil_ariane_session);

		$elems[]=$rubrique;
		$ret["liens"][]=array("libelle" => $this->_translate->_("Accueil"),"url" => BASE_URL."/opac/recherche/guidee?statut=reset");
		foreach($elems as $elem)
		{
			if(!$elem) continue;
			$fil_ariane.=";".$elem;
			$url=BASE_URL."/opac/recherche/guidee?rubrique=".$elem;
			$libelle=Class_Codification::getLibelleFacette($elem);
			$ret["liens"][]=array("libelle" => $libelle,"url" => $url);
			if($elem == $rubrique) break;
		}

		$ret["fil"]=$fil_ariane;
		return $ret;
	}


	public function fetchAll($req) {
		return Zend_Registry::get('sql')->fetchAll($req);
	}


// ----------------------------------------------------------------
// Facettes 
// ----------------------------------------------------------------
function getFacettes($req,$preferences)
	{
		// Preferences
		$p_facette=array("nombre" => $preferences["facettes_nombre"],"actif" => $preferences["facettes_actif"],"types" => "T".$preferences["facettes_codes"]);
		$p_facette["nombre"]=$p_facette["nombre"]*2; // pour afficher les n suivants
		$p_tag=array("actif" => $preferences["tags_actif"],"types" => $preferences["tags_codes"],"nombre" => $preferences["tags_nombre"]);
		if(!$p_facette["actif"] and !$p_tag["actif"]) return array();
		$titres=array("T" => $this->_translate->_("Type de document"),
									"B" => $this->_translate->_("Bibliothèque"),
									"A" => $this->_translate->_("Auteur"),
									"D" => $this->_translate->_("Dewey"),
									"F" => $this->_translate->_("Centre d'intérêt"),
									"P" => $this->_translate->_("Pcdm4"),
									"M" => $this->_translate->_("Sujet"),
									"L" => $this->_translate->_("Langue"),
									"G" => $this->_translate->_("Genre"), 
									"S" => $this->_translate->_("Section"),
									"Y" => $this->_translate->_("Site"));

		$l=getVar("PCDM4_LIB"); if(trim($l)) $titres["P"]=$l;
		$l=getVar("DEWEY_LIB"); if(trim($l)) $titres["D"]=$l;
		
		// Lecture des notices
		$rows = $this->fetchAll($req);
		$facettes = array();
		foreach($rows as $notice)	
		{
			$items=explode(" ",trim($notice["facettes"]));
			foreach($items as $item)
			{ 
				$type=substr($item,0,1);

				if (!array_key_exists($type, $facettes))
					$facettes[$type] = array();

				if (!array_key_exists($item, $facettes[$type]))
					$facettes[$type][$item] = 0;

				$facettes[$type][$item]++;
			}
		}
		
		// Constituer le tableau des facettes
		$table=array();
		for($i=0; $i<strlen($p_facette["types"]); $i++)
		{
			$type=$p_facette["types"][$i];
			if (!array_isset($type, $facettes)) continue;
			arsort($facettes[$type]);
			$sorted[$type]=true;
			$table["facettes"][$type]["titre"]=$titres[$type];
			$nb=0;
			foreach($facettes[$type] as $clef => $nombre)
			{
				$nb++;
				if($nb > $p_facette["nombre"]) break;
				$table["facettes"][$type][$nb]["id"]=$clef;
				$table["facettes"][$type][$nb]["libelle"]=Class_Codification::getLibelleFacette($clef);
				$table["facettes"][$type][$nb]["nombre"]=$nombre;
			}
		}
		if(!$p_tag["actif"]) return $table;
		
		// Constituer le tableau des tags
		$nb=0;
		while(true)
		{
			// On cherche le plus fort pour chaque type
			$controle=array("nombre" => 0);
			$yen_a_plus=true;
			for($i=0; $i<strlen($p_tag["types"]); $i++)
			{
				$type=$p_tag["types"][$i];
				if (!array_isset($type, $facettes)) continue;

				$yen_a_plus=false;
				if(!$sorted[$type]) {arsort($facettes[$type]); $sorted[$type]=true;}
				$lig=array_slice($facettes[$type],0,1);
				$compare=array_values($lig);
				if($compare[0] > $controle["nombre"]) 
				{
					$controle["nombre"]=$compare[0]; 
					$controle["type"]=$type;
					$controle["clef"]=$lig;
				}
			}
				
			// Si max atteint ou plus de facettes c'est fini
			if($yen_a_plus == true) break;
			$nb++;
			if($nb > $p_tag["nombre"]) break;
			
			// On depile l'item des facettes et on empile dans le resultat
			foreach($controle["clef"] as $clef => $nombre);
			array_shift($facettes[$controle["type"]]);
			$table["tags"][$nb]["id"]=$clef;
			$table["tags"][$nb]["libelle"]=Class_Codification::getLibelleFacette($clef);
			$table["tags"][$nb]["nombre"]=$nombre;
		}
		return $table;
	}
	
// ----------------------------------------------------------------
// Stats : recherches infructueuses
// ----------------------------------------------------------------
	private function addStatEchec($type_recherche,$criteres)
	{
		$criteres=addslashes(serialize($criteres));
		sqlExecute("insert into stats_recherche_echec(type_recherche,criteres) values($type_recherche,'$criteres')");
	}
}