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
// OPAC 3 :AUTEURS
///////////////////////////////////////////////////////////////////////////////////////

class Class_Auteur
{

// ----------------------------------------------------------------
// Rend une liste pour un champ suggestion
// ----------------------------------------------------------------
	public function getListeSuggestion($recherche,$mode,$limite_resultat)
	{
		// Lancer la recherche
		$ix=new Class_Indexation();
		$code_alpha=$ix->alphaMaj($recherche);
		$code_alpha=str_replace(" ","x",$code_alpha);
		if($mode=="1") $req="select id_auteur,libelle from codif_auteur where formes like'".$code_alpha."%' order by FORMES limit ".$limite_resultat;
	
		$resultat=fetchAll($req);
		
		// Mettre l'indice et le libelle
		if(!$resultat) return false;
		foreach($resultat as $enreg)
		{
			$liste[]=array($enreg["id_auteur"],$enreg["libelle"]);
		}
		
		return $liste;
	}	


// ----------------------------------------------------------------
// Rend prénom et nom
// ----------------------------------------------------------------
	static function getLibelle($id_auteur)
	{
		$libelle = fetchEnreg("select libelle from codif_auteur where id_auteur='$id_auteur'");
		return $libelle;
	}
}

?>