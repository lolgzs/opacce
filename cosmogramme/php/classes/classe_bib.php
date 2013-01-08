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
// FICHE BIBLIOTHEQUE
///////////////////////////////////////////////////////////////////////////////////////

class bibliotheque
{
//---------------------------------------------------------------------------------
// Renvoie le contenu complet des enregs
//---------------------------------------------------------------------------------
	public function getAll($sigb=false)
	{
		global $sql;
		if($sigb == true) $where = " Where sigb > 0 ";
		$bibs=$sql->fetchAll("select * from int_bib ".$where."order by nom_court");
		return $bibs;
	}
	
//---------------------------------------------------------------------------------
// Rend le nom court d'1 bib
//---------------------------------------------------------------------------------
	public function getNomCourt($idBib)
	{
		global $sql;
		if(!$idBib) return '** inconnue **';
		$nom=$sql->fetchOne("select nom_court from int_bib where id_bib=$idBib");
		return stripslashes($nom);
	}

//---------------------------------------------------------------------------------
// Renvoie la liste des bibs qui ont un mail
//---------------------------------------------------------------------------------
	public function getListeRetardIntegration()
	{
		global $sql;
		$bibs=$sql->fetchAll("select * from int_bib where mail > '' and ecart_ajouts > 0 order by nom_court");
		foreach($bibs as $bib)
		{
			$ecart=ecartDates(dateDuJour(0),$bib["dernier_ajout"]);
			if($ecart > $bib["ecart_ajouts"])
			{
				if($bib["dernier_ajout"] == "0000-00-00")
				{
					$bib["dernier_ajout"]="aucune";
					$ecart="jamais intégré";
				}
				else
				{
					$bib["dernier_ajout"]=rendDate($bib["dernier_ajout"],1);
					if($ecart == 1) $ecart="1 jour";
					else $ecart.=" jours";
				}
				$lig=$bib;
				if(!$lig["date_mail"]) $lig["date_mail"]="jamais";			
				$lig["retard"]=$ecart;
				$ret[]=$lig;	
			}
		}
		return $ret;
	}

//---------------------------------------------------------------------------------
// Rend une combo avec les noms courts
//---------------------------------------------------------------------------------
	public function getComboNoms($id_a_selectionner=0)
	{
		$bibs=$this->getAll();
		$combo='<select name="id_bib">';
		foreach($bibs as $bib)
		{
			if($id_a_selectionner == $bib["id_bib"]) $selected=" selected"; else $selected="";
			$combo.='<option value="'.$bib["id_bib"].'"'.$selected.'>'.stripSlashes($bib["nom_court"]).'</option>';
		}
		$combo.='</select>';
		return $combo;
	}
//---------------------------------------------------------------------------------
// Ecrire fiche bib
//---------------------------------------------------------------------------------
	public function ecrire($id_bib,$nom_court,$mail,$qualite,$ecart_ajouts,$sigb,$comm_sigb,$comm_params,$pas_exporter)
	{
		global $sql;
		$nom_court=addslashes($nom_court);
		$mail=addslashes($mail);
		$ecart_ajouts=(int)$ecart_ajouts;
		$comm_params=addslashes(serialize($comm_params));
		$req="update int_bib Set
			nom_court='$nom_court',
			mail='$mail',
			qualite=$qualite,
			ecart_ajouts=$ecart_ajouts,
			sigb=$sigb,
			comm_sigb=$comm_sigb,
			comm_params='$comm_params',
			pas_exporter=$pas_exporter
			Where id_bib=$id_bib";
		$sql->execute($req);
	}

}

?>