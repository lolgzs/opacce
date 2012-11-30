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
	protected $directory = '';
	protected $data = '';
	// propriétés 'publiques'
	protected $errors;
	// variables 'internes' de la classe
	protected $inner_guide;
	protected $inner_directory;
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
		$this->reset_notice();
		$this->type_accents = $type_accents;
		// récupération de l'enregistrement intégral
		$this->full_record = str_replace('\\', '/', $string);
		
		// guide de l'enregistrement
		$this->guide = substr($this->full_record, 0, 24);

		// guide interne : valeurs par défaut si création
		$rl = intval(substr($this->guide, 0 , 5));			// record length : pos.1-4
		$rs = $this->guide[5];								// record status : pos.5
		$dt = $this->guide[6];								// document type : pos.6	
		$bl = $this->guide[7];								// bibliographic level : pos.7
		$hl = intval($this->guide[8]);				// hierarchical level : pos.8
		$pos9 = $this->guide[9];							// pos.9 undefined, contains a blank
		$il = intval($this->guide[10]);			// indicator length : pos.10 (2)
		$sl = intval($this->guide[11]);			// subfield identifier length : pos.11 (2)	
		$ba = intval(substr($this->guide, 12, 5));			// base adress : pos.12-16	
		$el = $this->guide[17];							// encoding level : pos.17
		$ru = $this->guide[18];							// record update : pos.18
		$pos19 = $this->guide[19];						// pos.19 : undefined, contains a blank
		$dm1 = intval($this->guide[20]);			// Length of 'Length of field' (pos.20, 4 in UNIMARC) 
		$dm2 = intval($this->guide[21]);			// Length of 'Starting character position' (pos.21, 5 in UNIMARC)
		$dm3 = intval($this->guide[22]);			// Length of implementationdefined portion (pos.22, 0 in UNIMARC)
		$pos23 = $this->guide[23];						// POS.23 : undefined, contains a blank

		$this->inner_guide = array(
															 'rl' =>  $rl ? $rl : 0,
															 'rs' =>  $rs ? $rs : 'n',
															 'dt' => $dt ? $dt : 'a',
															 'bl' => $bl ? $bl : 'm',
															 'hl' => $hl ? $hl : 0,
															 'pos9' => $pos9 ? $pos9 : ' ',
															 'il' => $il ? $il : 2,
															 'sl' => $sl ? $sl : 2,
															 'ba' => $ba ? $ba : 24, 
															 'el' => $el ? $el : '1',
															 'ru' => $ru ? $ru : 'i',
															 'pos19' => $pos19 ? $pos19 : ' ',
															 'dm1' => $dm1 ? $dm1 : 4,
															 'dm2' => $dm2 ? $dm2 : 5,
															 'dm3' =>  $dm3 ? $dm3 : 0,
															 'pos23' => $pos23 ? $pos23 : ' '
															 );

		// récupération du répertoire
		$m = 3 + $this->inner_guide['dm1'] + $this->inner_guide['dm2'];
		$this->directory = substr($this->full_record,	24,	$this->inner_guide['ba'] - 25);

		$tmp_dir = explode('|', chunk_split($this->directory, $m, '|'));
		$tmp_dir_count = count($tmp_dir);
		for ($i = 0; $i < $tmp_dir_count; $i++) {
			if ($tmp_dir[$i]) {
				$this->inner_directory[$i] = array('label' => substr($tmp_dir[$i], 0, 3),
																					 'length' => intval(substr($tmp_dir[$i], 3, $this->inner_guide['dm1'])),
																					 'adress' => intval(substr($tmp_dir[$i], 3 + $this->inner_guide['dm1'], $this->inner_guide['dm2'])));
			}
		}

		// récupération des champs
		$m = substr($this->full_record, 
								$this->inner_guide['ba'], 
								strlen($this->full_record) - $this->inner_guide['ba']);

		if ($m) {
			foreach($this->inner_directory as $cle => $valeur) {
				$this->inner_data[$cle] = array('label' => $this->inner_directory[$cle]['label'],
																				'content' => substr($this->full_record,
																														$this->inner_guide['ba'] + $valeur['adress'],
																														$valeur['length']));
			}
		} else {
			$this->inner_data = array();
			$this->inner_directory = array();
		}

		$this->update();
	}
	
// ---------------------------------------------------
// Reset de la notice
// ---------------------------------------------------
	function reset_notice()
	{
		$this->full_record="";
		$this->guide = '';
		$this->directory = '';
		$this->data = '';
		$this->errors="";
		$this->inner_guide="";
		$this->inner_directory="";
		$this->inner_data="";
	}
// ---------------------------------------------------
// 		récupération d'un ou plusieurs sous-champ(s)
// ---------------------------------------------------

	/**
	 * Premier argument label de champ obligatoire
	 * Puis liste de sous-champs sinon renvoit le bloc entier
	 */
	public function get_subfield() {
		$result = array();

		// premier param obligatoire
		$args_count = func_num_args();
		if (!$args_count) 
			return $result;

		$contents = array();
		$inner_data_size = sizeof($this->inner_data);
		$inner_data_label = func_get_arg(0);
		for ($i = 0; $i < $inner_data_size; $i++) {
			if ($inner_data_label == $this->inner_data[$i]['label'])
				$contents[] = $this->inner_data[$i]['content'];
		}

		if (0 == count($contents))
			return $result;

		$subfields = array();
		if (2 == $args_count) {
			$subfields[] = func_get_arg(1);
		} else {
			for ($j = 1; $j < $args_count; $j++)
				$subfields[] = func_get_arg($j);
		}

		foreach ($contents as $content)
			$this->_getSubfieldInContent($content, $subfields, $result);

		return $result;
	}


	protected function _getSubfieldInContent($content, $subfields, &$result) {
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
// 		Decoupage d'un champ complet par son indice 
// ----------------------------------------------------------------

	public function decoupe_field($indice) 
	{
		if(! $this->inner_data[$indice]) return false;

		$sc["bloc"]=preg_replace("/$this->rgx_field_end/",'',$this->inner_data[$indice]["content"]);
		if(substr($this->inner_data[$indice]["label"],0,2)=="00")
		{
			$sc["indicateur1"]=""; 
			$sc["indicateur2"]="";
			$sc["champs"][0]["code"]="";
			$sc["champs"][0]["valeur"]=$sc[bloc];
		}
		else
		{
			$sc["indicateur1"]=substr($sc["bloc"],0,1);
			$sc["indicateur2"]=substr($sc["bloc"],1,1);
			$bloc=substr($sc["bloc"],3);
			$champs=split($this->subfield_begin,$bloc);
			for($j=0;$j<count($champs); $j++)
			{
				$sc["champs"][$j]["code"]="\$".substr($champs[$j],0,1);
				$sc["champs"][$j]["valeur"]=substr($champs[$j],1);
			}
		}
		return $sc;
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
// ----------------------------------------------------------------
// Refait une zone inner_data a partir d'un tableau de valeurs
// ----------------------------------------------------------------
	public function makeZoneByValeurs($indicateurs,$sous_champ,$valeurs)
	{
		$new=$indicateurs;
		foreach($valeurs as $valeur) $new.=$this->subfield_begin.$sous_champ.$valeur;
		return $new;
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
// ---------------------------------------------------
// 		ajout d'une zone formattée inner_data
// ---------------------------------------------------
	public function add_zone($zone,$valeur)
	{
		$index = sizeof($this->inner_data);
		$this->inner_data[$index][label] = $zone;
		$this->inner_data[$index][content] = $valeur;	
		sort($this->inner_data);
	}
// ---------------------------------------------------
// 		ajout d'un champ
// ---------------------------------------------------
	function add_field($label='000', $ind='') 
	{

		// vérification des paramètres : au moins 2
		if(func_num_args() < 3) {
			$this->errors[] = '[add_field] impossible d\'ajouter un champ vide';
			return false;
		}

		if($label < 1) {
			$this->errors[] = '[add_field] le label \''.$label. '\' n\'est pas valide';
			return false;
		}

		// test des indicateurs
		if(strlen($ind) != 0 && strlen($ind) != $this->inner_guide[il]) {
			$this->errors[] = '[add_field] l\'indicateur \''.$ind. '\' n\'est pas valide';
			return false;
		}

		// mise en form du label
		if(strlen($label) < 3 && $label < 100)
			$label = sprintf('%03d', $label);

		// notre champ doit commencer par un label

		if (!preg_match('/^[0-9]{3}$/', $label)) {
			$this->last_error = '[add_field] le label \''.$label. '\' n\'est pas valide';
			return false;
		}
		$nb_args = func_num_args();

		// suivant le cas, ajout des infos
		switch($nb_args) {
			case 3: // il n'y a qu'un seul param en plus du label et des indicateurs
				if(!is_array(func_get_arg(2)))
					$content = func_get_arg(2);
				else {
					// le param est un tableau
					$field = func_get_arg(2);
					for($i=0;$i < sizeof($field); $i++) {
						if(preg_match('/^[a-z0-9]$/', $field[$i][0]) && $field[$i][1]) {
							$content .= $this->subfield_begin.$field[$i][0].$field[$i][1];
						}
					}
				}
				break;
			default: // plus d'un champ
				// on s'assure que le nombre de param est pair
				if(floor($nb_args/2) < $nb_args/2)
					$nb_args = $nb_args - 1;
				// récupérer les paires champ/valeur
				$i = 2;
				while( $i < $nb_args - 1) {
					$field = func_get_arg($i);
					$fieldbis = func_get_arg($i + 1);
					if(preg_match('/^[a-z0-9]$/', $field))
						$content .= $this->subfield_begin.$field.$fieldbis;
					else
						$this->errors[] = '[add_field] étiquette de sous-champ non valide';
					$i = $i + 2;
				}
				break;
		}

		if(sizeof($content)) 
		{
			// ajout des éventuels indicateurs
			if(strlen($ind) == $this->inner_guide[il]) $content = $ind.$content;

			// mise à jour des inner_data
			$index = sizeof($this->inner_data);
			$this->inner_data[$index][label] = $label;
			$this->inner_data[$index][content] = $content;		

			// tri des inner_data
			sort($this->inner_data);
		}
		return true;
	}

// ---------------------------------------------------
// 		suppression d'un champ
// ---------------------------------------------------

	function delete_field($label, $index=-1) {

		if(!func_num_args()) {
			$this->errors[] = '[delete_field] pas de label pour le champ';
			return false;
		}

		if(!$label) {
			$this->errors[] = '[delete_field] le label \''.$label. '\' n\'est pas valide';
			return false;
		}

		// mise en form du label
		if(strlen($label) < 3 && $label < 100)
			$label = sprintf('%03d', $label);

		// vérification du format du label

		if (!preg_match('/^[0-9\.]{3}$/', $label)) {
			$this->last_error = '[delete_field] le label \''.$label. '\' n\'est pas valide';
			return false;
		}
		for($i=0; $i < sizeof($this->inner_data); $i++) {
			if($label== $this->inner_data[$i][label]) 
			{
				$this->inner_data[$i][label] ='';
				$this->inner_data[$i][content] ='';
			}	
		}		
		return true;
	}

// ---------------------------------------------------
// 		update de l'enregistrement
// ---------------------------------------------------

	function update() {
		// supprime les lignes vides d'inner_data
		$inner_data_count = sizeof($this->inner_data);
		$this->inner_directory = array();
    $this->data = '';

		for($i=0; $i < $inner_data_count; $i++) {	
			if(empty($this->inner_data[$i]['label']) || empty($this->inner_data[$i]['content'])) {
				array_splice($this->inner_data, $i, 1);
				$i--; 
			}
			elseif ($this->type_accents > 0) $this->inner_data[$i]['content']=$this->decode_accents($this->inner_data[$i]['content']);

			$this->inner_directory[$i] = array(
				'label' => $this->inner_data[$i]['label'],
				'length' => strlen($this->inner_data[$i]['content']),
				'adress' => 0
			);

      $this->data .= $this->inner_data[$i]['content'];
		}
		$this->data .= $this->record_end;
		$this->type_accents =0;


		// mise à jour des offset et du répertoire 'réel'
		$this->inner_directory[0]['length']-=1;
		for($i = 1; $i < $inner_data_count; $i++)	{
			$this->inner_directory[$i]['adress'] = $this->inner_directory[$i - 1]['length'] + $this->inner_directory[$i - 1]['adress'];
		}

		// mise à jour du répertoire
		$this->directory = ''; 
		$inner_directory_size = sizeof($this->inner_directory);
		for($i=0; $i <  $inner_directory_size; $i++) {
      $this->directory .= sprintf('%03d%0'.$this->inner_guide['dm1'].'d'.'%0'.$this->inner_guide['dm2'].'d', 
                                  $this->inner_directory[$i]['label'],
                                  $this->inner_directory[$i]['length'],
                                  $this->inner_directory[$i]['adress']);
		}

		// mise à jour du guide
		## adresse de base.
		$this->inner_guide['ba'] = 24 + strlen($this->directory) + 1;
		## longueur de l'enregistrement iso2709
		$this->inner_guide['rl'] = 24 + strlen($this->directory) + strlen($this->data);


		$this->guide = sprintf('%05d', $this->inner_guide['rl'])
		.$this->inner_guide['rs']
		.$this->inner_guide['dt']
		.$this->inner_guide['bl']
		.$this->inner_guide['hl']
		.$this->inner_guide['pos9']
		.$this->inner_guide['il']
		.$this->inner_guide['sl']
		.sprintf('%05d', $this->inner_guide['ba'])
		.$this->inner_guide['el']
		.$this->inner_guide['ru']
		.$this->inner_guide['pos19']
		.$this->inner_guide['dm1']
		.$this->inner_guide['dm2']
		.$this->inner_guide['dm3']
		.$this->inner_guide['pos23'];

		// constitution du nouvel enregistrement
		$this->full_record = $this->guide.$this->directory.$this->data;
		return $this->full_record;
	}

// ---------------------------------------------------
// 		affichage d'un rapport des erreurs
// ---------------------------------------------------
	function show_errors() 
	{
		if(sizeof($this->errors)) {
			print '<table border="1">';
			print '<tr><th colspan="2">iso2709_record : erreurs</th></tr>';
			for($i=0; $i < sizeof($this->errors); $i++) {
				print '<tr><td>';
				print $i+1;
				print '</td><td>'.$this->errors[$i].'</td></tr>';
			}
			print '</table>';
		} else {
			print 'aucune erreur<br>';
		}
	}

// ---------------------------------------------------
// 		fonction de validation d'un enregistrement
// ---------------------------------------------------

	function valid() {
		// test de la longueur de l'enregistrement
		if ( 	strlen($this->full_record) != $this->inner_guide['rl']
			|| 	substr($this->full_record, -1, 1) != $this->record_end)
			$this->errors[] = '[format] la longueur de l\'enregistrement ne correspond pas au guide';

		// test des fin de champs
		// on retourne false si un champ ne finit pas par l'IS3

		while(list($cle, $valeur) = each($this->inner_data)) {
			if(!preg_match("/$this->rgx_field_end$/", $valeur[content]))
				$this->errors[] = '[format] le champ '.$cle.' ne finit pas par le caractère de fin de champ';
		}

		// les tableaux internes sont vides
		if(!sizeof($this->inner_data) || !sizeof($this->inner_data))
			$this->errors[] = '[internal] cet enregistrement est vide';

		// les inner_data et le inner_directory ne sont pas synchronisés

		if(sizeof($this->inner_data) != sizeof($this->inner_directory))
			$this->errors[] = '[internal] les tableaux internes ne sont pas synchronisés';

		if(sizeof($this->errors))
			return false;

		return true;

	}

// ---------------------------------------------------
//		fonctions de mise à jour du guide
// ---------------------------------------------------

	function set_rs($status) 
	{
		if($status) $this->inner_guide[rs] = $status[0];		
	}
	
	function set_dt($dtype) 
	{
		if($dtype) $this->inner_guide[dt] = $dtype[0];			
	}

	function set_bl($bltype) 
	{
		if($bltype) $this->inner_guide[bl] = $dtype[0];			
	}

	function set_hl($hltype) 
	{
		if($hltype) $this->inner_guide[hl] = $hltype[0];
	}

	function set_el($eltype) 
	{
		if($eltype) $this->inner_guide[el] = $eltype[0];
	}

	function set_ru($rutype)
	{
		if($rutype)$this->inner_guide[ru] = $rutype[0];
	}

// ---------------------------------------------------
// fonctions de conversion ISO (caractères)
// ---------------------------------------------------
	function decode_accents($chaine)
	{
		switch($this->type_accents)
		{
			case 0: return $chaine;													// Utf8
			case 1: return $this->ISO_decode($chaine); 			// Iso standard
			case 2: return utf8_encode($chaine);						// Windows
			default: return $chaine;
		}
	}

# ISO_decode converti de l'ISO 5426

	function ISO_decode($chaine)
	{
		if(!preg_match("/[\xC1-\xFF]./misU", $chaine)) return $chaine;
		else 
		{
			for($i = 0 ; $i < strlen($chaine) ; $i++) 
			{
				if(ord($chaine[$i]) >= 0xC1) 
				{
					$result .=  $this->isodecode(ord($chaine[$i]), ord($chaine[$i+1]));
					$i++;
				}
				elseif(ord($chaine[$i]) == 0x80) $result.="€"; 
				else $result .= $chaine[$i];
			}
		}
		return $result;
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

	function isodecode($char1, $char2)
	{
		switch($char1) 
		{
			case 0xc1:
				switch($char2) 
				{
					case 0x41: $result = 'À'; break ;
					case 0x45: $result = 'È'; break ;
					case 0x49: $result = 'Ì'; break ;
					case 0x4f: $result = 'Ò'; break ;
					case 0x55: $result = 'Ù'; break ;
					case 0x61: $result = 'à'; break ;
					case 0x65: $result = 'è'; break ;
					case 0x69: $result = 'ì'; break ;
					case 0x6f: $result = 'ò'; break ;
					case 0x75: $result = 'ù'; break ;
					default: 
						if($this->tracer_accents)$result="{hex=c1;".dechex($char2)."}";
						else $result = '?'; 
						break;
				}
			break;
			case 0xc2:
				switch($char2) {
					case 0x41: $result = 'Á'; break ;
					case 0x45: $result = 'É'; break ;
					case 0x49: $result = 'Í'; break ;
					case 0x4f: $result = 'Ó'; break ;
					case 0x55: $result = 'Ú'; break ;
					case 0x59: $result = 'Ý'; break ;
					case 0x61: $result = 'á'; break ;
					case 0x65: $result = 'é'; break ;
					case 0x69: $result = 'í'; break ;
					case 0x6f: $result = 'ó'; break ;
					case 0x75: $result = 'ú'; break ;
					case 0x79: $result = 'ý'; break ;
					default:
					default: 
						if($this->tracer_accents)$result="{hex=c2;".dechex($char2)."}"; 
						else $result = '?'; 
						break;
				}
			break;
			case 0xc3:
				switch($char2) {
					case 0x41: $result = 'Â'; break ;
					case 0x45: $result = 'Ê'; break ;
					case 0x49: $result = 'Î'; break ;
					case 0x4f: $result = 'Ô'; break ;
					case 0x55: $result = 'Û'; break ;
					case 0x61: $result = 'â'; break ;
					case 0x65: $result = 'ê'; break ;
					case 0x69: $result = 'î'; break ;
					case 0x6f: $result = 'ô'; break ;
					case 0x75: $result = 'û'; break ;
					default: 
						default: 
						if($this->tracer_accents)$result="{hex=c3;".dechex($char2)."}";
						else $result = '?'; 
						break;
				}
			break;
			case 0xc4:
				switch($char2) {
					case 0x41: $result = 'Ã'; break ;
					case 0x4e: $result = 'Ñ'; break ;
					case 0x4f: $result = 'Õ'; break ;
					case 0x61: $result = 'ã'; break ;
					case 0x6e: $result = 'ñ'; break ;
					case 0x6f: $result = 'õ'; break ;
					default: 
						default: 
						if($this->tracer_accents)$result="{hex=c4;".dechex($char2)."}";
						else $result = '?'; 
						break;
				}
			break;
			case 0xc8:
				switch($char2) {
					case 0x45: $result = 'Ë'; break ;
					case 0x49: $result = 'Ï'; break ;
					case 0x61: $result = 'ä'; break ; // rajout
					case 0x65: $result = 'ë'; break ;
					case 0x69: $result = 'ï'; break ;
					case 0x6f: $result = 'ö'; break ; // rajout
					case 0x75: $result = 'ü'; break ; // rajout
					case 0x79: $result = 'ÿ'; break ;
					default: 
						default: 
						if($this->tracer_accents)$result="{hex=c8;".dechex($char2)."}";
						else $result = '?'; 
						break;
				}
			break;
			case 0xc9:
				switch($char2) {
					case 0x41: $result = 'Ä'; break ;
					case 0x4f: $result = 'Ö'; break ;
					case 0x55: $result = 'Ü'; break ;
					case 0x61: $result = 'ä'; break ; 
					case 0x6f: $result = 'ö'; break ; 
					case 0x75: $result = 'ü'; break ; 
					default: 
						default: 
						if($this->tracer_accents)$result="{hex=c9;".dechex($char2)."}";
						else $result = '?'; 
						break;
				}
			break;
			case 0xca:
				switch($char2) {
					case 0x41: $result = 'Å'; break ;
					case 0x61: $result = 'å'; break ;
					default: 
						default: 
						if($this->tracer_accents)$result="{hex=ca;".dechex($char2)."}";
						else $result = '?'; 
						break;
				}
			break;
			case 0xd0:
				switch($char2) {
					case 0x43: $result = 'Ç'; break ;
					case 0x63: $result = 'ç'; break ;
					default:
						default: 
						if($this->tracer_accents)$result="{hex=d0;".dechex($char2)."}";
						else $result = '?'; 
						break;
				}
			break;

		// char sur un caractère

		case 0xe1: $result = 'Æ'; break ;
		case 0xe2: $result = 'Ð'; break ;
		case 0xe9: $result = 'Ø'; break ;
		case 0xec: $result = 'þ'; break ;
		case 0xf1: $result = 'æ'; break ;
		case 0xf3: $result = 'ð'; break ;
		case 0xf8: $result = '°'.chr($char2); break ;
		case 0xf9: $result = 'ø'; break ;
		case 0xfb: $result = 'ß'; break ;
		default: $result = chr($char1).chr($char2); break;

		}

		return $result;
	}
}

?>
