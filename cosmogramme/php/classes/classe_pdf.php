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
// PDF : encapsule les classes du sous-dossier pdf
///////////////////////////////////////////////////////////////////////////////////////
include_once('pdf/ezpdf.php');

class pdf
{
	private $pdf;															// Instance de la classe de base Cezpdf
	private $path_fontes;											// Dossier fontes pdf
	private $fonte;														// Fonte par défaut
	
// ----------------------------------------------------------------
// Constructeur
// ----------------------------------------------------------------
	function __construct()
	{
		$this->pdf=new Cezpdf();
		$this->path_fontes="./php/classes/pdf/fonts/";
		$this->fonte="Helvetica.afm";
		$this->pdf->selectFont($this->path_fontes.$this->fonte);
	}

// ----------------------------------------------------------------
// Ecrire le fichier 
// ----------------------------------------------------------------
	public function ecrireFichier($nom_fic)
	{
		$pdfcode = $this->pdf->output();
		$path=getVariable("cache_path");
		$fic = fopen($path.$nom_fic,'w');
		fwrite($fic,$pdfcode);
		fclose($fic);
	}

}

?>