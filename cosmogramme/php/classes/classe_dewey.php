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
// INDICES DEWEY
///////////////////////////////////////////////////////////////////////////////////////

class dewey
{
	
// ----------------------------------------------------------------
// Dewey sans libelle
// ----------------------------------------------------------------
	public function getIndicesSanslibelle($indice, $limite)
	{
		global $sql;
		$liste=array();
		if($indice) $where = "and id_dewey like '$indice%'";
		$handle=$sql->prepareListe("Select id_dewey from codif_dewey Where LENGTH(id_dewey)=$limite and libelle ='' ".$where." order by id_dewey");
		while( $indice=$sql->fetchNext($handle,true)) $liste[]=$this->formatIndice($indice);
		return $liste;
	}
	
// ----------------------------------------------------------------
// Rend une liste d'indices par niveau
// ----------------------------------------------------------------
	static function getIndices($pere)
	{
		global $sql;
		if($pere == "root") $liste=$sql->fetchAll("select * from codif_dewey where LENGTH(id_dewey)=1 order by id_dewey");
		else 
		{
			$long=strlen($pere)+1;
			$req="select * from codif_dewey where id_dewey like '$pere%' and LENGTH(id_dewey)=$long order by id_dewey";
			$liste =$sql->fetchAll($req);
		}
		return $liste;
	}
	
// ----------------------------------------------------------------
// Ponctue un indice dewey
// ----------------------------------------------------------------
	static function formatIndice($indice)
	{
		if(strlen($indice)< 4) return $indice;
		$new="";
		while(strlen($indice)>3)
		{
			$new.=substr($indice,0,3).".";
			$indice=substr($indice,3,strlen($indice));
		}
		$new.=$indice;
		return $new;
	}
// ----------------------------------------------------------------
// Analyse et rend l'indice s'il est valide
// ----------------------------------------------------------------
	static function filtreIndice($indice)
	{
		$indice=trim($indice);
		if(strlen($indice)>18) return false;
		if(substr($indice,1,1) == ".") return "";  // si c'est de la pcdm4 on dégage
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
		$libelle=$sql->fetchOne("select libelle from codif_dewey where id_dewey='$indice'");
		if(!$libelle) $libelle=dewey::formatIndice($indice)." - intitulé non renseigné";
		return $libelle;
	}
	
// ----------------------------------------------------------------
// Ecrit un indice dewey
// ----------------------------------------------------------------
	public function ecrire($indice, $libelle)
	{
		global $sql;
		
		$indice=trim($indice);
		$indice=str_replace(".","",$indice);
		$libelle=trim(str_replace("'","''",$libelle));
		if($indice=="") return false;
		$controle=$sql->fetchOne("select count(*) from codif_dewey where id_dewey='$indice'");
		if($controle > 0)
		{ 
			// Maj du libelle
			$sql->execute("Update codif_dewey set libelle='$libelle' Where id_dewey='$indice'");
			// Maj des mots-recherche pour le nouveau libelle
			$this->majFulltext($indice);
		}
		else $sql->execute("insert into codif_dewey (id_dewey,libelle) Values('$indice','$libelle')");
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
		$code="+D".$indice;
		
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