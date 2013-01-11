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
class ZendAfi_View_Helper_HistoriqueRecherche extends ZendAfi_View_Helper_BaseHelper
{
	public function historiqueRecherche()
	{
		// Entete
		printf('<h4>%s :</h4>', $this->translate()->_('Vos dernières recherches'));
		print('<table style="width:100%">');
		print('<tr>');
		print('<th width="5%">&nbsp;</th>');
		printf('<th width="10%%" align="left">%s</th>',$this->translate()->_('Type'));
		printf('<th width="45%%" align="left">%s</th>', $this->translate()->_('Expression recherchée'));
		printf('<th width="40%%" align="left">%s</th>', $this->translate()->_('Critères de sélection'));
		print('</tr>');
		print('<tr><td colspan="4" style="background: transparent url(\'../images/separ.gif) repeat-x scroll center top\'">&nbsp;</td></tr>');

		// Lignes
		for($i=count($_SESSION["histo_recherche"])-1; $i>=0; $i--)
		{
			$id_histo=$i;
			$ligne=$_SESSION["histo_recherche"][$i];
			$expression="";	$this->addCritere("RESET","","");
			$criteres="";	$this->addSelection("RESET","","");
			$crit=$ligne["selection"];
			switch($ligne["type"])
			{
				case 1: // recherche simple
					$type="simple";
					$expression=$crit["expressionRecherche"];
					break;
				case 2: // recherche avancée
					$type="avancée";
					// expression recherche
					$expression=$this->addCritere($this->translate()->_("Titre"),$crit["operateur_titres"],$crit["rech_titres"]);
					$expression=$this->addCritere($this->translate()->_("Auteur"),$crit["operateur_auteurs"],$crit["rech_auteurs"]);
					$expression=$this->addCritere($this->translate()->_("Sujet"),$crit["operateur_matieres"],$crit["rech_matieres"]);
					$expression=$this->addCritere($this->translate()->_("Dewey"),$crit["operateur_dewey"],$crit["rech_dewey"]);
					$expression=$this->addCritere($this->translate()->_("Editeur"),$crit["operateur_editeur"],$crit["rech_editeur"]);
					$expression=$this->addCritere($this->translate()->_("Collection"),$crit["operateur_collection"],$crit["rech_collection"]);
					$expression=$this->addCritere($this->translate()->_("Dewey"),$crit["operateur_dewey"],$crit["rech_dewey"]);
					// criteres de selection
					$criteres=$this->addSelection($this->translate()->_("Année début"),$crit["annee_debut"]);
					$criteres=$this->addSelection($this->translate()->_("Année fin"),$crit["annee_fin"]);
					$criteres=$this->addSelection($this->translate()->_("Nouveautés"),$crit["nouveaute"]);
					$criteres=$this->addSelection($this->translate()->_("année début"),$crit["annee_debut"]);
					break;
			}
			// Criteres de selection communs
			$criteres=$this->addSelection($this->translate()->_("Type de doc."),$crit["type_doc"]);
			$criteres=$this->addSelection($this->translate()->_("Bibliothèques"),$crit["selection_bib"]);
			
			// Url pour relancer la recherche
			$url=BASE_URL."/recherche/histo?id_histo=".$id_histo;
			
			printf('<tr style="cursor:pointer" onclick="document.location=\''.$url.'\'" title="%s">', $this->translate()->_('Relancer cette recherche'));
			printf('<td valign="top" style="text-align:center"><img src="'.URL_IMG.'bouton/loupe.gif" border="0" title="%s"></td>', $this->translate()->_('Relancer cette recherche'));
			print('<td valign="top">'.$type.'</td>');
			print('<td valign="top">'.$expression.'</td>');
			print('<td valign="top">'.$criteres.'</td>');
			print('</tr>');
		}
		print('</table>'.BR.BR);
	}

//------------------------------------------------------------------------------------------------------	
// Fonctions privees
//------------------------------------------------------------------------------------------------------
	private function addCritere($libelle,$operateur,$texte)
	{
		global $expression;
		if($libelle == "RESET"){ $expression=""; return; }
		if(!$texte) return $expression;
		if($expression) $expression.=BR;

		switch($operateur)	{
  		case "or": $expression.=$this->translate()->_("%s ", 'ou'); break;
	  	case "not": $expression.=$this->translate()->_("%s ", 'sauf'); break;
		}
		$expression.=$libelle;
		$expression.="=".$texte;
		return $expression;
	}
	
	private function addSelection($libelle,$valeur)
	{
		global $criteres;
		if($libelle == "RESET"){ $criteres=""; return; }
		if(!trim($valeur)) return $criteres;
		if($libelle == $this->translate()->_("Type de doc."))
		{
			if(!$valeur or $valeur == "0") return $criteres;
			$valeur=Class_Codification::getLibelleFacette("T".$valeur);
		}
		if($libelle == $this->translate()->_("Bibliothèques") and trim($valeur) > "" )
		{
			$bibs=explode(" ",$valeur);
			$valeur="";
			foreach($bibs as $bib)
			{
				if(!trim($bib)) continue;
				$id_bib=(int)substr($bib,1);
				$lib=fetchOne("select nom_court from int_bib where id_bib=$id_bib");
				if($valeur) $valeur.=", ";
				$valeur.=$lib;
			}
		}
		if($criteres) $criteres.=BR;
		$criteres.=$libelle."=".$valeur;
		return $criteres;
	}
}