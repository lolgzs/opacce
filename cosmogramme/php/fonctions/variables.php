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

///////////////////////////////////////////////////////////////////////////////////////
// VARIABLES
///////////////////////////////////////////////////////////////////////////////////////

class VariableCache {
	static protected $_instance;
	protected	$_valeurs, $_listes;
	

	public static function getInstance() {
		if (!isset(self::$_instance))
			self::$_instance = new self();
		return self::$_instance;
	}


	public function setValeurCache($valeurs) {
		$this->_valeurs = $valeurs;
		return $this;
	}

	public function setListeCache($listes) {
		$this->_listes = $listes;
		return $this;
	}

	
	public function getValeur($clef) {
		global $sql;
		if (!isset($this->_valeurs[$clef]))
			$this->_valeurs[$clef] = $sql->fetchOne("Select valeur from variables where clef='$clef'");
		return $this->_valeurs[$clef];
	}


	public function setValeur($clef, $valeur) {
		global $sql;
		$data=array("valeur"=>$valeur);
		$sql->update("update variables set @SET@ Where clef='$clef'",$data);
		$this->_valeurs[$clef] = $valeur;
	}


	public function getListe($clef) {
		global $sql;
		if (!isset($this->_listes[$clef]))
			$this->_listes[$clef] = $sql->fetchOne("Select liste from variables where clef='$clef'");
		return $this->_listes[$clef];
	}
}



function getVariable($clef) {
	return VariableCache::getInstance()->getValeur($clef);
}


function setVariable($clef,$valeur) {
	VariableCache::getInstance()->setValeur($clef, $valeur);
}


function incrementeVariable($clef) {
	$valeur=getVariable($clef);
	$valeur++;
	setVariable($clef,$valeur);
	return $valeur;
}


function getLibCodifVariable($clef,$code) {
	$data = VariableCache::getInstance()->getListe($clef);

	$v=explode(chr(13).chr(10),$data);
	for($i=0; $i<count($v); $i++)
	{
		$elem=explode(":",$v[$i]);
		if($code==$elem[0]) return $elem[1];
	}
}



function getCodifsVariable($clef,$index_par_clefs=false) {
	$data = VariableCache::getInstance()->getListe($clef);
	$v=explode(chr(13).chr(10),$data);
	for($i=0; $i<count($v); $i++)
	{
		$elem=explode(":",$v[$i]);
		if(!trim($elem[1])) continue;
		if($index_par_clefs==true)
		{
			$liste[$elem[0]]=$elem[1];
		}
		else
		{
			$item["code"]=$elem[0];
			$item["libelle"]=$elem[1];
			$liste[]=$item;
		}
	}
	return $liste;
}

?>