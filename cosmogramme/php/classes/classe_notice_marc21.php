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
//////////////////////////////////////////////////////////////////////////////////////
// CLASSE MARC21 (Surcharge la classe notice_unimarc)
//////////////////////////////////////////////////////////////////////////////////////

require_once("classe_unimarc.php");

class notice_marc21 extends notice_unimarc
{
	private $notice_unimarc;
	private $map;

// ----------------------------------------------------------------
// Constructeur 
// ----------------------------------------------------------------
	function __construct()
	{
		// mapping des zones [zone unimarc][champ marc21]=[champ unimarc]
		$this->map["001"]="*";
		$this->map["010"]="*";
		$this->map["011"]="*";
		$this->map["071"]="*";
		$this->map["101"]["a"]="a";
		$this->map["101"]["h"]="c";
		$this->map["102"]["a"]="a";
		$this->map["200"]["a"]="a";
		$this->map["200"]["c"]="f";
		$this->map["200"]["b"]="e";
		$this->map["200"]["p"]="i";
		$this->map["200"]["n"]="h";
		$this->map["200"]["h"]="b";
		$this->map["205"]["a"]="a";
		$this->map["210"]["a"]="a";
		$this->map["210"]["b"]="c";
		$this->map["210"]["c"]="d";
		$this->map["215"]["a"]="a";
		$this->map["215"]["b"]="c";
		$this->map["215"]["c"]="d";
		$this->map["215"]["e"]="e";
		$this->map["225"]="*";
		$this->map["300"]="*";
		$this->map["330"]="*";
		$this->map["432"]="*";
		$this->map["461"]["a"]="t";
		$this->map["461"]["t"]="d";
		$this->map["461"]["g"]="v";
		$this->map["464"]="*";
		$this->map["500"]="*";
		$this->map["510"]["a"]="a";
		$this->map["517"]["a"]="a";
		$this->map["517"]["n"]="h";
		$this->map["517"]["p"]="v";
		$this->map["996"]="*";
		$this->map["600"]["a"]="a";
		$this->map["600"]["d"]="y";
		$this->map["600"]["e"]="z";
		$tmp=array("601","602","604","605","606","607","610","615","620","626");
		foreach($tmp as $zone)
		{
			$this->map[$zone]="*";
		}
		$this->map["700"]="*";
		$this->map["701"]="*";
		$this->map["702"]="*";
		$this->map["710"]="*";
		$this->map["711"]="*";
		$this->map["712"]="*";
		$this->map["720"]="*";
		$this->map["721"]="*";
		$this->map["722"]="*";
		$this->map["730"]="*";
		$this->map["741"]="*";
		$this->map["801"]["a"]="b";
		parent::__construct();
	}

// ----------------------------------------------------------------
// Initialisation nouvelle notice
// ----------------------------------------------------------------
	public function ouvrirNotice($data,$id_profil,$sigb=0,$type_doc_force="")	{
		//		parent::ouvrirNotice(utf8_decode($data),$id_profil,$sigb,$type_doc_force);
		parent::ouvrirNotice($data,$id_profil,$sigb,$type_doc_force);
		$this->setNotice($this->marc21ToUnimarc());
		return true;
	}
	
	public function setNotice($string, $type_accents = 0)	{
		parent::setNotice($string, 4);
	}

// ----------------------------------------------------------------
// Transco marc21 to unimarc
// ----------------------------------------------------------------
	private function marc21ToUnimarc()
	{
		$this->notice_unimarc=new iso2709_record();
		$this->notice_unimarc->setNotice("");

		// bloc de label
		$this->notice_unimarc->set_rs($this->inner_guide['rs']);
		$this->notice_unimarc->set_dt($this->inner_guide['dt']);
		$this->notice_unimarc->set_bl($this->inner_guide['bl']);

		// identifiants
		$bloc=$this->get_subfield('001');
		$this->notice_unimarc->add_field("001","",$bloc[0]);
		
		// date de nouveaute
		$bloc=$this->get_subfield('008');
		$this->notice_unimarc->add_field("005","",$bloc[0]);

		$this->traiteZone("020","010");		// isbn
		$this->traiteZone("022","011");		// issn
		$this->traiteZone("028","071");		// publisher number pour les sonores
		$this->traiteZone("041","101");		// langues
		$this->traiteZone("044","102");		// pays
		$this->traiteZone("260","210");		//editeur
		$this->traiteZone("440","225");		//collection
		$this->traiteZone("490","225");		//collection
		$this->traiteZone("300","215");		//collation
		$this->traiteZone("250","205");		// mention d'edition

		// titres
		$this->traiteZone("245","200");
		$this->traiteZone("246","510");	
		$this->traiteZone("130","500");	 // titre uniforme
		$this->traiteZone("505","464");	 // titres de depouillement
		$this->traiteZone("740","517");	 // autres titres
		$this->traiteZone("773","461");	 // chapeau
		$this->traiteZone("765","510");	 // titre original
		$this->traiteZone("780","432");	 // titre précédent pour périodiques
		
		// auteurs
		$this->traiteZone("100","700");
		$this->traiteZone("110","710");
		$this->traiteZone("111","711");
		$this->traiteZone("130","741");
		$this->traiteZone("700","702");
		$this->traiteZone("701","700");
		$this->traiteZone("702","700");
		$this->traiteZone("710","711");
		$this->traiteZone("711","730");
		$this->traiteZone("712","730");
		$this->traiteZone("720","700");
		$this->traiteZone("721","700");
		$this->traiteZone("722","700");
		$this->traiteZone("730","741");

		// matieres
		$this->traiteZone("600","600");
		$this->traiteZone("610","601");
		$this->traiteZone("611","601");
		$this->traiteZone("630","605");
		$this->traiteZone("650","606");
		$this->traiteZone("651","607");
		$this->traiteZone("653","610");
		$this->traiteZone("690","610");
		$this->traiteZone("752","620");
		$this->traiteZone("753","626");

		// notes
		$this->traiteZone("500","300");
		$this->traiteZone("504","300");
		$this->traiteZone("516","300");
		$this->traiteZone("518","300");
		$this->traiteZone("520","330");
		$this->traiteZone("521","300");

		// exemplaires
		$this->traiteZone("999","996");

		// fin
		return $this->marc21_decode($this->notice_unimarc->update());
	}

// ----------------------------------------------------------------
// Traitement d'une zone
// ----------------------------------------------------------------
	private function traiteZone($zone_marc21,$zone_unimarc)
	{
		$blocs=$this->get_subfield($zone_marc21);
		if($blocs)
		{
			foreach($blocs as $bloc)
			{
				$data=$this->decoupe_bloc_champ($bloc);
				$data=$this->mapChamps($zone_unimarc,$data);
				$this->notice_unimarc->add_field($zone_unimarc,"01",$data);
			}
		}
	}

// ----------------------------------------------------------------
// Transformation des sous-champs d'une zone
// ----------------------------------------------------------------
	private function mapChamps($zone,$data)
	{
		if(!$this->map[$zone]) return false;
		if(!$data) return false;
		foreach($data as $item)
		{
			$code=$item["code"];
			$valeur=$item["valeur"];
			if($zone=="601" and substr($valeur,0,3)=="NNN") continue;
			if($this->map[$zone]=="*") $new[]=array($code,$valeur);
			elseif(isset($this->map[$zone][$code])) $new[]=array($this->map[$zone][$code],$valeur);
		}
		return $new;
	}

}
?>