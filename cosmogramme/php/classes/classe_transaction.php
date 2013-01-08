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
// FICHE TRANSACTION ( Pret / réservation)
///////////////////////////////////////////////////////////////////////////////////////

class transaction
{
	private $id_bib;										// Id bib fichier en cours d'import
	private $pergame;										// Si fichier issu de pergame
	private $type_operation;						// Type de maj creation ou suppression
	private $champs;										// Champs du fichier a importer
	private $type_accents;							// Types d'accents

// ----------------------------------------------------------------
// Init du format
// ----------------------------------------------------------------
	public function setParamsIntegration($id_bib,$type_operation,$id_profil)
	{
		global $sql;
		$this->id_bib=$id_bib;
		$sigb=$sql->fetchOne("select sigb from int_bib where id_bib=$id_bib");
		if(($sigb==1) || ($sigb==13)) $this->pergame=true;
		$this->type_operation=$type_operation;
		$profil=$sql->fetchEnreg("select * from profil_donnees where id_profil=$id_profil");
		$this->type_accents=$profil["accents"];
		$attribs=unserialize($profil["attributs"]);
		$this->champs=explode(";",$attribs[1]["champs"]);
	}

// ----------------------------------------------------------------
// Import d'une fiche prêt
// ----------------------------------------------------------------
	public function importFichePret($data)
	{
		// Transco accents
		$data=$this->changeAccents($data);

		// Données d'import
		$data=explode(chr(9),$data);
		if($this->pergame==true)
		{
			$enreg["ID_PERGAME"]=$data[0];
			$enreg["IDABON"]=$data[1];
			$enreg["ORDREABON"]=$data[2];
			$enreg["EN_COURS"]=$data[3];
			$enreg["DATE_PRET"]=$data[4];
			$enreg["DATE_RETOUR"]=$data[5];
			$enreg["SUPPORT"]=$data[6];
			$enreg["ID_NOTICE_ORIGINE"]=$data[7];
			$enreg["CODE_BARRES"]=$data[11];
			$enreg["NB_PROLONGATIONS"]=$data[9];
		}
		else
		{
			for($i=0; $i<count($this->champs); $i++)
			{
				$colonne=$this->champs[$i];
				$enreg[$colonne]=$data[$i];
			}
		}

		// Completer l'enregistrement
		$enreg["ID_SITE"]=$this->id_bib;
		if((int)$enreg["ORDREABON"] <1 ) $enreg["ORDREABON"]=1;
		$enreg["DATE_PRET"]=rendDate($enreg["DATE_PRET"], 0);
		$enreg["DATE_RETOUR"]=rendDate($enreg["DATE_RETOUR"], 0);

		// Ecrire
		global $sql;
		$id_pret=$sql->fetchOne("select ID_PRET from prets where ID_PERGAME='".$enreg["ID_PERGAME"]."' and ID_SITE=".$this->id_bib);
		if(!$id_pret) $sql->insert("prets",$enreg);
		else $sql->update("update prets set @SET@ where ID_PRET=$id_pret",$enreg);
	}

// ----------------------------------------------------------------
// Import d'une fiche réservation
// ----------------------------------------------------------------
	public function importFicheReservation($data)
	{
		// Transco accents
		$data=$this->changeAccents($data);

		// Données d'import
		$data=explode(chr(9),$data);
		if($this->pergame==true)
		{
			$enreg["ID_PERGAME"]=$data[0];
			$enreg["IDABON"]=$data[1];
			$enreg["ORDREABON"]=$data[2];
			$enreg["SUPPORT"]=$data[3];
			$enreg["ID_NOTICE_ORIGINE"]=$data[4];
			$enreg["DATE_RESA"]=$data[5];
		}
		else
		{
			for($i=0; $i<count($this->champs); $i++)
			{
				$colonne=$this->champs[$i];
				$enreg[$colonne]=$data[$i];
			}
		}

		// Completer l'enregistrement
		$enreg["ID_SITE"]=$this->id_bib;
		if((int)$enreg["ORDREABON"] <1 ) $enreg["ORDREABON"]=1;
		$enreg["DATE_RESA"]=rendDate($enreg["DATE_RESA"], 0);

		// Ecrire
		global $sql;
		$id_resa=$sql->fetchOne("select ID_RESA from reservations where ID_PERGAME='".$enreg["ID_PERGAME"]."' and ID_SITE=".$this->id_bib);
		if(!$id_resa) $sql->insert("reservations",$enreg);
		else $sql->update("update reservations set @SET@ where ID_RESA=$id_resa",$enreg);
	}

// ----------------------------------------------------------------
// Transformation des accents
// ----------------------------------------------------------------
	private function changeAccents($chaine)
	{
		if(!trim($chaine)) return $chaine;
		switch($this->type_accents)
		{
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