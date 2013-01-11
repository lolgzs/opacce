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
// PARSEUR DE FICHIER
///////////////////////////////////////////////////////////////////////////////////////

class parseur
{
	private $formatFichier;					// 0=Unimarc, 1=Ascii Tabulé
	private $balise_xml="";					// balise xml pour découpage enregs
	private $hFic;									// Handle du fichier en cours de traitement
	private $buffer;								// Buffer de lecture
	private $pointeur_reprise;			// Pointeur d'avancement pour les reprises
	private $taille_fichier;				// Taille du fichier (pour calcul avancement)
	private $last_error="";					// derniere erreur

// ----------------------------------------------------------------
// Ouverture du fichier
// ----------------------------------------------------------------
	public function open($fic,$format,$pointeur_reprise,$id_profil=0)
	{
		$this->formatFichier=$format;
		$this->hFic=@fopen($fic,"rb");
		if(!$this->hFic)
		{
			$this->last_error="Impossible d'ouvrit le fichier :".$fic;
			return false;
		}
		$this->pointeur_reprise=$pointeur_reprise;
		if( $pointeur_reprise > 0 ) fseek($this->hFic,$pointeur_reprise);
		$this->buffer="";
		$this->taille_fichier=filesize($fic);

		// controle format xml
		if($format==4)
		{
			$data=fetchOne("select attributs from profil_donnees where id_profil=$id_profil");
			$attributs=unserialize($data);
			$this->balise_xml=$attributs[5]["xml_balise_abonne"];
			if(!$this->balise_xml)
			{
				$this->last_error="La balise Xml qui sépare les enregistrements n'est pas définie.";
				return false;
			}
		}
		return true;
	}
	
// ----------------------------------------------------------------
// Fermeture du fichier
// ----------------------------------------------------------------
	public function close()
	{
		if($this->hFic) fclose($this->hFic);
		unset($this->hFic);
	}
	
// ----------------------------------------------------------------
// Rend l'enregistrement suivant (statut,data,erreur)
// ----------------------------------------------------------------
	public function nextEnreg()
	{
		$adresse=$this->pointeur_reprise;
		switch($this->formatFichier)
		{
			case 0: // unimarc
				$ret = $this->nextUnimarc($this->hFic); 
				$this->pointeur_reprise += strlen($ret["data"]);
				break;
			case 1: // ascii tabulé
				while($ret["data"]=="" and $ret["statut"]!="eof")
				{
					$ret = $this->nextAscii($this->hFic,chr(9));
					$this->pointeur_reprise=ftell($this->hFic);
				}
				break;
			case 2: // ascii séparé par des ;
				$ret = $this->nextAscii($this->hFic,";");	
				$this->pointeur_reprise=ftell($this->hFic);
				break;
			case 3: // ascii séparé par des |
				$ret = $this->nextAscii($this->hFic,"|");
				$this->pointeur_reprise=ftell($this->hFic);
				break;
			case 4: // Xml
				$ret = $this->nextXml($this->hFic);
				$this->pointeur_reprise += strlen($ret["data"]);
				break;
			case 5: // CSV
				$ret = $this->nextCsv($this->hFic);
				$this->pointeur_reprise=ftell($this->hFic);
				break;
			case 6: // marc21
				$ret = $this->nextUnimarc($this->hFic); 
				$this->pointeur_reprise += strlen($ret["data"]);
				break;
		}
		$ret["adresse"]=$adresse;
		$ret["pointeur_reprise"]=$this->pointeur_reprise;
		if( $ret["statut"] == "eof" ) $this->close();
		$ret["pct"]=intval(($this->pointeur_reprise/$this->taille_fichier)*100);
		return $ret;
	}
// ----------------------------------------------------------------
// Rend un enregistrement par son adresse de début
// ----------------------------------------------------------------
	public function getEnreg($adresse)
	{
		if(!$this->hFic) afficherErreur("Il faut d'abord ouvrir le fichier");
		$this->pointeur_reprise=$adresse;
		$this->buffer="";
		fseek($this->hFic,$adresse);
		return $this->nextEnreg();
	}

// ----------------------------------------------------------------
// UNIMARC
// ----------------------------------------------------------------
	private function nextUnimarc($hFic)
	{
		$long=4096;
		$fin_enreg=chr(30).chr(29);
		$data = $this->buffer;
		
		while(true)
		{
			$pos=strScan($data,$fin_enreg,0);
			if($pos > 0 )
			{
				$this->buffer=substr($data,($pos+2));
				$data=substr($data,0,($pos+2));
				break;
			}
			$enreg=fread($hFic,$long);
			if(feof($hFic))
			{
				if( $enreg == false ) { $ret["statut"]="eof"; return $ret; }
			}
			$data .= $enreg;
		}
		// Verif du format
		if(strMid($data,20,2) != "45" and 1 ==2)
		{
			$ret["erreur"]="Le fichier n'est pas à la norme unimarc";
			$ret["statut"]="erreur";
			$ret["data"]=$data;
			return $ret;
		}
		// Retour ok
		$ret["statut"]="ok";
		$ret["data"]=$data;
		return $ret;
	}

// ----------------------------------------------------------------
// ASCII
// ----------------------------------------------------------------
	private function nextAscii($hFic,$separateur)
	{
		$ret["data"]=trim(fGets($hFic));
		if($separateur != chr(9)) $ret["data"]=str_replace($separateur,chr(9),$ret["data"]);
		if(feof($hFic)) $ret["statut"]="eof";
		else $ret["statut"]="ok";
		return $ret;
	}

// ----------------------------------------------------------------
// XML
// ----------------------------------------------------------------
	private function nextXml($hFic)
	{
		$long=1024;
		$debut_enreg='<'.$this->balise_xml.'>';
		$fin_enreg='</'.$this->balise_xml.'>';
		$data = $this->buffer;

		while(true)
		{
			// trouvé un enreg
			if(!$pos_debut)
			{
				$pos_debut=stripos($data,$debut_enreg);
				if($pos_debut !== false ) $data=substr($data,$pos_debut);
			}
			if($pos_debut !== false)
			{
				$pos_fin=stripos($data,$fin_enreg);
				if($pos_fin !== false )
				{
					$this->buffer=substr($data,($pos_fin+strlen($fin_enreg)));
					$data=substr($data,0,($pos_fin+strlen($fin_enreg)));
					break;
				}
			}

			// lecture buffer
			$enreg=fread($hFic,$long);
			if(feof($hFic))
			{
				if( $enreg == false ) { $ret["statut"]="eof"; return $ret; }
			}
			$data .= $enreg;
		}
		
		// Retour ok
		$ret["statut"]="ok";
		$ret["data"]=$data;
		return $ret;
	}

// ----------------------------------------------------------------
// CSV
// ----------------------------------------------------------------
	private function nextCsv($hFic)
	{
		$ret["data"]=trim(fGets($hFic));
		$ret["data"]=str_replace('","',chr(9),$ret["data"]);
		$ret["data"]=str_replace('"','',$ret["data"]);
		if(feof($hFic)) $ret["statut"]="eof";
		else $ret["statut"]="ok";
		return $ret;
	}

// ----------------------------------------------------------------
//  Renvoie la dernière erreur
// ----------------------------------------------------------------
	public function getLastError()
	{
		return $this->last_error;
	}
}

?>