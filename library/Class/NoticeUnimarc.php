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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 :	Notice UNIMARC
//////////////////////////////////////////////////////////////////////////////////////////
class Class_NoticeUnimarc {

	private $type_accents;
	private $tracer_accents;
	// enregistrement UNIMARC complet
	protected $full_record;
	// parties de l'enregistrement UNIMARC
	protected $guide = '';
	protected $data = '';
	// propriétés 'publiques'
	protected $errors;
	// variables 'internes' de la classe
	protected $inner_data;
	// caractères spéciaux
	protected $record_end;
	protected $rgx_record_end;
	protected $field_end;
	protected $rgx_field_end;
	protected $subfield_begin;
	protected $rgx_subfield_begin;
	protected $NSB_begin;
	protected $rgx_NSB_begin;
	protected $NSB_end;
	protected $rgx_NSB_end;

// ---------------------------------------------------
// constructeur : init des constantes
// ---------------------------------------------------
	function __construct() 
	{
		$this->tracer_accents=false;
		// initialisation des caractères spéciaux
		$this->record_end = chr(0x1d);					// fin de notice (IS3 de l'ISO 6630)
		$this->rgx_record_end = "\x1D";
		$this->field_end = chr(0x1e);						// fin de champ (IS2 de l'ISO 6630)
		$this->rgx_field_end ="\x1E";
		$this->subfield_begin = chr(0x1f);			// début de sous-champ (IS1 de l'ISO 6630)
		$this->rgx_subfield_begin = "\x1F";
		$this->NSB_begin = chr(0x88);						// début de NSB
		$this->rgx_NSB_begin = "\x88";
		$this->NSB_end = chr(0x89);							// fin de NSB (NSE)
		$this->rgx_NSB_end = "\x89";

		$this->errors = array();
	}


	public function hasNotice() {
			return !empty($this->full_record);
	}

	
// ---------------------------------------------------
// Decoupage de l'enregistrement
// ---------------------------------------------------
	public function setNotice($string, $type_accents = 0)	{
		$this->type_accents = $type_accents;
		// récupération de l'enregistrement intégral
		$this->full_record = $string; 
		
		// guide de l'enregistrement
		$this->guide = substr($this->full_record, 0, 24);

		// guide interne : valeurs par défaut si création
		$ba = (int)substr($this->guide, 12, 5);			// base adress : pos.12-16	
		$ba = $ba ? $ba : 24;

		$dm1 = (int)$this->guide[20];			// Length of 'Length of field' (pos.20, 4 in UNIMARC) 
		$dm1 = $dm1 ? $dm1 : 4;

		$dm2 = (int)$this->guide[21];			// Length of 'Starting character position' (pos.21, 5 in UNIMARC)
		$dm2 = $dm2 ? $dm2 : 5;

		// récupération du répertoire
		$m = 3 + $dm1 + $dm2;
		$directory = substr($this->full_record,	24,	$ba - 25);
		$tmp_dir = array_filter(explode('|', chunk_split($directory, $m, '|')));

		$adress_length = 3 + $dm1;
		foreach ($tmp_dir as $i => $dir) {
			$label = substr($dir, 0, 3);
			if (!isset($this->inner_data[$label]))
				$this->inner_data[$label] = [];

			$this->inner_data[$label][] = substr($this->full_record,
																					 $ba + (int)substr($dir, $adress_length, $dm2),
																					 (int)substr($dir, 3, $dm1));
		}
	}
	

// ---------------------------------------------------
// 		récupération d'un ou plusieurs sous-champ(s)
// ---------------------------------------------------

	/**
	 * Premier argument label de champ obligatoire
	 * Puis liste de sous-champs sinon renvoit le bloc entier
	 */
	public function get_subfield() {
		$result = [];
		
		if (!$args = func_get_args()) 
			return $result;

		$inner_data_label = $args[0];
		$subfields = array_slice($args, 1);

		if (!isset($this->inner_data[$inner_data_label]))
			return $result;

    foreach($this->inner_data[$inner_data_label] as $content)
			$this->_getSubfieldInContent($content, $subfields, $result);
		
		return $result;
	}


	protected function _getSubfieldInContent($content, &$subfields, &$result) {
		if (0 == count($subfields)) {
			$result[] = preg_replace('/' . $this->rgx_field_end .'/', '', $content);
			return;
		}

		// un seul sous-champ demandé
		if (1 == count($subfields)) {
			$mask = $this->_getPatternForSubfield($subfields[0]);
			while (preg_match($mask, $content, $regs)) {
				$result[] = $regs[1];
				$content = preg_replace($mask, '', $content);
			}
			return;
		}

		// plusieurs sous-champs
		foreach ($subfields as $subfield) {
			preg_match($this->_getPatternForSubfield($subfield), $content, $regs);
			$tmp[$subfield] = $regs[1]; 
		}
		$result[] = $tmp;
	}


	protected function _getPatternForSubfield($subfield) {
		return '/' . $this->rgx_subfield_begin . $subfield
			. '(.*)['.$this->rgx_subfield_begin.'|'.$this->rgx_field_end.']'
			. '/sU';
	}


// ----------------------------------------------------------------
// Rend un tableau de valeurs a partir d'un champ inner_data
// ----------------------------------------------------------------
	public function getValeursBloc($bloc)
	{		
		$bloc=substr($bloc,3);
		$champs=split($this->subfield_begin,$bloc);
		for($j=0;$j<count($champs); $j++) $valeur[]=substr($champs[$j],1);
		return $valeur;
	}


// ---------------------------------------------------
// Decoupe un bloc zone en sous-champs
// ---------------------------------------------------
	public function decoupe_bloc_champ($bloc)
	{
		$bloc=substr($bloc,3);
		$champs=explode($this->subfield_begin,$bloc);
		for($j=0;$j<count($champs); $j++)
		{
			$sc[$j]["code"]=substr($champs[$j],0,1);
			$sc[$j]["valeur"]=substr($champs[$j],1);
		}
		return $sc;
	}


	function ISO_encode($chaine) {
		if(!$chaine) return $chaine;

		$char_table['À'] = chr(0xc1).chr(0x41);
		$char_table['Á'] = chr(0xc2).chr(0x41);
		$char_table['Â'] = chr(0xc3).chr(0x41);
		$char_table['Ã'] = chr(0xc4).chr(0x41);
		$char_table['Ä'] = chr(0xc9).chr(0x41);
		$char_table['Å'] = chr(0xca).chr(0x41);
		$char_table['Å'] = chr(0xca).chr(0x41);
		$char_table['Ç'] = chr(0xd0).chr(0x43); 
		$char_table['È'] = chr(0xc1).chr(0x45);
		$char_table['É'] = chr(0xc2).chr(0x45);
		$char_table['Ê'] = chr(0xc3).chr(0x45);
		$char_table['Ë'] = chr(0xc8).chr(0x45);
		$char_table['Ì'] = chr(0xc1).chr(0x49);
		$char_table['Í'] = chr(0xc2).chr(0x49);
		$char_table['Î'] = chr(0xc3).chr(0x49);
		$char_table['Ï'] = chr(0xc8).chr(0x49);
		$char_table['Ñ'] = chr(0xc4).chr(0x4e);
		$char_table['Ò'] = chr(0xc1).chr(0x4f);
		$char_table['Ó'] = chr(0xc2).chr(0x4f);
		$char_table['Ô'] = chr(0xc3).chr(0x4f);
		$char_table['Õ'] = chr(0xc4).chr(0x4f);
		$char_table['Ö'] = chr(0xc9).chr(0x4f);
		$char_table['Ù'] = chr(0xc1).chr(0x55);
		$char_table['Ú'] = chr(0xc2).chr(0x55);
		$char_table['Û'] = chr(0xc3).chr(0x55);
		$char_table['Ý'] = chr(0xc2).chr(0x59);
		$char_table['à'] = chr(0xc1).chr(0x61);
		$char_table['á'] = chr(0xc2).chr(0x61);
		$char_table['â'] = chr(0xc3).chr(0x61);
		$char_table['ã'] = chr(0xc4).chr(0x61);
		$char_table['ä'] = chr(0xc9).chr(0x61);
		$char_table['å'] = chr(0xca).chr(0x61);
		$char_table['ç'] = chr(0xd0).chr(0x63);
		$char_table['è'] = chr(0xc1).chr(0x65);
		$char_table['é'] = chr(0xc2).chr(0x65);
		$char_table['ê'] = chr(0xc3).chr(0x65);
		$char_table['ë'] = chr(0xc8).chr(0x65);
		$char_table['ñ'] = chr(0xc4).chr(0x6e);
		$char_table['ì'] = chr(0xc1).chr(0x69);
		$char_table['í'] = chr(0xc2).chr(0x69);
		$char_table['î'] = chr(0xc3).chr(0x69);
		$char_table['ï'] = chr(0xc8).chr(0x69);
		$char_table['ò'] = chr(0xc1).chr(0x6f);
		$char_table['ó'] = chr(0xc2).chr(0x6f);
		$char_table['ô'] = chr(0xc3).chr(0x6f);
		$char_table['õ'] = chr(0xc4).chr(0x6f);
		$char_table['ö'] = chr(0xc9).chr(0x6f);
		$char_table['ù'] = chr(0xc1).chr(0x75);
		$char_table['ú'] = chr(0xc2).chr(0x75);
		$char_table['û'] = chr(0xc3).chr(0x75);
		$char_table['ü'] = chr(0xc9).chr(0x75);
		$char_table['ý'] = chr(0xc2).chr(0x79);
		$char_table['ÿ'] = chr(0xc8).chr(0x79);
		$char_table['Æ'] = chr(0xe1);
		$char_table['Ø'] = chr(0xe9);
		$char_table['þ'] = chr(0xec);
		$char_table['æ'] = chr(0xf1);
		$char_table['ð'] = chr(0xf3);
		$char_table['ø'] = chr(0xf9);
		$char_table['ß'] = chr(0xfb);

 		while(list($char, $value) = each($char_table))
			$chaine = preg_replace("/$char/", $value, $chaine);

		return $chaine;

	}
}

?>
