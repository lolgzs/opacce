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
////////////////////////////////////////////////////////////////////////
// OPAC3 : ISBN formattage
////////////////////////////////////////////////////////////////////////

class Class_Isbn
{
	private $code_brut;								// Code non filtré
	private $statut=0;								// 0=isbn vide 1=isbn incorrect 2=ok
	private $erreur;									// Message d'erreur
	private $isbn;										// ISbn retenu et formatté calculé 10 ou 13 selon le cas
	private $isbn10;									// ISbn 10 formatté
	private $isbn13;									// ISbn 13 formatté
	private $ean;											// Ean formatté
	private $pays;										// Code pays
	private $editeur;									// Code editeur
	private $numero;									// Numéro chez l'éditeur
	
// ----------------------------------------------------------------
// Constructeur (formatte le code)
// ----------------------------------------------------------------
	function __construct($code)
	{
		$this->code_brut=$code;
		$code=trim($code);
		if(!$code) return;
		
		// filtrer
		$new = '';
		for($i=0;$i < strLen($code); $i++) if($code[$i]>="0" and $code[$i] <="9") $new.= $code[$i]; 
		
		// Tester la longueur
		$len = strlen($new);
		if($len == 9 or $len == 10) { $this->calculIsbn($new); $this->isbn=$this->isbn10; }
		elseif($len < 12 )
		{ 
			$this->statut=1; 
			$this->erreur="longueur incorrecte (".$len.")"; 
		}
		// code ok
		else
		{
			$this->statut=2;
			if(strLeft($new,3) == "978" or strLeft($new,3) == "979") { $this->calculIsbn($new); $this->isbn=$this->isbn13; }
			else $this->ean=strleft($new,12).$this->clefEan($new);
		}
		if($this->isbn13 == '0-00-000000-0')
		{
			$this->isbn="";
			$this->isbn10="";
			$this->isbn13="";
		}
	}
// ----------------------------------------------------------------
// Rend la structure
// ----------------------------------------------------------------
	function getAll()
	{
		$ret["code_brut"]=$this->code_brut;
		$ret["statut"]=$this->statut;
		$ret["erreur"]=$this->erreur;
		$ret["isbn"]=$this->isbn;
		$ret["isbn10"]=$this->isbn10;
		$ret["isbn13"]=$this->isbn13;
		$ret["ean"]=$this->ean;
		$ret["pays"]=$this->pays;
		$ret["editeur"]=$this->editeur;
		$ret["numero"]=$this->numero;
		return $ret;
	}
	
// ----------------------------------------------------------------
// Calcul l'isbn 10 et 13
// ----------------------------------------------------------------
	private function calculIsbn($code) {
		$prefixe13 = '';

		if(strLeft($code,3)=="978" or strLeft($code,3)=="979")
		{
			$prefixe13=substr($code,0,3);
			$code=substr($code,3);
		}
		// isbn 10
		$somme = 0;
		for($i=0;$i<9; $i++)
		{
			$facteur = 10 -$i;
			$somme += ((int)$code[$i] * $facteur); 
		}
		$clef= 11 - $somme % 11;
		if($clef == 10) $clef="X";
		if($clef == 11) $clef = 0;
		$this->isbn10=$this->decoupeIsbn($code,$clef);
		
		// isbn 13
		if($prefixe13) $code=$prefixe13.$code; else $code="978".$code;
		$clef=$this->clefEan($code);
		$this->isbn13=$this->decoupeIsbn(substr($code,3,12),$clef);
		$this->isbn13=substr($code,0,3)."-".$this->isbn13;
	}
// ----------------------------------------------------------------	
// Calcul clef ean
// ----------------------------------------------------------------
	private function clefEan($code)
	{		
		$somme = 0;
		for($i=0; $i<12; $i++)
		{
			if($i & 1) $facteur=3; else $facteur=1;
			$somme += (int)$code[$i] * $facteur; 
		}
		$clef=$somme % 10;
		if($clef > 0)$clef=10-$clef;
		return $clef;
	}
// ----------------------------------------------------------------
// Decoupage isbn en pays editeur et no de doc
// ----------------------------------------------------------------
	private function decoupeIsbn($code,$clef)
	{
		$code=strLeft($code,9);
		// Pays
		$pays=strLeft($code,1);
		if($pays < "8") $code=strMid($code,1,10);
		elseif($pays == "8") { $pays=strLeft($code,2); $code=strMid($code,2,10);}
		else
		{
			if(strLeft($code,2) < "95") { $pays=strLeft($code,2); $code=strMid($code,2,10);}
			elseif(strLeft($code,3) < "996") { $pays=strLeft($code,3); $code=strMid($code,3,10);}
			elseif(strLeft($code,4) < "9990") { $pays=strLeft($code,4); $code=strMid($code,4,10);}
			else { $pays=strLeft($code,5); $code=strMid($code,5,10);}
		}
		$this->pays=$pays;
		
		// editeur
		if(strLeft($code,2) < "20") {$editeur=strLeft($code,2); $numero=strMid($code,2,10); }
		elseif(strLeft($code,3) < "700") {$editeur=strLeft($code,3); $numero=strMid($code,3,10); }
		elseif(strLeft($code,4) < "8500") {$editeur=strLeft($code,4); $numero=strMid($code,4,10); }
		elseif(strLeft($code,5) < "90000") {$editeur=strLeft($code,5); $numero=strMid($code,5,10); }
		elseif(strLeft($code,6) < "950000") {$editeur=strLeft($code,6); $numero=strMid($code,6,10); }
		else {$editeur=strLeft($code,7); $numero=strMid($code,7,10); }
		$this->editeur=$editeur;
		$this->numero=$numero;
		return $pays."-".$editeur."-".$numero."-".$clef;
	}
}

?>