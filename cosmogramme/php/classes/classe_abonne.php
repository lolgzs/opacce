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
// FICHE ABONNE
///////////////////////////////////////////////////////////////////////////////////////

class abonne
{
	private $id_bib;										// Id bib fichier en cours d'import
	private $type_operation;						// Type de maj creation ou suppression
	private $champs;										// Champs du fichier a importer
	private $type_accents;							// Types d'accents

// ----------------------------------------------------------------
// Init du format
// ----------------------------------------------------------------
	public function setParamsIntegration($id_bib,$type_operation,$id_profil)
	{
		$this->id_bib=$id_bib;
		$this->type_operation=$type_operation;
		$profil=fetchEnreg("select * from profil_donnees where id_profil=$id_profil");
		$this->type_accents=$profil["accents"];
		$attribs=unserialize($profil["attributs"]);
		if($profil["format"]==4) $this->champs=$attribs[5]["xml_champs_abonne"];
		else $this->champs=explode(";",$attribs[1]["champs"]);
	}

// ----------------------------------------------------------------
// Import d'une fiche
// ----------------------------------------------------------------
	public function importFiche($data,$format)
	{
		if($format==4) $this->importFicheXml($data);
		else $this->importFicheAscii($data);
	}

// ----------------------------------------------------------------
// Import d'une fiche en ASCII
// ----------------------------------------------------------------
	public function importFicheAscii($data)
	{
		// Transco accents
		$data=$this->changeAccents($data);

		// Données d'import
		$data=explode(chr(9),$data);
		for($i=0; $i<count($this->champs); $i++)
		{
			$colonne=$this->champs[$i];
			if($colonne!="NULL") $enreg[$colonne]=$data[$i];
		}

		// Completer l'enregistrement
		$enreg["ID_SITE"]=$this->id_bib;
		$enreg["LOGIN"]=$enreg["IDABON"];
		$enreg["ROLE"]="abonne_sigb";
		$enreg["ROLE_LEVEL"]=2;
		$enreg["STATUT"]=0;
		if((int)$enreg["ORDREABON"] <1 ) $enreg["ORDREABON"]=1;
		if(strlen($enreg["NAISSANCE"]) == 10) $enreg["NAISSANCE"]=rendDate($enreg["NAISSANCE"], 0);
		$enreg["DATE_DEBUT"]=rendDate($enreg["DATE_DEBUT"], 0);
		$enreg["DATE_FIN"]=rendDate($enreg["DATE_FIN"], 0);
		if(!$enreg["PASSWORD"] and $enreg["NAISSANCE"]) $enreg["PASSWORD"]=rendDate($enreg["NAISSANCE"],1);

		// Ecrire
		global $sql;
		$id_user=$sql->fetchOne("select ID_USER from bib_admin_users where LOGIN='".$enreg["LOGIN"]."' and ID_SITE=".$this->id_bib." and PASSWORD='".$enreg["PASSWORD"]."'");
		if(!$id_user) $sql->insert("bib_admin_users",$enreg);
		else $sql->update("update bib_admin_users set @SET@ where ID_USER=$id_user",$enreg);
	}

// ----------------------------------------------------------------
// Import d'une fiche en XML
// ----------------------------------------------------------------
	public function importFicheXml($data)
	{
		// Transco accents
		$data=$this->changeAccents($data);

		// Données d'import
		$data=new SimpleXMLElement($data);
		$nombre=count($this->champs);
		foreach($this->champs as $champ => $balise)
		{
			if($balise)
			{
				$cmd="\$enreg['$champ']=(string)\$data->".addslashes($balise).";";
				eval($cmd);
			}
		}

		// formattage des dates
		$enreg["NAISSANCE"]=rendDate($enreg["NAISSANCE"], 0);
		$enreg["DATE_DEBUT"]=rendDate($enreg["DATE_DEBUT"], 0);
		$enreg["DATE_FIN"]=rendDate($enreg["DATE_FIN"], 0);
		
		// valeurs par défaut
		$enreg["ID_SITE"]=$this->id_bib;
		$enreg["LOGIN"]=$enreg["IDABON"];
		if(!$enreg["ORDREABON"]) $enreg["ORDREABON"]=1;
		$enreg["ROLE"]="abonne_sigb";
		$enreg["ROLE_LEVEL"]=2;
		$enreg["STATUT"]=0;

		// Ecrire
		$id_user=fetchOne("select ID_USER from bib_admin_users where LOGIN='".$enreg["LOGIN"]."' and ID_SITE=".$this->id_bib);
		if(!$id_user) sqlInsert("bib_admin_users",$enreg);
		else sqlUpdate("update bib_admin_users set @SET@ where ID_USER=$id_user",$enreg);
	}

// ----------------------------------------------------------------
// Transformation des accents
// ----------------------------------------------------------------
	private function changeAccents($chaine)
	{
		if(!trim($chaine)) return $chaine;
		switch($this->type_accents)
		{
			case 2: // Windows
				return utf8_encode($chaine);
			case 3: // Dos
				for($i=0; $i < strlen($chaine); $i++) $new.=$this->dosDecode($chaine[$i]);
				return utf8_encode($new);
			default: return $chaine;
		}
	}

// ----------------------------------------------------------------
// Filtrage des caracteres dos
// ----------------------------------------------------------------
	private function dosDecode($char)
	{
		switch($char)
		{
			case 0xe9: $result = 'é';	break ;
			case 0xe8: $result = 'è';	break ;
			case 0xeb: $result = 'ë';	break ;
			case 0xe4: $result = 'ä';	break ;
			case 0xe2: $result = 'â';	break ;
			case 0xef: $result = 'ï';	break ;
			case 0xcf: $result = 'Ï';	break ;
			case 0xee: $result = 'î';	break ;
			case 0xce: $result = 'Î';	break ;
			case 0xf4: $result = 'ô';	break ;
			case 0xf6: $result = 'ö';	break ;
			case 0xd6: $result = 'Ö';	break ;
			case 0xfc: $result = 'ü';	break ;
			case 0xdc: $result = 'Ü';	break ;
			case 0xfb: $result = 'û';	break ;
			case 0xe7: $result = 'ç';	break ;
			case 0xc7: $result = 'Ç';	break ;
			default: $result = $char;	break;
		}
		return $result;
	}
		
}

?>