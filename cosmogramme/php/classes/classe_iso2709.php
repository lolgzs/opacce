<?php
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

class iso2709_record {
	protected static $_tracer_accents_iso;

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
	const LABEL_LENGTH=24;

  protected $_pattern_subfield_cache = [];

	// ---------------------------------------------------
	// constructeur : init des constantes
	// ---------------------------------------------------
	public function __construct() {
		$this->ansi_decode_from = [chr(127),chr(128),chr(129),chr(130),chr(131),chr(132),chr(133),chr(134),chr(135),chr(136),chr(137),chr(138),chr(139),chr(140),chr(141),chr(142),chr(143),chr(144),chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151),chr(152),chr(153),chr(154),chr(155),chr(156),chr(157),chr(158),chr(159),chr(160),chr(0xC9)];
		$this->ansi_decode_replacedby = ["","€","",",","ƒ", "„"  , "...", "†"  , "‡"  , ""   , ""   , "Š"  , "{"  , "Oe" , ""   , "Ž"  , ""   , ""   , "'"  , "'"  , "'"  , "'"  , "."  , "-"  , "-"  , "~" , "™"  , "š"  , "}"  , "oe" , ""   , "ž" , "Ÿ"  , "Â","É"  ];

		$this->tracer_accents = isset(self::$_tracer_accents_iso) 
			? self::$_tracer_accents_iso 
			: self::$_tracer_accents_iso = getVariable('tracer_accents_iso');

		// initialisation des caractères spéciaux
		$this->record_end = chr(0x1d);				// fin de notice (IS3 de l'ISO 6630)
		$this->rgx_record_end = "\x1D";
		$this->field_end = chr(0x1e);					// fin de champ (IS2 de l'ISO 6630)
		$this->rgx_field_end ="\x1E";
		$this->subfield_begin = chr(0x1f);			// début de sous-champ (IS1 de l'ISO 6630)
		$this->rgx_subfield_begin = "\x1F";
		$this->NSB_begin = chr(0x88);					// début de NSB
		$this->rgx_NSB_begin = "\x88";
		$this->NSB_end = chr(0x89);					// fin de NSB (NSE)
		$this->rgx_NSB_end = "\x89";

		$this->errors = array();
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
		$this->guide = substr($this->full_record, 0, self::LABEL_LENGTH);

		// guide interne : valeurs par défaut si création
		$rl = intval(substr($this->guide, 0 , 5));			// record length : pos.1-4
		$rs = $this->guide[5];                                // record status : pos.5
		$dt = $this->guide[6];                                // document type : pos.6	
		
		$bl = $this->guide[7];                                // bibliographic level : pos.7

		$hl = intval($this->guide[8]);	// hierarchical level : pos.8

		$pos9 = $this->guide[9];							// pos.9 undefined, contains a blank

		$il = intval($this->guide[10]);			// indicator length : pos.10 (2)
		$sl = intval($this->guide[11]);			// subfield identifier length : pos.11 (2)	


		if (!$ba = intval(substr($this->guide, 12, 5)))			// base adress : pos.12-16	
			$ba = self::LABEL_LENGTH;

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
															 'ba' => $ba,
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
		$this->directory = substr($this->full_record,	self::LABEL_LENGTH,	$this->inner_guide['ba'] - 25);


		$tmp_dir = array_filter(explode('|', chunk_split($this->directory, $m, '|')));
		$dm1 = $this->inner_guide['dm1'];
		$dm2 = $this->inner_guide['dm2'];
		$adress_length = 3 + $dm1;

		$has_data = substr($this->full_record, 
											 $ba, 
											 strlen($this->full_record) - $ba);

		if ($has_data) {
			foreach ($tmp_dir as $i => $dir) {
				$label = substr($dir, 0, 3);
				$length = (int)substr($dir, 3, $dm1);
				$adress = (int)substr($dir, $adress_length, $dm2);
				$this->inner_directory[$i] = ['label' => $label,
																			'length' => $length,
																			'adress' => $adress];


				if (!isset($this->inner_data[$label]))
					$this->inner_data[$label] = [];

				$this->inner_data[$label][] = substr($this->full_record,
																						 $ba + $adress,
																						 $length);
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
	public function reset_notice() {
		$this->full_record = '';
		$this->guide = '';
		$this->directory = '';
		$this->errors = '';
		$this->inner_guide = '';
		$this->inner_directory = [];
		$this->inner_data = [];
	}


	public function getInnerGuide($clef) {
		return $this->inner_guide[$clef];
	}


	public function getInnerData() {
		return $this->inner_data;
	}


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
		$subfields_count = count($subfields);
		$get_method = $subfields_count == 0 ? '_getSubfieldContent' : ($subfields_count == 1 ? '_getOneSubfieldContent' : '_getSeveralSubfieldContent');

		if (!isset($this->inner_data[$inner_data_label]))
			return $result;

    foreach($this->inner_data[$inner_data_label] as $content)
			$this->$get_method($content, $subfields, $result);
      
		return $result;
	}


	protected function _getSubfieldContent($content, &$subfields, &$result) {
		$result []= preg_replace('/' . $this->rgx_field_end .'/', '', $content);
	}


	protected function _getOneSubfieldContent($content, &$subfields, &$result) {
			$mask = $this->_getPatternForSubfield($subfields[0]);
			while (preg_match($mask, $content, $regs)) {
				$result[] = $regs[1];
				$content = preg_replace($mask, '', $content);
			}
	}


	protected function _getSeveralSubfieldContent($content, &$subfields, &$result) {
    $tmp = [];
		foreach ($subfields as $subfield) {
			preg_match($this->_getPatternForSubfield($subfield), $content, $regs);
			$tmp[$subfield] = $regs[1]; 
		}
		$result[] = $tmp;
	}


	protected function _getPatternForSubfield($subfield) {
    if (!isset($this->_pattern_subfield_cache[$subfield]))
        $this->_pattern_subfield_cache[$subfield] = '/' . $this->rgx_subfield_begin . $subfield
			. '(.*)['.$this->rgx_subfield_begin.'|'.$this->rgx_field_end.']'
			. '/sU';
    return $this->_pattern_subfield_cache[$subfield];
	}


	// ----------------------------------------------------------------
	// 		Decoupage d'un champ complet par son indice 
	// ----------------------------------------------------------------

	public function decoupe_field($label, $content) {
		$sc = array('bloc' => preg_replace('/' . $this->rgx_field_end . '/', 
																			 '', 
																			 $content),
								'indicateur1' => '',
								'indicateur2' => '',
								'champs' => array());

		if ('00' == substr($label, 0, 2)) {
			$sc['champs'][] = array('code' => '',
															'valeur' => $sc['bloc']);
			return $sc;
		}

		$sc['indicateur1'] = substr($sc['bloc'], 0, 1);
		$sc['indicateur2'] = substr($sc['bloc'], 1, 1);
		$bloc = substr($sc['bloc'], 3);
		$fields = explode($this->subfield_begin, $bloc);

		foreach ($fields as $field) {
			$sc['champs'][] = array('code' => '$'.substr($field, 0, 1),
															'valeur' => substr($field, 1));
		}

		return $sc;
	}


	public function getValeursBloc($bloc) {
		$valeurs = array();
		$bloc = substr($bloc, 3);
		$fields = explode($this->subfield_begin, $bloc);
		foreach ($fields as $field) 
			$valeurs[] = substr($field, 1);

		if (0 == count($valeurs)) 
			return null;
		return $valeurs;
	}


	public function makeZoneByValeurs($indicateurs, $sous_champ, $valeurs) {
		$new = $indicateurs;
		foreach ($valeurs as $valeur) 
			$new .= $this->subfield_begin . $sous_champ . $valeur;
		return $new;
	}


	public function decoupe_bloc_champ($bloc) {
		$sc = array();
		$bloc = substr($bloc, 3);
		$fields = explode($this->subfield_begin, $bloc);
		foreach($fields as $field) {
			$sc[] = array('code' => substr($field, 0, 1),
										'valeur' => substr($field, 1));
		}

		if (0 == count($sc)) 
			return null;
		return $sc;
	}


	public function add_zone($zone, $valeur) {
		if (!isset($this->inner_data[$zone]))
			$this->inner_data[$zone] = [];

		$this->inner_data[$zone][] = $valeur;
		ksort($this->inner_data);
	}


	public function add_field($label = '000', $ind = '') {

		// vérification des paramètres : au moins 2
		if (func_num_args() < 3) {
			$this->errors[] = '[add_field] impossible d\'ajouter un champ vide';
			return false;
		}

		if ($label < 1) {
			$this->errors[] = '[add_field] le label \'' . $label . '\' n\'est pas valide';
			return false;
		}

		// test des indicateurs
		if (strlen($ind) != 0 
				&& strlen($ind) != $this->inner_guide['il']) {
			$this->errors[] = '[add_field] l\'indicateur \'' . $ind . '\' n\'est pas valide';
			return false;
		}

		// mise en form du label
		if (strlen($label) < 3 && $label < 100) 
			$label = sprintf('%03d', $label);

		// notre champ doit commencer par un label
		if (!preg_match('/^[0-9]{3}$/', $label)) {
			$this->last_error = '[add_field] le label \''.$label. '\' n\'est pas valide';
			return false;
		}

		$nb_args = func_num_args();
		$content = '';
		// suivant le cas, ajout des infos
		switch ($nb_args) {
		  case 3: // il n'y a qu'un seul param en plus du label et des indicateurs
				$third_argument = func_get_arg(2);
				if (!is_array($third_argument)) {
					$content = ('00' != substr($label, 0, 2)) ? $this->subfield_begin : '';
					$content .= $third_argument . $this->field_end;
					break;
				}

				foreach ($third_argument as $field) {
					if (preg_match('/^[a-z0-9]$/', $field[0]) && $field[1]) 
						$content .= $this->subfield_begin . $field[0] . $field[1];
				}
				$content .= $this->field_end;
				break;
		  default: // plus d'un champ
				// on s'assure que le nombre de param est pair
				if (floor($nb_args/2) < $nb_args/2)
					$nb_args = $nb_args - 1;
				// récupérer les paires champ/valeur
				$i = 2;
				while ($i < $nb_args-1) {
					$field = func_get_arg($i);
					$fieldbis = func_get_arg($i + 1);
					if (preg_match('/^[a-z0-9]$/', $field))
						$content .= $this->subfield_begin . $field . $fieldbis;
					else
						$this->errors[] = '[add_field] étiquette de sous-champ non valide';
					$i = $i + 2;
				}
				$content .= $this->field_end;
				break;
		}

		if ('' != $content) {
			// ajout des éventuels indicateurs
			if (strlen($ind) == $this->inner_guide['il']) 
				$content = $ind . $content;
			$this->add_zone($label, $content);
		}

		return true;
	}


	public function delete_field($label, $index = -1) {
		if (!func_num_args()) {
			$this->errors[] = '[delete_field] pas de label pour le champ';
			return false;
		}

		if (!$label) {
			$this->errors[] = '[delete_field] le label \''.$label. '\' n\'est pas valide';
			return false;
		}

		// mise en form du label
		if (strlen($label) < 3 && $label < 100)
			$label = sprintf('%03d', $label);

		// vérification du format du label
		if (!preg_match('/^[0-9\.]{3}$/', $label)) {
			$this->last_error = '[delete_field] le label \''.$label. '\' n\'est pas valide';
			return false;
		}

		unset($this->inner_data[$label]);
		return true;
	}


	public function update($export_accents = false) {
		// supprime les lignes vides d'inner_data
		$inner_data_old = array_filter($this->inner_data);
		$this->inner_data = [];
		$this->inner_directory = [];

		$adress = 0;
		foreach($inner_data_old as $label => $contents) {
			if (!$label)
				continue;

			if (!$contents = array_filter($contents))
				continue;

			$this->inner_data[$label] = [];
			foreach($contents as $content) {
				$new_content = ($this->type_accents > 0) 
					? $this->decode_accents($content)
					: $content;

				if ($export_accents == 1) $new_content = $this->ISO_encode($content);
				if ($export_accents == 2) $new_content = utf8_decode($content);
				if ($export_accents == 3) $new_content = utf8_decode($content);
				$this->inner_data[$label][] = $new_content; 
				$data .= $new_content;

				$length = strlen($new_content);
				$this->inner_directory[] = ['label' => $label,
																		'length' => $length,
																		'adress' => $adress];
				$adress += $length; 
			}
		}
		$data .= $this->record_end;

		$this->type_accents = 0;

		// mise à jour du répertoire
		$this->directory = ''; 
		$inner_directory_size = sizeof($this->inner_directory);
		$template_string_directory = '%03d%0'.$this->inner_guide['dm1'].'d'.'%0'.$this->inner_guide['dm2'].'d';

		for($i=0; $i <  $inner_directory_size; $i++) {
      $this->directory .= sprintf($template_string_directory, 
                                  $this->inner_directory[$i]['label'],
                                  $this->inner_directory[$i]['length'],
                                  $this->inner_directory[$i]['adress']);
		}

		// mise à jour du guide
		## adresse de base.
		$this->inner_guide['ba'] = self::LABEL_LENGTH + strlen($this->directory) + 1;
		## longueur de l'enregistrement iso2709
		$this->inner_guide['rl'] = self::LABEL_LENGTH + strlen($this->directory) + strlen($data)+1;

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
		$this->full_record = $this->guide . $this->directory . $this->field_end . $data;
		return $this->full_record;
	}


	public function show_errors() {
		if (sizeof($this->errors)) {
			print '<table border="1">';
			print '<tr><th colspan="2">iso2709_record : erreurs</th></tr>';
			for ($i=0; $i < sizeof($this->errors); $i++) {
				print '<tr><td>';
				print $i+1;
				print '</td><td>'.$this->errors[$i].'</td></tr>';
			}
			print '</table>';
		} else {
			print 'aucune erreur<br>';
		}
	}


	public function valid() {
		// test de la longueur de l'enregistrement
		if (strlen($this->full_record) != $this->inner_guide['rl']
				|| substr($this->full_record, -1, 1) != $this->record_end)
			$this->errors[] = '[format] la longueur de l\'enregistrement ne correspond pas au guide : '.$this->inner_guide['rl']." / ".strlen($this->full_record);

		// test des fin de champs
		// on retourne false si un champ ne finit pas par l'IS3
		while (list($cle, $valeur) = each($this->inner_data)) {
			if (!preg_match("/$this->rgx_field_end$/", $valeur['content']))
				$this->errors[] = '[format] le champ ' . $cle . ' ne finit pas par le caractère de fin de champ';
		}

		// les tableaux internes sont vides
		if (!sizeof($this->inner_data) || !sizeof($this->inner_data))
			$this->errors[] = '[internal] cet enregistrement est vide';

		// les inner_data et le inner_directory ne sont pas synchronisés
		if (sizeof($this->inner_data) != sizeof($this->inner_directory))
			$this->errors[] = '[internal] les tableaux internes ne sont pas synchronisés';

		if (sizeof($this->errors))
			return false;
		return true;
	}

	// ---------------------------------------------------
	//		fonctions de mise à jour du guide
	// ---------------------------------------------------

	public function set_rs($status) {
		$this->_set_inner_guide_value('rs', $status);
	}
	
  public function set_dt($dtype) {
		$this->_set_inner_guide_value('dt', $dtype);
	}

  public function set_bl($bltype) {
		$this->_set_inner_guide_value('bl', $bltype);
	}

	public function set_hl($hltype) {
		$this->_set_inner_guide_value('hl', $hltype);
	}

	public function set_el($eltype) {
		$this->_set_inner_guide_value('el', $eltype);
	}

	public function set_ru($rutype) {
		$this->_set_inner_guide_value('ru', $rutype);
	}

	protected function _set_inner_guide_value($key, $value) {
		if ($value)
			$this->inner_guide[$key] = $value[0];
	}
	
	public function getFullRecord() {
		return $this->full_record;
	}

	// ---------------------------------------------------
	// fonctions de conversion ISO (caractères)
	// ---------------------------------------------------
	public function decode_accents($chaine) {
		switch ($this->type_accents) {
			case 0: return $chaine;													// Utf8
			case 1: return $this->ISO_decode($chaine); 			// Iso standard
			case 2:	return $this->ansi_decode($chaine);			// Windows
			case 4:	return $this->marc21_decode($chaine);		// marc21
			default: return $chaine;
		}
	}

	
	public function ansi_decode($chaine) {
		return utf8_encode(str_replace($this->ansi_decode_from,$this->ansi_decode_replacedby,$chaine));
	}


	# ISO_decode converti de l'ISO 5426
	public function ISO_decode($chaine) {
		if (!isset($this->_iso_decode_table))
			$this->_initISODecodeTable();

		if (!preg_match("/[\xC1-\xFF]./misU", $chaine)) 
			return $chaine;

		return str_replace($this->_iso_decode_table_2709, $this->_iso_decode_table_utf8, $chaine);
	}


	public function _initISODecodeTable() {
		$this->_iso_decode_table = [
			chr(0x89) => '',
			chr(0x90) => '',
			chr(0xc1).chr(0x41) => 'À',
			chr(0xc1).chr(0x45) => 'È',
			chr(0xc1).chr(0x49) => 'Ì',
			chr(0xc1).chr(0x4f) => 'Ò',
			chr(0xc1).chr(0x55) => 'Ù',
			chr(0xc1).chr(0x61) => 'à',
			chr(0xc1).chr(0x65) => 'è',
			chr(0xc1).chr(0x69) => 'ì',
			chr(0xc1).chr(0x6f) => 'ò',
			chr(0xc1).chr(0x75) => 'ù',
			chr(0xc2).chr(0x41) => 'Á',
			chr(0xc2).chr(0x45) => 'É',
			chr(0xc2).chr(0x49) => 'Í',
			chr(0xc2).chr(0x4f) => 'Ó',
			chr(0xc2).chr(0x55) => 'Ú',
			chr(0xc2).chr(0x59) => 'Ý',
			chr(0xc2).chr(0x61) => 'á',
			chr(0xc2).chr(0x65) => 'é',
			chr(0xc2).chr(0x69) => 'í',
			chr(0xc2).chr(0x6e) => 'ñ', // Rajout
			chr(0xc2).chr(0x6f) => 'ó',
			chr(0xc2).chr(0x75) => 'ú',
			chr(0xc2).chr(0x79) => 'ý',
			chr(0xc2).chr(0xb0) => 'C', // Rajout
			chr(0xc3).chr(0x41) => 'Â',
			chr(0xc3).chr(0x45) => 'Ê',
			chr(0xc3).chr(0x49) => 'Î',
			chr(0xc3).chr(0x4f) => 'Ô',
			chr(0xc3).chr(0x55) => 'Û',
			chr(0xc3).chr(0x61) => 'â',
			chr(0xc3).chr(0x63) => 'E', // Rajout
			chr(0xc3).chr(0x64) => 'É', // Rajout
			chr(0xc3).chr(0x65) => 'ê',
			chr(0xc3).chr(0x67) => 'E', // Rajout
			chr(0xc3).chr(0x69) => 'î',
			chr(0xc3).chr(0x6f) => 'ô',
			chr(0xc3).chr(0x75) => 'û',
			chr(0xc3).chr(0x8a) => 'E',  // Rajout
			chr(0xc3).chr(0xa9) => 'é',  // Rajout
			chr(0xc3).chr(0xa8) => 'è',  // Rajout
			chr(0xc3).chr(0xa7) => 'ç',  // Rajout
			chr(0xc3).chr(0xa0) => 'à',  // Rajout
			chr(0xc3).chr(0xa2) => 'â',  // Rajout
			chr(0xc3).chr(0xab) => 'ë',  // Rajout
			chr(0xc3).chr(0xaa) => 'ê',  // Rajout
			chr(0xc3).chr(0x80) => 'à',  // Rajout
			chr(0xc3).chr(0x82) => 'A',  // Rajout
			chr(0xc3).chr(0x87) => 'C',  // Rajout
			chr(0xc3).chr(0xb4) => 'ô',  // Rajout
			chr(0xc3).chr(0xbb) => 'û',  // Rajout
			chr(0xc3).chr(0xaf) => 'ï',  // Rajout
			chr(0xc3).chr(0xae) => 'î',  // Rajout
			chr(0xc4).chr(0x41) => 'Ã',
			chr(0xc4).chr(0x4e) => 'Ñ',
			chr(0xc4).chr(0x4f) => 'Õ',
			chr(0xc4).chr(0x61) => 'ã',
			chr(0xc4).chr(0x6e) => 'ñ',
			chr(0xc4).chr(0x6f) => 'õ',
			chr(0xc8).chr(0x45) => 'Ë',
			chr(0xc8).chr(0x49) => 'Ï',
			chr(0xc8).chr(0x61) => 'ä', // rajout
			chr(0xc8).chr(0x65) => 'ë',
			chr(0xc8).chr(0x69) => 'ï',
			chr(0xc8).chr(0x6f) => 'ö', // rajout
			chr(0xc8).chr(0x75) => 'ü', // rajout
			chr(0xc8).chr(0x79) => 'ÿ',
			chr(0xc9).chr(0x41) => 'Ä',
			chr(0xc9).chr(0x4f) => 'Ö',
			chr(0xc9).chr(0x55) => 'Ü',
			chr(0xc9).chr(0x61) => 'ä', 
			chr(0xc9).chr(0x65) => 'ë', // Rajout
			chr(0xc9).chr(0x69) => 'ï', // Rajout
			chr(0xc9).chr(0x6f) => 'ö', 
			chr(0xc9).chr(0x75) => 'ü', 
			chr(0xc9).chr(0x79) => 'ÿ', // Rajout
			chr(0xca).chr(0x41) => 'Å',
			chr(0xca).chr(0x61) => 'å',
			chr(0xca).chr(0x30) => '°', // Rajout
			chr(0xca).chr(0x20) => '°', // Rajout
			chr(0xd0).chr(0x43) => 'Ç',
			chr(0xd0).chr(0x63) => 'ç',
			chr(0xe1) =>'Æ',
			chr(0xe2) =>'Ð',
			chr(0xe9) =>'Ø',
			chr(0xec) =>'þ',
			chr(0xf1) =>'æ',
			chr(0xf3) =>'ð',
			chr(0xf8) =>'°'.chr($char2),
			chr(0xf9) =>'ø',
			chr(0xfb) =>'ß',
			chr(0x80) =>'€' ];

			$this->_iso_decode_table_2709 = array_keys($this->_iso_decode_table);
			$this->_iso_decode_table_utf8 = array_values($this->_iso_decode_table);
	}

	
	public function ISO_encode($chaine) {
		if (!$chaine) 
			return $chaine;

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

 		while (list($char, $value) = each($char_table))
			$chaine = preg_replace("/$char/", $value, $chaine);

		return $chaine;
	}


	public function marc21_decode($chaine) {
		if (!isset($this->_marc21_decode_table))
			$this->_initMARC21DecodeTable();

		if (!preg_match("/[\xC1-\xFF]./misU", $chaine)) 
			return $chaine;

		return str_replace($this->_marc21_decode_table_2709, $this->_marc21_decode_table_utf8, $chaine);
	}


	public function _initMARC21DecodeTable() {
		$this->_marc21_decode_table = [
			chr(0xe1).chr(0x41) =>  'À',
			chr(0xe1).chr(0x45) =>  'È',
			chr(0xe1).chr(0x49) =>  'Ì',
			chr(0xe1).chr(0x4f) =>  'Ò',
			chr(0xe1).chr(0x55) =>  'Ù',
			chr(0xe1).chr(0x61) =>  'à',
			chr(0xe1).chr(0x65) =>  'è',
			chr(0xe1).chr(0x69) =>  'ì',
			chr(0xe1).chr(0x6f) =>  'ò',
			chr(0xe1).chr(0x75) =>  'ù',
			chr(0xe2).chr(0x41) =>  'Á',
			chr(0xe2).chr(0x45) =>  'É',
			chr(0xe2).chr(0x49) =>  'Í',
			chr(0xe2).chr(0x4f) =>  'Ó',
			chr(0xe2).chr(0x55) =>  'Ú',
			chr(0xe2).chr(0x59) =>  'Ý',
			chr(0xe2).chr(0x61) =>  'á',
			chr(0xe2).chr(0x65) =>  'é',
			chr(0xe2).chr(0x69) =>  'í',
			chr(0xe2).chr(0x6e) =>  'ñ', // Rajout
			chr(0xe2).chr(0x6f) =>  'ó',
			chr(0xe2).chr(0x75) =>  'ú',
			chr(0xe2).chr(0x79) =>  'ý',
			chr(0xe2).chr(0xb0) =>  'C', // Rajout
			chr(0xe3).chr(0x41) =>  'Â',
			chr(0xe3).chr(0x45) =>  'Ê',
			chr(0xe3).chr(0x49) =>  'Î',
			chr(0xe3).chr(0x4f) =>  'Ô',
			chr(0xe3).chr(0x55) =>  'Û',
			chr(0xe3).chr(0x61) =>  'â',
			chr(0xe3).chr(0x63) =>  'E', // Rajout
			chr(0xe3).chr(0x64) =>  'É', // Rajout
			chr(0xe3).chr(0x65) =>  'ê',
			chr(0xe3).chr(0x67) =>  'E', // Rajout
			chr(0xe3).chr(0x69) =>  'î',
			chr(0xe3).chr(0x6f) =>  'ô',
			chr(0xe3).chr(0x75) =>  'û',
			chr(0xe3).chr(0x8a) =>  'E',  // Rajout
			chr(0xe3).chr(0xa9) =>  'é',  // Rajout
			chr(0xe3).chr(0xa8) =>  'è',  // Rajout
			chr(0xe3).chr(0xa7) =>  'ç',  // Rajout
			chr(0xe3).chr(0xa0) =>  'à',  // Rajout
			chr(0xe3).chr(0xa2) =>  'â',  // Rajout
			chr(0xe3).chr(0xab) =>  'ë',  // Rajout
			chr(0xe3).chr(0xaa) =>  'ê',  // Rajout
			chr(0xe3).chr(0x80) =>  'à',  // Rajout
			chr(0xe3).chr(0x82) =>  'A',  // Rajout
			chr(0xe3).chr(0x87) =>  'C',  // Rajout
			chr(0xe3).chr(0xb4) =>  'ô',  // Rajout
			chr(0xe3).chr(0xbb) =>  'û',  // Rajout
			chr(0xe3).chr(0xaf) =>  'ï',  // Rajout
			chr(0xe3).chr(0xae) =>  'î',  // Rajout
			chr(0xe4).chr(0x41) =>  'Ã',
			chr(0xe4).chr(0x4e) =>  'Ñ',
			chr(0xe4).chr(0x4f) =>  'Õ',
			chr(0xe4).chr(0x61) =>  'ã',
			chr(0xe4).chr(0x6e) =>  'ñ',
			chr(0xe4).chr(0x6f) =>  'õ',
			chr(0xe8).chr(0x45) =>  'Ë',
			chr(0xe8).chr(0x49) =>  'Ï',
			chr(0xe8).chr(0x61) =>  'ä', // rajout
			chr(0xe8).chr(0x65) =>  'ë',
			chr(0xe8).chr(0x69) =>  'ï',
			chr(0xe8).chr(0x6f) =>  'ö', // rajout
			chr(0xe8).chr(0x75) =>  'ü', // rajout
			chr(0xe8).chr(0x79) =>  'ÿ',
			chr(0xea).chr(0x41) =>  'Å',
			chr(0xea).chr(0x61) =>  'å',
			chr(0xea).chr(0x30) =>  '°',
			chr(0xea).chr(0x20) =>  '°',
			chr(0xf0).chr(0x43) =>  'Ç',
			chr(0xf0).chr(0x63) =>  'ç'
		];


		$this->_marc21_decode_table_2709 = array_keys($this->_marc21_decode_table);
		$this->_marc21_decode_table_utf8 = array_values($this->_marc21_decode_table);
	}

	
	public function addSerializedFields($serialized) {
		if (!$fields = unserialize($serialized))
			return $this;

		foreach ($fields as $k => $v)
			$this->addSerializedField($k, $v);

		return $this;
	}


	public function addSerializedField($key, $value) {
		if (is_array($value)) {
			if (!is_array($value['data']))
				return $this->addSerializedField($value['field'], $value['data']);

			$params = array($value['field'], '1 ');
			foreach ($value['data'] as $k => $v) {
				$params[] = $k;
				$params[] = $v;
			}
			call_user_func_array(array($this, 'add_field'), $params);
			return $this;
		}

		$field = explode('$', $key);
		$this->add_field($field[0], '1 ', $field[1] . $value);

		return $this;
	}
}

?>
