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
////////////////////////////////////////////////////////////////////////////////////////
// INDICES PCDM4
///////////////////////////////////////////////////////////////////////////////////////

class pcdm4
{
	
// ----------------------------------------------------------------
// Pcdm4 sans libelle
// ----------------------------------------------------------------
	public function getIndicesSanslibelle($indice, $limite)
	{
		global $sql;
		$liste=array();
		if($indice) $where = "and id_pcdm4 like '$indice%'";
		$handle=$sql->prepareListe("Select distinct SUBSTRING(id_pcdm4, 1, $limite) from codif_pcdm4 Where libelle ='' ".$where." order by id_pcdm4");
		while( $indice=$sql->fetchNext($handle,true)) $liste[]=$this->formatIndice($indice);
		return $liste;
	}
// ----------------------------------------------------------------
// Rend une liste d'indices par niveau
// ----------------------------------------------------------------
	static function getIndices($pere)
	{
		global $sql;
		if($pere == "root") $liste=$sql->fetchAll("select * from codif_pcdm4 where LENGTH(id_pcdm4)=1 order by id_pcdm4");
		else 
		{
			$long=strlen($pere)+1;
			$req="select * from codif_pcdm4 where id_pcdm4 like '$pere%' and LENGTH(id_pcdm4)=$long order by id_pcdm4";
			$liste =$sql->fetchAll($req);
		}
		return $liste;
	}
// ----------------------------------------------------------------
// Ponctue un indice pcdm4
// ----------------------------------------------------------------
	static function formatIndice($indice)
	{
		if(strlen($indice)< 2) return $indice;
		$new=substr($indice,0,1).".".substr($indice,1);
		return $new;
	}
// ----------------------------------------------------------------
// Analyse et rend l'indice s'il est valide
// ----------------------------------------------------------------
	static function filtreIndice($indice)
	{
		$indice=trim($indice);
		if(!is_numeric(substr($indice,0,1))) return "";
		if(strlen($indice) > 1 and substr($indice,1,1) != ".") return "";
		$new="";
		for($i=0; $i<strlen($indice); $i++)
		{
			$car=$indice[$i];
			if($car >="0" and $car<="9") $new.=$car;
		}
		return $new;
	}
// ----------------------------------------------------------------
// Rend le libelle ou le code si le libelle est vide
// ----------------------------------------------------------------
	static function getLibelle($indice)
	{
		global $sql;
		$libelle=$sql->fetchOne("select libelle from codif_pcdm4 where id_pcdm4='$indice'");
		if(!$libelle) $libelle=pcdm4::formatIndice($indice);
		return $libelle;
	}
// ----------------------------------------------------------------
// Ecrit un indice pcdm4
// ----------------------------------------------------------------
	public function ecrire($indice, $libelle)
	{
		global $sql;
		
		$indice=trim($indice);
		$indice=str_replace(".","",$indice);
		$libelle=trim(str_replace("'","''",$libelle));
		if($indice=="") return false;
		$controle=$sql->fetchOne("select count(*) from codif_pcdm4 where id_pcdm4='$indice'");
		if($controle > 0)
		{ 
			// Maj du libelle
			$sql->execute("Update codif_pcdm4 set libelle='$libelle' Where id_pcdm4='$indice'");
			// Maj des mots-recherche pour le nouveau libelle
			$this->majFulltext($indice);
		}
		else $sql->execute("insert into codif_pcdm4 (id_pcdm4,libelle) Values('$indice','$libelle')");
		return 1;
	}
	
// ----------------------------------------------------------------
// Maj des mots facettes pour 1 indice
// ----------------------------------------------------------------
	public function majFulltext($indice)
	{
		global $sql;
		require_once("classe_indexation.php");
		$ix = new indexation();
		
		// Verif parametres 
		$indice=trim($indice);
		$indice=str_replace(".","",$indice);
		$code="+P".$indice;
		
		// Get notices de cet indice
		$liste=$sql->prepareListe("select id_notice,facettes from notices where match(facettes) against('".$code."' IN BOOLEAN MODE)");
		if(!$liste) return;
		while($ligne=$sql->fetchNext($liste))
		{
			$id_notice=$ligne["id_notice"];
			// recup id dewey et pcdm4 des facettes
			$facette=explode(" ",$ligne["facettes"]);
			for($i=0; $i < count($facette); $i++)
			{
				if(!$facette[$i]) continue;
				if(substr($facette[$i],0,1) == "D")
				{
					$clef=substr($facette[$i],1);
					$libelle=$sql->fetchOne("select libelle from codif_dewey where id_dewey='$clef'");
					$fulltext.=" ".$libelle;
				}
				elseif(substr($facette[$i],0,1) == "P")
				{
					$clef=substr($facette[$i],1);
					$libelle=$sql->fetchOne("select libelle from codif_pcdm4 where id_pcdm4='$clef'");
					$fulltext.=" ".$libelle;
				}
			}
			// Calcul nouveau fulltext et maj notice
			$fulltext=$ix->getFulltext($fulltext);
			$sql->execute("update notices set dewey='$fulltext' where id_notice=$id_notice");
		}
	}
}

?>