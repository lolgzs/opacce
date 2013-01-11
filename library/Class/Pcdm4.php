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

class Class_Pcdm4 extends Storm_Model_Abstract {
  protected $_table_name = 'codif_pcdm4';
  protected $_table_primary = 'id_pcdm4';

// ----------------------------------------------------------------
// Rend une liste pour un champ suggestion
// ----------------------------------------------------------------
	public function getListeSuggestion($recherche,$mode,$limite_resultat)
	{
		// Lancer la recherche
		if($mode=="1") 
		{
			for($i=0; $i < strlen($recherche); $i++) if($recherche[$i] >="0" and $recherche[$i] <= "9") $new.=$recherche[$i];
			$req="select id_pcdm4,libelle from codif_pcdm4 where id_pcdm4 like'".$new."%' order by id_pcdm4 limit ".$limite_resultat;
		}
		if($mode=="2") $req="select id_pcdm4,libelle from codif_pcdm4 where libelle like'".addslashes($recherche)."%' order by id_pcdm4 limit ".$limite_resultat;
		if($mode=="3") $req="select id_pcdm4,libelle from codif_pcdm4 where libelle like'%".addslashes($recherche)."%' order by id_pcdm4 limit ".$limite_resultat;
	
		$resultat=fetchAll($req);
		
		// Mettre l'indice et le libelle
		if(!$resultat) return false;
		foreach($resultat as $enreg)
		{
			$liste[]=array($enreg["id_pcdm4"], $this->formatIndice($enreg["id_pcdm4"])." : ".$enreg["libelle"]);
		}
		
		return $liste;
	}	
	
// ----------------------------------------------------------------
// Rend une liste d'indices par niveau
// ----------------------------------------------------------------
	static function getIndices($pere)
	{
		$sql = Zend_Registry::get('sql');
		if ($pere == "root") 
			 $liste=$sql->fetchAll("select * from codif_pcdm4 where LENGTH(id_pcdm4)=1 order by id_pcdm4");
		else {
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
	static function getLibelle($indice)	{
		$sql = Zend_Registry::get('sql');
		$libelle=$sql->fetchOne("select libelle from codif_pcdm4 where id_pcdm4='$indice'");
		if(!$libelle) $libelle=Class_Pcdm4::formatIndice($indice);
		return $libelle;
	}
}

?>