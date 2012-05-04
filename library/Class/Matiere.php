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
// OPAC 3 : MOTS-MATIERE
///////////////////////////////////////////////////////////////////////////////////////

class Class_Matiere extends Storm_Model_Abstract {
	protected $_table_name = 'codif_matiere';
	protected $_table_primary = 'id_matiere';

	protected $_default_attribute_values = array('libelle' => '');

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}
	
// ----------------------------------------------------------------
// Rend une liste pour un champ suggestion
// ----------------------------------------------------------------
	public function getListeSuggestion($recherche,$mode,$limite_resultat)
	{
		// Transformer en code alpha
		$ix=new Class_Indexation();
		$recherche=$ix->alphaMaj($recherche);
		$recherche=str_replace(" ","%",$recherche);
		
		// Lancer la recherche
		if($mode=="1") $condition="like '".$recherche."%'";
		if($mode=="2") $condition="like '%".$recherche."%'";
		$req="select id_matiere,libelle from codif_matiere where code_alpha ".$condition." order by code_alpha limit ".$limite_resultat;
		$resultat=fetchAll($req,true);
		return $resultat;
	}

// ----------------------------------------------------------------
// Rend les identifiants de sous_vedettes pour un id_matiere
// ----------------------------------------------------------------
	public function getSousVedettes() {
		$vedette = $this->getLibelle();
		$vedette = str_replace("'", "''", $vedette);
		$children = self::getLoader()->findAllBy(array('where' => 'libelle LIKE \''. $vedette . ' : %\''));
		
		if (0 == count($children)) 
		  return '';
		
		$sous_vedette = '';
		foreach($children as $child)
			$sous_vedette.= $child->getId() . ' ';

		return $sous_vedette;
	}


	public function beforeSave() {
		if ($this->isNew())
			$this->setDateCreation(date('Y-m-d'));
	}
}

?>